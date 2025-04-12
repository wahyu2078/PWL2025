@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/barang/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <a href="{{ url('/barang/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel-o"></i> Export Barang</a>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/barang/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import Barang</button>
            <a href="{{ url('/barang/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file- pdf"></i> Export Barang PDF</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter Kategori -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kategori_id">Filter Kategori Barang:</label>
                <select class="form-control" id="kategori_id" name="kategori_id">
                    <option value="">- Semua -</option>
                    @foreach ($kategori as $item)
                    <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Filter berdasarkan kategori</small>
            </div>
        </div>

        <!-- Tabel Barang -->
        <table class="table table-bordered table-striped table-hover table-sm" id="table_barang">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Kategori</th>
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

    let dataBarang;

    $(document).ready(function() {
        dataBarang = $('#table_barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('barang/list') }}",
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.kategori_id = $('#kategori_id').val();
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "barang_kode"
                },
                {
                    data: "barang_nama"
                },
                {
                    data: "harga_beli"
                },
                {
                    data: "harga_jual"
                },
                {
                    data: "kategori.kategori_nama",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#kategori_id').on('change', function() {
            dataBarang.ajax.reload();
        });

        // Menangani form delete dari modal confirm_ajax
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
                        dataBarang.ajax.reload(null, false); // reload tanpa reset halaman
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