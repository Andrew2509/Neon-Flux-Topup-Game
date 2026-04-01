@extends('admin.layouts.app')

@section('title', 'Edit Paket: ' . $package->name)
@section('page_title', 'Edit Paket Layanan')
@section('page_description', 'Perbarui detail paket bundle atau ubah layanan di dalamnya.')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" class="space-y-6" id="packageForm">
        @csrf
        @method('PUT')
        <div class="glass-panel p-6 rounded-2xl border border-white/5 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-primary">Informasi Dasar</h4>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Nama Paket</label>
                        <input type="text" name="name" required value="{{ old('name', $package->name) }}" placeholder="Contoh: Paket Sultan MLBB"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none transition-all">
                        @error('name') <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan isi paket ini..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none transition-all">{{ old('description', $package->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Harga Jual</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">Rp</span>
                                <input type="number" name="price" required value="{{ old('price', (int)$package->price) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none transition-all">
                            </div>
                            @error('price') <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Diskon (Opsional)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">Rp</span>
                                <input type="number" name="discount" value="{{ old('discount', (int)$package->discount) }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Status</label>
                        <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none transition-all appearance-none cursor-pointer">
                            <option value="Aktif" class="bg-slate-900" {{ old('status', $package->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" class="bg-slate-900" {{ old('status', $package->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- Service Selection -->
                <div class="space-y-6">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-secondary">Layanan Terpilih</h4>
                        <div id="selectedServices" class="space-y-2 min-h-[50px] p-4 bg-white/5 rounded-2xl border border-white/10 flex flex-col space-y-2">
                             @forelse($selectedServices as $service)
                             <div id="selected-service-{{ $service->id }}" class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 group animate-in zoom-in-95 duration-200">
                                <div class="flex-1 min-w-0">
                                    <input type="hidden" name="services[]" value="{{ $service->id }}">
                                    <div class="text-xs font-bold text-slate-200 truncate">{{ $service->name }}</div>
                                    <div class="text-[9px] text-slate-500 font-bold">Harga: Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                </div>
                                <button type="button" class="size-7 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all ml-3" onclick="removeService({{ $service->id }})">
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                             </div>
                             @empty
                             <div id="selectedServicesPlaceholder" class="flex flex-col items-center justify-center text-slate-500 text-[10px] border-dashed border border-white/10 py-4 w-full">
                                <span class="material-symbols-outlined text-lg mb-1 opacity-20">check_box_outline_blank</span>
                                Belum ada layanan yang dipilih
                             </div>
                             @endforelse
                        </div>
                        @error('services') <p class="text-[10px] text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-accent-blue">Cari & Tambah Layanan</h4>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-accent-blue transition-colors">search</span>
                            <input type="text" id="serviceSearch" placeholder="Ketik nama layanan atau kode untuk mencari..."
                                class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-accent-blue outline-none transition-all">
                        </div>

                        <div id="searchResult" class="bg-black/40 rounded-2xl border border-white/5 max-h-[250px] overflow-y-auto scrollbar-thin p-2 space-y-2 hidden">
                            <!-- Search results will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="flex-1 bg-primary text-black font-bold py-3 rounded-2xl shadow-lg shadow-primary/20 hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined font-bold">save</span>
                Perbarui Paket
            </button>
            <a href="{{ route('admin.packages.index') }}" class="px-8 py-3 bg-white/5 border border-white/10 text-slate-400 font-bold rounded-2xl hover:bg-white/10 transition-all text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('serviceSearch');
    const searchResult = document.getElementById('searchResult');
    const selectedContainer = document.getElementById('selectedServices');
    const selectedIds = new Set([@foreach($selectedServices as $s){{ $s->id }},@endforeach]);
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResult.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.services.ajax_search') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    searchResult.innerHTML = '';
                    if (data.length === 0) {
                        searchResult.innerHTML = '<div class="text-[10px] text-center text-slate-500 py-4">Layanan tidak ditemukan</div>';
                    } else {
                        data.forEach(service => {
                            const isAlreadySelected = selectedIds.has(service.id);
                            const div = document.createElement('div');
                            div.className = `flex items-center justify-between p-3 rounded-xl bg-white/5 border border-transparent hover:border-white/10 hover:bg-white/10 cursor-pointer transition-all group ${isAlreadySelected ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''}`;
                            div.innerHTML = `
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-slate-200 truncate">${service.name}</div>
                                    <div class="text-[9px] text-slate-500 font-bold uppercase tracking-tighter">${service.category?.name || 'No Category'}</div>
                                </div>
                                <div class="text-right ml-3">
                                    <div class="text-[10px] font-bold text-accent-blue">Rp ${new Intl.NumberFormat('id-ID').format(service.price)}</div>
                                    <button type="button" class="text-[10px] font-bold text-primary hover:underline" onclick="addService(${service.id}, '${service.name.replace(/'/g, "\\'")}', ${service.price})">
                                        + Tambah
                                    </button>
                                </div>
                            `;
                            searchResult.appendChild(div);
                        });
                    }
                    searchResult.classList.remove('hidden');
                });
        }, 300);
    });

    window.addService = function(id, name, price) {
        if (selectedIds.has(id)) return;

        selectedIds.add(id);

        const placeholder = document.getElementById('selectedServicesPlaceholder');
        if (placeholder) placeholder.remove();

        const div = document.createElement('div');
        div.id = `selected-service-${id}`;
        div.className = `flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 group animate-in zoom-in-95 duration-200`;
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <input type="hidden" name="services[]" value="${id}">
                <div class="text-xs font-bold text-slate-200 truncate">${name}</div>
                <div class="text-[9px] text-slate-500 font-bold">Harga: Rp ${new Intl.NumberFormat('id-ID').format(price)}</div>
            </div>
            <button type="button" class="size-7 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all ml-3" onclick="removeService(${id})">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        `;
        selectedContainer.appendChild(div);

        // Clear search
        searchInput.value = '';
        searchResult.classList.add('hidden');
    }

    window.removeService = function(id) {
        selectedIds.delete(id);
        const el = document.getElementById(`selected-service-${id}`);
        if(el) el.remove();

        if (selectedIds.size === 0) {
            selectedContainer.innerHTML = `
                <div id="selectedServicesPlaceholder" class="flex flex-col items-center justify-center text-slate-500 text-[10px] border-dashed border border-white/10 py-4 w-full">
                    <span class="material-symbols-outlined text-lg mb-1 opacity-20">check_box_outline_blank</span>
                    Belum ada layanan yang dipilih
                </div>
            `;
        }
    }
</script>
@endpush

@push('styles')
<style>
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(0,240,255,0.2); border-radius: 10px; }
</style>
@endpush
