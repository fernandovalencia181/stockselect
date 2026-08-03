<?php

namespace App\Services;

class CartService
{
    /**
     * Calcula el subtotal del carrito
     */
    public function getSubtotal(): float
    {
        $subtotal = 0;
        $cart = session()->get('cart', []); // Suponiendo que usas la sesión

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }

    /**
     * Calcula el costo de envío según tus reglas de negocio strictas
     */
    public function calculateShippingCost(string $shippingMethod = 'standard_delivery'): float
    {
        // 1. Recogida local siempre es 0€
        if ($shippingMethod === 'local_pickup') {
            return 0.00;
        }

        // 2. Costo envío estándar basado en el subtotal
        $subtotal = $this->getSubtotal();

        // Si el subtotal es igual o mayor a 50€, envío gratis
        if ($subtotal >= 50.00) {
            return 0.00;
        }

        // Si no cumple nada de lo anterior, aplica tarifa estándar de 4€
        return 4.00;
    }

    /**
     * Devuelve el total a pagar
     */
    public function getTotalAmount(string $shippingMethod): float
    {
        return $this->getSubtotal() + $this->calculateShippingCost($shippingMethod);
    }
}
