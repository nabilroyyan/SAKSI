<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\RiwayatKelasController;
use App\Http\Controllers\TindakanSiswaController;
use App\Http\Controllers\SkorPelanggaranController;
use App\Http\Controllers\KategoriTindakanController;
use App\Http\Controllers\MonitoringAbsensiController;
use App\Http\Controllers\MonitoringPelanggaranController;


    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login_action', [AuthController::class, 'login'])->name('login.action');

    });

    Route::middleware(['auth', 'verified', 'role_permission'])->group(function () {
        Route::get('/dashboard', [DasboardController::class, 'superadmin'])->name('superadmin');

        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::prefix('siswa')->group(function () {
            Route::get('/', [SiswaController::class, 'index'])->name('siswa.index')->middleware('permission:view data-siswa');
            Route::get('/create', [SiswaController::class, 'create'])->name('siswa.create')->middleware('permission:tambah siswa');
            Route::post('/store', [SiswaController::class, 'store'])->name('siswa.store');
            Route::get('/edit/{siswa}', [SiswaController::class, 'edit'])->name('siswa.edit')->middleware('permission:edit siswa');
            Route::put('/{siswa}', [SiswaController::class, 'update'])->name('siswa.update')->middleware('permission:update siswa');
            Route::delete('/delete/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy')->middleware('permission:hapus siswa');
            //menampilakn kelas siswa
            Route::get('/showKelasSiswa', [SiswaController::class, 'showKelasSiswa'])->name('showKelasSiswa')->middleware('permission:view kelas-siswa');
            //tambah siswa ke kelas
            Route::get('/kelas/{id}/siswa', [SiswaController::class, 'showSiswa'])->name('kelas.siswa')->middleware('permission:tambah kelas-siswa');
            Route::post('/{kelas}/siswa', [SiswaController::class, 'storeSiswa'])->name('kelas.siswa.store');// Changed to POST
            Route::post('siswa/import/data', [SiswaController::class, 'import'])->name('siswa.import')->middleware('permission:import-siswa');
            Route::delete('/allDestroy', [SiswaController::class, 'allDestroy'])->name('allDestroy');
        });

        Route::prefix('jurusan')->group(function () {
            Route::get('/', [JurusanController::class, 'index'])->name('jurusan.index')->middleware('permission:view jurusan');
            Route::get('/create', [JurusanController::class, 'create'])->name('jurusan.create')->middleware('permission:tambah jurusan');
            Route::post('/store', [JurusanController::class, 'store'])->name('jurusan.store');
            Route::get('/edit/{id}', [JurusanController::class, 'edit'])->name('jurusan.edit')->middleware('permission:edit jurusan');
            Route::put('/update/{id}', [JurusanController::class, 'update'])->name('jurusan.update');
            Route::delete('/delete/{id}', [JurusanController::class, 'destroy'])->name('jurusan.destroy')->middleware('permission:hapus jurusan');
        });

       // Kelas Management
        Route::prefix('kelas')->group(function () {
            Route::get('/', [KelasController::class, 'index'])->name('kelas.index')->middleware('permission:view kelas');
            Route::get('/create', [KelasController::class, 'create'])->name('kelas.create')->middleware('permission:tambah kelas');
            Route::post('/store', [KelasController::class, 'store'])->name('kelas.store');
            Route::get('/edit/{id}', [KelasController::class, 'edit'])->name('kelas.edit')->middleware('permission:edit kelas');
            Route::put('/update/{id}', [KelasController::class, 'update'])->name('kelas.update');
            Route::delete('/delete/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy')->middleware('permission:hapus kelas');
            
            // detail

            Route::get('/{id}/detail-siswa', [KelasController::class, 'showSiswaByKelas'])->name('kelas.detailSiswa')->middleware('permission:detail kelas');

            // Naikkan siswa secara bulk (multiple)
            Route::post('/kelas/naikkan-bulk-siswa', [KelasController::class, 'naikkanBulkSiswa'])->name('kelas.naikkanBulkSiswa')->middleware('permission:naik kelas');
            Route::post('/kelas/bulk-periode', [KelasController::class, 'bulkPeriode'])->name('kelas.bulkPeriode')->middleware('permission:update periode');
            Route::delete('/kelas/hapus-siswa/{id}', [KelasController::class, 'hapusSiswa'])->name('kelas.hapusSiswa')->middleware('permission:hapus-siswa kelas');
            

        });

        Route::prefix('pelanggaran')->group(function () {
            Route::get('/', [PelanggaranController::class, 'index'])->name('pelanggaran.index')->middleware('permission:view pelanggaran');
            Route::get('/create', [PelanggaranController::class, 'create'])->name('pelanggaran.create')->middleware('permission:tambah pelanggaran');
            Route::post('/store', [PelanggaranController::class, 'store'])->name('pelanggaran.store');
            Route::get('/edit/{id}', [PelanggaranController::class, 'edit'])->name('pelanggaran.edit')->middleware('permission:edit pelanggaran');
            Route::put('/update/{pelanggaran}', [PelanggaranController::class, 'update'])->name('pelanggaran.update');
            Route::delete('/delete/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy')->middleware('permission:hapus pelanggaran');
            // Di routes/web.php
            Route::get('/get-kelas-siswa/{id}', [PelanggaranController::class, 'getKelasSiswa']);

        });

        Route::prefix('skor-pelanggaran')->group(function () {
            Route::get('/', [SkorPelanggaranController::class, 'index'])->name('skor-Pelanggaran.index')->middleware('permission:view skor-pelanggaran');
            Route::get('/create', [SkorPelanggaranController::class, 'create'])->name('skor-Pelanggaran.create')->middleware('permission:tambah skor-pelanggaran');
            Route::post('/store', [SkorPelanggaranController::class, 'store'])->name('skor-Pelanggaran.store');
            Route::delete('/delete/{id}', [SkorPelanggaranController::class, 'destroy'])->name('skor-Pelanggaran.destroy')->middleware('permission:hapus skor-pelanggaran');
        });

        Route::prefix('monitoring-pelanggaran')->group(function () {
            Route::get('/', [MonitoringPelanggaranController::class, 'index'])->name('monitoring-Pelanggaran.index');
            Route::get('/detail/{id}', [MonitoringPelanggaranController::class, 'getDetail']);
            Route::get('/pdf-detail/{id}', [MonitoringPelanggaranController::class, 'exportDetailPdf']);
            Route::get('/pdf', [MonitoringPelanggaranController::class, 'exportPdf'])->name('pdf');
        });

        Route::prefix('kategori-tindakan')->group(function () {
            Route::get('/', [KategoriTindakanController::class, 'index'])->name('kategori-tindakan.index')->middleware('permission:view kategori-tindakan');
            Route::get('/create', [KategoriTindakanController::class, 'create'])->name('kategori-tindakan.create')->middleware('permission:tambah kategori-tindakan');
            Route::post('/store', [KategoriTindakanController::class, 'store'])->name('kategori-tindakan.store');
            Route::delete('/delete/{id}', [KategoriTindakanController::class, 'destroy'])->name('kategori-tindakan.destroy')->middleware('permission:hapus kategori-tindakan');
        });

        Route::prefix('bk')->group(function () {
            Route::get('/', [BkController::class, 'index'])->name('bk.index')->middleware('permission:view bk');
            Route::post('/assign', [BkController::class, 'assign'])->name('bk.assign')->middleware('permission:assign bk');
            Route::get('/assign/{bkId}/{kelasId}', [BkController::class, 'assignKelas'])->name('bk.assign.kelas')->middleware('permission:assign bk');
            Route::get('/assign-all/{bkId}', [BkController::class, 'assignAll'])->name('bk.assign.all')->middleware('permission:assign bk');
            Route::get('/unassign/{bkId}/{kelasId}', [BkController::class, 'unassign'])->name('bk.unassign');
            Route::get('/unassign-all/{bkId}', [BkController::class, 'unassignAll'])->name('bk.unassign.all');
        });
       
        Route::prefix('absensi')->group(function () {
            Route::get('/createHariIni', [AbsensiController::class, 'createHariIni'])->name('createHariIni')->middleware('permission:view input-absensi');
            Route::post('/store', [AbsensiController::class, 'store'])->name('absensi.store');
            Route::get('/riwayatHariIni', [AbsensiController::class, 'riwayatHariIni'])->name('riwayatHariIni')->middleware('permission:view riwayat-absensi');
            Route::delete('/hapus/{id}', [AbsensiController::class, 'hapusAbsensi'])->name('hapusAbsensi');
            Route::get('/validasi-surat', [AbsensiController::class, 'validasiSuratIndex'])->name('validasi.index')->middleware('permission:view validasi surat');
            Route::put('/validasiSurat/{id}', [AbsensiController::class, 'validasiSurat'])->name('validasiSurat')->middleware('permission:terima vaidasi');
            Route::put('/tolak/{id}', [AbsensiController::class, 'tolakSurat'])->name('tolakSurat')->middleware('permission:tolak validasi');
        });
 
        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:view role');
            Route::get('/create', [RoleController::class, 'create'])->name('role.create')->middleware('permission:tambah role');
            Route::post('/store', [RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit role');
            Route::post('/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::get('/{id}/manage-permissions', [RoleController::class, 'managePermissions'])->name('roles.manage-permissions');
            Route::post('/{id}/update-permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:hapus role');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('permission:view user');
            Route::get('/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:tambah user');
            Route::post('/store', [UserController::class, 'store'])->name('users.store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit user');
            Route::post('/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:hapus user');
        });

        Route::prefix('tindakan-siswa')->group(function () {
            Route::get('/', [TindakanSiswaController::class, 'index'])->name('tindakan-siswa.index')->middleware('permission:view tindakan-siswa');
            Route::get('/pengaturan/tindakan', [TindakanSiswaController::class, 'edit'])->name('pengaturan-tindakan.edit');
            Route::post('/pengaturan/tindakan', [TindakanSiswaController::class, 'update'])->name('pengaturan-tindakan.update')->middleware('permission:update batas skor');


            Route::get('/create/{siswa_id}/{kelas_siswa_id}', [TindakanSiswaController::class, 'create'])->name('tindakan-siswa.create')->middleware('permission:berikan tindakan-siswa');
            Route::post('/store', [TindakanSiswaController::class, 'store'])->name('tindakan-siswa.store');
            Route::post('/update-status/{id}', [TindakanSiswaController::class, 'updateStatus'])->name('tindakan-siswa.updateStatus')->middleware('permission:update tindakan-siswa');
            Route::delete('/delete/{id}', [TindakanSiswaController::class, 'destroy'])->name('tindakan-siswa.destroy')->middleware('permission:hapus tindakan-siswa');

        });

         Route::prefix('monitoring-absensi')->group(function () {
            Route::get('/', [MonitoringAbsensiController::class, 'index'])->name('monitoring-absensi.index')->middleware('permission:view monitoring-absensi');
            Route::get('/detail/{id}', [MonitoringAbsensiController::class, 'detail'])->name('monitoring-absensi.detail');
            Route::get('/pdf-absensi', [MonitoringAbsensiController::class, 'exportPdf'])->name('pdf.absensi');
            Route::get('/pdf-detail/{id}', [MonitoringAbsensiController::class, 'exportDetailPdf'])->name('pdf.detail');
        });

        Route::prefix('periode')->group(function () {
            Route::get('/', [PeriodeController::class, 'index'])->name('periode.index')->middleware('permission:view periode');
            Route::get('/create', [PeriodeController::class, 'create'])->name('periode.create')->middleware('permission:tambah periode');
            Route::post('/store', [PeriodeController::class, 'store'])->name('periode.store');
            Route::patch('/periodes/{id}/activate', [PeriodeController::class, 'activate'])->name('periode.activate')->middleware('permission:aktif periode');
            Route::patch('/periode/{id}/deactivate', [PeriodeController::class, 'deactivate'])->name('periode.deactivate')->middleware('permission:aktif periode');
            Route::delete('/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy')->middleware('permission:hapus periode');
        });

        Route::prefix('riwayat')->group(function () {
            Route::get('/', [RiwayatKelasController::class, 'index'])->name('riwayat.index')->middleware('permission:view riwayat');
            Route::get('/{id}/riwayat', [RiwayatKelasController::class, 'showKelasDetail'])->name('riwayat.showKelasDetail')->middleware('permission:detail riwayat kelas');
            Route::get('/riwayat/cetak-pdf/{kelas_id}/{siswa_id}/{periode_id}', [RiwayatKelasController::class, 'cetakPdfSiswa'])->name('riwayat.cetakPdfSiswa')->middleware('permission:cetak pdf');
        });

        Route::prefix('wali-kelas')->group(function () {
            Route::get('/', [WaliKelasController::class, 'index'])->name('walikelas.index')->middleware('permission:view wali-kelas');
            Route::get('/detail', [WaliKelasController::class, 'detail'])->name('walikelas.detail')->middleware('permission:motoring wali-kelas');
            Route::get('/siswa/{id}/absensi', [WaliKelasController::class, 'getDetailAbsensi'])->name('walikelas.detail.absensi');
            Route::get('/siswa/{id}/pelanggaran', [WaliKelasController::class, 'getDetailPelanggaran'])->name('walikelas.detail.pelanggaran');
        });

    });