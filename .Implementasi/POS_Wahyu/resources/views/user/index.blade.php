@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/user/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/user/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import User</button>
            <a href="{{ url('/user/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel-o"></i> Export User Excel</a>
            <a href="{{ url('user/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf-o"></i> Export User PDF</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Filter Level:</label>
                <select class="form-control" id="level_id" name="level_id">
                    <option value="">- Semua -</option>
                    @foreach($level as $item)
                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Level Pengguna</small>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Level Pengguna</th>
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
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    var dataUser;
    $(document).ready(function() {
        dataUser = $('#table_user').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('user/list') }}",
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.level_id = $('#level_id').val();
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "username",
                    name: "username"
                },
                {
                    data: "nama",
                    name: "nama"
                },
                {
                    data: "level",
                    name: "level"
                },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#level_id').on('change', function() {
            dataUser.ajax.reload();
        });
    });
</script>
@endpush