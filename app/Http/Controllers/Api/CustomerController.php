<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends BaseController
{
    function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = Customer::create([
                'customer_id' => 1,
                'tanggal_transaksi' => '2023-03-13',
                'total_transaksi' => 100000,
                'status_pembayaran' => 'BELUM LUNAS',
            ]);
            DB::commit();
            return $this->sendResponse($customer, 'Data Created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return  $this->sendResponse($e->getMessage(), 'Error', 404);
        }
    }

    static function create($data)
    {
        $customer = new Customer;
        $customer->name = $data->name;
        $customer->address = $data->address;
        $customer->phone_number = $data->phone_number;
        $customer->member = $data->member;
        $customer->company = $data->company;
        $customer->pic = $data->pic;
        $customer->created_by = $data->created_by;
        $customer->branch_id = $data->branch_id;
        $customer->save();

        return $customer;
    }
}
