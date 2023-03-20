<?php

namespace App\Helpers;

use App\Models\Sales;

class InvoiceHelper
{
    public static function generateInvoiceNumber()
    {
        $lastInvoice = Sales::orderBy('id')->first();
        $number = $lastInvoice ? $lastInvoice->invoice : 'BBM-1000';
        return preg_replace_callback("|(\d+)|", function ($matches) {
            return $matches[1] + 1;
        }, $number);
    }
}
