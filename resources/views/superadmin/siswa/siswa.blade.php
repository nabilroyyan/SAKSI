@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Judul -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        Manajemen Siswa Kelas {{ $kelas->tingkat }} - {{ $kelas->jurusan->nama_jurusan }} {{ $kelas->kode_kelas }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Kelas</a></li>
                            <li class="breadcrumb-item active">Manajemen Siswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('kelas.siswa.store', $kelas->id) }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <a href="{{ route('showKelasSiswa') }}" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="search-kode" class="form-control" placeholder="Cari Kode Kelas...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="filter-button" class="btn btn-info w-100">Filter</button>
                                </div>
                                 <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="bx bx-plus me-1"></i> Tambah Siswa Terpilih
                                        </button>
                                </div>
                            </div>

                            <input type="hidden" name="status" value="new">
                            <!-- Tabel -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50px" class="text-center">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>No</th>
                                            <th>NISN</th>
                                            <th>Nama Siswa</th>
                                            <th>Kode Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody id="siswa-table">
                                        @foreach ($siswa as $index => $s)
                                            <tr class="siswa-row" data-kode="{{ strtolower($s->kode) }}">
                                                <td class="text-center">
                                                    <input type="checkbox" name="siswa_id[]" value="{{ $s->id }}" class="student-checkbox">
                                                </td>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $s->nis_nip }}</td>
                                                <td>{{ $s->nama_siswa }}</td>
                                                <td>{{ $s->kode }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select All checkbox
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('tr').style.display !== 'none');
                const allChecked = visibleCheckboxes.every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });

        // Filter Kode Kelas
        const filterInput = document.getElementById('search-kode');
        const filterButton = document.getElementById('filter-button');
        const rows = document.querySelectorAll('.siswa-row');

        function applyFilter() {
            const keyword = filterInput.value.toLowerCase().trim();
            rows.forEach(row => {
                const kode = row.getAttribute('data-kode');
                if (!keyword || kode.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        filterButton.addEventListener('click', applyFilter);

        // Tekan Enter untuk cari
        filterInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilter();
            }
        });
    });
</script>
@endpush
@endsection
