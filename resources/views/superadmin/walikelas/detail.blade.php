@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Detail Siswa Kelas</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('walikelas.index') }}">Wali Kelas</a></li>
                            <li class="breadcrumb-item active">Detail Siswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelas Info -->
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
                                <small class="text-muted">Total Siswa: {{ $dataSiswa->count() }}</small>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('walikelas.index') }}" class="btn btn-secondary me-2">
                                    <i class="mdi mdi-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabel Detail Siswa -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">
                                    <i class="mdi mdi-account-group text-primary me-2"></i>
                                    Detail Siswa
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0" id="siswaTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Kehadiran</th>
                                        <th>Statistik Absensi</th>
                                        <th>Pelanggaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dataSiswa as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="text-primary">
                                                            {{ substr($data['siswa']->nama_siswa, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $data['siswa']->nama_siswa }}</h6>
                                                        <small class="text-muted">{{ $data['siswa']->nis_nip }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $data['status_kehadiran']['class'] }}" 
                                                             style="width: {{ $data['persentase_kehadiran'] }}%"></div>
                                                    </div>
                                                    <span class="ms-2 fw-medium text-{{ $data['status_kehadiran']['class'] }}">
                                                        {{ $data['persentase_kehadiran'] }}%
                                                    </span>
                                                </div>
                                                <small class="text-muted">{{ $data['status_kehadiran']['status'] }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <span class="badge bg-success" title="Hadir">H: {{ $data['statistik_absensi']['hadir'] }}</span>
                                                    <span class="badge bg-info" title="Sakit">S: {{ $data['statistik_absensi']['sakit'] }}</span>
                                                    <span class="badge bg-warning" title="Izin">I: {{ $data['statistik_absensi']['izin'] }}</span>
                                                    <span class="badge bg-danger" title="Alpa">A: {{ $data['statistik_absensi']['alpa'] }}</span>
                                                </div>
                                                @if($data['absensi_terakhir'])
                                                    <small class="text-muted d-block mt-1">
                                                        Terakhir: {{ \Carbon\Carbon::parse($data['absensi_terakhir']->hari_tanggal)->format('d/m/Y') }}
                                                        <span class="badge bg-soft-{{ $data['absensi_terakhir']->status == 'hadir' ? 'success' : ($data['absensi_terakhir']->status == 'sakit' ? 'info' : ($data['absensi_terakhir']->status == 'izin' ? 'warning' : 'danger')) }}">
                                                            {{ ucfirst($data['absensi_terakhir']->status) }}
                                                        </span>
                                                    </small>
                                                @else
                                                    <small class="text-muted">Belum ada data absensi</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-danger">{{ $data['total_pelanggaran'] }} kasus</span>
                                                    @if($data['total_skor_pelanggaran'] > 0)
                                                        <span class="badge bg-dark">{{ $data['total_skor_pelanggaran'] }} poin</span>
                                                    @endif
                                                </div>
                                                @if($data['total_skor_pelanggaran'] > 0)
                                                    @php
                                                        $tingkat = $data['total_skor_pelanggaran'] <= 25 ? ['Ringan', 'info'] : 
                                                                  ($data['total_skor_pelanggaran'] <= 50 ? ['Sedang', 'warning'] : 
                                                                  ($data['total_skor_pelanggaran'] <= 100 ? ['Berat', 'danger'] : ['Sangat Berat', 'dark']));
                                                    @endphp
                                                    <small class="text-{{ $tingkat[1] }}">{{ $tingkat[0] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" 
                                                               onclick="showAbsensiDetail({{ $data['siswa']->id }})">
                                                                <i class="mdi mdi-calendar-check text-success me-2"></i>
                                                                Detail Absensi
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" 
                                                               onclick="showPelanggaranDetail({{ $data['siswa']->id }})">
                                                                <i class="mdi mdi-alert-circle text-danger me-2"></i>
                                                                Detail Pelanggaran
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-account-off display-6 mb-3"></i>
                                                    <p>Tidak ada data siswa yang ditemukan</p>
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
    </div>
</div>

<!-- Modal Detail Absensi -->
<div class="modal fade" id="absensiModal" tabindex="-1" aria-labelledby="absensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absensiModalLabel">
                    <i class="mdi mdi-calendar-check text-success me-2"></i>
                    Detail Absensi Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="absensiContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pelanggaran -->
<div class="modal fade" id="pelanggaranModal" tabindex="-1" aria-labelledby="pelanggaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pelanggaranModalLabel">
                    <i class="mdi mdi-alert-circle text-danger me-2"></i>
                    Detail Pelanggaran Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pelanggaranContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showAbsensiDetail(siswaId) {
    $('#absensiModal').modal('show');
    
    fetch(`{{ url('wali-kelas/siswa') }}/${siswaId}/absensi`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h6 class="mb-1">${data.nama}</h6>
                            <p class="text-muted mb-0">NIS: ${data.nis}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-soft-primary text-primary fs-6">${data.persentase_kehadiran}% Kehadiran</span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-3">
                            <div class="text-center">
                                <h5 class="text-success">${data.statistik.hadir}</h5>
                                <small class="text-muted">Hadir</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <h5 class="text-info">${data.statistik.sakit}</h5>
                                <small class="text-muted">Sakit</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <h5 class="text-warning">${data.statistik.izin}</h5>
                                <small class="text-muted">Izin</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <h5 class="text-danger">${data.statistik.alpa}</h5>
                                <small class="text-muted">Alpa</small>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Riwayat Absensi (30 Hari Terakhir)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th>Surat</th>
                                    <th>Petugas</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                if (data.detail_absensi.length > 0) {
                    data.detail_absensi.forEach(item => {
                        let statusClass = item.status === 'hadir' ? 'success' : 
                                         (item.status === 'sakit' ? 'info' : 
                                         (item.status === 'izin' ? 'warning' : 'danger'));
                        
                        html += `
                            <tr>
                                <td>${item.tanggal}</td>
                                <td>${item.hari}</td>
                                <td><span class="badge bg-${statusClass}">${item.status.toUpperCase()}</span></td>
                                <td>${item.status_surat || '-'}</td>
                                <td>${item.petugas}</td>
                                <td>${item.waktu_input}</td>
                            </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="6" class="text-center text-muted">Belum ada data absensi</td></tr>';
                }
                
                html += '</tbody></table></div>';
                
                document.getElementById('absensiContent').innerHTML = html;
            } else {
                document.getElementById('absensiContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        Gagal memuat data absensi
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('absensiContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    Terjadi kesalahan saat memuat data
                </div>`;
        });
}

function showPelanggaranDetail(siswaId) {
    $('#pelanggaranModal').modal('show');
    
    fetch(`{{ url('wali-kelas/siswa') }}/${siswaId}/pelanggaran`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let tingkatClass = data.total_skor <= 25 ? 'info' : 
                                 (data.total_skor <= 50 ? 'warning' : 
                                 (data.total_skor <= 100 ? 'danger' : 'dark'));
                
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h6 class="mb-1">${data.nama}</h6>
                            <p class="text-muted mb-0">NIS: ${data.nis}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-${tingkatClass} fs-6">${data.total_skor} Poin</span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-danger">${data.total_pelanggaran}</h5>
                                <small class="text-muted">Total Pelanggaran</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-${tingkatClass}">${data.total_skor}</h5>
                                <small class="text-muted">Total Skor</small>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Riwayat Pelanggaran (20 Terakhir)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelanggaran</th>
                                    <th>Skor</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                if (data.pelanggarans.length > 0) {
                    data.pelanggarans.forEach(item => {
                        let skorClass = item.skor <= 10 ? 'info' : 
                                       (item.skor <= 25 ? 'warning' : 'danger');
                        
                        html += `
                            <tr>
                                <td>
                                    ${item.tanggal}<br>
                                    <small class="text-muted">${item.hari}</small>
                                </td>
                                <td>
                                    <strong>${item.nama_pelanggaran}</strong><br>
                                    <small class="text-muted">${item.kategori}</small>
                                </td>
                                <td>
                                    <span class="badge bg-${skorClass}">${item.skor}</span>
                                </td>
                                <td>${item.petugas}</td>
                            </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="4" class="text-center text-muted">Tidak ada pelanggaran</td></tr>';
                }
                
                html += '</tbody></table></div>';
                
                document.getElementById('pelanggaranContent').innerHTML = html;
            } else {
                document.getElementById('pelanggaranContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        Gagal memuat data pelanggaran
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('pelanggaranContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    Terjadi kesalahan saat memuat data
                </div>`;
        });
}
</script>

<style>
.badge {
    font-size: 0.75em;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-hover tbody tr:hover {
    background-color: rgba(116, 120, 242, 0.05);
}

.modal .table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.bg-soft-primary { background-color: rgba(116, 120, 242, 0.1); }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
</style>
@endsection