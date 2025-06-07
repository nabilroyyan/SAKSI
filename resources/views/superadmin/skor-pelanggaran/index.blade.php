@extends('layout.MainLayout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Skor Pelanggaran</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data Skor Pelanggaran</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-3">
                                @can('tambah skor-pelanggaran')
                                    
                                @endcan
                                <a href="/skor-pelanggaran/create" class="btn btn-primary btn-rounded waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"> Tambah Skor Pelanggaran </i>
                                </a>
                            </div>
                            
                            <h4 class="card-title">Table Skor Pelanggaran</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Skor</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($SkorPelanggarans as $index => $pelanggaran)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pelanggaran->nama_pelanggaran }}</td>
                                    <td>{{ $pelanggaran->skor }}</td>
                                    <td>{{ ucfirst($pelanggaran->jenis_pelanggaran) }}</td>
                                    <td>
                                        @can('hapus skor-pelanggaran')
                                            
                                        @endcan
                                        <form action="{{ route('skor-Pelanggaran.destroy', $pelanggaran->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
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
