@extends('layout.MainLayout')

@section('content')
   <div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Input Absensi Hari Ini - {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</h4>
                    <!-- Breadcrumb remains the same -->
                     <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data Absensi</li>
                            </ol>
                        </div>
                </div>
            </div>
        </div>

        <!-- Add the class info box here -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    @foreach ($kelasList as $kelas)
                    <h5 class="alert-heading">Kelas: {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}</h5>
                    @endforeach
                    <p class="mb-0">Sekretaris Kelas: {{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>

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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if($siswaBelumAbsen->isEmpty())
                                <div class="alert alert-info">Semua siswa sudah diabsen hari ini.</div>
                            @else
                                <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="table-responsive">
                                        <table id="tableAbsensi" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th class="text-center">
                                                        Hadir<br>
                                                        <input type="checkbox" id="checkAllHadir" title="Check All Hadir" style="margin-top:4px;">
                                                    </th>
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
                                                            <input type="radio" class="radio-hadir" data-index="{{ $i }}" name="absensi[{{ $i }}][status]" value="hadir" onchange="toggleFotoSurat({{ $i }})" required>
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
                                                                   class="form-control foto-surat-{{ $i }}" 
                                                                   accept=".jpg,.jpeg,.png,.pdf" disabled>
                                                            <small class="text-muted">Wajib jika sakit/izin</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

    // Check All Hadir
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAllHadir');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const radios = document.querySelectorAll('.radio-hadir');
                radios.forEach(radio => {
                    radio.checked = checkAll.checked;
                    radio.dispatchEvent(new Event('change'));
                });
            });
        }

        // Initialize DataTable
        $('#tableAbsensi').DataTable({
            responsive: true,
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });
    });
</script>
@endpush

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush