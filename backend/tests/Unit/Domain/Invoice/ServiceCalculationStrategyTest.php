<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Strategies\ServiceCalculationStrategy;
use PHPUnit\Framework\TestCase;

class ServiceCalculationStrategyTest extends TestCase
{
    public function test_service_applies_discount_before_tax(): void
    {
        $strategy = new ServiceCalculationStrategy();

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
        // discounted = 180
        // tax = 18
        // total = 198

        $this->assertEquals(200.00, $result['subtotal']);
        $this->assertEquals(20.00, $result['discount_total']);
        $this->assertEquals(18.00, $result['tax_total']);
        $this->assertEquals(198.00, $result['total']);
    }
}