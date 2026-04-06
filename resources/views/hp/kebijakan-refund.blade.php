@extends('hp.layouts.neonflux')

@section('title', 'Kebijakan Refund - ' . get_setting('site_name', 'Prince Pay'))

@section('content')
<div class="space-y-6 mb-10">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-icons-round text-primary text-2xl">currency_exchange</span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Kebijakan Refund</h1>
            <p class="text-[11px] text-slate-500">Kapan pengembalian dana dapat diajukan</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-5">
        <section class="space-y-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Sifat produk digital
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Layanan kami berupa produk digital (top-up game / voucher). Setelah item berhasil dikirim ke ID atau akun yang Anda cantumkan, transaksi dianggap selesai dan umumnya tidak dapat dibatalkan.
            </p>
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Kapan refund dipertimbangkan
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                Pengembalian dana hanya dipertimbangkan jika memenuhi salah satu kondisi berikut:
            </p>
            <ul class="text-xs text-slate-600 leading-relaxed list-disc ml-4 space-y-1.5">
                <li>Terjadi kesalahan sistem di pihak {{ get_setting('site_name', 'Prince Pay') }} sehingga pesanan tidak dapat diproses setelah pembayaran terverifikasi.</li>
                <li>Produk tidak dapat dikirim sama sekali karena kegagalan teknis yang bersifat permanen dari sistem kami.</li>
                <li>Stok habis setelah pembayaran terverifikasi dan tidak dapat dipenuhi dalam waktu yang wajar (misalnya 24 jam), setelah konfirmasi ke Anda.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Yang tidak termasuk refund
            </h2>
            <ul class="text-xs text-slate-600 leading-relaxed list-disc ml-4 space-y-1.5">
                <li>Kesalahan penulisan ID game, server, atau data lain yang menjadi tanggung jawab pembeli.</li>
                <li>Perubahan kebijakan pihak publisher game setelah transaksi berhasil.</li>
                <li>Penyesalan pembelian setelah produk digital telah terkirim.</li>
            </ul>
        </section>

        <section class="space-y-2">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Cara mengajukan
            </h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Hubungi customer support melalui WhatsApp atau email yang tertera di situs, sertakan nomor invoice / order dan bukti pembayaran. Kami akan meninjau dalam waktu kerja dan memberi konfirmasi.
            </p>
        </section>

        <div class="pt-3 border-t border-slate-100">
            <p class="text-[10px] text-slate-400 text-center italic">
                Kebijakan ini melengkapi <a href="{{ route('syarat-ketentuan') }}" class="text-primary font-semibold">Syarat & Ketentuan</a> kami.
            </p>
        </div>
    </div>
</div>
@endsection
