<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(20);
        $roles = Role::all();
        $tokovoucherProvider = Provider::forTokovoucher();

        return view('admin.management.user.index', compact('users', 'roles', 'tokovoucherProvider'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => ['required', Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
            'balance' => 0,
        ]);

        return redirect()->back()->with('status', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->password) {
            $request->validate(['password' => Password::defaults()]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('status', 'Data user berhasil diperbarui!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa menonaktifkan akun sendiri!'], 400);
        }

        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status user berhasil diubah!',
            'status' => $user->status
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus diri sendiri!');
        }

        $user->delete();
        return back()->with('status', 'User berhasil dihapus!');
    }
}
