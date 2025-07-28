{{-- resources/views/superadmin/monitoring-pelanggaran/detail-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Pelanggaran - {{ $siswa->nama_siswa }}</title>
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
        
        .student-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .student-info table {
            width: 100%;
            border: none;
        }
        
        .student-info td {
            padding: 5px;
            border: none;
            font-size: 12px;
        }
        
        .student-info .label {
            font-weight: bold;
            width: 150px;
            color: #333;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .summary-card.danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .summary-card.warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }
        
        .summary-card.info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        
        .summary-card h3 {
            margin: 0;
            font-size: 24px;
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
        
        .skor-badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        .skor-low { background-color: #28a745; }
        .skor-medium { background-color: #ffc107; color: #333; }
        .skor-high { background-color: #dc3545; }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .status-normal {
            background-color: #28a745;
            color: white;
        }
        
        .status-warning {
            background-color: #ffc107;
            color: #333;
        }
        
        .status-critical {
            background-color: #dc3545;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .recommendations {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
        }
        
        .recommendations h4 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }
        
        .recommendations li {
            margin-bottom: 5px;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Pelanggaran Siswa</h1>
        <h2>{{ $siswa->nama_siswa }}</h2>
    </div>
    
    <div class="student-info">
        <table>
            <tr>
                <td class="label">NIS:</td>
                <td>{{ $siswa->nis_nip ?? 'N/A' }}</td>
                <td class="label">Kelas:</td>
                <td>
                    @if($siswa->kelasSiswa && $siswa->kelasSiswa->is_active == 'aktif')
                        {{ $siswa->kelasSiswa->kelas->nama_kelas ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap:</td>
                <td>{{ $siswa->nama_siswa }}</td>
                <td class="label">Tingkat:</td>
                <td>
                    @if($siswa->kelasSiswa && $siswa->kelasSiswa->is_active == 'aktif')
                        {{ $siswa->kelasSiswa->kelas->tingkat ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
        </table>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card info">
            <h3>{{ $total_pelanggaran }}</h3>
            <p>Total Pelanggaran</p>
        </div>
        <div class="summary-card {{ $total_skor >= 1000 ? 'danger' : ($total_skor >= 500 ? 'warning' : 'info') }}">
            <h3>{{ $total_skor }}</h3>
            <p>Total Skor</p>
        </div>
        <div class="summary-card {{ $total_skor >= 1000 ? 'danger' : ($total_skor >= 500 ? 'warning' : 'info') }}">
            <div class="status-badge {{ $total_skor >= 1000 ? 'status-critical' : ($total_skor >= 500 ? 'status-warning' : 'status-normal') }}">
                @if($total_skor >= 1000)
                    KRITIS
                @elseif($total_skor >= 500)
                    PERINGATAN
                @else
                    NORMAL
                @endif
            </div>
            <p>Status Siswa</p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 35%;">Nama Pelanggaran</th>
                <th style="width: 8%;">Skor</th>
                <th style="width: 20%;">Petugas</th>
                <th style="width: 20%;">Keterangan</th>
                <th style="width: 20%;">Foto</th>

            </tr>
        </thead>
        <tbody>
            @forelse($pelanggarans as $pelanggaran)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $pelanggaran->skor_pelanggaran->nama_pelanggaran ?? 'N/A' }}</td>
                <td class="text-center">
                    @php
                    $skor = $pelanggaran->skor_pelanggaran->skor ?? 0;
                    @endphp
                    <span class="skor-badge {{ $skor >= 100 ? 'skor-high' : ($skor >= 50 ? 'skor-medium' : 'skor-low') }}">
                        {{ $skor }}
                    </span>
                </td>
                <td>{{ $pelanggaran->petugas->name ?? 'N/A' }}</td>
                <td>{{ $pelanggaran->keterangan ?? '-' }}</td>
                <td>
                    @if ($pelanggaran->bukti_pelanggaran)
                        <img src="{{ public_path('storage/' . $pelanggaran->bukti_pelanggaran) }}" style="width: 100px;">
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #666; font-style: italic;">
                    Tidak ada data pelanggaran
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($total_skor >= 500)
    <div class="recommendations">
        <h4>🔔 Rekomendasi Tindakan:</h4>
        <ul>
            @if($total_skor >= 1000)
                <li>Segera lakukan pemanggilan orang tua/wali</li>
                <li>Berikan bimbingan konseling intensif</li>
                <li>Pantau perkembangan siswa secara ketat</li>
                <li>Pertimbangkan sanksi tegas sesuai peraturan sekolah</li>
                <li>Koordinasi dengan guru mata pelajaran dan wali kelas</li>
            @else
                <li>Berikan peringatan dan bimbingan kepada siswa</li>
                <li>Lakukan konseling untuk mengidentifikasi masalah</li>
                <li>Pantau perilaku siswa secara berkala</li>
                <li>Koordinasi dengan orang tua jika diperlukan</li>
            @endif
        </ul>
    </div>
    @endif
    
    <div class="footer">
        <p><strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name ?? 'System' }}</p>
        <p>Sistem Informasi BK - {{ config('app.name', 'Sekolah') }}</p>
    </div>
</body>
</html> 