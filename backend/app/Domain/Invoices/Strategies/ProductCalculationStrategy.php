<?php

namespace App\Domain\Invoices\Strategies;

class ProductCalculationStrategy implements CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0, bool $tax_is_percentage = true, bool $discount_is_percentage = true): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $price = (float) $item['price'];

            $subtotal += $quantity * $price;
        }

        // tax total
        $tax_amount = $tax_is_percentage ? $subtotal * ($tax_total / 100) : $tax_total;

        // discount total
        $discount_amount = $discount_is_percentage ? ($subtotal + $tax_amount) * ($discount / 100) : $discount;

        $total = ($subtotal + $tax_amount) - $discount_amount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($tax_amount, 2),
            'discount_total' => round($discount_amount, 2),
            'total' => round($total, 2),
        ];
    }
}