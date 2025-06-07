@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <h4>Riwayat Absensi Hari Ini - {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</h4>

    @if($riwayat->isEmpty())
        <div class="alert alert-info">Belum ada absensi yang dicatat hari ini.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Foto Surat</th>
                    <th>Status Surat</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riwayat as $data)
                    <tr>
                        <td>{{ $data->siswa->nama_siswa ?? $data->siswa->nama }}</td>
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
                                <a href="{{ asset('storage/' . $data->foto_surat) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Lihat Surat
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                           <span class="badge text-white
                                @if($data->status_surat == 'diterima') bg-success
                                @else bg-secondary
                                @endif
                            ">
                                {{ ucfirst($data->status_surat) }}
                            </span>
                        </td>
                       <td>
                        <form action="{{ route('hapusAbsensi', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus absensi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
