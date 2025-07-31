@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Monitoring Absensi</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Monitoring</a></li>
                            <li class="breadcrumb-item active">Absensi</li>
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
                            @if(!$siswaAbsensi->isEmpty())
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" id="exportPdfAbsensiBtn">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            @endif

                        </div>
                        
                        <form action="{{ route('monitoring-absensi.index') }}" method="GET" id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Nama Siswa</label>
                                    <input type="text" name="nama_siswa" class="form-control" placeholder="Cari nama siswa..." value="{{ request('nama_siswa') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Kelas</label>
                                    <input type="text" name="nama_kelas" class="form-control" placeholder="Cari nama kelas..." value="{{ request('nama_kelas') }}">
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
                                    <label class="form-label">Status Surat</label>
                                    <select name="status" class="form-select">
                                        <option value="">Pilih Status</option>
                                        @foreach($statusList as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                Status {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    <a href="{{ route('monitoring-absensi.index') }}" class="btn btn-secondary">
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
        @if(!$siswaAbsensi->isEmpty())
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Total Siswa</h5>
                                <h3>{{ $siswaAbsensi->count() }}</h3>
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
                                <h5 class="card-title">Total Hadir</h5>
                                <h3>{{ $siswaAbsensi->sum('hadir') }}</h3>
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
                                <h5 class="card-title">Sakit/Izin</h5>
                                <h3>{{ $siswaAbsensi->sum('sakit') + $siswaAbsensi->sum('izin') }}</h3>
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
                                <h5 class="card-title">Total Alpa</h5>
                                <h3>{{ $siswaAbsensi->sum('alpa') }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times-circle fa-2x"></i>
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
                        <h4 class="card-title">Data Absensi Siswa</h4>
                        
                        @if($siswaAbsensi->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Tidak ada data absensi yang ditemukan dengan filter yang dipilih.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead class="">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Hadir</th>
                                            <th>Sakit</th>
                                            <th>Izin</th>
                                            <th>Alpa</th>
                                            <th>Total</th>
                                            <th>Persentase</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswaAbsensi as $data)
                                        @php
                                            $persentase = $data['total'] > 0 ? round(($data['hadir'] / $data['total']) * 100, 2) : 0;
                                            $badgeClass = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0">{{ $data['siswa']->nama_siswa ?? 'N/A' }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                                                    <span class="badge bg-secondary">{{ $data['kelas_siswa']->kelas->tingkat ?? 'N/A' }}</span>
                                                @else
                                                    N/A
                                                @endif
                                                @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                                                    {{ $data['kelas_siswa']->kelas->nama_kelas ?? '' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>   
                                            <td class="text-center align-middle">
                                                <span class="badge bg-success fs-6">{{ $data['hadir'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-info fs-6">{{ $data['sakit'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-warning fs-6">{{ $data['izin'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-danger fs-6">{{ $data['alpa'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-secondary fs-6">{{ $data['total'] }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-{{ $badgeClass }} fs-6">{{ $persentase }}%</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">        
                                                            <button type="button" class="btn btn-sm btn-info btn-absen"
                                                                data-id="{{ $data['siswa']->id }}"
                                                                data-name="{{ $data['siswa']->nama_siswa }}">
                                                                <i class="fas fa-calendar-check"></i>
                                                            </button>
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


<!-- Modal -->
<div class="modal fade" id="modalAbsensi" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
      <div>
        <h5 class="modal-title mb-0">Detail Absensi: <span id="namaSiswa"></span></h5>
        <small class="text-muted">NIS: <span id="nisSiswa"></span></small>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
      <div class="modal-body">
        <p>Total Absen: <span id="totalAbsen"></span></p>
        <p>Hadir: <span id="hadir"></span> | Sakit: <span id="sakit"></span> | Izin: <span id="izin"></span> | Alpa: <span id="alpa"></span></p>
        <div id="loading" class="text-center">Memuat...</div>
        <div id="detailList" class="table-responsive d-none">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Surat</th>
                <th>Catatan</th>
                <th>Petugas</th>
                <th>Bukti</th>
              </tr>
            </thead>
            <tbody id="absensiDetailBody"></tbody>
          </table>
        </div>
        <div id="errorLoad" class="alert alert-danger d-none">Gagal memuat data</div>
      </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times"></i> Tutup
            </button>
            <button type="button" class="btn btn-success" id="btnExportDetailAbsensi">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>
  </div>
</div>




@endsection

@push('scripts')
<script>
$(document).on('click', '.btn-absen', function () {
    const id = $(this).data('id');
    const nama = $(this).data('name');

    $('#namaSiswa').text(nama);
    $('#modalAbsensi').data('siswa-id', id); 
    $('#modalAbsensi').modal('show');
    $('#loading').removeClass('d-none');
    $('#detailList').addClass('d-none');
    $('#errorLoad').addClass('d-none');
    $('#absensiDetailBody').empty();
    $('#totalAbsen').text('-');
    $('#hadir').text('-');
    $('#sakit').text('-');
    $('#izin').text('-');
    $('#alpa').text('-');

  $.ajax({
    url: "{{ route('monitoring-absensi.detail', ':id') }}".replace(':id', id),
    method: 'GET',
    success: function (response) {
        $('#loading').addClass('d-none');
        $('#errorLoad').addClass('d-none');
        $('#detailList').removeClass('d-none');

        $('#totalAbsen').text(response.total_absen);
        $('#hadir').text(response.hadir);
        $('#sakit').text(response.sakit);
        $('#izin').text(response.izin);
        $('#alpa').text(response.alpa);

        if (response.detail_absensi.length > 0) {
            response.detail_absensi.forEach(item => {
                $('#absensiDetailBody').append(`
                    <tr>
                        <td>${item.tanggal}</td>
                        <td>${item.status}</td>
                        <td>${item.status_surat ?? '-'}</td>
                        <td>${item.catatan}</td>
                        <td>${item.petugas}</td>
                        <td>${item.bukti ? `<a href="${item.bukti}" target="_blank">Lihat</a>` : '-'}</td>
                    </tr>
                `);
            });
        } else {
            $('#absensiDetailBody').append('<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
        }
    },
    error: function (xhr) {
        $('#loading').addClass('d-none');
        $('#detailList').addClass('d-none');
        $('#errorLoad').removeClass('d-none').text("Gagal memuat data: " + xhr.statusText);
        console.log(xhr.responseText); // debug
    }
});

});

$(document).on('click', '#btnExportDetailAbsensi', function () {
    const siswaId = $('#modalAbsensi').data('siswa-id');
    const query = new URLSearchParams(window.location.search).toString();
    window.open(`/monitoring-absensi/pdf-detail/${siswaId}?` + query, '_blank');
});

$('#exportPdfAbsensiBtn').click(function () {
    const params = new URLSearchParams(window.location.search);
    window.open(`/monitoring-absensi/pdf-absensi?${params.toString()}`, '_blank');
});
</script>
@endpush


