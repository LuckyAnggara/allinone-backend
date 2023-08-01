<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\TaxDetail;
use Illuminate\Support\Facades\DB;

class TaxController extends BaseController
{

    public function index(Request $request)
    {
        $name = $request->input('name');
        $items = TaxDetail::when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })

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

            $tax = TaxDetail::create($validatedData);
            DB::commit();
            return $this->sendResponse($tax, 'Data created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
        }
    }
}
