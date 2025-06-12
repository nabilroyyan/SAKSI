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
use Illuminate\Support\Str;
use PDF;

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

                // Ambil semua data terkait siswa
                $absensi = Absensi::where('kelas_siswa_id', $item->id)
                            ->where('id_siswa', $siswa->id)
                            ->orderBy('hari_tanggal', 'asc')
                            ->get();

                // Ambil pelanggaran beserta relasi skor_pelanggaran
                $pelanggaran = Pelanggaran::with('skor_pelanggaran')
                    ->where('kelas_siswa_id', $item->id)
                    ->where('id_siswa', $siswa->id)
                    ->get();

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

        public function cetakPdfSiswa($kelas_id, $siswa_id, $periode_id)
    {
        $kelas = Kelas::with('jurusan')->findOrFail($kelas_id);
        $siswa = Siswa::findOrFail($siswa_id);
        $periode = Periode::findOrFail($periode_id);

        // Ambil data kelas_siswa
        $kelasSiswa = KelasSiswa::where('id_kelas', $kelas_id)
                            ->where('id_siswa', $siswa_id)
                            ->where('periode_id', $periode_id)
                            ->first();

        if (!$kelasSiswa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Ambil semua data terkait siswa
        $absensi = Absensi::where('kelas_siswa_id', $kelasSiswa->id)
                        ->where('id_siswa', $siswa_id)
                        ->orderBy('hari_tanggal', 'asc')
                        ->get();

        $pelanggaran = Pelanggaran::where('kelas_siswa_id', $kelasSiswa->id)
                                ->where('id_siswa', $siswa_id)
                                ->orderBy('tanggal', 'asc')
                                ->get();

        $tindakan = TindakanSiswa::where('kelas_siswa_id', $kelasSiswa->id)
                            ->where('id_siswa', $siswa_id)
                            ->orderBy('tanggal', 'asc')
                            ->get();

        // Hitung statistik
        $statistik = [
            'total_absensi' => $absensi->count(),
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'alpha' => $absensi->where('status', 'alpha')->count(),
            'total_pelanggaran' => $pelanggaran->count(),
            'total_poin_pelanggaran' => $pelanggaran->sum('poin'),
            'total_tindakan' => $tindakan->count(),
        ];

        $data = [
            'kelas' => $kelas,
            'siswa' => $siswa,
            'periode' => $periode,
            'kelasSiswa' => $kelasSiswa,
            'absensi' => $absensi,
            'pelanggaran' => $pelanggaran,
            'tindakan' => $tindakan,
            'statistik' => $statistik,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('superadmin.riwayat.riwayatSiswa', $data);
        $pdf->setPaper('A4', 'portrait');
        
       $filename = 'Riwayat_' 
        . Str::slug($siswa->nama, '_') . '_' 
        . Str::slug($kelas->tingkat, '_') . '_' 
        . Str::slug($periode->semester, '_') . '_' 
        . Str::slug($periode->tahun, '_') . '.pdf';
        
        $cleanFilename = preg_replace('/[\/\\\\]/', '_', $filename);
        return $pdf->download($cleanFilename);
    }

    public function cetakPdfKelas($kelas_id, $periode_id = null)
    {
        $kelas = Kelas::with('jurusan')->findOrFail($kelas_id);
        $periode = $periode_id ? Periode::findOrFail($periode_id) : null;

        $query = KelasSiswa::with(['siswa', 'periode'])
            ->where('id_kelas', $kelas_id)
            ->where('is_active', 'non_aktif')
            ->orderBy('created_at', 'desc');

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        $riwayatSiswa = $query->get();

        // Ambil data detail untuk setiap siswa
        $dataDetail = [];
        foreach ($riwayatSiswa as $item) {
            $siswa = $item->siswa;

            $absensi = Absensi::where('kelas_siswa_id', $item->id)
                            ->where('id_siswa', $siswa->id)
                            ->get();

            $pelanggaran = Pelanggaran::where('kelas_siswa_id', $item->id)
                                    ->where('id_siswa', $siswa->id)
                                    ->get();

            $tindakan = TindakanSiswa::where('kelas_siswa_id', $item->id)
                                ->where('id_siswa', $siswa->id)
                                ->get();

            $dataDetail[] = [
                'siswa' => $siswa,
                'status' => $item->status,
                'periode' => $item->periode,
                'absensi_count' => $absensi->count(),
                'pelanggaran_count' => $pelanggaran->count(),
                'tindakan_count' => $tindakan->count(),
                'total_poin' => $pelanggaran->sum('poin'),
            ];
        }

        $data = [
            'kelas' => $kelas,
            'periode' => $periode,
            'dataDetail' => $dataDetail,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('superadmin.riwayat.pdf-kelas', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $periode_text = $periode ? '_' . $periode->semester . $periode->tahun : '_Semua_Periode';
        $filename = 'Riwayat_' 
        . Str::slug($siswa->nama, '_') . '_' 
        . Str::slug($kelas->tingkat, '_') . '_' 
        . Str::slug($periode->semester, '_') 
        . $periode->tahun . '.pdf';;
        
        return $pdf->download($filename);
    }
}