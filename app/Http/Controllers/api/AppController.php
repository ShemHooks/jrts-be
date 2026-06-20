<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;

class AppController extends BaseController
{
    public function retrieveDepartmentList()
    {
        $departments = Department::where('status', 'active')->get();

        $success = $departments->map(function ($dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->dept_name,
            ];
        });

        return $this->sendResponse($success, "");
    }

    public function TechniciansList()
    {
        $technicians = User::query()
            ->where('role', 'Technician')
            ->withCount(['assignedRequests as request_count'])
            ->orderBy('request_count', 'asc')
            ->get();

        return response()->json([
            'message' => 'Technicians sorted by workload',
            'data' => $technicians
        ]);
    }
}
