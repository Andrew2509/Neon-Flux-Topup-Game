<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PrincePayGaming</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Custom CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-size: 20px; }

        /* Inline Tailwind Config for Custom Colors if Vite doesn't have them yet */
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

        <!-- Left Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col p-6 sm:p-8 lg:p-10 xl:p-12 justify-between bg-black">
            @include('partials.neonflux.auth-brand-header')

            <!-- Greeting -->
            <div class="mb-4">
                <h1 class="text-white text-[2.25rem] font-black leading-none tracking-tight mb-2">Welcome Back</h1>
                <p class="text-zinc-400 text-base font-medium">Please login to your account</p>
            </div>

            <!-- Form -->
            <form action="{{ url('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-zinc-300 text-sm font-bold mb-2.5" for="email">Email</label>
                    <input class="w-full h-14 px-4 rounded-xl border @error('email') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="email" name="email" placeholder="Enter your email" type="email" value="{{ old('email') }}" required />
                    @error('email')
                        <p class="text-primary text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2.5">
                        <label class="block text-zinc-300 text-sm font-bold" for="password">Password</label>
                        <a class="text-primary text-sm font-bold hover:underline decoration-2 underline-offset-4" href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <input class="w-full h-14 px-4 rounded-xl border border-zinc-800 bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="password" name="password" placeholder="Enter your password" type="password" required />
                </div>
                <button class="w-full h-14 bg-primary hover:bg-primary/90 text-white font-extrabold text-lg rounded-xl shadow-xl shadow-primary/20 transition-all active:scale-[0.98]" type="submit">
                    Login
                </button>
            </form>

            <p class="text-zinc-500 text-sm font-medium text-center mt-6">
                Don't have an account? <a class="text-primary font-bold hover:underline decoration-2 underline-offset-4 ml-1" href="{{ url('register-ui') }}">Sign up</a>
            </p>
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
                        <img alt="Andrew.ui Profile" class="w-full h-full object-cover rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBF3kW80PKR0HJWTgWXfm7yanWVcRXbM2xwn0oKi7sNIz6OwAld9bPqQrXZchRKbffv1kIr4xodry-SM-aEEVH8B7Q2InwOZNO4Ea08tEP4O0VJLX7elytWi4MAQsgPEVDNg1Uhw-iAeJ7HZKH6XN7EJgtD8FHnX6PvpHu7oW7LHqFCFCBY3PvX8TiiRQ3eZqbECzrpQUoWeMwHz3qW1MuoPqUsaD9xMeafvMB0IVKjJk8uS8QCzdu3CbJL14WyndZZwLhMbbCP8_Sk"/>
                    </div>
                    <div>
                        <p class="text-white text-[10px] font-black opacity-60 uppercase tracking-[0.2em] mb-1">Featured Artist</p>
                        <p class="text-white text-xl font-black leading-tight tracking-tight">Andrew.ui</p>
                        <p class="text-zinc-400 text-sm font-semibold">UI & Illustration</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
