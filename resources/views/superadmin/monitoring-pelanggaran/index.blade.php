@extends('layout.mainlayout')

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
                        <h4 class="card-title">Filter Data</h4>
                        <form action="{{ route('monitoring-Pelanggaran.index') }}" method="GET" id="filterForm">
                           <div class="row">
                                <div class="col-md-3">
                                    <label>Nama Siswa</label>
                                    <input type="text" name="nama_siswa" class="form-control" placeholder="Cari nama siswa..." value="{{ request('nama_siswa') }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Nama Jurusan</label>
                                    <input type="text" name="jurusan" class="form-control" placeholder="Cari nama Jurusan..." value="{{ request('jurusan') }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Nama Pelanggaran</label>
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
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('monitoring-Pelanggaran.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Siswa Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Siswa Pelanggar</h4>
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
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
                                        <td>{{ $loop->iteration}}</td>
                                        <td>{{ $data['siswa']->nis_nip ?? 'N/A' }}</td>
                                        <td>{{ $data['siswa']->nama_siswa ?? 'N/A' }}</td>
                                        <td>
                                            @if($data['kelas_siswa'] && $data['kelas_siswa']->is_active == 'aktif')
                                                {{ $data['kelas_siswa']->kelas->tingkat ?? '' }}
                                                {{ $data['kelas_siswa']->kelas->jurusan->nama_jurusan ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-info">{{ $data['jumlah_pelanggaran'] }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge {{ $data['total_skor'] >= 1000 ? 'bg-danger' : ($data['total_skor'] >= 500 ? 'bg-warning' : 'bg-success') }}">
                                                {{ $data['total_skor'] }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($data['total_skor'] >= 1000)
                                                <span class="badge bg-danger">Kritis</span>
                                            @elseif($data['total_skor'] >= 500)
                                                <span class="badge bg-warning">Peringatan</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-detail"
                                                data-id="{{ $data['siswa']->id }}"
                                                data-name="{{ $data['siswa']->nama_siswa }}">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pelanggaran - <span id="namaSiswaModal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingDetail" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="detailContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
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
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Total Pelanggaran</h5>
                                        <h3 id="totalPelanggaran">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5>Total Skor</h5>
                                        <h3 id="totalSkorModal">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<!-- Pastikan Bootstrap JS sudah di-include sebelum script ini -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler untuk tombol detail
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-detail');
        if (button) {
            const siswaId = button.getAttribute('data-id');
            const namaSiswa = button.getAttribute('data-name');
            document.getElementById('namaSiswaModal').textContent = namaSiswa;

            // Modal Bootstrap 5
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();

            document.getElementById('loadingDetail').style.display = 'block';
            document.getElementById('detailContent').style.display = 'none';

            fetch(`/monitoring-pelanggaran/detail/${siswaId}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('detailTableBody');
                    tableBody.innerHTML = '';

                    if (Array.isArray(data.pelanggarans)) {
                        data.pelanggarans.forEach((item, index) => {
                            const row = `<tr>
                                <td>${index + 1}</td>
                                <td>${item.tanggal}</td>
                                <td>${item.nama_pelanggaran}</td>
                                <td>${item.skor}</td>
                                <td>${item.petugas}</td>
                                <td>${item.bukti ? `<a href="${item.bukti}" target="_blank">Lihat</a>` : '-'}</td>
                            </tr>`;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data pelanggaran</td></tr>';
                    }

                    document.getElementById('totalPelanggaran').textContent = data.total_pelanggaran ?? 0;
                    document.getElementById('totalSkorModal').textContent = data.total_skor ?? 0;

                    document.getElementById('loadingDetail').style.display = 'none';
                    document.getElementById('detailContent').style.display = 'block';
                })
                .catch(error => {
                    console.error('Gagal ambil data detail:', error);
                    alert('Gagal mengambil data detail. Coba lagi.');
                    document.getElementById('loadingDetail').style.display = 'none';
                });
        }
    });
});
</script>
@endsection

