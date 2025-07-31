<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h4>Detail Absensi Siswa: {{ $siswa->nama }}</h4>
<p>Filter: {{ $filterInfo }}</p>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $absen)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($absen->hari_tanggal)->format('d/m/Y') }}</td>
            <td>{{ ucfirst($absen->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>