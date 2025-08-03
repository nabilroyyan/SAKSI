<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Siswa - {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        
        .info-table .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 30%;
        }
        
        .section-title {
            background-color: #333;
            color: white;
            padding: 8px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        
        .data-table td {
            font-size: 10px;
        }
        
        .statistik {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .statistik-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 10px;
            background-color: white;
            border: 1px solid #ddd;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-lulus { background-color: #d4edda; color: #155724; }
        .status-pindah { background-color: #fff3cd; color: #856404; }
        .status-keluar { background-color: #f8d7da; color: #721c24; }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        @media print {
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN RIWAYAT SISWA</h1>
        <h2>{{ strtoupper($kelas->tingkat) }} {{ strtoupper($kelas->nama_kelas) }}</h2>
    </div>

    <!-- Informasi Siswa -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">NIS</td>
                <td>{{ $siswa->nis_nip }}</td>
                <td class="label">Nama Lengkap</td>
                <td>{{ $siswa->nama_siswa }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td>{{ $siswa->jenis_kelamin }}</td>
                <td class="label">Kelas</td>
                <td>{{ $kelas->tingkat }} {{ $kelas->jurusan->nama_jurusan }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td>{{ $periode->semester }} {{ $periode->tahun }}</td>
                <td class="label">Status Akhir</td>
                <td>
                    <span class="status-badge status-{{ $kelasSiswa->status }}">
                        {{ ucfirst($kelasSiswa->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Statistik -->
    <div class="statistik">
        <h3 style="margin-top: 0;">Ringkasan Statistik</h3>
        <div class="statistik-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['total_absensi'] }}</div>
                <div class="stat-label">Total Absensi</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['hadir'] }}</div>
                <div class="stat-label">Hadir</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['alpha'] }}</div>
                <div class="stat-label">Alpha</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['total_pelanggaran'] }}</div>
                <div class="stat-label">Pelanggaran</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['total_poin_pelanggaran'] }}</div>
                <div class="stat-label">Total Poin</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $statistik['total_tindakan'] }}</div>
                <div class="stat-label">Tindakan</div>
            </div>
        </div>
    </div>

    <!-- Data Absensi -->
    <div class="section-title">DATA ABSENSI</div>
    @if($absensi->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th width="15%">Hari</th>
                    <th width="15%">Status</th>
                    <th width="45%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absensi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->hari_tanggal)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->hari_tanggal)->locale('id')->dayName }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada data absensi</div>
    @endif

    <!-- Data Pelanggaran -->
    <div class="section-title">DATA PELANGGARAN</div>
    @if($pelanggaran->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="30%">Jenis Pelanggaran</th>
                    <th width="10%">Poin</th>
                    <th width="40%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pelanggaran as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_pelanggaran }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->poin }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada data pelanggaran</div>
    @endif

    <!-- Data Tindakan -->
    <div class="section-title">DATA TINDAKAN</div>
    @if($tindakan->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="30%">Jenis Tindakan</th>
                    <th width="50%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tindakan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_tindakan }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">Tidak ada data tindakan</div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>Laporan ini dihasilkan secara otomatis oleh sistem</p>
    </div>
</body>
</html>