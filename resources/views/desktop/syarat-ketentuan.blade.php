@extends('desktop.layouts.neonflux')

@section('title', 'Syarat & Ketentuan - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="glass-panel rounded-3xl p-10 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-round text-primary text-4xl">gavel</span>
                </div>
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                        Syarat <span class="text-primary">&</span> Ketentuan
                    </h1>
                    <p class="text-slate-500 dark:text-gray-400 mt-2">
                        Silakan baca dengan seksama aturan penggunaan layanan kami.
                    </p>
                    <div class="flex items-center gap-2 mt-4 text-xs font-medium text-slate-400">
                        <span class="material-icons-round text-sm">update</span>
                        Terakhir diperbarui: {{ date('d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="glass-panel rounded-3xl p-10 space-y-12">
            {{-- Section 1 --}}
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">1. Pendahuluan</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Ketentuan Layanan ini mengatur penggunaan Anda terhadap situs web {{ get_setting('site_name', 'Prince Pay') }} dan layanan terkait lainnya yang kami sediakan.
                    </p>
                    <p>
                        Dengan mengakses {{ get_setting('site_name', 'Prince Pay') }}, Anda setuju untuk mematuhi Ketentuan Layanan ini dan mematuhi semua hukum serta peraturan yang berlaku. Jika Anda tidak setuju dengan Ketentuan Layanan ini, Anda dilarang menggunakan atau mengakses situs web ini atau menggunakan layanan lainnya yang kami sediakan.
                    </p>
                </div>
            </section>

            {{-- Section 2 --}}
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">2. Layanan Top-Up & Voucher</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        {{ get_setting('site_name', 'Prince Pay') }} menyediakan layanan pembelian kredit game (top-up) dan voucher digital secara otomatis.
                    </p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li>Semua harga sudah termasuk pajak kecuali dinyatakan lain.</li>
                        <li>Waktu pengiriman produk bervariasi tergantung pada jenis produk dan metode pembayaran, namun biasanya diproses dalam hitungan detik.</li>
                        <li>Anda bertanggung jawab penuh untuk memastikan ID Game atau informasi akun yang Anda berikan adalah benar.</li>
                    </ul>
                </div>
            </section>

            {{-- Section 3 --}}
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">3. Pembayaran</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Kami menerima berbagai metode pembayaran melalui penyedia pihak ketiga yang sah. Kami tidak menyimpan informasi kartu kredit atau detail pembayaran sensitif lainnya di server kami.
                    </p>
                    <p>
                        Pembayaran yang dilakukan tidak dapat dibatalkan setelah sistem kami memproses pesanan tersebut ke penyedia produk (game/publisher).
                    </p>
                </div>
            </section>

            {{-- Section 4 --}}
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">4. Pengembalian Dana (Refund)</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Karena sifat produk digital, semua penjualan adalah final. Pengembalian dana hanya akan dipertimbangkan dalam kondisi berikut:
                    </p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li>Kesalahan sistem permanen yang menyebabkan produk tidak dapat dikirimkan sama sekali.</li>
                        <li>Stok produk habis setelah pembayaran Anda terverifikasi dan tidak dapat dipasok kembali dalam waktu 24 jam.</li>
                    </ul>
                </div>
            </section>

            {{-- Footer Note --}}
            <div class="pt-8 border-t border-black/5 dark:border-white/5 flex flex-col items-center gap-4 text-center">
                <p class="text-sm text-slate-400 italic">
                    Punya pertanyaan lebih lanjut? Hubungi dukungan pelanggan kami melalui WhatsApp atau Email.
                </p>
                <div class="flex gap-4">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('site_phone', '')) }}" class="px-6 py-2 bg-[#25D366] text-white rounded-full text-xs font-bold hover:shadow-lg transition-all flex items-center gap-2">
                        <span class="material-icons-round text-sm">chat</span> WhatsApp Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
