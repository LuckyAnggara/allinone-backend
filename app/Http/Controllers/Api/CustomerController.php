<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');
       $branch = $request->input('branch');

        $query = Customer::with(['branch', 'maker'])
        ->where('member',true)
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($branch, function ($query, $branch) {
                return $query->where('branch_id', $branch);
            })
            ->latest()
            ->paginate($perPage);

        return $this->sendResponse($query, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $customer = $data->customer;
        $user = $data->user;
        try {
            DB::beginTransaction();
            $result = Customer::create($customer, $user);
            DB::commit();
            return $this->sendResponse($result, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data, $user)
    {
        $member = false;        
        if($data->saveCustomer){
            $member = true;
        }
        return Customer::create([
            'name' => $data->name,
            'address' => $data->address,
            'phone_number' => $data->phone_number,
            'member' => $member,
            'company' => $data->company ?? 0,
            'pic' => $data->pic ?? '',
            'created_by' => $user->id,
            'branch_id' => $user->branchId,
        ]);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $customer = Customer::findOrFail($id);
            $customer->update($input);

            DB::commit();
            return $this->sendResponse($customer, 'Customer updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }
}
