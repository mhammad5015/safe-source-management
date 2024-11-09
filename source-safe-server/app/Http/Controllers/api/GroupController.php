<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class GroupController extends Controller
{
    //
    public function createGroup(Request $request)
    {
        $validator = $request->validate([
            'groupName' => 'required',
            'groupImage' => 'required|image|mimes:jpeg,png,gif,bmp,jpg,svg',
        ]);
        $user_id = auth()->user()->id;
        $input = $request->all();
        $input['groupImage'] = 'storage/' . $input['groupImage']->store('groupImages', 'public');
        $input['owner_id'] = $user_id;
        $group = Group::create($input);
        $groupMember = GroupMember::create([
            'user_id' => $user_id,
            'group_id' => $group->id,
            'isOwner' => true,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Group created successfully',
            'data' => $group,
        ]);
    }

    public function addGroupMembers(Request $request, $group_id)
    {
        $validator = $request->validate([
            'emails' => 'required|array',
        ]);
        return DB::transaction(function () use ($request, $group_id) {
            $notFoundMembers = [];
            $foundMembers = [];
            $alreadyExists = [];
            foreach ($request->emails as $email) {
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
                    $groupMember = GroupMember::create(attributes: [
                        'user_id' => $member->id,
                        'group_id' => $group_id,
                        'isOwner' => false,
                    ]);
                }
            }
            if (count($notFoundMembers) === count($request->emails)) {
                return response()->json([
                    'status' => false,
                    'message' => 'all the emails you entered are not found',
                    'notFoundMembers' => $notFoundMembers,
                ]);
            }
            if (count($alreadyExists) === count($request->emails)) {
                return response()->json([
                    'status' => false,
                    'message' => 'all the emails you entered are already exists',
                    'alreadyExists' => $alreadyExists,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Users added successfully to the group',
                    'addedMembers' => $foundMembers,
                    'notFoundMembers' => $notFoundMembers,
                    'alreadyExists' => $alreadyExists,
                ]);
            }
        });
    }

    public function getAllUserGroupsById($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => true,
                'message' => 'user not found',
            ]);
        }
        $userGroups = $user->groups;
        return response()->json([
            'status' => true,
            'data' => $userGroups,
        ]);
    }

    public function getAllUserGroups()
    {
        $user = auth()->user()->load('groups');
        return response()->json([
            'status' => true,
            'data' => $user->groups,
        ]);
    }

    public function getAllGroups()
    {
        $groups = Group::all();
        return response()->json([
            'status' => true,
            'data' => $groups,
        ]);
    }

    public function deleteGroup($group_id)
    {
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => "Group not found"
            ]);
        }
        $group->delete();
        return response()->json([
            'status' => true,
            'message' => "Group deleted successfully"
        ]);
    }
}
