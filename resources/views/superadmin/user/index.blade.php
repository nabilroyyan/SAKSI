@extends('layout.MainLayout')

@section('content')

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data User</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                <li class="breadcrumb-item active">Data User</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="filter-container">
                                    <form id="filterForm" action="{{ route('users.index') }}" method="GET" class="d-flex align-items-center">
                                        <label for="roleFilter" class="me-2">Filter Role:</label>
                                        <select id="roleFilter" name="role" class="form-select me-2" style="width: 150px;">
                                            <option value="">Semua Role</option>
                                            @foreach($data['roles'] as $role)
                                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-info btn-sm">Filter</button>
                                    </form>
                                </div>
                                @can('tambah user')                                   
                                <a href="{{route ('users.create')}}" class="btn btn-primary btn-rounded waves-effect waves-light">
                                    <i class="mdi mdi-plus me-1"> Tambah Data User </i>
                                </a>
                                @endcan
                            </div>
                            
                            <h4 class="card-title">Table User</h4>
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>nama</th>
                                    <th>role</th>
                                    <th>email</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                    @foreach($data['users'] as $index => $user)

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->getRoleNames()->first() }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @can('edit user')                                           
                                        <a href="{{route ('users.edit', $user->id)}}" class="btn btn-warning btn-sm">Edit</a>                                       
                                        @endcan
                                        @can('hapus user')                                           
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Delete</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
    </div>
    
@endsection