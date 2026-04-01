@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')
@section('page_title', 'Konfigurasi Sistem')
@section('page_description', 'Kelola identitas, kontak, dan parameter operasional PrincePay Gaming secara terpusat.')

@section('content')
<div class="space-y-8 pb-12" x-data="{ tab: 'umum' }">
    <!-- Header Stats / Overview (Subtle) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-panel p-4 rounded-2xl border border-white/5 bg-slate-900/40 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-xl">language</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Site Status</p>
                <h4 class="text-xs font-bold text-green-400 flex items-center gap-1.5">
                    <span class="size-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    Online
                </h4>
            </div>
        </div>
        <div class="glass-panel p-4 rounded-2xl border border-white/5 bg-slate-900/40 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20 shadow-lg shadow-blue-500/5">
                <span class="material-symbols-outlined text-xl">speed</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Performance</p>
                <h4 class="text-xs font-bold text-slate-200">Optimal</h4>
            </div>
        </div>
        <div class="glass-panel p-4 rounded-2xl border border-white/5 bg-slate-900/40 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/20 shadow-lg shadow-amber-500/5">
                <span class="material-symbols-outlined text-xl">security</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Security</p>
                <h4 class="text-xs font-bold text-slate-200">WAF Active</h4>
            </div>
        </div>
        <div class="glass-panel p-4 rounded-2xl border border-white/5 bg-slate-900/40 flex items-center gap-4">
            <div class="size-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20 shadow-lg shadow-purple-500/5">
                <span class="material-symbols-outlined text-xl">update</span>
            </div>
            <div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Last Sync</p>
                <h4 class="text-xs font-bold text-slate-200">2m ago</h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
        <!-- Sidebar Navigation Settings (Premium Style with Tab Switching) -->
        <div class="xl:col-span-1 space-y-6 sticky top-24">
            <div class="glass-panel p-2 rounded-2xl border border-white/5 bg-slate-900/40 backdrop-blur-xl">
                <nav class="flex flex-col gap-1">
                    <button
                        @click="tab = 'umum'"
                        :class="tab === 'umum' ? 'bg-linear-to-r from-primary/20 to-transparent text-primary border-l-4 border-primary shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-slate-100 border-l-4 border-transparent'"
                        class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-bold transition-all group text-left">
                        <span class="material-symbols-outlined shrink-0" :class="tab === 'umum' ? 'text-primary' : 'text-slate-500 group-hover:text-primary'">settings</span>
                        <span>Umum & SEO</span>
                    </button>
                    <button
                        @click="tab = 'kontak'"
                        :class="tab === 'kontak' ? 'bg-linear-to-r from-primary/20 to-transparent text-primary border-l-4 border-primary shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-slate-100 border-l-4 border-transparent'"
                        class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-bold transition-all group text-left">
                        <span class="material-symbols-outlined shrink-0" :class="tab === 'kontak' ? 'text-primary' : 'text-slate-500 group-hover:text-primary'">contact_support</span>
                        <span>Kontak & Sosmed</span>
                    </button>
                    <button
                        @click="tab = 'finansial'"
                        :class="tab === 'finansial' ? 'bg-linear-to-r from-primary/20 to-transparent text-primary border-l-4 border-primary shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-slate-100 border-l-4 border-transparent'"
                        class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-bold transition-all group text-left">
                        <span class="material-symbols-outlined shrink-0" :class="tab === 'finansial' ? 'text-primary' : 'text-slate-500 group-hover:text-primary'">payments</span>
                        <span>Finansial & Margin</span>
                    </button>
                    <button
                        @click="tab = 'api'"
                        :class="tab === 'api' ? 'bg-linear-to-r from-primary/20 to-transparent text-primary border-l-4 border-primary shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-slate-100 border-l-4 border-transparent'"
                        class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-bold transition-all group text-left">
                        <span class="material-symbols-outlined shrink-0" :class="tab === 'api' ? 'text-primary' : 'text-slate-500 group-hover:text-primary'">security</span>
                        <span>API & Keamanan</span>
                    </button>
                    <button
                        @click="tab = 'maintenance'"
                        :class="tab === 'maintenance' ? 'bg-linear-to-r from-primary/20 to-transparent text-primary border-l-4 border-primary shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-slate-100 border-l-4 border-transparent'"
                        class="flex items-center gap-3 px-5 py-4 rounded-xl text-sm font-bold transition-all group text-left">
                        <span class="material-symbols-outlined shrink-0" :class="tab === 'maintenance' ? 'text-primary' : 'text-slate-500 group-hover:text-primary'">construction</span>
                        <span>Mode Pemeliharaan</span>
                    </button>
                </nav>
            </div>

            <!-- Context Info Card -->
            <div class="glass-panel p-6 rounded-2xl border border-primary/10 bg-primary/5 backdrop-blur-md overflow-hidden relative group">
                <div class="absolute -right-4 -top-4 size-20 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all"></div>
                <div class="flex items-center gap-3 relative z-10 mb-3">
                    <div class="size-8 rounded-xl bg-primary/20 text-primary flex items-center justify-center border border-primary/30">
                        <span class="material-symbols-outlined text-sm">lightbulb</span>
                    </div>
                    <h4 class="text-xs font-bold text-slate-100 italic">Quick Tip</h4>
                </div>
                <p class="text-[10px] text-slate-400 leading-relaxed relative z-10">Pastikan untuk menekan tombol <strong>Simpan</strong> di setiap halaman sebelum berpindah ke bagian pengaturan lainnya.</p>
            </div>
        </div>

        <!-- Main Form Settings (Upgraded Look) -->
        <div class="xl:col-span-3">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Section: Umum & SEO -->
                <div x-show="tab === 'umum'" x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="glass-panel p-8 md:p-10 rounded-3xl border border-white/5 bg-slate-900/40 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-9xl">public</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-white/5 pb-8 relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="size-8 rounded-lg bg-primary/20 text-primary flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-lg">edit_note</span>
                                </span>
                                <h3 class="text-xl font-bold bg-linear-to-r from-white to-slate-400 bg-clip-text text-transparent">Informasi Umum & SEO</h3>
                            </div>
                            <p class="text-xs text-slate-500">Sesuaikan bagaimana website Anda muncul di browser dan hasil pencarian.</p>
                        </div>
                        <button type="submit" class="bg-linear-to-r from-primary to-primary-dark text-black px-8 py-3.5 rounded-xl text-sm font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            Simpan Perubahan
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Nama Website</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-primary transition-colors">branding_watermark</span>
                                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'PrincePay Gaming' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 placeholder:text-slate-700 hover:border-white/20 focus:ring-1 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Tagline Layanan</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-primary transition-colors">auto_awesome</span>
                                <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? 'Premium Gaming Experience' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 placeholder:text-slate-700 hover:border-white/20 focus:ring-1 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="md:col-span-2 space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Meta Deskripsi (SEO)</label>
                            <textarea name="meta_description" rows="3" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl px-5 py-4 text-sm text-slate-100 placeholder:text-slate-700 hover:border-white/20 focus:ring-1 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-inner resize-none text-left">{{ $settings['meta_description'] ?? 'Platform top up game termurah, tercepat, dan teraman di Indonesia. Tersedia berbagai pilihan game favorit Anda.' }}</textarea>
                        </div>

                        <!-- Asset Section -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-8 pt-6">
                            <div class="md:col-span-2 space-y-4">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Brand Logo (SVG / PNG)</label>
                                <div onclick="document.getElementById('site_logo').click()" class="relative h-44 rounded-3xl bg-slate-950/50 border border-white/10 border-dashed flex flex-col items-center justify-center group hover:bg-white/5 hover:border-primary/50 cursor-pointer transition-all overflow-hidden shadow-inner">
                                    <input type="file" id="site_logo" name="site_logo" class="hidden" onchange="previewImage(this, 'preview_logo')">
                                    @if(isset($settings['site_logo']))
                                        <img id="preview_logo" src="{{ get_image_url('site_logo') }}" class="absolute inset-0 w-full h-full object-contain p-4 bg-slate-900/50">
                                    @else
                                        <div id="preview_logo_placeholder" class="size-16 rounded-full bg-primary/10 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-primary/20 transition-all">
                                            <span class="material-symbols-outlined text-2xl text-primary">cloud_upload</span>
                                        </div>
                                        <h5 class="text-sm font-bold text-slate-200">Klik untuk Upload Logo</h5>
                                        <p class="text-[10px] text-slate-500 mt-1">Recommended size 512x128px</p>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Favicon (.ico)</label>
                                <div onclick="document.getElementById('site_favicon').click()" class="relative h-44 rounded-3xl bg-slate-950/50 border border-white/10 border-dashed flex flex-col items-center justify-center group hover:bg-white/5 hover:border-primary/50 cursor-pointer transition-all shadow-inner overflow-hidden">
                                    <input type="file" id="site_favicon" name="site_favicon" class="hidden" onchange="previewImage(this, 'preview_favicon')">
                                    @if(isset($settings['site_favicon']))
                                        <img id="preview_favicon" src="{{ get_image_url('site_favicon') }}" class="absolute inset-0 w-full h-full object-contain p-8">
                                    @else
                                        <div id="preview_favicon_placeholder" class="size-12 rounded-xl bg-slate-900 border border-white/10 flex items-center justify-center mb-3 group-hover:border-primary/30">
                                            <span class="material-symbols-outlined text-slate-700 text-3xl">image</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Upload Favicon</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Kontak & Sosmed -->
                <div x-show="tab === 'kontak'" x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="glass-panel p-8 md:p-10 rounded-3xl border border-white/5 bg-slate-900/40 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-9xl">contact_support</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-white/5 pb-8 relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="size-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center border border-blue-500/20">
                                    <span class="material-symbols-outlined text-lg">chat</span>
                                </span>
                                <h3 class="text-xl font-bold bg-linear-to-r from-white to-slate-400 bg-clip-text text-transparent">Kontak & Sosial Media</h3>
                            </div>
                            <p class="text-xs text-slate-500">Hubungkan pelanggan dengan layanan bantuan Anda.</p>
                        </div>
                        <button type="submit" class="bg-linear-to-r from-primary to-primary-dark text-black px-8 py-3.5 rounded-xl text-sm font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">save</span>
                            Simpan Kontak
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">WhatsApp CS</label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2 pr-3 border-r border-white/10">
                                    <span class="text-xs font-bold text-green-500">+62</span>
                                </div>
                                <input type="text" name="whatsapp_cs" value="{{ $settings['whatsapp_cs'] ?? '81234567890' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-20 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-green-500/50 focus:border-green-500 outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Email Support</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-blue-400 transition-colors">mail</span>
                                <input type="email" name="email_support" value="{{ $settings['email_support'] ?? 'support@princepay.id' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-400/50 focus:border-blue-400 outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Nomor Telepon Support</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-blue-500 transition-colors">phone_in_talk</span>
                                <input type="text" name="site_phone" value="{{ $settings['site_phone'] ?? '+62 812 3456 7890' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="md:col-span-2 space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Alamat Usaha</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-4 text-slate-600 text-lg group-focus-within:text-blue-500 transition-colors">location_on</span>
                                <textarea name="site_address" rows="2" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-inner resize-none text-left">{{ $settings['site_address'] ?? 'Jl. Jend. Sudirman No. 123, Jakarta' }}</textarea>
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Username Instagram</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-pink-500 transition-colors">photo_camera</span>
                                <input type="text" name="instagram_username" value="{{ $settings['instagram_username'] ?? '@princepay.gaming' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-pink-500/50 focus:border-pink-500 outline-none transition-all shadow-inner">
                            </div>
                        </div>
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Halaman Facebook</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg group-focus-within:text-blue-600 transition-colors">facebook</span>
                                <input type="text" name="facebook_page" value="{{ $settings['facebook_page'] ?? 'PrincePay Gaming Official' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-600/50 focus:border-blue-600 outline-none transition-all shadow-inner">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Finansial & Margin -->
                <div x-show="tab === 'finansial'" x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="glass-panel p-8 md:p-10 rounded-3xl border border-white/5 bg-slate-900/40 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-9xl">payments</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-white/5 pb-8 relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="size-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/20">
                                    <span class="material-symbols-outlined text-lg">trending_up</span>
                                </span>
                                <h3 class="text-xl font-bold bg-linear-to-r from-white to-slate-400 bg-clip-text text-transparent">Finansial & Margin Keuntungan</h3>
                            </div>
                            <p class="text-xs text-slate-500">Atur parameter harga dan keuntungan sistem secara global.</p>
                        </div>
                        <button type="submit" class="bg-linear-to-r from-primary to-primary-dark text-black px-8 py-3.5 rounded-xl text-sm font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">analytics</span>
                            Simpan Finansial
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                <span class="size-1.5 rounded-full bg-primary"></span>
                                Margin Keuntungan (Global)
                            </h5>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2 group">
                                    <label class="text-[10px] font-bold text-slate-500 ml-1">Publik (%)</label>
                                    <div class="relative">
                                        <input type="number" name="margin_public" value="{{ $settings['margin_public'] ?? '10' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-inner">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-600">%</span>
                                    </div>
                                </div>
                                <div class="space-y-2 group">
                                    <label class="text-[10px] font-bold text-slate-500 ml-1">Reseller (%)</label>
                                    <div class="relative">
                                        <input type="number" name="margin_reseller" value="{{ $settings['margin_reseller'] ?? '5' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-inner">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-600">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                <span class="size-1.5 rounded-full bg-blue-400"></span>
                                Tambahan Biaya Admin
                            </h5>
                            <div class="space-y-2 group">
                                <label class="text-[10px] font-bold text-slate-500 ml-1">Biaya Transaksi (Flat)</label>
                                <div class="relative">
                                    <input type="text" name="transaction_fee" value="{{ $settings['transaction_fee'] ?? '1,000' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-400/50 focus:border-blue-400 outline-none transition-all shadow-inner">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-600">Rp</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: API & Keamanan -->
                <div x-show="tab === 'api'" x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="glass-panel p-8 md:p-10 rounded-3xl border border-white/5 bg-slate-900/40 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-9xl">security</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-white/5 pb-8 relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="size-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center border border-red-500/20">
                                    <span class="material-symbols-outlined text-lg">vpn_key</span>
                                </span>
                                <h3 class="text-xl font-bold bg-linear-to-r from-white to-slate-400 bg-clip-text text-transparent">API Integrated & Keamanan</h3>
                            </div>
                            <p class="text-xs text-slate-500">Konfigurasi kredensial API dan parameter keamanan sistem.</p>
                        </div>
                        <button type="submit" class="bg-linear-to-r from-primary to-primary-dark text-black px-8 py-3.5 rounded-xl text-sm font-black shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">shield_lock</span>
                            Simpan Kunci
                        </button>
                    </div>

                    <div class="space-y-8 relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3 group">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Admin API Key</label>
                                <div class="relative">
                                    <input type="password" name="admin_api_key" value="{{ $settings['admin_api_key'] ?? 'PPG_992182741928' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl px-5 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-red-400/50 focus:border-red-400 outline-none transition-all shadow-inner">
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-white cursor-pointer transition-colors">visibility</span>
                                </div>
                            </div>
                            <div class="space-y-3 group">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">HMAC Secret</label>
                                <div class="relative">
                                    <input type="password" name="hmac_secret" value="{{ $settings['hmac_secret'] ?? 'SECRET_V1_77AB29' }}" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl px-5 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-red-400/50 focus:border-red-400 outline-none transition-all shadow-inner">
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-white cursor-pointer transition-colors">visibility</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 text-sm">history</span>
                                <h6 class="text-xs font-bold text-slate-200">Whitelist IP Address</h6>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-[10px] font-mono text-slate-400 border border-white/5 flex items-center gap-2">
                                    127.0.0.1
                                    <span class="material-symbols-outlined text-[12px] hover:text-red-400 cursor-pointer">close</span>
                                </span>
                                <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-[10px] font-mono text-slate-400 border border-white/5 flex items-center gap-2">
                                    192.168.1.100
                                    <span class="material-symbols-outlined text-[12px] hover:text-red-400 cursor-pointer">close</span>
                                </span>
                                <button type="button" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-[10px] font-bold border border-primary/20 hover:bg-primary/20 transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">add</span> Tambah IP
                                </button>
                            </div>
                            <p class="text-[9px] text-slate-600 italic">Hanya IP di atas yang diizinkan untuk melakukan request API administratif.</p>
                        </div>

                        <!-- One-Click Cron-Job.org Setup -->
                        <div class="p-8 rounded-3xl bg-blue-500/5 border border-blue-500/10 space-y-6 relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 size-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="size-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center border border-blue-500/30">
                                    <span class="material-symbols-outlined text-2xl">schedule_send</span>
                                </div>
                                <div>
                                    <h5 class="text-sm font-black text-slate-100 italic tracking-wide">cron-job.org <span class="text-blue-400 text-[10px] font-bold ml-1 uppercase not-italic tracking-tighter">One-Click Setup</span></h5>
                                    <p class="text-[10px] text-slate-500 mt-1">Daftarkan jadwal sinkronisasi otomatis ke akun cron-job.org Anda secara instan.</p>
                                </div>
                            </div>

                            <div class="space-y-4 relative z-10">
                                <div class="space-y-2 group">
                                    <label class="text-[10px] font-bold text-slate-500 ml-1 tracking-widest">API KEY CRON-JOB.ORG</label>
                                    <div class="flex flex-col md:flex-row gap-3">
                                        <div class="relative flex-1">
                                            <input type="password"
                                                   id="cron_job_api_key"
                                                   name="cron_job_api_key"
                                                   value="{{ $settings['cronjob_api_key'] ?? '' }}"
                                                   placeholder="Masukkan API Key dari cron-job.org"
                                                   class="w-full bg-slate-950/50 border border-white/10 rounded-2xl px-5 py-4 text-sm text-slate-100 focus:ring-1 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-inner">
                                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-white cursor-pointer transition-colors" onclick="togglePassword('cron_job_api_key')">visibility</span>
                                        </div>
                                        <button type="button"
                                                onclick="registerCronJob()"
                                                class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl text-xs font-black transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-900/20 active:scale-95 group">
                                            <span class="material-symbols-outlined text-lg group-hover:rotate-12 transition-transform">rocket_launch</span>
                                            Daftarkan Jadwal
                                        </button>
                                    </div>
                                </div>

                                @if(isset($settings['cronjob_job_id']))
                                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-500/10 border border-green-500/20 w-fit">
                                    <span class="size-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    <p class="text-[9px] font-black text-green-400 uppercase tracking-widest">Terdaftar (Job ID: {{ $settings['cronjob_job_id'] }})</p>
                                </div>
                                @endif

                                <p class="text-[9px] text-slate-600 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[12px]">info</span>
                                    Dapatkan API Key di: <a href="https://console.cron-job.org/settings" target="_blank" class="text-blue-400 underline hover:text-blue-300">console.cron-job.org/settings</a>
                                </p>
                            </div>
                        </div>

                        <script>
                            function togglePassword(id) {
                                const el = document.getElementById(id);
                                el.type = el.type === 'password' ? 'text' : 'password';
                            }

                            function registerCronJob() {
                                const apiKey = document.getElementById('cron_job_api_key').value;
                                if (!apiKey) {
                                    alert('Silakan masukkan API Key cron-job.org terlebih dahulu.');
                                    return;
                                }

                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = "{{ route('admin.cron.register') }}";

                                const csrf = document.createElement('input');
                                csrf.type = 'hidden';
                                csrf.name = '_token';
                                csrf.value = "{{ csrf_token() }}";
                                form.appendChild(csrf);

                                const apiInput = document.createElement('input');
                                apiInput.type = 'hidden';
                                apiInput.name = 'cron_job_api_key';
                                apiInput.value = apiKey;
                                form.appendChild(apiInput);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        </script>
                    </div>
                </div>

                <!-- Section: Mode Pemeliharaan -->
                <div x-show="tab === 'maintenance'" x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="glass-panel p-8 md:p-10 rounded-3xl border border-red-500/10 bg-red-500/5 backdrop-blur-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-9xl text-red-500">construction</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-white/5 pb-8 relative z-10">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="size-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center border border-red-500/20">
                                    <span class="material-symbols-outlined text-lg">warning</span>
                                </span>
                                <h3 class="text-xl font-bold bg-linear-to-r from-white to-slate-400 bg-clip-text text-transparent">Mode Pemeliharaan (Maintenance)</h3>
                            </div>
                            <p class="text-xs text-slate-500">Nonaktifkan akses publik sementara saat melakukan update sistem.</p>
                        </div>
                        <button type="submit" class="bg-linear-to-r from-red-600 to-red-800 text-white px-8 py-3.5 rounded-xl text-sm font-black shadow-xl shadow-red-900/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">power_settings_new</span>
                            Update Status
                        </button>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center justify-between p-6 rounded-2xl bg-white/5 border border-white/10">
                            <div>
                                <h6 class="text-sm font-bold text-slate-100">Aktifkan Maintenance Mode</h6>
                                <p class="text-[10px] text-slate-500 mt-1">Website akan menampilkan halaman khusus saat fitur ini aktif.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer scale-125 origin-right mr-2">
                                <input type="checkbox" name="maintenance_mode" value="1" {{ isset($settings['maintenance_mode']) && $settings['maintenance_mode'] ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500 peer-checked:after:bg-white shadow-lg shadow-black/50"></div>
                            </label>
                        </div>

                        <div class="space-y-3 group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">Pesan Pemeliharaan</label>
                            <textarea name="maintenance_message" rows="4" class="w-full bg-slate-950/50 border border-white/10 rounded-2xl px-5 py-4 text-sm text-slate-100 placeholder:text-slate-700 hover:border-white/20 focus:ring-1 focus:ring-red-500/50 focus:border-red-500 outline-none transition-all shadow-inner resize-none text-left">{{ $settings['maintenance_message'] ?? 'Mohon Maaf, PrincePay Gaming sedang dalam masa pemeliharaan sistem rutin. Kami akan segera kembali dalam beberapa saat. Terima kasih atas kesabarannya.' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Quick Actions -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-sm">history</span>
                        <p class="text-[10px] text-slate-500 font-medium">Terakhir diubah oleh <span class="text-slate-300 font-bold">Admin Utama</span> pada 09 Mar 2026, 16:30 WIB</p>
                    </div>
                    <div class="flex items-center gap-3">
                         <button type="button" class="text-slate-400 hover:text-white text-xs font-bold px-4 py-2 transition-all">
                            Reset ke Default
                        </button>
                         <button type="button" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl text-xs font-bold border border-white/10 transition-all shadow-lg">
                            Pratinjau Situs
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for Textarea */
    textarea::-webkit-scrollbar {
        width: 4px;
    }
    textarea::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }
    textarea::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    textarea::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById(previewId);
                let placeholder = document.getElementById(previewId + '_placeholder');

                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                } else {
                    // Create img if placeholder exists
                    const img = document.createElement('img');
                    img.id = previewId;
                    img.src = e.target.result;
                    img.className = "absolute inset-0 w-full h-full object-contain p-4 bg-slate-900/50";
                    placeholder.parentElement.appendChild(img);
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
