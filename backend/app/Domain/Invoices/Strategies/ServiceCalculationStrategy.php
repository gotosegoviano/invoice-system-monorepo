<?php

namespace App\Domain\Invoices\Strategies;

class ServiceCalculationStrategy implements CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0, bool $tax_is_percentage = true, bool $discount_is_percentage = true): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $price = (float) $item['price'];

            $subtotal += $quantity * $price;
        }

        // discount total
        $discount_amount = $discount_is_percentage ? $subtotal * ($discount / 100) : $discount;

        // tax total
        $tax_amount = $tax_is_percentage ? ($subtotal - $discount_amount) * ($tax_total / 100) : $tax_total;

        $total = ($subtotal - $discount_amount) + $tax_amount;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discount_amount, 2),
            'tax_total' => round($tax_amount, 2),
            'total' => round($total, 2),
        ];
    }
}