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

        // tax_total y discount global
        $result = $strategy->calculate($items, 10, 20);

        $this->assertEquals(200, $result['subtotal']);       // 2*100
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(10, $result['tax_total']);
        $this->assertEquals(190, $result['total']);          // subtotal + tax - discount
    }

    public function test_multiple_items(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 2, 'price' => 100],
            ['quantity' => 1, 'price' => 50]
        ];

        $result = $strategy->calculate($items, 20, 15);

        $this->assertEquals(250, $result['subtotal']);       // 200 + 50
        $this->assertEquals(15, $result['discount_total']);
        $this->assertEquals(20, $result['tax_total']);
        $this->assertEquals(255, $result['total']);          // subtotal + tax - discount
    }

    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ServiceCalculationStrategy();
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