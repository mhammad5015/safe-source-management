<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FileLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function getFileLog($file_id)
    {
        $log = FileLog::where('file_id', $file_id)->first();
        if (!$log) {
            return response()->json([
                'status' => false,
                'message' => 'file, log not found',
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $log,
        ]);
    }


    public function getAllFileLogs()
    {
        $log = FileLog::all();
        return response()->json([
            'status' => true,
            'data' => $log,
        ]);
    }
}
