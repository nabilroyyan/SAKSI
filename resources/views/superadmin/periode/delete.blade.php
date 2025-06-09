@extends('layout.MainLayout')

@section('content')
<div class="container">
    <h4>Manajemen Periode</h4>

    {{-- Form Tambah Periode --}}
    <div class="card mb-3">
        <div class="card-header">Tambah Periode</div>
        <div class="card-body">
            <form action="{{ route('periode.store') }}" method="POST">
                @csrf
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2024/2025" required>
                    </div>
                    <div class="col">
                        <select name="semester" class="form-control" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="col">
                        <select name="is_active" class="form-control" required>
                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Daftar Periode --}}
    <div class="card">
        <div class="card-header">Daftar Periode</div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tahun</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periodes as $periode)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $periode->tahun }}</td>
                            <td>{{ $periode->semester }}</td>
                            <td>
                                @if($periode->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('periode.edit', $periode->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('periode.destroy', $periode->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus periode ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($periodes->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data periode</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
