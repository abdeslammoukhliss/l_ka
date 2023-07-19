<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
// test APIs /////////////////////////////////////////////////////
Route::get('/get_users', [TestController::class,'getUsers']);

Route::post('/general_test', [TestController::class,'tester']);

// auth APIs //////////////////////////////////////////////////////
Route::post('/add_user',[AuthController::class,'addUser']);
Route::post('/sign_in',[AuthController::class,'signIn']);
Route::post('/forgot_password', [AuthController::class,'sendResetPasswordLink']);

// user APIs //////////////////////////////////////////////////////
// get the statistics of admin dashboard
Route::get('/get_statistics',[UserController::class,'getStatistics']);
Route::post('/change_group',[UserController::class,'changeGroup']);
Route::get('/get_teachers',[UserController::class,'getTeachers']);
Route::get('/get_students',[UserController::class,'getStudents']);

// course APIs //////////////////////////////////////////////////////
Route::post('/add_course',[CourseController::class,'addCourse']);
Route::get('/get_courses',[CourseController::class,'getCourses']);
Route::get('/get_teacher_courses/{teacher}',[CourseController::class,'getTeacherCourses']);
Route::get('/get_student_courses/{student}',[CourseController::class,'getStudentCourses']);
Route::get('/get_course_details/{id}',[CourseController::class,'getCourseDetails']);
Route::get('/get_course_details_for_student/{id}/{student}',[CourseController::class,'getCourseDetailsForStudent']);
// if the user want to register in a course
Route::post('/enroll',[CourseController::class,'enroll']);

// category APIs ///////////////////////////////////////////////////
// get all categories
Route::get('/get_categories',[CategoryController::class,'getCategories']);


// project APIs ////////////////////////////////////////////////////////
// assign a student to a project
Route::post('/assign_project', [ProjectController::class,'assignProject']);
// get the projects of a student
Route::get('/get_student_projects/{student}', [ProjectController::class,'getStudentProjects']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

