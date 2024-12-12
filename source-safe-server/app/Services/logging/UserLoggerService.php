<?php

namespace App\Services\logging;

use App\Models\FileLog;
use App\Models\UserLog;
use Illuminate\Support\Facades\Log;

class UserLoggerService
{
    public static function logForUser($user_id, $group_id, $message, $context = [])
    {
        $userLog = UserLog::where('user_id', $user_id)->where('group_id', $group_id)->first();
        if ($userLog) {
            $logPath = public_path($userLog->logPath);
        } else {
            $logPath = public_path("storage/logs/users/group$group_id/user$user_id.log");
            UserLog::create([
                'user_id' => $user_id,
                'group_id' => $group_id,
                'logPath' => "storage/logs/users/group$group_id/user$user_id.log",
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
