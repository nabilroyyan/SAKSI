@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Data Periode Akademik</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Table</a></li>
                            <li class="breadcrumb-item active">Data Periode Akademik</li>
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
                        <p class="card-title-desc">Tambah Periode Akademik Baru</p>

                        <form action="{{ route('periode.store') }}" class="needs-validation" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="tahun" class="form-label">Tahun</label>
                                        <input type="text" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun') }}" required>
                                        @error('tahun')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                                            <option value="">Pilih Semester</option>
                                            <option value="ganjil" @selected(old('semester') == 'ganjil')>Ganjil</option>
                                            <option value="genap" @selected(old('semester') == 'genap')>Genap</option>
                                        </select>
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                  <label class="form-label d-block">Status Periode</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="aktif" value="aktif" {{ old('is_active') == 'aktif' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aktif">Aktif</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="non_aktif" value="non_aktif" {{ old('is_active', 'non_aktif') == 'non_aktif' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="non_aktif">Non Aktif</label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                            </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan</button>
                                <a href="{{ route('periode.index') }}" class="btn btn-secondary waves-effect waves-light">Batal</a>
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