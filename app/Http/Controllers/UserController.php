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
            ->where('provider', 'like', '%iPaymu%')
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

        $pm = \App\Models\PaymentMethod::where('code', $request->payment_method)->first();
        if (!$pm) {
            return back()->with('error', 'Metode pembayaran tidak valid.');
        }

        $ipaymu = new \App\Services\IPaymuService();
        
        // Data for iPaymu
        $payload = [
            'orderId' => $depositId,
            'amount' => (int)$amount,
            'name' => $user->username ?? $user->name ?? 'User',
            'email' => $user->email ?? 'customer@princepay.com',
            'phone' => $user->no_wa ?? '081122334455',
            'product' => 'Top Up Saldo PrincePay',
            'notifyUrl' => route('api.ipaymu.callback'),
            'returnUrl' => route('user.dashboard'),
            'cancelUrl' => route('user.deposit'),
            'paymentMethod' => strtolower($pm->type),
            'paymentChannel' => $pm->code,
        ];

        try {
            $res = $ipaymu->createPayment($payload);
            $statusCode = $res['Status'] ?? $res['status'] ?? 0;
            $data = $res['Data'] ?? $res['data'] ?? [];
            $paymentUrl = $data['Url'] ?? $data['url'] ?? null;

            if ($statusCode == 200 && $paymentUrl) {
                Deposit::create([
                    'deposit_id' => $depositId,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'method' => $pm->name,
                    'status' => 'pending',
                    'payload' => [
                        'ipaymu' => $res,
                        'request' => $payload
                    ]
                ]);

                return redirect($paymentUrl);
            } else {
                $msg = $res['Message'] ?? $res['message'] ?? 'Gagal membuat transaksi ke iPaymu.';
                return back()->with('error', 'Gagal membuat transaksi: ' . $msg);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('iPaymu Deposit Error', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
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
