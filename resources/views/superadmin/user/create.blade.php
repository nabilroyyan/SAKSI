@extends('layout.MainLayout')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data user</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                                <li class="breadcrumb-item active">Data user</li>
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
                            <p class="card-title-desc">Data User</p>

                            <form action="{{ route('users.store') }}" method="POST" class="needs-validation">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="name">Nama</label>
                                            <input id="name" name="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" placeholder="Nama"
                                                value="{{ old('name') }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email">Email</label>
                                            <input id="email" name="email" type="text"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Masukkan email" value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="role">Pilih Role</label>
                                            <select id="role" name="role"
                                                class="form-control @error('role') is-invalid @enderror">
                                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih
                                                    Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <!-- 1. Tambahkan ID pada div pembungkus ini -->
                                        <div class="mb-3" id="nis_nip_container">
                                            <label for="nis_nip">NIS/NIP</label>
                                            <input id="nis_nip" name="nis_nip" type="text"
                                                class="form-control @error('nis_nip') is-invalid @enderror"
                                                placeholder="Masukkan NIS (khusus siswa)" value="{{ old('nis_nip') }}">
                                            @error('nis_nip')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password">Password</label>
                                            <input id="password" name="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Masukkan password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save
                                        Changes</button>
                                    <a href="/users" class="btn btn-secondary waves-effect waves-light">Cancel</a>
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const nisNipContainer = document.getElementById('nis_nip_container');

            function toggleNisNipField() {
                // Ambil teks dari opsi yang dipilih dan ubah ke huruf kecil
                const selectedOptionText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

                // Jika role yang dipilih adalah 'siswa', tampilkan input NIS/NIP
                if (selectedOptionText === 'siswa') {
                    nisNipContainer.style.display = 'block';
                } else {
                    // Jika bukan, sembunyikan
                    nisNipContainer.style.display = 'none';
                }
            }

            // Jalankan fungsi saat ada perubahan pada dropdown role
            roleSelect.addEventListener('change', toggleNisNipField);

            // Jalankan fungsi saat halaman pertama kali dimuat untuk mengatur kondisi awal
            toggleNisNipField();
        });
    </script>
@endpush
