<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class JobManagementController extends Controller
{
    // Display active and failed jobs
    public function index()
    {
        $activeJobs = DB::table('jobs')->get();
        $failedJobs = DB::table('failed_jobs')->get();

        return view('jobs.index', compact('activeJobs', 'failedJobs'));
    }

    // Cancel a running job
    public function cancelJob($id)
    {
        DB::table('jobs')->where('id', $id)->delete();

        return redirect()->route('jobs.index')->with('success', 'Job cancelled successfully.');
    }

    // Retry a failed job
    public function retryJob($id)
    {
        $failedJob = DB::table('failed_jobs')->where('id', $id)->first();

        if ($failedJob) {
            Queue::connection('sync')->pushRaw($failedJob->payload, $failedJob->queue);
            DB::table('failed_jobs')->where('id', $id)->delete();

            return redirect()->route('jobs.index')->with('success', 'Job retried successfully.');
        }

        return redirect()->route('jobs.index')->with('error', 'Job not found.');
    }
}
