<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    private $basePath;

    public function __construct()
    {
        // Las carpetas de productos están en resources/imagenes/
        $this->basePath = resource_path('imagenes');
    }

    private function getMetadata($path)
    {
        $meta = ['name' => str_replace('_', ' ', basename($path))];
        $txtFiles = File::glob($path . '/*.txt');
        
        if (count($txtFiles) > 0) {
            $content = File::get($txtFiles[0]);
            $lines = explode("\n", str_replace("\r", "", $content));
            
            for ($i = 0; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                
                // Parse TALLAS
                if (stripos($line, 'TALLAS') !== false && !isset($meta['tallas'])) {
                    for ($j = $i + 1; $j < count($lines); $j++) {
                        if (trim($lines[$j]) !== '') {
                            $meta['tallas'] = array_map('trim', explode(',', $lines[$j]));
                            break;
                        }
                    }
                }
                
                // Parse PRECIO
                if (stripos($line, 'PRECIO') !== false && !isset($meta['price'])) {
                    for ($j = $i + 1; $j < count($lines); $j++) {
                        if (trim($lines[$j]) !== '') {
                            $meta['price'] = (float) str_replace(',', '', trim($lines[$j]));
                            break;
                        }
                    }
                }
                
                // Parse MARCA
                if (stripos($line, 'MARCA') !== false && !isset($meta['brand'])) {
                    for ($j = $i + 1; $j < count($lines); $j++) {
                        if (trim($lines[$j]) !== '') {
                            $meta['brand'] = trim($lines[$j]);
                            break;
                        }
                    }
                }
            }
        }
        
        return $meta;
    }

    private function getImageUrl($file)
    {
        // Normalizar ambas rutas a forward slashes para comparar
        $normalBase = rtrim(str_replace('\\', '/', $this->basePath), '/');
        $normalFile = str_replace('\\', '/', $file->getPathname());
        $relative   = ltrim(str_replace($normalBase, '', $normalFile), '/');
        return route('serve.image', ['path' => $relative]);
    }

    private function getCoverImage($path)
    {
        // Búsqueda directa en la carpeta
        foreach (File::files($path) as $file) {
            if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                return $this->getImageUrl($file);
            }
        }
        
        // Búsqueda recursiva (fallback: usa la primera imagen que encuentre)
        foreach (File::allFiles($path) as $file) {
            if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                return $this->getImageUrl($file);
            }
        }
        
        return asset('placeholder.jpg');
    }

    public function index()
    {
        $directories = File::directories($this->basePath);
        $categories = [];

        foreach ($directories as $dir) {
            $slug = basename($dir);
            // Ignorar la carpeta del proyecto Laravel
            if ($slug === 'catalog' || $slug === '.gemini' || Str::startsWith($slug, '.')) continue;

            $categories[] = [
                'slug' => $slug,
                'name' => $this->getMetadata($dir)['name'] ?? $slug,
                'cover' => $this->getCoverImage($dir),
            ];
        }

        return view('catalog.index', compact('categories'));
    }

    public function category($categorySlug)
    {
        $path = $this->basePath . '/' . $categorySlug;
        if (!File::exists($path)) abort(404);

        $directories = File::directories($path);
        $subcategories = [];

        foreach ($directories as $dir) {
            $subcategories[] = [
                'slug' => basename($dir),
                'name' => $this->getMetadata($dir)['name'] ?? basename($dir),
                'cover' => $this->getCoverImage($dir),
            ];
        }

        $categoryMeta = $this->getMetadata($path);

        return view('catalog.category', compact('subcategories', 'categorySlug', 'categoryMeta'));
    }

    public function subcategory($categorySlug, $subcategorySlug)
    {
        $path = $this->basePath . '/' . $categorySlug . '/' . $subcategorySlug;
        if (!File::exists($path)) abort(404);

        $directories = File::directories($path);
        $products = [];

        foreach ($directories as $dir) {
            $meta = $this->getMetadata($dir);
            $products[] = [
                'slug' => basename($dir),
                'name' => $meta['name'] ?? basename($dir),
                'price' => $meta['price'] ?? null,
                'brand' => $meta['brand'] ?? null,
                'cover' => $this->getCoverImage($dir),
            ];
        }

        $subcategoryMeta = $this->getMetadata($path);

        return view('catalog.subcategory', compact('products', 'categorySlug', 'subcategorySlug', 'subcategoryMeta'));
    }

    public function product($categorySlug, $subcategorySlug, $productSlug)
    {
        $path = $this->basePath . '/' . $categorySlug . '/' . $subcategorySlug . '/' . $productSlug;
        if (!File::exists($path)) abort(404);

        $productMeta = $this->getMetadata($path);
        $productMeta['cover'] = $this->getCoverImage($path);
        
        $colorDirs = File::directories($path);
        $colors = [];
        $firstColorImages = [];

        foreach ($colorDirs as $dir) {
            $colorName = basename($dir);
            $images = [];
            foreach (File::files($dir) as $file) {
                if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $images[] = $this->getImageUrl($file);
                }
            }
            
            if (count($images) > 0) {
                $colors[$colorName] = $images;
                if (empty($firstColorImages)) {
                    $firstColorImages = $images;
                }
            }
        }

        // Si no hay carpetas de colores, usar fotos directamente de la carpeta del producto
        if (count($colorDirs) === 0) {
            $images = [];
            foreach (File::files($path) as $file) {
                if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $images[] = $this->getImageUrl($file);
                }
            }
            if (count($images) > 0) {
                $firstColorImages = $images;
            }
        }

        // Parse tallas from metadata if available
        $sizes = [];
        if (isset($productMeta['tallas'])) {
            foreach ($productMeta['tallas'] as $s) {
                $sizes[$s] = 10; // Mock stock available
            }
        } else {
            $sizes = ['Única' => 10];
        }

        return view('catalog.product', compact('productMeta', 'colors', 'firstColorImages', 'sizes', 'categorySlug', 'subcategorySlug', 'productSlug'));
    }

    public function search(Request $request)
    {
        $query = strtolower($request->get('q', ''));
        if (empty($query)) return response()->json([]);

        $results = [];
        $categories = File::directories($this->basePath);
        
        foreach ($categories as $catDir) {
            $catSlug = basename($catDir);
            if ($catSlug === 'catalog' || $catSlug === '.gemini' || Str::startsWith($catSlug, '.')) continue;

            $subcategories = File::directories($catDir);
            foreach ($subcategories as $subcatDir) {
                $subcatSlug = basename($subcatDir);
                $products = File::directories($subcatDir);
                
                foreach ($products as $prodDir) {
                    $prodSlug = basename($prodDir);
                    $meta = $this->getMetadata($prodDir);
                    $name = $meta['name'] ?? $prodSlug;
                    
                    if (str_contains(strtolower($name), $query)) {
                        $results[] = [
                            'name' => $name,
                            'price' => $meta['price'] ?? null,
                            'brand' => $meta['brand'] ?? null,
                            'cover' => $this->getCoverImage($prodDir),
                            'url' => route('catalog.product', ['category' => $catSlug, 'subcategory' => $subcatSlug, 'product' => $prodSlug])
                        ];
                    }
                }
            }
        }
        
        return response()->json($results);
    }
}
