<?php

namespace App\Repositories;

use App\Models\JobLog;
use App\Repositories\Contracts\JobLogRepositoryInterface;

class JobLogRepository extends BaseRepository implements JobLogRepositoryInterface
{
    public function model(): string
    {
        return JobLog::class;
    }
}
