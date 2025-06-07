<?php

namespace App\Http\Controllers;

use App\Models\KategoriTindakan;
use Illuminate\Http\Request;

class KategoriTindakanController extends Controller
{
    public function index()
    {
        $kategori = KategoriTindakan::all();
        return view('superadmin.kategori-tindakan.index', compact('kategori'));
    }

    public function create()
    {
        return view('superadmin.kategori-tindakan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255'
        ]);

        KategoriTindakan::create([
            'nama_tindakan' => $request->nama_tindakan
        ]);

        return redirect()->route('kategori-tindakan.index')->with('success', 'Kategori Tindakan created successfully.');
    }

    public function destroy($id)
    {
        $kategori = KategoriTindakan::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori tindakan berhasil dihapus.');
    }
}
