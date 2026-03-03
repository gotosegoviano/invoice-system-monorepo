<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ProductCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ProductCalculationStrategyTest extends TestCase
{
    public function test_product_applies_tax_before_discount(): void
    {
        $strategy = new ProductCalculationStrategy();

        $items = [
            [
                'quantity' => 2,
                'price' => 100,
                'discount' => 20,
                'tax_rate' => 10,
            ],
        ];

        $result = $strategy->calculate($items);

        // base = 200
        // tax = 20
        // subtotal+tax = 220
        // total = 200

        $this->assertEquals(200.00, $result['subtotal']);
        $this->assertEquals(20.00, $result['discount_total']);
        $this->assertEquals(20.00, $result['tax_total']);
        $this->assertEquals(200.00, $result['total']);
    }
}