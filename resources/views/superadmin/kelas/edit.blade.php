@extends('layout.MainLayout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit Data Kelas</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Data Kelas</a></li>
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
                            <p class="card-title-desc">Edit data kelas</p>

                            <form action="{{ route('kelas.update', $kelas->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="id_users" class="form-control-label">{{ __('SEKRETARIS') }}</label>
                                            <select name="id_users" class="form-control">
                                                <option value="">-- Pilih sekretaris --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('id_users', $kelas->id_users) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_users')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
        
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="stt" class="form-label">Status</label>
                                            <select id="stt" name="stt" class="form-control" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="aktif" {{ $kelas->stt == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="tidak_aktif" {{ $kelas->stt == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div>
    </div>
@endsection