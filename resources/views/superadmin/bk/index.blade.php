@extends('layout.MainLayout')

@section('content')
<div class="page-content">
<div class="container-fluid">
    <h1 class="h3 mb-2">Manajemen BK & Kelas</h1>

    <!-- Form Assign BK -->
    @can('assign bk')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assign BK ke Kelas</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.assign') }}" method="POST" id="assignForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih BK</label>
                            <select name="bk_id" class="form-control" required id="bkSelect">
                                <option value="">-- Pilih BK --</option>
                                @foreach($bks as $bk)
                                    <option value="{{ $bk->id }}">{{ $bk->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="d-flex justify-content-between align-items-center">
                                <span>Pilih Kelas</span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                                        <i class="fas fa-check-square"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="clearAllBtn">
                                        <i class="fas fa-square"></i> Bersihkan
                                    </button>
                                </div>
                            </label>
                            
                            <!-- Filter buttons -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Filter berdasarkan tingkat:</small>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info filter-btn active" data-filter="all">
                                        Semua Tingkat
                                    </button>
                                    @php
                                        $availableTingkat = $kelas->pluck('tingkat')->unique()->sort();
                                    @endphp
                                    @foreach($availableTingkat as $tingkat)
                                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="tingkat-{{ $tingkat }}">
                                            Tingkat {{ $tingkat }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Checkbox container -->
                            <div class="checkbox-container border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @php
                                    $kelasGrouped = $kelas->groupBy('tingkat');
                                @endphp
                                
                                @foreach($kelasGrouped as $tingkat => $kelasPerTingkat)
                                    <div class="tingkat-group mb-3" data-tingkat="{{ $tingkat }}">
                                        <h6 class="text-primary mb-2 border-bottom pb-1">
                                            <i class="fas fa-layer-group"></i> Tingkat {{ $tingkat }}
                                        </h6>
                                        <div class="row">
                                            @foreach($kelasPerTingkat as $k)
                                                @php
                                                    $sudahDiampu = false;
                                                    $pengampuBk = '';
                                                    $pengampuBkId = null;
                                                    foreach($bks as $bk) {
                                                        if($bk->kelasYangDiampuBk->contains('id', $k->id)) {
                                                            $sudahDiampu = true;
                                                            $pengampuBk = $bk->name;
                                                            $pengampuBkId = $bk->id;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-checkbox kelas-item {{ $sudahDiampu ? 'assigned' : 'available' }}" 
                                                         data-status="{{ $sudahDiampu ? 'assigned' : 'available' }}"
                                                         data-tingkat="{{ $tingkat }}"
                                                         data-pengampu-id="{{ $pengampuBkId }}">
                                                        <input type="checkbox" 
                                                               class="custom-control-input kelas-checkbox" 
                                                               id="kelas_{{ $k->id }}" 
                                                               name="kelas_ids[]" 
                                                               value="{{ $k->id }}"
                                                               data-tingkat="{{ $tingkat }}"
                                                               data-nama="{{ $k->tingkat }} {{ $k->jurusan->nama_jurusan }}"
                                                               {{ $sudahDiampu ? 'data-sudah-diampu=true' : '' }}
                                                               {{ $sudahDiampu ? 'data-pengampu='.$pengampuBk : '' }}>
                                                        <label class="custom-control-label d-flex justify-content-between align-items-center" 
                                                               for="kelas_{{ $k->id }}">
                                                            <span class="kelas-name">
                                                                {{ $k->tingkat }} {{ $k->jurusan->nama_jurusan }}
                                                            </span>
                                                            @if($sudahDiampu)
                                                                <small class="px-1 text-warning ml-2">
                                                                    <i class="fas fa-user"></i> {{ $pengampuBk }}
                                                                </small>
                                                            @else
                                                                <small class="text-success ml-2">
                                                                    <i class="fas fa-check-circle"></i> Tersedia
                                                                </small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> 
                                Kelas yang sudah diampu akan ditampilkan dengan nama BK pengampunya. 
                                Memilih kelas yang sudah diampu akan memindahkannya ke BK yang dipilih.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-info mr-2" id="selectedCount">0 kelas dipilih</span>
                                <span class="badge badge-warning" id="conflictCount" style="display: none;">0 konflik</span>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-save"></i> Assign Kelas
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan
                

    <!-- Daftar Assign BK -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar BK & Kelas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama BK</th>
                            <th>Kelas yang Diampu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bks as $bk)
                        <tr>
                            <td>
                                <strong>{{ $bk->name }}</strong>
                                @if($bk->kelasYangDiampuBk->count() > 0)
                                    <br><small class="text-success">
                                        <i class="fas fa-check-circle"></i> Mengampu {{ $bk->kelasYangDiampuBk->count() }} kelas
                                    </small>
                                @else
                                    <br><small class="text-muted">
                                        <i class="fas fa-exclamation-circle"></i> Belum mengampu kelas
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($bk->kelasYangDiampuBk->count() > 0)
                                    @foreach($bk->kelasYangDiampuBk as $kelas)
                                        <span class="badge badge-primary mr-1 mb-1 d-inline-flex text-black align-items-center">
                                            <i class="fas fa-graduation-cap mr-1 px-2"></i>
                                            {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}
                                            <a href="{{ route('bk.unassign', ['bkId' => $bk->id, 'kelasId' => $kelas->id]) }}" 
                                               class="text-white ml-2"
                                               style="margin-left:8px;"
                                               onclick="return confirm('Hapus assign kelas {{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}?')"
                                               title="Hapus assign kelas ini">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted font-italic">
                                        <i class="fas fa-minus"></i> Tidak ada kelas yang diampu
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                        data-bk-id="{{ $bk->id }}"
                                        data-kelas-ids="{{ $bk->kelasYangDiampuBk->pluck('id')->implode(',') }}"
                                        title="Edit assignment kelas untuk {{ $bk->name }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                @if($bk->kelasYangDiampuBk->count() > 0)
                                    <button type="button" class="btn btn-sm btn-danger ml-1" 
                                            onclick="if(confirm('Hapus semua assignment kelas untuk {{ $bk->name }}?')) { 
                                                window.location.href='{{ route('bk.unassign.all', $bk->id) }}' 
                                            }"
                                            title="Hapus semua assignment">
                                        <i class="fas fa-trash"></i> Clear All
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total BK Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bks->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kelas Sudah Terassign
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $kelasTerassign = 0;
                                    foreach($bks as $bk) {
                                        $kelasTerassign += $bk->kelasYangDiampuBk->count();
                                    }
                                @endphp
                                {{ $kelasTerassign }} / {{ $kelas->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    .checkbox-container {
        background-color: #f8f9fc;
    }
    
    .tingkat-group {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .kelas-item {
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
        margin-bottom: 5px;
    }
    
    .kelas-item:hover {
        background-color: #f1f3f4;
        transform: translateX(2px);
    }
    
    .kelas-item.assigned {
        background-color: #fff3cd;
        border-left: 3px solid #ffc107;
    }
    
    .kelas-item.available {
        background-color: #f8f9fa;
        border-left: 3px solid #28a745;
    }
    
    .kelas-item input[type="checkbox"]:checked + label {
        font-weight: bold;
        color: #007bff;
    }
    
    .custom-control-label {
        cursor: pointer;
        font-size: 0.9rem;
        width: 100%;
    }
    
    .filter-btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .kelas-item.hidden {
        display: none;
    }
    
    #selectedCount {
        font-size: 0.9rem;
    }
    
    #conflictCount {
        font-size: 0.9rem;
    }
    
    .badge-warning {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedCount = 0;
    let conflictCount = 0;
    
    // Update counters
    function updateCounters() {
        selectedCount = $('.kelas-checkbox:checked:visible').length;
        conflictCount = $('.kelas-checkbox:checked[data-sudah-diampu="true"]:visible').length;
        
        $('#selectedCount').text(selectedCount + ' kelas dipilih');
        
        if (conflictCount > 0) {
            $('#conflictCount').text(conflictCount + ' konflik').show();
        } else {
            $('#conflictCount').hide();
        }
        
        // Enable/disable submit button
        if (selectedCount > 0 && $('#bkSelect').val()) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }
    
    // BK selection change
    $('#bkSelect').change(function() {
        const selectedBkId = $(this).val();
        
        // Reset semua checkbox
        $('.kelas-checkbox').prop('checked', false);
        
        // Jika ada BK yang dipilih, otomatis check kelas yang sudah diampu BK tersebut
        if (selectedBkId) {
            $('.kelas-item[data-pengampu-id="' + selectedBkId + '"] .kelas-checkbox').prop('checked', true);
        }
        
        updateCounters();
    });
    
    // Checkbox change
    $(document).on('change', '.kelas-checkbox', function() {
        updateCounters();
    });
    
    // Select All - Only visible items
    $('#selectAllBtn').click(function() {
        $('.tingkat-group:visible .kelas-checkbox').prop('checked', true);
        updateCounters();
    });
    
    // Clear All
    $('#clearAllBtn').click(function() {
        $('.kelas-checkbox').prop('checked', false);
        updateCounters();
    });
    
    // Filter functionality - FIXED VERSION
    $(document).on('click', '.filter-btn', function(e) {
        e.preventDefault();
        
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        console.log('Filter clicked:', filter); // Debug log
        
        // Show all groups first
        $('.tingkat-group').show();
        
        if (filter !== 'all') {
            // Extract tingkat number from filter (e.g., 'tingkat-10' -> '10')
            const tingkatNumber = filter.replace('tingkat-', '');
            console.log('Tingkat number:', tingkatNumber); // Debug log
            
            // Hide all groups
            $('.tingkat-group').hide();
            
            // Show only the selected tingkat group
            $(`.tingkat-group[data-tingkat="${tingkatNumber}"]`).show();
            
            console.log('Showing groups with tingkat:', tingkatNumber); // Debug log
        }
        
        updateCounters();
    });
    
    // Edit button
    $('.edit-btn').click(function() {
        const bkId = $(this).data('bk-id');
        const kelasIds = $(this).data('kelas-ids').toString().split(',').filter(id => id !== '');
        
        // Set BK
        $('#bkSelect').val(bkId);
        
        // Reset dan set checkboxes
        $('.kelas-checkbox').prop('checked', false);
        kelasIds.forEach(function(kelasId) {
            $('#kelas_' + kelasId).prop('checked', true);
        });
        
        updateCounters();
        
        // Highlight form
        $('.card').first().addClass('border-warning');
        setTimeout(function() {
            $('.card').first().removeClass('border-warning');
        }, 2000);
        
        $('html, body').animate({
            scrollTop: $('.card').offset().top
        }, 500);
    });
    
    // Form submission confirmation
    $('#assignForm').on('submit', function(e) {
        if (conflictCount > 0) {
            const conflictNames = [];
            $('.kelas-checkbox:checked[data-sudah-diampu="true"]').each(function() {
                conflictNames.push($(this).data('nama') + ' (diampu oleh: ' + $(this).data('pengampu') + ')');
            });
            
            const message = 'Peringatan: Ada ' + conflictCount + ' kelas yang sudah diampu BK lain:\n\n' +
                           conflictNames.join('\n') + '\n\n' +
                           'Assignment ini akan memindahkan kelas tersebut ke BK yang dipilih. Lanjutkan?';
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        }
        
        // Pastikan ada minimal 1 kelas yang dipilih
        if (selectedCount === 0) {
            alert('Pilih minimal 1 kelas untuk di-assign!');
            e.preventDefault();
            return false;
        }
        
        // Pastikan BK sudah dipilih
        if (!$('#bkSelect').val()) {
            alert('Pilih BK terlebih dahulu!');
            e.preventDefault();
            return false;
        }
    });
    
    // Initialize
    updateCounters();
});
</script>
@endpush