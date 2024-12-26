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
    // event(new SendNotification([
    //     "id" => 1,
    //     "fileName" => "name"
    // ], 1));
    event(new SendNotification('hello world'));

    return view('welcome');
});

Route::get('/sendNotification', function () {
    event(new SendNotification('hello world'));
});
