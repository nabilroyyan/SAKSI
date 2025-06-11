@extends('layout.MainLayout')

@section('content')
<div class="page-content">
<div class="container-fluid">

    <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tambah Data pelanggaran siswa</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('pelanggaran.index') }}">Data Kelas</a></li>
                                <li class="breadcrumb-item active">tambah Data</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>   
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('pelanggaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div x-data="{
                            kelasNama: '',
                            tingkat: '',
                            jurusan: '',
                            siswaId: '',
                            async fetchKelasSiswa(siswaId) {
                                if (!siswaId) {
                                    this.kelasNama = '';
                                    this.tingkat = '';
                                    this.jurusan = '';
                                    return;
                                }
                                
                                try {
                                    const response = await fetch(`/pelanggaran/get-kelas-siswa/${siswaId}`);
                                    if (!response.ok) throw new Error('Network response was not ok');
                                    
                                    const data = await response.json();
                                    
                                    this.kelasNama = data?.kelas?.kode_kelas ?? '—';
                                    this.tingkat = data?.kelas?.tingkat ?? '—';
                                    this.jurusan = data?.kelas?.jurusan?.nama_jurusan ?? '—';
                                } catch (error) {
                                    console.error('Error fetching kelas data:', error);
                                    this.kelasNama = 'Error';
                                    this.tingkat = 'Error';
                                    this.jurusan = 'Error';
                                }
                            }
                        }" x-init="
                            // Initialize Select2
                            $nextTick(() => {
                                $('#id_siswa').select2({
                                    placeholder: 'Pilih Siswa',
                                    allowClear: true,
                                    width: '100%'
                                }).on('change', function() {
                                    // Update Alpine.js state when Select2 changes
                                    siswaId = $(this).val();
                                    fetchKelasSiswa(siswaId);
                                });
                            });
                        ">

                            <!-- Select Siswa dengan Select2 -->
                            <div class="form-group">
                                <label for="id_siswa">Siswa</label>
                                <select class="form-control select2" id="id_siswa" name="id_siswa" x-model="siswaId" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($siswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nis_nip }} - {{ $s->nama_siswa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Info Kelas -->
                            <div class="form-group m-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tingkat_info">Tingkat</label>
                                        <input type="text" class="form-control" id="tingkat_info" name="tingkat_info"
                                            x-model="tingkat" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jurusan_info">Jurusan</label>
                                        <input type="text" class="form-control" id="jurusan_info" name="jurusan_info"
                                            x-model="jurusan" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tanggal">Tanggal Pelanggaran</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required 
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_skor_pelanggaran">Jenis Pelanggaran</label>
                            <select class="form-control select2" id="id_skor_pelanggaran" name="id_skor_pelanggaran" required>
                                <option value="">Pilih Jenis Pelanggaran</option>
                                @foreach($skor as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_pelanggaran }} (Skor: {{ $s->skor }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group m-2">
                            <label for="ket_pelanggaran">Keterangan Pelanggaran</label>
                            <textarea class="form-control" id="ket_pelanggaran" name="ket_pelanggaran" rows="3" required>{{ old('ket_pelanggaran') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="bukti_pelanggaran">Bukti Pelanggaran (Opsional)</label>
                            <div class="custom-file">
                                <input type="file" 
                                    class="custom-file-input" 
                                    id="bukti_pelanggaran" 
                                    name="bukti_pelanggaran"
                                    accept="image/*" 
                                    capture="environment">
                                <label class="custom-file-label" for="bukti_pelanggaran" id="label_bukti">Pilih file atau ambil foto</label>

                            </div>
                            <small class="form-text text-muted">Format: JPEG, PNG (Maks. 2MB)</small>
                            
                            <!-- Preview gambar (opsional) -->
                            <div class="mt-2">
                                <img id="preview" src="#" alt="Preview gambar" style="max-width: 200px; display: none;"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('pelanggaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')


<script>
    // Menampilkan nama file yang dipilih
    document.getElementById('bukti_pelanggaran').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var label = document.getElementById('label_bukti');

    if (file) {
        // Preview
        var reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('preview').style.display = 'block';
            document.getElementById('preview').src = event.target.result;
        };
        reader.readAsDataURL(file);

        // Tampilkan nama file
        label.textContent = file.name;

        // Validasi ukuran
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
            e.target.value = ''; // Reset input
            label.textContent = 'Pilih file atau ambil foto';
            document.getElementById('preview').style.display = 'none';
        }
    } else {
        label.textContent = 'Pilih file atau ambil foto';
        document.getElementById('preview').style.display = 'none';
    }
});


    $(document).ready(function() {
        // Inisialisasi Select2 untuk jenis pelanggaran
        $('#id_skor_pelanggaran').select2({
            placeholder: 'Pilih Jenis Pelanggaran',
            allowClear: true,
            width: '100%'
        });
        
        // Update nama file pada input file
        $('#bukti_pelanggaran').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endpush