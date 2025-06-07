@extends('layout.mainlayout')

@section('content')
 <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data pelanggaran</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data pelanggaran</li>
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
                                @can('tambah pelanggaran')     
                                <a href="/pelanggaran/create" class="btn btn-primary btn-rounded waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"> Tambah Data pelanggaran </i>
                                </a>
                                @endcan
                            </div>
                            <h4 class="card-title">Table Pelanggaran</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;" >No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas Saat Pelanggaran</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggaran</th>
                                        <th>Skor</th>
                                        <th>Petugas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pelanggaran as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->siswa->nama_siswa }}</td>
                                        <td>
                                            {{ $item->kelasSiswa->kelas->tingkat }} 
                                            {{ $item->kelasSiswa->kelas->jurusan->nama_jurusan }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $item->ket_pelanggaran }}</td>
                                        <td>{{ $item->skor_pelanggaran->nama_pelanggaran }} ({{ $item->skor_pelanggaran->skor }})</td>
                                        <td>{{ $item->petugas->name }}</td>
                                        <td>
                                            @can('edit pelanggaran')
                                            <a href="{{ route('pelanggaran.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @endcan
                                            @can('hapus pelanggaran')                                               
                                            <form action="{{ route('pelanggaran.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Delete</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
@endsection