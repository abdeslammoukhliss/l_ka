<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/first', function () {
    return view('first');
});
 
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     return $request;
//     $request->fulfill();
 
//     return view('first');
// })->middleware('signed')->name('verification.verify');

// verify link
Route::get('/verify_email/{id}/{token}', [AuthController::class,'verifyEmail'])->name('user.verify');

Route::get('/new_password_form/{token}',function(String $token){
    return view('new_password_form')->with(['message'=>'','token'=>$token]);
})->name('new_password_form');

Route::post('/new_password',[AuthController::class,'newPassword'])->name('new_password');

// Route::get('/reset_password', function () {
//     return view('reset_password')->with(['message'=>'','token'=>'fje']);
// })->name('reset_password');
