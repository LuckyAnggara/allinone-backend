<?php

namespace App\Http\Controllers\Api;

use App\Enums\NotificationStatusEnum;
use App\Http\Controllers\Api\BaseController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends BaseController
{

    public function index(Request $request)
    {
        $user = $request->input('user_id');
        $notif = Notification::when($user, function ($query, $user) {
            return $query->where('notifiable_id', $user);
        })->get();

        return $this->sendResponse($notif, 'Data fetched');
    }

    public function getUnread($user)
    {
        $count = Notification::when($user, function ($query, $user) {
            return  $query->where('notifiable_id', $user)->where('status', NotificationStatusEnum::Unread);
        })->count();

        return $this->sendResponse($count, 'Data fetched');
    }

    static function create($data)
    {
        $data = json_decode($data);
        $notification = Notification::create([
            'type' => $data->type,
            'message' =>  $data->message,
            'link' =>  $data->link,
            'notifiable_id' =>  $data->user->id,
            'status' =>  NotificationStatusEnum::Unread,
        ]);

        return $notification;
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        try {
            DB::beginTransaction();
            $notif = Notification::findOrFail($id);
            $notif->update($input);
            DB::commit();
            return $this->sendResponse($notif, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }
}
