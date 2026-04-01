@extends('desktop.layouts.neonflux')

@section('title', 'Kebijakan Privasi - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="glass-panel rounded-3xl p-10 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-round text-primary text-4xl">security</span>
                </div>
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                        Kebijakan <span class="text-primary">Privasi</span>
                    </h1>
                    <p class="text-slate-500 dark:text-gray-400 mt-2">
                        Komitmen kami dalam melindungi data pribadi dan privasi Anda.
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
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">1. Informasi yang Kami Kumpulkan</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        {{ get_setting('site_name', 'Prince Pay') }} mengumpulkan informasi untuk memberikan layanan yang lebih baik kepada semua pengguna kami. Informasi yang kami kumpulkan meliputi:
                    </p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li><strong>Informasi Akun:</strong> Nama, alamat email, nomor telepon, dan kata sandi saat Anda mendaftar.</li>
                        <li><strong>Informasi Transaksi:</strong> Detail mengenai produk yang Anda beli, metode pembayaran, dan waktu transaksi.</li>
                        <li><strong>Data ID Game:</strong> Informasi identitas game yang diperlukan untuk memproses top-up.</li>
                    </ul>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">2. Cara Kami Menggunakan Informasi</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Kami menggunakan informasi yang kami kumpulkan untuk tujuan berikut:
                    </p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li>Menyediakan, memelihara, dan meningkatkan kualitas layanan kami.</li>
                        <li>Memproses transaksi Anda dan mengirimkan bukti pembayaran atau notifikasi status pesanan.</li>
                        <li>Melindungi keamanan pengguna kami dan mencegah penipuan atau penyalahgunaan layanan.</li>
                        <li>Berkomunikasi dengan Anda mengenai pembaruan layanan atau penawaran promosi pilihan.</li>
                    </ul>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">3. Keamanan Informasi</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Kami bekerja keras untuk melindungi data Anda dari akses, perubahan, pengungkapan, atau penghancuran yang tidak sah. Kami menerapkan enkripsi data (SSL) pada setiap transmisi informasi sensitif di platform kami.
                    </p>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">4. Berbagi Informasi dengan Pihak Ketiga</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Kami hanya akan berbagi informasi pribadi Anda dengan perusahaan, organisasi, atau individu di luar {{ get_setting('site_name', 'Prince Pay') }} jika terjadi salah satu keadaan berikut:
                    </p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li><strong>Dengan Persetujuan Anda:</strong> Kami akan meminta persetujuan eksplisit Anda sebelum berbagi informasi pribadi apa pun.</li>
                        <li><strong>Untuk Pemrosesan Eksternal:</strong> Kami memberikan informasi kepada mitra terpercaya kami (seperti Payment Gateway) berdasarkan instruksi kami dan sesuai dengan Kebijakan Privasi ini.</li>
                        <li><strong>Untuk Keperluan Hukum:</strong> Jika kami memiliki keyakinan dengan niat baik bahwa akses, penggunaan, penyimpanan, atau pengungkapan tersebut diperlukan untuk memenuhi hukum yang berlaku.</li>
                    </ul>
                </div>
            </section>

            <div class="pt-8 border-t border-black/5 dark:border-white/5 flex flex-col items-center gap-4 text-center">
                <p class="text-sm text-slate-400 italic">
                    Kebijakan Privasi ini dapat berubah sewaktu-waktu sesuai dengan perkembangan layanan kami.
                </p>
                <div class="flex gap-4">
                    <a href="mailto:{{ get_setting('email_support', 'support@princepay.id') }}" class="px-6 py-2 bg-primary/10 text-primary rounded-full text-xs font-bold hover:bg-primary/20 transition-all flex items-center gap-2">
                        <span class="material-icons-round text-sm">email</span> Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
