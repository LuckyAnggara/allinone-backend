<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemUnit;
use Illuminate\Support\Facades\DB;

class ItemUnitController extends BaseController
{

    public function index(Request $request)
    {
        $name = $request->input('name');
        $items = ItemUnit::when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->latest()
            ->get();

        return $this->sendResponse($items, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'name' => 'required',
            ]);

            $brand = ItemUnit::create($validatedData);
            DB::commit();
            return $this->sendResponse($brand, 'Data created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
        }
    }

  
}
