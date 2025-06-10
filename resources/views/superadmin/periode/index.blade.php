@extends('layout.MainLayout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Daftar Periode Akademik</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Periode Akademik</li>
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
                                @can('tambah periode')
                                <a href="{{ route('periode.create') }}" class="btn btn-primary btn-rounded waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Periode
                                </a>
                                @endcan
                            </div>
                            <h4 class="card-title">Table Periode Akademik</h4>
                            <div class="table-responsive">
                               <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">NO</th>
                                            <th>Tahun</th>
                                            <th>Semester</th>
                                            <th>Status</th>
                                            <th >Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($periodes as $periode)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $periode->tahun }}</td>
                                                <td>{{ ucfirst($periode->semester) }}</td>
                                                <td>
                                                     @if($periode->is_active === 'aktif')
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                                        @endif
                                                 </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('aktif periode')
                                                        @if($periode->is_active === 'aktif')
                                                        <form action="{{ route('periode.deactivate', $periode->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Nonaktifkan periode ini?')">
                                                                <i class="fas fa-times"></i> Non Aktifkan
                                                            </button>
                                                        </form>
                                                        @else
                                                            <form action="{{ route('periode.activate', $periode->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Aktifkan periode ini? Semua periode lain akan dinonaktifkan.')">
                                                                    <i class="fas fa-check"></i> Aktifkan
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @endcan
                                                        @can('hapus periode')             
                                                        <form action="{{ route('periode.destroy', $periode->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin?')">
                                                                <i class="fas fa-trash"></i>
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
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
    </div>
@endsection