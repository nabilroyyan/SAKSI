<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('halaman-admin.user.createUser', compact('roles'));
    }

    public function store(Request $request)
    {
        // Ambil role yang dipilih untuk pengecekan
        $role = Role::find($request->input('role'));

        // Validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,id',
        ];

        // Tambahkan validasi kondisional untuk nis_nip
        // Hanya wajib diisi jika role yang dipilih adalah 'siswa'
        if ($role && strtolower($role->name) === 'siswa') {
            $rules['nis_nip'] = 'required|string|unique:users,nis_nip';
        }

        $request->validate($rules);

        // Siapkan data user
        $userData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'email_verified_at' => now(),
        ];

        // Tentukan nilai nis_nip berdasarkan role
        if ($role && strtolower($role->name) === 'siswa') {
            // Jika role adalah siswa, gunakan input dari form
            $userData['nis_nip'] = $request->input('nis_nip');
        } else {
            // Jika bukan siswa, buat NIP unik secara otomatis
            // Format: [Huruf Awal Role]-[Timestamp]
            $prefix = strtoupper(substr($role->name, 0, 1));
            $userData['nis_nip'] = $prefix . '-' . time();
        }

        // Buat user baru dan berikan role
        $user = User::create($userData);
        $user->assignRole($role->name);

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
        // Memulai transaksi database untuk memastikan operasi berhasil.
        DB::beginTransaction();

        try {
            // 1. Cari user berdasarkan ID.
            $user = User::find($id);

            // 2. Jika user tidak ditemukan, batalkan transaksi dan redirect dengan error.
            if (!$user) {
                DB::rollBack();
                return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
            }

            // 3. Hapus data user secara permanen menggunakan forceDelete().
            $user->forceDelete();

            // 4. Jika proses berhasil, konfirmasi transaksi.
            DB::commit();

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            // 5. Jika terjadi error di tengah proses, batalkan semua perubahan.
            DB::rollBack();

            // Catat error (opsional, tapi sangat disarankan untuk debugging)
            // \Log::error('Gagal menghapus user: ' . $e->getMessage());

            return redirect()->route('users.index')->with('error', 'Gagal menghapus user karena terjadi kesalahan pada server.');
        }
    }
}