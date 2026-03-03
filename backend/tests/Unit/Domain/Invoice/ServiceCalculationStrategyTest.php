<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ServiceCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ServiceCalculationStrategyTest extends TestCase
{
    #[Test]
    public function test_single_item(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [['quantity' => 2, 'price' => 100, 'discount' => 20, 'tax_rate' => 10]];
        $result = $strategy->calculate($items);

        $this->assertEquals(200, $result['subtotal']);
        $this->assertEquals(20, $result['discount_total']);
        $this->assertEquals(18, $result['tax_total']);
        $this->assertEquals(198, $result['total']);
    }

    #[Test]
    public function test_multiple_items(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [
            ['quantity' => 2, 'price' => 100, 'discount' => 10, 'tax_rate' => 10],
            ['quantity' => 1, 'price' => 50, 'discount' => 5, 'tax_rate' => 10]
        ];
        $result = $strategy->calculate($items);

        $this->assertEquals(250, $result['subtotal']);
        $this->assertEquals(15, $result['discount_total']);
        $this->assertEquals(23.5, $result['tax_total']);
        $this->assertEquals(258.5, $result['total']);
    }

    #[Test]
    public function test_zero_discount_and_tax(): void
    {
        $strategy = new ServiceCalculationStrategy();
        $items = [['quantity' => 1, 'price' => 100, 'discount' => 0, 'tax_rate' => 0]];
        $result = $strategy->calculate($items);

        $this->assertEquals(100, $result['subtotal']);
        $this->assertEquals(0, $result['discount_total']);
        $this->assertEquals(0, $result['tax_total']);
        $this->assertEquals(100, $result['total']);
    }
}