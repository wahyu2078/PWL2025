@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-success mt-1"
                data-url="{{ url('/level/create_ajax') }}"
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

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filter_level_kode">Filter Kode Level:</label>
                <select class="form-control" id="filter_level_kode">
                    <option value="">- Semua -</option>
                    @foreach ($level_kode as $item)
                        <option value="{{ $item->level_kode }}">{{ $item->level_kode }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Filter berdasarkan kode level</small>
            </div>
        </div>

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
     data-width="75%" aria-hidden="true">
</div>
@endsection

@push('css')
<!-- Tambahkan custom CSS jika dibutuhkan -->
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var dataLevel;
    $(document).ready(function () {
        dataLevel = $('#table_level').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('level/list') }}",
                type: "POST",
                dataType: "json",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.filter_level = $('#filter_level_kode').val();
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "level_kode" },
                { data: "level_nama" },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        $('#filter_level_kode').on('change', function () {
            dataLevel.ajax.reload();
        });

        $(document).on('click', '.btn-delete-level', function () {
            let url = $(this).data('url');
            let token = "{{ csrf_token() }}";

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: token },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Berhasil', response.message, 'success');
                                dataLevel.ajax.reload();
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
