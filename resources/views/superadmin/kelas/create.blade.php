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
                            <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Data Kelas</a></li>
                                <li class="breadcrumb-item active">Creat Data</li>
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
                        <p class="card-title-desc">Data Kelas</p>

                        <form action="{{route ('kelas.store')}}" class="needs-validation" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="kelas">Kelas</label>
                                        <select id="tingkat" name="tingkat" class="form-control">
                                            <option value="" disabled selected>Pilih Tingkat</option>
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                    <label for="id_users" class="form-control-label">{{ __('SEKRETARIS') }}</label>
                                    <select name="id_users" class="form-control">
                                        <option value="">-- Pilih sekretaris --</option>
                                        @if(isset($users) && $users->count() > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('id_users') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Data tidak tersedia</option>
                                        @endif
                                    </select>
                                    @error('id_users')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                 <div class="mb-3">
                                        <label for="kelas">Jurusan</label>
                                        <select id="id_jurusan" name="id_jurusan" class="form-control">
                                            <option value="">- Pilih Jurusan -</option>
                                            @foreach($jurusan as $item)
                                            <option value="{{$item->id}}" >{{$item->nama_jurusan}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <input type="hidden" id="stt" name="stt" value="aktif">
                                    </div>
                                </div>
                            </div>

                         
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                <a href="/kelas" class="btn btn-secondary waves-effect waves-light">Cancel</a>
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