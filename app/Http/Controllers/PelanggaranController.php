<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\skor_pelanggaran;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PelanggaranController extends Controller
{
    public function index()
    {
        $hari_ini = now()->toDateString();
        $pelanggaran = Pelanggaran::with(['siswa', 'kelasSiswa.kelas', 'petugas', 'skor_pelanggaran'])
                        ->whereDate('tanggal', $hari_ini)
                        ->latest()
                        ->get();

        return view('superadmin.pelanggaran.index', compact('pelanggaran'));
    }

    public function create()
    {
        $siswa = Siswa::has('kelasAktif')->get();
        $skor = skor_pelanggaran::all();
        $pelanggaran = Pelanggaran::with(['kelasSiswa.kelas'])
                        ->latest()
                        ->get();
        return view('superadmin.pelanggaran.create', compact('siswa', 'skor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'ket_pelanggaran' => 'required',
            'tanggal' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value > now()->toDateString()) {
                        $fail('Tanggal pelanggaran tidak boleh di masa depan.');
                    }
                }
            ],
            'id_skor_pelanggaran' => 'required|exists:skor_pelanggaran,id',
            'bukti_pelanggaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $kelasSiswa = KelasSiswa::where('id_siswa', $request->id_siswa)
                        ->where('is_active', 'aktif')
                        ->firstOrFail();

        // Ambil data request kecuali file bukti_pelanggaran
        $data = $request->except('bukti_pelanggaran');

        // Tambahkan foreign key dan user id
        $data['kelas_siswa_id'] = $kelasSiswa->id;
        $data['id_users'] = Auth::id();

        // Simpan file jika ada
        if ($request->hasFile('bukti_pelanggaran')) {
            $data['bukti_pelanggaran'] = $request->file('bukti_pelanggaran')->store('bukti_pelanggaran', 'public');
        }

        Pelanggaran::create($data);

        return redirect()->route('pelanggaran.index')->with('success', 'Data pelanggaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        $siswas = Siswa::has('kelasAktif')->get();
        $users = User::all();
        $skors = skor_pelanggaran::all();

        return view('superadmin.pelanggaran.edit', compact('pelanggaran', 'siswas', 'users', 'skors'));
    }

        public function update(Request $request, Pelanggaran $pelanggaran)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'ket_pelanggaran' => 'required',
            'tanggal' => 'required|date',
            'id_skor_pelanggaran' => 'required|exists:skor_pelanggaran,id',
            'bukti_pelanggaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Ambil kelas aktif
        $kelasSiswa = KelasSiswa::where('id_siswa', $request->id_siswa)
                        ->where('is_active', 'aktif')
                        ->firstOrFail();

        // Gabungkan semua data form (kecuali file)
        $data = $request->except('bukti_pelanggaran');
        $data['kelas_siswa_id'] = $kelasSiswa->id;

        // Simpan file baru jika ada
        if ($request->hasFile('bukti_pelanggaran')) {
            if ($pelanggaran->bukti_pelanggaran) {
                Storage::disk('public')->delete($pelanggaran->bukti_pelanggaran);
            }
            $data['bukti_pelanggaran'] = $request->file('bukti_pelanggaran')->store('bukti_pelanggaran', 'public');
        }


        $pelanggaran->update($data);

        return redirect()->route('pelanggaran.index')->with('success', 'Data pelanggaran berhasil diperbarui');
    }


    public function destroy(Pelanggaran $pelanggaran)
    {
        if ($pelanggaran->bukti_pelanggaran) {
            Storage::disk('public')->delete($pelanggaran->bukti_pelanggaran);
        }
        
        $pelanggaran->delete();
        
        return redirect()->route('pelanggaran.index')->with('success', 'Data pelanggaran berhasil dihapus');
    }

    // Untuk mendapatkan data kelas siswa aktif
    public function getKelasSiswa($id_siswa)
    {
        $kelasSiswa = KelasSiswa::with(['kelas.jurusan'])
            ->where('id_siswa', $id_siswa)
            ->where('is_active', 'aktif')
            ->first();

        return response()->json($kelasSiswa);

    }
}
    