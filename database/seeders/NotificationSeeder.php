<?php

namespace Database\Seeders;

use App\Enums\NotificationStatusEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Notification::create([
            'type' => NotificationTypeEnum::Customer,
            'message' => 'Data customer belum lengkap',
            'link' => '/orders/123',
            'notifiable_id' => '1',
            'status' => NotificationStatusEnum::Unread,
        ]);
    }
}
