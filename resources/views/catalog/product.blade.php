@extends('layout')

@section('title', $productMeta['name'] ?? 'Producto')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.category', ['category' => $categorySlug]) }}">{{ ucfirst(str_replace('_', ' ', $categorySlug)) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.subcategory', ['category' => $categorySlug, 'subcategory' => $subcategorySlug]) }}">{{ ucfirst(str_replace('_', ' ', $subcategorySlug)) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $productMeta['name'] ?? $productSlug }}</li>
        </ol>
    </nav>

    <div class="row mt-4">
        <!-- Galería de imágenes (Carrusel) -->
        <div class="col-md-6 mb-4">
            <div id="productCarousel" class="carousel slide product-gallery" data-bs-ride="carousel">
                <div class="carousel-inner" id="carouselInner">
                    @foreach($firstColorImages as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ asset($image) }}" class="d-block w-100" alt="Producto">
                        </div>
                    @endforeach
                    
                    @if(empty($firstColorImages))
                         <div class="carousel-item active">
                            <img src="{{ asset($productMeta['cover']) }}" class="d-block w-100" alt="Producto">
                        </div>
                    @endif
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>

        <!-- Detalles del producto -->
        <div class="col-md-6">
            @if(isset($productMeta['brand']))
                <p class="text-muted mb-1 text-uppercase tracking-wide">{{ $productMeta['brand'] }}</p>
            @endif
            <h1 class="fw-bold mb-3">{{ $productMeta['name'] ?? $productSlug }}</h1>
            
            @if(isset($productMeta['price']))
                <h2 class="text-danger fw-bold mb-4">L. {{ number_format($productMeta['price'], 2) }}</h2>
            @endif

            @if(isset($productMeta['description']))
                <p class="mb-4">{{ $productMeta['description'] }}</p>
            @endif

            <!-- Selector de Colores -->
            @if(count($colors) > 0)
                <h5 class="fw-bold mb-3">Color Seleccionado: <span id="colorNameLabel" class="fw-normal text-muted">{{ array_key_first($colors) }}</span></h5>
                <div class="color-selector" id="colorSelector">
                    @foreach($colors as $colorName => $images)
                        <button class="color-btn {{ $loop->first ? 'active' : '' }}" 
                                data-color="{{ $colorName }}" 
                                data-images="{{ json_encode($images) }}"
                                title="{{ $colorName }}">
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Selector de Tallas -->
            <h5 class="fw-bold mt-4 mb-3">Talla</h5>
            <div class="size-selector" id="sizeSelector">
                @foreach($sizes as $size => $stock)
                    <button class="size-btn" data-stock="{{ $stock }}" {{ $stock == 0 ? 'disabled' : '' }}>
                        {{ $size }}
                    </button>
                @endforeach
            </div>

            <!-- Estado de Stock (Oculto temporalmente) -->
            <!--
            <div class="stock-status mt-3" id="stockStatus">
                <span class="text-muted">Selecciona una talla para ver disponibilidad</span>
            </div>
            -->
            
            <!-- Botón carrito eliminado, es solo catálogo -->
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorBtns = document.querySelectorAll('.color-btn');
        const colorNameLabel = document.getElementById('colorNameLabel');
        const carouselInner = document.getElementById('carouselInner');
        
        // Mapeo de colores básicos
        const colorMap = {
            'negro': '#1a1a1a',
            'rojo': '#e63946',
            'blanco': '#f8f9fa',
            'azul': '#1d3557',
            'verde': '#2a9d8f',
            'amarillo': '#e9c46a',
            'gris': '#6c757d',
            'naranja': '#f4a261',
            'morado': '#6a4c93',
            'rosado': '#ffb5a7'
        };
        
        // Manejo de colores
        colorBtns.forEach(btn => {
            const colorName = btn.getAttribute('data-color');
            const colorNameLower = colorName.toLowerCase();
            
            // Intentar detectar el color a partir del nombre
            let foundColor = '#cccccc'; // default
            for (let key in colorMap) {
                if (colorNameLower.includes(key)) {
                    foundColor = colorMap[key];
                    break;
                }
            }
            btn.style.backgroundColor = foundColor;
            
            btn.addEventListener('click', function() {
                // Remover active de todos
                colorBtns.forEach(b => b.classList.remove('active'));
                // Agregar active al clickeado
                this.classList.add('active');
                
                // Actualizar nombre
                const colorName = this.getAttribute('data-color');
                colorNameLabel.textContent = colorName;
                
                // Actualizar carrusel
                const images = JSON.parse(this.getAttribute('data-images'));
                if(images && images.length > 0) {
                    let html = '';
                    images.forEach((img, index) => {
                        html += `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <img src="${img}" class="d-block w-100" alt="Color ${colorName}">
                            </div>
                        `;
                    });
                    carouselInner.innerHTML = html;
                }
            });
        });

        // Manejo de tallas y stock
        const sizeBtns = document.querySelectorAll('.size-btn');
        const stockStatus = document.getElementById('stockStatus');

        sizeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if(this.disabled) return;
                
                // Remover active
                sizeBtns.forEach(b => b.classList.remove('active'));
                // Activar actual
                this.classList.add('active');
                
                // Verificar stock (funcionalidad comentada)
                /*
                const stock = parseInt(this.getAttribute('data-stock'));
                
                if (stock > 0) {
                    if (stock <= 3) {
                        stockStatus.innerHTML = `<span class="stock-in text-warning">¡Últimas ${stock} unidades disponibles!</span>`;
                    } else {
                        stockStatus.innerHTML = `<span class="stock-in">Disponible en stock</span>`;
                    }
                } else {
                    stockStatus.innerHTML = `<span class="stock-out">Agotado</span>`;
                }
                */
            });
        });
    });
</script>
@endsection
