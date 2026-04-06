@extends('admin.layouts.app')

@section('title', 'Rating Customer')
@section('page_title', 'Rating & Ulasan')
@section('page_description', 'Lihat apa yang pelanggan katakan tentang layanan Anda.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" placeholder="Cari Pelanggan atau Produk..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <select class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none appearance-none text-slate-300">
                <option value="">Semua Bintang</option>
                <option value="5">5 Bintang</option>
                <option value="4">4 Bintang</option>
                <option value="3">3 Bintang</option>
                <option value="2">2 Bintang</option>
                <option value="1">1 Bintang</option>
            </select>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
             <button class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Report
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-panel p-6 rounded-2xl border border-white/5 flex items-center gap-4 bg-slate-900/40">
            <div class="size-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/20">
                <span class="material-symbols-outlined text-2xl">star_rate</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Average Rating</p>
                <h3 class="text-2xl font-bold text-slate-100">4.8 / 5.0</h3>
            </div>
        </div>
        <div class="glass-panel p-6 rounded-2xl border border-white/5 flex items-center gap-4 bg-slate-900/40">
            <div class="size-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center border border-blue-500/20">
                <span class="material-symbols-outlined text-2xl">reviews</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Total Reviews</p>
                <h3 class="text-2xl font-bold text-slate-100">1,248</h3>
            </div>
        </div>
        <div class="glass-panel p-6 rounded-2xl border border-white/5 flex items-center gap-4 bg-slate-900/40">
            <div class="size-12 rounded-xl bg-green-500/10 text-green-500 flex items-center justify-center border border-green-500/20">
                <span class="material-symbols-outlined text-2xl">recommend</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Satisfaction Rate</p>
                <h3 class="text-2xl font-bold text-slate-100">96.4%</h3>
            </div>
        </div>
    </div>

    <!-- Ratings Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Rating</th>
                        <th class="px-6 py-4">Komentar</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($ratings as $rating)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-xs uppercase">
                                    {{ mb_substr($rating->displayName(), 0, 1) }}
                                </div>
                                <div class="text-sm font-bold text-slate-100">{{ $rating->displayName() }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400 font-medium">{{ $rating->product_name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-0.5 text-amber-500">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-sm {{ $i <= $rating->stars ? 'fill-1' : '' }}">star</span>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-300 max-w-[300px] italic">"{{ $rating->comment }}"</div>
                        </td>
                        <td class="px-6 py-4 text-[10px] text-slate-500 font-bold uppercase">{{ $rating->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Lihat Detail">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </button>
                                <button class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all" title="Hapus Ulasan">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                             <span class="material-symbols-outlined text-4xl mb-2 opacity-20">reviews</span>
                             <p class="text-xs">Belum ada ulasan dari pelanggan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t border-white/5 flex items-center justify-between">
            <p class="text-[10px] text-slate-500 uppercase font-bold">Menampilkan 1-5 dari 1,248 Ulasan</p>
            <div class="flex items-center gap-2">
                <button class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-white/10">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </button>
                <div class="size-8 rounded-lg bg-primary text-black font-bold text-xs flex items-center justify-center shadow-lg shadow-primary/20">1</div>
                <div class="size-8 rounded-lg bg-white/5 border border-white/10 text-slate-400 font-bold text-xs flex items-center justify-center hover:bg-white/10 cursor-pointer">2</div>
                <button class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-white/10">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
