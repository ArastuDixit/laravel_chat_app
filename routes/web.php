<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;

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

Route::get('/login', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'userLogin'])->name('login');
Route::get('/registration', [UserController::class, 'registration'])->name('registration');
Route::post('/registration', [UserController::class, 'userRegistration'])->name('registration');

Route::get('/sign-out', [UserController::class, 'signOut'])->name('signout');


Route::middleware('auth')->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages');
    Route::post('sendMessage', [MessageController::class, 'sendMessage'])->name('sendMessage');
    Route::post('getMessages', [MessageController::class, 'getMessages'])->name('getMessages');

});
