<?php

namespace App\Interfaces;

interface GroupRepositoryInterface
{
    public function createGroup(array $data);
    // public function addGroupMember(array $data);
    public function addGroupMember($user_id, $gourp_id, $isAdmin);
}
