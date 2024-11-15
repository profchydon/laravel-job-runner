# Background Job Runner System in Laravel

The `runBackgroundJob` function allows you to execute specific class methods in Laravel as background jobs. This is useful for handling time-consuming or repetitive tasks without blocking the main application flow.

---

## Table of Contents

- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Setup Instructions](#setup-instructions)
- [Function Definition](#function-definition)
- [Parameters](#parameters)
- [Usage](#usage)
- [Detailed Explanation](#detailed-explanation)
- [Logging](#logging)
- [Example Logs](#example-logs)

---

## Overview

The `runBackgroundJob` function is a utility function that lets you run specified methods on classes as background jobs. You can pass the class and method you want to execute along with any required parameters, and it will handle running the job in the background with optional retry capabilities.


## Prerequisites
Before setting up the system, ensure the following software is installed on your machine:

Docker (for running with Sail)
Node.js (for Vite build and asset management)


## Setup Instructions

1. Clone the Repository
Clone the repository to your local machine:
```
git clone https://github.com/profchydon/laravel-job-runner.git
cd laravel-job-runner
```

2. Start the app
Run the following command in your terminal to start the app
``` 
/.start.sh
```

Make sure to select 

![alt text](<Screenshot at Nov 15 18-30-06.png>)

3. Visit http://localhost/jobs


---

## Function Definition

```php
/**
 * Runs a specified class and method as a background job.
 *
 * @param string $class The fully qualified class name of the job
 * @param string $method The method to call on the job
 * @param array $parameters An array of parameters to pass to the method
 * @param int $maxRetries The maximum number of retry attempts if the job fails
 * @return void
 * @throws \InvalidArgumentException
 * @throws \RuntimeException
 */
function runBackgroundJob(string $class, string $method, array $parameters = [], int $maxRetries = 3): void
```

## Parameters
```
$class (string): The fully qualified class name, e.g., 'App\\Jobs\\ArithmeticJob'.
$method (string): The name of the method to call on the specified class.
$parameters (array): An array of parameters to pass to the method.
$maxRetries (int): The maximum number of retry attempts if the job fails. Default is 3.
```

## Usage
To use runBackgroundJob, you can call it directly from a command 

`alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'`

`sail artisan job:run-background`

This will queue each job defined in the $jobs array and execute it as a background process.

## Logging
The function logs both successful and failed job executions for easy monitoring.

Log Types
Successful Execution: Logs when a job completes successfully.
Error Logs: Captures errors when a job fails, including class, method, parameters, and error messages.
Logs are stored in the background_jobs and background_jobs_errors log channels.


## Example Logs
// Success log
```
[2024-11-15 17:09:43] local.INFO: Starting job: App\Jobs\ArithmeticJob::handle {"class":"App\\Jobs\\ArithmeticJob","method":"handle","parameters":["5","10","add"],"timestamp":"2024-11-15 17:09:43"} 
[2024-11-15 17:09:43] local.INFO: Execution Successful: Job completed: App\Jobs\ArithmeticJob::handle. {"class":"App\\Jobs\\ArithmeticJob","method":"handle","parameters":["5","10","add"],"attempt":1,"timestamp":"2024-11-15 17:09:43"} 
[2024-11-15 17:09:44] local.INFO: Starting job: App\Services\RoutineService::randomRoutine {"class":"App\\Services\\RoutineService","method":"randomRoutine","parameters":["email","5","1"],"timestamp":"2024-11-15 17:09:44"} 
[2024-11-15 17:09:44] local.INFO: Execution Successful: Job completed: App\Services\RoutineService::randomRoutine. {"class":"App\\Services\\RoutineService","method":"randomRoutine","parameters":["email","5","1"],"attempt":1,"timestamp":"2024-11-15 17:09:44"} 
[2024-11-15 17:32:28] local.INFO: Starting job: App\Jobs\ArithmeticJob::handle {"class":"App\\Jobs\\ArithmeticJob","method":"handle","parameters":["5","10","add"],"timestamp":"2024-11-15 17:32:28"} 
[2024-11-15 17:32:28] local.INFO: Execution Successful: Job completed: App\Jobs\ArithmeticJob::handle. {"class":"App\\Jobs\\ArithmeticJob","method":"handle","parameters":["5","10","add"],"attempt":1,"timestamp":"2024-11-15 17:32:28"} 
[2024-11-15 17:32:30] local.INFO: Starting job: App\Services\RoutineService::randomRoutine {"class":"App\\Services\\RoutineService","method":"randomRoutine","parameters":["email","5","1"],"timestamp":"2024-11-15 17:32:30"} 
[2024-11-15 17:32:30] local.INFO: Execution Successful: Job completed: App\Services\RoutineService::randomRoutine. {"class":"App\\Services\\RoutineService","method":"randomRoutine","parameters":["email","5","1"],"attempt":1,"timestamp":"2024-11-15 17:32:30"} 
 
```

// Error log
```
[2024-11-15 10:05:00] background_jobs_errors.ERROR: Job failed after 3 attempts: App\\Jobs\\ExampleJob::handle.
```
