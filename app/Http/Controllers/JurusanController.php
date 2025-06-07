<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index()
{
    $jurusan = Jurusan::all();
    return view('superadmin.jurusan.index', compact('jurusan'));
}

public function create()
{
    return view('superadmin.jurusan.create');
}

public function store(Request $request)
{
    $request->validate([
        'nama_jurusan' => 'required|string|max:50',
    ]);

    Jurusan::create([
        'nama_jurusan' => $request->nama_jurusan,
    ]);

    return redirect()->route('jurusan.index')->with('success', 'Jurusan created successfully.');
}

public function edit($id)
{
    $jurusan = Jurusan::findOrFail($id);
    return view('superadmin.jurusan.edit', compact('jurusan'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_jurusan' => 'required|string|max:50',
    ]);

    $jurusan = Jurusan::findOrFail($id);
    $jurusan->update([
        'nama_jurusan' => $request->nama_jurusan,
    ]);

    return redirect()->route('jurusan.index')->with('success', 'Jurusan updated successfully.');
}

public function destroy($id)
{
    $jurusan = Jurusan::findOrFail($id);
    $jurusan->delete();

    return redirect()->route('jurusan.index')->with('success', 'Jurusan deleted successfully.');
}


}