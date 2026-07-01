@extends('layout')

@section('title', 'Catálogo de Productos')

@section('content')
    <h2 class="section-title">Categorías Principales</h2>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-2">
        @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('catalog.category', ['category' => $category['slug']]) }}" class="text-decoration-none">
                    <div class="card catalog-card h-100">
                        <img src="{{ asset($category['cover']) }}" class="card-img-top" alt="{{ $category['name'] }}">
                        <div class="card-body text-center">
                            <h5 class="card-title text-dark">{{ $category['name'] }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
