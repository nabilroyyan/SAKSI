@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Data Siswa</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                            <li class="breadcrumb-item active">Data Siswa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6 col-md-auto mb-2 mb-md-0">
                                {{-- Tombol untuk memicu Modal Import --}}
                                @can('import-siswa')                            
                                <button type="button" class="btn btn-info waves-effect waves-light w-100" data-bs-toggle="modal" data-bs-target="#importSiswaModal">
                                    <i class="fas fa-file-import me-1"></i> Import Siswa
                                </button>
                                @endcan
                            </div>
                            <div class="col-sm-6 col-md-auto ms-auto"> {{-- Tombol Tambah Data Siswa di kanan --}}
                                @can('tambah siswa')
                                <a href="{{ route('siswa.create') }}" class="btn btn-primary btn-rounded waves-effect waves-light w-100">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Data Siswa
                                </a>
                                @endcan
                            </div>
                        </div>

                        <form action="{{ route('allDestroy') }}" method="POST" id="bulkDeleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" id="btnDeleteAll" disabled>
                                <i class="fas fa-trash-alt me-1"></i> Hapus Semua Terpilih
                            </button>
                        </form>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                         @if ($errors->has('file_siswa'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first('file_siswa') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif                      

                        <h4 class="card-title mt-4">Table Siswa</h4>
                        <div class="table-responsive">
                            <table id="tableedit" class="table table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="checkAll">
                                        </td>
                                        <th>No</th>
                                        <th>Nama siswa</th>
                                        <th>Kode</th>
                                        <th>Email</th>
                                        <th>NISN</th>
                                        <th>Agama</th>
                                        <th>Tempat, Tanggal Lahir</th>
                                        <th>Jenis kelamin</th>
                                        <th>No telepon</th>
                                        <th>Tahun masuk</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($siswa as $index => $item)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="checkItem" name="ids[]" value="{{ $item->id }}">
                                            </td>
                                            <td>{{ $siswa instanceof \Illuminate\Pagination\LengthAwarePaginator ? $siswa->firstItem() + $index : $loop->iteration }}</td>
                                            <td>{{ $item->nama_siswa }}</td>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->nis_nip }}</td>
                                            <td>{{ $item->agama }}</td>
                                            <td>{{ $item->tempat }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->isoFormat('D MMM YYYY') }}</td>
                                            <td>{{ $item->jenis_kelamin }}</td>
                                            <td>{{ $item->no_telepon }}</td>
                                            <td>{{ $item->tahun_masuk }}</td>
                                            <td>
                                                @if($item->status == 'aktif')
                                                    <span class="badge badge-soft-success font-size-12">{{ ucfirst($item->status) }}</span>
                                                @else
                                                    <span class="badge badge-soft-danger font-size-12">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('edit siswa')                                                 
                                                <a href="{{ route('siswa.edit', $item->id) }}"
                                                    class="btn btn-warning btn-sm waves-effect waves-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            @can('hapus siswa')
                                            <form action="{{ route('siswa.destroy', $item->id) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm waves-effect waves-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
                                                    data-nama="{{ $item->nama_siswa }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($siswa, 'hasPages') && $siswa->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="datatable_info_custom" role="status" aria-live="polite">
                                    Menampilkan {{ $siswa->firstItem() }} sampai {{ $siswa->lastItem() }} dari {{ $siswa->total() }} entri
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers float-md-end" id="datatable_paginate_custom">
                                    {{ $siswa->links() }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div> 
        </div> 
    </div>
</div>

<div class="modal fade" id="importSiswaModal" tabindex="-1" aria-labelledby="importSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSiswaModalLabel">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" id="formImportSiswaModal">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_siswa_modal" class="form-label">Pilih File Excel/CSV:</label>
                        <input type="file" class="form-control" id="file_siswa_modal" name="file_siswa" required accept=".xlsx, .xls, .csv">
                        <div class="form-text mt-1">Hanya file .xlsx, .xls, atau .csv yang diizinkan.</div>
                        {{-- Contoh link template jika ada --}}
                        <div class="mt-2">
                            <a href="{{ asset('templates/Template_siswa.xlsx') }}" download class="btn btn-link btn-sm p-0">
                                <i class="fas fa-download me-1"></i> Unduh Template Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">
                        <i class="fas fa-file-import me-1"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tableedit').DataTable({
        responsive: false,
        paging: false,
        searching: true,
        info: false,
        ordering: false
    });

    $('#checkAll').on('click', function () {
        $('.checkItem').prop('checked', this.checked);
        toggleDeleteAll();
    });

    $('.checkItem').on('change', function () {
        toggleDeleteAll();
    });

    function toggleDeleteAll() {
        const anyChecked = $('.checkItem:checked').length > 0;
        $('#btnDeleteAll').prop('disabled', !anyChecked);
    }

    $('#bulkDeleteForm').on('submit', function (e) {
        e.preventDefault(); // cegah submit default

        // Bersihkan input sebelumnya
        $(this).find('input[name="ids[]"]').remove();

        // Tambahkan input hidden ids[]
        $('.checkItem:checked').each(function () {
            $('<input>').attr({
                type: 'hidden',
                name: 'ids[]',
                value: $(this).val()
            }).appendTo('#bulkDeleteForm');
        });

        const total = $('.checkItem:checked').length;

        if (total === 0) {
            alert('Tidak ada siswa dipilih');
        } else if (confirm(`Yakin ingin menghapus ${total} siswa?`)) {
            this.submit(); // lanjut submit form
        }
    });
});
</script>
@endpush