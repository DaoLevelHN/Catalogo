@extends('layout')

@section('title', $categoryMeta['name'] ?? 'Subcategorías')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $categoryMeta['name'] ?? $categorySlug }}</li>
        </ol>
    </nav>

    <h2 class="section-title">{{ $categoryMeta['name'] ?? $categorySlug }}</h2>
    
    @if(isset($categoryMeta['description']))
        <p class="text-muted mb-4">{{ $categoryMeta['description'] }}</p>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-2">
        @foreach($subcategories as $subcategory)
            <div class="col">
                <a href="{{ route('catalog.subcategory', ['category' => $categorySlug, 'subcategory' => $subcategory['slug']]) }}" class="text-decoration-none">
                    <div class="card catalog-card h-100">
                        <img src="{{ asset($subcategory['cover']) }}" class="card-img-top" alt="{{ $subcategory['name'] }}">
                        <div class="card-body text-center">
                            <h5 class="card-title text-dark">{{ $subcategory['name'] }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
