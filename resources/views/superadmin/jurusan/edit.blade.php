@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Edit jurusan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('jurusan.index')}}">Data jurusan</a></li>
                            <li class="breadcrumb-item active">Data jurusan</li>
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
                        <p class="card-title-desc">Edit Data jurusan</p>

                        <form action="{{ route('jurusan.update', $jurusan->id) }}" method="POST" class="needs-validation">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="nama_jurusan">jurusan</label>
                                        <input type="text" id="nama_jurusan" name="nama_jurusan" class="form-control"
                                            value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" placeholder="Masukkan Nama jurusan">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan Perubahan</button>
                                <a href="{{ route('jurusan.index') }}" class="btn btn-secondary waves-effect waves-light">Batal</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection