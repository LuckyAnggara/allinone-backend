<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemPrice;

class ItemPriceController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $item_id = $request->input('id');

        $data = ItemPrice::where('item_id', $item_id)->orderBy('created_at', 'DESC')->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }
}
