<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\GroupMember;

class UserRepository implements UserRepositoryInterface
{
    public function getAllGroupUsers($group_id)
    {
        $groupMembers = GroupMember::where('group_id', $group_id)->get();
        return $groupMembers->map(function ($member) {
            return $member->user;
        })->toArray();
    }
}
