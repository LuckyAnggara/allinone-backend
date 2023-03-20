<?php

namespace App\Helpers;

use App\Models\Sales;

class InvoiceHelper
{
    public static function generateInvoiceNumber()
    {
        $lastInvoice = Sales::orderBy('id')->first();

        if (!$lastInvoice) {
            $number = 'BBM-1000';
        } else {
            $number = $lastInvoice->invoice;
            $number = preg_replace_callback("|(\d+)|", function($matches) {
                return $matches[1] + 1;
            }, $number);
        }

        return $number;
    }
}
