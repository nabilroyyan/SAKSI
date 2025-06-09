<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Periode;
use App\Models\KelasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;

class KelasController extends Controller
{
        public function index(Request $request)
    {
        $stt = $request->get('stt', 'aktif'); // default: aktif
        $tingkat = $request->get('tingkat'); // x, xi, xii

        $query = Kelas::with('jurusan')->where('stt', $stt);

        if ($tingkat) {
            $query->where('tingkat', strtoupper($tingkat)); // pastikan huruf kapital
        }

        $kelas = $query->get();
        $siswa = Siswa::whereNotIn('id', function ($query) {
            $query->select('id_siswa')->from('kelas_siswa');
        })->get();

        return view('superadmin.kelas.index', compact('kelas', 'siswa', 'stt', 'tingkat'));
    }

    
    public function create()
    {
        // Get necessary data for dropdowns
        $jurusan = Jurusan::all(); // Assuming you have a Jurusan model
        $users = User::all();

        // Tampilkan form untuk membuat data kelas baru
        return view('superadmin.kelas.create', compact('jurusan','users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|in:X,XI,XII',
            'id_jurusan' => 'required|exists:jurusan,id',
            'stt' => 'required|in:tidak_aktif,aktif',
        ]);

        Kelas::create([
            'tingkat' => $request->tingkat,
            'id_jurusan' => $request->id_jurusan,
            'id_users' => $request->id_users ?? null,
            'stt' => $request->stt,
        ]);

        return redirect('/kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = Kelas::with(['jurusan', 'siswa'])->find($id);
        $users = User::all();

        if (!$kelas) {
            return redirect()->route('kelas.index')->with('error', 'Data kelas tidak ditemukan');
        }


        return view('superadmin.kelas.edit', compact('kelas','users'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'id_users' => 'nullable|exists:users,id',
            'stt' => 'required|in:tidak_aktif,aktif'
        ]);

        // Ambil data kelas
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return redirect()->route('kelas.index')->with('error', 'Data kelas tidak ditemukan.');
        }

        // Update data
        $kelas->update([
            'id_users' => $request->id_users,
            'stt' => $request->stt
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }


   // KelasController.php

  public function naikkanBulkSiswa(Request $request)
{
    $request->validate([
        'id_kelas' => 'required_if:status,naik',
        'siswa_ids' => 'required',
        'kelas_siswa_ids' => 'required',
        'status' => 'required|in:naik,tidak_naik,lulus'
    ]);

    $siswaIds = explode(',', $request->siswa_ids);
    $kelasSiswaIds = explode(',', $request->kelas_siswa_ids);
    $idKelas = $request->id_kelas;
    $status = $request->status;

    $periode = Periode::where('is_active', true)->first();
    if (!$periode) {
        return redirect()->back()->with('error', 'Periode aktif tidak ditemukan.');
    }

    DB::beginTransaction();
    try {
        foreach ($siswaIds as $index => $siswaId) {
            if (!isset($kelasSiswaIds[$index])) continue;

            $kelasSiswa = KelasSiswa::findOrFail($kelasSiswaIds[$index]);
            $currentKelasId = $kelasSiswa->id_kelas;

            $kelasSiswa->update([
                'status' => $status,
                'is_active' => 'non_aktif'
            ]);

            if ($status === 'naik') {
                KelasSiswa::create([
                    'id_siswa' => $siswaId,
                    'id_kelas' => $idKelas,
                    'status' => 'naik',
                    'is_active' => 'aktif',
                    'periode_id' => $periode->id
                ]);
            } elseif ($status === 'tidak_naik') {
                KelasSiswa::create([
                    'id_siswa' => $siswaId,
                    'id_kelas' => $currentKelasId,
                    'status' => 'tidak_naik',
                    'is_active' => 'aktif',
                    'periode_id' => $periode->id
                ]);
            }
            // Tidak buat record baru kalau 'lulus'
        }

        DB::commit();
        return redirect()->back()->with('success', 'Status siswa berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in naikkanBulkSiswa: '.$e->getMessage());
        return redirect()->back()->with('error', 'Gagal memperbarui status siswa: '.$e->getMessage());
    }
}


    public function showSiswaByKelas($id_kelas)
{
    $kelas = Kelas::with('jurusan')->findOrFail($id_kelas);
    $allKelas = Kelas::with('jurusan')->where('id', '!=', $id_kelas)->get();

    $periode = Periode::where('is_active', true)->first();
    if (!$periode) {
        return redirect()->back()->with('error', 'Periode aktif tidak ditemukan.');
    }

    $siswaDiKelas = KelasSiswa::with(['siswa', 'kelas'])
        ->where('id_kelas', $id_kelas)
        ->where('periode_id', $periode->id)
        ->where('is_active', 'aktif')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('superadmin.kelas.detailSiswa', [
        'kelas' => $kelas,
        'siswaDiKelas' => $siswaDiKelas,
        'allKelas' => $allKelas
    ]);
}



    public function hapusSiswa($id)
    {
        $data = KelasSiswa::findOrFail($id);
        $id_kelas = $data->id_kelas;
        
        $data->delete();

        return redirect()->route('kelas.detailSiswa', $id_kelas)->with('success', 'Siswa berhasil dihapus dari kelas.');
    }


    public function destroy($id)
    {
        $kelas = Kelas::find($id);
        if (!$kelas) {
            return redirect()->route('kelas.index')->with('error', 'Data kelas tidak ditemukan');
        }

        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil dihapus');
    }

    
}