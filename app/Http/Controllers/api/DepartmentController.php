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
    public function createDepartment(Request $request)
    {
        $creator = Auth::user();

        $validator = Validator::make($request->all(), [
            'department_code' => "nullable|string",
            'dept_name' => "required|string",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $input['dept_head_id'] = $creator->id();

        $dept = Department::create($input);

        $success['name'] = $dept->dept_name;

        return $this->sendResponse($success, 'Department');


    }
}
