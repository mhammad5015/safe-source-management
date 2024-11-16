<?php

namespace App\Repositories;

use App\Interfaces\GroupRepositoryInterface;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;

class GroupRepository implements GroupRepositoryInterface
{
    public function createGroup(array $data)
    {
        return Group::create($data);
    }
    public function addGroupMember(array $data)
    {
        return GroupMember::create($data);
    }
}
