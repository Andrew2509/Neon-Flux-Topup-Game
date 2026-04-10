@extends('admin.layouts.app')

@section('title', 'Role & Permission')
@section('page_title', 'Role & Permission')
@section('page_description', 'Kelola hak akses dan izin untuk setiap grup pengguna.')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold">Daftar Role</h3>
        <a href="{{ route('admin.management.role.create') }}" class="glass-panel px-4 py-2 rounded-xl bg-primary/20 hover:bg-primary/30 text-primary border-primary/20 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">add</span>
            <span class="text-sm font-bold">Tambah Role</span>
        </a>
    </div>

    <div class="glass-panel rounded-3xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5">
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">Nama Role</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">Slug</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold text-center">Jumlah User</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($roles as $role)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold">
                                    {{ substr($role->name, 0, 1) }}
                                </div>
                                <span class="font-medium">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-slate-800 px-2 py-1 rounded text-slate-400">{{ $role->slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                {{ $role->users_count }} User
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.management.role.edit', $role->id) }}" class="size-9 glass-panel rounded-xl flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                @if(!in_array($role->slug, ['super-admin', 'member']))
                                <form action="{{ route('admin.management.role.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="size-9 glass-panel rounded-xl flex items-center justify-center text-accent-red hover:bg-accent-red hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            Belum ada role yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
