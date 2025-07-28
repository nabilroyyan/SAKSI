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
                            
                            <h4 class="card-title">Table Kelas</h4>
                            <table id="tablekelas" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Tingkat</th>
                                    <th>Nama Kelas</th>
                                    <th>Total Siswa</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                               <tbody>
                                    @foreach($kelas as $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($k->tingkat) }}</td>
                                        <td>{{ $k->nama_kelas }}</td>
                                        <td>{{ $k->kelasSiswa->where('is_active', 'aktif')->count() }}</td>
                                        <td>
                                            @can('tambah kelas-siswa')
                                            <a href="{{ route('kelas.siswa', $k->id) }}" class="btn btn-info btn-sm">Tambah Siswa</a>                                                                              
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
    $(document).ready(function() {
        $('#tablekelas').DataTable({
            responsive: true,
            paging: true,
            searching: false,
            info: true,
            ordering: true,
            columnDefs: [{
                targets: [4], // kolom Action tidak bisa diurutkan
                orderable: false
            }]
        });

        // Konfirmasi penghapusan kelas
        document.querySelectorAll('.form-delete').forEach(function(form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                let confirmText = prompt(
                    "⚠️ PERINGATAN KERAS!\n\n" +
                    "Menghapus kelas ini juga akan menghapus seluruh riwayat siswa yang pernah berada di kelas ini!\n\n" +
                    "Jika Anda benar-benar yakin, ketik DELETE:"
                );
                if (confirmText === 'DELETE') {
                    form.submit();
                } else {
                    alert("Penghapusan dibatalkan. Anda tidak mengetik DELETE.");
                }
            });
        });
    });
</script>
@endpush

