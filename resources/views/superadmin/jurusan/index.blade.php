@extends('layout.MainLayout')

@section('content')
{{-- @can('view jurusan')
@if (!Auth::user()->hasRole('siswa')) --}}
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data jurusan</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data jurusan</li>
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
                                @can('tambah jurusan')                           
                                <a href="/jurusan/create" class="btn btn-primary btn-rounded waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"> Tambah Data jurusan </i>
                                </a>
                                @endcan
                            </div>
                            
                            <h4 class="card-title">Table jurusan</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama jurusan</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($jurusan as $index => $jurusan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $jurusan->nama_jurusan }}</td>
                                    <td>
                                        @can('edit jurusan')                                           
                                        <a href="{{route ('jurusan.edit', $jurusan->id)}}" class="btn btn-warning btn-sm">Edit</a>
                                        @endcan
                                        @can('hapus jurusan')                                           
                                        <form action="{{route ('jurusan.destroy', $jurusan->id)}}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
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
    {{-- @endif
@endcan --}}
@endsection