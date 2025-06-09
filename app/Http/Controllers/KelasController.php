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
            'siswa_ids' => 'required',
            'kelas_siswa_ids' => 'required',
            'status' => 'required|in:naik,tidak_naik,lulus',
            'periode_id' => 'required|exists:periode,id' // Diperbaiki: exists validation
        ]);

        $siswaIds = explode(',', $request->siswa_ids);
        $kelasSiswaIds = explode(',', $request->kelas_siswa_ids);
        $status = $request->status;

        // Ambil periode berdasarkan ID yang dipilih
        $periode = Periode::findOrFail($request->periode_id);

        // Pastikan periode yang dipilih aktif
        if ($periode->is_active !== 'aktif') {
            return redirect()->back()->with('error', 'Periode yang dipilih tidak aktif.');
        }

        // Urutan tingkat enum
        $tingkatUrutan = ['X', 'XI', 'XII']; // Sesuai dengan enum database

        DB::beginTransaction();
        try {
            foreach ($siswaIds as $index => $siswaId) {
                if (!isset($kelasSiswaIds[$index])) continue;

                $kelasSiswa = KelasSiswa::findOrFail($kelasSiswaIds[$index]);
                $kelas = Kelas::findOrFail($kelasSiswa->id_kelas);

                // Nonaktifkan entri lama
                $kelasSiswa->update([
                    'status' => $status,
                    'is_active' => 'non_aktif'
                ]);

                if ($status === 'naik') {
                    $currentTingkat = $kelas->tingkat;
                    $currentIndex = array_search($currentTingkat, $tingkatUrutan);
                    
                    // Validasi tingkat saat ini
                    if ($currentIndex === false) {
                        throw new \Exception("Tingkat kelas '$currentTingkat' tidak valid.");
                    }
                    
                    $nextIndex = $currentIndex + 1;

                    // Cek apakah ada tingkat selanjutnya
                    if ($nextIndex >= count($tingkatUrutan)) {
                        // Sudah di tingkat terakhir (XII), tidak bisa naik lagi
                        // Buat entry baru dengan status lulus
                        KelasSiswa::create([
                            'id_siswa' => $siswaId,
                            'id_kelas' => $kelas->id,
                            'status' => 'lulus',
                            'is_active' => 'aktif',
                            'periode_id' => $periode->id
                        ]);
                        continue;
                    }

                    $nextTingkat = $tingkatUrutan[$nextIndex];

                    // Cari kelas dengan tingkat selanjutnya dan jurusan yang sama
                    $kelasBaru = Kelas::where('tingkat', $nextTingkat)
                        ->where('id_jurusan', $kelas->id_jurusan)
                        ->first();

                    if (!$kelasBaru) {
                        throw new \Exception("Kelas tingkat '$nextTingkat' untuk jurusan {$kelas->jurusan->nama_jurusan} tidak ditemukan.");
                    }

                    // Tambahkan ke kelas baru
                    KelasSiswa::create([
                        'id_siswa' => $siswaId,
                        'id_kelas' => $kelasBaru->id,
                        'status' => 'naik',
                        'is_active' => 'aktif',
                        'periode_id' => $periode->id
                    ]);
                    
                } elseif ($status === 'tidak_naik') {
                    // Tetap di kelas sekarang dengan status tidak naik
                    KelasSiswa::create([
                        'id_siswa' => $siswaId,
                        'id_kelas' => $kelas->id,
                        'status' => 'tidak_naik',
                        'is_active' => 'aktif',
                        'periode_id' => $periode->id
                    ]);
                    
                } elseif ($status === 'lulus') {
                    // Hanya bisa meluluskan jika tingkat kelas adalah XII
                    if ($kelas->tingkat !== 'XII') {
                        throw new \Exception("Siswa hanya bisa diluluskan jika berada di tingkat XII.");
                    }
                    // Jika lulus, cukup update is_active menjadi non_aktif pada entri lama
                    $kelasSiswa->update([
                        'status' => 'lulus',
                        'is_active' => 'non_aktif'
                    ]);
                }
            }

            DB::commit();
            
            // Pesan sukses yang lebih informatif
            $message = match($status) {
                'naik' => 'Siswa berhasil dinaikkan ke kelas berikutnya.',
                'tidak_naik' => 'Status siswa berhasil diubah menjadi tidak naik kelas.',
                'lulus' => 'Siswa berhasil dinyatakan lulus.',
                default => 'Status siswa berhasil diperbarui.'
            };
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in naikkanBulkSiswa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui status siswa: ' . $e->getMessage());
        }
    }

    public function bulkPeriode(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required',
            'kelas_siswa_ids' => 'required',
            'id_kelas' => 'required|integer',
        ]);

        $siswaIds = explode(',', $request->siswa_ids);
        $kelasSiswaIds = explode(',', $request->kelas_siswa_ids);
        $idKelas = $request->id_kelas;

        $periode = Periode::where('is_active', 'aktif')->first();
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode aktif tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            foreach ($siswaIds as $index => $siswaId) {
                if (!isset($kelasSiswaIds[$index])) continue;

                // Cek apakah sudah ada di periode yang sama dan aktif
                $sudahAda = KelasSiswa::where('id_siswa', $siswaId)
                    ->where('periode_id', $periode->id)
                    ->where('is_active', 'aktif')
                    ->exists();

                if ($sudahAda) {
                    DB::rollBack(); // batalkan semua proses
                    return redirect()->back()->with('error', 'Beberapa siswa sudah ada di periode aktif.');
                }

                // Nonaktifkan data lama
                $kelasSiswa = KelasSiswa::findOrFail($kelasSiswaIds[$index]);
                $kelasSiswa->update([
                    'is_active' => 'non_aktif'
                ]);

                // Tambahkan data baru dengan periode aktif
                KelasSiswa::create([
                    'id_siswa' => $siswaId,
                    'id_kelas' => $idKelas,
                    'is_active' => 'aktif',
                    'periode_id' => $periode->id
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Periode siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulkPeriode: '.$e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui periode siswa: '.$e->getMessage());
        }
    }



    public function showSiswaByKelas($id_kelas)
    {
        $kelas = Kelas::with('jurusan')->findOrFail($id_kelas);
        $allKelas = Kelas::with('jurusan')->where('id', '!=', $id_kelas)->get();

        $periodes = Periode::all();
        if ($periodes->isEmpty()) {
            return redirect()->back()->with('error', 'Data periode tidak ditemukan.');
        }

        // Ambil semua siswa di kelas ini untuk semua periode
        $siswaDiKelas = KelasSiswa::with(['siswa', 'kelas', 'periode'])
            ->where('id_kelas', $id_kelas)
            ->where('is_active', 'aktif')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.kelas.detailSiswa', [
            'kelas' => $kelas,
            'siswaDiKelas' => $siswaDiKelas,
            'allKelas' => $allKelas,
            'periodes' => $periodes
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