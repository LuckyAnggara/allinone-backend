<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemSellingPrice;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

class ItemSellingPriceController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $item_id = $request->input('id');

        $data = ItemSellingPrice::where('item_id', $item_id)->orderBy('created_at', 'DESC')->paginate($perPage);

        return $this->sendResponse($data, 'Data fetched');
    }

    static function create($data,$date=null)
    {
        $item = ItemSellingPrice::where(function ($query) use ($data) {
            $query->where('item_id', $data->id);
            $query->where('price', $data->price);
        })->get();

        if (!isEmpty($item)) {
            return true;
        }


        $result = ItemSellingPrice::create([
            'item_id' => $data->id,
            'price' => $data->price,
             'created_at' => $date ?? Carbon::now()
        ]);

        return $result;
    }
}
