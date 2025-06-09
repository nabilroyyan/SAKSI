@extends('layout.MainLayout')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Periode Akademik</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('periodes.update', $periode->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="tahun" class="form-label">Tahun</label>
                    <input type="text" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun', $periode->tahun) }}" required>
                    @error('tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                        <option value="ganjil" @selected(old('semester', $periode->semester) == 'ganjil')>Ganjil</option>
                        <option value="genap" @selected(old('semester', $periode->semester) == 'genap')>Genap</option>
                    </select>
                    @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $periode->is_active))>
                    <label class="form-check-label" for="is_active">Aktifkan Periode Ini</label>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('periodes.show', $periode->id) }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection