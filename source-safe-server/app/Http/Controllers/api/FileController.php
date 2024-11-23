<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Group;
use App\Models\RequestApproval;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;

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
        $response = $this->fileService->uploadNewFile($request->all(), $group_id);
        return response()->json($response);
    }
    public function getOwnerRequests($group_id)
    {
        $reqs = RequestApproval::where('owner_id', auth()->user()->id)->get();
        $reqs->map(function ($req) {
            return $req->file->group;
        });
        return response()->json([
            'status' => true,
            'data' => $reqs
        ]);
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
}
