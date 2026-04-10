@extends('admin.layouts.app')

@section('title', 'Manajemen User')
@section('page_title', 'Manajemen User')
@section('page_description', 'Kelola semua pengguna sistem, atur role, dan pantau status akun.')

@section('content')
<div class="space-y-6">
    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="openModal('modal-add-user')" class="glass-panel px-4 py-2.5 rounded-xl bg-primary/20 hover:bg-primary/30 text-primary border-primary/20 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">person_add</span>
                <span class="text-sm font-bold">Tambah User</span>
            </button>
        </div>

        <form action="{{ route('admin.management.user.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary outline-none" placeholder="Cari nama atau email...">
            </div>
            
            <select name="role_id" onchange="this.form.submit()" class="bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </form>
    </div>

    <!-- User Table -->
    <div class="glass-panel rounded-3xl overflow-hidden border border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5">
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">User</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">Kontak</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">Role</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold">Status</th>
                        <th class="px-6 py-4 text-xs uppercase tracking-wider text-slate-400 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden border border-white/10">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="size-full object-cover">
                                    @else
                                        <span class="text-sm font-bold text-primary">{{ substr($user->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ $user->name }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-slate-300">{{ $user->email }}</p>
                            <p class="text-xs text-slate-500">{{ $user->phone }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $user->role->slug === 'super-admin' ? 'bg-accent-red/10 text-accent-red border border-accent-red/20' : 'bg-primary/10 text-primary border border-primary/20' }}">
                                {{ $user->role->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="toggleUserStatus({{ $user->id }}, this)" class="inline-flex items-center gap-2 group">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $user->status === 'active' ? 'bg-green-400' : 'bg-slate-400' }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $user->status === 'active' ? 'bg-green-500' : 'bg-slate-500' }}"></span>
                                </span>
                                <span class="text-xs font-bold uppercase tracking-widest transition-colors {{ $user->status === 'active' ? 'text-green-500 group-hover:text-green-400' : 'text-slate-500 group-hover:text-slate-400' }}">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ json_encode($user) }})" class="size-9 glass-panel rounded-xl flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.management.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="size-9 glass-panel rounded-xl flex items-center justify-center text-accent-red hover:bg-accent-red hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-lg">person_remove</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">
                            Data user tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 bg-white/5 border-t border-white/5">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Add User -->
<div id="modal-add-user" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-add-user')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-6">
        <div class="glass-panel bg-[#0a0a15] border border-white/10 rounded-3xl overflow-hidden shadow-2xl animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-xl font-bold">Tambah User Baru</h3>
                <button onclick="closeModal('modal-add-user')" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="{{ route('admin.management.user.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">Role</label>
                        <select name="role_id" class="w-full bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase">Email</label>
                    <input type="email" name="email" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">WhatsApp / No. Telp</label>
                        <input type="text" name="phone" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" placeholder="62812..." required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">Status</label>
                        <select name="status" class="w-full bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase">Password</label>
                    <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('modal-add-user')" class="flex-1 px-6 py-3 rounded-xl border border-white/10 text-sm font-bold hover:bg-white/5 transition-all text-slate-400">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/20 hover:bg-primary/80 transition-all">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modal-edit-user" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-edit-user')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-6">
        <div class="glass-panel bg-[#0a0a15] border border-white/10 rounded-3xl overflow-hidden shadow-2xl animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-xl font-bold">Edit User</h3>
                <button onclick="closeModal('modal-edit-user')" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="form-edit-user" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">Nama Lengkap</label>
                        <input type="text" name="name" id="edit-name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase">Role</label>
                        <select name="role_id" id="edit-role-id" class="w-full bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase">Email</label>
                    <input type="email" name="email" id="edit-email" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase">Status</label>
                    <select name="status" id="edit-status" class="w-full bg-[#0a0a15] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none" required>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div class="space-y-1 pt-2">
                    <label class="text-xs font-bold text-slate-400 uppercase">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('modal-edit-user')" class="flex-1 px-6 py-3 rounded-xl border border-white/10 text-sm font-bold hover:bg-white/5 transition-all text-slate-400">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold shadow-lg shadow-primary/20 hover:bg-primary/80 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditModal(user) {
        const form = document.getElementById('form-edit-user');
        form.action = `/admin/management/user/${user.id}`;
        
        document.getElementById('edit-name').value = user.name;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-role-id').value = user.role_id;
        document.getElementById('edit-status').value = user.status;
        
        openModal('modal-edit-user');
    }

    function toggleUserStatus(userId, btn) {
        const spanPing = btn.querySelector('.animate-ping');
        const spanDot = btn.querySelector('.relative.inline-flex');
        const textSpan = btn.querySelector('span:last-child');

        fetch(`/admin/management/user/${userId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'active') {
                    spanPing.className = 'animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75';
                    spanDot.className = 'relative inline-flex rounded-full h-2 w-2 bg-green-500';
                    textSpan.className = 'text-xs font-bold uppercase tracking-widest transition-colors text-green-500 group-hover:text-green-400';
                    textSpan.textContent = 'Aktif';
                } else {
                    spanPing.className = 'animate-ping absolute inline-flex h-full w-full rounded-full bg-slate-400 opacity-75';
                    spanDot.className = 'relative inline-flex rounded-full h-2 w-2 bg-slate-500';
                    textSpan.className = 'text-xs font-bold uppercase tracking-widest transition-colors text-slate-500 group-hover:text-slate-400';
                    textSpan.textContent = 'Nonaktif';
                }
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mengubah status user.');
        });
    }
</script>
@endpush
@endsection
