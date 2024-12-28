<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FileController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\LogController;
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
Route::middleware(['auth:sanctum'])->post('/user/search', [UserController::class, 'search']);


// Groups
Route::middleware(['auth:sanctum', 'isUser', 'isNotBlocked'])->prefix("/user")->group(function () {
    Route::post('/group/createGroup', [GroupController::class, 'createGroup']);
    Route::post('/group/{group_id}/addGroupMembers', [GroupController::class, 'addGroupMembers'])->middleware("isOwner");
    Route::get('/group/getAllUserGroups', [GroupController::class, 'getAllUserGroups']);
});
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/group/getAllGroups', [GroupController::class, 'getAllGroups']);
    Route::get('/group/getAllUserGroupsById/{user_id}', [GroupController::class, 'getAllUserGroupsById']);
    Route::delete('/group/deleteGroup/{group_id}', [GroupController::class, 'deleteGroup']);
});


// Files
Route::middleware(['auth:sanctum', 'isUser', 'isNotBlocked'])->prefix('/user')->group(function () {
    Route::post('/group/{group_id}/uploadNewFile', [FileController::class, 'uploadNewFile'])->middleware('isGroupMember');
    Route::put('/group/{group_id}/file/{file_id}/check_in', [FileController::class, 'check_in'])->middleware('isGroupMember');
    Route::put('/group/{group_id}/file/{file_id}/check_in_rollback', [FileController::class, 'check_in_rollback'])->middleware('isGroupMember');
    Route::post('/group/{group_id}/file/{file_id}/check_out', [FileController::class, 'check_out'])->middleware('isGroupMember');
    Route::get('/getOwnerRequests/{group_id}', [FileController::class, 'getOwnerRequests'])->middleware("isOwner");
    Route::put('/group/{group_id}/processRequest/{req_id}', [FileController::class, 'processRequest'])->middleware("isOwner");
    Route::get('/getUserFiles', [FileController::class, 'getUserFiles']);
});
Route::get('/user/getGroupFiles/{group_id}', [FileController::class, 'getGroupFiles'])->middleware(['auth:sanctum', 'isgroupMemberOrAdmin', 'isNotBlocked']);
Route::delete('/user/group/{group_id}/deleteFile/{file_id}', [FileController::class, 'deleteFile'])->middleware(['auth:sanctum', 'isOwner', 'isNotBlocked']);
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin')->group(function () {
    Route::get('/getAllFiles', [FileController::class, 'getAllFiles']);
    Route::get('/getUserFilesById/{user_id}', [FileController::class, 'getUserFilesById']);
});


// Logs
Route::middleware(['auth:sanctum', 'isNotBlocked'])->group(function () {
    Route::get('/file/{file_id}/getFileLog', [LogController::class, 'getFileLog']);
    Route::get('/file/getAllFilesLogs', [LogController::class, 'getAllFilesLogs'])->middleware('isAdmin');

    Route::get('/user/{user_id}/group/{group_id}/getUserLog', [LogController::class, 'getUserLog'])->middleware('isOwner');
    Route::get('/users/logs/getAllUsersLogs', [LogController::class, 'getAllUsersLogs'])->middleware('isAdmin');
});
