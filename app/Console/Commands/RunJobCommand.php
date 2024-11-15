<?php

// In a custom Artisan command
namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunJobCommand extends Command
{
    protected $signature = 'job:run-background';
    protected $description = 'Run a background job manually';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        // Define the list of classes and methods with their parameters and retries
        $jobs = [
            [
                'class' => 'App\\Jobs\\ArithmeticJob',
                'method' => 'handle',
                'parameters' => [5, 10, 'add'],
                'maxRetries' => 3,
            ],
            [
                'class' => 'App\\Services\\RoutineService',
                'method' => 'randomRoutine',
                'parameters' => ['email', 5, true],
                'maxRetries' => 3,
            ],
            // Add more jobs as needed
        ];

        // Loop through each job and run it in the background
        foreach ($jobs as $job) {
            runBackgroundJob(
                $job['class'],
                $job['method'],
                $job['parameters'],
                $job['maxRetries']
            );

            $this->info("Job {$job['class']}::{$job['method']} has been sent to the background.");
        }

    }
}
