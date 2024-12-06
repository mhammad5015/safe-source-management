<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function getAllUsers()
    {
        $users = User::where('isAdmin', false)->get();
        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }


    public function getAllGroupUsers($gourp_id)
    {
        $response = $this->userService->getAllGroupUsers($gourp_id);
        return response()->json($response);
    }


    public function blockUser($user_id)
    {
        $response = $this->userService->blockUser($user_id);
        return response()->json($response);
    }
}
