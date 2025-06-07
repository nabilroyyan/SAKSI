@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Data siswa</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                            <li class="breadcrumb-item active">Data siswa</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">SMKN 1 SUMENEP</h4>
                        <p class="card-title-desc">Data siswa</p>

                        <form action="{{route ('siswa.store')}}" class="needs-validation" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="nama_siswa">Nama siswa</label>
                                        <input id="nama_siswa" name="nama_siswa" type="text" class="form-control" placeholder="Nama siswa">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input id="email" name="email" type="email" class="form-control" placeholder="Masukkan Email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal_lahir">Tanggal Lahir</label>
                                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="form-control">
                                     </div>
                                    <div class="mb-3">
                                        <label for="tempat">Tempat Lahir</label>
                                        <input id="tempat" name="tempat" type="text" class="form-control" placeholder="Masukkan Tempat Lahir">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                            <option value="laki-laki">Laki-laki</option>
                                            <option value="perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                   
                                    <div class="mb-3">
                                        <label for="kode">Kode</label>
                                        <input id="kode" name="kode" type="text" class="form-control" placeholder="Masukkan kode">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nis">NIS</label>
                                        <input id="nis_nip" name="nis_nip" type="text" class="form-control" placeholder="Masukkan NIS">
                                    </div>
                                    <div class="mb-3">
                                        <label for="agama">Agama</label>
                                        <select id="agama" name="agama" class="form-control">
                                            <option value="" disabled selected>Pilih Agama</option>
                                            <option value="islam">Islam</option>
                                            <option value="protestan">protestan</option>
                                            <option value="katolik">Katolik</option>
                                            <option value="hindu">Hindu</option>
                                            <option value="buddha">Buddha</option>
                                            <option value="khonghucu">Khonghucu</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="no_telepon">No Telepon</label>
                                        <input id="no_telepon" name="no_telepon" type="text" class="form-control" placeholder="Masukkan No Telepon">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tahun_masuk">Tahun Masuk</label>
                                        <input id="tahun_masuk" name="tahun_masuk" type="text" class="form-control" placeholder="Masukkan Tahun Masuk">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                <a href="/siswa" class="btn btn-secondary waves-effect waves-light">Cancel</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

    </div>
</div>
@endsection