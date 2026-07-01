<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <script>
        // Evitar flash blanco al cargar: aplica el tema antes de renderizar
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catálogo de Productos')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('catalog.index') }}">
      <img src="{{ asset('logo.jpeg') }}" alt="DAO LEVEL Logo" width="40" height="40" class="d-inline-block align-text-top me-2 rounded-circle">
      DAO LEVEL
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Search Form -->
      <form class="d-flex mx-auto w-50 my-2 my-lg-0 search-form position-relative" id="searchForm" onsubmit="event.preventDefault()">
        <input class="form-control rounded-pill px-4" type="search" placeholder="Buscar productos..." aria-label="Search" id="searchInput" autocomplete="off">
        
        <!-- Dropdown de Resultados -->
        <div class="search-results-dropdown" id="searchResults">
            <!-- Los resultados se inyectan aquí con JS -->
        </div>
      </form>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="{{ route('catalog.index') }}">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('catalog.index') }}">Categorías</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Contacto</a>
        </li>
        <li class="nav-item ms-2">
          <button id="themeToggle" class="btn btn-outline-light rounded-circle" style="width: 40px; height: 40px; padding: 0;">
            <svg id="themeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <!-- Default: Moon (for light mode) -->
              <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .812.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278M4.858 1.311A7.27 7.27 0 0 0 1.018 7.71c0 3.866 3.134 7.001 7.001 7.001a7.2 7.2 0 0 0 4.308-1.42 6.3 6.3 0 0 1-5.748-6.196c0-1.85.834-3.518 2.18-4.606a7.2 7.2 0 0 0-3.901-.178"/>
            </svg>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="container my-5 min-vh-100">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-auto">
    <div class="container">
        <p class="mb-0">&copy; {{ date('Y') }} Moto Catalog. Todos los derechos reservados.</p>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Search JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.classList.remove('active');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="p-3 text-center text-muted">No se encontraron productos.</div>';
                        } else {
                            let html = '';
                            data.forEach(item => {
                                const priceHtml = item.price ? `<small class="text-danger fw-bold d-block">L. ${item.price.toFixed(2)}</small>` : '';
                                const brandHtml = item.brand ? `<small class="d-block">${item.brand}</small>` : '';
                                html += `
                                    <a href="${item.url}" class="search-result-item">
                                        <img src="${item.cover}" alt="${item.name}" class="search-result-img">
                                        <div class="search-result-info">
                                            <h6>${item.name}</h6>
                                            ${brandHtml}
                                            ${priceHtml}
                                        </div>
                                    </a>
                                `;
                            });
                            searchResults.innerHTML = html;
                        }
                        searchResults.classList.add('active');
                    })
                    .catch(err => console.error(err));
            }, 300); // 300ms debounce
        });

        // Ocultar resultados al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('active');
            }
        });

        // Lógica de Dark Mode
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.innerHTML = '<path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>';
            } else {
                themeIcon.innerHTML = '<path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .812.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278M4.858 1.311A7.27 7.27 0 0 0 1.018 7.71c0 3.866 3.134 7.001 7.001 7.001a7.2 7.2 0 0 0 4.308-1.42 6.3 6.3 0 0 1-5.748-6.196c0-1.85.834-3.518 2.18-4.606a7.2 7.2 0 0 0-3.901-.178"/>';
            }
        }
        
        // Cargar estado inicial del icono
        updateIcon(document.documentElement.getAttribute('data-bs-theme'));

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    });
</script>

<!-- Custom JS -->
@yield('scripts')
</body>
</html>
