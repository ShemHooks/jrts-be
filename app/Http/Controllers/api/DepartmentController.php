<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\BaseController;
use App\Models\Department;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DepartmentController extends BaseController
{

    public function index(Request $request)
    {
        $number_per_page = $request->input('per_page', 10);
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $department = Department::when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('dept_name', 'like', "%{$keyword}%")
                    ->orWhere('department_code', 'like', "%{$keyword}%");
            })->paginate((int) $number_per_page);


        return $this->sendResponse($department, 'List of Users');
    }

    public function createDepartment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'department_code' => "nullable|string",
            'dept_name' => "required|string",
            'dept_head_id' => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();


        $dept = Department::create($input);

        $success['name'] = $dept->dept_name;

        return $this->sendResponse($success, 'Department');


    }

    public function archiveDepartment(string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return $this->sendError("You are not allowed to proccedd this action", []);
        }

        $department = Department::findOrFail($id);

        if ($department->status === 'archive') {
            return $this->sendError("Department is Already Archived", []);
        }

        $user->status = 'archive';

        $user->save();

        return $this->sendResponse([], "Department Archived Successfully");

    }

    public function unarchiveDepartment(string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return $this->sendError("You are not allowed to proccedd this action", []);
        }

        $department = Department::findOrFail($id);

        if ($department->status === 'active') {
            return $this->sendError("Department is Already Active", []);
        }

        $user->status = 'active';

        $user->save();

        return $this->sendResponse([], "Department Archived Successfully");
    }


}
