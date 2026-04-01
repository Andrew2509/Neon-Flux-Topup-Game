<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - PrincePayGaming</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Custom CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        /* Social Buttons from Uiverse.io */
        .social-buttons {
          display: flex;
          justify-content: center;
          align-items: center;
          background-color: transparent;
          padding: 10px;
          border-radius: 5em;
        }

        .social-button {
          display: flex;
          justify-content: center;
          align-items: center;
          width: 44px;
          height: 44px;
          border-radius: 50%;
          margin: 0 8px;
          background-color: #1a1a1a;
          box-shadow: 0px 0px 4px rgba(0,0,0,0.5);
          transition: 0.3s;
          border: 1px solid rgba(255,255,255,0.1);
        }

        .social-button:hover {
          background-color: #262626;
          box-shadow: 0px 0px 8px rgba(229, 55, 52, 0.4);
          transform: translateY(-2px);
        }
/*  */
        .social-buttons svg {
          transition: 0.3s;
          height: 18px;
        }

        .facebook { background-color: #3b5998; }
        .facebook svg { fill: #f2f2f2; }
        .facebook:hover svg { fill: #3b5998; }

        .github { background-color: #333; }
        .github svg { width: 22px; height: 22px; fill: #f2f2f2; }
        .github:hover svg { fill: #333; }

        .linkedin { background-color: #0077b5; }
        .linkedin svg { fill: #f2f2f2; }
        .linkedin:hover svg { fill: #0077b5; }

        .instagram { background-color: #c13584; }
        .instagram svg { fill: #f2f2f2; }
        .instagram:hover svg { fill: #c13584; }

        .whatsapp { background-color: #25d366; }
        .whatsapp svg { fill: #f2f2f2; }
        .whatsapp:hover svg { fill: #25d366; }
    </style>
</head>
<body class="bg-[#0a0a0a] min-h-screen flex items-center justify-center p-4 sm:p-8 selection:bg-primary/30 selection:text-white">

    <div class="flex flex-col lg:flex-row w-full max-w-[1200px] bg-black rounded-3xl overflow-hidden shadow-2xl border border-white/10 relative my-8">

        <!-- Left Side: Register Form -->
        <div class="w-full lg:w-1/2 flex flex-col p-6 sm:p-8 lg:p-10 xl:p-12 justify-between bg-black">
            <!-- Header/Logo -->
            <div class="flex items-center gap-3 mb-6">
                <div class="size-9 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                    <svg class="size-6" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V44" fill="currentColor" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-white text-2xl font-black tracking-tighter">{{ get_setting('site_name', 'NEON FLUX') }}</h2>
            </div>

            <!-- Greeting -->
            <div class="mb-4">
                <h1 class="text-white text-[2.25rem] font-black leading-none tracking-tight mb-2">Create Account</h1>
                <p class="text-zinc-400 text-base font-medium">Join {{ get_setting('site_name', 'Neon Flux') }} today</p>
            </div>

            <!-- Form -->
            <form action="{{ url('register') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-zinc-300 text-sm font-bold mb-1.5" for="name">Full Name</label>
                    <input class="w-full h-14 px-4 rounded-xl border @error('name') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="name" name="name" placeholder="Enter your full name" type="text" value="{{ old('name') }}" required />
                    @error('name')
                        <p class="text-primary text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-zinc-300 text-sm font-bold mb-1.5" for="email">Email</label>
                    <input class="w-full h-14 px-4 rounded-xl border @error('email') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="email" name="email" placeholder="Enter your email" type="email" value="{{ old('email') }}" required />
                    @error('email')
                        <p class="text-primary text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-zinc-300 text-sm font-bold mb-1.5" for="phone">Phone Number (WhatsApp)</label>
                    <input class="w-full h-14 px-4 rounded-xl border @error('phone') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="phone" name="phone" placeholder="Enter your WhatsApp number" type="text" value="{{ old('phone') }}" required />
                    @error('phone')
                        <p class="text-primary text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-300 text-sm font-bold mb-1.5" for="password">Password</label>
                        <input class="w-full h-14 px-4 rounded-xl border @error('password') border-primary @else @enderror bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="password" name="password" placeholder="Password" type="password" required />
                        @error('password')
                            <p class="text-primary text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-bold mb-1.5" for="password_confirmation">Confirm</label>
                        <input class="w-full h-14 px-4 rounded-xl border border-zinc-800 bg-zinc-900/50 text-white placeholder:text-zinc-600 focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium" id="password_confirmation" name="password_confirmation" placeholder="Confirm" type="password" required />
                    </div>
                </div>
                <button class="w-full h-14 bg-primary hover:bg-primary/90 text-white font-extrabold text-lg rounded-xl shadow-xl shadow-primary/20 transition-all active:scale-[0.98] mt-4" type="submit">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-4 flex items-center">
                <div class="grow border-t border-zinc-800/80"></div>
                <span class="mx-4 text-zinc-600 text-sm font-bold uppercase tracking-widest">or</span>
                <div class="grow border-t border-zinc-800/80"></div>
            </div>

            <!-- Social Login -->
            <button type="button" class="w-full h-12 border border-zinc-800 bg-zinc-900/40 rounded-xl flex items-center justify-center gap-3 hover:bg-zinc-800/60 transition-all mb-4 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" viewbox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"></path>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                </svg>
                <span class="text-zinc-200 font-bold">Sign up with Google</span>
            </button>

            <!-- Bottom Links -->
            <div class="flex flex-col items-center gap-4">
                <p class="text-zinc-500 text-sm font-medium">
                    Already have an account? <a class="text-primary font-bold hover:underline decoration-2 underline-offset-4 ml-1" href="{{ url('login-ui') }}">Login</a>
                </p>
                <!-- Social Media Buttons -->
                <div class="social-buttons">
                  <a href="#" class="social-button facebook" title="Facebook">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 310 310">
                      <g id="XMLID_834_">
                        <path id="XMLID_835_" d="M81.703,165.106h33.981V305c0,2.762,2.238,5,5,5h57.616c2.762,0,5-2.238,5-5V165.765h39.064 c2.54,0,4.677-1.906,4.967-4.429l5.933-51.502c0.163-1.417-0.286-2.836-1.234-3.899c-0.949-1.064-2.307-1.673-3.732-1.673h-44.996 V71.978c0-9.732,5.24-14.667,15.576-14.667c1.473,0,29.42,0,29.42,0c2.762,0,5-2.239,5-5V5.037c0-2.762-2.238-5-5-5h-40.545 C187.467,0.023,186.832,0,185.896,0c-7.035,0-31.488,1.381-50.804,19.151c-21.402,19.692-18.427,43.27-17.716,47.358v37.752H81.703 c-2.762,0-5,2.238-5,5v50.844C76.703,162.867,78.941,165.106,81.703,165.106z"></path>
                      </g>
                    </svg>
                  </a>
                  <a href="#" class="social-button instagram" title="Instagram">
                    <svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg">
                      <g id="Page-1" stroke="none" stroke-width="1">
                        <g id="Dribbble-Light-Preview" transform="translate(-340.000000, -7439.000000)">
                          <g id="icons" transform="translate(56.000000, 160.000000)">
                            <path d="M289.869652,7279.12273 C288.241769,7279.19618 286.830805,7279.5942 285.691486,7280.72871 C284.548187,7281.86918 284.155147,7283.28558 284.081514,7284.89653 C284.035742,7285.90201 283.768077,7293.49818 284.544207,7295.49028 C285.067597,7296.83422 286.098457,7297.86749 287.454694,7298.39256 C288.087538,7298.63872 288.809936,7298.80547 289.869652,7298.85411 C298.730467,7299.25511 302.015089,7299.03674 303.400182,7295.49028 C303.645956,7294.859 303.815113,7294.1374 303.86188,7293.08031 C304.26686,7284.19677 303.796207,7282.27117 302.251908,7280.72871 C301.027016,7279.50685 299.5862,7278.67508 289.869652,7279.12273 M289.951245,7297.06748 C288.981083,7297.0238 288.454707,7296.86201 288.103459,7296.72603 C287.219865,7296.3826 286.556174,7295.72155 286.214876,7294.84312 C285.623823,7293.32944 285.819846,7286.14023 285.872583,7284.97693 C285.924325,7283.83745 286.155174,7282.79624 286.959165,7281.99226 C287.954203,7280.99968 289.239792,7280.51332 297.993144,7280.90837 C299.135448,7280.95998 300.179243,7281.19026 300.985224,7281.99226 C301.980262,7282.98483 302.473801,7284.28014 302.071806,7292.99991 C302.028024,7293.96767 301.865833,7294.49274 301.729513,7294.84312 C300.829003,7297.15085 298.757333,7297.47145 289.951245,7297.06748 M298.089663,7283.68956 C298.089663,7284.34665 298.623998,7284.88065 299.283709,7284.88065 C299.943419,7284.88065 300.47875,7284.34665 300.47875,7283.68956 C300.47875,7283.03248 299.943419,7282.49847 299.283709,7282.49847 C298.623998,7282.49847 298.089663,7283.03248 298.089663,7283.68956 M288.862673,7288.98792 C288.862673,7291.80286 291.150266,7294.08479 293.972194,7294.08479 C296.794123,7294.08479 299.081716,7291.80286 299.081716,7288.98792 C299.081716,7286.17298 296.794123,7283.89205 293.972194,7283.89205 C291.150266,7283.89205 288.862673,7286.17298 288.862673,7288.98792 M290.655732,7288.98792 C290.655732,7287.16159 292.140329,7285.67967 293.972194,7285.67967 C295.80406,7285.67967 297.288657,7287.16159 297.288657,7288.98792 C297.288657,7290.81525 295.80406,7292.29716 293.972194,7292.29716 C292.140329,7292.29716 290.655732,7290.81525 290.655732,7288.98792" id="instagram-[#167]"></path>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </a>
                  <a href="#" class="social-button whatsapp" title="WhatsApp">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" fill="currentColor"/></svg>
                  </a>
                </div>
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
                        <img alt="Andrew.ui Profile" class="w-full h-full object-cover rounded-full" src="https://ui-avatars.com/api/?name=Pro+Gaming&background=e53734&color=fff&size=100"/>
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
