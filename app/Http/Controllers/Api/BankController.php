<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankController extends BaseController
{

    public function index(Request $request)
    {
        $name = $request->input('name');
        
        $items = Bank::where('branch_id', Auth::user()->branch_id)->when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })->get();

        return $this->sendResponse($items, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'name' => 'required',
                'number_account' => 'required',
            ]);

            $bank = Bank::create($validatedData);
            DB::commit();
            return $this->sendResponse($bank, 'Data created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, 'Failed to saved data');
        }
    }
}
