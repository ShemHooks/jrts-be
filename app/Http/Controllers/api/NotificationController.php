<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;


class NotificationController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $notifications = Notification::where(function ($query) use ($user) {
                $query->where('receiver', $user->id)
                    ->orWhere('is_broadcast', true);
            })
                ->orderBy('created_at', 'desc')
                ->paginate(10);


            return $this->sendResponse($notifications, 'Notifications retrieved successfully');

        } catch (\Exception $e) {
            \Log::error("Fetch Notification Error: " . $e->getMessage());
            return $this->sendError([], 'Failed to retrieve notifications');
        }
    }

    public function getUnreadNotificationCount()
    {
        $user = Auth::user();

        $count = Notification::where(function ($query) use ($user) {
            $query->where('receiver', $user->id)
                ->orWhere('is_broadcast', true);
        })
            ->where('is_read', false)
            ->count();



        return $this->sendResponse($count, 'Notification Count');
    }
}
