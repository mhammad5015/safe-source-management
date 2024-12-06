<?php

namespace App\Repositories;

use App\Interfaces\FileRepositoryInterface;
use App\Models\File;
use App\Models\RequestApproval;

class FileRepository implements FileRepositoryInterface
{
    public function createFile($data)
    {
        return File::create($data);
    }

    public function createRequest($data)
    {
        return RequestApproval::create($data);
    }
    public function getFile($file_id)
    {
        return File::find($file_id);
    }
}
