<?php

namespace App\Services;

use App\Interfaces\FileRepositoryInterface;
use App\Models\File;
use App\Models\Group;
use App\Models\RequestApproval;

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
        if (!$group) {
            return [
                'status' => false,
                'message' => 'there is no group with this id ',
            ];
        }
        $user_id = auth()->user()->id;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_id;
        $uniqueFilename = $group_id . '_' . time() . '_' . $data['filePath']->getClientOriginalName();
        $data['filePath'] = 'storage/' . $data['filePath']->storeAs('files', $uniqueFilename, 'public');
        if ($user_id == $group->owner_id) {
            // is owner
            $data['approved'] = true;
            $file = File::create(attributes: $data);
            return [
                'status' => true,
                'message' => 'File created successfully',
                'data' => $file
            ];
        } else {
            // is member
            $file = File::create($data);
            $req = RequestApproval::create([
                'file_id' => $file->id,
                'user_id' => $user_id,
                'owner_id' => $group->owner_id,
            ]);
            return [
                'status' => true,
                'message' => 'your request was successfuly sent to the group owner'
            ];
        }
    }
}
