# DAO LEVEL — Catálogo Web de Productos

<p align="center">
  <img src="public/logo.jpeg" alt="DAO LEVEL Logo" width="120"/>
</p>

<p align="center">
  <strong>Catálogo web moderno, dinámico y responsive para la visualización de productos de DAO LEVEL.</strong><br/>
  Desarrollado con Laravel 12 + Bootstrap 5. Incluye modo oscuro, búsqueda en tiempo real y carrusel dinámico por colores.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?logo=laravel" alt="Laravel"/>
  <img src="https://img.shields.io/badge/Bootstrap-5.3-purple?logo=bootstrap" alt="Bootstrap"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?logo=php" alt="PHP"/>
  <img src="https://img.shields.io/badge/License-Privado-gray" alt="License"/>
</p>

---

## 📋 Descripción

Sistema de catálogo web dinámico para **DAO LEVEL** que lee directamente desde una estructura de carpetas físicas dentro del proyecto para mostrar una jerarquía de **Categorías → Subcategorías → Productos → Variantes por Color**.

Es un catálogo **100% visual** (no tienda online). El cliente puede explorar todos los productos, ver el carrusel de fotos por color, ver precios en Lempiras (L.) y tallas disponibles.

---

## ✨ Características

| Característica | Detalle |
|---|---|
| 🗂️ Catálogo jerárquico | Categorías → Subcategorías → Productos |
| 🎨 Selector de colores | Cambia el carrusel automáticamente (sin recargar) |
| 🖼️ Carrusel dinámico | Bootstrap 5, funciona con o sin variantes de color |
| 🔍 Búsqueda en tiempo real | Dropdown inteligente con foto, precio y marca |
| 🌙 Modo oscuro / claro | Con memoria del usuario (localStorage) |
| 📐 Totalmente responsive | Desktop (4 col), Tablet (2 col), Móvil (1 col) |
| 📁 Sin base de datos | Solo carpetas e imágenes, sin configuración extra |
| 📝 Datos en texto plano | Precio, marca y tallas en archivo `DATOS.txt` |
| 👟 Tallas dinámicas | Leídas automáticamente del archivo de texto |

---

## 🗂️ Estructura de Carpetas del Catálogo

Las imágenes y datos de los productos se ubican en `resources/imagenes/`:

```
catalog/
└── resources/
    └── imagenes/                          ← Raíz del catálogo
        │
        ├── CASCOS/                        ← Categoría principal
        │   ├── PORTADA Y PRINCIPAL.jpeg   ← Portada de la categoría
        │   ├── CASCOS MODULARES/          ← Subcategoría
        │   │   └── MI CASCO MODULAR/      ← Producto
        │   │       ├── DATOS.txt          ← Metadatos
        │   │       ├── PORTADA.jpeg       ← Portada del producto
        │   │       ├── NEGRO/             ← Variante por color
        │   │       │   ├── foto1.jpeg
        │   │       │   └── foto2.jpeg
        │   │       └── ROJO/
        │   │           └── foto1.jpeg
        │   └── CASCOS INTEGRALES/
        │       └── ...
        │
        ├── ACCESORIOS/
        │   └── GUANTES/
        │       ├── CON DEDOS/
        │       └── SIN DEDOS/
        │
        └── DEPORTE/
            └── BALONES DE FUTBALL/
```

---

## 📄 Formato del archivo `DATOS.txt`

Cada carpeta de producto puede contener un `DATOS.txt` con este formato:

```
TALLAS
S,M,L,XL

PRECIO
1100

MARCA
ICH
```

> ✅ Todos los campos son **opcionales**.
> El parser lee línea por línea e ignora líneas vacías, por lo que el formato es flexible.

---

## 🚀 Instalación Local

### Requisitos
- PHP >= 8.2
- Composer

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/DaoLevelHN/Catalogo.git
cd Catalogo

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Levantar servidor de desarrollo
php artisan serve
```

Abre [http://localhost:8000](http://localhost:8000) en tu navegador.

> ⚠️ Recuerda tener tus carpetas de productos dentro de `resources/imagenes/`.

---

## 🛣️ Rutas del Sistema

| Ruta | Descripción |
|------|-------------|
| `/` | Página principal — categorías principales |
| `/categoria/{nombre}` | Lista de subcategorías |
| `/categoria/{categoria}/{subcategoria}` | Lista de productos |
| `/producto/{cat}/{subcat}/{producto}` | Detalle del producto |
| `/api/search?q={texto}` | API búsqueda en tiempo real (JSON) |
| `/serve-image/{ruta}` | Sirve imágenes desde `resources/imagenes` |

---

## 📁 Arquitectura del Código

```
catalog/
├── app/
│   └── Http/
│       └── Controllers/
│           └── CatalogController.php    ← Toda la lógica de negocio
│
├── resources/
│   ├── imagenes/                        ← Tus productos (CASCOS, ACCESORIOS, DEPORTE)
│   └── views/
│       ├── layout.blade.php             ← Plantilla base (Navbar + Dark Mode + Search)
│       └── catalog/
│           ├── index.blade.php          ← Vista: Categorías principales
│           ├── category.blade.php       ← Vista: Subcategorías
│           ├── subcategory.blade.php    ← Vista: Productos
│           └── product.blade.php        ← Vista: Detalle del producto
│
├── routes/
│   └── web.php                          ← Definición de todas las rutas
│
└── public/
    ├── css/style.css                    ← Estilos personalizados + Dark Mode CSS
    └── logo.jpeg                        ← Logo del negocio
```

---

## 🛠️ Tecnologías

| Tecnología | Versión | Uso |
|---|---|---|
| **Laravel** | 12.x | Framework PHP, rutas, controladores, Blade |
| **Bootstrap** | 5.3 | Grid, Cards, Carousel, Navbar, Botones |
| **JavaScript** (Vanilla) | ES6+ | Colores dinámicos, búsqueda, dark mode |
| **CSS personalizado** | — | Animaciones, hover effects, modo oscuro |
| **PHP** | 8.2+ | Backend, lectura de sistema de archivos |

---

## 🔄 ¿Cómo agregar un nuevo producto?

1. Crea una carpeta con el nombre del producto dentro de su subcategoría en `resources/imagenes/`
2. Agrega un archivo `DATOS.txt` con el precio, tallas y marca
3. Agrega la imagen de portada (`.jpg`, `.jpeg`, `.png` o `.webp`) directamente en la carpeta
4. **Si tiene variantes por color**: Crea subcarpetas con el nombre del color (ej: `NEGRO`, `ROJO`) y agrega las fotos adentro
5. **Si no tiene colores**: Pon todas las fotos directamente en la carpeta del producto
6. ¡Recarga la página y aparecerá automáticamente!

---

## 🌙 Modo Oscuro

El sitio incluye un botón de toggle (🌙/☀️) en la barra de navegación. La preferencia se guarda en `localStorage` del navegador, por lo que persiste entre sesiones y páginas.

---

## 🔍 Búsqueda en Tiempo Real

La barra de búsqueda en la Navbar consulta la ruta `/api/search?q=` mediante `fetch()` con 300ms de debounce. Muestra resultados con foto, marca y precio en un dropdown flotante. Al hacer clic navega directamente al producto.

---

## 📅 Historial de versiones

| Versión | Fecha | Cambios |
|---|---|---|
| v1.0.0 | Julio 2026 | Lanzamiento inicial del catálogo |

---

## 👤 Créditos

Proyecto desarrollado para **DAO LEVEL HN**.

---

## 📃 Licencia

Uso privado y exclusivo de **DAO LEVEL**. Todos los derechos reservados © 2026.
