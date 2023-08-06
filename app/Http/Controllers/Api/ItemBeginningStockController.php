<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemBeginningStock;
use Illuminate\Support\Facades\DB;


class ItemBeginningStockController extends BaseController
{
    static function create($data, $notes)
    {
        $result = ItemBeginningStock::create([
            'item_id' => $data->id,
            'stock' => $data->stock,
            'price' => $data->price,
            'notes' => $notes,
        ]);

        return $result;
    }
}
