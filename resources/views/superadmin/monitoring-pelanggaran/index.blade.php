@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Monitoring Pelanggaran</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Monitoring</a></li>
                            <li class="breadcrumb-item active">Pelanggaran</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Filter Data</h4>
                            @if(!$siswaPelanggar->isEmpty())
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" id="exportPdfBtn">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <form action="{{ route('monitoring-Pelanggaran.index') }}" method="GET" id="filterForm">
                           <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Nama Siswa</label>
                                    <input type="text" name="nama_siswa" class="form-control" placeholder="Cari nama siswa..." value="{{ request('nama_siswa') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Jurusan</label>
                                    <input type="text" name="jurusan" class="form-control" placeholder="Cari nama Jurusan..." value="{{ request('jurusan') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tingkat Kelas</label>
                                    <select name="tingkat" class="form-select">
                                        <option value="">Pilih Tingkat</option>
                                        @foreach($tingkatList as $tingkat)
                                            <option value="{{ $tingkat }}" {{ request('tingkat') == $tingkat ? 'selected' : '' }}>
                                                Tingkat {{ $tingkat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Pelanggaran</label>
                                    <input type="text" name="nama_pelanggaran" class="form-control" placeholder="Cari nama pelanggaran..." value="{{ request('nama_pelanggaran') }}">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="bulan" class="form-label">Bulan</label>
                                        <select class="form-select" id="bulan" name="bulan">
                                            <option value="">Pilih Bulan</option>
                                            @foreach(range(1, 12) as $month)
                                                <option value="{{ $month }}" {{ request('bulan') == $month ? 'selected' : '' }}>
                                                    {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="tahun" class="form-label">Tahun</label>
                                        <select class="form-select" id="tahun" name="tahun">
                                            <option value="">Pilih Tahun</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Periode</label>
                                    <select name="periode_id" class="form-select">
                                        @foreach($periodes as $periode)
                                            <option value="{{ $periode->id }}"
                                                {{ $selectedPeriodeId == $periode->id ? 'selected' : '' }}>
                                                {{ $periode->tahun }} - {{ ucfirst($periode->semester) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('monitoring-Pelanggaran.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        @if(!$siswaPelanggar->isEmpty())
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Total Siswa</h5>
                                <h3>{{ $siswaPelanggar->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Total Pelanggaran</h5>
                                <h3>{{ $pelanggaran->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                                <h5 class="card-title">Status Peringatan</h5>
                                <h3>{{ $siswaPelanggar->filter(fn($s) => $s['total_skor'] >= 500 && $s['total_skor'] < 1000)->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation fa-2x"></i>
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
                                <h5 class="card-title">Status Kritis</h5>
                                <h3>{{ $siswaPeringatan->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-ban fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Siswa Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Siswa Pelanggar</h4>
                        
                        @if($siswaPelanggar->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Tidak ada data pelanggaran yang ditemukan dengan filter yang dipilih.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Tingkat</th>
                                            <th>Kelas</th>
                                            <th>Jumlah Pelanggaran</th>
                                            <th>Total Skor</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswaPelanggar as $id_siswa => $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data['siswa']->nis_nip ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                        <h6 class="mb-0">{{ $data['siswa']->nama_siswa ?? 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                             <td>
                                                @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                                                    <span class="badge bg-secondary">{{ $data['kelas_siswa']->kelas->tingkat ?? 'N/A' }}</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                                                    {{ $data['kelas_siswa']->kelas->nama_kelas ?? '' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                           
                                            <td class="text-center align-middle">
                                                <span class="badge bg-info fs-6">{{ $data['jumlah_pelanggaran'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge fs-6 {{ $data['total_skor'] >= 1000 ? 'bg-danger' : ($data['total_skor'] >= 500 ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $data['total_skor'] }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($data['total_skor'] >= 1000)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-ban"></i> Kritis
                                                    </span>
                                                @elseif($data['total_skor'] >= 500)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation"></i> Peringatan
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Normal
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @can('detail monitoring-pelanggaran')         
                                                        <button type="button" class="btn btn-sm btn-primary btn-detail"
                                                        data-id="{{ $data['siswa']->id }}"
                                                        data-name="{{ $data['siswa']->nama_siswa }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pelanggaran -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-list-alt"></i> Detail Pelanggaran - <span id="namaSiswaModal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingDetail" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
                <div id="detailContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggaran</th>
                                    <th>Skor</th>
                                    <th>Petugas</th>
                                    <th>Bukti</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                                <!-- Data akan diisi melalui JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-gradient-info text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                        <h5>Total Pelanggaran</h5>
                                        <h2 id="totalPelanggaran">0</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-gradient-danger text-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calculator fa-2x mb-2"></i>
                                        <h5>Total Skor</h5>
                                        <h2 id="totalSkorModal">0</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <button type="button" class="btn btn-success" id="btnExportDetailFromModal">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-detail').on('click', function () {
        const siswaId = $(this).data('id');
        const nama = $(this).data('name');

        $('#namaSiswaModal').text(nama);
        $('#loadingDetail').show();
        $('#detailContent').hide();
        $('#detailTableBody').html('');
        $('#totalPelanggaran').text('0');
        $('#totalSkorModal').text('0');

        $('#detailModal').modal('show');

        $.ajax({
            url: `/monitoring-pelanggaran/detail/${siswaId}`,
            type: 'GET',
            success: function(response) {
                $('#loadingDetail').hide();
                $('#detailContent').show();

                let rows = '';
                response.pelanggarans.forEach((p, i) => {
                    rows += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${p.tanggal}</td>
                            <td>${p.nama_pelanggaran}</td>
                            <td>${p.skor}</td>
                            <td>${p.petugas}</td>
                            <td>${p.bukti ? `<a href="${p.bukti}" target="_blank">Lihat</a>` : '-'}</td>
                        </tr>
                    `;
                });

                $('#detailTableBody').html(rows);
                $('#totalPelanggaran').text(response.total_pelanggaran);
                $('#totalSkorModal').text(response.total_skor);

                $('#btnExportDetailFromModal').off().on('click', function () {
                    window.open(`/monitoring-pelanggaran/pdf-detail/${siswaId}`, '_blank');
                });
            },
            error: function(xhr) {
                $('#loadingDetail').hide();
                $('#detailModal').modal('hide');
                Swal.fire('Gagal', 'Tidak bisa memuat detail pelanggaran', 'error');
            }
        });
    });

    $('#exportPdfBtn').on('click', function () {
        const params = $('#filterForm').serialize();
        window.open(`/monitoring-pelanggaran/pdf?${params}`, '_blank');
    });
});
</script>
@endpush


@section('styles')
<style>
.avatar-sm {
    height: 2rem;
    width: 2rem;
}

.avatar-title {
    align-items: center;
    background-color: #556ee6;
    color: #fff;
    display: flex;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #20c997) !important;
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #fd7e14) !important;
}

.card-body .fa-2x {
    opacity: 0.8;
}

.table th {
    font-weight: 600;
    border-color: #dee2e6;
}

.badge {
    font-weight: 500;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .btn-group .btn {
        margin-right: 0;
        width: 100%;
    }
}
</style>
@endsection