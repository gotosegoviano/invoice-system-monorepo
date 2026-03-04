<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ServiceCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ServiceCalculationStrategyTest extends TestCase
{
    public function test_single_item(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 2, 'price' => 100]
        ];

        // tax = 10% (percentage), discount = 20 (fixed amount)
        $result = $strategy->calculate($items, 10, 20, true, false);

        $subtotal = 2 * 100; // 200
        $discount_amount = 20;
        $tax_amount = ($subtotal - $discount_amount) * 0.10; // 180*0.10 = 18
        $total = ($subtotal - $discount_amount) + $tax_amount; // 180 + 18 = 198

        $this->assertEquals(200, $result['subtotal']);
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(18, $result['tax_total']);
        $this->assertEquals(198, $result['total']);
    }

    public function test_multiple_items(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 2, 'price' => 100],
            ['quantity' => 1, 'price' => 50]
        ];

        // tax = 20% (percentage), discount = 15 (fixed amount)
        $result = $strategy->calculate($items, 20, 15, true, false);

        $subtotal = 2 * 100 + 1 * 50; // 250
        $discount_amount = 15;
        $tax_amount = ($subtotal - $discount_amount) * 0.20; // (250-15)*0.2 = 47
        $total = ($subtotal - $discount_amount) + $tax_amount; // 235 + 47 = 282

        $this->assertEquals(250, $result['subtotal']);
        $this->assertEquals(15, $result['discount_total']);
        $this->assertEquals(47, $result['tax_total']);
        $this->assertEquals(282, $result['total']);
    }

    public function test_percentage_discount(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 3, 'price' => 50]
        ];

        // tax 10%, discount 20%, both percentage
        $result = $strategy->calculate($items, 10, 20, true, true);

        $subtotal = 3 * 50; // 150
        $discount_amount = $subtotal * 0.20; // 30
        $tax_amount = ($subtotal - $discount_amount) * 0.10; // (150-30)*0.10 = 12
        $total = ($subtotal - $discount_amount) + $tax_amount; // 120 + 12 = 132

        $this->assertEquals(150, $result['subtotal']);
        $this->assertEquals(30, $result['discount_total']);
        $this->assertEquals(12, $result['tax_total']);
        $this->assertEquals(132, $result['total']);
    }

    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 1, 'price' => 100]
        ];

        $result = $strategy->calculate($items, 0, 0, true, true);

        $this->assertEquals(100, $result['subtotal']);
        $this->assertEquals(0, $result['discount_total']);
        $this->assertEquals(0, $result['tax_total']);
        $this->assertEquals(100, $result['total']);
    }
}