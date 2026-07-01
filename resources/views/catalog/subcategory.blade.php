@extends('layout')

@section('title', $subcategoryMeta['name'] ?? 'Productos')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.category', ['category' => $categorySlug]) }}">{{ ucfirst(str_replace('_', ' ', $categorySlug)) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $subcategoryMeta['name'] ?? $subcategorySlug }}</li>
        </ol>
    </nav>

    <h2 class="section-title">{{ $subcategoryMeta['name'] ?? $subcategorySlug }}</h2>
    
    @if(isset($subcategoryMeta['description']))
        <p class="text-muted mb-4">{{ $subcategoryMeta['description'] }}</p>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-2">
        @foreach($products as $product)
            <div class="col">
                <a href="{{ route('catalog.product', ['category' => $categorySlug, 'subcategory' => $subcategorySlug, 'product' => $product['slug']]) }}" class="text-decoration-none">
                    <div class="card catalog-card h-100">
                        <img src="{{ asset($product['cover']) }}" class="card-img-top" alt="{{ $product['name'] }}">
                        <div class="card-body">
                            @if($product['brand'])
                                <small class="text-muted d-block mb-1">{{ $product['brand'] }}</small>
                            @endif
                            <h5 class="card-title text-dark">{{ $product['name'] }}</h5>
                            @if($product['price'])
                                <h4 class="text-danger fw-bold mt-3">L. {{ number_format($product['price'], 2) }}</h4>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
