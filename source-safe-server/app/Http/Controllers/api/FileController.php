<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileBackup;
use App\Models\Group;
use App\Models\RequestApproval;
use App\Models\User;
use App\Services\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function PHPSTORM_META\map;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }


    public function uploadNewFile(Request $request, $group_id)
    {
        $validation = $request->validate([
            'fileName' => 'required',
            'filePath' => 'required|file|mimes:doc,docx,xls,xlsx',
        ]);
        $response = $this->fileService->uploadNewFile($request->all(), group_id: $group_id);
        return response()->json($response, $response['statusCode']);
    }


    public function check_in($group_id, $file_id)
    {
        $response = $this->fileService->check_in($group_id, $file_id);
        return response()->json($response, $response['statusCode']);
    }


    public function check_in_rollback($group_id, $file_id)
    {
        $response = $this->fileService->check_in_rollback($group_id, $file_id);
        return response()->json($response, $response['statusCode']);
    }

    public function check_out(Request $request, $group_id, $file_id)
    {
        $validator = $request->validate([
            'filePath' => 'required|file|mimes:doc,docx,xls,xlsx',
        ]);
        $response = $this->fileService->check_out($request, $group_id, $file_id);
        return response()->json($response, $response['statusCode']);
    }


    public function getOwnerRequests($group_id)
    {
        $requests = RequestApproval::where('owner_id', auth()->user()->id)
            ->with(['file.group', 'user']) // Eager load the related file, group, and user
            ->get();
        return response()->json([
            'status' => true,
            'data' => $requests,
        ]);
    }


    public function processRequest(Request $request, $group_id, $req_id)
    {
        $validate = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        $response = $this->fileService->processRequest($request, $req_id);
        return response()->json($response, $response['statusCode']);
    }


    public function getGroupFiles($group_id)
    {
        $group = Group::with('files')->find($group_id);
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => 'group not found',
            ]);
        }
        $approvedFiles = $group->files->where('approved', true);
        return response()->json([
            'status' => true,
            'data' => $approvedFiles
        ]);
    }


    public function getUserFiles()
    {
        $user = auth()->user();
        return response()->json([
            'status' => true,
            'data' => $user->files
        ]);
    }


    public function getUserFilesById($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $user->files
        ]);
    }


    public function getAllFiles()
    {
        $files = File::get();
        return response()->json([
            'status' => true,
            'data' => $files
        ]);
    }

    public function deleteFile($group_id, $file_id)
    {
        $response = $this->fileService->deleteFile($file_id);
        return response()->json($response, $response['statusCode']);
    }

    public function getFileVersions($group_id, $file_id)
    {
        $files = FileBackup::where("file_id", $file_id)->get();
        if ($files->isEmpty()) {
            return response()->json([
                "status" => false,
                "message" => "File not found, No versions found for this file"
            ]);
        }
        return response()->json([
            "status" => true,
            "data" => $files
        ]);
    }
}
