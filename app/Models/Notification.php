<?php

namespace App\Models;

use App\Enums\NotificationStatusEnum;
use App\Enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'message', 'link', 'notifiable_id', 'status'];
    protected $casts = [
        'type' => NotificationTypeEnum::class,
        'status' => NotificationStatusEnum::class
    ];

    public function notifiable()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }
}
