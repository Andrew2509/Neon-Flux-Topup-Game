@extends('hp.layouts.neonflux')

@section('title', 'Syarat & Ketentuan - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-round text-primary text-2xl">gavel</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Syarat & Ketentuan</h1>
            <p class="text-[11px] text-slate-500">Terakhir diperbarui: {{ date('d M Y') }}</p>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-6">
        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Pendahuluan
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Selamat datang di {{ get_setting('site_name', 'Prince Pay') }}. Dengan mengakses dan menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui semua syarat dan ketentuan yang berlaku di bawah ini.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Layanan Top Up
            </h2>
            <ul class="space-y-2">
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Proses top up akan dilakukan segera setelah pembayaran terkonfirmasi oleh sistem kami.</span>
                </li>
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Pengguna bertanggung jawab penuh atas kebenaran data ID Game yang dimasukkan. Kesalahan input data bukan tanggung jawab kami.</span>
                </li>
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Harga layanan dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</span>
                </li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Pembayaran
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami menyediakan berbagai metode pembayaran yang aman. Kami tidak bertanggung jawab atas kegagalan transaksi yang disebabkan oleh gangguan pada pihak penyedia layanan pembayaran atau kesalahan pengguna.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Kebijakan Pengembalian (Refund)
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Transaksi yang sudah berhasil dan masuk ke ID Game target tidak dapat dibatalkan atau dikembalikan. Refund hanya dapat dilakukan jika terjadi kesalahan sistem di pihak {{ get_setting('site_name', 'Prince Pay') }} yang mengakibatkan pesanan tidak dapat diproses.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Keamanan & Privasi
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami berkomitmen menjaga kerahasiaan data pribadi Anda. Data yang dikumpulkan hanya digunakan untuk kepentingan proses transaksi dan peningkatan layanan.
            </p>
        </section>

        <div class="pt-4 border-t border-slate-100 mt-6">
            <p class="text-[10px] text-slate-400 text-center italic">
                Hubungi Customer Support kami jika Anda memiliki pertanyaan mengenai Syarat & Ketentuan ini.
            </p>
        </div>
    </div>
</div>
@endsection
