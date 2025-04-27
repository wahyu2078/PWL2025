@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Barang</h3>
        <div class="card-tools d-flex flex-wrap gap-1">
            <a href="{{ url('/barang/export_excel') }}" class="btn btn-sm btn-primary mt-1">
                <i class="fa fa-file-excel-o"></i> Export Barang
            </a>
            <a href="{{ url('/barang/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file- pdf"></i> Export Barang PDF</a>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/barang/import') }}" onclick="modalAction(this.getAttribute('data-url'))">
                Import Barang
            </button>
            <a href="{{ url('/barang/create') }}" class="btn btn-sm btn-primary mt-1">
                Tambah Data
            </a>
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/barang/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">
                Tambah Ajax
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filter Kategori -->
        <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_kategori" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select name="filter_kategori" class="form-control form-control-sm filter_kategori">
                                <option value="">- Semua -</option>
                                @foreach($kategori as $l)
                                <option value="{{ $l->kategori_id }}">{{ $l->kategori_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kategori Barang</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Tabel Barang -->
        <table class="table table-bordered table-sm table-striped table-hover" id="table-barang">
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
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    var tableBarang;
    $(document).ready(function() {
        tableBarang = $('#table-barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('barang/list') }}",
                type: "POST",
                data: function(d) {
                    d.filter_kategori = $('.filter_kategori').val();
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false },
                { data: "barang_kode", width: "10%" },
                { data: "barang_nama", width: "37%" },
                { 
                    data: "harga_beli", 
                    width: "10%", 
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                },
                { 
                    data: "harga_jual", 
                    width: "10%", 
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                },
                { data: "kategori.kategori_nama", width: "14%" },
                { data: "aksi", className: "text-center", width: "14%", orderable: false, searchable: false }
            ]
        });

        $('#table-barang_filter input').unbind().bind().on('keyup', function(e) {
            if (e.keyCode == 13) {
                tableBarang.search(this.value).draw();
            }
        });

        $('.filter_kategori').change(function() {
            tableBarang.draw();
        });
    });
</script>
@endpush
