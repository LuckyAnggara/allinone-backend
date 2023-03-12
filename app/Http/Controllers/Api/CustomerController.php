<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;

class CustomerController extends BaseController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 5);
        $name = $request->input('name');
        $branch = '1';
        $query = Customer::with(['branch', 'maker']);

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($branch) {
            $query->where('branch_id', $branch);
        }

        $result = $query->latest()->paginate($limit);

        return $this->sendResponse($result, 'Data fetched');
    }
    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return response()->json(
            [
                'customer' => $customer,
            ],
            200,
        );
    }
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $customer->name = $request->input('name');
        $customer->address = $request->input('address');
        $customer->phone_number = $request->input('phone_number');
        $customer->member = $request->input('member');
        $customer->company = $request->input('company');

        $customer->save();

        return response()->json(
            [
                'message' => 'Customer updated successfully',
                'customer' => $customer,
            ],
            200,
        );
    }
}
