<?php

namespace App\Http\Controllers;

use App\Models\Skor_Pelanggaran;
use Illuminate\Http\Request;

class SkorPelanggaranController extends Controller
{
    public function index()
    {
        $SkorPelanggarans = Skor_Pelanggaran::all();
        return view('superadmin.skor-pelanggaran.index', compact('SkorPelanggarans'));
    }

    public function create()
    {
        return view('superadmin.skor-pelanggaran.create');
    }

    public function store(Request $request)
    {
        // Validasi dan simpan data skor pelanggaran
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'skor' => 'required|integer|min:1',
            'jenis_pelanggaran' => 'required|string|in:ringan,sedang,berat',
        ]);

        Skor_Pelanggaran::create($request->all());

        return redirect()->route('skor-Pelanggaran.index')->with('message', 'Data berhasil disimpan.');
    }

        public function destroy($id)
    {
        $skor = Skor_Pelanggaran::findOrFail($id);
        $skor->delete();

        return redirect()->route('skor-Pelanggaran.index')->with('message', 'Data berhasil dihapus.');
    }
}
