<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function deviceType()
    {
        return app(CatalogController::class)->deviceType();
    }

    public function index()
    {
        $user = Auth::user();
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        $recentDeposits = Deposit::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $view = $this->deviceType() . '.user.dashboard';
        
        if (!view()->exists($view)) {
             $view = 'desktop.user.dashboard';
        }

        return view($view, compact('user', 'recentOrders', 'recentDeposits'));
    }

    public function profile()
    {
        $user = Auth::user();
        $view = $this->deviceType() . '.user.profile';
        
        if (!view()->exists($view)) {
             // Fallback to desktop if mobile view not yet created
             $view = 'desktop.user.profile';
        }

        return view($view, compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function riwayat()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $view = $this->deviceType() . '.user.history';
        
        if (!view()->exists($view)) {
             $view = 'desktop.user.history';
        }

        return view($view, compact('orders'));
    }

    public function deposit()
    {
        $user = Auth::user();
        $paymentMethods = \App\Models\PaymentMethod::where('status', 'Aktif')
            ->where('provider', 'TokoVoucher')
            ->get();
        $view = $this->deviceType() . '.user.deposit';
        
        if (!view()->exists($view)) {
             $view = 'desktop.user.deposit';
        }

        return view($view, compact('paymentMethods', 'user'));
    }

    public function storeDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:50000|max:2500000',
            'custom_amount' => 'nullable|numeric|min:50000|max:2500000',
            'payment_method' => 'required|string'
        ]);

        $amount = $request->custom_amount ?: $request->amount;
        if (!$amount) {
            return back()->with('error', 'Silakan pilih atau masukkan nominal deposit.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $depositId = 'DEP-' . date('ymdHis') . strtoupper(\Illuminate\Support\Str::random(4));

        $provider = \App\Models\Provider::where('name', 'like', '%Toko%')->first();
        if (!$provider || !$provider->provider_id || !$provider->api_key) {
            return back()->with('error', 'Metode deposit sedang tidak tersedia (Provider error).');
        }

        $params = [
            'member_code' => $provider->provider_id,
            'secret' => $provider->api_key,
            'nominal' => (int)$amount,
            'kode' => $request->payment_method,
            'ref_id' => $depositId
        ];

        try {
            // According to documentation: https://api.tokovoucher.net/v1/deposit?member_code=[YOUR_MEMBER_CODE]&secret=[YOUR_SECRET_KEY]&nominal=[NOMINAL]&kode=[KODE_BAYAR]
            // The docs show it as query params in the URL, but the search results suggest POST application/json for transactions.
            // Let's try as query params first as per the specific deposit endpoint documentation provided by the user.
            $url = "https://api.tokovoucher.net/v1/deposit?" . http_build_query($params);
            
            \Illuminate\Support\Facades\Log::info('TokoVoucher Deposit Request', ['url' => $url]);
            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if (!$response->successful()) {
                throw new \Exception("TokoVoucher API Error: " . $response->status());
            }

            $result = $response->json();
            \Illuminate\Support\Facades\Log::info('TokoVoucher Deposit Response', ['body' => $result]);

            if (isset($result['status']) && $result['status'] == 1) {
                $data = $result['data'];
                
                Deposit::create([
                    'deposit_id' => $depositId,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'method' => $request->payment_method,
                    'status' => 'pending',
                    'payload' => $result
                ]);

                if (isset($data['pay']) && filter_var($data['pay'], FILTER_VALIDATE_URL)) {
                    return redirect($data['pay']);
                }

                return back()->with('success', 'Deposit berhasil dibuat. Silakan selesaikan pembayaran sesuai instruksi.');
            } else {
                return back()->with('error', 'Gagal membuat deposit: ' . ($result['error_msg'] ?? 'Unknown Error'));
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Deposit Exception', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function riwayatDeposit()
    {
        $deposits = Deposit::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $view = $this->deviceType() . '.user.riwayat_deposit';
        
        if (!view()->exists($view)) {
             $view = 'desktop.user.riwayat_deposit';
        }

        return view($view, compact('deposits'));
    }
}
