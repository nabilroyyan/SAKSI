@extends('layout.MainLayout')  
@section('content')  

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-shield-alt mr-2 text-primary"></i>
                Manage Permissions for Role: <span class="badge bg-primary">{{ $role->name }}</span>
            </h4>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>
        
        <!-- Main Card -->
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <form method="POST" action="{{ route('roles.update-permissions', $role->id) }}" id="permissionForm">
                    @csrf

                    <!-- Control Buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm" id="selectAll">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="deselectAll">
                                <i class="fas fa-times mr-1"></i>Deselect All
                            </button>
                        </div>
                        
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Total: <span id="totalPermissions">{{ count($permissions) }}</span> | 
                            Selected: <span id="selectedCount">{{ count($rolePermissions) }}</span>
                        </small>
                    </div>
                    
                    <!-- Permissions Grid -->
                    <div class="permissions-container">
                        @php
                            $groupedPermissions = collect($permissions)->groupBy(function($permission) {
                                $parts = explode(' ', $permission->name);
                                return count($parts) > 1 ? $parts[1] : 'general';
                            });
                        @endphp
                        
                        @foreach($groupedPermissions as $category => $categoryPermissions)
                            <div class="mb-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-2">
                                    <i class="fas fa-folder-open me-1"></i>
                                    {{ ucfirst($category) }}
                                    <span class="badge bg-secondary ms-1">{{ count($categoryPermissions) }}</span>
                                </h6>
                                
                                <div class="row g-2">
                                    @foreach($categoryPermissions as $permission)
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input permission-checkbox" 
                                                    type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->id }}" 
                                                    id="permission_{{ $permission->id }}"
                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label small" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                    @if(in_array($permission->id, $rolePermissions))
                                                        <i class="fas fa-check-circle text-success ms-1" style="font-size: 10px;"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Save Permissions
                        </button>
                        <small class="text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            Last updated: {{ $role->updated_at ? $role->updated_at->format('d M Y, H:i') : 'Never' }}
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .permissions-container {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .form-check-label {
            font-size: 0.85rem;
            cursor: pointer;
        }
        
        .form-check-input {
            margin-top: 0.2rem;
        }
        
        .card {
            border: none;
            border-radius: 8px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const form = document.getElementById('permissionForm');
            
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
                selectedCountSpan.textContent = checkedBoxes.length;
                
                selectAllBtn.disabled = checkedBoxes.length === checkboxes.length;
                deselectAllBtn.disabled = checkedBoxes.length === 0;
            }
            
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => checkbox.checked = true);
                updateSelectedCount();
            });
            
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => checkbox.checked = false);
                updateSelectedCount();
            });
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            form.addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one permission for this role.');
                    return false;
                }
            });
            
            updateSelectedCount();
        });
    </script>
@endsection