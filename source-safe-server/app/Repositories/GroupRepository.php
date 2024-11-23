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
    // public function addGroupMember(array $data)
    // {
    //     return GroupMember::create($data);
    // }
    public function addGroupMember($user_id, $group_id, $isAdmin)
    {
        return GroupMember::create([
            'user_id' => $user_id,
            'group_id' => $group_id,
            'isOwner' => $isAdmin,
        ]);
    }
}
