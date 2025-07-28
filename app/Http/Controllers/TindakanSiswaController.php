<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\TindakanSiswa;
use App\Models\PengaturanTindakan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TindakanSiswaController extends Controller
{

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $pengaturan = PengaturanTindakan::first();
        $batasSkor = $pengaturan->batas_skor ?? null;

        if ($user->hasRole('superadmin')) {
            $siswaTindakan = DB::table('pelanggaran')
                ->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id')
                ->join('kelas_siswa', 'pelanggaran.kelas_siswa_id', '=', 'kelas_siswa.id')
                ->join('kelas', 'kelas_siswa.id_kelas', '=', 'kelas.id')
                ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id')
                ->join('skor_pelanggaran', 'pelanggaran.id_skor_pelanggaran', '=', 'skor_pelanggaran.id')
                ->select(
                    'siswa.id as siswa_id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id as kelas_siswa_id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat',
                    'kelas.nama_kelas',
                    DB::raw('SUM(skor_pelanggaran.skor) as total_skor')
                )
                ->groupBy(
                    'siswa.id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat',
                    'kelas.nama_kelas'
                )
                ->having('total_skor', '>=', $batasSkor)
                ->get();

        } else {
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
                    'kelas.nama_kelas',
                    DB::raw('SUM(skor_pelanggaran.skor) as total_skor')
                )
                ->groupBy(
                    'siswa.id',
                    'siswa.nama_siswa',
                    'siswa.nis_nip',
                    'kelas_siswa.id',
                    'jurusan.nama_jurusan',
                    'kelas.tingkat',
                    'kelas.nama_kelas'
                )
                ->having('total_skor', '>=', $batasSkor)
                ->get();
        }

        // Tambahkan properti apakah siswa ini memiliki tindakan yang belum ditindak
        foreach ($siswaTindakan as $siswa) {
            $siswa->ada_tindakan_belum = TindakanSiswa::where('id_siswa', $siswa->siswa_id)
                ->where('kelas_siswa_id', $siswa->kelas_siswa_id)
                ->where('status', 'belum')
                ->exists();
        }


        return view('superadmin.tindakan-siswa.index', compact('siswaTindakan','pengaturan'));
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
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'bukti_tindakan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $tindakan = TindakanSiswa::findOrFail($id);

        // Upload foto bukti
        if ($request->hasFile('bukti_tindakan')) {
            $file = $request->file('bukti_tindakan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/bukti_tindakan'), $filename);

            $tindakan->bukti_tindakan = $filename;
            $tindakan->status = 'sudah'; // Set status otomatis jadi selesai
            $tindakan->save();

            return redirect()->back()->with('success', 'Bukti berhasil diunggah dan status otomatis menjadi selesai.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti.');
    }

    // Hapus tindakan siswa
    public function destroy($id)
    {
        $tindakan = TindakanSiswa::findOrFail($id);
        $tindakan->delete();

        return back()->with('success', 'Tindakan berhasil dihapus.');
    }

        public function edit()
    {
        $pengaturan = PengaturanTindakan::first();
        return view('superadmin.pengaturan.tindakan', compact('pengaturan'));
    }

        public function update(Request $request)
    {
        $request->validate([
            'batas_skor' => 'required|integer|min:0'
        ]);

        $pengaturan = PengaturanTindakan::first();
        if (!$pengaturan) {
            $pengaturan = new PengaturanTindakan();
        }
        $pengaturan->batas_skor = $request->batas_skor;
        $pengaturan->save();

        return redirect()->back()->with('success', 'Batas skor berhasil diperbarui.');
    }

}
