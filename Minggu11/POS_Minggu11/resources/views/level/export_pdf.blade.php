<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Data Level</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h3>Data Level</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Level</th>
                <th>Nama Level</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levels as $i => $level)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $level->level_kode }}</td>
                <td>{{ $level->level_nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
