<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserManagement extends BaseController
{
    public function index(Request $request)
    {
        $number_per_page = $request->input('per_page', 10);
        $keyword = $request->input('keyword');
        $status = $request->input('status');
        $role = $request->input('role');
        $position = $request->input('position');

        $users = User::with('office')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($position, function ($query) use ($position) {
                $query->where('position', $position);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })->paginate((int) $number_per_page);

        return $this->sendResponse($users, 'List of Users');
    }

    public function archiveAccount(string $id)
    {
        $initiator = Auth::user();

        $user = User::findOrFail($id);

        if ($user->account_status === 'archived') {
            return $this->sendError('User Account Already Archived');
        }

        $user->account_status = 'archived';

        $user->save();

        $logs = [
            'user_id' => $initiator->id,

            'action' => "{$initiator->name} Archived {$user->name} Account"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse([], 'User Archived Successfully');

    }

    public function unArchiveAccount(string $id)
    {
        $initiator = Auth::user();

        $user = User::findOrFail($id);

        if ($user->account_status === 'active') {
            return $this->sendError('User Account Already Active');
        }

        $user->account_status = 'active';

        $user->save();

        $logs = [
            'user_id' => $initiator->id,

            'action' => "{$initiator->name} Unarchived {$user->name} Account"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse([], 'User Archived Successfully');
    }

    public function deleteUserAccount(string $id)
    {
        $initiator = Auth::user();

        $user = User::findOrFail($id);

        if ($user->account_status === 'active') {
            return $this->sendError('User Account is Active', []);
        }

        $user->delete();

        $logs = [
            'user_id' => $initiator->id,

            'action' => "{$initiator->name} deleted {$user->name} Account"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse('User Deleted Successfully');

    }


}
