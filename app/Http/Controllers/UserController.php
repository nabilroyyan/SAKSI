<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->input('role');
        
        // Query dasar
        $usersQuery = User::query();
        
        // Filter berdasarkan role jika ada
        if ($roleFilter) {
            $usersQuery->role($roleFilter);
        }
        
        // Ambil data user
        $users = $usersQuery->get();
        
        // Ambil semua role yang ada
        $roles = Role::all();
        
        // Data untuk view
        $data = [
            'users' => $users,
            'roles' => $roles
        ];
        
        return view('superadmin.user.index', compact('data'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('superadmin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nis_nip' => 'required|string|unique:users',
            'role' => 'required|exists:roles,id', // Validasi role ID harus ada di tabel roles
        ]);
    
        // Buat user baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'nis_nip' => $validated['nis_nip'],
            'email_verified_at' => now(),
        ]);
    
        // Ambil role berdasarkan ID
        $role = Role::findById($validated['role']);
        
        // Assign role ke user berdasarkan nama
        $user->assignRole($role->name);
    
        // Redirect dengan pesan sukses
        return redirect('/users')->with('success', 'User berhasil dibuat dan diberikan role.');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        
        return view('superadmin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed', // Gunakan confirmed untuk validasi password konfirmasi
            'nis_nip' => 'required|string|unique:users,nis_nip,'.$user->id,
            'role' => 'required|exists:roles,id',
        ]);

        // Cek jika password baru sama dengan password lama
        if (!empty($validated['password']) && Hash::check($validated['password'], $user->password)) {
            return redirect()->back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        // Update data user
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nis_nip' => $validated['nis_nip'],
        ];

        // Jika password diisi, update password
        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        // Update role
        $role = Role::findById($validated['role']);
        
        // Hapus semua role yang ada kemudian assign role baru
        $user->syncRoles([$role->name]);

        return redirect('/users')->with('success', 'Data user berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            // User tidak ditemukan, redirect dengan pesan error
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }
        
        $delete = $user->delete();
        
        if ($delete) {
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
        } else {
            return redirect()->route('users.index')->with('error', 'Gagal menghapus user.');
        }
    }
}