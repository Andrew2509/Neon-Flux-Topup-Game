<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Services\JwtService;
use App\Services\WhatsAppService as WAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $whatsapp;
    protected $jwt;

    public function __construct(WAService $whatsapp, JwtService $jwt)
   {
        $this->whatsapp = $whatsapp;
        $this->jwt = $jwt;
    }
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/');
        }
        return view('desktop.neonflux.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if phone is verified
            if (!$user->phone_verified_at && $user->role !== 'admin') {
                // Generate and send new OTP
                $otpCode = rand(100000, 999999);
                Otp::updateOrCreate(
                    ['phone' => $user->phone, 'type' => 'register'],
                    [
                        'code' => $otpCode,
                        'expires_at' => Carbon::now()->addMinutes(10),
                    ]
                );

                $message = "Halo {$user->name}, silakan verifikasi nomor Anda. Kode OTP Anda adalah: *{$otpCode}*.";
                $this->whatsapp->sendMessage($user->phone, $message);

                return redirect()->route('verify.otp', ['type' => 'register']);
            }

            if ($request->expectsJson()) {
                $token = $this->jwt->generateToken([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);

                return response()->json([
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user
                ]);
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => __('auth.failed')], 401);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showRegister()
    {
        return view('desktop.neonflux.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if user exists by email or phone
        $existingUser = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if ($existingUser) {
            // If already verified, throw validation error
            if ($existingUser->phone_verified_at) {
                $field = $existingUser->email === $request->email ? 'email' : 'phone';
                throw ValidationException::withMessages([
                    $field => ["Data ini sudah terdaftar dan terverifikasi."],
                ]);
            }

            // If not verified, update their info and proceed to re-verify
            $existingUser->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            $user = $existingUser;
        } else {
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'member',
                'status' => 'active',
                'balance' => 0,
            ]);
        }

        // Generate and send OTP
        $otpCode = rand(100000, 999999);
        Otp::updateOrCreate(
            ['phone' => $user->phone, 'type' => 'register'],
            [
                'code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $message = "Halo {$user->name}, kode verifikasi pendaftaran Anda adalah: *{$otpCode}*. Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.";
        $this->whatsapp->sendMessage($user->phone, $message);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Registration successful. Please verify OTP.',
                'phone' => $user->phone
            ]);
        }

        Auth::login($user);

        return redirect()->route('verify.otp', ['type' => 'register']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('desktop.neonflux.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        // Generate and send OTP
        $otpCode = rand(100000, 999999);
        Otp::updateOrCreate(
            ['phone' => $request->phone, 'type' => 'reset'],
            [
                'code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $message = "Halo {$user->name}, kode verifikasi lupa kata sandi Anda adalah: *{$otpCode}*. Kode ini berlaku selama 10 menit.";
        $this->whatsapp->sendMessage($request->phone, $message);

        return redirect()->route('verify.otp', ['type' => 'reset', 'phone' => $request->phone]);
    }

    public function showVerifyOtp(Request $request)
    {
        return view('desktop.neonflux.auth.verify-otp', [
            'type' => $request->type,
            'phone' => $request->phone ?? ($request->user() ? $request->user()->phone : null),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
            'type' => ['required', 'string', 'in:register,reset'],
        ]);

        $otp = Otp::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('type', $request->type)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Kode OTP tidak valid atau sudah kadaluarsa.']);
        }

        if ($request->type === 'register') {
            $user = User::where('phone', $request->phone)->first();
            $user->update(['phone_verified_at' => Carbon::now()]);
            $otp->delete();

            // Log the user in if they aren't already
            if (!Auth::check()) {
                Auth::login($user);
                if (!$request->expectsJson()) {
                    $request->session()->regenerate();
                }
            }

            if ($request->expectsJson()) {
                $token = $this->jwt->generateToken([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);

                return response()->json([
                    'message' => 'OTP verified successfully',
                    'token' => $token,
                    'user' => $user
                ]);
            }

            return redirect('/')->with('status', 'Nomor telepon Anda berhasil diverifikasi!');
        } else {
            // For reset, we keep the OTP until password is changed, or just pass a signed session
            session(['otp_verified_phone' => $request->phone]);
            $otp->delete();
            return redirect()->route('password.reset.phone');
        }
    }

    public function showResetPasswordForm()
    {
        if (!session('otp_verified_phone')) {
            return redirect()->route('password.request');
        }
        return view('desktop.neonflux.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $phone = session('otp_verified_phone');
        if (!$phone) {
            return redirect()->route('password.request');
        }

        $user = User::where('phone', $phone)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget('otp_verified_phone');

        return redirect()->route('login')->with('status', 'Kata sandi Anda berhasil diperbarui!');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'type' => ['required', 'string', 'in:register,reset'],
        ]);

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor telepon tidak terdaftar.']);
        }

        $otpCode = rand(100000, 999999);
        Otp::updateOrCreate(
            ['phone' => $request->phone, 'type' => $request->type],
            [
                'code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $typeText = $request->type === 'register' ? 'pendaftaran' : 'lupa kata sandi';
        $message = "Halo {$user->name}, kode verifikasi {$typeText} baru Anda adalah: *{$otpCode}*.";
        $this->whatsapp->sendMessage($request->phone, $message);

        return back()->with('status', 'Kode OTP baru telah dikirim ke WhatsApp Anda!');
    }
}
