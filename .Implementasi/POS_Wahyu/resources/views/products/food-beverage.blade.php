@extends('layouts.app')

@section('title', 'Food & Beverage')

@section('content')
    <div class="container">
        <h1 class="mt-4 mb-3 text-center">Produk: Food & Beverage</h1>
        <p class="text-center">Kategori ini mencakup berbagai makanan dan minuman.</p>

        @if($products->isEmpty())
            <div class="alert alert-warning text-center">
                <strong>Belum ada produk dalam kategori ini.</strong>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-end">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
