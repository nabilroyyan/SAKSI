<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Pelanggaran;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;

class DasboardController extends Controller
{
    public function superadmin()
    {
        // Data Statistik Utama
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalGuru = User::role('tatip')->count();
        $totalBK = User::role('bk')->count();
        
        // Periode Aktif
        $periodeAktif = Periode::where('is_active', 'aktif')->first();
        
        // Statistik Pelanggaran
        $pelanggaranBulanIni = Pelanggaran::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();
            
        $pelanggaranBulanLalu = Pelanggaran::whereMonth('tanggal', Carbon::now()->subMonth()->month)
            ->whereYear('tanggal', Carbon::now()->subMonth()->year)
            ->count();
            
        $persentasePelanggaran = $pelanggaranBulanLalu > 0 ? 
            (($pelanggaranBulanIni - $pelanggaranBulanLalu) / $pelanggaranBulanLalu) * 100 : 0;
        
        // Statistik Absensi
        $absensiHariIni = Absensi::whereDate('hari_tanggal', Carbon::today())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
            
        // 5 Pelanggaran Terbaru
        $pelanggaranTerbaru = Pelanggaran::with(['Siswa', 'Skor_Pelanggaran'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();
            
        // 5 Absensi Terbaru (Izin/Sakit/Alpa)
        $absensiTerbaru = Absensi::with('siswa')
            ->whereIn('status', ['izin', 'sakit', 'alpa'])
            ->orderBy('hari_tanggal', 'desc')
            ->take(5)
            ->get();
            
        // Data untuk Chart
        $pelanggaranPerBulan = Pelanggaran::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
            
        $absensiPerStatus = Absensi::selectRaw('status, COUNT(*) as total')
            ->whereMonth('hari_tanggal', Carbon::now()->month)
            ->groupBy('status')
            ->get();

        return view('superadmin.dasboard', compact(
            'totalSiswa',
            'totalKelas',
            'totalGuru',
            'totalBK',
            'periodeAktif',
            'pelanggaranBulanIni',
            'persentasePelanggaran',
            'absensiHariIni',
            'pelanggaranTerbaru',
            'absensiTerbaru',
            'pelanggaranPerBulan',
            'absensiPerStatus'
        ));
    }
}