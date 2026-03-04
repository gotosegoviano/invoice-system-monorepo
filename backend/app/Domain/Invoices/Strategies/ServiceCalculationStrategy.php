<?php

namespace App\Domain\Invoices\Strategies;

class ServiceCalculationStrategy implements CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $price = (float) $item['price'];

            $base = $quantity * $price;
            $discounted = $base - $discount;
            $tax = $discounted * ($tax_total / 100);

            $subtotal += $base;
        }

        $total = ($subtotal - $discount) + $tax_total;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discount, 2),
            'tax_total' => round($tax_total, 2),
            'total' => round($total, 2),
        ];
    }
}