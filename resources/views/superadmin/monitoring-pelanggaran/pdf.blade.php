{{-- resources/views/superadmin/monitoring-pelanggaran/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Pelanggaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        
        .info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .summary-card h3 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        
        .summary-card p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        td {
            vertical-align: middle;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-normal {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-warning {
            background-color: #ffc107;
            color: #333;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-critical {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .skor-badge {
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        .skor-normal { background-color: #28a745; }
        .skor-warning { background-color: #ffc107; color: #333; }
        .skor-critical { background-color: #dc3545; }
        
        .jumlah-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Monitoring Pelanggaran Siswa</h1>
        <h2>Sistem Informasi BK</h2>
    </div>
    
    <div class="info-section">
        <div class="info-left">
            <strong>Filter:</strong> {{ $filterInfo }}<br>
            <strong>Total Siswa:</strong> {{ $total_siswa }} siswa<br>
            <strong>Total Pelanggaran:</strong> {{ $total_pelanggaran }} pelanggaran
        </div>
        <div class="info-right">
            <strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}<br>
            <strong>Dicetak oleh:</strong> {{ auth()->user()->name ?? 'System' }}
        </div>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <h3>{{ $total_siswa }}</h3>
            <p>Total Siswa</p>
        </div>
        <div class="summary-card">
            <h3>{{ $siswaPelanggar->filter(fn($s) => $s['total_skor'] < 500)->count() }}</h3>
            <p>Status Normal</p>
        </div>
        <div class="summary-card">
            <h3>{{ $siswaPelanggar->filter(fn($s) => $s['total_skor'] >= 500 && $s['total_skor'] < 1000)->count() }}</h3>
            <p>Status Peringatan</p>
        </div>
        <div class="summary-card">
            <h3>{{ $siswaPelanggar->filter(fn($s) => $s['total_skor'] >= 1000)->count() }}</h3>
            <p>Status Kritis</p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 20%;">Kelas</th>
                <th style="width: 8%;">Tingkat</th>
                <th style="width: 10%;">Jml Pelanggaran</th>
                <th style="width: 10%;">Total Skor</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswaPelanggar as $id_siswa => $data)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $data['siswa']->nis_nip ?? 'N/A' }}</td>
                <td>{{ $data['siswa']->nama_siswa ?? 'N/A' }}</td>
                <td>
                    @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                        {{ $data['kelas_siswa']->kelas->jurusan->nama_jurusan ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
                <td class="text-center">
                    @if(isset($data['kelas_siswa']) && $data['kelas_siswa']->is_active == 'aktif')
                        {{ $data['kelas_siswa']->kelas->tingkat ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
                <td class="text-center">
                    <span class="jumlah-badge">{{ $data['jumlah_pelanggaran'] }}</span>
                </td>
                <td class="text-center">
                    <span class="skor-badge {{ $data['total_skor'] >= 1000 ? 'skor-critical' : ($data['total_skor'] >= 500 ? 'skor-warning' : 'skor-normal') }}">
                        {{ $data['total_skor'] }}
                    </span>
                </td>
                <td class="text-center">
                    @if($data['total_skor'] >= 1000)
                        <span class="status-critical">KRITIS</span>
                    @elseif($data['total_skor'] >= 500)
                        <span class="status-warning">PERINGATAN</span>
                    @else
                        <span class="status-normal">NORMAL</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($siswaPelanggar->filter(fn($s) => $s['total_skor'] >= 1000)->count() > 0)
    <div style="margin-top: 30px; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
        <h3 style="color: #721c24; margin: 0 0 10px 0;">⚠️ PERHATIAN - SISWA STATUS KRITIS</h3>
        <p style="margin: 0; color: #721c24; font-size: 11px;">
            Terdapat <strong>{{ $siswaPelanggar->filter(fn($s) => $s['total_skor'] >= 1000)->count() }} siswa</strong> 
            dengan status KRITIS (skor ≥ 1000). Diperlukan tindakan segera dari pihak BK dan orang tua.
        </p>
    </div>
    @endif
    
    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>Sistem Informasi BK - {{ config('app.name', 'Sekolah') }}</p>
    </div>
</body>
</html>