<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');

        $query = Customer::with(['branch', 'maker'])
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($query, 'Data fetched');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = Customer::create($request->all());
            DB::commit();
            return $this->sendResponse($customer, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data)
    {
        return Customer::create([
            'name' => $data->name,
            'address' => $data->address,
            'phone_number' => $data->phone_number,
            'member' => $data->member ?? 0,
            'company' => $data->company ?? 0,
            'pic' => $data->pic ?? '',
            'created_by' => $data->userId,
            'branch_id' => 1,
        ]);
    }
}
