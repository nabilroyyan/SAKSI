<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Jurusan;
use App\Models\Periode;
use Illuminate\Http\Request;

class MonitoringAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Query absensi dengan relasi
        $query = Absensi::with([
            'siswa',
            'kelasSiswa.kelas.jurusan',
            'petugas'
        ])->whereHas('kelasSiswa', function ($q) {
            $q->where('is_active', 'aktif');
        });

        $this->applyFilters($query, $request);

        // Ambil semua data absensi setelah filter
        $absensiList = $query->orderBy('hari_tanggal', 'desc')->get();

        // Group data berdasarkan siswa
        $siswaAbsensi = $absensiList->groupBy('id_siswa')->map(function ($items) {
            $first = $items->first();
            return [
                'siswa' => $first->siswa,
                'kelas_siswa' => $first->kelasSiswa,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
                'absensi' => $items,
            ];
        })->sortByDesc('alpa');

        $activePeriode = Periode::where('is_active', 'aktif')->first();

        $selectedPeriodeId = $request->filled('periode_id')
            ? $request->periode_id
            : ($activePeriode ? $activePeriode->id : null); 

        // Data tambahan untuk filter di view
        $siswaList = Siswa::all();
        $kelasList = Kelas::with('jurusan')->get();
        $jurusanList = Jurusan::all();
        $tingkatList = Kelas::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        $periodes = Periode::orderByDesc('tahun')->get();
        $statusList = Absensi::select('status')->distinct()->orderBy('status')->pluck('status');

        return view('superadmin.monitoring-absensi.index', compact(
            'siswaAbsensi',
            'siswaList',
            'kelasList',
            'jurusanList',
            'tingkatList',
            'periodes',
            'selectedPeriodeId',
            'statusList',
        ));
    }

    private function applyFilters($query, $request)
    {
        // Filter berdasarkan nama siswa
        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        // Filter berdasarkan nama kelas
        if ($request->filled('nama_kelas')) {
            $query->whereHas('kelasSiswa.kelas', function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->nama_kelas . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tingkat
        if ($request->filled('tingkat')) {
            $query->whereHas('kelasSiswa.kelas', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        // Filter berdasarkan tanggal - perbaiki field name
        if ($request->filled('tanggal')) {
            $query->whereDate('hari_tanggal', Carbon::parse($request->tanggal));
        }

        // Filter berdasarkan bulan - perbaiki field name
        if ($request->filled('bulan')) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun - perbaiki field name
        if ($request->filled('tahun')) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }

        if ($request->filled('periode_id')) {
            $query->whereHas('kelasSiswa', function ($q) use ($request) {
                $q->where('periode_id', $request->periode_id);
            });
        }

        return $query;
    }

    private function getFilterInfo($request)
    {
        $filters = [];
        
        if ($request->filled('nama_siswa')) {
            $filters[] = 'Nama Siswa: ' . $request->nama_siswa;
        }
        
        if ($request->filled('nama_kelas')) {
            $filters[] = 'Kelas: ' . $request->nama_kelas;
        }
        
        if ($request->filled('tingkat')) {
            $filters[] = 'Tingkat: ' . $request->tingkat;
        }
        
        if ($request->filled('status')) {
            $filters[] = 'Status: ' . ucfirst($request->status);
        }
        
        if ($request->filled('tanggal')) {
            $filters[] = 'Tanggal: ' . Carbon::parse($request->tanggal)->format('d/m/Y');
        }
        
        if ($request->filled('bulan')) {
            $monthName = Carbon::createFromFormat('!m', $request->bulan)->format('F');
            $filters[] = 'Bulan: ' . $monthName;
        }
        
        if ($request->filled('tahun')) {
            $filters[] = 'Tahun: ' . $request->tahun;
        }
        
        if ($request->filled('periode_id')) {
            $periode = Periode::find($request->periode_id);
            if ($periode) {
                $filters[] = 'Periode: ' . $periode->tahun . ' - ' . ucfirst($periode->semester);
            }
        }
        
        return empty($filters) ? 'Semua Data' : implode(', ', $filters);
    }

    public function detail(Request $request, $id)
    {
        $siswa = Siswa::with([
            'absensis' => function ($query) use ($request) {
                $query->with(['kelasSiswa.kelas', 'petugas']);

                if ($request->filled('tanggal')) {
                    $query->whereDate('hari_tanggal', $request->tanggal);
                }

                if ($request->filled('bulan')) {
                    $query->whereMonth('hari_tanggal', $request->bulan);
                }

                if ($request->filled('tahun')) {
                    $query->whereYear('hari_tanggal', $request->tahun);
                }

                if ($request->filled('periode_id')) {
                    $query->whereHas('kelasSiswa', function ($q) use ($request) {
                        $q->where('periode_id', $request->periode_id);
                    });
                }
            }
        ])->findOrFail($id);

        $absensis = $siswa->absensis->sortByDesc('hari_tanggal');

        $data = $absensis->map(function ($item) {
            return [
                'tanggal' => \Carbon\Carbon::parse($item->hari_tanggal)->format('d/m/Y'),
                'hari' => \Carbon\Carbon::parse($item->hari_tanggal)->locale('id')->dayName,
                'status' => ucfirst($item->status),
                'status_surat' => $item->status_surat ? ucfirst($item->status_surat) : '-',
                'catatan' => $item->catatan ?? '-',
                'petugas' => $item->petugas->name ?? '-',
                'bukti' => $item->foto_surat ? asset('storage/' . $item->foto_surat) : null,
            ];
        });

        // Get class info
        $kelasInfo = $siswa->absensis->first()?->kelasSiswa;

        return response()->json([
            'nama' => $siswa->nama_siswa,
            'nis' => $siswa->nis_nip,
            'kelas' => $kelasInfo ? $kelasInfo->kelas->nama_kelas : '-',
            'tingkat' => $kelasInfo ? $kelasInfo->kelas->tingkat : '-',
            'jurusan' => $kelasInfo ? $kelasInfo->kelas->jurusan->nama_jurusan : '-',
            'total_absen' => $absensis->count(),
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'alpa' => $absensis->where('status', 'alpa')->count(),
            'persentase_hadir' => $absensis->count() > 0 ? round(($absensis->where('status', 'hadir')->count() / $absensis->count()) * 100, 2) : 0,
            'detail_absensi' => $data->values(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = Absensi::with(['siswa', 'kelasSiswa.kelas.jurusan', 'petugas'])
            ->whereHas('kelasSiswa', fn($q) => $q->where('is_active', 'aktif'));

        $this->applyFilters($query, $request);

        $absensiList = $query->orderBy('hari_tanggal', 'desc')->get();

        // Group data berdasarkan siswa
        $data = $absensiList->groupBy('id_siswa')->map(function ($items) {
            $first = $items->first();
            return [
                'siswa' => $first->siswa,
                'kelas_siswa' => $first->kelasSiswa,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->sortByDesc('alpa');

        $filterInfo = $this->getFilterInfo($request);

        $pdf = PDF::loadView('superadmin.monitoring-absensi.pdf', compact('data', 'filterInfo'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->stream('Laporan_Monitoring_Absensi_' . date('Y-m-d') . '.pdf');
    }

    public function exportDetailPdf(Request $request, $id)
    {
        $siswa = Siswa::with([
            'absensis' => function ($query) use ($request) {
                $query->with(['kelasSiswa.kelas.jurusan', 'petugas']);

                if ($request->filled('tanggal')) {
                    $query->whereDate('hari_tanggal', $request->tanggal);
                }

                if ($request->filled('bulan')) {
                    $query->whereMonth('hari_tanggal', $request->bulan);
                }

                if ($request->filled('tahun')) {
                    $query->whereYear('hari_tanggal', $request->tahun);
                }

                if ($request->filled('periode_id')) {
                    $query->whereHas('kelasSiswa', function ($q) use ($request) {
                        $q->where('periode_id', $request->periode_id);
                    });
                }
            }
        ])->findOrFail($id);

        $data = $siswa->absensis->sortByDesc('hari_tanggal');
        $filterInfo = $this->getFilterInfo($request);

        // Get class info
        $kelasInfo = $siswa->absensis->first()?->kelasSiswa;

        $pdf = PDF::loadView('superadmin.monitoring-absensi.pdf-detail', compact('siswa', 'data', 'filterInfo', 'kelasInfo'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream("Detail_Absensi_{$siswa->nama_siswa}_" . date('Y-m-d') . ".pdf");
    }
}