<?php

namespace App\Domain\Invoices\Strategies;

class ProductCalculationStrategy implements CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $price = (float) $item['price'];

            $base = $quantity * $price;
            $tax = $base * ($tax_total / 100);

            $subtotal += $base;
        }

        $total = ($subtotal + $tax_total) - $discount;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discount, 2),
            'tax_total' => round($tax_total, 2),
            'total' => round($total, 2),
        ];
    }
}