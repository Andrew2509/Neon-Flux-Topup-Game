<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'customer_whatsapp' => ['required', 'string', 'max:32'],
            'user_id' => 'required',
            'product_code' => 'required',
            'payment' => 'nullable',
            'player_nickname' => ['nullable', 'string', 'max:128'],
        ]);

        $waDigits = preg_replace('/\D/', '', $request->customer_whatsapp);
        if (strlen($waDigits) < 10 || strlen($waDigits) > 15) {
            return back()->withErrors([
                'customer_whatsapp' => 'Nomor WhatsApp tidak valid. Gunakan 10–15 digit angka.',
            ])->withInput();
        }

        $tujuan = $request->user_id;
        if ($request->filled('zone_id')) {
            $tujuan .= $request->zone_id;
        }

        $playerNickname = trim((string) $request->input('player_nickname', ''));
        if (mb_strlen($playerNickname) > 128) {
            $playerNickname = mb_substr($playerNickname, 0, 128);
        }

        // 2. Ambil Detail Layanan dari Database dengan pengecekan status bertingkat
        $service = Service::where('product_code', $request->product_code)
            ->where('status', 'Aktif')
            ->whereHas('category', function($q) {
                // Pastikan Operator Game Aktif
                $q->where('status', 'Aktif')
                  ->where(function($q) {
                      // Jika terhubung ke Kategori Tokovoucher (Direct/Voucher), pastikan Kategori tersebut Aktif
                      $q->whereDoesntHave('providerCategory')
                        ->orWhereHas('providerCategory', function($q) {
                            $q->where('status', 'Aktif');
                        });
                  });
            })
            ->first();

        if (!$service) {
            return back()->with('error', 'Produk tidak ditemukan atau sedang tidak aktif (Operator/Kategori dinonaktifkan).');
        }

        // 3. Buat Data Pesanan (Order)
        // Use more robust Order ID: Timestamp + Random (Enterprise Standard)
        $orderId = 'PRP' . date('ymdHis') . strtoupper(Str::random(4));

        // Relocate Fee: Fetch payment method fee and add to price
        $paymentFee = 0;
        if ($request->filled('payment')) {
            $pm = \App\Models\PaymentMethod::where('code', $request->payment)->first();
            if ($pm) {
                $paymentFee = (float)$pm->fee;
            }
        }

        $paymentAmount = (int) ($service->price + $paymentFee);
        $productDetails = $service->name . " ($tujuan)";

        $userId = \Illuminate\Support\Facades\Auth::id();

        // Combined Create into one call
        $order = Order::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'product_name' => $productDetails,
            'total_price' => $paymentAmount,
            'payment_method' => $request->payment ?? 'Duitku',
            'status' => 'pending_payment',
            'payload' => [
                'tujuan' => $tujuan,
                'game_user_id' => (string) $request->user_id,
                'server_id' => $request->zone_id ?? '',
                'product_code' => $service->product_code,
                'cost' => $service->cost ?? $service->price,
                'customer_whatsapp' => $waDigits,
                'player_nickname' => $playerNickname,
            ]
        ]);

        // Log initial creation
        $order->logStatus(
            'Pesanan dibuat untuk produk '.$service->name
            . ($playerNickname !== '' ? ' — nama pemain (cek ID): '.$playerNickname : '')
        );

        // 3. Get Payment Provider
        $paymentMethod = null;
        if ($request->filled('payment')) {
            $paymentMethod = \App\Models\PaymentMethod::where('code', $request->payment)->first();
        }

        $providerName = $paymentMethod ? ($paymentMethod->provider ?? 'Duitku') : 'Duitku';

        if ($providerName === 'Internal' && $request->payment === 'SALDO') {
            return $this->processBalancePayment($order, $request);
        }

        if (stripos($providerName, 'DOKU') !== false) {
            return $this->processDoku($order, $paymentMethod, $request);
        }

        if (stripos($providerName, 'iPaymu') !== false) {
            return $this->processIPaymu($order, $paymentMethod, $request);
        }

        if (stripos($providerName, 'Midtrans') !== false) {
            return $this->processMidtrans($order, $paymentMethod, $request);
        }

        // Default to Duitku
        return $this->processDuitku($order, $paymentMethod, $request);
    }

    private function processBalancePayment($order, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return back()->with('error', 'Silakan login terlebih dahulu untuk menggunakan saldo.');
        }

        if ($user->balance < $order->total_price) {
            return back()->with('error', 'Saldo tidak mencukupi. Silakan isi saldo terlebih dahulu.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $order) {
                // Deduct balance
                $user->decrement('balance', $order->total_price);
                
                // Update Order
                $order->logStatus("Pembayaran menggunakan saldo berhasil. Sisa saldo: " . number_format($user->balance, 0, ',', '.'), 'paid');
                
                // Dispatch Job
                \App\Jobs\ProcessSupplierOrder::dispatch($order);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran saldo berhasil.',
                    'data' => [
                        'order_id' => $order->order_id,
                        'redirect_url' => route('track.order', ['order_id' => $order->order_id])
                    ]
                ]);
            }

            return redirect()->route('track.order', ['order_id' => $order->order_id])->with('success', 'Pembayaran saldo berhasil!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Balance Payment Error', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses saldo.');
        }
    }

    private function processDuitku($order, $paymentMethod, Request $request)
    {
        $duitku = Provider::where('name', 'like', '%Duitku%')->first();
        if (!$duitku || !$duitku->provider_id || !$duitku->api_key) {
            $order->update(['status' => 'failed', 'payload' => ['error' => 'Provider Duitku belum disetting']]);
            return back()->with('error', 'Sistem error: Kredensial Duitku tidak ditemukan.');
        }

        $merchantCode = $duitku->provider_id;
        $merchantKey = $duitku->api_key;
        $orderId = $order->order_id;
        $paymentAmount = $order->total_price;
        $productDetails = $order->product_name;

        // Duitku Minimum Payment Constraint (Usually 10.000 IDR)
        if ($paymentAmount < 10000) {
            $order->update(['status' => 'failed', 'payload' => ['error' => 'Minimum payment for Duitku is 10.000 IDR']]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pembayaran gagal: Minimal pembayaran Duitku adalah Rp 10.000'], 400);
            }
            return back()->with('error', 'Pesanan gagal diinisiasi: Minimal total pembayaran menggunakan Duitku adalah Rp 10.000.');
        }

        // 4. Proses Tembak API Duitku (POST)
        $signature = md5($merchantCode . $orderId . (int)$paymentAmount . $merchantKey);

        $mode = $duitku->mode ?? env('DUITKU_MODE', (env('APP_ENV') === 'local' ? 'sandbox' : 'passport'));
        $duitkuUrl = "https://{$mode}.duitku.com/webapi/api/merchant/v2/inquiry";

        $params = array(
            'merchantCode' => $merchantCode,
            'paymentAmount' => (int)$paymentAmount,
            'merchantOrderId' => $orderId,
            'productDetails' => $productDetails,
            'email' => 'guest@princepay.com',
            'phoneNumber' => '08111222333',
            'customerVaName' => 'PrincePay ' . $orderId,
            'callbackUrl' => url('/api/duitku/callback'),
            'returnUrl' => route('home'),
            'signature' => $signature,
            'expiryPeriod' => 60
        );

        if ($paymentMethod) {
            $params['paymentMethod'] = $paymentMethod->code;
        }

        try {
            Log::info('Duitku Request', ['url' => $duitkuUrl, 'params' => $params]);
            $response = Http::post($duitkuUrl, $params);
            
            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('Duitku API Error', ['status' => $response->status(), 'body' => $errorBody]);
                throw new \Exception("Duitku API error: " . $errorBody);
            }

            $result = $response->json();
            Log::info('Duitku Response', ['status' => $response->status(), 'body' => $result]);

            if (!$result || !is_array($result)) {
                 Log::error('Duitku Invalid JSON', ['body' => $response->body()]);
                 throw new \Exception("Duitku returned invalid response format");
            }

            if ($response->successful() && isset($result['statusCode']) && $result['statusCode'] == '00') {
                if (!empty($result['paymentUrl'])) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Pesanan berhasil dibuat. Silakan lanjut ke pembayaran.',
                            'data' => [
                                'order_id' => $orderId,
                                'payment_url' => $result['paymentUrl'],
                                'reference' => $result['reference'] ?? ''
                            ]
                        ]);
                    }
                    return redirect($result['paymentUrl']);
                } else {
                    $order->update(['status' => 'failed', 'payload' => $result]);
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Gagal generate URL Pembayaran', 'debug' => $result], 400);
                    }
                    return back()->with('error', 'Pesanan gagal diinisiasi: Gagal generate URL Pembayaran.');
                }
            } else {
                $order->update(['status' => 'failed', 'payload' => $result]);
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $result['statusMessage'] ?? 'Inquiry Failed', 'debug' => $result], 400);
                }
                return back()->with('error', 'Pesanan gagal diinisiasi: ' . ($result['statusMessage'] ?? 'Unknown Error Duitku.'));
            }

        } catch (\Exception $e) {
            $order->update(['status' => 'failed', 'payload' => ['error_exception' => $e->getMessage()]]);
            return back()->with('error', 'Gagal terhubung ke Duitku: ' . $e->getMessage());
        }
    }

    private function processDoku($order, $paymentMethod, Request $request)
    {
        $user = $order->user ?? \Illuminate\Support\Facades\Auth::user();
        $dokuService = new \App\Services\DokuService();

        // Map payment method code to DOKU payment_method_types
        $paymentMethodTypes = [];
        if ($paymentMethod) {
            $code = strtoupper($paymentMethod->code);
            if (str_contains($code, 'QRIS')) $paymentMethodTypes = ['QRIS'];
            elseif (str_contains($code, 'BCA')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BCA'];
            elseif (str_contains($code, 'BNI')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BNI'];
            elseif (str_contains($code, 'BRI')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BRI'];
            elseif (str_contains($code, 'MANDIRI')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BANK_MANDIRI'];
            elseif (str_contains($code, 'PERMATA')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BANK_PERMATA'];
            elseif (str_contains($code, 'CIMB')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BANK_CIMB'];
            elseif (str_contains($code, 'DANAMON')) $paymentMethodTypes = ['VIRTUAL_ACCOUNT_BANK_DANAMON'];
            elseif (str_contains($code, 'SHOPEEPAY')) $paymentMethodTypes = ['EMONEY_SHOPEE_PAY'];
            elseif (str_contains($code, 'OVO')) $paymentMethodTypes = ['EMONEY_OVO'];
            elseif (str_contains($code, 'DANA')) $paymentMethodTypes = ['EMONEY_DANA'];
            elseif (str_contains($code, 'LINKAJA')) $paymentMethodTypes = ['EMONEY_LINKAJA'];
            elseif (str_contains($code, 'ALFAMART')) $paymentMethodTypes = ['ONLINE_TO_OFFLINE_ALFA'];
            elseif (str_contains($code, 'INDOMARET')) $paymentMethodTypes = ['ONLINE_TO_OFFLINE_INDOMARET'];
            elseif (str_contains($code, 'CC') || str_contains($code, 'CREDIT')) $paymentMethodTypes = ['CREDIT_CARD'];
        }

        $res = $dokuService->createCheckoutPayment([
            'orderId'            => $order->order_id,
            'amount'             => $order->total_price,
            'name'               => $user->name ?? 'Guest',
            'email'              => $user->email ?? 'guest@neonflux.my.id',
            'phone'              => $user->phone ?? '081122334455',
            'returnUrl'          => route('track.order', ['order_id' => $order->order_id]),
            'notifyUrl'          => url('/api/doku/callback'),
            'paymentMethodTypes' => $paymentMethodTypes,
        ]);

        if (!empty($res['success']) && !empty($res['paymentUrl'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat. Silakan lanjut ke pembayaran.',
                    'data'    => [
                        'order_id'    => $order->order_id,
                        'payment_url' => $res['paymentUrl'],
                    ]
                ]);
            }
            return redirect($res['paymentUrl']);
        }

        // Payment creation failed
        $errorMsg = $res['message'] ?? 'Gagal membuat pembayaran DOKU.';
        
        // Fix array to string conversion
        if (is_array($errorMsg)) {
            // Try to extract readable message if possible
            $errorMsgStr = json_encode($errorMsg);
        } else {
            $errorMsgStr = $errorMsg;
        }

        $order->update(['status' => 'failed', 'payload' => $res['raw'] ?? $res]);
        Log::error('DOKU Checkout Failed', ['order_id' => $order->order_id, 'response' => $res]);

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $errorMsgStr], 400);
        }
        return back()->with('error', 'Pesanan gagal diinisiasi: ' . $errorMsgStr);
    }

    private function processIPaymu($order, $paymentMethod, Request $request)
    {
        /** Samakan dengan Midtrans/DOKU: relasi order + sesi login */
        $user = $order->user ?? \Illuminate\Support\Facades\Auth::user();

        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $orderTag = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $order->order_id));
        $guestEmail = 'guest+' . $orderTag . '@' . $appHost;

        $buyerName = $user->name ?? ('Pelanggan ' . $order->order_id);
        $buyerEmail = $user->email ?? $guestEmail;
        $buyerPhone = $user->phone ?? ('08' . str_pad((string) (abs(crc32($order->order_id . (string) $order->id)) % 1000000000), 9, '0', STR_PAD_LEFT));

        $ipaymuService = new \App\Services\IPaymuService();
        $res = $ipaymuService->createPayment([
            'orderId' => $order->order_id,
            'amount' => $order->total_price,
            'product' => $order->product_name,
            'name' => $buyerName,
            'email' => $buyerEmail,
            'phone' => $buyerPhone,
            'returnUrl' => route('home'),
            'cancelUrl' => route('home'),
            'notifyUrl' => url('/api/ipaymu/callback'),
            'paymentMethod' => $paymentMethod ? $paymentMethod->type : 'va',
            'paymentChannel' => $paymentMethod ? $paymentMethod->code : null,
        ]);

        Log::info('iPaymu Response API:', ['res' => $res]);

        // Normalize iPaymu response keys (Case Insensitivity Fix)
        $status = $res['Status'] ?? $res['status'] ?? null;
        $data = $res['Data'] ?? $res['data'] ?? null;
        $message = $res['Message'] ?? $res['message'] ?? 'Gagal membuat pembayaran iPaymu.';
        if (stripos((string) $message, 'Suspicious buyer') !== false) {
            $message = 'Pembayaran ditolak oleh iPaymu (pembeli terdeteksi risiko). Coba tanpa VPN, jaringan lain, atau login dengan data asli. Jika terus terjadi, hubungi iPaymu atau gunakan metode bayar lain.';
        }

        if ($status == 200 && $data) {
            // Detect device for proper view folder
            $device = (new CatalogController())->deviceType();
            $view = "{$device}.neonflux.payment.ipaymu";
            
            if (!view()->exists($view)) {
                $view = "desktop.neonflux.payment.ipaymu"; // Fallback to desktop
            }

            // Normalize QR Source for QRIS
            $qrUrl = null;
            if (($data['Via'] ?? $data['via'] ?? '') == 'QRIS') {
                $qrString = $data['QrString'] ?? $data['qr_string'] ?? $data['PaymentNo'] ?? $data['payment_no'] ?? null;
                // If it looks like a valid QRIS string (starting with 000201), use external generator as it's more reliable
                if ($qrString && str_starts_with($qrString, '000201')) {
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($qrString) . "&size=400x400";
                } else {
                    $qrUrl = $data['QrImage'] ?? $data['qr_image'] ?? $data['QrTemplate'] ?? $data['qr_template'] ?? null;
                }
            }

            return view($view, [
                'order' => $order,
                'ipaymuData' => $data,
                'qrUrl' => $qrUrl
            ]);
        }

        $order->update(['status' => 'failed', 'payload' => $res]);
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message, 'debug' => $res], 400);
        }
        return back()->with('error', 'Pesanan gagal diinisiasi: ' . $message . ' (Raw: ' . json_encode($res) . ')');
    }

    private function processMidtrans($order, $paymentMethod, Request $request)
    {
        $user = $order->user ?? \Illuminate\Support\Facades\Auth::user();
        $midtransService = new \App\Services\MidtransService();
        
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => (int)$order->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Guest',
                'email' => $user->email ?? 'guest@princepay.com',
                'phone' => $user->phone ?? '081122334455',
            ],
            'item_details' => [
                [
                    'id' => $order->payload['product_code'] ?? 'P001',
                    'price' => (int)$order->total_price,
                    'quantity' => 1,
                    'name' => $order->product_name,
                ]
            ],
            'callbacks' => [
                'finish' => route('home'),
            ],
            'expiry' => [
                'unit' => 'minutes',
                'duration' => 60
            ],
            'metadata' => [
                'order_id' => $order->order_id
            ]
        ];

        // Map Payment Method Code to Midtrans enabled_payments
        if ($paymentMethod) {
            $code = strtoupper($paymentMethod->code);
            $enabledPayments = [];
            
            if (str_contains($code, 'QRIS')) $enabledPayments = ['qris', 'gopay', 'shopeepay'];
            elseif (str_contains($code, 'GOPAY')) $enabledPayments = ['gopay', 'qris'];
            elseif (str_contains($code, 'SHOPEEPAY')) $enabledPayments = ['shopeepay', 'qris'];
            elseif (str_contains($code, 'DANA')) $enabledPayments = ['dana', 'qris'];
            elseif (str_contains($code, 'BCA')) $enabledPayments = ['bca_va'];
            elseif (str_contains($code, 'BNI')) $enabledPayments = ['bni_va'];
            elseif (str_contains($code, 'BRI')) $enabledPayments = ['bri_va'];
            elseif (str_contains($code, 'MANDIRI')) $enabledPayments = ['echannel', 'mandiri_clickpay'];
            elseif (str_contains($code, 'PERMATA')) $enabledPayments = ['permata_va'];
            elseif (str_contains($code, 'ALFAMART')) $enabledPayments = ['alfamart'];
            elseif (str_contains($code, 'INDOMARET')) $enabledPayments = ['indomaret'];
            elseif (str_contains($code, 'OVO') || str_contains($code, 'LINKAJA')) $enabledPayments = ['qris'];

            if (!empty($enabledPayments)) {
                $params['enabled_payments'] = $enabledPayments;
            }
        }
        
        // Add dynamic notification URL for reliability (useful for Snap)
        $params['notification_url'] = url('/api/midtrans/callback');

        try {
            $paymentUrl = $midtransService->createSnapUrl($params);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat. Silakan lanjut ke pembayaran.',
                    'data' => [
                        'order_id' => $order->order_id,
                        'payment_url' => $paymentUrl
                    ]
                ]);
            }
            
            return redirect($paymentUrl);
        } catch (\Exception $e) {
            $order->update(['status' => 'failed', 'payload' => ['error_exception' => $e->getMessage()]]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
        }
    }

    public function midtransCallback(Request $request)
    {
        $midtransService = new \App\Services\MidtransService();
        $payload = $request->all();

        // 1. Signature Verification (Hardened Security)
        if (!$midtransService->verifyNotification($payload)) {
            Log::error('Midtrans Callback: Invalid Signature', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid signature'], 403);
        }
        
        try {
            // 2. Double-Check status with Midtrans API (Secondary Layer of trust)
            $notification = $midtransService->getStatus($request->order_id ?? $request->id);
            
            if (is_array($notification)) {
                $notification = (object)$notification;
            }
            
            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            $order = Order::where('order_id', $orderId)->first();
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            if ($order->status !== 'pending_payment' && ($transaction == 'settlement' || $transaction == 'capture')) {
                 return response()->json(['success' => 'already_processed']);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $order->logStatus('Pembayaran challenge by fraud detection (Midtrans).', 'pending_payment');
                    } else {
                        $this->finalizeOrder($order, 'Midtrans (CC)');
                    }
                }
            } else if ($transaction == 'settlement') {
                $this->finalizeOrder($order, "Midtrans ($type)");
            } else if ($transaction == 'pending') {
                $order->logStatus('Menunggu pembayaran (Midtrans).', 'pending_payment');
            } else if ($transaction == 'deny') {
                $order->logStatus('Pembayaran ditolak (Midtrans).', 'failed');
            } else if ($transaction == 'expire') {
                $order->logStatus('Pembayaran kedaluwarsa (Midtrans).', 'failed');
            } else if ($transaction == 'cancel') {
                $order->logStatus('Pembayaran dibatalkan (Midtrans).', 'failed');
            }

            return response()->json(['success' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error:', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    private function finalizeOrder($order, $providerInfo)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $providerInfo) {
            $order->logStatus("Pembayaran berhasil dikonfirmasi oleh $providerInfo.", 'paid');
            \App\Jobs\ProcessSupplierOrder::dispatch($order);
        });
    }

    public function duitkuCallback(Request $request)
    {
        $merchantCode = $request->input('merchantCode');
        $amount = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $signature = $request->input('signature');
        $resultCode = $request->input('resultCode');
        $reference = $request->input('reference');

        $duitku = Provider::where('name', 'like', '%Duitku%')->first();
        if (!$duitku) return response()->json(['error' => 'Provider Duitku tidak ditemukan'], 400);

        $merchantKey = $duitku->api_key;

        // Normalize: hapus spasi & pastikan amount tanpa desimal (Duitku standard)
        $cleanAmount = (int)$amount;
        $merchantCode = trim($merchantCode);
        $merchantOrderId = trim($merchantOrderId);

        $rawString = $merchantCode . $cleanAmount . $merchantOrderId . $merchantKey;
        $calcSignature = md5($rawString);

        if ($signature !== $calcSignature) {
            Log::warning('Duitku Callback Signature Mismatch', [
                'received_from_postman' => $signature,
                'calculated_by_server' => $calcSignature,
                'string_to_hash' => $rawString, // Copy string ini ke MD5 generator untuk tes
                'merchantOrderId' => $merchantOrderId,
                'received_amount' => $amount,
                'clean_amount_used' => $cleanAmount
            ]);

            // Bypass khusus untuk testing jika Anda ingin memaksa sukses (Hapus jika sudah live!)
            // if (env('APP_ENV') === 'local') { $signature = $calcSignature; }

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_id', $merchantOrderId)->first();
        if (!$order) {
            Log::error('Duitku Callback: Order Not Found', ['order_id' => $merchantOrderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Idempotency: Jika status bukan pending_payment, berarti sudah diproses
        if ($order->status !== 'pending_payment' && $resultCode === '00') {
            Log::info('Duitku Callback: Order already processed (Idempotency)', ['order_id' => $merchantOrderId]);
            return response()->json(['success' => 'already_processed']);
        }

        if ($resultCode === '00' && $order->status === 'pending_payment') {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($order, $reference) {
                    // Update Order status and log payment success
                    $order->logStatus('Pembayaran berhasil dikonfirmasi oleh Duitku.', 'paid', [
                        'duitku_reference' => $reference
                    ]);

                    // Dispatch Job to process supplier fulfillment in background
                    \App\Jobs\ProcessSupplierOrder::dispatch($order);
                });

                return response()->json(['success' => 'ok']);
            } catch (\Exception $e) {
                Log::error('Duitku Callback Transaction Error', ['order_id' => $order->order_id, 'msg' => $e->getMessage()]);
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        } else if ($resultCode === '01') {
            $order->logStatus('Pembayaran gagal atau dibatalkan oleh pengguna.', 'failed');
            return response()->json(['success' => 'ok']);
        }

        return response()->json(['success' => 'ok']);
    }

    public function ipaymuCallback(Request $request)
    {
        Log::info('iPaymu Callback Full Info:', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_content' => $request->getContent()
        ]);

        $ipaymuService = new \App\Services\IPaymuService();
        
        // Try multiple header names for signature
        $signature = $request->header('x-signature')
                  ?? $request->header('signature') 
                  ?? $request->header('Signature') 
                  ?? $request->header('X-Ipaymu-Signature') 
                  ?? $request->input('signature') 
                  ?? '';
                  
        $rawBody = $request->getContent();



        if (!$ipaymuService->validateCallback($request->all(), $signature, $rawBody)) {
            Log::error('iPaymu Callback: Invalid Signature', [
                'signature_received' => $signature,
                'header_x_sig' => $request->header('x-signature'),
                'has_raw_body' => !empty($rawBody)
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }


        $trxId = $request->input('trx_id');
        $referenceId = $request->input('reference_id');
        $status = $request->input('status');
        $statusCode = $request->input('status_code');

        $order = Order::where('order_id', $referenceId)->first();
        if (!$order) {
            Log::error('iPaymu Callback: Order Not Found', ['order_id' => $referenceId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Idempotency
        if ($order->status !== 'pending_payment' && $statusCode == 1) {
            return response()->json(['success' => 'already_processed']);
        }

        if ($statusCode == 1 && $order->status === 'pending_payment') {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($order, $trxId) {
                    $order->logStatus('Pembayaran berhasil dikonfirmasi oleh iPaymu.', 'paid', [
                        'ipaymu_trx_id' => $trxId
                    ]);

                    \App\Jobs\ProcessSupplierOrder::dispatch($order);
                });

                return response()->json(['success' => 'ok']);
            } catch (\Exception $e) {
                Log::error('iPaymu Callback Transaction Error', ['order_id' => $order->order_id, 'msg' => $e->getMessage()]);
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        } else if ($status === 'cancel' || $status === 'failed') {
            $order->logStatus('Pembayaran gagal atau dibatalkan oleh pengguna (iPaymu).', 'failed');
            return response()->json(['success' => 'ok']);
        }

        return response()->json(['success' => 'ok']);
    }

    public function dokuCallback(Request $request)
    {
        Log::info('DOKU Callback Received:', [
            'headers' => [
                'Client-Id'         => $request->header('Client-Id'),
                'Request-Id'        => $request->header('Request-Id'),
                'Request-Timestamp' => $request->header('Request-Timestamp'),
                'Signature'         => $request->header('Signature') ? '***PRESENT***' : '***MISSING***',
            ],
            'body' => $request->all(),
        ]);

        $dokuService = new \App\Services\DokuService();

        // 1. Validate Signature
        if (!$dokuService->validateNotificationSignature($request)) {
            Log::error('DOKU Callback: Signature validation FAILED');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        Log::info('DOKU Callback: Signature VALID');

        try {
            $payload = $request->all();

            // 2. Extract key fields from notification
            $invoiceNumber  = $payload['order']['invoice_number'] ?? null;
            $transStatus    = $payload['transaction']['status'] ?? null;
            $serviceId      = $payload['service']['id'] ?? 'UNKNOWN';
            $channelId      = $payload['channel']['id'] ?? 'UNKNOWN';

            if (!$invoiceNumber) {
                Log::error('DOKU Callback: Missing invoice_number', ['payload' => $payload]);
                return response()->json(['error' => 'Missing invoice_number'], 400);
            }

            // 3. Find Order
            $order = Order::where('order_id', $invoiceNumber)->first();
            if (!$order) {
                Log::error('DOKU Callback: Order not found', ['invoice' => $invoiceNumber]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // 4. Idempotency check
            if ($order->status !== 'pending_payment' && $transStatus === 'SUCCESS') {
                Log::info('DOKU Callback: Already processed', ['order_id' => $invoiceNumber]);
                return response()->json(['success' => 'already_processed']);
            }

            // 5. Process based on transaction status
            if ($transStatus === 'SUCCESS' && $order->status === 'pending_payment') {
                $this->finalizeOrder($order, "DOKU ({$serviceId}/{$channelId})");
                Log::info('DOKU Callback: Order finalized', ['order_id' => $invoiceNumber]);
            } elseif ($transStatus === 'FAILED') {
                $order->logStatus('Pembayaran gagal (DOKU - ' . $channelId . ').', 'failed');
                Log::info('DOKU Callback: Payment failed', ['order_id' => $invoiceNumber]);
            } else {
                Log::info('DOKU Callback: Unhandled status', [
                    'order_id' => $invoiceNumber,
                    'status'   => $transStatus,
                ]);
            }

            return response()->json(['success' => 'ok']);
        } catch (\Exception $e) {
            Log::error('DOKU Callback Exception:', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Game code mapping for Codashop nickname validation.
     * Each entry: 'category_slug' => ['vppId' => ..., 'vppPrice' => ..., 'voucherTypeName' => ..., 'nicknameKey' => ...]
     */
    private static $codashopGames = [
        'mobile-legends'    => ['vppId' => 27684, 'vppPrice' => 527250, 'voucherTypeName' => 'MOBILE_LEGENDS', 'nicknameKey' => 'username'],
        'free-fire'         => ['vppId' => 28153, 'vppPrice' => 1000000, 'voucherTypeName' => 'FREEFIRE', 'nicknameKey' => 'roles.0.role'],
        'genshin-impact'    => ['vppId' => 116118, 'vppPrice' => 815000, 'voucherTypeName' => 'GENSHIN_IMPACT', 'nicknameKey' => 'username'],
        'call-of-duty'      => ['vppId' => 46251, 'vppPrice' => 2000000, 'voucherTypeName' => 'CALL_OF_DUTY', 'nicknameKey' => 'username'],
        'valorant'          => ['vppId' => 950605, 'vppPrice' => 739000, 'voucherTypeName' => 'VALORANT', 'nicknameKey' => 'username'],
        'pubg-mobile'       => ['vppId' => 11568, 'vppPrice' => 1500000, 'voucherTypeName' => 'PUBG_MOBILE', 'nicknameKey' => 'username'],
        'arena-of-valor'    => ['vppId' => 8003, 'vppPrice' => 300000, 'voucherTypeName' => 'AOV', 'nicknameKey' => 'username'],
        'league-of-legends' => ['vppId' => 372111, 'vppPrice' => 360000, 'voucherTypeName' => 'WILD_RIFT', 'nicknameKey' => 'username'],
        'honkai-star-rail'  => ['vppId' => 116118, 'vppPrice' => 815000, 'voucherTypeName' => 'HONKAI_STAR_RAIL', 'nicknameKey' => 'username'],
    ];

    /**
     * Samakan slug kategori (URL / DB) dengan kunci mapping Codashop.
     *
     * @return array{0: ?array, 1: string} [config atau null, slug normal untuk log]
     */
    private static function resolveCodashopGameConfig(?string $requestGameSlug, ?string $operatorId): array
    {
        $slug = $requestGameSlug ? trim((string) $requestGameSlug) : '';

        if ($slug === '' && $operatorId) {
            $category = \App\Models\Category::where('ext_id', $operatorId)->first();
            if ($category) {
                $slug = (string) ($category->slug ?: \Illuminate\Support\Str::slug($category->name));
            }
        }

        $aliases = [
            'mlbb' => 'mobile-legends',
            'ml' => 'mobile-legends',
            'mobile-legend' => 'mobile-legends',
            'mobile-legends-bang-bang' => 'mobile-legends',
            'honkai' => 'honkai-star-rail',
            'hsr' => 'honkai-star-rail',
            'genshin' => 'genshin-impact',
            'pubg' => 'pubg-mobile',
            'codm' => 'call-of-duty',
            'aov' => 'arena-of-valor',
            'wild-rift' => 'league-of-legends',
            'lol-wild-rift' => 'league-of-legends',
            'freefire' => 'free-fire',
            'ff' => 'free-fire',
        ];

        $lookup = strtolower($slug);
        $normalized = $aliases[$lookup] ?? $slug;

        if (isset(self::$codashopGames[$normalized])) {
            return [self::$codashopGames[$normalized], $normalized];
        }

        if ($normalized !== '') {
            $ln = strtolower($normalized);
            foreach (self::$codashopGames as $key => $config) {
                if (str_contains($ln, $key) || str_contains($key, $ln)) {
                    return [$config, $key];
                }
            }
        }

        return [null, $slug];
    }

    /**
     * Deteksi sukses respons Codashop initPayment (bentuk field bisa bervariasi).
     */
    private static function codashopCheckSucceeded(?array $result): bool
    {
        if (! is_array($result) || $result === []) {
            return false;
        }

        if (isset($result['RESULT_CODE']) && (string) $result['RESULT_CODE'] === '10001') {
            return false;
        }

        if (trim((string) ($result['errorMsg'] ?? '')) !== '') {
            return false;
        }

        if (! empty($result['is_publisher_validate_error'])) {
            return false;
        }

        $s = $result['success'] ?? null;
        if ($s === true || $s === 1 || $s === '1' || $s === 'true') {
            return true;
        }

        $c = $result['confirmation'] ?? null;
        if (($c === true || $c === 1 || $c === '1') && ! empty($result['confirmationFields']) && is_array($result['confirmationFields'])) {
            return true;
        }

        return false;
    }

    /**
     * Ambil nickname dari confirmationFields atau dari result (JSON URL-encoded).
     */
    private static function extractCodashopNickname(array $result, array $gameConfig): string
    {
        $nicknameKey = $gameConfig['nicknameKey'];
        $fields = $result['confirmationFields'] ?? [];
        if (! is_array($fields)) {
            $fields = [];
        }

        $nickname = '';
        if ($nicknameKey === 'roles.0.role') {
            $nickname = (string) urldecode($fields['roles'][0]['role'] ?? '');
        } else {
            $nickname = (string) urldecode($fields[$nicknameKey] ?? '');
        }

        $nickname = trim(str_replace('+', ' ', $nickname));

        if ($nickname === '' && ! empty($result['result'])) {
            $payload = json_decode(rawurldecode((string) $result['result']), true);
            if (is_array($payload) && ! empty($payload['username'])) {
                $nickname = trim(str_replace('+', ' ', urldecode((string) $payload['username'])));
            }
        }

        return $nickname !== '' ? $nickname : 'Nickname ditemukan';
    }

    public function checkPlayerId(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'operator_id' => 'nullable|string',
            'zone_id' => 'nullable',
            'game_slug' => 'nullable|string',
        ]);

        [$gameConfig, $resolvedSlug] = self::resolveCodashopGameConfig(
            $request->game_slug,
            $request->operator_id
        );

        if (! $gameConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi ID belum tersedia untuk game ini.',
            ]);
        }

        try {
            $postdata = [
                'voucherPricePoint.id'            => $gameConfig['vppId'],
                'voucherPricePoint.price'         => $gameConfig['vppPrice'],
                'voucherPricePoint.variablePrice' => 0,
                'user.userId'                     => $request->user_id,
                'user.zoneId'                     => $request->zone_id ?? '',
                'voucherTypeName'                 => $gameConfig['voucherTypeName'],
                'lvtId'                           => '',
                'shopLang'                        => 'id_ID',
                'dynamicSkuToken'                 => '',
                'pricePointDynamicSkuToken'       => '',
                'voucherTypeId'                   => ''
            ];

            Log::info('CheckID Codashop Request', [
                'game_slug_resolved' => $resolvedSlug,
                'voucherTypeName' => $gameConfig['voucherTypeName'],
                'user_id' => $request->user_id,
                'zone_id' => $request->zone_id ?? '',
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'Origin'       => 'https://www.codashop.com',
                'Referer'      => 'https://www.codashop.com/',
                'User-Agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ])->connectTimeout(12)->timeout(22)->post('https://order-sg.codashop.com/initPayment.action', $postdata);

            $result = $response->json();
            if (! is_array($result) && is_string($response->body()) && $response->body() !== '') {
                $decoded = json_decode($response->body(), true);
                $result = is_array($decoded) ? $decoded : null;
            }

            Log::info('CheckID Codashop Response', [
                'http' => $response->status(),
                'body' => $result,
            ]);

            if ($response->successful() && self::codashopCheckSucceeded($result)) {
                $nickname = self::extractCodashopNickname($result, $gameConfig);

                return response()->json([
                    'success' => true,
                    'nickname' => $nickname,
                ]);
            } else {
                // Check for rate limit
                if (is_array($result) && isset($result['RESULT_CODE']) && $result['RESULT_CODE'] == '10001') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan. Tunggu 5 detik dan coba lagi.'
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak ditemukan. Pastikan User ID dan Zone ID sudah benar.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('CheckID Exception', ['msg' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi. Coba lagi nanti.'
            ], 500);
        }
    }

    public function trackOrder(Request $request)
    {
        $order = null;
        if ($request->has('order_id')) {
            $order = Order::with('logs')->where('order_id', $request->order_id)->first();

            if (!$order && $request->filled('order_id')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
                }
                return back()->with('error', 'Nomor Transaksi tidak ditemukan.');
            }
        }

        if ($request->expectsJson() && $order) {
            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->order_id,
                    'product' => $order->product_name,
                    'status' => $order->status,
                    'price' => $order->total_price,
                    'method' => $order->payment_method,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'logs' => $order->logs->map(function($log) {
                        return [
                            'time' => $log->created_at->format('H:i:s'),
                            'message' => $log->message,
                            'status' => $log->status_to
                        ];
                    })
                ]
            ]);
        }

        $latestOrders = Order::latest()->take(10)->get();

        // Detect device for proper view folder
        $device = (new CatalogController())->deviceType();
        $view = "{$device}.neonflux.track";

        if (!view()->exists($view)) {
            $view = "desktop.neonflux.track"; // Fallback to desktop
        }

        return view($view, compact('order', 'latestOrders'));
    }
}
