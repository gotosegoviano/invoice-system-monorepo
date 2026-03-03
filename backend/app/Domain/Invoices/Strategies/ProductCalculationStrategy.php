<?php

namespace App\Domain\Invoices\Strategies;

class ProductCalculationStrategy implements CalculationStrategy
{
    /**
     * Calculates the invoice totals for product-based invoices.
     * Each item should have 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * The calculation applies tax to the base price and then subtracts any discounts.
     * @param array $items An array of items, each containing 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * @return array An array containing 'subtotal', 'discount_total', 'tax_total', and 'total'.
     */
    public function calculate(array $items): array
    {
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $price = (float) $item['price'];
            $discount = (float) ($item['discount'] ?? 0);
            $taxRate = (float) $item['tax_rate'];

            $base = $quantity * $price;
            $tax = $base * ($taxRate / 100);
            $subtotalWithTax = $base + $tax;

            $subtotal += $base;
            $discountTotal += $discount;
            $taxTotal += $tax;
        }

        $total = ($subtotal + $taxTotal) - $discountTotal;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'total' => round($total, 2),
        ];
    }
}