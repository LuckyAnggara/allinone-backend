<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'shipping_vendor_id',
        'price',
        'receiver_name',
        'receiver_address',
        'receiver_postal_code',
        'receiver_kelurahan',
        'receiver_kecamatan',
        'receiver_kota',
        'receiver_phone_number',
        'receiver_email',
        'sender_name',
        'sender_address',
        'sender_phone_number',

    ];

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id')->withTrashed();
    }
}
