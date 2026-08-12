<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;
use App\Mail\NewsletterWelcome;
use App\Models\Subscriber;
use App\Models\Coupon;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shippingCost = (float) \App\Models\SiteSetting::getValue('shipping_cost', 4.00);
        $threshold = (float) \App\Models\SiteSetting::getValue('shipping_free_threshold', 50.00);

        $checkoutMode = \App\Models\SiteSetting::getValue('checkout_mode', 'simple');
        $view = $checkoutMode === 'multistep' ? 'frontend.checkout_multistep' : 'frontend.checkout';

        return view($view, compact('cart', 'subtotal', 'shippingCost', 'threshold'));
    }

    /**
     * Proxy para Photon API (Evitar CORS y mejorar robustez)
     */
    public function proxyAddressSearch(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 3) {
            return response()->json(['features' => []]);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(6)->get('https://photon.komoot.io/api/', [
                'q'     => $query,
                'limit' => 10,       // Pedimos más para poder filtrar
                'lang'  => 'en',     // Photon solo soporta: de, en, fr, it
                // Bounding box de España: min_lon, min_lat, max_lon, max_lat
                'bbox'  => '-9.3,35.9,4.3,43.8',
            ]);

            if (!$response->successful()) {
                return response()->json(['features' => []]);
            }

            $data = $response->json();

            $features = collect($data['features'] ?? [])
                ->filter(function ($feature) {
                    $props = $feature['properties'] ?? [];

                    // Solo resultados de España
                    if (strtoupper($props['countrycode'] ?? '') !== 'ES') return false;

                    $key   = $props['osm_key']   ?? '';
                    $value = $props['osm_value'] ?? '';
                    $type  = $props['type']       ?? '';

                    // Aceptar calles (highway) de cualquier tipo
                    if ($key === 'highway') return true;

                    // Aceptar direcciones concretas (tienen número de portal)
                    if (!empty($props['housenumber'])) return true;

                    // Aceptar plazas, barrios, municipios
                    if ($key === 'place' && in_array($value, [
                        'village', 'town', 'city', 'neighbourhood',
                        'suburb', 'quarter', 'square', 'pedestrian',
                    ])) return true;

                    // Aceptar si tiene type=street (algunos resultados de Photon lo indican explícitamente)
                    if ($type === 'street') return true;

                    return false;
                })
                ->take(5)
                ->values()
                ->all();

            return response()->json(['features' => $features]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Photon Proxy Error: ' . $e->getMessage());
            return response()->json(['features' => []], 200);
        }
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric',
            'email' => 'nullable|email'
        ]);

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'El cupón no existe.']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Este cupón ya no es válido o está agotado.']);
        }

        // Validación de Suscripción si es requerida
        if ($coupon->requires_subscription) {
            // Si requiere suscripción pero no hay email, rechazar directamente
            if (!$request->filled('email')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Introduce tu email antes de aplicar este cupón exclusivo para suscriptores.'
                ]);
            }
            $isSubscribed = \App\Models\Subscriber::where('email', $request->email)->exists();
            if (!$isSubscribed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cupón es exclusivo para suscriptores. ¡Suscríbete primero!'
                ]);
            }
        }

        // Validación de Uso Único por Email
        if ($request->filled('email')) {
            $alreadyUsed = \App\Models\Order::where('customer_email', $request->email)
                ->where('coupon_code', $coupon->code)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($alreadyUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya has utilizado este código en un pedido anterior.'
                ]);
            }
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        return response()->json([
            'success'  => true,
            'code'     => $coupon->code,
            'discount' => round($discount, 2)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string|max:50',
            'customer_email'  => 'nullable|email|max:255',
            'shipping_method' => 'nullable|string',
            'payment_method'  => 'nullable|string',
        ], [
            'customer_name.required'  => 'Por favor, introduce tu nombre.',
            'customer_phone.required' => 'Por favor, introduce tu teléfono de contacto.',
            'customer_email.email'    => 'El correo electrónico no es válido.',
        ]);

        $shippingMethod = $request->input('shipping_method', 'local_pickup');
        $paymentMethod  = $request->input('payment_method', 'whatsapp');
        
        $customerEmail = $request->filled('customer_email')
            ? trim($request->customer_email)
            : (preg_replace('/[^0-9]/', '', $request->customer_phone) . '@whatsapp.stockselect.es');

        // Construir la dirección según el método de envío
        if ($shippingMethod === 'standard_delivery') {
            $parts = array_filter([
                trim(($request->shipping_street ?? '') . ' ' . ($request->shipping_number ?? '')),
                $request->shipping_floor ?? '',
                $request->shipping_city ?? '',
                $request->shipping_zip ?? '',
                $request->shipping_province ?? '',
            ]);
            $fullAddress = implode(', ', array_filter($parts)) ?: 'Sin dirección indicada';
        } else {
            $fullAddress = 'Entrega en Mano (Mollerussa)';
        }

        // Sobrescribimos el request para que fluya correctamente a los modelos
        $request->merge([
            'shipping_address' => $fullAddress,
            'shipping_method'  => $shippingMethod,
            'payment_method'   => $paymentMethod,
            'customer_email'   => $customerEmail,
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // ── CÁLCULO DE DESCUENTO ──────────────────────────────────────────
        $discountAmount = 0;
        $coupon         = null;
        if ($request->filled('coupon_code')) {
            // lockForUpdate evita race condition: si dos clientes usan el mismo cupón
            // limitado simultáneamente, solo uno pasa. El otro verá el límite agotado.
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $subtotal, &$discountAmount, &$coupon) {
                $coupon = Coupon::lockForUpdate()
                    ->where('code', strtoupper(trim($request->coupon_code)))
                    ->first();

                if ($coupon && $coupon->isValid()) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                }
            });
        }

        // Reglas de envío dinámicas
        $shippingCost = (float) \App\Models\SiteSetting::getValue('shipping_cost', 4.00);
        $freeShippingThreshold = (float) \App\Models\SiteSetting::getValue('shipping_free_threshold', 50.00);

        $shipping = ($request->shipping_method === 'local_pickup' || $subtotal >= $freeShippingThreshold) ? 0.0 : $shippingCost;

        // El total es el subtotal (menos descuento) + envío. Nunca inferior a 0 + envío.
        $totalWithoutShipping = max(0, $subtotal - $discountAmount);
        $total = $totalWithoutShipping + $shipping;

        // ── STRIPE ─────────────────────────────────────────────────────────
        if ($request->payment_method === 'stripe') {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $lineItems = [];
            foreach ($cart as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => (int) round($item['price'] * 100),
                        'product_data' => [
                            'name' => $item['name'] . ' (Talla ' . ($item['size'] ?? '-') . ')',
                        ],
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            // Envío como line_item extra (si aplica)
            if ($shipping > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => (int) ($shipping * 100),
                        'product_data' => ['name' => 'Gastos de envío'],
                    ],
                    'quantity' => 1,
                ];
            }

            // Guardamos datos del cliente en sesión para recuperarlos al volver
            session()->put('pending_checkout', [
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_method' => $request->shipping_method,
                'total' => $total,
                'shipping' => $shipping,
            ]);

            // ── VERIFICACIÓN DE STOCK (PRE-VIAJE A STRIPE) ──────────────────
            foreach ($cart as $item) {
                if (isset($item['variant_id'])) {
                    $variant = \App\Models\ProductVariant::find($item['variant_id']);
                    if (!$variant || $variant->stock < $item['quantity']) {
                        return back()->withErrors(['cart' => "Lo sentimos, el producto '{$item['name']}' ya no tiene stock disponible."]);
                    }
                }
            }

            $stripeDiscounts = [];

            if ($request->filled('coupon_code')) {
                // Re-validación de seguridad en el servidor para Stripe
                $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();

                if ($coupon && $coupon->isValid()) {
                    // Check suscripción
                    if ($coupon->requires_subscription) {
                        if (!\App\Models\Subscriber::where('email', $request->customer_email)->exists()) {
                            return back()->withErrors(['cart' => 'El cupón requiere suscripción previa.']);
                        }
                    }

                    // Check re-uso
                    $alreadyUsed = \App\Models\Order::where('customer_email', $request->customer_email)
                        ->where('coupon_code', $coupon->code)
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if ($alreadyUsed) {
                        return back()->withErrors(['cart' => 'Ya has utilizado este cupón anteriormente.']);
                    }

                    // Creamos un cupón temporal en Stripe solo si el descuento es real
                    if ($discountAmount > 0) {
                        try {
                            $stripeCoupon = \Stripe\Coupon::create([
                                'amount_off' => (int) ($discountAmount * 100),
                                'currency' => 'eur',
                                'duration' => 'once',
                            ]);
                            $stripeDiscounts[] = ['coupon' => $stripeCoupon->id];
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error creando cupón en Stripe: " . $e->getMessage());
                        }
                    }
                }
            }

            try {
                $session = \Stripe\Checkout\Session::create([
                    // Sin 'payment_method_types' => Stripe usa automáticamente los métodos
                    // habilitados en el dashboard (tarjetas, Bizum, Klarna, etc.)
                    'line_items'        => $lineItems,
                    'mode'              => 'payment',
                    'discounts'         => $stripeDiscounts,
                    'customer_email'    => $request->customer_email,
                    'locale'            => 'es',
                    'success_url'       => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url'        => route('checkout.cancel'),
                    'metadata'          => [
                        'customer_name'   => $request->customer_name,
                        'customer_phone'  => $request->customer_phone,
                        'shipping_method' => $request->shipping_method,
                    ],
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                \Illuminate\Support\Facades\Log::error("Stripe Error: " . $e->getMessage());
                return back()->withErrors(['cart' => 'Error al conectar con la pasarela de pago: ' . $e->getUserMessage()])->withInput();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Stripe General Error: " . $e->getMessage());
                return back()->withErrors(['cart' => 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo o elige pago por WhatsApp.'])->withInput();
            }

            // Creamos el pedido en 'pending' para tener registro aunque el usuario no vuelva
            \Illuminate\Support\Facades\DB::transaction(function () use ($total, $shipping, $request, $cart, $session, $discountAmount) {
                $order = \App\Models\Order::create([
                    'session_id'       => $session->id,
                    'status'           => 'pending',
                    'total_amount'     => $total,
                    'shipping_cost'    => $shipping,
                    'shipping_method'  => $request->shipping_method,
                    'customer_name'    => $request->customer_name,
                    'customer_email'   => $request->customer_email,
                    'customer_phone'   => $request->customer_phone,
                    'wants_newsletter' => $request->has('subscribe_newsletter'),
                    'shipping_address' => $request->shipping_address ?? 'Recogida Local',
                    'discount_amount'  => $discountAmount,
                    'coupon_code'      => strtoupper(trim($request->coupon_code ?? '')),
                ]);

                foreach ($cart as $item) {
                    $product   = \App\Models\Product::find($item['id']);
                    $costPrice = $product ? (float) ($product->cost_price ?? 0) : 0;

                    \App\Models\OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $item['id'],
                        'variant_id'         => $item['variant_id'] ?? null,
                        'quantity'           => $item['quantity'],
                        'price_at_time'      => $item['price'],
                        'cost_price_at_time' => $costPrice,
                        'size'               => $item['size'] ?? null,
                    ]);
                }
            });

            return redirect()->away($session->url);
        }

        // ── PAGO EN EFECTIVO VÍA WHATSAPP (Flujo de entrega en mano) ────────
        $itemsText = '';
        $order = null;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $total, $shipping, $request, $cart, $discountAmount, &$order, &$itemsText
            ) {
                $order = \App\Models\Order::create([
                    'status'           => 'pending_cash',
                    'total_amount'     => $total,
                    'shipping_cost'    => $shipping,
                    'shipping_method'  => $request->shipping_method,
                    'customer_name'    => $request->customer_name,
                    'customer_email'   => $request->customer_email,
                    'customer_phone'   => $request->customer_phone,
                    'wants_newsletter' => $request->has('subscribe_newsletter'),
                    'shipping_address' => $request->shipping_address ?? 'Recogida Local',
                    'discount_amount'  => $discountAmount,
                    'coupon_code'      => strtoupper(trim($request->coupon_code ?? '')),
                ]);

                foreach ($cart as $item) {
                    // Bloqueo pesimista con lockForUpdate() para evitar race conditions
                    if (isset($item['variant_id'])) {
                        $variant = \App\Models\ProductVariant::lockForUpdate()->find($item['variant_id']);
                        if (!$variant || $variant->stock < $item['quantity']) {
                            throw new \Exception("Lo sentimos, el producto '{$item['name']}' (Talla " . ($item['size'] ?? '-') . ") ya no dispone de stock suficiente.");
                        }
                        $variant->decrement('stock', $item['quantity']);
                    }

                    $product   = \App\Models\Product::find($item['id']);
                    $costPrice = $product ? (float) ($product->cost_price ?? 0) : 0;

                    \App\Models\OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $item['id'],
                        'variant_id'         => $item['variant_id'] ?? null,
                        'quantity'           => $item['quantity'],
                        'price_at_time'      => $item['price'],
                        'cost_price_at_time' => $costPrice,
                        'size'               => $item['size'] ?? null,
                    ]);

                    $itemsText .= "- {$item['quantity']}x {$item['name']} (Talla " . ($item['size'] ?? '-') . ") — " . number_format($item['price'], 2) . "€\n";
                }
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error procesando pedido en efectivo / WhatsApp: " . $e->getMessage());
            return back()->withErrors(['cart' => $e->getMessage()])->withInput();
        }

        // Registrar uso del cupón
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();
            if ($coupon) {
                $coupon->increment('used_count');
            }
        }

        // Newsletter
        if ($request->has('subscribe_newsletter')) {
            $subscriber = Subscriber::firstOrCreate(
                ['email' => $request->customer_email],
                ['name' => $request->customer_name, 'source' => 'checkout']
            );

            if ($subscriber->wasRecentlyCreated) {
                // DESACTIVADO TEMPORALMENTE (a petición del admin, para no dar cupón enseguida)
                /*
                try {
                    \Illuminate\Support\Facades\Mail::to($subscriber->email)->queue(new NewsletterWelcome($subscriber));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error enviando bienvenida de newsletter (WhatsApp): " . $e->getMessage());
                }
                */
            }
        }

        session()->forget('cart');

        // Email de confirmación de pedido si el cliente indicó un email real
        if (!str_ends_with($order->customer_email, '@whatsapp.stockselect.es')) {
            try {
                Mail::to($order->customer_email)->queue(new OrderConfirmed($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error enviando confirmación de pedido WhatsApp #{$order->id}: " . $e->getMessage());
            }
        }

        $rawWaNumber = \App\Models\SiteSetting::getValue('whatsapp_number', '34643717157');
        $waNumber    = preg_replace('/[^0-9]/', '', (string) $rawWaNumber);
        if (empty($waNumber)) {
            $waNumber = '34643717157';
        }
        $shopName  = \App\Models\SiteSetting::getValue('legal_shop_name', 'Stock Select');

        $msg  = "¡Hola! Quiero confirmar mi pedido #{$order->id} en {$shopName}.\n\n";
        $msg .= "*Cliente:* {$order->customer_name}\n";
        $msg .= "*Teléfono:* {$order->customer_phone}\n\n";
        $msg .= "*Artículos:*\n{$itemsText}\n";

        if ($order->discount_amount > 0) {
            $msg .= "*Descuento cupón ({$order->coupon_code}):* -" . number_format($order->discount_amount, 2) . "€\n";
        }
        $msg .= "*Total:* " . number_format($total, 2) . " €\n\n";

        if ($order->shipping_method === 'local_pickup') {
            $msg .= "📍 Entrega en mano (envío gratis). Pago en efectivo al recibir.\n\n¿Cuándo y dónde podríamos coordinar la entrega?";
        } else {
            $msg .= "📍 Entrega: {$order->shipping_address}\n💵 Pago en efectivo al recibir / entrega en mano.\n\n¿Cuándo podríamos coordinar la entrega?";
        }

        return redirect()->away("https://wa.me/{$waNumber}?text=" . urlencode($msg));

    }

    /** Stripe callback — pago completado */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('home');
        }

        try {
            // Buscamos el pedido que ya debería existir
            $order = \App\Models\Order::where('session_id', $sessionId)->first();

            if (!$order) {
                \Illuminate\Support\Facades\Log::warning("Éxito visual pero pedido no encontrado para sesión: " . $sessionId);
                return redirect()->route('home')->with('error', 'Pedido no encontrado.');
            }

            // Limpieza de sesión si existe el carrito
            if (session()->has('cart')) {
                session()->forget(['cart', 'pending_checkout']);
            }

            // Fallback de Suscripción a Newsletter (en caso de que el webhook tarde o el usuario vuelva antes)
            if ($order->wants_newsletter) {
                $subscriber = Subscriber::firstOrCreate(
                    ['email' => $order->customer_email],
                    ['name' => $order->customer_name, 'source' => 'checkout']
                );

                if ($subscriber->wasRecentlyCreated) {
                    // DESACTIVADO TEMPORALMENTE (a petición del admin, para no dar cupón enseguida)
                    /*
                    try {
                        \Illuminate\Support\Facades\Mail::to($subscriber->email)->queue(new NewsletterWelcome($subscriber));
                        \Illuminate\Support\Facades\Log::info("Newsletter Fallback: Cliente " . $subscriber->email . " suscrito desde success.");
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Error enviando bienvenida de newsletter (Stripe Fallback): " . $e->getMessage());
                    }
                    */
                }
            }

            return view('frontend.checkout_success', compact('order'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error en Checkout Success View: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Hubo un problema al mostrar la confirmación.');
        }
    }

    /** Stripe callback — usuario canceló */
    public function cancel()
    {
        return redirect()->route('checkout.index')
            ->with('error', 'El pago fue cancelado. Puedes intentarlo de nuevo o elegir otro método.');
    }
}