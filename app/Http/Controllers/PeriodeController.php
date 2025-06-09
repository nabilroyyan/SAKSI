<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // ✅ Tampilkan semua periode
    public function index()
    {
        $periodes = Periode::all();
        return view('superadmin.periode.index', compact('periodes'));
    }

    // ✅ Tampilkan form tambah periode
    public function create()
    {
        $periodes = Periode::all();
        return view('superadmin.periode.create', compact('periodes'));
    }

    // ✅ Simpan periode baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|string',
            'semester' => 'required|in:ganjil,genap',
            'is_active' => 'required|in:aktif,non_aktif',
        ]);

        if ($validated['is_active'] === 'aktif') {
        Periode::where('is_active', 'aktif')->update(['is_active' => 'non_aktif']);
     }

        Periode::create($validated);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil ditambahkan');
    }

     public function deactivate($id)
    {
        $periode = Periode::findOrFail($id);

        // Set periode ini jadi non_aktif tanpa mengubah lainnya
        $periode->update(['is_active' => 'non_aktif']);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil dinonaktifkan');
    }



    // ✅ Update data periode
    public function update(Request $request, $id)
    {
        $periode = Periode::findOrFail($id);

        $validated = $request->validate([
            'tahun' => 'sometimes|required|string',
            'semester' => 'sometimes|required|in:ganjil,genap',
            'is_active' => 'sometimes|required|boolean',
        ]);

        // Jika diaktifkan, nonaktifkan yang lain
        if (isset($validated['is_active']) && $validated['is_active']) {
            Periode::where('is_active', 1)
                ->where('id', '!=', $id)
                ->update(['is_active' => 0]);
        }

        $periode->update($validated);

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diupdate');
    }

        public function activate($id)
    {
        // Nonaktifkan semua periode
        Periode::where('is_active', 'aktif')->update(['is_active' => 'non_aktif']);

        // Aktifkan periode yang dipilih
        $periode = Periode::findOrFail($id);
        $periode->is_active = 'aktif';
        $periode->save();

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diaktifkan');
    }


    // ✅ Hapus periode
    public function destroy($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return redirect()->route('periode.index')->with('success', 'Periode berhasil dihapus');
    }
}
