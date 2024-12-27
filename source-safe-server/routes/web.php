<?php

use App\Events\SendNotification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sendNotification', function () {
    $file = [
        "fileName" => "file1",
        "filePath" => "storage/files/pAhjozenFzWlTuVuKVSfLtO9fs5Ii6Up2GgjV81m.docx",
        "user_id" => 2,
        "group_id" => "1",
        "originalName" => "Distributed Systems(Chord Protocol).docx",
        "approved" => true,
        "updated_at" => "2024-12-27T13:33:59.000000Z",
        "created_at" => "2024-12-27T13:33:59.000000Z",
        "id" => 1
    ];
    $user = [
        "id" => 2,
        "name" => "muhammad1",
        "email" => "mh1@gmail.com",
        "email_verified_at" => null,
        "isAdmin" => 0,
        "isBlocked" => 0,
        "created_at" => null,
        "updated_at" => null
    ];
    $fileName = "file1";
    $message = "checked in";
    $userName = 'muhammad1';
    $group_id = 1;
    event(new SendNotification($fileName, $message, $userName, $group_id));
    // sleep(5);
    // return redirect('/');
});
