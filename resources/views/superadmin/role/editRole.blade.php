@extends('layout.MainLayout')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Edit Data Role</h4>
                    
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Edit</a></li>
                            <li class="breadcrumb-item active">Data Role</li>
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
                        <p class="card-title-desc">Edit Data Role</p>
                        
                        <form action="{{ route('roles.update', $role->id) }}" class="needs-validation" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="manufacturername">Role</label>
                                        <input id="name" name="name" type="text" class="form-control" placeholder="Edit role" value="{{ $role->name }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Pilih guard</label>
                                        <select id="guard_name" name="guard_name" class="form-control">
                                            <option value="web" {{ $role->guard_name == 'web' ? 'selected' : '' }}>web</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Role</button>
                                <a href="/role" class="btn btn-secondary waves-effect waves-light">Cancel</a>
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