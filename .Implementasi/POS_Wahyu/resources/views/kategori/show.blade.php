@extends('layouts.app')

@section('title', 'Produk dalam ' . $category->name)

@section('content')
    <div class="container">
        <h1>Produk dalam kategori: {{ $category->name }}</h1>

        @if($products->count() > 0)
            <ul>
                @foreach ($products as $product)
                    <li>{{ $product->name }} - Rp {{ number_format($product->price, 2, ',', '.') }}</li>
                @endforeach
            </ul>
        @else
            <p>Tidak ada produk dalam kategori ini.</p>
        @endif
    </div>
@endsection
