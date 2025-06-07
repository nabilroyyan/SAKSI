@extends('layout.mainlayout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Data Pelanggaran</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Pelanggaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div x-data="{ kelasNama: '', tingkat: '', jurusan: '', siswaId: '' }" x-init="
                            $watch('siswaId', async value => {
                                if (!value) {
                                    kelasNama = tingkat = jurusan = '';
                                    return;
                                }
                                try {
                                    const res = await fetch(`/pelanggaran/get-kelas-siswa/${value}`, {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                                    });
                                    const data = await res.json();

                                    kelasNama = data?.kelas?.kode_kelas ?? '—';
                                    tingkat    = data?.kelas?.tingkat    ?? '—';
                                    jurusan    = data?.kelas?.jurusan?.nama_jurusan ?? '—';
                                } catch {
                                    kelasNama = tingkat = jurusan = 'Error';
                                }
                            });
                        ">

                            <!-- Select Siswa -->
                            <div class="form-group">
                                <label for="id_siswa">Siswa</label>
                                <select class="form-control" id="id_siswa" name="id_siswa" x-model="siswaId" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($siswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nis_nip }} - {{ $s->nama_siswa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tingkat -->
                            <div class="form-group">
                                <label for="tingkat_info">Tingkat</label>
                                <input type="text" class="form-control mb-2" id="tingkat_info" name="tingkat_info"
                                    x-model="tingkat" readonly>
                                <label for="jurusan_info">Jurusan</label>
                                <input type="text" class="form-control" id="jurusan_info" name="jurusan_info"
                                    x-model="jurusan" readonly>
                            </div>

                        </div>


                        <div class="form-group">
                            <label for="tanggal">Tanggal Pelanggaran</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                                   value="{{ old('tanggal', date('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_skor_pelanggaran">Jenis Pelanggaran</label>
                            <select class="form-control" id="id_skor_pelanggaran" name="id_skor_pelanggaran" required>
                                <option value="">Pilih Jenis Pelanggaran</option>
                                @foreach($skor as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_pelanggaran }} (Skor: {{ $s->skor }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ket_pelanggaran">Keterangan Pelanggaran</label>
                            <textarea class="form-control" id="ket_pelanggaran" name="ket_pelanggaran" rows="3" required>{{ old('ket_pelanggaran') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="bukti_pelanggaran">Bukti Pelanggaran (Opsional)</label>
                            <input type="file" class="form-control-file" id="bukti_pelanggaran" name="bukti_pelanggaran">
                            <small class="form-text text-muted">Format: JPEG, PNG (Maks. 2MB)</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('pelanggaran.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
