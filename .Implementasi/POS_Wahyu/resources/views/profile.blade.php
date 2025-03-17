@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <h1>Profil Pengguna</h1>
    <p><strong>ID:</strong> {{ $id }}</p>
    <p><strong>Nama:</strong> {{ $name }}</p>
@endsection
