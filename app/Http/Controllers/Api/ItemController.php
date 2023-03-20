<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\DB;

class ItemController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');

        $items = Item::with(['brand', 'unit',  'maker'])
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($items, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // validate request data
            $validatedData = $request->validate([
                'name' => 'required',
                'unit_id' => 'required',
                'brand_id' => 'required',
                'warehouse_id' => 'required',
                'rack' => 'required',
                'created_by' => 'required',
            ]);
            // create a new instance of YourModel using the validated data
            $item = Item::create($validatedData);
            DB::commit();
            return $this->sendResponse($item, 'Data saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
        }
    }
}
