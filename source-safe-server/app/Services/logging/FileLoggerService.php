<?php

namespace App\Services\logging;

use App\Models\FileLog;
use Illuminate\Support\Facades\Log;

class FileLoggerService
{
    public static function logForFile($file_id, $message, $context = [])
    {
        $fileLog = FileLog::where('file_id', $file_id)->first();
        if ($fileLog) {
            $logPath = public_path($fileLog->logPath);
        } else {
            $logPath = public_path("storage/logs/files/{$file_id}.log");
            FileLog::create([
                'file_id' => $file_id,
                'logPath' => 'storage/logs/files/' . $file_id . '.log',
            ]);
        }

        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }

        $logger = Log::build([
            'driver' => 'single',
            'path' => $logPath,
            'level' => 'debug',
        ]);

        $logger->info($message, $context);
    }
}
