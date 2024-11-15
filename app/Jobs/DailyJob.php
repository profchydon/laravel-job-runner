<?php

namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;

class DailyJob
{

     /**
     * Handle the daily job process.
     *
     * @return void
     */
    public function handle(int $num1, int $num2, string $operation): void
    {
        try {
            Log::info('DailyJob: Starting handle method.');

            $result = $this->performOperation($num1, $num2, $operation);

            Log::info("DailyJob: Operation '{$operation}' with {$num1} and {$num2} resulted in {$result}");

            Log::info('DailyJob: Successfully completed handle method.');
        } catch (Exception $e) {
            Log::error('DailyJob: Failed during handle method - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Perform the specified operation on the numbers.
     *
     * @return int|float Result of the operation
     * @throws Exception
     */
    protected function performOperation(int $num1, int $num2, string $operation)
    {
        switch ($operation) {
            case 'add':
                return $num1 + $num2;
            case 'multiply':
                return $num1 * $num2;
            default:
                throw new Exception("Invalid operation '{$operation}'");
        }
    }
}
