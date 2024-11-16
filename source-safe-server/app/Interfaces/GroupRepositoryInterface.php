<?php

namespace App\Interfaces;

interface GroupRepositoryInterface
{
    public function createGroup(array $data);
    public function addGroupMember(array $data);
}
