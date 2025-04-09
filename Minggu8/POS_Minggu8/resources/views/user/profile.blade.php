@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header"><h5>Edit Profil</h5></div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ url('/profile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="form-control" required>
                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label>Foto Profil</label><br>
                <img src="{{ $user->foto ? asset('uploads/user/' . $user->foto) : asset('images/default.png') }}" width="100" class="rounded mb-2">
                <input type="file" name="foto" class="form-control-file">
                @error('foto') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
