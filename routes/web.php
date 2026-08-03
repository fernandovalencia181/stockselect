<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CartController;

// PÃ¡gina Principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Detalle Producto
Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('product.show');

// Carrito (sesión)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{cartKey}', [CartController::class, 'update'])->name('cart.update')->where('cartKey', '.+');
Route::delete('/cart/remove/{cartKey}', [CartController::class, 'remove'])->name('cart.remove')->where('cartKey', '.+');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Público
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/address-search', [CheckoutController::class, 'proxyAddressSearch'])->name('address.search');
Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validate-coupon');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

use App\Http\Controllers\StripeWebhookController;
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

use App\Http\Controllers\Frontend\StaticPageController;

// Páginas Estáticas y Legal
Route::get('/politica-privacidad', [StaticPageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terminos-condiciones', [StaticPageController::class, 'terms'])->name('pages.terms');
Route::get('/politica-cookies', [StaticPageController::class, 'cookies'])->name('pages.cookies');
Route::get('/aviso-legal', [StaticPageController::class, 'legal'])->name('pages.legal');
Route::get('/preguntas-frecuentes', [StaticPageController::class, 'faq'])->name('pages.faq');
Route::get('/guia-tallas', [StaticPageController::class, 'sizeGuide'])->name('pages.size-guide');

// Seguimiento de Pedido
Route::match(['get', 'post'], '/seguimiento-pedido', [StaticPageController::class, 'tracking'])->name('pages.tracking');
// Newsletter Suscripcion (footer)
Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email|max:255']);
    \App\Models\Subscriber::firstOrCreate(
        ['email' => strtolower(trim($request->email))],
        ['source' => $request->input('source', 'footer'), 'subscribed_at' => now()]
    );
    if ($request->expectsJson()) {
        return response()->json(['success' => true]);
    }
    return back()->with('success', 'Suscrito!');
})->name('newsletter.subscribe');
