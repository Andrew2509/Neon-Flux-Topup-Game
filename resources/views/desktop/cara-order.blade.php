@extends('desktop.layouts.neonflux')

@section('title', 'Cara Order - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="glass-panel rounded-3xl p-12 mb-10 relative overflow-hidden text-center">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-6">
                    <span class="material-icons-round text-primary text-4xl">shopping_cart</span>
                </div>
                <h1 class="text-4xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                    Panduan <span class="text-primary">Cara Order</span>
                </h1>
                <p class="text-slate-500 dark:text-gray-400 max-w-2xl mx-auto">
                    Nikmati kemudahan top-up game favorit Anda dalam hitungan detik. Ikuti langkah-langkah sederhana berikut untuk memulai transaksi.
                </p>
            </div>
        </div>

        {{-- Steps Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Step 1 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-primary font-bold text-xl border border-slate-100 dark:border-white/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-500">
                    1
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pilih Produk</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Pilih kategori game atau layanan digital yang Anda inginkan dari menu utama atau gunakan fitur pencarian untuk menemukan layanan spesifik.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-primary font-bold text-xl border border-slate-100 dark:border-white/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-500">
                    2
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Input Data Akun</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Masukkan data akun Anda dengan benar (misal: User ID, Zone ID, atau nomor server). Kami menyediakan panduan gambar di setiap halaman produk.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-primary font-bold text-xl border border-slate-100 dark:border-white/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-500">
                    3
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pilih Nominal</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Pilih jumlah saldo atau item game yang ingin dibeli. Pastikan saldo dompet atau budget Anda mencukupi untuk nominal yang dipilih.
                </p>
            </div>

            {{-- Step 4 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-primary font-bold text-xl border border-slate-100 dark:border-white/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-500">
                    4
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Metode Pembayaran</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Pilih metode pembayaran (E-Wallet, VA, atau Gerai Retail). Jika Anda member, Anda bisa menggunakan Saldo Akun untuk harga lebih murah.
                </p>
            </div>

            {{-- Step 5 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-primary font-bold text-xl border border-slate-100 dark:border-white/10 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all duration-500">
                    5
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Verifikasi Nomor WA</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Masukkan nomor WhatsApp aktif Anda. Sistem kami akan mengirimkan invoice dan update status pesanan langsung ke WhatsApp Anda.
                </p>
            </div>

            {{-- Step 6 --}}
            <div class="glass-panel rounded-3xl p-8 space-y-4 relative group hover:border-primary/30 transition-all duration-500">
                <div class="h-12 w-12 rounded-2xl bg-primary flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary/20">
                    <span class="material-icons-round">done_all</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Selesai</h3>
                <p class="text-sm text-slate-500 dark:text-gray-400 leading-relaxed">
                    Klik 'Beli Sekarang' dan selesaikan pembayaran. Sistem otomatis kami akan memproses pesanan Anda dalam 1-5 menit saja!
                </p>
            </div>
        </div>

        {{-- Support Banner --}}
        <div class="mt-12 glass-panel rounded-3xl p-10 flex flex-col md:flex-row items-center justify-between gap-8 border-primary/20 bg-primary/5">
            <div class="space-y-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Masih Bingung atau Butuh Bantuan?</h3>
                <p class="text-slate-500 dark:text-gray-400 text-sm">Tim Customer Support kami siap membantu Anda 24/7 jika mengalami kendala.</p>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('site_phone', '')) }}" class="px-8 py-4 bg-primary text-white rounded-2xl text-sm font-bold shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                Hubungi Kami Sekarang
            </a>
        </div>
    </div>
</main>
@endsection
