@extends('layout.MainLayout')
@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Superadmin</h1>
        <div class="alert alert-info">
            Periode Aktif: <strong>{{ $periodeAktif->tahun }} - Semester {{ ucfirst($periodeAktif->semester) }}</strong>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row">
        <!-- Total Siswa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSiswa }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Kelas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKelas }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total TATIP</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGuru }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total BK -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total BK</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBK }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Data -->
    <div class="row">
        <!-- Grafik Pelanggaran -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pelanggaran Tahun Ini</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="pelanggaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Pelanggaran -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Pelanggaran</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">Pelanggaran Bulan Ini <span class="float-right">{{ $pelanggaranBulanIni }}</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: {{ min($pelanggaranBulanIni, 100) }}%" 
                             aria-valuenow="{{ $pelanggaranBulanIni }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                    <div class="mb-1">
                        <span class="text-sm {{ $persentasePelanggaran > 0 ? 'text-danger' : 'text-success' }}">
                            <i class="fas {{ $persentasePelanggaran > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($persentasePelanggaran) }}% dari bulan lalu
                        </span>
                    </div>
                    <hr>
                    <h6 class="font-weight-bold">Absensi Hari Ini</h6>
                    <div class="mt-3">
                        <span class="text-success"><i class="fas fa-circle"></i> Hadir: {{ $absensiHariIni['hadir'] ?? 0 }}</span><br>
                        <span class="text-warning"><i class="fas fa-circle"></i> Izin: {{ $absensiHariIni['izin'] ?? 0 }}</span><br>
                        <span class="text-info"><i class="fas fa-circle"></i> Sakit: {{ $absensiHariIni['sakit'] ?? 0 }}</span><br>
                        <span class="text-danger"><i class="fas fa-circle"></i> Alpa: {{ $absensiHariIni['alpa'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="row">
        <!-- Pelanggaran Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pelanggaran Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Pelanggaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggaranTerbaru as $pelanggaran)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $pelanggaran->siswa->nama_siswa }}</td>
                                    <td>{{ $pelanggaran->Skor_Pelanggaran->nama_pelanggaran }}</td>
                                    <td>
                                      <span class="badge badge-danger" style=" background-color: transparent; color: #e74a3b; border: 1px solid #e74a3b;">
                                        Baru
                                      </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data pelanggaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absensi Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ketidakhadiran Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensiTerbaru as $absensi)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($absensi->hari_tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $absensi->siswa->nama_siswa }}</td>
                                    <td>
                                        @if($absensi->status == 'sakit')
                                            <span class="badge badge-info">Sakit</span>
                                        @elseif($absensi->status == 'izin')
                                            <span class="badge badge-warning">Izin</span>
                                        @else
                                            <span class="badge badge-danger">Alpa</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($absensi->catatan, 30) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data ketidakhadiran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Grafik Pelanggaran
var ctx = document.getElementById('pelanggaranChart').getContext('2d');
var pelanggaranChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Jumlah Pelanggaran',
            data: [
                @foreach(range(1,12) as $month)
                    {{ $pelanggaranPerBulan->firstWhere('bulan', $month)->total ?? 0 }},
                @endforeach
            ],
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderColor: 'rgba(78, 115, 223, 1)',
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 2
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Grafik Absensi (jika ingin menambahkan)
// ...
</script>
@endsection