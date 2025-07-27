<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\KelasSiswa;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    // Tampilkan semua siswa
    public function index()
    {
        $siswa = Siswa::orderBy('created_at', 'desc')->get();
        return view('superadmin.siswa.index', compact('siswa'));
    }

    public function import(Request $request)
    {
        Excel::import(new SiswaImport, $request->file('file_siswa'));

        return redirect()->back()->with('success', 'Data berhasil diimpor');
    }

    // Tampilkan form tambah siswa
    public function create()
    {
        return view('superadmin.siswa.create');
    }
    public function store(Request $request)
    {

        $request->validate([
            'nis_nip' => 'required|string|max:20|unique:siswa,nis_nip',
            'nama_siswa' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'tempat' => 'required|string|max:10',
            'tanggal_lahir' => 'required|string|max:10',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'agama' => 'required|in:islam,protestan,katolik,hindu,buddha,khonghucu',
            'no_telepon' => 'required|string|max:20',
            'tahun_masuk' => 'required|string|max:20',
            'kode' => 'required|string|max:30',
        ]);

        $data = $request->only([
            'nis_nip',
            'nama_siswa',
            'email',
            'tempat',
            'tanggal_lahir',
            'jenis_kelamin',
            'agama',
            'no_telepon',
            'tahun_masuk',
            'kode',
        ]);

        Siswa::create($data);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }





    // Tampilkan detail siswa
    public function show(Siswa $siswa)
    {
        return view('siswa.show', compact('siswa'));
    }

    // Form edit
    public function edit(Siswa $siswa)
    {

        $Kelas = Kelas::all();


        return view('superadmin.siswa.edit', compact('siswa', 'Kelas'));
    }

    // Update data siswa
    public function update(Request $request, Siswa $siswa)
    {
        // $request->validate([
        //     'nama_siswa' => 'required|string|max:255',
        //     'alamat' => 'required|string',
        //     'nis' => 'required|string|unique:siswas,nis,' . $siswa->id,
        // ]);

        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate.');
    }

    // Hapus siswa
    public function destroy($id)
    {
        // 1. Cari siswa berdasarkan ID.
        $siswa = Siswa::find($id);

        // 2. Jika siswa tidak ditemukan, kembalikan ke halaman index dengan pesan error.
        if (!$siswa) {
            return redirect()->route('siswa.index')->with('error', 'Data siswa tidak ditemukan');
        }

        // 3. Gunakan forceDelete() untuk menghapus data secara permanen.
        // Metode ini akan mengabaikan fitur Soft Deletes.
        $siswa->forceDelete();

        // 4. Kembalikan ke halaman index dengan pesan sukses.
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus secara permanen');
    }
    // public function destroy($id)
    // {
    //     $siswa = Siswa::find($id);
    //     if (!$siswa) {
    //         return redirect()->route('siswa.index')->with('error', 'Data kelas tidak ditemukan');
    //     }

    //     $siswa->delete();

    //     return redirect()->route('siswa.index')->with('success', 'Data kelas berhasil dihapus');
    // }

    public function showKelasSiswa(Request $request)
    {
        // Set tingkat default ke 'X'
        $tingkat = $request->get('tingkat', 'X');

        $kelas = Kelas::with(['jurusan', 'kelasSiswa' => function ($q) {
            $q->where('is_active', 'aktif');
        }])
            ->where('tingkat', $tingkat)
            ->get();

        return view('superadmin.siswa.kelassiswa', compact('kelas', 'tingkat'));
    }

    public function showSiswa($id)
    {
        $kelas = Kelas::with(['siswa', 'jurusan'])->findOrFail($id);

        // Ambil siswa yang TIDAK ada di tabel kelas_siswa sama sekali
        $siswa = Siswa::whereNotIn('id', function ($query) {
            $query->select('id_siswa')->from('kelas_siswa');
        })->get();

        return view('superadmin.siswa.siswa', compact('kelas', 'siswa'));
    }

    public function storeSiswa(Request $request, $kelasId)
    {
        $request->validate([
            'status' => 'required|in:new,naik,tidak_naik,lulus',
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
        ]);

        $kelas = Kelas::findOrFail($kelasId);
        $periode = Periode::where('is_active', 'aktif')->first();

        if (!$periode) {
            return redirect()->back()->with('error', 'Periode aktif tidak ditemukan.');
        }

        foreach ($request->siswa_id as $siswaId) {
            KelasSiswa::updateOrCreate(
                [
                    'id_kelas' => $kelasId,
                    'id_siswa' => $siswaId,
                    'periode_id' => $periode->id, // hanya periode yang is_active = aktif
                ],
                [
                    'status' => $request->status,
                    'is_active' => 'aktif',
                ]
            );
        }

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }
}
