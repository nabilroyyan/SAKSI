@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <h4>Input Absensi Hari Ini - {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($siswaBelumAbsen->isEmpty())
        <div class="alert alert-info">Semua siswa sudah diabsen hari ini.</div>
    @else
        <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Catatan</th>
                        <th>Foto Surat (Jika Sakit/Izin)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswaBelumAbsen as $i => $data)
                        <tr>
                            <td>{{ $data->siswa->nama_siswa }}</td>
                            <td class="text-center">
                                <input type="radio" name="absensi[{{ $i }}][status]" value="hadir" onchange="toggleFotoSurat({{ $i }})" required>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="absensi[{{ $i }}][status]" value="sakit" onchange="toggleFotoSurat({{ $i }})" required>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="absensi[{{ $i }}][status]" value="izin" onchange="toggleFotoSurat({{ $i }})" required>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="absensi[{{ $i }}][status]" value="alpa" onchange="toggleFotoSurat({{ $i }})" required>                         
                            </td>
                            <td>
                                <input type="text" name="absensi[{{ $i }}][catatan]" class="form-control">
                                <input type="hidden" name="absensi[{{ $i }}][kelas_siswa_id]" value="{{ $data->id }}">
                            </td>
                            <td>
                                <input type="file" name="absensi[{{ $i }}][foto_surat]" 
                                       class="form-control-file foto-surat-{{ $i }}" 
                                       accept=".jpg,.jpeg,.png,.pdf" disabled>
                                <small class="text-muted">Wajib jika sakit/izin</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
        </form>
    @endif
</div>

<script>
function toggleFotoSurat(index) {
    const radioButtons = document.querySelectorAll(`input[name="absensi[${index}][status]"]`);
    const fotoSuratInput = document.querySelector(`.foto-surat-${index}`);
    
    let selectedValue = '';
    radioButtons.forEach(radio => {
        if (radio.checked) {
            selectedValue = radio.value;
        }
    });
    
    // Enable/disable foto surat input berdasarkan status
    if (selectedValue === 'sakit' || selectedValue === 'izin') {
        fotoSuratInput.disabled = false;
        fotoSuratInput.required = true;
        fotoSuratInput.parentElement.classList.add('bg-light');
    } else {
        fotoSuratInput.disabled = true;
        fotoSuratInput.required = false;
        fotoSuratInput.value = '';
        fotoSuratInput.parentElement.classList.remove('bg-light');
    }
}
</script>

<style>
.table th, .table td {
    vertical-align: middle;
}
.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection
