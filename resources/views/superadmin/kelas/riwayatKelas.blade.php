@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Riwayat Kelas</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Riwayat Kelas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Filter Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bx bx-filter-alt me-2"></i>Filter Data
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Periode</label>
                                    <select name="periode_id" id="periode_id" class="form-select">
                                        <option value="">Semua Periode</option>
                                        @foreach($periodes as $periode)
                                            <option value="{{ $periode->id }}" 
                                                {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                                {{ $periode->tahun }} - {{ ucfirst($periode->semester) }}
                                                @if($periode->is_active == 'aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tingkat Kelas</label>
                                    <select name="tingkat" id="tingkat" class="form-select">
                                        <option value="">Semua Tingkat</option>
                                        <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                                        <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                                        <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status Kelas</label>
                                    <select name="status_kelas" id="status_kelas" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status_kelas') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tidak_aktif" {{ request('status_kelas') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status Siswa</label>
                                    <select name="status_siswa" id="status_siswa" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="naik" {{ request('status_siswa') == 'naik' ? 'selected' : '' }}>Naik</option>
                                        <option value="tidak_naik" {{ request('status_siswa') == 'tidak_naik' ? 'selected' : '' }}>Tidak Naik</option>
                                        <option value="lulus" {{ request('status_siswa') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                        <option value="new" {{ request('status_siswa') == 'new' ? 'selected' : '' }}>Baru</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('riwayat.kelas') }}" class="btn btn-secondary">
                                        <i class="bx bx-refresh me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Section -->
        <div class="row">
            @forelse($kelasData as $kelas)
            <div class="col-12">
                <div class="card kelas-card" data-kelas-id="{{ $kelas->id }}">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-1 text-white">
                                    <i class="bx bx-home-circle me-2"></i>
                                    Kelas {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan ?? 'Jurusan' }}
                                </h5>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="bx bx-group me-1"></i>
                                        {{ $kelas->siswa_count }} Siswa
                                    </span>
                                    <span class="badge {{ $kelas->stt == 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($kelas->stt) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistik Siswa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="p-2 bg-success-subtle rounded">
                                            <h4 class="mb-1 text-success">{{ $kelas->siswa_naik ?? 0 }}</h4>
                                            <p class="mb-0 text-muted">Naik</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-danger-subtle rounded">
                                            <h4 class="mb-1 text-danger">{{ $kelas->siswa_tidak_naik ?? 0 }}</h4>
                                            <p class="mb-0 text-muted">Tidak Naik</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-primary-subtle rounded">
                                            <h4 class="mb-1 text-primary">{{ $kelas->siswa_lulus ?? 0 }}</h4>
                                            <p class="mb-0 text-muted">Lulus</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 bg-info-subtle rounded">
                                            <h4 class="mb-1 text-info">{{ $kelas->siswa_baru ?? 0 }}</h4>
                                            <p class="mb-0 text-muted">Siswa Baru</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Button -->
                        <div class="text-center mb-3">
                            <button class="btn btn-outline-primary btn-sm toggle-siswa" data-bs-toggle="collapse" 
                                    data-bs-target="#siswa-{{ $kelas->id }}" aria-expanded="false">
                                <i class="bx bx-chevron-down me-1"></i>
                                Lihat Detail Siswa
                            </button>
                        </div>

                        <!-- Tabel Siswa (Collapsed) -->
                        <div class="collapse" id="siswa-{{ $kelas->id }}">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Periode</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kelas->kelasSiswa as $index => $kelasSiswa)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $kelasSiswa->siswa->nis_nip }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            {{ substr($kelasSiswa->siswa->nama_siswa, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $kelasSiswa->siswa->nama_siswa }}</h6>
                                                        <small class="text-muted">{{ $kelasSiswa->siswa->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($kelasSiswa->periode)
                                                    <span class="badge bg-info">
                                                        {{ $kelasSiswa->periode->tahun }} - {{ ucfirst($kelasSiswa->periode->semester) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($kelasSiswa->status) {
                                                        'naik' => 'bg-success',
                                                        'tidak_naik' => 'bg-danger',
                                                        'lulus' => 'bg-primary',
                                                        'new' => 'bg-info',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $kelasSiswa->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('siswa.detail', $kelasSiswa->siswa->id) }}">
                                                                <i class="bx bx-user me-2"></i>Detail Siswa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('siswa.absensi', $kelasSiswa->id) }}">
                                                                <i class="bx bx-calendar me-2"></i>Riwayat Absensi
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('siswa.pelanggaran', $kelasSiswa->id) }}">
                                                                <i class="bx bx-error me-2"></i>Riwayat Pelanggaran
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('siswa.tindakan', $kelasSiswa->id) }}">
                                                                <i class="bx bx-check-circle me-2"></i>Riwayat Tindakan
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="bx bx-info-circle me-1"></i>
                                                Tidak ada data siswa
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
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bx bx-search font-size-48 text-muted"></i>
                        </div>
                        <h5 class="mb-3">Tidak ada data ditemukan</h5>
                        <p class="text-muted">Silakan ubah filter atau tambah data kelas baru</p>
                        <a href="{{ route('kelas.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Tambah Kelas
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($kelasData->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $kelasData->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('css')
<style>
.kelas-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.kelas-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.toggle-siswa .bx-chevron-down {
    transition: transform 0.3s ease;
}

.toggle-siswa[aria-expanded="true"] .bx-chevron-down {
    transform: rotate(180deg);
}

.avatar-xs {
    width: 2rem;
    height: 2rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}

.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1);
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto submit form on filter change
    $('#periode_id, #tingkat, #status_kelas, #status_siswa').on('change', function() {
        $('#filterForm').submit();
    });
    
    // Toggle button text
    $('.toggle-siswa').on('click', function() {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        const icon = $(this).find('i');
        const text = $(this).contents().last();
        
        if (isExpanded) {
            text.replaceWith(' Sembunyikan Detail Siswa');
            icon.removeClass('bx-chevron-down').addClass('bx-chevron-up');
        } else {
            text.replaceWith(' Lihat Detail Siswa');
            icon.removeClass('bx-chevron-up').addClass('bx-chevron-down');
        }
    });
});

function exportData() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');

}

// Show success message if exists
@if(session('success'))
    Toastify({
        text: "{{ session('success') }}",
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: "#28a745",
    }).showToast();
@endif

// Show error message if exists
@if(session('error'))
    Toastify({
        text: "{{ session('error') }}",
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: "#dc3545",
    }).showToast();
@endif
</script>
@endpush