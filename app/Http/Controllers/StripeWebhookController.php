<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\ProductVariant;
use App\Mail\OrderConfirmed;
use App\Mail\NewsletterWelcome;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error("Webhook error: Invalid payload");
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error("Webhook error: Invalid signature");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info("Stripe Webhook recibido: " . $event->type);

        // Manejar el evento checkout.session.completed
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $this->handleCheckoutSessionCompleted($session);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $sessionId = $session->id;
        $order = Order::where('session_id', $sessionId)->first();

        if (!$order) {
            Log::warning("Webhook: Pedido no encontrado para la sesión " . $sessionId);
            return;
        }

        if ($order->status === 'paid') {
            Log::info("Webhook: El pedido " . $order->id . " ya estaba marcado como pagado.");
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order, $session) {
                // Actualizar estado del pedido
                $order->update([
                    'status' => 'paid',
                    'stripe_payment_id' => $session->payment_intent,
                ]);

                // Reducción de Stock
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $variant = ProductVariant::find($item->variant_id);
                        if ($variant) {
                            $variant->decrement('stock', $item->quantity);
                            Log::info("Webhook: Stock reducido para variante " . $variant->id . " del pedido " . $order->id);
                        }
                    }
                }

                // Incrementar uso del cupón si se usó uno (Bug fix: faltaba en flujo Stripe)
                if ($order->coupon_code) {
                    Coupon::where('code', $order->coupon_code)->increment('used_count');
                    Log::info("Webhook: Cupón {$order->coupon_code} incrementado (pedido #{$order->id}).");
                }

                Log::info("Webhook: Pedido " . $order->id . " marcado como pagado con éxito.");

                // Suscripción a Newsletter si se solicitó
                if ($order->wants_newsletter) {
                    $subscriber = Subscriber::firstOrCreate(
                        ['email' => $order->customer_email],
                        ['name' => $order->customer_name, 'source' => 'checkout']
                    );

                    if ($subscriber->wasRecentlyCreated) {
                        // DESACTIVADO TEMPORALMENTE (a petición del admin, para no dar cupón enseguida)
                        /*
                        try {
                            Mail::to($subscriber->email)->queue(new NewsletterWelcome($subscriber));
                            \Illuminate\Support\Facades\Log::info("Newsletter Webhook: Cliente " . $subscriber->email . " suscrito.");
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error enviando bienvenida de newsletter (Webhook): " . $e->getMessage());
                        }
                        */
                    } else {
                        Log::info("Webhook: El cliente " . $subscriber->email . " ya estaba suscrito basándose en validación duplicate-prevention.");
                    }
                }
            });

            // Envío de email de confirmación
            try {
                Mail::to($order->customer_email)->queue(new OrderConfirmed($order));
                Log::info("Webhook: Email de confirmación encolado para " . $order->customer_email);
            } catch (\Exception $e) {
                Log::error("Webhook: Error enviando email para pedido " . $order->id . ": " . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error("Webhook: Error procesando el pago del pedido " . $order->id . ": " . $e->getMessage());
        }
    }
}
