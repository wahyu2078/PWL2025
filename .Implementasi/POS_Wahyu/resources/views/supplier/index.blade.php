@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/supplier/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/supplier/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import Supplier</button>
            <a href="{{ url('/supplier/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel-o"></i> Export Supplier Excel</a>
            <a href="{{ url('/supplier/export_pdf') }}" class="btn btn-warning btn-sm mt-1"><i class="fa fa-file-pdf-o"></i> Export Supplier PDF</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Tabel Supplier -->
        <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Supplier</th>
                    <th>Nama Supplier</th>
                    <th>Alamat Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal AJAX -->
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

    let dataSupplier;

    $(document).ready(function() {
        dataSupplier = $('#table_supplier').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('supplier/list') }}",
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "supplier_id"
                },
                {
                    data: "supplier_nama"
                },
                {
                    data: "supplier_alamat"
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Submit form delete via AJAX dari modal confirm_ajax
        $(document).on('submit', '#form-delete', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        dataSupplier.ajax.reload(null, false); // reload tanpa reset halaman
                    } else {
                        alert(response.message || 'Gagal menghapus data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        });
    });
</script>
@endpush