<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\BKKelas; // Pastikan konsisten dengan nama model
use App\Models\KelasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // Menampilkan form input absensi hari ini
    public function createHariIni()
    {
        $userId = Auth::id();

        // Cek kelas aktif yang dipegang user ini
        $kelas = Kelas::where('id_users', $userId)
                      ->where('stt', 'aktif')
                      ->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas aktif.');
        }

        $tanggalHariIni = Carbon::today()->toDateString();

        // Ambil siswa di kelas ini yang belum diabsen hari ini
        $siswaBelumAbsen = KelasSiswa::with('siswa')
            ->where('id_kelas', $kelas->id)
            ->whereDoesntHave('absensi', function ($query) use ($tanggalHariIni) {
                $query->where('hari_tanggal', $tanggalHariIni);
            })
            ->get();

        return view('superadmin.absensi.create', compact('siswaBelumAbsen', 'tanggalHariIni'));
    }

    // Menyimpan data absensi
    public function store(Request $request)
    {
        $request->validate([
            'absensi.*.kelas_siswa_id' => 'required|exists:kelas_siswa,id',
            'absensi.*.status' => 'required|in:hadir,sakit,izin,alpa',
            'absensi.*.catatan' => 'nullable|string',
            'absensi.*.foto_surat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Validasi tambahan: jika status sakit/izin, foto wajib
        foreach ($request->absensi as $index => $data) {
            if (in_array($data['status'], ['sakit', 'izin']) && !$request->hasFile("absensi.$index.foto_surat")) {
                return back()->withErrors([
                    "absensi.$index.foto_surat" => "Foto surat wajib diunggah jika status sakit atau izin."
                ])->withInput();
            }
        }

        $userId = Auth::id();
        $tanggalHariIni = Carbon::today()->toDateString();

        foreach ($request->absensi as $index => $data) {
            $kelasSiswa = KelasSiswa::findOrFail($data['kelas_siswa_id']);
            
            $absen = new Absensi();
            $absen->status = $data['status'];
            $absen->hari_tanggal = $tanggalHariIni;
            // Fix: gunakan 'tertunda' bukan 'tertunda'
            $absen->status_surat = in_array($data['status'], ['sakit', 'izin']) ? 'tertunda' : 'diterima';
            $absen->catatan = $data['catatan'] ?? null;
            $absen->id_siswa = $kelasSiswa->id_siswa;
            $absen->kelas_siswa_id = $kelasSiswa->id;
            $absen->id_users = $userId;

            // Handle file upload
            if ($request->hasFile("absensi.$index.foto_surat")) {
                $file = $request->file("absensi.$index.foto_surat");
                $path = $file->store('foto_surat', 'public');
                $absen->foto_surat = $path;
            }

            $absen->save();
        }

        return redirect()->route('createHariIni')->with('success', 'Absensi berhasil disimpan.');
    }

    // Riwayat absensi hari ini
    public function riwayatHariIni()
    {
        $userId = Auth::id();
        $tanggalHariIni = Carbon::today()->toDateString();

        $kelas = Kelas::where('id_users', $userId)->where('stt', 'aktif')->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas aktif.');
        }

        $riwayat = Absensi::with('siswa')
            ->where('hari_tanggal', $tanggalHariIni)
            ->where('id_users', $userId)
            ->whereHas('kelasSiswa', function ($q) use ($kelas) {
                $q->where('id_kelas', $kelas->id);
            })
            ->get();

        return view('superadmin.absensi.riwayat', compact('riwayat', 'tanggalHariIni'));
    }

    public function hapusAbsensi($id)
    {
        $absensi = Absensi::findOrFail($id);

        // Hapus foto surat jika ada
        if ($absensi->foto_surat && Storage::disk('public')->exists($absensi->foto_surat)) {
            Storage::disk('public')->delete($absensi->foto_surat);
        }

        $absensi->delete();

        return back()->with('message', 'Absensi berhasil dihapus.');
    }

    public function validasiSuratIndex()
    {
        $userId = Auth::id();

        $kelasIds = BKKelas::where('id_bk', $userId)->pluck('id_kelas');

        $absensi = Absensi::with(['siswa', 'kelasSiswa.kelas.jurusan'])
            ->where('status_surat', 'tertunda') // Fix: gunakan 'tertunda'
            ->whereHas('kelasSiswa', function ($q) use ($kelasIds) {
                $q->whereIn('id_kelas', $kelasIds);
            })
            ->orderBy('hari_tanggal', 'desc')
            ->get();

        return view('superadmin.bk.validasi', compact('absensi'));
    }

    public function validasiSurat($id)
    {
        $userId = Auth::id();
        $absensi = Absensi::with('kelasSiswa')->findOrFail($id);
        $idKelas = $absensi->kelasSiswa->id_kelas;

        // Cek apakah user BK terkait dengan kelas ini
        $isBk = BKKelas::where('id_kelas', $idKelas)
            ->where('id_bk', $userId)
            ->exists();

        if (!$isBk) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk memvalidasi surat ini.');
        }

        if ($absensi->status_surat !== 'tertunda') { // Fix: gunakan 'tertunda'
            return redirect()->back()->with('error', 'Surat sudah divalidasi atau tidak memerlukan validasi.');
        }

        $absensi->status_surat = 'diterima';
        $absensi->save();

        return back()->with('success', 'Surat berhasil divalidasi dan diterima.');
    }

    // Method baru untuk menolak surat
        public function tolakSurat(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $userId = Auth::id();
        $absensi = Absensi::with('kelasSiswa')->findOrFail($id);
        $idKelas = $absensi->kelasSiswa->id_kelas;

        $isBk = BKKelas::where('id_kelas', $idKelas)
            ->where('id_bk', $userId)
            ->exists();

        if (!$isBk) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menolak surat ini.');
        }

        if ($absensi->status_surat !== 'tertunda') {
            return redirect()->back()->with('error', 'Surat sudah divalidasi atau tidak memerlukan validasi.');
        }

        $absensi->status_surat = 'ditolak';
        $absensi->status = 'alpa';
        $absensi->catatan = $request->catatan;
        $absensi->save();

        return back()->with('success', 'Surat berhasil ditolak. Status absensi diubah menjadi alpa.');
    }

}