<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\TindakanSiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TindakanSiswaController extends Controller
{
    // Menampilkan daftar siswa yang perlu ditindak (skor ≥ 500)
    public function index()
    {
        $user = Auth::user();

        // Jika Superadmin, tampilkan semua siswa tindakan
       if ($user->hasRole('superadmin')) {
             $siswaTindakan = DB::table('pelanggaran')
                ->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id')
                ->join('kelas_siswa', 'pelanggaran.kelas_siswa_id', '=', 'kelas_siswa.id')
                ->join('kelas', 'kelas_siswa.id_kelas', '=', 'kelas.id')
                ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id')
                ->join('skor_pelanggaran', 'pelanggaran.id_skor_pelanggaran', '=', 'skor_pelanggaran.id')
                ->where('kelas_siswa.is_active', 'aktif') // ✅ penting
                ->select(
                    'siswa.id as siswa_id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id as kelas_siswa_id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat',
                    DB::raw('SUM(skor_pelanggaran.skor) as total_skor')
                )
                ->groupBy(
                    'siswa.id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat'
                )
                ->having('total_skor', '>=', 500)
                ->get();

        } else {
            // Jika user BK
            $kelasIds = DB::table('bk_kelas')
                ->where('id_bk', $user->id)
                ->pluck('id_kelas');

            $siswaTindakan = DB::table('pelanggaran')
                ->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id')
                ->join('kelas_siswa', 'pelanggaran.kelas_siswa_id', '=', 'kelas_siswa.id')
                ->join('kelas', 'kelas_siswa.id_kelas', '=', 'kelas.id')
                ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id')
                ->join('skor_pelanggaran', 'pelanggaran.id_skor_pelanggaran', '=', 'skor_pelanggaran.id')
                ->where('kelas_siswa.is_active', 'aktif')
                ->whereIn('kelas.id', $kelasIds)
                ->select(
                    'siswa.id as siswa_id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id as kelas_siswa_id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat',
                    DB::raw('SUM(skor_pelanggaran.skor) as total_skor')
                )
                ->groupBy(
                    'siswa.id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat'
                )
                ->having('total_skor', '>=', 500)
                ->get();
        }

        return view('superadmin.tindakan-siswa.index', compact('siswaTindakan'));
    }

    // Menampilkan form tambah tindakan terhadap siswa
    public function create($siswa_id, $kelas_siswa_id)
    {
         $siswa = DB::table('siswa')
        ->join('kelas_siswa', 'siswa.id', '=', 'kelas_siswa.id_siswa')
        ->join('kelas', 'kelas_siswa.id_kelas', '=', 'kelas.id')
        ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id')
        ->where('siswa.id', $siswa_id)
        ->where('kelas_siswa.id', $kelas_siswa_id)
        ->select(
            'siswa.*',
            'kelas_siswa.id as kelas_siswa_id',
            'kelas.tingkat',
            'jurusan.nama_jurusan'
        )
        ->first();

        return view('superadmin.tindakan-siswa.create', compact('siswa', 'kelas_siswa_id'));
    }

    // Simpan tindakan
    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'kelas_siswa_id' => 'required|exists:kelas_siswa,id',
            'id_tindakan' => 'required|exists:kategori_tindakan,id',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        TindakanSiswa::create([
            'id_siswa' => $request->id_siswa,
            'kelas_siswa_id' => $request->kelas_siswa_id, 
            'id_tindakan' => $request->id_tindakan,
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
            'status' => 'belum',
        ]);

        return redirect()->route('tindakan-siswa.index')->with('success', 'Tindakan berhasil disimpan.');
    }

    // (opsional) Tandai tindakan sudah dilakukan
    public function updateStatus($id)
    {
        $tindakan = TindakanSiswa::findOrFail($id);
        $tindakan->update(['status' => 'sudah']);
        return back()->with('success', 'Status tindakan diperbarui.');
    }
}
