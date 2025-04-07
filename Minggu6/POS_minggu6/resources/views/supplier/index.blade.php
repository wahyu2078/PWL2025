@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('supplier/create') }}">Tambah</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!--  Filter Dropdown Kode Supplier -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter_supplier">Filter Kode Supplier:</label>
                <select class="form-control" id="filter_supplier">
                    <option value="">- Semua Kode Supplier -</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--  Tabel Data Supplier -->
        <table class="table table-bordered table-hover table-sm" id="table_supplier">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Supplier</th>
                    <th>Nama Supplier</th>
                    <th>Alamat Supplier</th>
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
        //  Inisialisasi DataTable
        let dataSupplier = $('#table_supplier').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ url('supplier/list') }}",
                dataType: "json",
                type: "POST",
                data: function (d) {
                    d.supplier_id = $('#filter_supplier').val();  // 🛠 Kirim filter ke backend
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "supplier_id", orderable: true, searchable: true },
                { data: "supplier_nama", orderable: true, searchable: true },
                { data: "supplier_alamat", orderable: true, searchable: true },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        //  Reload DataTables saat dropdown diubah
        $('#filter_supplier').on('change', function() {
            dataSupplier.ajax.reload();
        });
    });
</script>
@endpush
