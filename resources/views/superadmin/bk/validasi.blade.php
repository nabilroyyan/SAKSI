@extends('layout.MainLayout')

@section('content')

<div class="page-content">
<div class="container-fluid">
<div class="container">
    <h4>Validasi Surat Izin / Sakit</h4>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($absensi->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Surat</th>
                    <th>Status Surat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absensi as $a)
                <tr>
                    <td>{{ $a->siswa->nama_siswa ?? '-' }}</td>
                    <td>{{ $a->kelasSiswa->kelas->tingkat ?? '-' }} {{ $a->kelasSiswa->kelas->jurusan->nama_jurusan }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->hari_tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge 
                            @if($a->status == 'hadir') bg-success
                            @elseif($a->status == 'sakit') bg-warning
                            @elseif($a->status == 'izin') bg-info
                            @else bg-danger
                            @endif">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>
                    <td>{{ $a->catatan ?? '-' }}</td>
                    <td>
                        @if($a->foto_surat)
                            <a href="{{ asset('storage/' . $a->foto_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-file"></i> Lihat Surat
                            </a>
                        @else
                            <span class="text-muted">Tidak Ada</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge 
                            @if($a->status_surat == 'diterima') bg-success
                            @elseif($a->status_surat == 'ditolak') bg-danger
                            @else bg-warning
                            @endif">
                            {{ ucfirst($a->status_surat) }}
                        </span>
                    </td>
                    <td>
                        @if($a->status_surat == 'tertunda')
                            <div class="btn-group" role="group">
                                <!-- Form untuk menerima surat -->
                                <form action="{{ route('validasiSurat', $a->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" 
                                            onclick="return confirm('Apakah Anda yakin ingin menerima surat ini?')">
                                        <i class="bx bx-check"></i> Terima
                                    </button>
                                </form>
                                
                                <!-- Form untuk menolak surat -->
                                <form action="{{ route('tolakSurat', $a->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#tolakModal"
                                            data-id="{{ $a->id }}"
                                            onclick="setTolakSuratId({{ $a->id }})">
                                        <i class="bx bx-x"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-muted">Sudah divalidasi</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            <i class="bx bx-info-circle"></i>
            Tidak ada surat yang perlu divalidasi saat ini.
        </div>
    @endif
</div>
</div>
</div>

<!-- Modal Tolak Surat -->
<div class="modal fade" id="tolakModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('tolakSurat', 0) }}" id="tolakSuratForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Masukkan alasan penolakan surat:</p>
                    <div class="mb-3">
                        <textarea name="catatan" class="form-control" required rows="3" placeholder="Tulis alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Surat</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal untuk konfirmasi (opsional, jika ingin lebih interaktif) -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function setTolakSuratId(id) {
    const form = document.getElementById('tolakSuratForm');
    form.action = `/absensi/tolak/${id}`; // Sesuaikan dengan route tolakSurat
}
// Script untuk konfirmasi aksi (opsional)
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection