<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">Laporan Data Stok</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stok as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->stok_tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $s->barang->barang_nama ?? '-' }}</td>
                    <td class="text-center">{{ $s->stok_jumlah }}</td>
                    <td>{{ $s->user->nama ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
