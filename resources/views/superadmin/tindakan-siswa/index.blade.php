{{-- resources/views/superadmin/tindakan-siswa/index.blade.php --}}
@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Daftar Siswa Perlu Tindakan (Skor ≥ 500)
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($siswaTindakan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 20%">Nama Siswa</th>
                                        <th style="width: 20%">Kelas</th>
                                        <th style="width: 15%">Total Skor</th>
                                        <th style="width: 10%">Status</th>
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
                                                <span class="">{{ $siswa->tingkat }} {{ $siswa->nama_jurusan }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $siswa->total_skor }} Poin
                                                </span>
                                            </td>
                                            <td>
                                               @php
                                                    $tindakan = \App\Models\TindakanSiswa::where('id_siswa', $siswa->siswa_id)
                                                                ->where('kelas_siswa_id', $siswa->kelas_siswa_id)
                                                                ->latest()
                                                                ->first();
                                                @endphp
                                                
                                                @if($tindakan)
                                                    @if($tindakan->status == 'sudah')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Selesai
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>Proses
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Belum
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if(!$tindakan || $tindakan->status == 'belum')
                                                        <a href="{{ route('tindakan-siswa.create', ['siswa_id' => $siswa->siswa_id, 'kelas_siswa_id' => $siswa->kelas_siswa_id]) }}" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="Berikan Tindakan">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if($tindakan && $tindakan->status == 'belum')
                                                        <form action="{{ route('tindakan-siswa.updateStatus', $tindakan->id_siswa) }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-success" 
                                                                    title="Tandai Selesai"
                                                                    onclick="return confirm('Yakin tindakan sudah dilakukan?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-info" 
                                                            title="Lihat Detail"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailModal{{ $siswa->siswa_id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Detail -->
                                        <div class="modal fade" id="detailModal{{ $siswa->siswa_id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
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
                                                            <div class="col-md-6">
                                                                <h6 class="text-muted">Informasi Siswa</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><strong>ID:</strong> {{ $siswa->nis_nip }}</li>
                                                                    <li><strong>Nama:</strong> {{ $siswa->nama_siswa }}</li>
                                                                    <li><strong>Kelas:</strong> {{ $siswa->tingkat }}  {{ $siswa->nama_jurusan }}</li>
                                                                    <li><strong>Total Skor:</strong> 
                                                                        <span class="badge bg-danger">{{ $siswa->total_skor }} Poin</span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6 class="text-muted">Riwayat Tindakan</h6>
                                                                @php
                                                                    $riwayatTindakan = \App\Models\TindakanSiswa::where('id_siswa', $siswa->siswa_id)
                                                                                    ->where('kelas_siswa_id', $siswa->kelas_siswa_id)
                                                                                    ->with('kategoriTindakan')
                                                                                    ->orderBy('created_at', 'desc')
                                                                                    ->get();
                                                                @endphp
                                                                
                                                                @if($riwayatTindakan->count() > 0)
                                                                    <div class="list-group">
                                                                        @foreach($riwayatTindakan as $riwayat)
                                                                            <div class="list-group-item">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted">{{ $riwayat->tanggal }}</small>
                                                                                    <span class="badge {{ $riwayat->status == 'sudah' ? 'bg-success' : 'bg-warning' }}">
                                                                                        {{ ucfirst($riwayat->status) }}
                                                                                    </span>
                                                                                </div>
                                                                                <p class="mb-1">{{ $riwayat->catatan ?? 'Tidak ada catatan' }}</p>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <p class="text-muted">Belum ada tindakan yang diberikan.</p>
                                                                @endif
                                                            </div>
                                                        </div>
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
    // Auto close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection