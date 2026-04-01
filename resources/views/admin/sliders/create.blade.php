@extends('admin.layouts.app')

@section('title', 'Tambah Slider & Banner')
@section('page_title', 'Unggah Slider Baru')
@section('page_description', 'Tambahkan gambar promosi atau banner baru ke halaman utama.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.sliders') }}" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors mb-6 group w-fit">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Kelola Slider</span>
    </a>

    <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="glass-panel p-8 rounded-3xl border border-white/5 space-y-8 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 blur-[100px] rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Detail Gambar -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="size-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">image</span>
                        </span>
                        Aset Visual
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">JUDUL PROMO</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">title</span>
                                <input type="text" name="title" required placeholder="Contoh: Promo Akhir Tahun" value="{{ old('title') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('title') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Image Selection Tabs --}}
                        <div class="space-y-1 pt-2">
                             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">METODE GAMBAR</label>
                             <div class="flex p-1 bg-white/5 rounded-xl border border-white/10 gap-1">
                                <button type="button" onclick="setMetode('upload')" id="btn-upload" class="flex-1 py-2 text-[10px] font-bold rounded-lg transition-all bg-primary text-black">UPLOAD FILE</button>
                                <button type="button" onclick="setMetode('url')" id="btn-url" class="flex-1 py-2 text-[10px] font-bold rounded-lg transition-all text-slate-400 hover:text-white">URL HTTPS</button>
                             </div>
                        </div>

                        <!-- File Upload (Visible by Default) -->
                        <div id="wrapper-upload" class="space-y-1 animate-in fade-in duration-300">
                            <div class="relative group">
                                <input type="file" name="image_file" id="image_file" accept="image/*" onchange="previewFile(this)"
                                    class="hidden">
                                <label for="image_file" class="w-full bg-white/5 border-2 border-dashed border-white/10 rounded-2xl px-4 py-8 flex flex-col items-center justify-center cursor-pointer hover:bg-white/[0.07] hover:border-primary/50 transition-all group">
                                    <span class="material-symbols-outlined text-3xl text-slate-500 group-hover:text-primary transition-colors mb-2">cloud_upload</span>
                                    <span class="text-[10px] font-bold text-slate-400 group-hover:text-white">KLIK UNTUK UNGGAH GAMBAR</span>
                                    <span class="text-[8px] text-slate-600 mt-1 uppercase tracking-tighter">MAX 2MB (JPG, PNG, WEBP)</span>
                                </label>
                            </div>
                            @error('image_file') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- URL Input (Hidden by Default) -->
                        <div id="wrapper-url" class="space-y-1 hidden animate-in fade-in duration-300">
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">link</span>
                                <input type="url" name="image_path" id="image_path" placeholder="https://example.com/image.jpg" value="{{ old('image_path') }}" oninput="previewUrl(this.value)"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all font-mono">
                            </div>
                            <p class="text-[9px] text-slate-500 mt-1 ml-1 leading-relaxed">Gunakan link langsung (Direct Link). Ukuran disarankan 1200x400px.</p>
                            @error('image_path') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">LABELS / TAGS</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">label</span>
                                <input type="text" name="tags" placeholder="HOT, RPG, NEW" value="{{ old('tags') }}"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            </div>
                            @error('tags') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Social Links Section --}}
                        <div class="space-y-4 pt-4 border-t border-white/5">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">KONTEN SOSIAL (OPSIONAL)</h4>
                            
                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-slate-500 ml-1 italic capitalize">Nomor WhatsApp Admin</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#25D366] text-lg">call</span>
                                    <input type="text" name="wa_link" placeholder="6281234567890" value="{{ old('wa_link') }}"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-[#25D366] outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-slate-500 ml-1 italic capitalize">Username Instagram</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#ee2a7b] text-lg">photo_camera</span>
                                    <input type="text" name="ig_link" placeholder="princepay.gaming" value="{{ old('ig_link') }}"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-[#ee2a7b] outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[9px] font-bold text-slate-500 ml-1 italic capitalize">Username/ID Facebook</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#1877F2] text-lg">facebook</span>
                                    <input type="text" name="fb_link" placeholder="PrincePayGaming" value="{{ old('fb_link') }}"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-[#1877F2] outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Konten & Preview -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-accent-blue uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="size-8 rounded-lg bg-accent-blue/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">description</span>
                        </span>
                        Narasi & Highlight
                    </h3>

                    <div class="space-y-4">
                        <div class="space-y-1">
                             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">SUBTITLE</label>
                             <div class="relative group">
                                 <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">stylus</span>
                                 <input type="text" name="subtitle" placeholder="Contoh: Event Terbatas" value="{{ old('subtitle') }}"
                                     class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                             </div>
                             @error('subtitle') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                             <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">LABELS / TAGS</label>
                             <div class="relative group">
                                 <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-lg group-focus-within:text-primary transition-colors">label</span>
                                 <input type="text" name="tags" placeholder="HOT, RPG, NEW" value="{{ old('tags') }}"
                                     class="w-full bg-white/5 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                             </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">DESKRIPSI</label>
                            <textarea name="description" rows="2" placeholder="Masukkan narasi promosi..."
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all resize-none">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">STATUS</label>
                                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary outline-none appearance-none cursor-pointer">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Sembunyikan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Preview Card --}}
                    <div class="w-full space-y-2 mt-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">visibility</span>
                            Live Preview
                        </label>
                        <div class="w-full aspect-21/9 rounded-2xl bg-black/40 border border-white/10 overflow-hidden relative flex items-center justify-center group shadow-2xl">
                             <div id="preview-placeholder" class="flex flex-col items-center gap-1 text-slate-600 transition-opacity">
                                 <span class="material-symbols-outlined text-4xl">photo_library</span>
                                 <span class="text-[8px] font-black uppercase tracking-tighter">BELUM ADA GAMBAR</span>
                             </div>
                             <img id="imgPreview" src="" class="absolute inset-0 w-full h-full object-cover hidden z-10 animate-in fade-in zoom-in-105 duration-500">
                             <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent z-20"></div>
                             
                             {{-- Overlay Mockup Content --}}
                             <div id="preview-overlay" class="absolute bottom-4 left-4 right-4 z-30 space-y-1 hidden">
                                 <span class="px-2 py-0.5 rounded-md bg-primary text-black text-[8px] font-black uppercase w-fit">PRIVEW TAG</span>
                                 <h4 class="text-xs font-black text-white uppercase truncate">Judul Terpilih</h4>
                                 <p class="text-[8px] text-white/60 line-clamp-1 italic">Narasi deskripsi slider akan muncul di sini...</p>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex items-center justify-end gap-3 border-t border-white/5">
                <a href="{{ route('admin.sliders') }}" class="px-6 py-3 rounded-2xl text-xs font-bold text-slate-500 hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="bg-primary text-black px-10 py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">cloud_upload</span>
                    Simpan Slider
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setMetode(metode) {
        const btnUpload = document.getElementById('btn-upload');
        const btnUrl = document.getElementById('btn-url');
        const wrapUpload = document.getElementById('wrapper-upload');
        const wrapUrl = document.getElementById('wrapper-url');
        const inputUrl = document.getElementById('image_path');
        const inputFile = document.getElementById('image_file');

        if (metode === 'upload') {
            btnUpload.className = 'flex-1 py-2 text-[10px] font-bold rounded-lg transition-all bg-primary text-black';
            btnUrl.className = 'flex-1 py-2 text-[10px] font-bold rounded-lg transition-all text-slate-400 hover:text-white';
            wrapUpload.classList.remove('hidden');
            wrapUrl.classList.add('hidden');
            inputUrl.value = ''; // Clear URL if switching to upload
        } else {
            btnUrl.className = 'flex-1 py-2 text-[10px] font-bold rounded-lg transition-all bg-primary text-black';
            btnUpload.className = 'flex-1 py-2 text-[10px] font-bold rounded-lg transition-all text-slate-400 hover:text-white';
            wrapUrl.classList.remove('hidden');
            wrapUpload.classList.add('hidden');
            inputFile.value = ''; // Clear File if switching to URL
        }
        updatePreviewVisibility();
    }

    function previewUrl(url) {
        const img = document.getElementById('imgPreview');
        const placeholder = document.getElementById('preview-placeholder');
        const overlay = document.getElementById('preview-overlay');

        if (url) {
            img.src = url;
            img.classList.remove('hidden');
            placeholder.classList.add('opacity-0');
            overlay.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('opacity-0');
            overlay.classList.add('hidden');
        }
    }

    function previewFile(input) {
        const img = document.getElementById('imgPreview');
        const placeholder = document.getElementById('preview-placeholder');
        const overlay = document.getElementById('preview-overlay');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.classList.remove('hidden');
                placeholder.classList.add('opacity-0');
                overlay.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updatePreviewVisibility() {
        // Reset preview when switching methods if needed
        // For now, let's keep it sticky unless cleared
    }
</script>
@endpush
@endsection
