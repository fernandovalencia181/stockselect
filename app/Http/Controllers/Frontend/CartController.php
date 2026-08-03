<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** Ver el carrito */
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('frontend.cart', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $product = Product::with('variants')->findOrFail($request->product_id);
        $variant = $product->variants->where('id', $request->variant_id)->first();

        if (!$variant) {
            return back()->with('error', 'Talla no válida seleccionada.');
        }

        $cart = session()->get('cart', []);
        // Clave única basada en producto y talla para permitir varias tallas del mismo producto
        $key = $product->id . '-' . $variant->size;

        $currentQty = isset($cart[$key]) ? $cart[$key]['quantity'] : 0;

        if ($currentQty + 1 > $variant->stock) {
            return back()->with('error', "Solo quedan {$variant->stock} unidades de la talla {$variant->size}.");
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'size' => $variant->size,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')
            ->with('success', "«{$product->name}» añadido al carrito.");
    }

    /** Actualizar cantidad de un ítem */
    public function update(Request $request, $cartKey)
    {
        $cart = session()->get('cart', []);
        $qty = (int) $request->quantity;

        if (isset($cart[$cartKey])) {
            $item = $cart[$cartKey];

            // Validar Stock
            if ($qty > $item['quantity']) {
                $variant = \App\Models\ProductVariant::find($item['variant_id']);
                if ($variant && $qty > $variant->stock) {
                    return back()->with('error', "Solo quedan {$variant->stock} unidades de este artículo.");
                }
            }

            if ($qty < 1) {
                unset($cart[$cartKey]);
            } else {
                $cart[$cartKey]['quantity'] = $qty;
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    /** Eliminar un ítem */
    public function remove($cartKey)
    {
        $cart = session()->get('cart', []);
        unset($cart[$cartKey]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')
            ->with('success', 'Producto eliminado del carrito.');
    }

    /** Vaciar el carrito entero */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index');
    }
}
