<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tindakan;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\TindakanSiswa;
use App\Models\KategoriTindakan;
use App\Models\Skor_Pelanggaran;
use Illuminate\Support\Facades\Log;
use App\Models\Periode;

class MonitoringPelanggaranController extends Controller
{


    public function index(Request $request)
    {
        $kategoriTindakan = KategoriTindakan::all();
        $periodes = Periode::orderByDesc('tahun')->get();

        // Ambil periode aktif sebagai default
        $activePeriode = Periode::where('is_active', 'aktif')->first();

        $selectedPeriodeId = $request->filled('periode_id')
            ? $request->periode_id
            : ($activePeriode ? $activePeriode->id : null);

        // Base query
        $query = Pelanggaran::with([
            'siswa',
            'kelasSiswa' => function($q) {
                $q->where('is_active', 'aktif')
                ->with(['kelas.jurusan']);
            },
            'skor_pelanggaran',
            'petugas'
        ])
        ->whereHas('kelasSiswa', function ($q) use ($selectedPeriodeId) {
            $q->where('is_active', 'aktif');
            if ($selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
            }
        });

        $this->applyFilters($query, $request);

        $pelanggaran = $query->orderBy('tanggal', 'desc')->get();

        $siswaPelanggar = $pelanggaran->groupBy('id_siswa')->map(function ($items) {
            $firstItem = $items->first();
            return [
                'siswa' => $firstItem->siswa,
                'kelas_siswa' => $firstItem->kelasSiswa,
                'jumlah_pelanggaran' => $items->count(),
                'total_skor' => $items->sum(fn($item) => $item->skor_pelanggaran->skor ?? 0),
                'pelanggaran' => $items
            ];
        })->sortByDesc('total_skor');

        $siswaList = Siswa::all();
        $kelasList = Kelas::with('jurusan')->get();
        $skorList = Skor_Pelanggaran::all();
        $tingkatList = Kelas::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');

        $totalSkor = $siswaPelanggar->pluck('total_skor', 'siswa.id');
        $siswaWithWarning = $siswaPelanggar->filter(fn($data) => $data['total_skor'] >= 1000);
        $siswaPeringatan = $siswaWithWarning->map(fn($data) => $data['siswa'])->values();

        return view('superadmin/monitoring-pelanggaran.index', compact(
            'pelanggaran',
            'kategoriTindakan',
            'siswaPelanggar',
            'siswaList',
            'kelasList',
            'skorList',
            'tingkatList',
            'totalSkor',
            'siswaPeringatan',
            'periodes',
            'selectedPeriodeId'
        ));
    }


    /**
     * Apply filters to query
     */
    private function applyFilters($query, $request)
    {
        // Filter berdasarkan nama siswa
        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        // Filter berdasarkan jurusan
        if ($request->filled('jurusan')) {
            $query->whereHas('kelasSiswa.kelas.jurusan', function ($q) use ($request) {
                $q->where('nama_jurusan', 'like', '%' . $request->jurusan . '%');
            });
        }

        // Filter berdasarkan tingkat - NEW FILTER
        if ($request->filled('tingkat')) {
            $query->whereHas('kelasSiswa.kelas', function ($q) use ($request) {
                $q->where('tingkat', $request->tingkat);
            });
        }

        // Filter berdasarkan nama pelanggaran
        if ($request->filled('nama_pelanggaran')) {
            $query->whereHas('skor_pelanggaran', function ($q) use ($request) {
                $q->where('nama_pelanggaran', 'like', '%' . $request->nama_pelanggaran . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', Carbon::parse($request->tanggal));
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        if ($request->filled('periode_id')) {
            $query->whereHas('kelasSiswa', function ($q) use ($request) {
                $q->where('periode_id', $request->periode_id);
            });
        }

        return $query;
    }

    public function exportPdf(Request $request)
    {
        try {
            // Build query sama seperti di index
            $query = Pelanggaran::with([
                'siswa',
                'kelasSiswa' => function($q) {
                    $q->where('is_active', 'aktif')
                      ->with(['kelas.jurusan']);
                },
                'skor_pelanggaran',
                'petugas'
            ])
            ->whereHas('kelasSiswa', function ($q) {
                $q->where('is_active', 'aktif');
            });

            // Apply same filters
            $this->applyFilters($query, $request);

            $pelanggaran = $query->orderBy('tanggal', 'desc')->get();

            // Group data per siswa
            $siswaPelanggar = $pelanggaran->groupBy('id_siswa')->map(function ($items, $id_siswa) {
                $firstItem = $items->first();
                return [
                    'siswa' => $firstItem->siswa,
                    'kelas_siswa' => $firstItem->kelasSiswa,
                    'jumlah_pelanggaran' => $items->count(),
                    'total_skor' => $items->sum(function($item) {
                        return $item->skor_pelanggaran->skor ?? 0;
                    }),
                    'pelanggaran' => $items
                ];
            })->sortByDesc('total_skor');

            // Data untuk header laporan
            $filterInfo = $this->getFilterInfo($request);
            
            $data = [
                'siswaPelanggar' => $siswaPelanggar,
                'filterInfo' => $filterInfo,
                'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s'),
                'total_siswa' => $siswaPelanggar->count(),
                'total_pelanggaran' => $pelanggaran->count(),
            ];

            $pdf = PDF::loadView('superadmin.monitoring-pelanggaran.pdf', $data);
            $pdf->setPaper('A4', 'landscape');

            $filename = 'monitoring-pelanggaran-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get filter information for PDF header
     */
    private function getFilterInfo($request)
    {
        $filters = [];
        
        if ($request->filled('nama_siswa')) {
            $filters[] = 'Nama Siswa: ' . $request->nama_siswa;
        }
        
        if ($request->filled('jurusan')) {
            $filters[] = 'Jurusan: ' . $request->jurusan;
        }
        
        if ($request->filled('tingkat')) {
            $filters[] = 'Tingkat: ' . $request->tingkat;
        }
        
        if ($request->filled('nama_pelanggaran')) {
            $filters[] = 'Pelanggaran: ' . $request->nama_pelanggaran;
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

// Perbaikan untuk method getDetail di MonitoringPelanggaranController

public function getDetail($id)
{
    try {
        Log::info("Getting detail for siswa ID: " . $id); // Debug log
        
        $siswa = Siswa::with([
            'pelanggarans' => function($q) {
                $q->with(['skor_pelanggaran', 'petugas'])
                  ->whereHas('kelasSiswa', function($subQ) {
                      $subQ->where('is_active', 'aktif');
                  })
                  ->orderBy('tanggal', 'desc');
            },
            'kelasSiswa' => function($q) {
                $q->where('is_active', 'aktif')
                  ->with(['kelas.jurusan']);
            }
        ])->findOrFail($id);

        Log::info("Found siswa: " . $siswa->nama_siswa); // Debug log
        Log::info("Total pelanggarans: " . $siswa->pelanggarans->count()); // Debug log

        $data = $siswa->pelanggarans->map(function ($p) {
            return [
                'tanggal' => Carbon::parse($p->tanggal)->format('d/m/Y'),
                'nama_pelanggaran' => $p->skor_pelanggaran->nama_pelanggaran ?? '-',
                'skor' => $p->skor_pelanggaran->skor ?? 0,
                'petugas' => $p->petugas->name ?? '-',
                'bukti' => $p->bukti_pelanggaran ? asset('storage/' . $p->bukti_pelanggaran) : null,
            ];
        });

        $totalSkor = $siswa->pelanggarans->sum(function($p) {
            return $p->skor_pelanggaran->skor ?? 0;
        });

        $response = [
            'nama' => $siswa->nama_siswa,
            'total_skor' => $totalSkor,
            'total_pelanggaran' => $siswa->pelanggarans->count(),
            'pelanggarans' => $data,
        ];

        Log::info("Response data: " . json_encode($response)); // Debug log

        return response()->json($response);
        
    } catch (\Exception $e) {
        Log::error('Error getting violation details: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString()); // Debug log
        
        return response()->json([
            'error' => 'Gagal mengambil data detail pelanggaran',
            'message' => $e->getMessage()
        ], 500);
    }
}

// Perbaikan untuk method exportDetailPdf
public function exportDetailPdf($id)
{
    try {
        Log::info("Exporting detail PDF for siswa ID: " . $id);

        $siswa = Siswa::with([
            'pelanggarans' => function($q) {
                $q->with(['skor_pelanggaran', 'petugas'])
                  ->whereHas('kelasSiswa', function($subQ) {
                      $subQ->where('is_active', 'aktif');
                  })
                  ->orderBy('tanggal', 'desc');
            },
            'kelasSiswa' => function($q) {
                $q->where('is_active', 'aktif')
                  ->with(['kelas.jurusan']);
            }
        ])->findOrFail($id);

        Log::info("Found siswa for PDF: " . $siswa->nama_siswa);

        $totalSkor = $siswa->pelanggarans->sum(function($p) {
            return $p->skor_pelanggaran->skor ?? 0;
        });

        // Proses base64 image
        foreach ($siswa->pelanggarans as $pelanggaran) {
            $path = storage_path('app/public/bukti_pelanggaran/' . $pelanggaran->bukti_pelanggaran);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $pelanggaran->base64_image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $pelanggaran->base64_image = null;
            }
        }

        $data = [
            'siswa' => $siswa,
            'pelanggarans' => $siswa->pelanggarans,
            'total_skor' => $totalSkor,
            'total_pelanggaran' => $siswa->pelanggarans->count(),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s'),
        ];

        $pdf = PDF::loadView('superadmin.monitoring-pelanggaran.detail-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'detail-pelanggaran-' . str_replace(' ', '-', $siswa->nama_siswa) . '-' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error exporting detail PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF detail: ' . $e->getMessage());
    }
}
}