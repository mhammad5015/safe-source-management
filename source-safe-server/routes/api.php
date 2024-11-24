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
Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);


// Users
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);
    Route::put('/blockUser/{user_id}', [UserController::class, 'blockUser']);
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
    Route::post('/group/uploadNewFile/{group_id}', [FileController::class, 'uploadNewFile'])->middleware('isGroupMember');
    Route::put('/group/{group_id}/file/check_in/{file_id}', [FileController::class, 'check_in'])->middleware('isGroupMember');
    Route::post('/group/{group_id}/file/check_out/{file_id}', [FileController::class, 'check_out'])->middleware('isGroupMember');
    Route::get('/getOwnerRequests/{group_id}', [FileController::class, 'getOwnerRequests'])->middleware("isOwner");
    Route::get('/getUserFiles', [FileController::class, 'getUserFiles']);
});
Route::get('/user/getGroupFiles/{group_id}', [FileController::class, 'getGroupFiles'])->middleware(['auth:sanctum', 'isgroupMemberOrAdmin', 'isNotBlocked']);
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/getAllFiles', [FileController::class, 'getAllFiles']);
    Route::get('/getUserFilesById/{user_id}', [FileController::class, 'getUserFilesById']);
});
