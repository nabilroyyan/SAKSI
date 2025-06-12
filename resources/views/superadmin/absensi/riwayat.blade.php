@extends('layout.MainLayout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Riwayat Absensi Hari Ini - {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Absensi</a></li>
                                <li class="breadcrumb-item active">Riwayat Hari Ini</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                          @foreach($kelasList as $kelas)
                            <h5 class="alert-heading">Kelas: {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}</h5>
                        @endforeach
                        <p class="mb-0">Sekretaris Kelas: {{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if($riwayat->isEmpty())
                                <div class="alert alert-info">Belum ada absensi yang dicatat hari ini.</div>
                            @else
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Status</th>
                                                <th>Catatan</th>
                                                <th>Foto Surat</th>
                                                <th>Status Surat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayat as $index => $data)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $data->kelasSiswa->siswa->nama_siswa ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge text-white text-uppercase
                                                            @if($data->status == 'hadir') bg-success
                                                            @elseif($data->status == 'sakit') bg-warning
                                                            @elseif($data->status == 'izin') bg-info
                                                            @else bg-danger
                                                            @endif
                                                        ">
                                                            {{ ucfirst($data->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $data->catatan ?? '-' }}</td>
                                                    <td>
                                                         @if($data->foto_surat)
                                                            <a href="{{ asset('storage/' . $data->foto_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bx bx-file"></i> Lihat Surat
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Tidak Ada</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge text-white
                                                            @if($data->status_surat == 'diterima') bg-success
                                                            @else bg-secondary
                                                            @endif
                                                        ">
                                                            {{ $data->status_surat ? ucfirst($data->status_surat) : '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('hapusAbsensi', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus absensi ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="mdi mdi-delete-outline"></i> Hapus
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush