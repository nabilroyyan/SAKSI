@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Detail Riwayat Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('riwayat.index') }}">Riwayat Kelas</a></li>
                            <li class="breadcrumb-item active">Detail Riwayat</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Kelas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title mb-3">Informasi Kelas</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Kelas</strong></td>
                                        <td>: {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: 
                                            @if($kelas->stt == 'aktif')
                                                <span class="badge bg-primary">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="text-end">
                                    <a href="{{ route('riwayat.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('riwayat.showKelasDetail', $kelas->id) }}">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label">Filter Periode</label>
                                    <select name="periode_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Periode</option>
                                        @foreach($periodes as $periode)
                                            <option value="{{ $periode->id }}" {{ $periode_id == $periode->id ? 'selected' : '' }}>
                                                {{ $periode->semester }} {{ $periode->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="mt-3">
                                        <span class="badge bg-info me-2">Total Riwayat: {{ count($dataDetail) }}</span>
                                        @if($periode_id)
                                            <span class="badge bg-success">Periode: {{ $periodes->find($periode_id)->semester }} {{ $periodes->find($periode_id)->tahun }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Riwayat Siswa -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Riwayat Siswa</h4>
                        
                        @if(count($dataDetail) > 0)
                            <div class="table-responsive">
                                <table id="tableRiwayat" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Status</th>
                                            <th>Periode</th>
                                            <th>Total Absensi</th>
                                            <th>Total Pelanggaran</th>
                                            <th>Total Tindakan</th>
                                            <th style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataDetail as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $data['siswa']->nis_nip }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $data['siswa']->nama_siswa }}</h6>
                                                        <small class="text-muted">{{ $data['siswa']->jenis_kelamin }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data['status'] == 'lulus')
                                                    <span class="badge bg-success">Lulus</span>
                                                @elseif($data['status'] == 'pindah')
                                                    <span class="badge bg-warning">Pindah</span>
                                                @elseif($data['status'] == 'keluar')
                                                    <span class="badge bg-danger">Keluar</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($data['status']) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $data['periode']->semester }} {{ $data['periode']->tahun }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ count($data['absensi']) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ count($data['pelanggaran']) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">{{ count($data['tindakan']) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @can('detail riwayat siswa')     
                                                    <button type="button" class="btn btn-primary btn-sm" 
                                                            onclick="showDetailModal({{ $index }})">
                                                        <i class="mdi mdi-eye"></i> Detail
                                                    </button>
                                                    @endcan
                                                    @can('cetak pdf')                         
                                                    <a href="{{ route('riwayat.cetakPdfSiswa', ['kelas_id' => $kelas->id, 'siswa_id' => $data['siswa']->id, 'periode_id' => $data['periode']->id]) }}" 
                                                        class="btn btn-danger btn-sm" target="_blank">
                                                        <i class="mdi mdi-file-pdf"></i> PDF
                                                    </a>
                                                    @endcan
                                                </div>
                                            </td>       
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-information-outline h1 text-muted"></i>
                                <h5 class="mt-3">Tidak Ada Data Riwayat</h5>
                                <p class="text-muted">Belum ada siswa yang tercatat dalam riwayat kelas ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Siswa -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Riwayat Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Data untuk modal
    const dataDetail = @json($dataDetail);
    
    $(document).ready(function() {
        $('#tableRiwayat').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            info: true,
            ordering: true,
            pageLength: 25,
            columnDefs: [{
                targets: [8], // kolom action
                orderable: false
            }],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });

    function showDetailModal(index) {
        const data = dataDetail[index];
        const siswa = data.siswa;
        const absensi = data.absensi;
        const pelanggaran = data.pelanggaran;
        const tindakan = data.tindakan;
        
        let modalContent = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Informasi Siswa</h6>
                    <table class="table table-borderless table-sm">
                        <tr><td width="40%"><strong>NIS</strong></td><td>: ${siswa.nis_nip}</td></tr>
                        <tr><td><strong>Nama</strong></td><td>: ${siswa.nama_siswa}</td></tr>
                        <tr><td><strong>Jenis Kelamin</strong></td><td>: ${siswa.jenis_kelamin}</td></tr>
                        <tr><td><strong>Status</strong></td><td>: ${data.status}</td></tr>
                        <tr><td><strong>Periode</strong></td><td>: ${data.periode.semester} ${data.periode.tahun}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Ringkasan Data</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <h4 class="mb-0">${absensi.length}</h4>
                                    <small>Absensi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body py-2">
                                    <h4 class="mb-0">${pelanggaran.length}</h4>
                                    <small>Pelanggaran</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <h4 class="mb-0">${tindakan.length}</h4>
                                    <small>Tindakan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="absensi-tab" data-bs-toggle="tab" data-bs-target="#absensi" 
                            type="button" role="tab" aria-controls="absensi" aria-selected="true">
                        Absensi (${absensi.length})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pelanggaran-tab" data-bs-toggle="tab" data-bs-target="#pelanggaran" 
                            type="button" role="tab" aria-controls="pelanggaran" aria-selected="false">
                        Pelanggaran (${pelanggaran.length})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tindakan-tab" data-bs-toggle="tab" data-bs-target="#tindakan" 
                            type="button" role="tab" aria-controls="tindakan" aria-selected="false">
                        Tindakan (${tindakan.length})
                    </button>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content mt-3" id="detailTabContent">
                <div class="tab-pane fade show active" id="absensi" role="tabpanel" aria-labelledby="absensi-tab">
                    ${absensi.length > 0 ? `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${absensi.map((item, idx) => `
                                        <tr>
                                            <td>${idx + 1}</td>
                                            <td>${item.hari_tanggal}</td>
                                            <td><span class="badge bg-info">${item.status}</span></td>
                                            <td>${item.catatan || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-center text-muted py-3">Tidak ada data absensi</p>'}
                </div>
                
                <div class="tab-pane fade" id="pelanggaran" role="tabpanel" aria-labelledby="pelanggaran-tab">
                    ${pelanggaran.length > 0 ? `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${pelanggaran.map((item, idx) => `
                                        <tr>
                                            <td>${idx + 1}</td>
                                            <td>${item.tanggal}</td>
                                            <td>${item.skor_pelanggaran?.nama_pelanggaran || '-'}</td>
                                            <td><span class="badge bg-warning">${item.skor_pelanggaran.skor}</span></td>
                                            <td>${item.ket_pelanggaran || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-center text-muted py-3">Tidak ada data pelanggaran</p>'}
                </div>
                
                <div class="tab-pane fade" id="tindakan" role="tabpanel" aria-labelledby="tindakan-tab">
                    ${tindakan.length > 0 ? `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Tindakan</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tindakan.map((item, idx) => `
                                        <tr>
                                            <td>${idx + 1}</td>
                                            <td>${item.tanggal}</td>
                                            <td><span class="badge bg-danger">${item.kategori_tindakan.nama_tindakan}</span></td>
                                            <td>${item.status || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-center text-muted py-3">Tidak ada data tindakan</p>'}
                </div>
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('detailModalLabel').innerHTML = `Detail Riwayat - ${siswa.nama}`;
        
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
</script>
@endpush