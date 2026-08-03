<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                          ->where('is_active', true)
                          ->with('variants')
                          ->firstOrFail();

        // Buscamos otros colores vinculados por el mismo modelo/grupo
        $colorProducts = collect();
        if ($product->model_group) {
            $colorProducts = Product::where('model_group', $product->model_group)
                                    ->where('is_active', true)
                                    ->where('id', '!=', $product->id)
                                    ->get();
        }

        // Venta Cruzada: Priorizar categorías diferentes para "Completa tu look"
        $relatedProducts = Product::where('id', '!=', $product->id)
                                  ->where('is_active', true)
                                  ->where('category_id', '!=', $product->category_id)
                                  ->inRandomOrder()
                                  ->take(4)
                                  ->get();

        // Si no hay suficientes de otras categorías, rellenamos con aleatorios totales
        if ($relatedProducts->count() < 4) {
             $moreProducts = Product::where('id', '!=', $product->id)
                                  ->where('is_active', true)
                                  ->whereNotIn('id', $relatedProducts->pluck('id'))
                                  ->inRandomOrder()
                                  ->take(4 - $relatedProducts->count())
                                  ->get();
             $relatedProducts = $relatedProducts->merge($moreProducts);
        }

        return view('frontend.product_show', compact('product', 'colorProducts', 'relatedProducts'));
    }
}
