<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class GroupController extends Controller
{
    protected $groupService;
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function createGroup(Request $request)
    {
        $validator = $request->validate([
            'groupName' => 'required',
            'groupImage' => 'required|image|mimes:jpeg,png,gif,bmp,jpg,svg',
        ]);
        $group = $this->groupService->create($request->all());
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
        $resault = $this->groupService->addGroupMember($request->all(), $group_id);
        return response()->json($resault);
    }

    public function getAllUserGroupsById($user_id)
    {
        $resault = $this->groupService->getAllUserGroups($user_id);
        return response()->json($resault);
    }

    public function getAllUserGroups()
    {
        $user = auth()->user()->load(relations: 'groups');
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
