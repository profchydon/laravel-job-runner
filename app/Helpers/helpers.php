<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

if (!function_exists('runBackgroundJob')) {

    /**
     * Runs a specified class and method as a background job.
     *
     * The class and method should be passed as strings and can be either fully qualified or relative to the app namespace.
     * Parameters can be passed as an array and will be appended to the command as options.
     * If the job fails, it will be retried up to the specified number of times.
     *
     * @param string $class The fully qualified class name of the job
     * @param string $method The method to call on the job
     * @param array $parameters An array of parameters to pass to the method
     * @param int $maxRetries The maximum number of times to retry the job if it fails
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    function runBackgroundJob(string $class, string $method, array $parameters = [], int $maxRetries = 3): void
    {
        // Validate class parameter
        if ($class === null) throw new \InvalidArgumentException('Class cannot be null');

        // Validate method parameter
        if ($method === null) throw new \InvalidArgumentException('Method cannot be null');

        // Check if the class exists
        if (!class_exists($class))  throw new \InvalidArgumentException("Class $class does not exist.");

        if (!method_exists($class, $method)) throw new \InvalidArgumentException("Method $method does not exist on class $class.");

        // Build the command array for the artisan call
        $command = [
            'php',
            'artisan',
            'app:execute-background-job',
            $class,
            $method
        ];

        if ($parameters !== null) {

            // Convert the array to a comma-separated string
            $parameterStr = implode(',', (array)$parameters);

            // Escape the parameter string and append to the command array
            $command[] = "--parameters=" . $parameterStr;
        }

        // Append the maximum retry count to the command array
        $command[] = "--maxRetries=" . $maxRetries;

        try {

            // Determine execution environment and run the command in the background
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

                // Windows environment
                $commandStr = implode(' ', array_filter($command)) . " > NUL 2>&1";
                pclose(popen("start /B " . $commandStr, "r"));
            } else {

                // Unix environment
                $process = new Process($command);
                $process->setOptions(['create_new_console' => true]);
                $process->run();

                // Check if the process was successful
                if (!$process->isSuccessful()) throw new ProcessFailedException($process);
            }
        } catch (\Exception $e) {

            // Handle process execution errors
            throw new \RuntimeException('Error running background job: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
