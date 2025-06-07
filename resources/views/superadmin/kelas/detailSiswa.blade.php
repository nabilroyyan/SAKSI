

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
                                        <td>{{ $item->tahun_ajaran }}</td>
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
                            <option value="naik">Naik Kelas</option>
                            <option value="tidak_naik">Tidak Naik Kelas</option>
                            <option value="lulus">Lulus</option>
                        </select>
                        <small class="form-text text-muted">
                            Naik Kelas: Siswa akan dipindahkan ke kelas baru<br>
                            Tidak Naik Kelas: Siswa akan tetap di kelas yang sama<br>
                            Lulus: Siswa telah menyelesaikan pendidikan
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulk_tahun_ajaran" class="form-label">Tahun Ajaran Baru</label>
                        <input type="text" class="form-control" id="bulk_tahun_ajaran" 
                               name="tahun_ajaran" value="{{ date('Y') . '/' . (date('Y')+1) }}" required>
                    </div>
                    
                    <div class="mb-3" id="kelasTargetContainer">
                        <label for="bulk_id_kelas" class="form-label">Kelas Tujuan <span class="text-muted">(Untuk status Naik Kelas)</span></label>
                        @if($allKelas->isNotEmpty())
                            <select class="form-select" id="bulk_id_kelas" name="id_kelas">
                                @foreach($allKelas as $k)
                                    <option value="{{ $k->id }}">
                                        {{ $k->tingkat }} {{ $k->jurusan->nama_jurusan }} ({{ $k->kode_kelas }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning">
                                Tidak ada kelas lain yang tersedia.
                            </div>
                            <input type="hidden" name="id_kelas" value="">
                        @endif
                    </div>
                    
                    <input type="hidden" name="siswa_ids" id="selectedSiswaIds">
                    <input type="hidden" name="kelas_siswa_ids" id="selectedKelasSiswaIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" @if(!$allKelas->isNotEmpty()) id="submitBulkAction" @endif>
                        Proses Siswa Terpilih
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
    const kelasTargetContainer = document.getElementById('kelasTargetContainer');
    const kelasSelect = document.getElementById('bulk_id_kelas');

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

    // Toggle visibility and required attribute of kelas tujuan based on selected status
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'naik') {
                kelasTargetContainer.style.display = 'block';
                if (kelasSelect) kelasSelect.required = true;
            } else {
                kelasTargetContainer.style.display = 'none';
                if (kelasSelect) kelasSelect.required = false;
            }
        });
    }

    openBulkButton.addEventListener('click', function () {
        const selectedSiswaIds = [];
        const selectedKelasSiswaIds = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                selectedSiswaIds.push(cb.value); // This is the student ID (value attribute)
                selectedKelasSiswaIds.push(cb.dataset.kelasSiswaId); // This is the kelas_siswa ID
            }
        });

        if (selectedSiswaIds.length === 0) {
            alert("Pilih minimal satu siswa terlebih dahulu.");
            return;
        }

        // Set hidden input values
        selectedSiswaInput.value = selectedSiswaIds.join(',');
        selectedKelasSiswaInput.value = selectedKelasSiswaIds.join(',');

        // Set default status to 'naik' and trigger change to show kelas tujuan
        if (statusSelect) {
            statusSelect.value = 'naik';
            // Manually trigger the change event
            const changeEvent = new Event('change');
            statusSelect.dispatchEvent(changeEvent);
        }

        // Buka modal
        const modal = new bootstrap.Modal(document.getElementById('bulkNaikKelasModal'));
        modal.show();
    });
});
</script>
@endpush