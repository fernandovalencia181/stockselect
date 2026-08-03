<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'session_id',
        'stripe_payment_id',
        'status',
        'tracking_number',
        'total_amount',
        'shipping_cost',
        'shipping_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'wants_newsletter',
        'discount_amount',
        'coupon_code',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
    {
        static::updated(function (Order $order) {
            // Solo actuamos si el estado ha cambiado
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                $inactiveStatuses = ['cancelled', 'returned'];
                $wasInactive = in_array($oldStatus, $inactiveStatuses);
                $isInactive = in_array($newStatus, $inactiveStatuses);

                // CASO A: Pasamos de Activo -> Cancelado/Devuelto (DEVOLVER STOCK)
                if (!$wasInactive && $isInactive) {
                    foreach ($order->items as $item) {
                        if ($item->variant) {
                            $item->variant->increment('stock', $item->quantity);
                        }
                    }
                }
                
                // CASO B: Pasamos de Cancelado/Devuelto -> Activo (VOLVER A QUITAR STOCK)
                if ($wasInactive && !$isInactive) {
                    foreach ($order->items as $item) {
                        if ($item->variant) {
                            $item->variant->decrement('stock', $item->quantity);
                        }
                    }
                }
            }
        });
    }
}
