<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Supplier</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h3>Data Supplier</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Supplier</th>
                <th>Alamat Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->supplier_nama }}</td>
                <td>{{ $item->supplier_alamat ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
