<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/users', function (Request $request) {
    return DB::select('select * from users');
})->middleware('verified');

Route::post('/add_user',[AuthController::class,'addUser']);
Route::post('/sign_in',[AuthController::class,'signIn']);

Route::post('/add_course',[CourseController::class,'addCourse']);
Route::get('/get_courses',[CourseController::class,'getCourses']);
Route::get('/get_course_details/{id}',[CourseController::class,'getCourseDetails']);
Route::post('/enroll',[CourseController::class,'enroll']);

Route::post('/general_test', [TestController::class,'tester']);

Route::post('/forgot_password', [AuthController::class,'sendResetPasswordLink']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

