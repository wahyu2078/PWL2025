@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/stok/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/stok/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import Stok</button>
            <a href="{{ url('/stok/export_excel') }}" class="btn btn-sm btn-primary mt-1">Export Excel</a>
            <a href="{{ url('/stok/export_pdf') }}" class="btn btn-sm btn-warning mt-1" target="_blank">Export PDF</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>User Pembuat</th>
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
    data-width="75%" aria-hidden="true">
</div>
@endsection

@push('css')
<!-- Tambahkan CSS tambahan di sini jika diperlukan -->
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    function hapusData(url = '') {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.status) {
                        tableStok.ajax.reload();
                        alert(res.message);
                    } else {
                        alert('Gagal menghapus data');
                    }
                }
            });
        }
    }

    var tableStok;
    $(document).ready(function () {
        tableStok = $('#table_stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/stok/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "barang_nama",
                    name: "barang_nama"
                },
                {
                    data: "stok_jumlah",
                    name: "stok_jumlah",
                    className: "text-center"
                },
                {
                    data: "stok_tanggal",
                    name: "stok_tanggal",
                    className: "text-center"
                },
                {
                    data: "user_nama",
                    name: "user_nama"
                },
                {
                    data: "aksi",
                    name: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endpush
