<?php

namespace App\Http\Controllers\api;

use Date;
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

        $jobRequests = JobRequest::with(['requester', 'requestingOffice'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {


                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('look_for', 'like', "%{$keyword}%");

                    $q->orWhereHas('requester', function ($qq) use ($keyword) {
                        $qq->where('name', 'like', "%{$keyword}%");
                    });

                    $q->orWhereHas('requestingOffice', function ($qq) use ($keyword) {
                        $qq->where('name', 'like', "%{$keyword}%");
                    });

                });
            })
            ->paginate((int) $number_per_page);


        return $this->sendResponse($jobRequests, 'List of Jobs');
    }

    public function retrieveIndividualRequest()
    {
        $user = Auth::user();

        $job_request = JobRequest::where('requested_by', $user->id);

        if (!$job_request) {
            return $this->sendError([], 'No Request Found');
        }

        return $this->sendResponse($job_request, 'List of Job Requests');

    }

    public function createJobRequest(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'require|string',
            'description' => 'required|string',
            'requested_from' => 'required|string',
            'look_for' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $input['requested_by'] = $user->id;

        $job_request = JobRequest::create($input);

        $success['title'] = $job_request->title;

        $logs = [
            'user_id' => $user->id,

            'action' => "{$user->name} Created {$job_request->title} request"

        ];

        $this->insertSystemLogs($logs);

        $date = now()->format('F d, Y');
        $time = now()->format('H:i');

        $timeStamp = [
            'request_id' => $job_request->id,

            'description' => "{$user->name} Created {$job_request->title} request on {$date} ",
            'action' => 'processed',
            'date' => $date,
            'time' => $time
        ];

        $this->requestTimeStamp($timeStamp);

        return $this->sendResponse($success, 'Job Request Submitted Successfully');

    }
}
