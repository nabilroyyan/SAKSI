@extends('layout.mainlayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monitoring Absensi Siswa</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('monitoring-absensi.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nama_siswa">Nama Siswa</label>
                                    <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" 
                                           value="{{ request('nama_siswa') }}" placeholder="Cari nama siswa...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="jurusan">Jurusan</label>
                                    <input type="text" class="form-control" id="jurusan" name="jurusan" 
                                           value="{{ request('jurusan') }}" placeholder="Cari jurusan...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                           value="{{ request('tanggal') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="bulan">Bulan</label>
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Semua Bulan</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tahun">Tahun</label>
                                    <select class="form-control" id="tahun" name="tahun">
                                        <option value="">Semua Tahun</option>
                                        @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('monitoring-absensi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset Filter
                                </a>
                                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $siswaAbsensi->count() }}</h4>
                                            <p class="mb-0">Total Siswa</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $siswaAbsensi->sum('hadir') }}</h4>
                                            <p class="mb-0">Total Hadir</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $siswaAbsensi->sum('sakit') + $siswaAbsensi->sum('izin') }}</h4>
                                            <p class="mb-0">Total Sakit/Izin</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $siswaAbsensi->sum('alpa') }}</h4>
                                            <p class="mb-0">Total Alpa</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="absensiTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jurusan</th>
                                    <th>Hadir</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alpa</th>
                                    <th>Total</th>
                                    <th>Persentase Kehadiran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswaAbsensi as $index => $data)
                                    @php
                                        $persentase = $data['total'] > 0 ? round(($data['hadir'] / $data['total']) * 100, 2) : 0;
                                        $badgeClass = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data['siswa']->nama_siswa }}</td>
                                        <td>{{ $data['kelas_siswa']->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ $data['kelas_siswa']->kelas->jurusan->nama_jurusan ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-success">{{ $data['hadir'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $data['sakit'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">{{ $data['izin'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">{{ $data['alpa'] }}</span>
                                        </td>
                                        <td>{{ $data['total'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $badgeClass }}">{{ $persentase }}%</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showDetail({{ $data['siswa']->id }})"
                                                    data-toggle="tooltip" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data absensi</td>
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
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Absensi - <span id="modalNamaSiswa"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h4 id="modalHadir">0</h4>
                                <p class="mb-0">Hadir</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h4 id="modalSakit">0</h4>
                                <p class="mb-0">Sakit</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white text-center">
                            <div class="card-body">
                                <h4 id="modalIzin">0</h4>
                                <p class="mb-0">Izin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body">
                                <h4 id="modalAlpa">0</h4>
                                <p class="mb-0">Alpa</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Status Surat</th>
                                <th>Catatan</th>
                                <th>Petugas</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="detailAbsensiTable">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bukti Surat -->
<div class="modal fade" id="buktiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Surat</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="buktiImage" src="" alt="Bukti Surat" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .card {
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        border: none;
    }
    
    .badge {
        font-size: 0.8em;
        padding: 0.4em 0.6em;
    }
    
    .table th {
        background-color: #343a40;
        color: white;
        border-color: #454d55;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .summary-card {
        transition: transform 0.2s;
    }
    
    .summary-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#absensiTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "ordering": true,
        "info": true,
        "paging": true,
        "searching": false, // Disable default search since we have custom filters
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

function showDetail(siswaId) {
    $.ajax({
        url: `/monitoring-absensi/detail/${siswaId}`,
        type: 'GET',
        success: function(response) {
            $('#modalNamaSiswa').text(response.nama);
            $('#modalHadir').text(response.hadir);
            $('#modalSakit').text(response.sakit);
            $('#modalIzin').text(response.izin);
            $('#modalAlpa').text(response.alpa);
            
            let tableRows = '';
            response.detail_absensi.forEach(function(item) {
                let statusBadge = getStatusBadge(item.status);
                let statusSuratBadge = item.status_surat ? getStatusSuratBadge(item.status_surat) : '-';
                let buktiButton = item.bukti ? 
                    `<button type="button" class="btn btn-sm btn-info" onclick="showBukti('${item.bukti}')">
                        <i class="fas fa-image"></i> Lihat
                    </button>` : '-';
                
                tableRows += `
                    <tr>
                        <td>${formatDate(item.tanggal)}</td>
                        <td>${statusBadge}</td>
                        <td>${statusSuratBadge}</td>
                        <td>${item.catatan}</td>
                        <td>${item.petugas}</td>
                        <td>${buktiButton}</td>
                    </tr>
                `;
            });
            
            $('#detailAbsensiTable').html(tableRows);
            $('#detailModal').modal('show');
        },
        error: function() {
            alert('Gagal memuat detail absensi');
        }
    });
}

function getStatusBadge(status) {
    const badges = {
        'hadir': '<span class="badge badge-success">Hadir</span>',
        'sakit': '<span class="badge badge-info">Sakit</span>',
        'izin': '<span class="badge badge-warning">Izin</span>',
        'alpa': '<span class="badge badge-danger">Alpa</span>'
    };
    return badges[status] || status;
}

function getStatusSuratBadge(status) {
    const badges = {
        'diterima': '<span class="badge badge-success">Diterima</span>',
        'ditolak': '<span class="badge badge-danger">Ditolak</span>',
        'pending': '<span class="badge badge-warning">Pending</span>'
    };
    return badges[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function showBukti(imageUrl) {
    $('#buktiImage').attr('src', imageUrl);
    $('#buktiModal').modal('show');
}

function exportToExcel() {
    let params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.location.href = `{{ route('monitoring-absensi.index') }}?${params.toString()}`;
}
</script>
@endpush