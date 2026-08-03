<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Filtros y búsqueda para el catálogo principal
        $query = Product::where('is_active', true)
            ->where('brand', 'Adidas')
            ->with(['category', 'variants']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'Todo') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('genero') && $request->genero !== 'Todo') {
            $query->whereIn('gender', [$request->genero, \App\Models\Product::GENDER_UNISEX]);
        }

        // Orden por los más recientes primero (Lo mejor para ventas y clientes recurrentes)
        // Así los usuarios que vuelven ven las novedades arriba.
        $products = $query->latest()->paginate(12)->withQueryString();

        // Respuesta AJAX (botón "Cargar más")
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('frontend.partials.product-cards', compact('products'))->render();

            return response()->json([
                'html'     => $html,
                'hasMore'  => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
                'total'    => $products->total(),
                'showing'  => $products->currentPage() * $products->perPage(),
            ]);
        }

        // Carga normal (SSR)
        $settings = SiteSetting::pluck('value', 'key');

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('brand', 'Adidas')
            ->latest()
            ->limit(8)
            ->get();

        $categories = \App\Models\Category::all();

        return view('frontend.home', compact('products', 'categories', 'featuredProducts', 'settings'));
    }
}
