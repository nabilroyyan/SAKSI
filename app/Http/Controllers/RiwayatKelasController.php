<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Periode;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use App\Models\Skor_Pelanggaran;
use Illuminate\Http\Request;
use App\Models\TindakanSiswa;

class RiwayatKelasController extends Controller
{
    public function index(Request $request)
    {
        $stt = $request->get('stt', 'aktif');
        $tingkat = $request->get('tingkat');

        $query = Kelas::with('jurusan')->where('stt', $stt);

        if ($tingkat) {
            $query->where('tingkat', strtoupper($tingkat));
        }

        $kelas = $query->get();
        $siswa = Siswa::whereNotIn('id', function ($q) {
            $q->select('id_siswa')->from('kelas_siswa');
        })->get();

        return view('superadmin.riwayat.index', compact('kelas', 'siswa', 'stt', 'tingkat'));
    }

    public function showKelasDetail(Request $request, $id_kelas)
    {
        $kelas = Kelas::with('jurusan')->findOrFail($id_kelas);
        $periodes = Periode::all();

        $periode_id = $request->get('periode_id');

        $query = KelasSiswa::with(['siswa', 'periode'])
            ->where('id_kelas', $id_kelas)
            ->whereIn('is_active', ['aktif', 'non_aktif'])
            ->orderBy('created_at', 'desc');

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        $riwayatSiswa = $query->get();

        // Ambil data absensi, pelanggaran, dan tindakan tiap siswa dalam periode
        $dataDetail = [];

        foreach ($riwayatSiswa as $item) {
            $siswa = $item->siswa;

            $absensi = Absensi::where('id_siswa', $siswa->id)
                ->whereHas('kelasSiswa', function ($q) use ($periode_id, $id_kelas) {
                    $q->where('periode_id', $periode_id)->where('id_kelas', $id_kelas);
                })
                ->get();

            // Ambil pelanggaran beserta relasi skor_pelanggaran
            $pelanggaran = Pelanggaran::with('skor_pelanggaran')
                ->where('kelas_siswa_id', $item->id)
                ->where('id_siswa', $siswa->id)
                ->get();;

            $tindakan = TindakanSiswa::with('kategoriTindakan')
                        ->where('kelas_siswa_id', $item->id)
                        ->where('id_siswa', $siswa->id)
                        ->get();

            $dataDetail[] = [
                'siswa' => $siswa,
                'status' => $item->status,
                'periode' => $item->periode,
                'absensi' => $absensi,
                'pelanggaran' => $pelanggaran,
                'tindakan' => $tindakan,
            ];
        }

        return view('superadmin.riwayat.detailRiwayat', [
            'kelas' => $kelas,
            'periodes' => $periodes,
            'periode_id' => $periode_id,
            'dataDetail' => $dataDetail
        ]);
    }
}