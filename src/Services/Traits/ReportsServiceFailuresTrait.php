<?php

namespace NinjaPortal\Portal\Services\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait ReportsServiceFailuresTrait
{
    protected function reportFailure(string $message, array $context, Exception $exception): void
    {
        Log::error($message, array_merge($context, [
            'exception' => $exception,
        ]));
    }
}
