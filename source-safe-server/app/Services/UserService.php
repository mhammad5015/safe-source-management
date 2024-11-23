<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\GroupMember;
use App\Models\User;

class UserService
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllGroupUsers($group_id)
    {
        $groupMembers = $this->userRepository->getAllGroupUsers($group_id);
        if (!$groupMembers) {
            return [
                'status' => false,
                'message' => 'there is no group with that id'
            ];
        } else {
            return [
                'status' => true,
                'data' => $groupMembers
            ];
        }
    }

    public function blockUser($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return [
                'status' => false,
                'message' => 'there is no user with that id'
            ];
        }
        $user['isBlocked'] = !$user['isBlocked'];
        $user->save();
        if ($user['isBlocked'] == 1) {
            return [
                'status' => true,
                'message' => 'user blocked successfully'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'user unblocked successfully'
            ];
        }
    }
}
