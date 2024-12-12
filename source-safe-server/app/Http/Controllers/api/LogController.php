<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FileLog;
use App\Models\UserLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function getFileLog($file_id)
    {
        $log = FileLog::where('file_id', $file_id)->first();
        if (!$log) {
            return response()->json([
                'status' => false,
                'message' => 'log not found',
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $log,
        ]);
    }


    public function getAllFilesLogs()
    {
        $logs = FileLog::all();
        return response()->json([
            'status' => true,
            'data' => $logs,
        ]);
    }


    public function getUserLog($user_id, $group_id)
    {
        $userLog = UserLog::where('user_id', $user_id)->where('group_id', $group_id)->first();
        if (!$userLog) {
            return response()->json([
                'status' => false,
                'message' => 'log not found',
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $userLog,
        ]);
    }


    public function getAllUsersLogs()
    {
        $logs = UserLog::all();
        return response()->json([
            'status' => true,
            'data' => $logs,
        ]);
    }
}
