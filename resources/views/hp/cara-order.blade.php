@extends('hp.layouts.neonflux')

@section('title', 'Cara Order - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-round text-primary text-2xl">help_outline</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Cara Order</h1>
            <p class="text-[11px] text-slate-500">Ikuti langkah mudah berikut ini</p>
        </div>
    </div>

    {{-- Steps Section --}}
    <div class="space-y-4">
        {{-- Step 1 --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex gap-4">
            <div class="shrink-0 h-10 w-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-primary font-bold text-sm">
                1
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">Pilih Game / Layanan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Cari dan pilih game atau layanan yang ingin Anda top-up dari daftar kategori yang tersedia di halaman utama atau catalog.
                </p>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex gap-4">
            <div class="shrink-0 h-10 w-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-primary font-bold text-sm">
                2
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">Masukkan Data Akun</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Masukkan User ID, Zone ID, atau data akun lainnya yang diperlukan dengan benar. Pastikan tidak ada kesalahan ketik.
                </p>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex gap-4">
            <div class="shrink-0 h-10 w-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-primary font-bold text-sm">
                3
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">Pilih Nominal</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pilih jumlah diamond, gold, atau nominal layanan yang Anda inginkan. Harga akan muncul otomatis sesuai pilihan Anda.
                </p>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex gap-4">
            <div class="shrink-0 h-10 w-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-primary font-bold text-sm">
                4
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">Pilih Metode Pembayaran</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pilih metode pembayaran yang paling memudahkan Anda (E-Wallet, Transfer Bank, atau lainnya) dan masukkan kode promo jika ada.
                </p>
            </div>
        </div>

        {{-- Step 5 --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex gap-4">
            <div class="shrink-0 h-10 w-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-primary font-bold text-sm">
                5
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">Konfirmasi & Bayar</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Masukkan nomor WhatsApp Anda untuk notifikasi, klik 'Beli Sekarang', dan selesaikan pembayaran sesuai instruksi yang diberikan.
                </p>
            </div>
        </div>
    </div>

    {{-- Contact Section --}}
    <div class="bg-primary/5 rounded-3xl p-6 border border-primary/10 text-center space-y-3">
        <h4 class="text-sm font-bold text-slate-900">Butuh Bantuan?</h4>
        <p class="text-xs text-slate-500">Jika mengalami kendala saat melakukan pemesanan, silakan hubungi tim dukungan kami.</p>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('site_phone', '')) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl text-xs font-bold shadow-lg shadow-primary/20 transition-all active:scale-95">
            <span class="material-icons-round text-sm">chat</span> WhatsApp Support
        </a>
    </div>
</div>
@endsection
