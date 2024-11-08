<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function getAllUsers(){
        $users = User::where('isAdmin', false)->get();
        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }
}
