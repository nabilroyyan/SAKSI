@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Daftar Siswa Kelas {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-3">
                            <a href="{{ url('/kelas') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            
                            <div class="d-flex align-items-center">
                                @can('update periode')
                                <button type="button" class="btn btn-warning me-2" id="openBulkPeriodeModal">
                                    <i class="bi bi-calendar-check"></i> Update Periode Terpilih</button>
                                @endcan
                                @can('naik kelas')           
                                <button type="button" class="btn btn-primary me-2" id="openBulkPromoteModal">Naikkan Siswa Terpilih</button>
                                <span id="selectedCount" class="badge bg-primary">0 dipilih</span>
                                @endcan
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                        <h4 class="card-title">Table Siswa di Kelas</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        @can('naik kelas')                                          
                                        <th width="50px" class="text-center">
                                            <input type="checkbox" id="select_all_ids">
                                        </th>
                                        @endcan
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Status</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Tanggal Masuk</th>
                                        <th width="150px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaDiKelas as $index => $item)
                                    <tr>
                                        @can('naik kelas')                                           
                                        <td class="text-center">
                                            <input type="checkbox" name="ids" class="checkbox_ids" 
                                            value="{{ $item->siswa->id }}" data-kelas-siswa-id="{{ $item->id }}">
                                        </td>
                                        @endcan
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->siswa->nis_nip }}</td>
                                        <td>{{ $item->siswa->nama_siswa }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $item->status == 'naik' ? 'success' : 
                                                ($item->status == 'tidak_naik' ? 'warning' : 'primary')
                                            }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->periode->tahun }} {{ $item->periode->semester }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('hapus-siswa kelas')                      
                                                <form action="{{ route('kelas.hapusSiswa', $item->id) }}" method="POST" 
                                                    onsubmit="return confirm('Yakin ingin menghapus siswa ini dari kelas?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
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

<!-- Modal Naik Kelas Massal -->
<div class="modal fade" id="bulkNaikKelasModal" tabindex="-1" aria-labelledby="bulkNaikKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkNaikKelasModalLabel">Proses Siswa Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kelas.naikkanBulkSiswa') }}" method="POST" id="bulkPromoteForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_status" class="form-label">Status Siswa</label>
                        <select class="form-select" id="bulk_status" name="status" required>
                            <option value="naik" @if($kelas->tingkat != 'XII') selected @endif>Naik Kelas</option>
                            <option value="tidak_naik">Tidak Naik Kelas</option>
                            <option value="lulus" @if($kelas->tingkat == 'XII') selected @endif>Lulus</option>
                        </select>
                    </div>

                    <!-- Kelas Tujuan (Muncul hanya jika status = naik) -->
                   <div id="kelasTargetContainer" style="display: none;">
                        <label for="bulk_id_kelas">Pilih Kelas Tujuan</label>
                        <select id="bulk_id_kelas" name="id_kelas_tujuan" class="form-select">
                            @foreach($kelas_tujuan as $k)
                                <option value="{{ $k->id }}">{{ $k->tingkat }} - {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_periode" class="form-label">Periode Aktif</label>
                        <select class="form-select" id="bulk_periode" name="periode_id" required>
                            @foreach($periodes as $periode)
                                @if($periode->is_active == 'aktif')
                                    <option value="{{ $periode->id }}">
                                        {{ $periode->tahun }} {{ $periode->semester }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Pilih periode aktif untuk data siswa yang baru dibuat
                        </small>
                    </div>

                    <div class="alert alert-info" id="kelasInfoContainer">
                        <span id="infoText">
                            @if($kelas->tingkat == 'X')
                                Siswa akan naik ke kelas XI {{ $kelas->jurusan->nama_jurusan }}
                            @elseif($kelas->tingkat == 'XI')
                                Siswa akan naik ke kelas XII {{ $kelas->jurusan->nama_jurusan }}
                            @else
                                Siswa akan dinyatakan Lulus
                            @endif
                        </span>
                    </div>

                    <input type="hidden" name="siswa_ids" id="selectedSiswaIds">
                    <input type="hidden" name="kelas_siswa_ids" id="selectedKelasSiswaIds">
                    <input type="hidden" name="id_kelas" value="{{ $kelas->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Siswa Terpilih</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Update Periode Massal -->
<div class="modal fade" id="bulkPeriodeModal" tabindex="-1" aria-labelledby="bulkPeriodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkPeriodeModalLabel">Update Periode Siswa Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kelas.bulkPeriode') }}" method="POST" id="bulkPeriodeForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Ini akan mengupdate periode siswa terpilih ke periode aktif saat ini.
                        Siswa akan tetap di kelas yang sama tetapi dengan periode yang diperbarui.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kelas Saat Ini</label>
                        <input type="text" class="form-control" value="{{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}" readonly>
                        <input type="hidden" name="id_kelas" value="{{ $kelas->id }}">
                    </div>
                    
                    <input type="hidden" name="siswa_ids" id="periodeSiswaIds">
                    <input type="hidden" name="kelas_siswa_ids" id="periodeKelasSiswaIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        Update Periode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select_all_ids');
    const checkboxes = document.querySelectorAll('.checkbox_ids');
    const selectedCountBadge = document.getElementById('selectedCount');
    const openBulkButton = document.getElementById('openBulkPromoteModal');
    const selectedSiswaInput = document.getElementById('selectedSiswaIds');
    const selectedKelasSiswaInput = document.getElementById('selectedKelasSiswaIds');
    const bulkForm = document.getElementById('bulkPromoteForm');
    const statusSelect = document.getElementById('bulk_status');
    const kelasTargetContainer = document.getElementById('kelasTargetContainer'); // Ini container dropdown kelas tujuan
    const kelasSelect = document.getElementById('bulk_id_kelas'); // Ini select-nya
    const openBulkPeriodeButton = document.getElementById('openBulkPeriodeModal');
    const periodeSiswaInput = document.getElementById('periodeSiswaIds');
    const periodeKelasSiswaInput = document.getElementById('periodeKelasSiswaIds');
    const kelasInfoContainer = document.getElementById('kelasInfoContainer');
    const kelasTingkat = '{{ $kelas->tingkat }}';
    const namaJurusan = '{{ $kelas->jurusan->nama_jurusan }}';

    // Update info status
    function updateStatusInfo() {
        const status = statusSelect.value;

        if (status === 'naik') {
            if (kelasTingkat === 'X') {
                kelasInfoContainer.innerHTML = `Siswa akan naik ke kelas XI ${namaJurusan}`;
            } else if (kelasTingkat === 'XI') {
                kelasInfoContainer.innerHTML = `Siswa akan naik ke kelas XII ${namaJurusan}`;
            }
        } else if (status === 'tidak_naik') {
            kelasInfoContainer.innerHTML = `Siswa akan tetap di kelas ${kelasTingkat} ${namaJurusan}`;
        } else if (status === 'lulus') {
            kelasInfoContainer.innerHTML = 'Siswa akan dinyatakan Lulus';
        }
    }

    // Toggle dropdown kelas tujuan berdasarkan status
    function toggleKelasTujuan() {
        const status = statusSelect.value;

        if (status === 'naik' && kelasTingkat !== 'XII') {
            kelasTargetContainer.style.display = 'block';
            if (kelasSelect) kelasSelect.required = true;
        } else {
            kelasTargetContainer.style.display = 'none';
            if (kelasSelect) kelasSelect.required = false;
        }

        // Jika XII, paksa jadi lulus
        if (kelasTingkat === 'XII' && status === 'naik') {
            statusSelect.value = 'lulus';
            updateStatusInfo();
            kelasTargetContainer.style.display = 'none';
        }
    }

    // Inisialisasi event
    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            toggleKelasTujuan();
            updateStatusInfo();
        });

        // Trigger awal
        toggleKelasTujuan();
        updateStatusInfo();
    }

    // Disable naik kelas jika XII
    if (kelasTingkat === 'XII') {
        const naikOption = statusSelect.querySelector('option[value="naik"]');
        if (naikOption) naikOption.disabled = true;
    }

    // Modal periode
    openBulkPeriodeButton.addEventListener('click', function () {
        const selectedSiswaIds = [];
        const selectedKelasSiswaIds = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                selectedSiswaIds.push(cb.value);
                selectedKelasSiswaIds.push(cb.dataset.kelasSiswaId);
            }
        });

        if (selectedSiswaIds.length === 0) {
            alert("Pilih minimal satu siswa terlebih dahulu.");
            return;
        }

        periodeSiswaInput.value = selectedSiswaIds.join(',');
        periodeKelasSiswaInput.value = selectedKelasSiswaIds.join(',');

        const modal = new bootstrap.Modal(document.getElementById('bulkPeriodeModal'));
        modal.show();
    });

    // Counter
    function updateSelectedCount() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        selectedCountBadge.textContent = `${checked.length} dipilih`;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Buka modal naik kelas
    openBulkButton.addEventListener('click', function () {
        const selectedSiswaIds = [];
        const selectedKelasSiswaIds = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                selectedSiswaIds.push(cb.value);
                selectedKelasSiswaIds.push(cb.dataset.kelasSiswaId);
            }
        });

        if (selectedSiswaIds.length === 0) {
            alert("Pilih minimal satu siswa terlebih dahulu.");
            return;
        }

        selectedSiswaInput.value = selectedSiswaIds.join(',');
        selectedKelasSiswaInput.value = selectedKelasSiswaIds.join(',');

        statusSelect.value = 'naik';
        const changeEvent = new Event('change');
        statusSelect.dispatchEvent(changeEvent);

        const modal = new bootstrap.Modal(document.getElementById('bulkNaikKelasModal'));
        modal.show();
    });
});
</script>
@endpush
