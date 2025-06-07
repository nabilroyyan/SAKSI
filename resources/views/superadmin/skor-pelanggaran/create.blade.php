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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Table</a></li>
                            <li class="breadcrumb-item active">Data Skor Pelanggaran</li>
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
                        <p class="card-title-desc">Data Skor Pelanggaran</p>

                        <form action="{{ route('skor-Pelanggaran.store') }}" class="needs-validation" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="nama_pelanggaran">Pelanggaran</label>
                                        <input id="nama_pelanggaran" name="nama_pelanggaran" type="text" class="form-control" placeholder="Masukkan Nama Pelanggaran">
                                    </div>
                                    <div class="mb-3">
                                        <label for="skor">Skor</label>
                                        <input id="skor" name="skor" type="number" class="form-control" placeholder="Masukkan Skor">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenis_pelanggaran">Jenis Pelanggaran</label>
                                        <select id="jenis_pelanggaran" name="jenis_pelanggaran" class="form-control">
                                            <option value="">Pilih Jenis Pelanggaran</option>
                                            <option value="ringan">Ringan</option>
                                            <option value="sedang">Sedang</option>
                                            <option value="berat">Berat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                <a href="/skor-pelanggaran" class="btn btn-secondary waves-effect waves-light">Cancel</a>
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
