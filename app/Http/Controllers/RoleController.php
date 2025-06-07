<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    public function index()
    {
        $datas = [
            'roles' => Role::with('permissions')->get(),
            'title' => 'Roles', // Judul halaman roles
        ];

        return view('superadmin.role.index', $datas);
    }

    public function create()
    {
        return view('superadmin.role.createRole');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if (!empty($request->permissions)) {
            foreach ($request->permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
                $role->givePermissionTo($permissionName);
            }
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        
        return view('superadmin.role.editRole', compact('role', 'permissions'));
    }

    public function update(Request $request, $roleId)
    {
        // Mencari role berdasarkan ID
        $role = Role::findOrFail($roleId);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $roleId, // Memperbaiki validasi unik
            'permissions' => 'array',  // Memastikan permissions berupa array
        ]);

        if ($validator->passes()) {
            // Update nama role
            $role->update(['name' => $request->name]);

            // Hapus permissions lama dan sync permissions baru
            $role->syncPermissions($request->permissions);

            // Mengarahkan ke halaman roles dengan pesan sukses
            return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
        } else {
            // Kembali ke halaman roles dengan error dan input sebelumnya
            return redirect()->route('roles.index')->withInput()->withErrors($validator);
        }
    }


    public function managePermissions($id)
    {
        // Ambil data role berdasarkan ID
        $role = Role::findOrFail($id);
        
        // Ambil semua permission yang tersedia
        $permissions = Permission::all();
        
        // Ambil permission yang sudah dimiliki role ini
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('superadmin.role.manage', compact('role', 'permissions', 'rolePermissions'));
    }
    
    /**
     * Update permissions untuk role tertentu
     */
    public function updatePermissions(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Ambil role berdasarkan ID
        $role = Role::findOrFail($id);
        
        // Jika tidak ada permissions yang dipilih
        if (!$request->has('permissions')) {
            $role->syncPermissions([]);
            return redirect()->route('roles.index')
                         ->with('success', 'Permissions untuk role ' . $role->name . ' berhasil diperbarui.');
        }
        
        // Ambil semua permission yang dipilih berdasarkan ID
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        
        // Sync permissions menggunakan collection permission
        $role->syncPermissions($permissions);
        
        return redirect()->route('roles.index')
                         ->with('success', 'Permissions untuk role ' . $role->name . ' berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role == null) {
            session()->flash('error', 'role not found.');

            return response()->json(['error' => 'Role not found.']);
        }

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}