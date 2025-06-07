<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
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
        'tahun_ajaran' => 'required',
        'id_kelas' => 'required_if:status,naik',
        'siswa_ids' => 'required',
        'kelas_siswa_ids' => 'required',
        'status' => 'required|in:naik,tidak_naik,lulus'
    ]);

    $siswaIds = explode(',', $request->siswa_ids);
    $kelasSiswaIds = explode(',', $request->kelas_siswa_ids);
    $tahunAjaran = $request->tahun_ajaran;
    $idKelas = $request->id_kelas;
    $status = $request->status;

    DB::beginTransaction();
    try {
        foreach ($siswaIds as $index => $siswaId) {
            // Periksa validitas index
            if (!isset($kelasSiswaIds[$index])) {
                continue;
            }

            // Mendapatkan kelas siswa saat ini
            $kelasSiswa = KelasSiswa::findOrFail($kelasSiswaIds[$index]);
            $currentKelasId = $kelasSiswa->id_kelas;

            // Nonaktifkan record lama
            $kelasSiswa->update([
                'status' => $status,
                'is_active' => 'non_aktif'  // Menggunakan nilai integer untuk is_active
            ]);

            // Buat record baru berdasarkan status
            if ($status === 'naik') {
                // Siswa naik kelas - gunakan kelas tujuan baru
                KelasSiswa::create([
                    'id_siswa' => $siswaId,
                    'id_kelas' => $idKelas,
                    'tahun_ajaran' => $tahunAjaran,
                    'status' => 'naik',
                    'is_active' => 'aktif'
                ]);
            } elseif ($status === 'tidak_naik') {
                // Siswa tidak naik kelas - gunakan kelas yang sama
                KelasSiswa::create([
                    'id_siswa' => $siswaId,
                    'id_kelas' => $currentKelasId,  // Menggunakan kelas yang sama
                    'tahun_ajaran' => $tahunAjaran,
                    'status' => 'tidak_naik',
                    'is_active' => 'aktif'
                ]);
            }
            // Untuk status 'lulus', tidak perlu membuat record baru

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
        // Ambil data kelas
        $kelas = Kelas::with('jurusan')->findOrFail($id_kelas);
        
        // Ambil semua kelas kecuali kelas saat ini
        $allKelas = Kelas::with('jurusan')
                    ->where('id', '!=', $id_kelas)
                    ->get();
        
        // Ambil relasi siswa melalui tabel pivot
        $siswaDiKelas = KelasSiswa::with(['siswa', 'kelas'])
            ->where('id_kelas', $id_kelas)
            ->where('is_active', 1)
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