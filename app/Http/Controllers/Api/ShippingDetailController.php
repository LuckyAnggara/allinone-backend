<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\ShippingDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingDetailController extends BaseController
{
    public function index()
    {

    }

    public function show($id)
    {
        $result = ShippingDetail::where('sale_id', $id)->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    static function create($data, $saleId)
    {
        return ShippingDetail::create([
        'sale_id'=> $saleId,
        'shipping_vendor_id'=> $data->vendor,
        'price'=> $data->fee,
        'receiver_name'=> $data->receiverName,
        'receiver_address'=> $data->receiverAddress,
        'receiver_postal_code'=> $data->receiverPostalCode,
        'receiver_kelurahan'=> $data->receiverKelurahan,
        'receiver_kecamatan'=> $data->receiverKecamatan,
        'receiver_kota'=> $data->receiverKota,
        'receiver_phone_number'=> $data->receiverPhoneNumber,
        'receiver_email'=> $data->email ?? '',
        'sender_name'=> '',
        'sender_address'=> '',
        'sender_phone_number'=> '',
        ]);
    }
}
