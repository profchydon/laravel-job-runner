<?php

namespace App\Console\Commands;

use App\Models\JobLog;
use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecuteBackgroundJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:execute-background-job {class} {method} {--parameters=*} {--maxRetries=3} {--delay=0} {--priority=normal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes a specified class and method as a background job.';


    /**
     * List of approved classes and their methods.
     *
     * @var array
     */
    protected $approvedClasses = [
        'App\\Jobs\\ArithmeticJob' => ['handle', 'calculate'],
        'App\\Services\\RoutineService' => ['randomRoutine'],
    ];

    /**
     * Execute the console command.
     *
     * This command runs a specified class and method as a background job. It will attempt to run the job up to the
     * specified number of times before giving up.
     *
     * @return int
     */
    public function handle(): int
    {
        $className = $this->argument('class');
        $methodName = $this->argument('method');
        $parameters = array_map('trim', explode(',', $this->option('parameters')[0]));
        $maxRetries = (int) $this->option('maxRetries');
        $delay = (int) $this->option('delay');
        $priority = $this->option('priority');

        // Delay execution if specified
        if ($delay > 0) {
            Log::channel('background_jobs')->info("Delaying job execution for {$delay} seconds.", [
                'class' => $className,
                'method' => $methodName,
                'parameters' => $parameters,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);
            sleep($delay);
        }

        // Create a job log entry
        $jobLog = JobLog::create([
            'class_name' => $className,
            'method_name' => $methodName,
            'parameters' => json_encode($parameters),
            'status' => 'pending',
        ]);

        // Check if the class and method are approved for execution
        if (!$this->isClassApproved($className, $methodName)) {
            Log::channel('background_jobs_errors')->error("Execution Failed: Unauthorized class attempted to run: $className", [
                'class' => $className,
                'method' => $methodName,
                'parameters' => $parameters,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);
            return 1;
        }

        // Log the start of the job
        Log::channel('background_jobs')->info("Starting job: $className::$methodName", [
            'class' => $className,
            'method' => $methodName,
            'parameters' => $parameters,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ]);

        // Set the number of attempts to 0
        $attempt = 0;

        // Loop until the job is successful or the maximum number of attempts is reached
        while ($attempt < $maxRetries) {
            try {
                // Increment the attempt number
                $attempt++;
                $jobLog->update(['status' => 'running', 'retry_count' => $attempt]);

                // Check if the class exists
                if (!class_exists($className)) {
                    Log::channel('background_jobs_errors')->error("Execution Failed: Class $className does not exist.", [
                        'class' => $className,
                        'method' => $methodName,
                        'parameters' => $parameters,
                        'attempt' => $attempt,
                        'timestamp' => Carbon::now()->toDateTimeString(),
                    ]);
                    return 1;
                }

                // Create an instance of the class
                $instance = app($className);

                // Check if the instance is null
                if (is_null($instance)) {
                    Log::channel('background_jobs_errors')->error("Execution Failed: Instance of $className is null.", [
                        'class' => $className,
                        'method' => $methodName,
                        'parameters' => $parameters,
                        'attempt' => $attempt,
                        'timestamp' => Carbon::now()->toDateTimeString(),
                    ]);
                    return 1;
                }

                // Check if the method exists on the instance
                if (!method_exists($instance, $methodName)) {
                    Log::channel('background_jobs_errors')->error("Execution Failed: Method $methodName does not exist on $className.", [
                        'class' => $className,
                        'method' => $methodName,
                        'parameters' => $parameters,
                        'attempt' => $attempt,
                        'timestamp' => Carbon::now()->toDateTimeString(),
                    ]);
                    return 1;
                }

                // Call the method on the instance with the provided parameters
                call_user_func_array([$instance, $methodName], $parameters);

                // Log successful execution
                $jobLog->update(['status' => 'completed']);
                Log::channel('background_jobs')->info("Execution Successful: Job completed: $className::$methodName.", [
                    'class' => $className,
                    'method' => $methodName,
                    'parameters' => $parameters,
                    'attempt' => $attempt,
                    'timestamp' => Carbon::now()->toDateTimeString(),
                ]);

                // Return a success code
                return 0;
            } catch (Exception $e) {
                // Update the job log with failure status and error message
                $jobLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'retry_count' => $attempt,
                ]);

                // Log the error with details
                Log::channel('background_jobs_errors')->error("Execution Failed: Error in job $className::$methodName - Attempt $attempt: " . $e->getMessage(), [
                    'class' => $className,
                    'method' => $methodName,
                    'parameters' => $parameters,
                    'attempt' => $attempt,
                    'timestamp' => Carbon::now()->toDateTimeString(),
                ]);
            }
        }

        // Log that the job has failed after the maximum number of attempts
        Log::channel('background_jobs_errors')->error("Execution Failed: Job failed after {$maxRetries} attempts: $className::$methodName", [
            'class' => $className,
            'method' => $methodName,
            'parameters' => $parameters,
            'attempt' => $attempt,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ]);

        // Add exponential backoff (delay before retrying)
        sleep(pow(2, $attempt));

        // Log the final failure and return a failure code
        $this->logFailure($jobLog, "Job failed after {$maxRetries} attempts.");
        return 1;
    }

    /**
     * Validate if the class and method are approved for execution.
     *
     * @param  string  $className
     * @param  string  $methodName
     * @return bool
     */
    protected function isClassApproved(string $className, string $methodName): bool
    {
        // Check if the class is in the approved classes list and if the method is valid for that class
        return isset($this->approvedClasses[$className]) && in_array($methodName, $this->approvedClasses[$className]);
    }

    private function logFailure(JobLog $jobLog, string $message)
    {
        $jobLog->update(['status' => 'failed', 'error_message' => $message]);
        Log::error($message);
    }
}
