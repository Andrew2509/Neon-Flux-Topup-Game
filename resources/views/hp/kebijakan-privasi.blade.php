@extends('hp.layouts.neonflux')

@section('title', 'Kebijakan Privasi - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    {{-- Header Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-round text-primary text-2xl">security</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Kebijakan Privasi</h1>
            <p class="text-[11px] text-slate-500">Terakhir diperbarui: {{ date('d M Y') }}</p>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-6">
        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Informasi yang Kami Kumpulkan
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami mengumpulkan informasi yang Anda berikan langsung kepada kami, seperti saat Anda membuat akun, melakukan transaksi, atau menghubungi layanan pelanggan kami. Informasi ini meliputi nama, alamat email, nomor telepon, dan data ID Game.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Penggunaan Informasi
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami menggunakan informasi yang dikumpulkan untuk:
            </p>
            <ul class="space-y-2">
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Memproses transaksi dan mengirimkan notifikasi terkait pesanan Anda.</span>
                </li>
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Meningkatkan layanan dan pengalaman pengguna di platform kami.</span>
                </li>
                <li class="flex gap-2 text-xs text-slate-600 leading-relaxed">
                    <span class="text-primary font-bold">•</span>
                    <span>Mengirimkan informasi promosi dan pembaruan layanan (Anda dapat memilih untuk berhenti berlangganan).</span>
                </li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Keamanan Data
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Keamanan data Anda adalah prioritas kami. Kami menggunakan langkah-langkah teknis dan organisasi yang sesuai untuk melindungi data pribadi Anda dari akses yang tidak sah atau kebocoran.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Berbagi Informasi
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga. Kami hanya berbagi informasi dengan penyedia layanan pihak ketiga (seperti payment gateway dan provider game) sejauh yang diperlukan untuk memproses transaksi Anda.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Perubahan Kebijakan
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan diinformasikan melalui halaman ini dengan memperbarui tanggal "Terakhir diperbarui".
            </p>
        </section>

        <div class="pt-4 border-t border-slate-100 mt-6">
            <p class="text-[10px] text-slate-400 text-center italic">
                Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini.
            </p>
        </div>
    </div>
</div>
@endsection
