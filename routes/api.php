<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
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
Route::post('/forgot_password', [AuthController::class,'sendResetPasswordLink']);

Route::post('/add_course',[CourseController::class,'addCourse']);
Route::get('/get_courses',[CourseController::class,'getCourses']);
Route::get('/get_course_details/{id}',[CourseController::class,'getCourseDetails']);
Route::post('/enroll',[CourseController::class,'enroll']);

Route::get('/get_categories',[CategoryController::class,'getCategories']);

Route::post('/general_test', [TestController::class,'tester']);

Route::get('/get_statistics',[UserController::class,'getStatistics']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

