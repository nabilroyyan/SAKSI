@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Tambah Tindakan untuk Siswa
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Informasi Siswa -->
                    <div class="alert alert-info mb-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar bg-primary text-white rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold;">
                                    {{ substr($siswa->nama_siswa, 0, 1) }}
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="mb-1">{{ $siswa->nama_siswa }}</h5>
                                <p class="mb-0">
                                    <span class="badge bg-secondary me-2">ID: {{ $siswa->nis_nip }}</span>
                                    <span class="badge bg-info">Kelas: {{ $siswa->tingkat }} {{ $siswa->nama_jurusan }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('tindakan-siswa.store') }}" method="POST">
                        @csrf
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                        <input type="hidden" name="kelas_siswa_id" value="{{ $kelas_siswa_id }}">

                        <div class="row g-3">
                            <!-- Jenis Tindakan -->
                            <div class="col-md-6">
                                <label for="id_tindakan" class="form-label">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    Jenis Tindakan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('id_tindakan') is-invalid @enderror" 
                                        id="id_tindakan" 
                                        name="id_tindakan" 
                                        required>
                                    <option value="">Pilih Jenis Tindakan</option>
                                    @php
                                        $kategoriTindakan = \App\Models\KategoriTindakan::all();
                                    @endphp
                                    @foreach($kategoriTindakan as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('id_tindakan') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_tindakan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_tindakan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input 
                                value="{{ old('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                                type="date"
                                id="tanggal"
                                name="tanggal" 
                                class="form-control" required>
                            </div>

                            <!-- Catatan -->
                            <div class="col-12">
                                <label for="catatan" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Catatan Tindakan
                                </label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                          id="catatan" 
                                          name="catatan" 
                                          rows="4" 
                                          placeholder="Tuliskan catatan atau detail tindakan yang akan diberikan...">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Jelaskan detail tindakan, alasan, atau langkah yang akan diambil
                                </div>
                            </div>

                            <!-- Riwayat Pelanggaran -->
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history me-2"></i>
                                            Riwayat Pelanggaran Siswa
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $pelanggaranSiswa = \App\Models\Pelanggaran::where('id_siswa', $siswa->id)
                                                              ->where('kelas_siswa_id', $kelas_siswa_id)
                                                              ->with('skor_pelanggaran')
                                                              ->orderBy('created_at', 'desc')
                                                              ->take(5)
                                                              ->get();
                                            $totalSkor = \App\Models\Pelanggaran::where('id_siswa', $siswa->id)
                                                       ->where('kelas_siswa_id', $kelas_siswa_id)
                                                       ->sum('id_skor_pelanggaran');
                                        @endphp
                                        
                                        <div class="mb-3">
                                            <span class="badge bg-danger fs-6">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Total Skor: {{ $totalSkor }} Poin
                                            </span>
                                        </div>

                                        @if($pelanggaranSiswa->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Jenis Pelanggaran</th>
                                                            <th>Skor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($pelanggaranSiswa as $pelanggaran)
                                                        <tr>
                                                            <td>{{ date('d/m/Y', strtotime($pelanggaran->created_at)) }}</td>
                                                            <td>{{ $pelanggaran->skor_pelanggaran->nama_pelanggaran ?? 'Tidak diketahui' }}</td>
                                                            <td>
                                                                <span class="badge bg-warning">{{ $pelanggaran->skor_pelanggaran->skor }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if($pelanggaranSiswa->count() == 5)
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Menampilkan 5 pelanggaran terbaru
                                                </small>
                                            @endif
                                        @else
                                            <p class="text-muted mb-0">Tidak ada riwayat pelanggaran ditemukan.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tindakan-siswa.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-2"></i>
                                            Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Simpan Tindakan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        font-weight: bold;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    
    .card-header {
        border-bottom: 3px solid #007bff;
    }
    
    .bg-light .card-header {
        border-bottom: 2px solid #ffc107;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-resize textarea
    const textarea = document.getElementById('catatan');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const tindakan = document.getElementById('id_tindakan').value;
        const tanggal = document.getElementById('tanggal').value;
        
        if (!tindakan || !tanggal) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Confirm before submit
        if (!confirm('Yakin ingin menyimpan tindakan untuk siswa ini?')) {
            e.preventDefault();
            return false;
        }
    });

    // Opsional: Set minimal tanggal hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').min = today;

    // HAPUS atau ubah baris berikut jika ingin mengizinkan tanggal ke depan
    // document.getElementById('tanggal').max = today; // ← INI yang membuat kamu tidak bisa pilih tanggal besok
</script>
@endpush
@endsection