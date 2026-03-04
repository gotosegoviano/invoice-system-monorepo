<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ProductCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ProductCalculationStrategyTest extends TestCase
{
    public function test_single_item(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [
            ['quantity' => 2, 'price' => 100]
        ];

        $result = $strategy->calculate($items, 10, 20);

        $this->assertEquals(200, $result['subtotal']);
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(10, $result['tax_total']);
        $this->assertEquals(190, $result['total']);
    }

    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [
            ['quantity' => 1, 'price' => 100]
        ];

        $result = $strategy->calculate($items, 0, 0);

        $this->assertEquals(100, $result['subtotal']);
        $this->assertEquals(0, $result['discount_total']);
        $this->assertEquals(0, $result['tax_total']);
        $this->assertEquals(100, $result['total']);
    }
}