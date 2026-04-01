@extends('admin.layouts.app')

@section('title', 'Edit Input: ' . $operator->name)
@section('page_title', 'Konfigurasi Input Pemain')
@section('page_description', 'Sesuaikan label dan jumlah kolom input ID pemain untuk operator ' . $operator->name)

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-2 text-slate-400 hover:text-primary transition-colors text-sm font-bold">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali ke Daftar Operator
        </a>
    </div>

    <form action="{{ route('admin.packages.operator.update', $operator->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="glass-panel p-8 rounded-3xl border border-white/5 bg-linear-to-b from-white/[0.02] to-transparent">
            <div class="flex items-center gap-4 mb-8">
                <div class="size-16 rounded-2xl overflow-hidden border border-white/10">
                    <img src="{{ $operator->icon }}" alt="{{ $operator->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-100">{{ $operator->name }}</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">{{ $operator->type }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Main Input Config -->
                <div class="space-y-6">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">input</span>
                        Input Utama
                    </h4>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Label Input</label>
                        <input type="text" name="input_label" required value="{{ old('input_label', $operator->input_label ?? 'User ID') }}" placeholder="Contoh: User ID / ID Player"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-slate-200">
                        <p class="text-[9px] text-slate-500 ml-1">Nama kolom yang akan muncul di halaman top-up.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Placeholder</label>
                        <input type="text" name="input_placeholder" required value="{{ old('input_placeholder', $operator->input_placeholder ?? 'Masukkan ID Pemain') }}" placeholder="Contoh: Contoh: 12345678"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-slate-200">
                    </div>
                </div>

                <!-- Zone ID Config -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-secondary flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">grid_view</span>
                            Input Tambahan (Zone)
                        </h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="has_zone" value="0">
                            <input type="checkbox" name="has_zone" value="1" class="sr-only peer" {{ old('has_zone', $operator->has_zone) ? 'checked' : '' }} onchange="toggleZoneFields(this.checked)">
                            <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:inset-s-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
                        </label>
                    </div>

                    <div id="zone-fields" class="{{ old('has_zone', $operator->has_zone) ? '' : 'opacity-40 pointer-events-none' }} space-y-6 transition-all duration-300">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-slate-500 ml-1 text-secondary">Label Kolom Ke-2</label>
                            <input type="text" name="zone_label" id="zone_label" value="{{ old('zone_label', $operator->zone_label ?? 'Zone ID') }}" placeholder="Contoh: Zone ID / Server ID"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all text-slate-200">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-slate-500 ml-1 text-secondary">Placeholder Ke-2</label>
                            <input type="text" name="zone_placeholder" id="zone_placeholder" value="{{ old('zone_placeholder', $operator->zone_placeholder ?? 'Contoh: 1234') }}" placeholder="Contoh: Contoh: 1234"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all text-slate-200">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/5 flex items-center gap-4">
                <button type="submit" class="flex-1 bg-primary text-black font-black py-4 rounded-2xl shadow-xl shadow-primary/20 hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm uppercase tracking-tighter">
                    <span class="material-symbols-outlined font-bold">save</span>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.packages.index') }}" class="px-8 py-4 bg-white/5 border border-white/10 text-slate-400 font-bold rounded-2xl hover:bg-white/10 transition-all text-sm uppercase tracking-tighter">Batal</a>
            </div>
        </div>
    </form>
</div>

<script>
function toggleZoneFields(checked) {
    const container = document.getElementById('zone-fields');
    const inputs = container.querySelectorAll('input');

    if (checked) {
        container.classList.remove('opacity-40', 'pointer-events-none');
    } else {
        container.classList.add('opacity-40', 'pointer-events-none');
    }
}
</script>
@endsection
