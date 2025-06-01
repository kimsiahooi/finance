<?php

namespace App\Services;

use App\Models\ErrorLog;
use Throwable;

class ErrorLoggerService
{
    public static function log(Throwable $e, $context = null, string $level = 'error'): void
    {
        $request = request();
        $location = request()->route()?->getName() ?? 'unknown';

        ErrorLog::create([
            'level'    => $level,
            'type'     => class_basename($e),
            'location' => $location,
            'message'  => $e->getMessage(),
            'trace'    => config('app.debug') ? $e->getTraceAsString() : null,
            'context'  => $context,
            'user_id' => $request->user()?->id,
            'input' => $request->all(),
        ]);
    }
}
