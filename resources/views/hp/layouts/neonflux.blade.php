<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Neon Flux Mobile')</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            // Force Light Mode for Mobile
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        })();
    </script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
        }


        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }


        .glass-panel-mobile {
            background: var(--surface-color);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
        }


        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Safe Area Padding for modern phones */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>

    @stack('styles')
</head>
<body class="font-body min-h-screen pb-24 bg-slate-50 text-slate-900 transition-colors duration-300 antialiased"> {{-- pb-24 for bottom nav --}}

    {{-- Top Navbar Mobile --}}
    @include('hp.partials.navbar')

    {{-- Content --}}
    <div class="max-w-5xl mx-auto px-4 md:px-8 pt-20 relative z-0">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('hp.partials.footer')

    {{-- Bottom Navigation --}}
    @include('hp.partials.bottom-nav')

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Session Alerts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                });
            @endif
        });
    </script>

    {{-- Real-time Transaction Notifications --}}
    <div id="transaction-toast-container" class="fixed bottom-24 left-4 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <style>
        .transaction-toast {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            padding: 12px 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 240px;
            max-width: 320px;
            transform: translateX(-120%);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            pointer-events: auto;
        }

        .transaction-toast.show {
            transform: translateX(0);
        }

        .transaction-toast .icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .transaction-toast .content {
            flex-grow: 1;
            display: flex;
            flex-col;
            gap: 1.5px;
        }

        .transaction-toast .user-name {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .transaction-toast .product-name {
            font-size: 10px;
            color: #475569;
            font-weight: 500;
            line-height: 1.2;
        }

        .transaction-toast .status-msg {
            font-size: 9px;
            font-weight: 700;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('transaction-toast-container');
            let seenIds = new Set(JSON.parse(sessionStorage.getItem('seen_notifications') || '[]'));
            
            // Limit seenIds to last 50 to prevent bloated storage
            if (seenIds.size > 50) {
                const idsArray = Array.from(seenIds);
                seenIds = new Set(idsArray.slice(-50));
            }

            function createToast(data) {
                const toast = document.createElement('div');
                toast.className = 'transaction-toast';
                toast.innerHTML = `
                    <div class="icon-box">
                        <span class="material-icons-round" style="font-size: 20px;">check_circle</span>
                    </div>
                    <div class="content">
                        <div class="user-name">${data.user}</div>
                        <div class="product-name">${data.product}</div>
                        <div class="status-msg">${data.message}</div>
                    </div>
                `;
                
                container.appendChild(toast);
                
                // Trigger animation
                setTimeout(() => toast.classList.add('show'), 100);
                
                // Remove after 5 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            }

            async function fetchNotifications() {
                try {
                    const response = await fetch('{{ route('api.notifications.recent') }}');
                    if (!response.ok) return;
                    
                    const data = await response.json();
                    let delay = 0;
                    
                    // Filter out already seen
                    const newItems = data.filter(item => !seenIds.has(item.id));
                    
                    newItems.forEach(item => {
                        seenIds.add(item.id);
                        // Stagger multiple notifications
                        setTimeout(() => createToast(item), delay);
                        delay += 3000;
                    });
                    
                    if (newItems.length > 0) {
                        sessionStorage.setItem('seen_notifications', JSON.stringify(Array.from(seenIds)));
                    }
                } catch (error) {
                    console.error('Failed to fetch notifications:', error);
                }
            }

            // Initial fetch after 3 seconds
            setTimeout(fetchNotifications, 3000);
            
            // Poll every 15 seconds
            setInterval(fetchNotifications, 15000);
        });
    </script>
</body>
</html>
