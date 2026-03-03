<?php

namespace App\Domain\Invoices\Enums;

enum InvoiceType: string
{
    case PRODUCT = 'product';
    case SERVICE = 'service';
}