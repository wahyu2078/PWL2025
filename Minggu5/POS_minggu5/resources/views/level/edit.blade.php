@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>

    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Form Edit Level -->
        <form method="POST" action="{{ url('/level/' . $level->level_id) }}">
            @csrf
            @method('PUT') <!-- Gunakan PUT agar update sesuai dengan route -->

            <!-- Input Level Kode -->
            <div class="form-group row">
                <label class="col-2 col-form-label">Level Kode</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="level_kode" value="{{ old('level_kode', $level->level_kode) }}" required>
                    @error('level_kode')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Input Level Nama -->
            <div class="form-group row">
                <label class="col-2 col-form-label">Level Nama</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="level_nama" value="{{ old('level_nama', $level->level_nama) }}" required>
                    @error('level_nama')
                        <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Tombol Simpan & Kembali -->
            <div class="form-group row">
                <div class="col-10 offset-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a class="btn btn-default ml-1" href="{{ url('/level') }}">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
