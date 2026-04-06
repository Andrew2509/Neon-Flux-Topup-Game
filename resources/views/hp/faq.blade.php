@extends('hp.layouts.neonflux')

@section('title', 'FAQ - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-round text-primary text-2xl">quiz</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Pertanyaan Umum (FAQ)</h1>
            <p class="text-[11px] text-slate-500">Jawaban singkat seputar order & pembayaran</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Bagaimana cara memesan top-up?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Pilih game di catalog, isi ID dan nominal, lalu lanjut ke pembayaran. Panduan lengkap ada di halaman <a href="{{ route('cara-order') }}" class="text-primary font-semibold">Cara Order</a>.
            </p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Metode pembayaran apa saja yang tersedia?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Tergantung konfigurasi situs; umumnya tersedia transfer bank, e-wallet, dan kanal pembayaran mitra. Daftar metode akan muncul saat checkout.
            </p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Berapa lama pesanan diproses?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Untuk pembayaran yang sudah diverifikasi, proses otomatis biasanya berjalan dalam hitungan menit. Jika ada gangguan dari publisher game atau sistem, status akan diperbarui atau tim support akan menghubungi Anda.
            </p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Bagaimana cara cek status transaksi?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Gunakan halaman <a href="{{ route('track.order') }}" class="text-primary font-semibold">Cek Transaksi</a> dengan kode atau data yang Anda terima saat order.
            </p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Apakah bisa refund jika salah ketik ID?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Umumnya tidak, karena produk digital yang sudah terkirim ke ID yang Anda masukkan. Detail ada di <a href="{{ route('kebijakan-refund') }}" class="text-primary font-semibold">Kebijakan Refund</a>.
            </p>
        </div>
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-2">
            <h2 class="text-sm font-bold text-slate-900">Bagaimana data saya digunakan?</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami menggunakan data sesuai <a href="{{ route('kebijakan-privasi') }}" class="text-primary font-semibold">Kebijakan Privasi</a> untuk memproses transaksi dan layanan pelanggan.
            </p>
        </div>
    </div>
</div>
@endsection
