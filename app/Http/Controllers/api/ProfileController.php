<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;


class ProfileController extends BaseController
{
    public function userProfile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->sendError([], 'Unauthorized');
            }

            $success = [
                'name' => $user->name,
                'role' => $user->role,
            ];

            return $this->sendResponse($success, 'User data retrieved successfully');

        } catch (\Throwable $e) {
            return $this->sendError([], 'Server error. Kindly contact system administrator');
        }
    }
}
