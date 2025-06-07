@extends('layout.MainLayout')

@section('content')

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Kelas</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data Kelas</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                   <form method="GET" action="{{ route('kelas.index') }}" class="mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <select name="stt" class="form-control" onchange="this.form.submit()">
                                                    <option value="aktif" {{ $stt == 'aktif' ? 'selected' : '' }}>Kelas Aktif</option>
                                                    <option value="tidak_aktif" {{ $stt == 'tidak_aktif' ? 'selected' : '' }}>Kelas Tidak Aktif</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="tingkat" class="form-control" onchange="this.form.submit()">
                                                    <option value="">Tinkat</option>
                                                    <option value="x" {{ $tingkat == 'x' ? 'selected' : '' }}>X</option>
                                                    <option value="xi" {{ $tingkat == 'xi' ? 'selected' : '' }}>XI</option>
                                                    <option value="xii" {{ $tingkat == 'xii' ? 'selected' : '' }}>XII</option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                <div class="col-md-6 text-end">
                                    @can('tambah kelas')                                       
                                    <a href="/kelas/create" class="btn btn-primary btn-rounded waves-effect waves-light">
                                        <i class="mdi mdi-plus me-1"></i> Tambah Data Kelas
                                    </a>
                                    @endcan
                                </div>
                            </div>
                            
                            <h4 class="card-title">Table Kelas</h4>
                            <table id="tablekelas" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Kelas</th>
                                    <th>Jurusan</th>
                                    <th>Status</th>
                                    <th>total Siswa</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                               <tbody>
                            @foreach($kelas as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->tingkat }}</td>
                                <td>{{ $k->jurusan->nama_jurusan }}</td>
                                <td>
                                    @if($k->stt == 'aktif')
                                        <span class="badge bg-primary">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $k->kelasSiswa->count() }}</td>
                                <td>
                                    @can('edit kelas')                                      
                                    <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>                                                                            
                                    @endcan
                                    @can('detail kelas')                                        
                                    <a href="{{ route('kelas.detailSiswa', $k->id) }}" class="btn btn-success btn-sm">Detail</a>
                                    @endcan
                                    @can('hapus kelas')                                       
                                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="form-delete" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
    </div>
    @endsection
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.form-delete').forEach(function(form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Cegah submit default

                let confirmText = prompt(
                    "⚠️ PERINGATAN KERAS!\n\n" +
                    "Menghapus kelas ini juga akan menghapus seluruh riwayat siswa yang pernah berada di kelas ini!\n\n" +
                    "Jika Anda benar-benar yakin, ketik DELETE:"
                );

                if (confirmText === 'DELETE') {
                    form.submit(); // Submit jika benar
                } else {
                    alert("Penghapusan dibatalkan. Anda tidak mengetik DELETE.");
                }
            });
        });
    });

    $(document).ready(function() {
        $('#tablekelas').DataTable({
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
    });

    </script>
    @endpush
