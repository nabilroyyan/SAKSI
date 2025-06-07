@extends('layout.MainLayout')  
@section('content')  

    <div class="container-fluid">
        <!-- Header Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Manage Permissions untuk Role: {{ $role->name }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Permission Management Card -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list-check mr-2"></i>
                                Pilih Permissions untuk Role: <span class="badge badge-primary">{{ $role->name }}</span>
                            </h5>
                            
                            <!-- Control Buttons -->
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success btn-sm m-2" id="selectAll">
                                    <i class="fas fa-check-double mr-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-warning btn-sm m-2" id="deselectAll">
                                    <i class="fas fa-times mr-1"></i>Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Permission Count Info -->
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Total tersedia: <span id="totalPermissions">{{ count($permissions) }}</span> permissions | 
                                Terpilih: <span id="selectedCount">{{ count($rolePermissions) }}</span> permissions
                            </small>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('roles.update-permissions', $role->id) }}" id="permissionForm">
                            @csrf

                             <!-- Action Buttons -->
                            <div class="form-group border-top pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save mr-2"></i>Simpan Permissions
                                        </button>
                                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-lg ml-2">
                                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                                        </a>
                                    </div>
                                    
                                    <div class="text-muted">
                                        <small>
                                            <i class="fas fa-clock mr-1"></i>
                                            Last updated: {{ $role->updated_at ? $role->updated_at->format('d M Y, H:i') : 'Never' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permissions Grid with Categories -->
                            <div class="permissions-container">
                                @php
                                    $groupedPermissions = collect($permissions)->groupBy(function($permission) {
                                        $parts = explode(' ', $permission->name);
                                        return count($parts) > 1 ? $parts[1] : 'general';
                                    });
                                @endphp
                                
                                @foreach($groupedPermissions as $category => $categoryPermissions)
                                    <div class="permission-category mb-4">
                                        <div class="category-header mb-3">
                                            <h6 class="text-uppercase text-muted font-weight-bold">
                                                <i class="fas fa-folder mr-2"></i>
                                                {{ ucfirst($category) }}
                                                <span class="badge badge-secondary ml-2">{{ count($categoryPermissions) }}</span>
                                            </h6>
                                            <hr class="mt-1 mb-3">
                                        </div>
                                        
                                        <div class="row">
                                            @foreach($categoryPermissions as $permission)
                                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                                    <div class="permission-item">
                                                        <div class="custom-control custom-checkbox">
                                                            <input 
                                                                class="custom-control-input permission-checkbox" 
                                                                type="checkbox" 
                                                                name="permissions[]" 
                                                                value="{{ $permission->id }}" 
                                                                id="permission_{{ $permission->id }}"
                                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                            >
                                                            <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                                <span class="permission-name">{{ $permission->name }}</span>
                                                                @if(in_array($permission->id, $rolePermissions))
                                                                    <i class="fas fa-check-circle text-success ml-1" style="font-size: 12px;"></i>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .permission-category {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #007bff;
        }
        
        .category-header h6 {
            margin-bottom: 0;
        }
        
        .permission-item {
            background: white;
            border-radius: 6px;
            padding: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            height: 100%;
        }
        
        .permission-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0,123,255,0.1);
        }
        
        .custom-control-input:checked ~ .custom-control-label .permission-item {
            background: #e3f2fd;
            border-color: #2196f3;
        }
        
        .permission-name {
            font-size: 14px;
            font-weight: 500;
        }
        
        .badge {
            font-size: 11px;
        }
        
        .card {
            border: none;
        }
        
        .card-header {
            border-bottom: 2px solid #dee2e6;
        }
        
        .btn-group .btn {
            border-radius: 4px !important;
            margin-left: 4px;
        }
        
        .btn-group .btn:first-child {
            margin-left: 0;
        }
    </style>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const form = document.getElementById('permissionForm');
            
            // Update selected count
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
                selectedCountSpan.textContent = checkedBoxes.length;
                
                // Update button states
                const allChecked = checkedBoxes.length === checkboxes.length;
                const noneChecked = checkedBoxes.length === 0;
                
                selectAllBtn.disabled = allChecked;
                deselectAllBtn.disabled = noneChecked;
            }
            
            // Select all permissions
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
                
                // Add visual feedback
                this.innerHTML = '<i class="fas fa-check mr-1"></i>All Selected';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-check-double mr-1"></i>Select All';
                }, 1000);
            });
            
            // Deselect all permissions
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
                
                // Add visual feedback
                this.innerHTML = '<i class="fas fa-check mr-1"></i>All Deselected';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-times mr-1"></i>Deselect All';
                }, 1000);
            });
            
            // Add change event to each checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            // Form submission confirmation
            form.addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu permission untuk role ini.');
                    return false;
                }
                
                const confirmMessage = `Apakah Anda yakin ingin menyimpan ${checkedCount} permissions untuk role "${document.querySelector('.badge-primary').textContent}"?`;
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Initialize count on page load
            updateSelectedCount();
            
            // Search functionality (bonus feature)
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control mb-3';
            searchInput.placeholder = 'Cari permission...';
            searchInput.id = 'permissionSearch';
            
            document.querySelector('.permissions-container').insertBefore(searchInput, document.querySelector('.permission-category'));
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const permissionItems = document.querySelectorAll('.permission-item');
                
                permissionItems.forEach(item => {
                    const permissionName = item.querySelector('.permission-name').textContent.toLowerCase();
                    const parentCol = item.closest('.col-lg-3');
                    
                    if (permissionName.includes(searchTerm)) {
                        parentCol.style.display = 'block';
                    } else {
                        parentCol.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection