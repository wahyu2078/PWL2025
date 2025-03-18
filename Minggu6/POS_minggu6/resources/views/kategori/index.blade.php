@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', 'Kategori')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori')

@section('content')
<div class="container">
    <h2>Manage Kategori</h2>

    <a href="{{ route('kategori.create') }}" class="btn btn-primary mb-3">+ Add Kategori</a>

    <div class="card">
        <div class="card-header">Manage Kategori</div>
        <div class="card-body">
            {!! $dataTable->table() !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
