<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllGroupUsers($group_id);
}
