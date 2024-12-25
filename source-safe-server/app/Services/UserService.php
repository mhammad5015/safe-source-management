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

    public function searchForUser($search)
    {
        if ($search) {
            // Search for users by name or email
            $users = User::where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->get(['id', 'name', 'email']);

            // Check if any users were found
            if ($users->isNotEmpty()) {
                return [
                    'data' => ['users' => $users],
                    'statusCode' => 200,
                ];
            }
        }

        // Return a 422 response if no users are found
        return [
            'message' => 'No users found with the provided name or email.',
            'data' => [],
            'statusCode' => 422,
        ];
        // $user = User::query();
        // if ($search) {
        //     if ($user->where('email', 'like', '%' . $search . '%')->exists() && $user->where('name', 'like', '%' . $search . '%')->exists()) {
        //         return response()->json([
        //             'data' => ['users' => $user->get(['id', 'name'])]
        //         ]);
        //     } else if ($user->where('email', 'like', '%' . $search . '%')->exists()) {
        //         return response()->json([
        //             'data' => ['users' => [], 'consults' => $user->get()]
        //         ]);
        //     } else if ($user->where('name', 'like', '%' . $search . '%')->exists()) {
        //         return response()->json([
        //             'data' => ['users' => $user->get(['id', 'name'])]
        //         ]);
        //     }
        // }

        // return response()->json([
        //     'message' => 'There Is No Expert or user With This Name',
        //     'data' => []
        // ], 422);
    }
}
