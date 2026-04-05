<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP - PrincePayGaming</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Hanya CSS: halaman ini tidak butuh bundle JS top-up (mengurangi konflik ekstensi browser di konsol) --}}
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-size: 20px; }

        :root {
            --primary: #e53734;
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .focus\:ring-primary:focus { --tw-ring-color: var(--primary); }
        .hover\:bg-primary\/90:hover { background-color: rgba(229, 55, 52, 0.9); }
        .shadow-primary\/20 { --tw-shadow-color: rgba(229, 55, 52, 0.2); }
    </style>
</head>
<body class="bg-[#0a0a0a] min-h-screen flex items-center justify-center p-4 sm:p-8 selection:bg-primary/30 selection:text-white">

    <div class="flex flex-col lg:flex-row w-full max-w-[1200px] bg-black rounded-3xl overflow-hidden shadow-2xl border border-white/10 relative my-8">

        <!-- Left Side: OTP Verification Form -->
        <div class="w-full lg:w-1/2 flex flex-col p-6 sm:p-8 lg:p-10 xl:p-12 justify-center bg-black min-h-[600px]">
            @include('partials.neonflux.auth-brand-header', ['marginBottom' => 'mb-8'])

            <!-- Title -->
            <div class="mb-8">
                <h1 class="text-white text-[2.25rem] font-black leading-none tracking-tight mb-3">Verifikasi OTP</h1>
                <p class="text-zinc-400 text-base font-medium">Kami telah mengirimkan kode OTP ke WhatsApp Anda <strong>{{ $phone }}</strong>. Silakan masukkan kode tersebut di bawah ini.</p>
            </div>

            <!-- Error Message -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ url('verify-otp') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="phone" value="{{ $phone }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <div>
                    <label class="block text-zinc-300 text-sm font-bold mb-2.5" for="code">Kode OTP (6 Digit)</label>
                    <input class="w-full h-14 px-4 text-center text-3xl tracking-[0.5em] rounded-xl border @error('code') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-black" id="code" name="code" placeholder="------" type="text" maxlength="6" required autofocus />
                </div>

                <button class="w-full h-14 bg-primary hover:bg-primary/90 text-white font-extrabold text-lg rounded-xl shadow-xl shadow-primary/20 transition-all active:scale-[0.98]" type="submit">
                    Verifikasi Kode
                </button>
            </form>

            @if (session('status'))
                <div class="mt-4 p-3 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-sm text-center font-bold">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-8 pt-6 border-t border-zinc-800/50 flex flex-col items-center gap-4">
                <p class="text-zinc-500 text-sm font-medium text-center">Belum menerima kode? Gunakan tombol di bawah untuk mengirim ulang.</p>
                <form action="{{ route('resend.otp') }}" method="POST">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <button type="submit" class="text-primary font-bold hover:underline decoration-2 underline-offset-4">
                        Kirim ulang kode
                    </button>
                </form>
                <a class="flex items-center gap-2 text-zinc-500 hover:text-white transition-colors font-bold text-sm" href="{{ url('login-ui') }}">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke login
                </a>
            </div>
        </div>

        <!-- Right Side: Cinematic Artwork -->
        <div class="hidden lg:flex lg:w-1/2 p-4">
            <div class="relative w-full h-full rounded-2xl overflow-hidden bg-zinc-900 group">
                <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000">
                    <source src="{{ asset('video/362c7c33834539d602e1fdddd88792fd_720w.mp4') }}" type="video/mp4">
                </video>
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-linear-to-t from-black/95 via-black/20 to-transparent"></div>

                <!-- Artist Profile Snippet -->
                <div class="absolute bottom-10 left-10 flex items-center gap-5 bg-black/40 backdrop-blur-xl p-5 rounded-2xl border border-white/10 shadow-2xl">
                    <div class="size-14 rounded-full overflow-hidden border-2 border-primary/50 p-0.5">
                        <img alt="Prince Gamer" class="w-full h-full object-cover rounded-full" src="https://ui-avatars.com/api/?name=Pro+Gaming&background=e53734&color=fff&size=100"/>
                    </div>
                    <div>
                        <p class="text-white text-[10px] font-black opacity-60 uppercase tracking-[0.2em] mb-1">Featured Artist</p>
                        <p class="text-white text-xl font-black leading-tight tracking-tight">Prince Gamer</p>
                        <p class="text-zinc-400 text-sm font-semibold">Pro Level Topup</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
