<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\JobLogRepositoryInterface;

class JobLogController extends Controller
{
    public function __construct(private readonly JobLogRepositoryInterface $jobLogRepository)
    {
    }

    public function index()
    {
        $jobs = $this->jobLogRepository->all(true);
        return view('admin.jobs.index', compact('jobs'));
    }
}
