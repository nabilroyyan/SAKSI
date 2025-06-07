<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tindakan;
use App\Models\KelasSiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\TindakanSiswa;
use App\Models\KategoriTindakan;
use App\Models\skor_pelanggaran;
use Illuminate\Support\Facades\Log;

class MonitoringPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $kategoriTindakan = KategoriTindakan::all();

        $query = Pelanggaran::with([
            'siswa',
            'kelasSiswa.kelas.jurusan',
            'skor_pelanggaran',
            'petugas'
        ])
        ->whereHas('kelasSiswa', function ($q) {
            $q->where('is_active', 'aktif');
        });

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

        // Filter berdasarkan nama pelanggaran
        if ($request->filled('nama_pelanggaran')) {
            $query->whereHas('skor_pelanggaran', function ($q) use ($request) {
                $q->where('nama_pelanggaran', 'like', '%' . $request->nama_pelanggaran . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', \Carbon\Carbon::parse($request->tanggal));
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Eksekusi query
        $pelanggaran = $query->orderBy('tanggal', 'desc')->get();

        // Group data per siswa untuk tampilan
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

        $siswaList = Siswa::all();
        $kelasList = Kelas::all();
        $skorList = skor_pelanggaran::all();

        $totalSkor = $siswaPelanggar->pluck('total_skor', 'siswa.id');

        // Ambil siswa yang total skornya >= 1000
        $siswaWithWarning = $siswaPelanggar->filter(function($data) {
            return $data['total_skor'] >= 1000;
        });

        $siswaPeringatan = $siswaWithWarning->map(function($data) {
            return $data['siswa'];
        })->values();

        return view('superadmin/monitoring-pelanggaran.index', compact(
            'pelanggaran',
            'kategoriTindakan',
            'siswaPelanggar',
            'siswaList',
            'kelasList',
            'skorList',
            'totalSkor',
            'siswaPeringatan'
        ));
    }

    public function getDetail($id)
{
    $siswa = Siswa::with([
        'pelanggarans.skor_pelanggaran',
        'pelanggarans.petugas',
    ])->findOrFail($id);

    $data = $siswa->pelanggarans->map(function ($p) {
        return [
            'tanggal' => $p->tanggal,
            'nama_pelanggaran' => $p->skor_pelanggaran->nama_pelanggaran ?? '-',
            'skor' => $p->skor_pelanggaran->skor ?? 0,
            'petugas' => $p->petugas->name ?? '-',
            'bukti' => asset('storage/' . $p->bukti_pelanggaran),
        ];
    });

    return response()->json([
        'nama' => $siswa->nama_siswa,
        'total_skor' => $siswa->pelanggarans->sum(fn($p) => $p->skor_pelanggaran->skor ?? 0),
        'total_pelanggaran' => $siswa->pelanggarans->count(),
        'pelanggarans' => $data, // <== penting!
    ]);
}



    public function simpanTindakan(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_tindakan' => 'required|exists:kategori_tindakan,id',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        TindakanSiswa::create([
            'id_siswa' => $request->id_siswa,
            'id_tindakan' => $request->id_tindakan,
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
        ]);

        return response()->json(['message' => 'Tindakan berhasil disimpan.']);
    }
 

}