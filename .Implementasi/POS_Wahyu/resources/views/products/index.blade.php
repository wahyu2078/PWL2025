@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
    <div class="container">
        <h1 class="mt-4 mb-3 text-center">Daftar Produk</h1>
        <p class="text-center">Berikut adalah daftar semua produk yang tersedia.</p>

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('products.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </a>
        </div>

        @if($products->isEmpty())
            <div class="alert alert-warning text-center">
                <strong>Belum ada produk yang tersedia.</strong>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-center">{{ $product->category->name ?? 'Tidak ada kategori' }}</td>
                                <td class="text-end">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-info btn-sm"><i class="bi bi-eye"></i> Lihat</a>
                                    <a href="#" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
