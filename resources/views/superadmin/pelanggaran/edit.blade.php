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
                                <li class="breadcrumb-item"><a href="{{ route('pelanggaran.index') }}">Data Kelas</a></li>
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
                            <p class="card-title-desc">Edit data Pelanggaran</p>

                            <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="id_siswa">Nama Siswa</label>
                                            <select id="id_siswa" name="id_siswa" class="form-control" required>
                                                @foreach ($siswas as $siswa)
                                                    <option value="{{ $siswa->id }}" {{ $pelanggaran->id_siswa == $siswa->id ? 'selected' : '' }}>
                                                        {{ $siswa->nama_siswa }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ket_pelanggaran">Keterangan Pelanggaran</label>
                                            <input id="ket_pelanggaran" name="ket_pelanggaran" type="text" class="form-control"
                                                value="{{ $pelanggaran->ket_pelanggaran }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bukti_pelanggaran">Bukti Pelanggaran</label>
                                            <input id="bukti_pelanggaran" name="bukti_pelanggaran" type="file" class="form-control">
                                            @if($pelanggaran->bukti_pelanggaran)
                                                <a href="{{ asset('storage/' . $pelanggaran->bukti_pelanggaran) }}" target="_blank">Lihat Bukti</a>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="id_users" value="{{ auth()->user()->id }}">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="tanggal">Tanggal</label>
                                            <input id="tanggal" name="tanggal" type="date" class="form-control"
                                                value="{{ $pelanggaran->tanggal }}" required>
                                        </div>
                                        <div class="mb-3">
                                                    <label for="id_skor_pelanggaran" class="form-control-label">{{ __('Nama Pelanggaran') }}</label>
                                                <select id="id_skor_pelanggaran" name="id_skor_pelanggaran" class="form-control">
                                                    <option value="">-- Pilih Pelanggaran --</option>
                                                    @foreach($skors as $skor)
                                                        <option value="{{ $skor->id }}" {{ old('id_skor_pelanggaran', $pelanggaran->id_skor_pelanggaran ?? '') == $skor->id ? 'selected' : '' }}>{{ $skor->nama_pelanggaran }} ({{$skor->skor }})</option>
                                                    @endforeach
                                                </select>
                                                @error('id_skor_pelanggaran')
                                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
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