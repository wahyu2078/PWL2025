@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1"
                    data-url="{{ url('/kategori/create_ajax') }}"
                    onclick="modalAction(this.getAttribute('data-url'))">
                Tambah Ajax
            </button>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter Dropdown -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filter_kode_kategori">Filter Kode Kategori:</label>
                <select class="form-control" id="filter_kode_kategori">
                    <option value="">- Semua -</option>
                    @foreach ($kategori_kode as $item)
                        <option value="{{ $item->kategori_kode }}">{{ $item->kategori_kode }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Filter berdasarkan kode kategori</small>
            </div>
        </div>

        <!-- Table Kategori -->
        <table class="table table-bordered table-hover table-sm" id="table_kategori">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Ajax -->
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
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    let dataKategori;

    $(document).ready(function () {
        // Inisialisasi DataTables
        dataKategori = $('#table_kategori').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('kategori/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.filter_kode = $('#filter_kode_kategori').val();
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "kategori_kode" },
                { data: "kategori_nama" },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        // Filter saat select berubah
        $('#filter_kode_kategori').on('change', function () {
            dataKategori.ajax.reload();
        });

        // Button Hapus → arahkan ke confirm_ajax (bukan langsung delete)
        $(document).on('click', '.btn-delete-kategori', function () {
            const url = $(this).data('url');
            modalAction(url);
        });
    });
</script>
@endpush
