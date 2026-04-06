@extends('desktop.layouts.neonflux')

@section('title', 'Kebijakan Refund - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<main class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="glass-panel rounded-3xl p-10 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-round text-primary text-4xl">currency_exchange</span>
                </div>
                <div>
                    <h1 class="text-3xl font-display font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                        Kebijakan <span class="text-primary">Refund</span>
                    </h1>
                    <p class="text-slate-500 dark:text-gray-400 mt-2">
                        Ketentuan pengembalian dana untuk layanan top-up dan produk digital.
                    </p>
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-10 space-y-12">
            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">1. Produk digital</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Layanan berupa produk digital. Setelah item berhasil dikirim ke ID atau akun yang Anda berikan, transaksi dianggap selesai dan umumnya tidak dapat dibatalkan.
                    </p>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">2. Kondisi refund</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>Pengembalian dana dipertimbangkan jika:</p>
                    <ul class="list-disc ml-5 space-y-2">
                        <li>Terjadi kesalahan sistem di pihak {{ get_setting('site_name', 'Prince Pay') }} sehingga pesanan tidak dapat diproses setelah pembayaran terverifikasi.</li>
                        <li>Produk tidak dapat dikirim sama sekali karena kegagalan teknis permanen dari sistem kami.</li>
                        <li>Stok habis setelah pembayaran terverifikasi dan tidak dapat dipenuhi dalam waktu wajar (misalnya 24 jam), setelah kami konfirmasi ke Anda.</li>
                    </ul>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">3. Di luar cakupan refund</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <ul class="list-disc ml-5 space-y-2">
                        <li>Kesalahan data ID game, server, atau informasi lain dari pihak pembeli.</li>
                        <li>Perubahan kebijakan publisher setelah transaksi sukses.</li>
                        <li>Penyesalan pembelian setelah produk digital telah terkirim.</li>
                    </ul>
                </div>
            </section>

            <section class="relative">
                <div class="absolute -left-10 top-1 w-1 h-8 bg-primary rounded-full hidden md:block"></div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">4. Pengajuan</h2>
                <div class="text-slate-600 dark:text-gray-400 leading-relaxed space-y-4">
                    <p>
                        Hubungi customer support (WhatsApp / email di footer situs) dengan nomor invoice atau order dan bukti pembayaran. Kami akan meninjau pada hari kerja.
                    </p>
                    <p class="text-sm italic text-slate-400">
                        Lihat juga <a href="{{ route('syarat-ketentuan') }}" class="text-primary hover:underline">Syarat & Ketentuan</a>.
                    </p>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection
