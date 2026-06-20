<?php

namespace App\Http\Controllers\api;

use Date;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\JobRequest;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\JobRequestTechnician;
use App\Models\User;

class JobRequestController extends BaseController
{
    public function index(Request $request)
    {
        $number_per_page = $request->input('per_page', 10);
        $keyword = trim($request->input('keyword', ''));
        $status = trim($request->input('status', ''));

        $jobRequests = JobRequest::with(['requester', 'requestingOffice'])
            ->when($request->filled('status'), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($request->filled('keyword'), function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('look_for', 'like', "%{$keyword}%")
                        ->orWhereHas('requester', function ($qq) use ($keyword) {
                            $qq->where('name', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('requestingOffice', function ($qq) use ($keyword) {
                            $qq->where('dept_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate((int) $number_per_page);

        return $this->sendResponse($jobRequests, 'List of Jobs');
    }

    public function retrieveIndividualRequest()
    {
        $user = Auth::user();

        $job_request = JobRequest::with('requestTimeStamp')->where('requested_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($job_request->isEmpty()) {
            return $this->sendError([], 'No Request Found');
        }

        return $this->sendResponse($job_request, 'List of Job Requests');
    }

    public function createJobRequest(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
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

        $date = now()->toDateString();
        $time = now()->toTimeString();

        $timeStamp = [
            'request_id' => $job_request->id,
            'description' => "{$user->name} Created {$job_request->title} request on {$date} ",
            'action' => 'processed',
            'date' => $date,
            'time' => $time
        ];

        $this->requestTimeStamp($timeStamp);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->insertNotification([
                'title' => 'New Job Request',
                'note' => "{$user->name} created a job request: {$job_request->title}",
                'receiver' => $admin->id
            ]);
        }

        return $this->sendResponse($success, 'Job Request Submitted Successfully');

    }

    public function assignTech(Request $request)
    {
        $admin = Auth::user();

        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string|exists:job_requests,id',
            'technician_id' => 'required|string|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $job_request = JobRequest::findOrFail($request->request_id);

        $tech = User::findOrFail($request->technician_id);

        // Check if request already has assigned technician
        $assignment = JobRequestTechnician::where('request_id', $request->request_id)->first();

        $actionType = '';
        $description = '';

        if ($assignment) {

            // OLD TECH
            $oldTech = User::find($assignment->technician_id);

            // REASSIGN
            $assignment->technician_id = $request->technician_id;
            $assignment->save();

            $actionType = 'reassigned';

            $description = "{$admin->name} Reassigned {$job_request->title} request from {$oldTech->name} to {$tech->name}";

        } else {

            // FIRST ASSIGN
            $assignment = JobRequestTechnician::create([
                'request_id' => $request->request_id,
                'technician_id' => $request->technician_id
            ]);

            $actionType = 'assigned';

            $description = "{$admin->name} Assigned {$job_request->title} request to {$tech->name}";
        }

        // Update request status
        $job_request->status = "accepted";
        $job_request->save();

        // SYSTEM LOGS
        $logs = [
            'user_id' => $admin->id,
            'action' => $description
        ];

        $this->insertSystemLogs($logs);

        // TIMESTAMP
        $timeStamp = [
            'request_id' => $job_request->id,
            'description' => $description,
            'action' => $actionType,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString()
        ];

        $this->requestTimeStamp($timeStamp);

        return $this->sendResponse($assignment, "Successfully {$actionType} technician");
    }


}
