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

        // taxIsPercentage = true, discountIsPercentage = false
        $result = $strategy->calculate($items, 10, 20, true, false);

        $this->assertEquals(200, $result['subtotal']); // 2*100
        $this->assertEquals(20, $result['tax_total']); // 10% de 200
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(200, $result['total']); // (200+20)-20
    }

    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [
            ['quantity' => 1, 'price' => 100]
        ];

        $result = $strategy->calculate($items, 0, 0, true, true);

        $this->assertEquals(100, $result['subtotal']);
        $this->assertEquals(0, $result['discount_total']);
        $this->assertEquals(0, $result['tax_total']);
        $this->assertEquals(100, $result['total']);
    }

    public function test_percentage_discount(): void
    {
        $strategy = new ProductCalculationStrategy();
        $items = [
            ['quantity' => 3, 'price' => 50]
        ];

        // tax 10% percentage, discount 20% percentage
        $result = $strategy->calculate($items, 10, 20, true, true);

        $subtotal = 3 * 50; // 150
        $tax = $subtotal * 0.10; // 15
        $discount = ($subtotal + $tax) * 0.20; // (150+15)*0.2 = 33
        $total = ($subtotal + $tax) - $discount; // 165 - 33 = 132

        $this->assertEquals(150, $result['subtotal']);
        $this->assertEquals(15, $result['tax_total']);
        $this->assertEquals(33, $result['discount_total']);
        $this->assertEquals(132, $result['total']);
    }
}