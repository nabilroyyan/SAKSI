<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WaliKelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil kelas dengan relasi yang lebih lengkap
        $kelas = Kelas::with([
            'jurusan', 
            'kelasSiswa' => function($query) {
                $query->where('is_active', 'aktif');
            },
            'kelasSiswa.siswa' => function ($query) {
                $query->orderBy('nama_siswa', 'asc');
            },
            'kelasSiswa.periode'
        ])->where('id_wakel', $user->id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Tidak ada kelas yang diampu.');
        }

        // Statistik siswa
        $totalSiswa = $kelas->kelasSiswa->count();
        $siswaAktif = $kelas->kelasSiswa->where('is_active', 'aktif')->count();

        // Statistik absensi hari ini
        $today = Carbon::today();
        $absensiHariIni = collect();
        $statistikAbsensi = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'belum_absen' => 0
        ];

        foreach ($kelas->kelasSiswa as $kelasSiswa) {
            $siswa = $kelasSiswa->siswa;
            $absensi = $siswa->absensis()->whereDate('hari_tanggal', $today)->first();
            
            if ($absensi) {
                $statistikAbsensi[$absensi->status]++;
            } else {
                $statistikAbsensi['belum_absen']++;
            }
        }

        return view('superadmin.walikelas.index', compact(
            'kelas', 
            'totalSiswa', 
            'siswaAktif', 
            'statistikAbsensi'
        ));
    }

    public function detail(Request $request)
    {
        $user = Auth::user();
        $kelas = Kelas::with(['jurusan', 'kelasSiswa.siswa' => function ($query) {
                $query->orderBy('nama_siswa', 'asc');
            }])
            ->where('id_wakel', $user->id)
            ->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda belum menjadi wali kelas dari kelas manapun.');
        }

        // Filter berdasarkan parameter
        $filterBulan = $request->get('bulan', date('m'));
        $filterTahun = $request->get('tahun', date('Y'));
        $search = $request->get('search');

        // Ambil siswa aktif di kelas ini
        $query = KelasSiswa::with(['siswa', 'periode'])
            ->where('id_kelas', $kelas->id)
            ->where('is_active', 'aktif');

        if ($search) {
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nis_nip', 'like', '%' . $search . '%');
            });
        }

        $siswaKelas = $query->get();

        // Ambil data lengkap per siswa
        $dataSiswa = $siswaKelas->map(function ($item) use ($filterBulan, $filterTahun) {
            $siswa = $item->siswa;
            
            // Statistik absensi bulan ini
            $absensiFilter = $siswa->absensis()
                ->whereMonth('hari_tanggal', $filterBulan)
                ->whereYear('hari_tanggal', $filterTahun);
            
            $statistikAbsensi = [
                'hadir' => $absensiFilter->clone()->where('status', 'hadir')->count(),
                'sakit' => $absensiFilter->clone()->where('status', 'sakit')->count(),
                'izin' => $absensiFilter->clone()->where('status', 'izin')->count(),
                'alpa' => $absensiFilter->clone()->where('status', 'alpa')->count(),
            ];

            // Absensi terakhir
            $absensiTerakhir = $siswa->absensis()->latest('hari_tanggal')->first();
            
            // Total pelanggaran
            $totalPelanggaran = $siswa->pelanggarans()->count();
            $totalSkorPelanggaran = $siswa->pelanggarans()
                ->join('skor_pelanggaran', 'pelanggaran.id', '=', 'skor_pelanggaran.id')
                ->sum('skor_pelanggaran.skor');

            // Status kehadiran (persentase kehadiran)
            $totalAbsensi = array_sum($statistikAbsensi);
            $persentaseKehadiran = $totalAbsensi > 0 ? 
                round(($statistikAbsensi['hadir'] / $totalAbsensi) * 100, 1) : 0;

            return [
                'siswa' => $siswa,
                'periode' => $item->periode,
                'absensi_terakhir' => $absensiTerakhir,
                'statistik_absensi' => $statistikAbsensi,
                'total_pelanggaran' => $totalPelanggaran,
                'total_skor_pelanggaran' => $totalSkorPelanggaran,
                'persentase_kehadiran' => $persentaseKehadiran,
                'status_kehadiran' => $this->getStatusKehadiran($persentaseKehadiran)
            ];
        });

        return view('superadmin.walikelas.detail', [
            'kelas' => $kelas,
            'dataSiswa' => $dataSiswa,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
            'search' => $search,
        ]);
    }

    public function getDetailAbsensi($id)
    {
        try {
            $siswa = Siswa::with([
                'absensis' => function($query) {
                    $query->with(['petugas'])
                          ->orderBy('hari_tanggal', 'desc')
                          ->limit(30); // 30 hari terakhir
                }
            ])->findOrFail($id);

            $data = $siswa->absensis->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->hari_tanggal)->format('d/m/Y'),
                    'hari' => Carbon::parse($item->hari_tanggal)->locale('id')->dayName,
                    'status' => $item->status,
                    'status_surat' => $item->status_surat,
                    'catatan' => $item->catatan ?? '-',
                    'petugas' => $item->petugas->name ?? '-',
                    'bukti' => $item->foto_surat ? asset('storage/' . $item->foto_surat) : null,
                    'waktu_input' => $item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-'
                ];
            });

            // Statistik keseluruhan
            $totalAbsen = $siswa->absensis->count();
            $statistik = [
                'hadir' => $siswa->absensis->where('status', 'hadir')->count(),
                'sakit' => $siswa->absensis->where('status', 'sakit')->count(),
                'izin' => $siswa->absensis->where('status', 'izin')->count(),
                'alpa' => $siswa->absensis->where('status', 'alpa')->count(),
            ];

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama_siswa,
                'nis' => $siswa->nis_nip,
                'total_absen' => $totalAbsen,
                'statistik' => $statistik,
                'persentase_kehadiran' => $totalAbsen > 0 ? 
                    round(($statistik['hadir'] / $totalAbsen) * 100, 1) : 0,
                'detail_absensi' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting absensi details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data absensi'
            ], 500);
        }
    }

    public function getDetailPelanggaran($id)
    {
        try {
            $siswa = Siswa::with([
                'pelanggarans' => function($q) {
                    $q->with(['skor_pelanggaran', 'petugas'])
                      ->whereHas('kelasSiswa', function($subQ) {
                          $subQ->where('is_active', 'aktif');
                      })
                      ->orderBy('tanggal', 'desc')
                      ->limit(20); // 20 pelanggaran terakhir
                }
            ])->findOrFail($id);

            $data = $siswa->pelanggarans->map(function ($p) {
                return [
                    'tanggal' => Carbon::parse($p->tanggal)->format('d/m/Y'),
                    'hari' => Carbon::parse($p->tanggal)->locale('id')->dayName,
                    'nama_pelanggaran' => $p->skor_pelanggaran->nama_pelanggaran ?? '-',
                    'kategori' => $p->skor_pelanggaran->kategori ?? '-',
                    'skor' => $p->skor_pelanggaran->skor ?? 0,
                    'petugas' => $p->petugas->name ?? '-',
                    'keterangan' => $p->keterangan ?? '-',
                    'bukti' => $p->bukti_pelanggaran ? asset('storage/' . $p->bukti_pelanggaran) : null,
                    'waktu_input' => $p->created_at ? Carbon::parse($p->created_at)->format('H:i') : '-'
                ];
            });

            $totalSkor = $siswa->pelanggarans->sum(function($p) {
                return $p->skor_pelanggaran->skor ?? 0;
            });

            // Kategorisasi tingkat pelanggaran
            $tingkatPelanggaran = $this->getTingkatPelanggaran($totalSkor);

            return response()->json([
                'success' => true,
                'nama' => $siswa->nama_siswa,
                'nis' => $siswa->nis_nip,
                'total_skor' => $totalSkor,
                'total_pelanggaran' => $siswa->pelanggarans->count(),
                'tingkat_pelanggaran' => $tingkatPelanggaran,
                'pelanggarans' => $data,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting violation details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pelanggaran'
            ], 500);
        }
    }

    private function getStatusKehadiran($persentase)
    {
        if ($persentase >= 95) return ['status' => 'Sangat Baik', 'class' => 'success'];
        if ($persentase >= 85) return ['status' => 'Baik', 'class' => 'info'];
        if ($persentase >= 75) return ['status' => 'Cukup', 'class' => 'warning'];
        return ['status' => 'Kurang', 'class' => 'danger'];
    }

    private function getTingkatPelanggaran($totalSkor)
    {
        if ($totalSkor == 0) return ['tingkat' => 'Tidak Ada', 'class' => 'success'];
        if ($totalSkor <= 25) return ['tingkat' => 'Ringan', 'class' => 'info'];
        if ($totalSkor <= 50) return ['tingkat' => 'Sedang', 'class' => 'warning'];
        if ($totalSkor <= 100) return ['tingkat' => 'Berat', 'class' => 'danger'];
        return ['tingkat' => 'Sangat Berat', 'class' => 'dark'];
    }
}