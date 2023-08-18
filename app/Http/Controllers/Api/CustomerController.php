<?php

namespace App\Http\Controllers\Api;

use App\Enums\CustomerTypeEnum;
use App\Enums\NotificationStatusEnum;
use App\Enums\NotificationTypeEnum;
use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class CustomerController extends BaseController
{

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name', null);
        $uuid = $request->input('uuid', null);
        $list = $request->input('list', false);
        $branch = $request->input('branch');

        if ($list == true) {
            $query = Customer::with(['branch', 'maker'])
                ->where('member', true)
                ->when($name, function ($query) use ($name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->when($uuid, function ($query) use ($uuid) {
                    return $query->where('uuid', 'like', '%' . $uuid . '%');
                })
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch_id', $branch);
                })
                ->latest()
                ->paginate($perPage);
            return $this->sendResponse($query, 'Data fetched');
        }
        if ($name) {
            $query = Customer::with(['branch', 'maker'])
                ->where('member', true)
                ->when($name, function ($query) use ($name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->when($branch, function ($query, $branch) {
                    return $query->where('branch_id', $branch);
                })
                ->latest()
                ->paginate($perPage);
            return $this->sendResponse($query, 'Data fetched');
        } else {
            return $this->sendResponse([], 'Data fetched');
        }
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = Customer::create([
                'name' => $data->name,
                'type' => $data->type,
                'address' => $data->address,
                'email' => $data->email,
                'phone_number' => $data->phoneNumber,
                'member' => true,
                'company' => $data->company ?? 0,
                'pic' => $data->pic ?? '',
                'created_by' => $data->user->id,
                'branch_id' => $data->user->branch->id,
            ]);
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
        if ($data->saveCustomer) {
            $member = true;
            $notifData =  [
                'type' => NotificationTypeEnum::Customer,
                'message' =>  'Data customer baru a/n ' . $data->name . ' belum lengkap',
                'link' =>  '/sales/1/invoice',
                'user' =>  $user,
                'status' =>  NotificationStatusEnum::Unread
            ];

            NotificationController::create(json_encode($notifData));
        }
        // DB::beginTransaction();
        // try {
        $customer = Customer::create([
            'name' => $data->name,
            'type' => $data->type,
            'address' => $data->address,
            'phone_number' => $data->phone_number,
            'member' => $member,
            'company' => $data->company ?? 0,
            'pic' => $data->pic ?? '',
            'created_by' => $user->id,
            'branch_id' => $user->branch->id,
        ]);
        return $customer;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return $e->getMessage();
        // }
    }

    public function show($uuid)
    {
        $result = Customer::where('uuid', $uuid)
            ->with(['branch', 'maker'])

            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
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
