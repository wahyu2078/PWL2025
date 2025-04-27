@extends('layouts.template')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <div class="card-body">
        @if(empty($user))
            <div class="alert alert-danger alert-dismissible">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                Data yang Anda cari tidak ditemukan.
            </div>
            <a href="{{ url('/user') }}" class="btn btn-default mt-2">Kembali</a>
        @else
            <table class="table table-bordered">
                <tr>
                    <td><strong>ID</strong></td>
                    <td>{{ $user->user_id }}</td>
                </tr>
                <tr>
                    <td><strong>Level</strong></td>
                    <td>{{ $user->level->level_nama }}</td>
                </tr>
                <tr>
                    <td><strong>Username</strong></td>
                    <td>{{ $user->username }}</td>
                </tr>
                <tr>
                    <td><strong>Nama</strong></td>
                    <td>{{ $user->nama }}</td>
                </tr>
                <tr>
                    <td><strong>Password</strong></td>
                    <td>********</td>
                </tr>
            </table>

            <a href="{{ url('/user') }}" class="btn btn-default">Kembali</a>
        @endif
    </div>
</div>

@endsection

@push('css') 
@endpush

@push('js') 
@endpush
