<?php

namespace App\Exports;

use App\Models\Absensi;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class MonitoringAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $request;
    protected $no = 1;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Absensi::with([
            'siswa',
            'kelasSiswa.kelas.jurusan',
            'petugas'
        ])->whereHas('kelasSiswa', function ($q) {
            $q->where('is_active', 'aktif');
        });

        // Apply filters
        if (!empty($this->request->nama_siswa)) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
            });
        }

        if (!empty($this->request->jurusan)) {
            $query->whereHas('kelasSiswa.kelas.jurusan', function ($q) {
                $q->where('nama_jurusan', 'like', '%' . $this->request->jurusan . '%');
            });
        }

        if (!empty($this->request->tanggal)) {
            $query->whereDate('hari_tanggal', $this->request->tanggal);
        }

        if (!empty($this->request->bulan)) {
            $query->whereMonth('hari_tanggal', $this->request->bulan);
        }

        if (!empty($this->request->tahun)) {
            $query->whereYear('hari_tanggal', $this->request->tahun);
        }

        $absensiList = $query->orderBy('hari_tanggal', 'desc')->get();

        // Group by siswa for summary
        $siswaAbsensi = $absensiList->groupBy('id_siswa')->map(function ($items) {
            $first = $items->first();
            return [
                'siswa' => $first->siswa,
                'kelas_siswa' => $first->kelasSiswa,
                'hadir' => $items->where('status', 'hadir')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->values();

        return new Collection($siswaAbsensi);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Jurusan',
            'Hadir',
            'Sakit',
            'Izin',
            'Alpa',
            'Total Absensi',
            'Persentase Kehadiran (%)',
            'Status Kehadiran'
        ];
    }

    public function map($siswaAbsensi): array
    {
        $persentase = $siswaAbsensi['total'] > 0 ?
            round(($siswaAbsensi['hadir'] / $siswaAbsensi['total']) * 100, 2) : 0;

        $statusKehadiran = $persentase >= 80 ? 'Baik' :
                          ($persentase >= 60 ? 'Cukup' : 'Kurang');

        return [
            $this->no++,
            $siswaAbsensi['siswa']->nama_siswa ?? '',
            $siswaAbsensi['siswa']->nisn ?? '',
            $siswaAbsensi['kelas_siswa']->kelas->nama_kelas ?? '',
            $siswaAbsensi['kelas_siswa']->kelas->jurusan->nama_jurusan ?? '',
            $siswaAbsensi['hadir'],
            $siswaAbsensi['sakit'],
            $siswaAbsensi['izin'],
            $siswaAbsensi['alpa'],
            $siswaAbsensi['total'],
            $persentase,
            $statusKehadiran
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style untuk semua data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:L' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Center alignment untuk kolom angka
        $sheet->getStyle('A1:A' . $highestRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F1:L' . $highestRow)->getAlignment()->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Monitoring Absensi ' . Carbon::now()->format('Y-m-d');
    }
}
