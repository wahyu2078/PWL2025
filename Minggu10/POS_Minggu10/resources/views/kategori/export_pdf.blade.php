<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h3>Data Kategori</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Kategori</th>
                <th>Nama Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kategori_kode }}</td>
                <td>{{ $item->kategori_nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
