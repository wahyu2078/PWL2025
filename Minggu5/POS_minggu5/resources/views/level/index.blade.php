@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('level/create') }}">Tambah</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!--  Filter Dropdown Kode Level -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter_kode">Filter Kode Level:</label>
                <select class="form-control" id="filter_kode">
                    <option value="">- Semua Kode Level -</option>
                    @foreach ($level_kode as $kode)
                        <option value="{{ $kode->level_kode }}">{{ $kode->level_kode }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabel Data -->
        <table class="table table-bordered table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Level</th>
                    <th>Nama Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('css')
<!-- Tambahkan custom CSS di sini jika diperlukan -->
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        let dataLevel = $('#table_level').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('level/list') }}",
                type: "POST",
                data: function (d) {
                    d.filter_kode = $('#filter_kode').val(); // Kirim kode level terpilih
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "level_kode", orderable: true, searchable: true },
                { data: "level_nama", orderable: true, searchable: true },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        $('#filter_kode').on('change', function() {
            dataLevel.ajax.reload(); // Reload tabel saat filter dropdown diubah
        });
    });
</script>
@endpush
