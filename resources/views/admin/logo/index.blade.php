@extends('admin.layouts.app')

@section('title', 'Logo Studio')
@section('page_title', 'Logo Studio v2.0')
@section('page_description', 'Generate premium esports logos using Google Imagen AI.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="logoGenerator()">
    <!-- Left Column: Settings & Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-panel p-6 rounded-2xl border-white/10">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-primary">
                <span class="material-symbols-outlined">settings</span>
                Konfigurasi Logo
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Mode Tampilan</label>
                    <button 
                        @click="isIsolated = !isIsolated"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl border transition-all"
                        :class="isIsolated ? 'bg-primary/20 border-primary text-primary' : 'bg-white/5 border-white/10 text-slate-400 hover:bg-white/10'"
                    >
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">auto_fix_high</span>
                            <span class="text-sm font-semibold" x-text="isIsolated ? 'Tanpa Background' : 'Background Full'"></span>
                        </div>
                        <span class="material-symbols-outlined text-sm" x-text="isIsolated ? 'toggle_on' : 'toggle_off'"></span>
                    </button>
                    <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                        <span x-show="isIsolated">Logo akan dibuat dengan latar belakang putih polos agar mudah dihapus.</span>
                        <span x-show="!isIsolated">Logo akan dibuat dengan latar belakang futuristik dan efek neon.</span>
                    </p>
                </div>

                <div class="pt-4 border-t border-white/5">
                    <button 
                        @click="generateLogo()"
                        :disabled="loading"
                        class="w-full bg-primary hover:bg-primary/80 disabled:opacity-50 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/20"
                    >
                        <span class="material-symbols-outlined animate-spin" x-show="loading">sync</span>
                        <span class="material-symbols-outlined" x-show="!loading">bolt</span>
                        <span x-text="loading ? 'Sedang Memproses...' : 'Generate Logo Baru'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border-white/10">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-primary">
                <span class="material-symbols-outlined">info</span>
                Panduan Penggunaan
            </h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="bg-primary/20 p-2 rounded-lg h-fit text-primary">
                        <span class="material-symbols-outlined text-sm">image</span>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">PNG Ready</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-tight">Mudah diintegrasikan ke website.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="bg-secondary/20 p-2 rounded-lg h-fit text-secondary">
                        <span class="material-symbols-outlined text-sm">sports_esports</span>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Esports Branding</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-tight">Desain agresif & agresif sesuai tren.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Preview -->
    <div class="lg:col-span-2">
        <div 
            class="aspect-square relative group rounded-3xl overflow-hidden border border-white/10 shadow-2xl flex items-center justify-center"
            :class="isIsolated ? 'bg-[url(\'https://www.transparenttextures.com/patterns/checkerboard.png\')] bg-slate-200' : 'bg-slate-900 shadow-primary/10'"
        >
            <!-- Loading Overlay -->
            <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 z-10 backdrop-blur-sm">
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary text-3xl animate-pulse">brush</span>
                </div>
                <p class="mt-6 text-primary font-black tracking-[0.2em] animate-pulse uppercase text-xs">Membangun Mahakarya...</p>
            </div>

            <!-- Error State -->
            <div x-show="error" class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center bg-slate-950">
                <span class="material-symbols-outlined text-secondary text-5xl mb-4">error</span>
                <p class="text-white font-bold text-lg mb-2">Gagal Menghasilkan Gambar</p>
                <p class="text-slate-400 text-sm mb-6" x-text="error"></p>
                <button @click="generateLogo()" class="px-8 py-3 bg-white text-slate-950 rounded-full font-bold hover:bg-slate-200 transition-all">Coba Lagi</button>
            </div>

            <!-- Image Preview -->
            <template x-if="imageUrl">
                <div class="w-full h-full p-8 flex flex-col items-center justify-center">
                    <img :src="imageUrl" alt="Logo Preview" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl transition-transform hover:scale-[1.02] duration-500">
                    
                    <div class="absolute bottom-6 right-6 flex gap-2">
                        <button 
                            @click="downloadImage()"
                            class="flex items-center gap-2 bg-primary hover:bg-primary/80 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-xl"
                        >
                            <span class="material-symbols-outlined">download</span>
                            Simpan Hasil
                        </button>
                    </div>
                </div>
            </template>

            <!-- Placeholder -->
            <div x-show="!imageUrl && !loading && !error" class="text-center p-12">
                <span class="material-symbols-outlined text-slate-700 text-8xl mb-6">draw</span>
                <p class="text-slate-500 font-medium">Belum ada logo yang dibuat.</p>
                <p class="text-slate-600 text-xs mt-2 italic">Klik "Generate" untuk memulai.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-between items-center px-2">
            <div class="flex gap-2">
                <span class="bg-primary/10 text-primary text-[10px] px-3 py-1.5 rounded-full border border-primary/20 font-bold uppercase tracking-wider">Imagen 3.0 Engine</span>
                <span class="bg-purple-500/10 text-purple-400 text-[10px] px-3 py-1.5 rounded-full border border-purple-500/20 font-bold uppercase tracking-wider">Vector Optimized</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function logoGenerator() {
        return {
            imageUrl: null,
            loading: false,
            error: null,
            isIsolated: true,

            getPrompt() {
                const basePrompt = "A high-end, professional esports gaming logo for 'Neon Flux'. The centerpiece is a sharp, aggressive, stylized letter 'N' beautifully integrated with a sleek, futuristic PlayStation-style controller silhouette. The 'N' and the controller are glowing with vibrant electric blue and deep neon purple energy. The design should convey speed and power.";
                const styleSuffix = "Minimalist vector style, symmetrical, 8k resolution, cinematic lighting, high contrast.";
                
                if (this.isIsolated) {
                    return `${basePrompt} ISOLATED ON A PLAIN WHITE BACKGROUND. No shadows, no gradients in background, flat isolated vector logo. ${styleSuffix}`;
                } else {
                    return `${basePrompt} Dark futuristic background with subtle hexagonal grid patterns. ${styleSuffix}`;
                }
            },

            async generateLogo() {
                this.loading = true;
                this.error = null;
                this.imageUrl = null;

                try {
                    const response = await fetch("{{ route('admin.logo-generator.generate') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            prompt: this.getPrompt()
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Terjadi kesalahan saat menghubungi API');
                    }

                    this.imageUrl = data.image;
                } catch (err) {
                    console.error('Logo Generator Error:', err);
                    this.error = err.message;
                } finally {
                    this.loading = false;
                }
            },

            downloadImage() {
                if (!this.imageUrl) return;
                const link = document.createElement('a');
                link.href = this.imageUrl;
                link.download = this.isIsolated ? 'Neon_Flux_Logo_Clean.png' : 'Neon_Flux_Logo_Full.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
</script>
@endpush
@endsection
