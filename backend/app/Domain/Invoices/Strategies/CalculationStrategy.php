<?php

namespace App\Domain\Invoices\Strategies;

interface CalculationStrategy
{
    public function calculate(array $items, float $tax_total = 0, float $discount = 0): array;
}