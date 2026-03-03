<?php

namespace App\Domain\Invoices\Strategies;

interface CalculationStrategy
{
    /**
     * Calculates the invoice totals based on the provided items.
     * Each item should have 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * @param array $items An array of items, each containing 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * @return array An array containing 'subtotal', 'discount_total', 'tax_total', and 'total'.
     */
    public function calculate(array $items): array;
}