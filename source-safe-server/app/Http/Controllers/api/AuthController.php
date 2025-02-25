<?php

namespace App\Http\Controllers\api;

use App\Events\FileBackupEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6'
        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create(attributes: $input);
        // $plainTextToken = $user->createToken($request->name, ['user'])->plainTextToken;
        $plainTextToken = $user->createToken($request->name, ["role:user"])->plainTextToken;
        return response()->json([
            "status" => true,
            "message" => "User registered successfully",
            "data" => $user,
            "plainTextToken" => $plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);
        $user = User::where("name", $request->name)->first();
        if (isset($user)) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->isAdmin == true) {
                    $plainTextToken = $user->createToken($user->name, ["role:admin"])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "message" => "Admin logged in successfully",
                        "data" => $user,
                        "plainTextToken" => $plainTextToken
                    ]);
                } else {
                    $plainTextToken = $user->createToken($user->name, ["role:user"])->plainTextToken;
                    return response()->json([
                        "status" => true,
                        "message" => "User logged in successfully",
                        "data" => $user,
                        "plainTextToken" => $plainTextToken
                    ]);
                }
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Password is incorrect",
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Email not found",
            ]);
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'user logged out successfully',
        ]);
    }
}
