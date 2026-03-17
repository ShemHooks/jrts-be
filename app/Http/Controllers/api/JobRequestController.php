<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\JobRequest;
use App\Http\Controllers\api\BaseController as BaseController;

class JobRequestController extends BaseController
{
    public function index(Request $request)
    {
        $number_per_page = $request->input('per_page', 10);
        $keyword = $request->input('keyword');
        $status = $request->input('status');

        $department = JobRequest::when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('look_for', 'like', "%{$keyword}%");
            })->paginate((int) $number_per_page);


        return $this->sendResponse($department, 'List of Users');
    }
}
