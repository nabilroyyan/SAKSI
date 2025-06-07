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
                            
                            <h4 class="card-title">Table riwayat</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas Sebelumnya</th>
                                    <th>Kelas Sekarang</th>
                                    <th>Tahun Ajaran</th>
                                </tr>
                                </thead>
                                <tbody>
                                      @foreach ($riwayatKelas as $index => $riwayat)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $riwayat->siswa->nama_siswa }}</td>
                                                    <td>{{ $riwayat->kelasLama->tingkat }} - {{ $riwayat->kelasLama->jurusan->nama_jurusan }} {{ $riwayat->kelasLama->kode_kelas }}</td>
                                                    <td>{{ $riwayat->kelasBaru->tingkat }} - {{ $riwayat->kelasBaru->jurusan->nama_jurusan }} {{ $riwayat->kelasBaru->kode_kelas }}</td>
                                                    <td>{{ $riwayat->tahun_ajaran }}</td>
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
    <script>
        $(document).ready(function() {
            $('#dataTableExample').DataTable();
        });
    </script>
@endsection