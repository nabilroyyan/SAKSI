{{-- resources/views/superadmin/tindakan-siswa/index.blade.php --}}
@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
                <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Tindakan</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data Tindakan</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
               
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                 <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="filter-container">
                                </div>
                                @can('update batas skor')                                    
                                <form action="{{ route('pengaturan-tindakan.update') }}" method="POST">
                                    @csrf
                                    <label for="batas_skor">Batas Skor Tindakan</label>
                                    <input type="number" name="batas_skor"
                                    value="{{ old('batas_skor', $pengaturan->batas_skor ?? '') }}" 
                                    class="form-control" style="width: 150px; display: inline-block;"
                                    min="1" required>
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                </form>
                                @endcan
                            </div>

                    @if($siswaTindakan->count() > 0)
                         <h4 class="card-title"></h4>
                            Table Tindakan Siswa (Batas Skor: {{ $pengaturan->batas_skor ?? '-' }})
                        </h4>
                             <table id="tabletindakan" class="table table-bordered dt-responsive nowrap w-100">
                                <thead class="">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 20%">Nama Siswa</th>
                                        <th style="width: 20%">Kelas</th>
                                        <th style="width: 15%">Total Skor</th>
                                        <th style="width: 10%">Tindakan</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaTindakan as $index => $siswa)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $siswa->nama_siswa }}</strong>
                                            </td>
                                            <td>
                                                <span class="">{{ $siswa->tingkat }} {{ $siswa->nama_kelas }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $siswa->total_skor }} Poin
                                                </span>
                                            </td>
                                            <td>
                                                @if($siswa->ada_tindakan_belum)
                                                    <span class="badge bg-warning text-dark">⚠ Belum Ditindaklanjuti</span>
                                                @else
                                                    <span class="badge bg-success">✅ Tuntas</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $siswa->siswa_id }}" title="Detail Siswa">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                        <a href="{{ route('tindakan-siswa.create', ['siswa_id' => $siswa->siswa_id, 'kelas_siswa_id' => $siswa->kelas_siswa_id]) }}" class="btn btn-success btn-sm" title="Tambah Tindakan">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                </div>
                                            </td>   
                                        </tr>

                                        <!-- Modal Detail -->
                                        <!-- Modal Detail -->
                                        <div class="modal fade" id="detailModal{{ $siswa->siswa_id }}" tabindex="-1">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-user me-2"></i>
                                                            Detail Siswa: {{ $siswa->nama_siswa }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <!-- Informasi Siswa -->
                                                            <div class="col-md-5">
                                                                <div class="card border-primary">
                                                                    <div class="card-header bg-light">
                                                                        <h6 class="card-title mb-0 text-primary">
                                                                            <i class="fas fa-user-graduate me-2"></i>
                                                                            Informasi Siswa
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row mb-2">
                                                                            <div class="col-4"><strong>ID:</strong></div>
                                                                            <div class="col-8">{{ $siswa->nis_nip }}</div>
                                                                        </div>
                                                                        <div class="row mb-2">
                                                                            <div class="col-4"><strong>Nama:</strong></div>
                                                                            <div class="col-8">{{ $siswa->nama_siswa }}</div>
                                                                        </div>
                                                                        <div class="row mb-2">
                                                                            <div class="col-4"><strong>Kelas:</strong></div>
                                                                            <div class="col-8">{{ $siswa->tingkat }} {{ $siswa->nama_kelas }}</div>
                                                                        </div>
                                                                        <div class="row mb-2">
                                                                            <div class="col-4"><strong>Jurusan:</strong></div>
                                                                            <div class="col-8">{{ $siswa->nama_jurusan ?? '-' }}</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-4"><strong>Total Skor:</strong></div>
                                                                            <div class="col-8">
                                                                                <span class="badge bg-danger fs-6">
                                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                                    {{ $siswa->total_skor }} Poin
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Riwayat Tindakan -->
                                                            <div class="col-md-7">
                                                                <div class="card border-warning">
                                                                    <div class="card-header bg-light">
                                                                        <h6 class="card-title mb-0 text-warning">
                                                                            <i class="fas fa-history me-2"></i>
                                                                            Riwayat Tindakan
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        @php
                                                                            $riwayatTindakan = \App\Models\TindakanSiswa::where('id_siswa', $siswa->siswa_id)
                                                                                            ->where('kelas_siswa_id', $siswa->kelas_siswa_id)
                                                                                            ->with('kategoriTindakan')
                                                                                            ->orderBy('created_at', 'desc')
                                                                                            ->get();
                                                                        @endphp
                                                                        
                                                                        @if($riwayatTindakan->count() > 0)
                                                                            <div style="max-height: 300px; overflow-y: auto;">
                                                                                @foreach($riwayatTindakan as $index => $riwayat)
                                                                                    <div class="border rounded p-3 mb-3 {{ $riwayat->status == 'sudah' ? 'border-success bg-light' : 'border-warning' }}">
                                                                                        <!-- Header Tindakan -->
                                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                            <div>
                                                                                                <small class="text-muted">
                                                                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                                                                    {{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}
                                                                                                </small>
                                                                                            </div>
                                                                                            <span class="badge {{ $riwayat->status == 'sudah' ? 'bg-success' : 'bg-warning text-dark' }} px-3">
                                                                                                <i class="fas {{ $riwayat->status == 'sudah' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                                                                                                {{ $riwayat->status == 'sudah' ? 'Selesai' : 'Belum Selesai' }}
                                                                                            </span>
                                                                                        </div>

                                                                                        <!-- Konten Tindakan -->
                                                                                        <div class="mb-3">
                                                                                            <h6 class="mb-1">
                                                                                                <i class="fas fa-clipboard-list me-1"></i>
                                                                                                {{ $riwayat->kategoriTindakan->nama_tindakan ?? 'Tindakan Khusus' }}
                                                                                            </h6>
                                                                                            <p class="mb-2 text-muted">
                                                                                                {{ $riwayat->catatan ?? 'Tidak ada catatan khusus' }}
                                                                                            </p>
                                                                                        </div>

                                                                                        <!-- Aksi Tindakan -->
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            @if($riwayat->status === 'belum')
                                                                                                <!-- Form Upload Bukti -->
                                                                                                <div class="flex-grow-1 me-3">
                                                                                                    <form action="{{ route('tindakan-siswa.updateStatus', $riwayat->id) }}" method="POST" enctype="multipart/form-data" class="mb-0">
                                                                                                        @csrf
                                                                                                        @method('POST')
                                                                                                        
                                                                                                        <div class="input-group input-group-sm">
                                                                                                            <input type="file" 
                                                                                                                class="form-control form-control-sm" 
                                                                                                                name="bukti_tindakan" 
                                                                                                                accept="image/*"
                                                                                                                required>
                                                                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                                                                <i class="fas fa-upload me-1"></i>
                                                                                                                Selesai
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        <small class="text-muted">Upload bukti foto tindakan</small>
                                                                                                    </form>
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="text-success">
                                                                                                    <i class="fas fa-check-double me-1"></i>
                                                                                                    <small>Tindakan telah diselesaikan</small>
                                                                                                    @if($riwayat->bukti_tindakan)
                                                                                                        <div class="mt-1">
                                                                                                            <a href="{{ asset('storage/bukti_tindakan/' . $riwayat->bukti_tindakan) }}" target="_blank" title="Lihat Bukti Tindakan">
                                                                                                                <i class="fas fa-image"></i> Lihat Bukti
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endif
                                                                                      

                                                                                            <!-- Tombol Hapus -->
                                                                                            <div class="ms-2">
                                                                                                @can('hapus tindakan-siswa')                                                                      
                                                                                                    <form action="{{ route('tindakan-siswa.destroy', $riwayat->id) }}" 
                                                                                                        method="POST" 
                                                                                                        onsubmit="return confirm('Yakin ingin menghapus tindakan ini?')" 
                                                                                                        style="display:inline;">
                                                                                                        @csrf
                                                                                                        @method('DELETE')
                                                                                                        <button type="submit" 
                                                                                                                class="btn btn-sm btn-outline-danger" 
                                                                                                                title="Hapus Tindakan">
                                                                                                            <i class="fas fa-trash"></i>
                                                                                                        </button>
                                                                                                    </form>
                                                                                                @endcan 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <div class="text-center py-4">
                                                                                <i class="fas fa-clipboard text-muted mb-3" style="font-size: 3rem;"></i>
                                                                                <h6 class="text-muted">Belum Ada Tindakan</h6>
                                                                                <p class="text-muted mb-0">Belum ada tindakan yang diberikan untuk siswa ini.</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i>
                                                            Tutup
                                                        </button>
                                                        <a href="{{ route('tindakan-siswa.create', ['siswa_id' => $siswa->siswa_id, 'kelas_siswa_id' => $siswa->kelas_siswa_id]) }}" 
                                                        class="btn btn-primary">
                                                            <i class="fas fa-plus me-1"></i>
                                                            Tambah Tindakan Baru
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi:</strong> Siswa dengan skor pelanggaran ≥ 500 poin memerlukan tindakan khusus dari Bimbingan Konseling.
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-smile text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">Tidak Ada Siswa yang Perlu Tindakan</h5>
                            <p class="text-muted">Saat ini tidak ada siswa dengan skor pelanggaran ≥ 500 poin.</p>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
    .avatar {
        font-size: 14px;
        font-weight: bold;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .card-header {
        border-bottom: 3px solid #ffc107;
    }
    
    .badge {
        font-size: 0.75em;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tabletindakan').DataTable({
            responsive: true,
            paging: true,
            searching: false,
            info: true,
            ordering: true,
            columnDefs: [{
                targets: [4], // kolom ke-5 (action), ubah ke 4 karena hanya ada 5 kolom
                orderable: false
            }]
        });

        // Auto close alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Tooltip initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection