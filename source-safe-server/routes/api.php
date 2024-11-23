<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FileController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\UserController;
use App\Http\Middleware\auth\authorization\isOwner;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication
Route::post('/user/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Users
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);
    Route::post('/blockUser/{user_id}', [UserController::class, 'blockUser']);
});
Route::middleware(['auth:sanctum', 'isgroupMemberOrAdmin'])->get('/getAllGroupUsers/{group_id}', [UserController::class, 'getAllGroupUsers']);


// Groups
Route::middleware(['auth:sanctum', 'isUser', 'isNotBlocked'])->prefix("/user")->group(function () {
    Route::post('/group/createGroup', [GroupController::class, 'createGroup']);
    Route::post('/group/addGroupMembers/{group_id}', [GroupController::class, 'addGroupMembers'])->middleware("isOwner");
    Route::get('/group/getAllUserGroups', [GroupController::class, 'getAllUserGroups']);
});
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/group/getAllGroups', [GroupController::class, 'getAllGroups']);
    Route::get('/group/getAllUserGroupsById/{user_id}', [GroupController::class, 'getAllUserGroupsById']);
    Route::delete('/group/deleteGroup/{group_id}', [GroupController::class, 'deleteGroup']);
});


// Files
Route::middleware(['auth:sanctum', 'isUser', 'isNotBlocked'])->prefix('/user')->group(function () {
    Route::get('/user/getUserFiles', [FileController::class, 'getUserFiles']);
    Route::get('/user/getAllGroupFiles', [FileController::class, 'getAllGroupFiles'])->middleware('isGroupMember');
});
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/user')->group(function () {
    Route::get('/user', [FileController::class, 'getAllFiles']);
    Route::get('/user/getUserFilesById/{user_id}', [FileController::class, 'getUserFilesById']);
});
