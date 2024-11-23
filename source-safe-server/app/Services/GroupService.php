<?php

namespace App\Services;

use App\Interfaces\GroupRepositoryInterface;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GroupService
{
    protected $groupRepository;
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['groupImage'])) {
                $data['groupImage'] = 'storage/' . $data['groupImage']->store('groupImages', 'public');
            }
            $data['owner_id'] = auth()->user()->id;
            $group = $this->groupRepository->createGroup($data);
            $groupMember = $this->groupRepository->addGroupMember($data['owner_id'], $group->id, true);
            return $group;
        });
    }

    public function addGroupMember(array $data, $group_id)
    {
        return DB::transaction(function () use ($data, $group_id) {
            $notFoundMembers = [];
            $foundMembers = [];
            $alreadyExists = [];
            foreach ($data['emails'] as $email) {
                $member = User::where('email', $email)->first();
                if (!$member) {
                    array_push($notFoundMembers, $email);
                } else {
                    $isGroupMember = GroupMember::where('group_id', $group_id)->where('user_id', $member->id)->first();
                    if ($isGroupMember) {
                        array_push($alreadyExists, $email);
                        continue;
                    }
                    array_push($foundMembers, $email);
                    $groupMember = $this->groupRepository->addGroupMember($member->id, $group_id, false);
                }
            }
            if (count($notFoundMembers) === count($data['emails'])) {
                return [
                    'status' => false,
                    'message' => 'all the emails you entered are not found',
                    'notFoundMembers' => $notFoundMembers,
                ];
            }
            if (count($alreadyExists) === count($data['emails'])) {
                return [
                    'status' => false,
                    'message' => 'all the emails you entered are already exists',
                    'alreadyExists' => $alreadyExists,
                ];
            } else {
                return [
                    'status' => true,
                    'message' => 'Users added successfully to the group',
                    'addedMembers' => $foundMembers,
                    'notFoundMembers' => $notFoundMembers,
                    'alreadyExists' => $alreadyExists,
                ];
            }
        });
    }

    public function getAllUserGroups($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found',
            ];
        }
        return [
            'status' => true,
            'data' => $user->groups,
        ];
    }
}
