@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/level/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/level/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import Level</button>
            <a href="{{ url('/level/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel-o"></i> Export Level Excel</a>
            <a href="{{ url('/level/export_pdf') }}" class="btn btn-warning btn-sm mt-1"><i class="fa fa-file-pdf-o"></i> Export Level PDF</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filter_level_kode">Filter Kode Level:</label>
                <select class="form-control" id="filter_level_kode">
                    <option value="">- Semua -</option>
                    @foreach ($level_kode as $item)
                    <option value="{{ $item->level_kode }}">{{ $item->level_kode }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Tampilkan berdasarkan kode level</small>
            </div>
        </div>

        <!-- Tabel -->
        <table class="table table-bordered table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Level</th>
                    <th>Nama Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal untuk Ajax -->
<div id="myModal" class="modal fade animate shake"
    tabindex="-1" role="dialog"
    data-backdrop="static"
    data-keyboard="false"
    aria-hidden="true">
</div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    let dataLevel;

    $(document).ready(function() {
        // Inisialisasi datatable
        dataLevel = $('#table_level').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('level/list') }}",
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.filter_level = $('#filter_level_kode').val();
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "level_kode"
                },
                {
                    data: "level_nama"
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Reload tabel saat filter dropdown berubah
        $('#filter_level_kode').on('change', function() {
            dataLevel.ajax.reload();
        });

        // Tombol Hapus: buka confirm_ajax
        $(document).on('click', '.btn-delete-level', function() {
            const url = $(this).data('url');
            modalAction(url);
        });
    });
</script>
@endpush