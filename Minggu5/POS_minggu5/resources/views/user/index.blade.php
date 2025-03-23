@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('user/create') }}">Tambah</a>
        </div>
    </div>
    <div class="card-body">
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
@endsection

@push('css')
@endpush

@push('js')
<script>
$(document).ready(function() {
    $('#table_user').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('user/list') }}",
            type: 'POST',
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'username', name: 'username' },
            { data: 'nama', name: 'nama' },
            { data: 'level', name: 'level' },  // Pastikan ini sesuai
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});



</script>
@endpush
