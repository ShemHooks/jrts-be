<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\BaseController as BaseController;
use Exception;
use Illuminate\Http\Request;
use App\Models\JobRequest;
use App\Models\JobRequestTimeStamp;
use Carbon\Carbon;


class DashboardController extends BaseController
{
    public function admin()
    {
        try {
            $status_counts = JobRequest::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $pending_count = $status_counts['pending'] ?? 0;
            $responding_count = $status_counts['responding'] ?? 0;
            $inbound_count = $status_counts['in-bounded'] ?? 0;
            $done_tasks_count = $status_counts['done'] ?? 0;

            $weekly_accomplishment = JobRequest::join('job_request_time_stamps as jts', function ($join) {
                $join->on('job_requests.id', '=', 'jts.request_id')
                    ->where('jts.action', 'done');
            })
                ->where('jts.date', '>=', now()->subDays(7)->toDateString())
                ->selectRaw('jts.date, COUNT(*) as total')
                ->groupBy('jts.date')
                ->orderBy('jts.date')
                ->get();

            return $this->sendResponse([
                'pending' => [
                    'title' => 'Pending Requests',
                    'value' => $pending_count
                ],
                'responding' => [
                    'title' => 'In Responding',
                    'value' => $responding_count
                ],
                'inbound' => [
                    'title' => 'Total Inbound',
                    'value' => $inbound_count
                ],
                'done' => [
                    'title' => 'Tasks Done',
                    'value' => $done_tasks_count
                ],
                'weekly_accomplishment' => $weekly_accomplishment,
            ], 'Dashboard data retrieved successfully');

        } catch (Exception $e) {
            return $this->sendError('Server error. Kindly contact system administrator', $e->getMessage());
        }



    }

    public function client()
    {
        $status_counts = JobRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $pending_count = $status_counts['pending'] ?? 0;
        $responding_count = $status_counts['responding'] ?? 0;
        $inbound_count = $status_counts['in-bounded'] ?? 0;
        $done_tasks_count = $status_counts['done'] ?? 0;
    }
}
