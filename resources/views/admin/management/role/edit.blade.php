@extends('admin.layouts.app')

@section('title', 'Edit Role: ' . $role->name)
@section('page_title', 'Edit Role')
@section('page_description', 'Perbarui hak akses untuk grup ' . $role->name)

@php
    $isSystemRole = in_array($role->slug, ['super-admin', 'member']);
    $isSuperAdmin = $role->slug === 'super-admin';
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.management.role.index') }}" class="flex items-center gap-2 text-slate-400 hover:text-primary transition-colors mb-6 group">
        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
        <span class="text-sm font-medium">Kembali ke Daftar</span>
    </a>

    <form action="{{ route('admin.management.role.update', $role->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="glass-panel p-8 rounded-3xl space-y-6">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <label class="text-sm font-bold text-slate-400 uppercase tracking-wider">Nama Role</label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                        {{ $isSystemRole ? 'readonly' : '' }}
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none {{ $isSystemRole ? 'opacity-70 cursor-not-allowed' : '' }}" 
                        placeholder="Contoh: Admin Operator" required>
                </div>
                @if($isSystemRole)
                <div class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 text-primary flex items-center gap-2 h-fit mt-6">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Role Sistem</span>
                </div>
                @endif
            </div>
            @if($isSuperAdmin)
            <div class="p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-400 flex items-start gap-3">
                <span class="material-symbols-outlined text-xl">info</span>
                <p class="text-xs leading-relaxed">Role <strong>Super Admin</strong> memiliki akses penuh ke seluruh fitur sistem secara permanen untuk mencegah kesalahan konfigurasi yang dapat mengunci akses sistem.</p>
            </div>
            @endif
            @error('name') <p class="text-xs text-accent-red mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold">Izin & Hak Akses</h3>
                @if(!$isSuperAdmin)
                <button type="button" onclick="toggleAllPermissions(true)" class="text-xs font-bold text-primary hover:underline">Pilih Semua</button>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($permissions as $permission)
                @php $isChecked = in_array($permission->id, $rolePermissions); @endphp
                <label class="glass-panel p-4 rounded-2xl flex items-center gap-4 border border-white/5 transition-all group {{ $isSuperAdmin ? 'opacity-80' : 'cursor-pointer hover:bg-white/5' }}">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                            {{ $isChecked || $isSuperAdmin ? 'checked' : '' }}
                            {{ $isSuperAdmin ? 'disabled' : '' }}
                            class="permission-checkbox peer appearance-none size-6 border-2 border-white/10 rounded-lg checked:bg-primary checked:border-primary transition-all outline-none {{ $isSuperAdmin ? 'cursor-default' : 'cursor-pointer' }}">
                        <span class="material-symbols-outlined absolute text-white scale-0 peer-checked:scale-100 transition-transform pointer-events-none text-sm font-bold">check</span>
                        
                        @if($isSuperAdmin)
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        @endif
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
                Simpan Perubahan
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
        document.querySelectorAll('.permission-checkbox').forEach(el => {
            if (!el.disabled) {
                el.checked = checked;
            }
        });
    }
</script>
@endpush
@endsection
