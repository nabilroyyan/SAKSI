@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard Wali Kelas</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Wali Kelas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(!$kelas)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-soft-warning text-warning rounded-circle">
                                    <i class="mdi mdi-school-outline display-4"></i>
                                </div>
                            </div>
                            <h5 class="text-muted">Anda belum menjadi wali kelas manapun</h5>
                            <p class="text-muted">Silakan hubungi administrator untuk mendapatkan tugas sebagai wali kelas.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Kelas Info Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg me-3">
                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                        <i class="mdi mdi-school display-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>
                                    <p class="text-muted mb-0">{{ $kelas->jurusan->nama_jurusan }}</p>
                                    <small class="text-muted">Total Siswa: {{ $totalSiswa }} • Aktif: {{ $siswaAktif }}</small>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('walikelas.detail') }}" class="btn btn-primary">
                                        <i class="mdi mdi-eye me-1"></i>
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Absensi Hari Ini -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-calendar-today text-primary me-2"></i>
                                Statistik Absensi Hari Ini
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-success border-success">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-success">
                                                    <i class="mdi mdi-check font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-success mb-1">{{ $statistikAbsensi['hadir'] }}</h5>
                                            <p class="text-success mb-0 font-size-12">Hadir</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-info border-info">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-info">
                                                    <i class="mdi mdi-medical-bag font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-info mb-1">{{ $statistikAbsensi['sakit'] }}</h5>
                                            <p class="text-info mb-0 font-size-12">Sakit</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-warning border-warning">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-warning">
                                                    <i class="mdi mdi-file-document font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-warning mb-1">{{ $statistikAbsensi['izin'] }}</h5>
                                            <p class="text-warning mb-0 font-size-12">Izin</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-danger border-danger">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-danger">
                                                    <i class="mdi mdi-close font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-danger mb-1">{{ $statistikAbsensi['alpa'] }}</h5>
                                            <p class="text-danger mb-0 font-size-12">Alpa</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-secondary border-secondary">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-secondary">
                                                    <i class="mdi mdi-clock-outline font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-secondary mb-1">{{ $statistikAbsensi['belum_absen'] }}</h5>
                                            <p class="text-secondary mb-0 font-size-12">Belum Absen</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card bg-soft-primary border-primary">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-2">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="mdi mdi-account-group font-size-16"></i>
                                                </span>
                                            </div>
                                            <h5 class="text-primary mb-1">{{ $totalSiswa }}</h5>
                                            <p class="text-primary mb-0 font-size-12">Total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Siswa -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">
                                        <i class="mdi mdi-account-group text-primary me-2"></i>
                                        Daftar Siswa
                                    </h5>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('walikelas.detail') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-eye me-1"></i>
                                        Lihat Detail Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kelas->kelasSiswa->where('is_active', 'aktif') as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-medium">{{ $item->siswa->nis_nip }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                {{ substr($item->siswa->nama_siswa, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $item->siswa->nama_siswa }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item->siswa->jenis_kelamin == 'L')
                                                        <span class="badge bg-soft-info text-info">Laki-laki</span>
                                                    @else
                                                        <span class="badge bg-soft-warning text-warning">Perempuan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-success text-success">Aktif</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="mdi mdi-account-off display-6 mb-3"></i>
                                                        <p>Belum ada siswa aktif di kelas ini</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-soft-primary { background-color: rgba(116, 120, 242, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }

.table-hover tbody tr:hover {
    background-color: rgba(116, 120, 242, 0.05);
}
</style>
@endsection