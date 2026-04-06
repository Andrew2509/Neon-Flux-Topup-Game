@extends('desktop.layouts.neonflux')

@section('title', 'FAQ - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="glass-panel rounded-3xl p-10 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-round text-primary text-4xl">quiz</span>
                </div>
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                        FAQ
                    </h1>
                    <p class="text-slate-500 dark:text-gray-400 mt-2">
                        Pertanyaan yang sering diajukan seputar top-up dan pembayaran.
                    </p>
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-10 space-y-10">
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Bagaimana cara memesan top-up?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Pilih game di catalog, lengkapi ID dan nominal, lalu bayar sesuai instruksi. Panduan langkah demi langkah: <a href="{{ route('cara-order') }}" class="text-primary hover:underline font-medium">Cara Order</a>.
                </p>
            </section>
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Metode pembayaran apa saja yang tersedia?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Bergantung pengaturan toko; biasanya bank, e-wallet, dan mitra pembayaran. Opsi lengkap tampil di halaman checkout.
                </p>
            </section>
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Berapa lama pesanan diproses?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Setelah pembayaran terverifikasi, proses otomatis umumnya selesai dalam hitungan menit. Gangguan dari pihak game atau sistem dapat menunda proses; tim kami akan membantu melalui channel support.
                </p>
            </section>
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Bagaimana cek status transaksi?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Gunakan <a href="{{ route('track.order') }}" class="text-primary hover:underline font-medium">Cek Transaksi</a> dengan kode atau informasi yang Anda dapat saat order.
                </p>
            </section>
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Apakah bisa refund jika salah ketik ID?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Biasanya tidak jika item sudah terkirim ke ID yang Anda input. Rincian di <a href="{{ route('kebijakan-refund') }}" class="text-primary hover:underline font-medium">Kebijakan Refund</a>.
                </p>
            </section>
            <section>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Bagaimana data saya digunakan?</h2>
                <p class="text-slate-600 dark:text-gray-400 leading-relaxed">
                    Sesuai <a href="{{ route('kebijakan-privasi') }}" class="text-primary hover:underline font-medium">Kebijakan Privasi</a> untuk transaksi, keamanan, dan dukungan pelanggan.
                </p>
            </section>
        </div>
    </div>
</main>
@endsection
