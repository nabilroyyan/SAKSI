<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Jurusan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringAbsensiExport;
use Carbon\Carbon;

class MonitoringAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Handle export request
        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcel($request);
        }

        // Query absensi dengan relasi
        $query = Absensi::with([
            'siswa',
            'kelasSiswa.kelas.jurusan',
            'petugas'
        ])->whereHas('kelasSiswa', function ($q) {
            $q->where('is_active', 'aktif');
        });

        // Filter Nama Siswa
        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        // Filter Jurusan
        if ($request->filled('jurusan')) {
            $query->whereHas('kelasSiswa.kelas.jurusan', function ($q) use ($request) {
                $q->where('nama_jurusan', 'like', '%' . $request->jurusan . '%');
            });
        }

        // Filter Kelas
        if ($request->filled('kelas')) {
            $query->whereHas('kelasSiswa.kelas', function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->kelas . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('hari_tanggal', $request->tanggal);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }

        // Filter rentang tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('hari_tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

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

        // Data tambahan untuk filter di view
        $siswaList = Siswa::all();
        $kelasList = Kelas::with('jurusan')->get();
        $jurusanList = Jurusan::all();

        return view('superadmin.monitoring-absensi.index', compact(
            'siswaAbsensi',
            'siswaList',
            'kelasList',
            'jurusanList'
        ));
    }

public function getDetail($id)
{
    $siswa = Siswa::with([
        'absensis.kelasSiswa.kelas',
        'absensis.petugas'
    ])->findOrFail($id);

    $data = $siswa->absensis->sortByDesc('hari_tanggal')->map(function ($item) {
        return [
            'tanggal' => $item->hari_tanggal,
            'status' => $item->status,
            'status_surat' => $item->status_surat,
            'catatan' => $item->catatan ?? '-',
            'petugas' => $item->petugas->name ?? '-',
            'bukti' => $item->foto_surat ? asset('storage/' . $item->foto_surat) : null,
        ];
    });

    return response()->json([
        'nama' => $siswa->nama_siswa,
        'total_absen' => $siswa->absensis->count(),
        'hadir' => $siswa->absensis->where('status', 'hadir')->count(),
        'sakit' => $siswa->absensis->where('status', 'sakit')->count(),
        'izin' => $siswa->absensis->where('status', 'izin')->count(),
        'alpa' => $siswa->absensis->where('status', 'alpa')->count(),
        'detail_absensi' => $data,
    ]);
}


    public function getStatistik(Request $request)
    {
        $query = Absensi::with(['siswa', 'kelasSiswa.kelas.jurusan'])
            ->whereHas('kelasSiswa', function ($q) {
                $q->where('is_active', 'aktif');
            });

        // Apply filters
        if ($request->filled('bulan')) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }

        $absensiList = $query->get();

        // Statistik per status
        $statistik = [
            'hadir' => $absensiList->where('status', 'hadir')->count(),
            'sakit' => $absensiList->where('status', 'sakit')->count(),
            'izin' => $absensiList->where('status', 'izin')->count(),
            'alpa' => $absensiList->where('status', 'alpa')->count(),
            'total' => $absensiList->count(),
        ];

        // Statistik per jurusan
        $statistikJurusan = $absensiList->groupBy(function ($item) {
            return $item->kelasSiswa->kelas->jurusan->nama_jurusan ?? 'Tidak Ada';
        })->map(function ($items, $jurusan) {
            return [
                'jurusan' => $jurusan,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->values();

        // Statistik per bulan (untuk chart)
        $statistikBulanan = $absensiList->groupBy(function ($item) {
            return Carbon::parse($item->hari_tanggal)->format('Y-m');
        })->map(function ($items, $bulan) {
            return [
                'bulan' => $bulan,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->values();

        return response()->json([
            'statistik_umum' => $statistik,
            'statistik_jurusan' => $statistikJurusan,
            'statistik_bulanan' => $statistikBulanan,
        ]);
    }

    public function getSiswaBerisiko(Request $request)
    {
        $query = Absensi::with(['siswa', 'kelasSiswa.kelas.jurusan'])
            ->whereHas('kelasSiswa', function ($q) {
                $q->where('is_active', 'aktif');
            });

        // Filter bulan dan tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }

        $absensiList = $query->get();

        // Group data berdasarkan siswa dan hitung persentase alpa
        $siswaBerisiko = $absensiList->groupBy('id_siswa')->map(function ($items) {
            $first = $items->first();
            $totalAlpa = $items->where('status', 'alpa')->count();
            $totalAbsensi = $items->count();
            $persentaseAlpa = $totalAbsensi > 0 ? ($totalAlpa / $totalAbsensi) * 100 : 0;

            return [
                'siswa' => $first->siswa,
                'kelas_siswa' => $first->kelasSiswa,
                'total_alpa' => $totalAlpa,
                'total_absensi' => $totalAbsensi,
                'persentase_alpa' => round($persentaseAlpa, 2),
            ];
        })
        ->filter(function ($item) {
            // Filter siswa dengan alpa > 20% atau total alpa > 5
            return $item['persentase_alpa'] > 20 || $item['total_alpa'] > 5;
        })
        ->sortByDesc('persentase_alpa')
        ->take(10); // Ambil 10 siswa dengan risiko tertinggi

        return response()->json([
            'siswa_berisiko' => $siswaBerisiko->values(),
        ]);
    }


}