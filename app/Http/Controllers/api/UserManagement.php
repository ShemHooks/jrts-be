<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\User;

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


}
