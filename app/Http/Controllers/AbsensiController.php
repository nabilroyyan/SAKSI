<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\KelasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BKKelas; // Pastikan konsisten dengan nama model

class AbsensiController extends Controller
{
    // Menampilkan form input absensi hari ini
    public function createHariIni()
    {
        $userId = Auth::id();

        // Ambil semua kelas aktif yang dipegang user
        $kelasList = Kelas::with('jurusan')
                    ->where('id_users', $userId)
                    ->where('stt', 'aktif') // pastikan hanya kelas aktif
                    ->get();

        if ($kelasList->isEmpty()) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas aktif.');
        }

        $tanggalHariIni = Carbon::today()->toDateString();

        // Ambil semua ID kelas yang dipegang user
        $kelasIds = $kelasList->pluck('id')->toArray();

        // Ambil siswa dari kelas tersebut yang aktif dan belum absen hari ini
        $siswaBelumAbsen = KelasSiswa::with('siswa', 'kelas')
            ->whereIn('id_kelas', $kelasIds)
            ->where('is_active', 'aktif')
            ->whereDoesntHave('absensi', function ($query) use ($tanggalHariIni) {
                $query->where('hari_tanggal', $tanggalHariIni);
            })
            ->get();

        return view('superadmin.absensi.create', [
            'siswaBelumAbsen' => $siswaBelumAbsen,
            'tanggalHariIni' => $tanggalHariIni,
            'kelasList' => $kelasList // opsional: bisa ditampilkan di view
        ]);
    }


    public function store(Request $request)
    {
        // Improved validation with conditional file requirement
        $request->validate([
            'absensi.*.kelas_siswa_id' => 'required|exists:kelas_siswa,id',
            'absensi.*.status' => 'required|in:hadir,sakit,izin,alpa',
            'absensi.*.catatan' => 'nullable|string|max:500',
            'absensi.*.foto_surat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Reduced to 5MB
        ]);

        // Additional validation for required foto_surat
        $absensiData = $request->absensi ?? [];
        foreach ($absensiData as $index => $data) {
            if (in_array($data['status'], ['sakit', 'izin'])) {
                $request->validate([
                    "absensi.$index.foto_surat" => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
                ], [
                    "absensi.$index.foto_surat.required" => "Foto surat wajib diupload untuk status {$data['status']}."
                ]);
            }
        }

        $userId = Auth::id();
        $tanggalHariIni = Carbon::today()->toDateString();
        $berhasilDisimpan = 0;
        $gagalDisimpan = 0;

        foreach ($absensiData as $index => $data) {
            try {
                if (!in_array($data['status'], ['hadir', 'sakit', 'izin', 'alpa'])) {
                    Log::error("Status tidak valid di index $index", ['status' => $data['status']]);
                    $gagalDisimpan++;
                    continue;
                }

                $kelasSiswa = KelasSiswa::findOrFail($data['kelas_siswa_id']);

                // Check if already exists for today
                $existingAbsensi = Absensi::where('kelas_siswa_id', $kelasSiswa->id)
                    ->where('hari_tanggal', $tanggalHariIni)
                    ->first();

                if ($existingAbsensi) {
                    Log::warning("Absensi sudah ada untuk siswa ini hari ini", [
                        'kelas_siswa_id' => $kelasSiswa->id,
                        'tanggal' => $tanggalHariIni
                    ]);
                    $gagalDisimpan++;
                    continue;
                }

                $absen = new Absensi();
                $absen->status = $data['status'];
                $absen->hari_tanggal = $tanggalHariIni;
                $absen->catatan = $data['catatan'] ?? null;
                $absen->id_siswa = $kelasSiswa->id_siswa;
                $absen->kelas_siswa_id = $kelasSiswa->id;
                $absen->id_users = $userId;

                // Cek jika status memerlukan surat
                $needsSurat = in_array($data['status'], ['sakit', 'izin']);
                
                // Set status_surat dengan nilai yang sesuai dengan enum di database
                if ($needsSurat) {
                    $absen->status_surat = 'tertunda'; // Gunakan 'pending' instead of 'tertunda'
                } else {
                    $absen->status_surat = 'diterima'; // Gunakan 'approved' instead of 'diterima'
                }

                // Penanganan file upload jika ada
                if ($request->hasFile("absensi.$index.foto_surat")) {
                    $file = $request->file("absensi.$index.foto_surat");

                    if ($file && $file->isValid()) {
                        try {
                            // Create directory if not exists
                            $directory = 'foto_surat/' . date('Y/m');
                            if (!Storage::disk('public')->exists($directory)) {
                                Storage::disk('public')->makeDirectory($directory);
                            }

                            // Generate unique filename
                            $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs($directory, $filename, 'public');
                            
                            $absen->foto_surat = $path;
                            $absen->status_surat = 'tertunda'; // Status pending ketika ada file
                            
                            Log::info("Upload berhasil untuk index $index", [
                                'path' => $path,
                                'original_name' => $file->getClientOriginalName(),
                                'size' => $file->getSize()
                            ]);
                        } catch (\Exception $uploadException) {
                            Log::error("Gagal upload file untuk index $index", [
                                'error' => $uploadException->getMessage(),
                                'file_info' => [
                                    'name' => $file->getClientOriginalName(),
                                    'size' => $file->getSize(),
                                    'mime' => $file->getMimeType()
                                ]
                            ]);
                            
                            return redirect()->back()
                                ->with('error', "Gagal mengupload foto surat untuk siswa nomor " . ($index + 1))
                                ->withInput();
                        }
                    } else {
                        Log::warning("File upload tidak valid untuk index $index");
                        return redirect()->back()
                            ->with('error', "File foto surat tidak valid untuk siswa nomor " . ($index + 1))
                            ->withInput();
                    }
                } else {
                    if ($needsSurat) {
                        Log::warning("Status {$data['status']} tanpa foto_surat di index $index");
                        return redirect()->back()
                            ->with('error', "Foto surat wajib diupload untuk status {$data['status']}")
                            ->withInput();
                    }
                }

                $absen->save();
                $berhasilDisimpan++;

                Log::info("Absensi berhasil disimpan untuk index $index", [
                    'absensi_id' => $absen->id,
                    'status' => $absen->status,
                    'status_surat' => $absen->status_surat
                ]);

            } catch (\Exception $e) {
                Log::error("Gagal menyimpan absensi untuk index $index", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);
                $gagalDisimpan++;
                continue;
            }
        }

        $message = "Absensi berhasil disimpan: $berhasilDisimpan";
        if ($gagalDisimpan > 0) {
            $message .= ", Gagal: $gagalDisimpan";
        }

        return redirect()->route('createHariIni')->with('success', $message);
    }


    // Riwayat absensi hari ini
    public function riwayatHariIni()
    {
        $userId = Auth::id();
        $tanggalHariIni = Carbon::today()->toDateString();

        // Ambil semua kelas aktif yang dipegang oleh user
        $kelasList = Kelas::with('jurusan')
            ->where('id_users', $userId)
            ->where('stt', 'aktif')
            ->get();

        if ($kelasList->isEmpty()) {
            return redirect()->back()->with('error', 'Anda bukan wali kelas aktif.');
        }

        $idKelasList = $kelasList->pluck('id')->toArray();

        $riwayat = Absensi::with(['kelasSiswa.siswa', 'petugas'])
            ->where('hari_tanggal', $tanggalHariIni)
            ->where('id_users', $userId)
            ->whereHas('kelasSiswa', function ($q) use ($idKelasList) {
                $q->whereIn('id_kelas', $idKelasList);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.absensi.riwayat', [
            'riwayat' => $riwayat,
            'tanggalHariIni' => $tanggalHariIni,
            'kelasList' => $kelasList // mengirim semua kelas
        ]);
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