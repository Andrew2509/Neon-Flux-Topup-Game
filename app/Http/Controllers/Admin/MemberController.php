<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Menampilkan semua user
        $members = User::query()
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return view('admin.members', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:member,vip,admin',
            'status' => 'required|string|in:active,blocked',
            'balance' => 'required|numeric|min:0',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'balance' => $request->balance,
        ]);

        return redirect()->route('admin.members')->with('success', 'Member berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return view('admin.members.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.members.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:member,vip,admin',
            'status' => 'required|string|in:active,blocked',
            'balance' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'balance' => $request->balance,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.members')->with('success', 'Data member berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.members')->with('success', 'Member berhasil dihapus.');
    }
}
