@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <h1>Daftar Kategori</h1>
    <ul>
        @foreach($kategori as $kat)
            <li>{{ $kat->nama_kategori }}</li>
        @endforeach
    </ul>
@endsection
