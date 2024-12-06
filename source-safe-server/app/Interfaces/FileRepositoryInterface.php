<?php

namespace App\Interfaces;

interface FileRepositoryInterface
{
    public function createFile($data);
    public function createRequest($data);
    public function getFile($file_id);
}
