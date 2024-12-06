<?php

namespace App\Services;

use App\Interfaces\FileRepositoryInterface;
use App\Models\File;
use App\Models\Group;
use App\Models\RequestApproval;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService
{
    protected $fileRepository;


    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }


    public function uploadNewFile(array $data, $group_id)
    {
        $group = Group::find($group_id);
        if (!$group) return ['status' => false, 'message' => 'Group not found', 'statusCode' => 401];

        $data = $this->prepareData($data, $group_id);

        return auth()->id() == $group->owner_id
            ? $this->handleOwnerUpload($data)
            : $this->handleMemberUpload($data, $group);
    }


    public function check_in($group_id, $file_id)
    {
        $user = auth()->user();
        $file = $this->fileRepository->getFile($file_id);
        if (!$file) {
            return ['status' => false, 'message' => 'file not found', 'statusCode' => 400];
        }
        if ($file->group_id != $group_id) {
            return ['status' => false, 'message' => 'You are not authorized to perform this action', 'statusCode' => 403];
        }
        if ($file->isAvailable == false && $file->user_id == $user->id) {
            return [
                'status' => true,
                'message' => 'you have already downloaded the file',
                'data' => $file,
                'statusCode' => 200
            ];
        }
        if ($file->isAvailable == true) {
            $file->isAvailable = false;
            $file->user_id = $user->id;
            $file->save();
            return [
                'status' => true,
                'data' => $file,
                'statusCode' => 200
            ];
        } else {
            return [
                'status' => false,
                'message' => 'the file is not free to edit',
                'statusCode' => 400
            ];
        }
    }


    public function check_in_rollback($group_id, $file_id)
    {
        $file = $this->fileRepository->getFile($file_id);
        if (!$file) {
            return [
                'status' => false,
                'message' => 'file not found',
                'statusCode' => 400
            ];
        }
        if ($file->group_id != $group_id) {
            return [
                'status' => false,
                'message' => 'You are not authorized to perform this action',
                'statusCode' => 401
            ];
        }
        $user = auth()->user();
        if ($file->user_id != $user->id) {
            return [
                'status' => false,
                'message' => 'You are not authorized to perform this action, not checker',
                'statusCode' => 401
            ];
        }
        $file->isAvailable = true;
        $file->save();
        return [
            'status' => true,
            'message' => 'the check in canceled successfully',
            'data' => $file,
            'statusCode' => 200
        ];
    }


    public function check_out($request, $group_id, $file_id)
    {
        DB::beginTransaction();
        try {
            $file = $this->fileRepository->getFile($file_id);
            if (!$file) {
                return [
                    'status' => false,
                    'message' => 'file not found',
                    'statusCode' => 400
                ];
            }
            if ($file->group_id != $group_id) {
                return [
                    'status' => false,
                    'message' => 'You are not authorized to perform this action',
                    'statusCode' => 401
                ];
            }
            if ($file->user_id != auth()->user()->id) {
                return [
                    'status' => false,
                    'message' => 'You are not authorized to perform this action, not checker',
                    'statusCode' => 401
                ];
            }
            if ($file->isAvailable == false) {
                $uploadedFile = $request->filePath;
                $uploadedFileName = $uploadedFile->getClientOriginalName();
                $reservedFileName = pathinfo($file->filePath, PATHINFO_BASENAME);

                if ($uploadedFileName !== $reservedFileName) {
                    return [
                        'status' => false,
                        'message' => 'The uploaded file must have the same name and extension as the reserved file',
                        'statusCode' => 400
                    ];
                }

                $file->isAvailable = true;
                $this->deleteExistingFile($file->filePath);

                $file->filePath = 'storage/' . $request->filePath->store('files', 'public');
                $file->save();
                DB::commit();
                return [
                    'status' => true,
                    'data' => $file,
                    'statusCode' => 200
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'you cannot check_out, you have already checked out',
                    'statusCode' => 400
                ];
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $ex->getMessage(),
                'statusCode' => 500
            ];
        }
    }


    public function processRequest($request, $req_id)
    {
        DB::beginTransaction();
        try {
            $reqApproval = RequestApproval::find($req_id);
            if (!$reqApproval)
                return [
                    'status' => false,
                    'message' => "there is no request with this id",
                    'statusCode' => 400,
                ];

            $reqApproval->status = $request->status;
            $reqApproval->save();

            $request->status == 'approved' ?
                File::where('id', $reqApproval->file_id)->update(['approved' => true]) :
                File::where('id', $reqApproval->file_id)->update(['approved' => false]);
            DB::commit();
            return [
                'status' => true,
                'message' => "request has been $request->status successfully",
                'statusCode' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'statusCode' => 500
            ];
        }
    }


    // ****************************************************************
    // HELPER FUNCTIONS
    // ****************************************************************
    private function prepareData($data, $group_id)
    {
        $data['user_id'] = auth()->id();
        $data['group_id'] = $group_id;
        $data['filePath'] = 'storage/' . $data['filePath']->store('files', 'public');
        return $data;
    }

    private function handleOwnerUpload($data)
    {
        $data['approved'] = true;
        $file = $this->fileRepository->createFile($data);
        return [
            'status' => true,
            'message' => 'File created successfully',
            'data' => $file,
            'statusCode' => 200
        ];
    }

    private function handleMemberUpload($data, $group)
    {
        $file = $this->fileRepository->createFile($data);
        $this->fileRepository->createRequest([
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'owner_id' => $group->owner_id,
        ]);
        return [
            'status' => true,
            'message' => 'Your request was sent to the group owner.',
            'statusCode' => 200
        ];
    }

    private function deleteExistingFile($filePath)
    {
        $relativePath = str_replace('storage/', '', $filePath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    private function generateUniqueFilename($group_id, $file)
    {
        return $group_id . '_' . time() . '_' . $file->getClientOriginalName();
    }
}
