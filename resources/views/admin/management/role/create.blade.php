@extends('admin.layouts.app')

@section('title', 'Tambah Role Baru')
@section('page_title', 'Tambah Role')
@section('page_description', 'Buat grup akses baru untuk staf atau operator Anda.')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.management.role.index') }}" class="flex items-center gap-2 text-slate-400 hover:text-primary transition-colors mb-6 group">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Daftar</span>
    </a>

    <form action="{{ route('admin.management.role.store') }}" method="POST" class="space-y-8">
        @csrf

        <div class="glass-panel p-8 rounded-3xl space-y-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-400 uppercase tracking-wider">Nama Role</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" placeholder="Contoh: Admin Operator" required>
                @error('name') <p class="text-xs text-accent-red mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold">Izin & Hak Akses</h3>
                <button type="button" onclick="toggleAllPermissions(true)" class="text-xs font-bold text-primary hover:underline">Pilih Semua</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($permissions as $permission)
                <label class="glass-panel p-4 rounded-2xl flex items-center gap-4 cursor-pointer hover:bg-white/5 border border-white/5 transition-all group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                            class="permission-checkbox peer appearance-none size-6 border-2 border-white/10 rounded-lg checked:bg-primary checked:border-primary transition-all outline-none cursor-pointer">
                        <span class="material-symbols-outlined absolute text-white scale-0 peer-checked:scale-100 transition-transform pointer-events-none text-sm font-bold">check</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-sm">{{ $permission->name }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">{{ $permission->slug }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="flex-1 glass-panel py-4 rounded-2xl bg-primary hover:bg-primary/80 text-white font-bold transition-all shadow-lg shadow-primary/20">
                Buat Role
            </button>
            <a href="{{ route('admin.management.role.index') }}" class="px-8 py-4 rounded-2xl border border-white/10 text-slate-400 hover:bg-white/5 transition-all text-sm font-bold">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.permission-checkbox').forEach(el => el.checked = checked);
    }
</script>
@endpush
@endsection
