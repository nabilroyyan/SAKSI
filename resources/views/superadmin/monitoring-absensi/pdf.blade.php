<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Monitoring Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
            color: #666;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
        }
        
        .summary-cards {
            display: flex;
            margin-bottom: 30px;
            gap: 15px;
        }
        
        .summary-card {
            flex: 1;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            color: white;
            font-weight: bold;
        }
        
        .card-primary { background-color: #007bff; }
        .card-success { background-color: #28a745; }
        .card-warning { background-color: #ffc107; color: #212529; }
        .card-danger { background-color: #dc3545; }
        
        .card-title {
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .card-value {
            font-size: 24px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        td {
            vertical-align: middle;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
        .badge-primary { background-color: #007bff; }
        
        .persentase-tinggi { color: #28a745; font-weight: bold; }
        .persentase-sedang { color: #ffc107; font-weight: bold; }
        .persentase-rendah { color: #dc3545; font-weight: bold; }
        
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .row-even {
            background-color: #f8f9fa;
        }
        
        .student-name {
            font-weight: bold;
            color: #212529;
        }
        
        .class-info {
            font-size: 10px;
            color: #666;
        }
        
        @media print {
            body { margin: 0; }
            .header { page-break-inside: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MONITORING ABSENSI</h1>
        <h2>Sistem Informasi Sekolah</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</div>
        </div>
        <div class="info-row">
            <div class="info-label">Filter Diterapkan:</div>
            <div class="info-value">{{ $filterInfo }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Data:</div>
            <div class="info-value">{{ $data->count() }} Siswa</div>
        </div>
    </div>
    
    @if($data->count() > 0)
        @php
            $totalSiswa = $data->count();
            $totalHadir = $data->sum('hadir');
            $totalSakit = $data->sum('sakit');
            $totalIzin = $data->sum('izin');
            $totalAlpa = $data->sum('alpa');
        @endphp
        
        <div class="summary-cards">
            <div class="summary-card card-primary">
                <div class="card-title">Total Siswa</div>
                <div class="card-value">{{ $totalSiswa }}</div>
            </div>
            <div class="summary-card card-success">
                <div class="card-title">Total Hadir</div>
                <div class="card-value">{{ $totalHadir }}</div>
            </div>
            <div class="summary-card card-warning">
                <div class="card-title">Sakit + Izin</div>
                <div class="card-value">{{ $totalSakit + $totalIzin }}</div>
            </div>
            <div class="summary-card card-danger">
                <div class="card-title">Total Alpa</div>
                <div class="card-value">{{ $totalAlpa }}</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Siswa</th>
                    <th width="15%">Kelas</th>
                    <th width="8%">Hadir</th>
                    <th width="8%">Sakit</th>
                    <th width="8%">Izin</th>
                    <th width="8%">Alpa</th>
                    <th width="8%">Total</th>
                    <th width="15%">Persentase Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    @php
                        $persentase = $item['total'] > 0 ? round(($item['hadir'] / $item['total']) * 100, 2) : 0;
                        $persentaseClass = $persentase >= 80 ? 'persentase-tinggi' : ($persentase >= 60 ? 'persentase-sedang' : 'persentase-rendah');
                        $rowClass = $index % 2 == 0 ? '' : 'row-even';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="student-name">{{ $item['siswa']->nama_siswa ?? 'N/A' }}</div>
                            <div class="class-info">NIS: {{ $item['siswa']->nis_nip ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            @if(isset($item['kelas_siswa']) && $item['kelas_siswa']->is_active == 'aktif')
                                <span class="badge badge-secondary">{{ $item['kelas_siswa']->kelas->tingkat ?? 'N/A' }}</span><br>
                                <small>{{ $item['kelas_siswa']->kelas->nama_kelas ?? '' }}</small><br>
                                <small class="class-info">{{ $item['kelas_siswa']->kelas->jurusan->nama_jurusan ?? '' }}</small>
                            @else
                                <span class="class-info">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $item['hadir'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $item['sakit'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-warning">{{ $item['izin'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">{{ $item['alpa'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $item['total'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $persentaseClass }}">{{ $persentase }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <strong>Tidak ada data absensi yang ditemukan dengan filter yang diterapkan.</strong>
        </div>
    @endif
    
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB</p>
        <p>Sistem Informasi Sekolah - Laporan Monitoring Absensi</p>
    </div>
</body>
</html> 