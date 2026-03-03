<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ProductCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ProductCalculationStrategyTest extends TestCase
{
    #[Test]
    public function test_single_item(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [['quantity' => 2, 'price' => 100, 'discount' => 20, 'tax_rate' => 10]];
        $result = $strategy->calculate($items);

        $this->assertEquals(200, $result['subtotal']);
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(20, $result['tax_total']);
        $this->assertEquals(200, $result['total']);
    }

    #[Test]
    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [['quantity' => 1, 'price' => 100, 'discount' => 0, 'tax_rate' => 0]];
        $result = $strategy->calculate($items);

        $this->assertEquals(100, $result['subtotal']);
        $this->assertEquals(0, $result['discount_total']);
        $this->assertEquals(0, $result['tax_total']);
        $this->assertEquals(100, $result['total']);
    }
}