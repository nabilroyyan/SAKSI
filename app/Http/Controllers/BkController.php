<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BkController extends Controller
{
    // Tampilkan semua BK dan kelas yang diampu
    public function index()
    {
        // Ambil semua user dengan role 'bk'
        $bks = User::role('bk')
            ->with(['kelasYangDiampuBk.jurusan']) // eager load jurusan relationship
            ->get();

        $kelas = Kelas::where('stt', 'aktif')
            ->with('jurusan') // eager load jurusan
            ->get();
        
        return view('superadmin.bk.index', compact('bks', 'kelas'));
    }

    // Assign BK ke Kelas
    public function assign(Request $request)
    {
        $request->validate([
            'bk_id' => 'required|exists:users,id', 
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id'
        ]);

        $bk = User::find($request->bk_id);
        
        // Gunakan sync untuk replace semua assignments atau syncWithoutDetaching untuk menambah
        $bk->kelasYangDiampuBk()->sync($request->kelas_ids);

        return back()->with('success', 'BK berhasil diassign ke kelas');
    }

    // Hapus Assign BK dari Kelas
    public function unassign($bkId, $kelasId)
    {
        $bk = User::find($bkId);
        
        if ($bk) {
            $bk->kelasYangDiampuBk()->detach($kelasId);
            return back()->with('success', 'Assign kelas berhasil dihapus');
        }
        
        return back()->with('error', 'BK tidak ditemukan');
    }
     public function unassignAll($bkId)
    {
        $bk = User::find($bkId);
        
        if ($bk) {
            $jumlahKelas = $bk->kelasYangDiampuBk()->count();
            $bk->kelasYangDiampuBk()->detach();
            
            return back()->with('success', "Semua assignment kelas ({$jumlahKelas} kelas) berhasil dihapus dari {$bk->name}");
        }
        
        return back()->with('error', 'BK tidak ditemukan');
    }
}