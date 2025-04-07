@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary mt-1" href="{{ url('kategori/create') }}">Tambah</a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- 🚀 Filter Dropdown Kode Kategori -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter_kode">Filter Kode Kategori:</label>
                    <select class="form-control" id="filter_kode">
                        <option value="">- Semua Kode Kategori -</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->kategori_kode }}">{{ $item->kategori_kode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tabel Data Kategori -->
            <table class="table table-bordered table-hover table-sm" id="table_kategori">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Kategori</th>
                        <th>Nama Kategori</th>
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
            var dataKategori = $('#table_kategori').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ url('kategori/list') }}",
                    dataType: "json",
                    type: "POST",
                    data: function (d) {
                        d.filter_kode = $('#filter_kode').val(); // Kirim kode kategori yang dipilih
                    }
                },
                columns: [
                    { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                    { data: "kategori_kode", orderable: true, searchable: true },
                    { data: "kategori_nama", orderable: true, searchable: true },
                    { data: "aksi", orderable: false, searchable: false }
                ]
            });

            // Event onchange untuk filter kode kategori
            $('#filter_kode').on('change', function() {
                dataKategori.ajax.reload(); // Reload tabel saat filter berubah
            });
        });
    </script>
@endpush
