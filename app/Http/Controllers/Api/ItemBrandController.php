<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\ItemBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemBrandController extends BaseController
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        $items = ItemBrand::where('branch_id', Auth::user()->branch_id)->when($name, function ($query, $name) {
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

            $brand = ItemBrand::create([
                'name' => $request->name,
                'description' =>  $request->description,
                'created_by' => Auth::user()->id,
                'branch_id' => Auth::user()->branch_id,
            ]);
            DB::commit();
            return $this->sendResponse($brand, 'Data created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
        }
    }

    // public function store(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         // validate request data
    //         $validatedData = $request->validate([
    //             'name' => 'required',
    //             'unit_id' => 'required',
    //             'brand_id' => 'required',
    //             'warehouse_id' => 'required',
    //             'rack' => 'required',
    //             'created_by' => 'required',
    //         ]);
    //         // create a new instance of YourModel using the validated data
    //         $item = Item::create($validatedData);
    //         DB::commit();
    //         return $this->sendResponse($item, 'Data saved successfully');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->sendError($e, 'Failed to saved data');
    //     }
    // }
}
