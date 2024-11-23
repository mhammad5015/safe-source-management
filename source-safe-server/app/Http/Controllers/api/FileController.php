<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\File;
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
        return response()->json($response);
    }

    public function check_in($group_id, $file_id)
    {
        $user = auth()->user();
        $file = File::find($file_id);
        if (!$file) {
            return response()->json([
                'status' => false,
                'message' => 'file not found',
            ]);
        }
        if ($file->group_id !== $group_id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action',
            ], 403);
        }
        if ($file->isAvailable == false && $file->user_id == $user->id) {
            return response()->json([
                'status' => true,
                'message' => 'you have already downloaded the file',
                'data' => $file,
            ]);
        }
        if ($file->isAvailable == true) {
            $file->isAvailable = false;
            $file->save();
            return response()->json([
                'status' => true,
                'data' => $file,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'the file is not free to edit',
            ]);
        }
    }
    public function check_out(Request $request, $group_id, $file_id)
    {
        $validator = $request->validate([
            'filePath' => 'required|file|mimes:doc,docx,xls,xlsx',
        ]);
        DB::beginTransaction();
        try {
            $file = File::find($file_id);
            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'file not found',
                ]);
            }
            if ($file->group_id != $group_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to perform this action',
                ], 403);
            }
            if ($file->isAvailable == false) {
                $file->isAvailable = true;
                $relativePath = str_replace('storage/', '', $file->filePath);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
                $uniqueFilename = $file->group_id . '_' . time() . '_' . $request->filePath->getClientOriginalName();
                $file->filePath = 'storage/' . $request->filePath->storeAs('files', $uniqueFilename, 'public');
                $file->save();
                DB::commit();
                return response()->json([
                    'status' => true,
                    'data' => $file,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'you cannot check_out, you have already checked out',
                ]);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage(),
            ], 500);
        }
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
