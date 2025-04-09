@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools d-flex flex-wrap gap-1">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('user/create') }}">Tambah</a>
            <button class="btn btn-sm btn-success mt-1" data-url="{{ url('/user/create_ajax') }}" onclick="modalAction(this.getAttribute('data-url'))">Tambah Ajax</button>
            <button class="btn btn-sm btn-info mt-1" data-url="{{ url('/user/import') }}" onclick="modalAction(this.getAttribute('data-url'))">Import</button>
            <a href="{{ url('/user/export_excel') }}" class="btn btn-sm btn-primary mt-1">
                <i class="fa fa-file-excel-o"></i> Export Excel
            </a>
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
                <label for="level_id" class="form-label">Filter Level</label>
                <select class="form-control form-control-sm" id="level_id" name="level_id">
                    <option value="">- Semua -</option>
                    @foreach($level as $item)
                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table User -->
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
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
    data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
@endsection

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
                type: 'POST',
                data: function(d) {
                    d.level_id = $('#level_id').val();
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    className: "text-center",
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
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Filter realtime
        $('#level_id').change(function() {
            dataUser.ajax.reload();
        });

        // Search on enter
        $('#table_user_filter input').unbind().bind().on('keyup', function(e) {
            if (e.keyCode == 13) {
                dataUser.search(this.value).draw();
            }
        });
    });
</script>
@endpush