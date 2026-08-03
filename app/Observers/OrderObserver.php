<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if (!$order->isDirty('status')) {
            return;
        }

        $newStatus = $order->status;

        try {
            match ($newStatus) {
                'processing' => \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->queue(new \App\Mail\OrderProcessing($order)),

                'shipped' => \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->queue(new \App\Mail\OrderShipped($order)),

                'delivered' => \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->queue(new \App\Mail\OrderDelivered($order)),

                default => null,
            };

            if (in_array($newStatus, ['processing', 'shipped', 'delivered'])) {
                \Illuminate\Support\Facades\Log::info("Email de estado '{$newStatus}' enviado para pedido #{$order->id} a {$order->customer_email}");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error enviando email de estado '{$newStatus}' para pedido #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
