<?php

namespace App\Domain\Invoices\Strategies;

interface CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0, bool $tax_is_percentage = true, bool $discount_is_percentage = true): array;
}