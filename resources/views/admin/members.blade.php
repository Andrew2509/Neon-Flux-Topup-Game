@extends('admin.layouts.app')

@section('title', 'Daftar Member')
@section('page_title', 'Daftar Member')
@section('page_description', 'Kelola semua data member yang terdaftar di sistem Neon Flux.')

@section('content')
<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="glass-panel p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 border border-white/5">
        <form action="{{ route('admin.members') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg transition-colors group-focus-within:text-primary">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Email, atau No HP..." class="bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all w-full outline-none">
            </div>
            <button type="submit" class="glass-panel px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/10 transition-all">
                Cari
            </button>
        </form>
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
             <a href="{{ route('admin.members.create') }}" class="bg-accent-blue/10 hover:bg-accent-blue/20 text-accent-blue border border-accent-blue/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Member
            </a>
            <button class="bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/5">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Members Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Profil</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Saldo</th>
                        <th class="px-6 py-4 text-center">Total Transaksi</th>
                        <th class="px-6 py-4">Bergabung Pada</th>
                        <th class="px-6 py-4 text-center">Role</th>
                        <th class="px-6 py-4 text-center">Verification</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($members as $member)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $member->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&background=2563eb&color=fff' }}" alt="{{ $member->name }}" class="size-10 rounded-full object-cover border border-white/10">
                                <div>
                                    <div class="text-sm font-bold text-slate-100">{{ $member->name }}</div>
                                    <div class="text-[10px] text-slate-500">ID: NF-USR-{{ $member->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-300 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">mail</span> {{ $member->email }}</div>
                            <div class="text-xs text-slate-400 mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">call</span> {{ $member->phone ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-accent-blue">Rp {{ number_format($member->balance, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center text-sm font-medium">{{ $member->orders_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $member->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $roleSlug = $member->role ? $member->role->slug : 'member';
                                $role_color = match($roleSlug) {
                                    'super-admin' => 'red',
                                    'vip' => 'amber',
                                    'member' => 'blue',
                                    default => 'slate'
                                };
                            @endphp
                            <span class="px-2 py-1 bg-{{ $role_color }}-500/10 text-{{ $role_color }}-400 text-[9px] font-bold rounded-lg border border-{{ $role_color }}-500/20 uppercase tracking-tighter">
                                {{ $member->role ? $member->role->name : 'Member' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($member->phone_verified_at)
                                <span class="px-2 py-1 bg-green-500/10 text-green-400 text-[9px] font-bold rounded-lg border border-green-500/20 uppercase tracking-tighter flex items-center gap-1 justify-center mx-auto w-fit">
                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                    Telah Di Verifikasi
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-500/10 text-red-400 text-[9px] font-bold rounded-lg border border-red-500/20 uppercase tracking-tighter flex items-center gap-1 justify-center mx-auto w-fit">
                                    <span class="material-symbols-outlined text-[12px]">pending</span>
                                    Belum Verifikasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $status_color = match($member->status) {
                                    'active' => 'blue',
                                    'vip' => 'amber',
                                    'blocked' => 'red',
                                    default => 'slate'
                                };
                            @endphp
                            <span class="px-3 py-1 bg-{{ $status_color }}-500/10 text-{{ $status_color }}-400 text-[10px] font-bold rounded-full border border-{{ $status_color }}-500/20">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.members.show', $member->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all" title="Detail Member">
                                    <span class="material-symbols-outlined text-lg">person_search</span>
                                </a>
                                <a href="{{ route('admin.members.edit', $member->id) }}" class="size-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-secondary hover:border-secondary transition-all" title="Edit Member">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="size-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all" title="Hapus Member">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3 text-slate-500">
                                <span class="material-symbols-outlined text-5xl opacity-20">group</span>
                                <p class="text-sm font-medium">Belum ada member terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-white/5">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-[10px] text-slate-500 uppercase font-bold">
                    Menampilkan {{ $members->firstItem() ?: 0 }} - {{ $members->lastItem() ?: 0 }} dari {{ $members->total() }} Member
                </p>
                <div class="pagination-custom">
                    {{ $members->links('vendor.pagination.tailwind-admin') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
