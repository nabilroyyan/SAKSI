@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Edit Data Siswa</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Data Siswa</a></li>
                            <li class="breadcrumb-item active">Edit Data</li>
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
                        <p class="card-title-desc">Edit data siswa</p>

                        <form action="{{ route('siswa.update', $siswa) }}" class="needs-validation" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="nama_siswa">Nama siswa</label>
                                        <input id="nama_siswa" name="nama_siswa" type="text" class="form-control" 
                                               value="{{ old('nama_siswa', $siswa->nama_siswa) }}" placeholder="Nama siswa" required>
                                        @error('nama_siswa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input id="email" name="email" type="email" class="form-control" 
                                               value="{{ old('email', $siswa->email) }}" placeholder="Masukkan Email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal_lahir">Tanggal Lahir</label>
                                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="form-control" 
                                               value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tempat">Tempat Lahir</label>
                                        <input id="tempat" name="tempat" type="text" class="form-control" 
                                               value="{{ old('tempat', $siswa->tempat) }}" placeholder="Masukkan Tempat Lahir">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control">
                                            <option value="" disabled>Pilih Jenis Kelamin</option>
                                            <option value="laki-laki" {{ $siswa->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="perempuan" {{ $siswa->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    
                                </div>

                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="kode">kode</label>
                                        <input id="kode" name="kode" type="text" class="form-control" 
                                               value="{{ old('kode', $siswa->kode) }}" placeholder="Masukkan kode">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nis_nip">NIS</label>
                                        <input id="nis_nip" name="nis_nip" type="text" class="form-control" 
                                               value="{{ old('nis_nip', $siswa->nis_nip) }}" placeholder="Masukkan NIS" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="agama">Agama</label>
                                        <select id="agama" name="agama" class="form-control">
                                            <option value="" disabled>Pilih Agama</option>
                                            <option value="islam" {{ $siswa->agama == 'islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="kristen" {{ $siswa->agama == 'kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="katolik" {{ $siswa->agama == 'katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="hindu" {{ $siswa->agama == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="buddha" {{ $siswa->agama == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="konghucu" {{ $siswa->agama == 'konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="no_telepon">No Telepon</label>
                                        <input id="no_telepon" name="no_telepon" type="text" class="form-control" 
                                               value="{{ old('no_telepon', $siswa->no_telepon) }}" placeholder="Masukkan No Telepon">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tahun_masuk">Tahun Masuk</label>
                                        <input id="tahun_masuk" name="tahun_masuk" type="text" class="form-control" 
                                               value="{{ old('tahun_masuk', $siswa->tahun_masuk) }}" placeholder="Masukkan Tahun Masuk">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan Perubahan</button>
                                <a href="{{ route('siswa.index') }}" class="btn btn-secondary waves-effect waves-light">Batal</a>
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