<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SessionController;
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
Route::get('/get_statistics',[UserController::class,'getStatistics']);
Route::post('/change_group',[UserController::class,'changeGroup']);
Route::get('/get_teachers',[UserController::class,'getTeachers']);
Route::get('/get_students',[UserController::class,'getStudents']);
Route::put('/edit_profile', [UserController::class,'editProfile']);
Route::put('/change_password', [UserController::class,'changePassword']);

// course APIs //////////////////////////////////////////////////////
Route::post('/add_course',[CourseController::class,'addCourse']);
Route::post('/add_course_only',[CourseController::class,'addCourseOnly']);
Route::get('/get_courses',[CourseController::class,'getCourses']);
Route::get('/get_courses_with_details',[CourseController::class,'getCoursesWithDetails']);
Route::get('/get_teacher_courses/{teacher}',[CourseController::class,'getTeacherCourses']);
Route::get('/get_student_courses/{student}',[CourseController::class,'getStudentCourses']);
Route::get('/get_course_details/{id}',[CourseController::class,'getCourseDetails']);
Route::get('/get_course_details_for_student/{id}/{student}',[CourseController::class,'getCourseDetailsForStudent']);
Route::post('/enroll',[CourseController::class,'enroll']);
Route::put('/edit_course',[CourseController::class,'editCourse']);

// category APIs ///////////////////////////////////////////////////
Route::get('/get_categories',[CategoryController::class,'getCategories']);

// module APIs ///////////////////////////////////////////////////
Route::get('/get_all_modules',[ModuleController::class,'getAllModules']);
Route::get('/get_module_chapters/{course}',[ModuleController::class,'getModuleChapters']);
Route::post('/add_module',[ModuleController::class,'addModule']);
Route::put('/edit_module',[ModuleController::class,'editModule']);
Route::delete('/delete_module',[ModuleController::class,'deleteModule']);

// chapter APIs ///////////////////////////////////////////////////
Route::get('/get_all_chapters',[ChapterController::class,'getAllChapters']);
Route::post('/add_chapter',[ChapterController::class,'addChapter']);
Route::put('/edit_chapter',[ChapterController::class,'editChapter']);
Route::delete('/delete_chapter',[ChapterController::class,'deleteChapter']);

// project APIs ////////////////////////////////////////////////////////
Route::post('/assign_project', [ProjectController::class,'assignProject']);
Route::get('/get_student_projects/{student}', [ProjectController::class,'getStudentProjects']);
Route::get('/get_course_projects/{course}', [ProjectController::class,'getCourseProjects']);
Route::get('/get_all_projects', [ProjectController::class,'getAllProjects']);
Route::get('/get_teacher_projects/{teacher}/{course}', [ProjectController::class,'getTeacherProjects']);
Route::post('/add_project',[ProjectController::class,'addProject']);
Route::put('/edit_project',[ProjectController::class,'editProject']);
Route::delete('/delete_project',[ProjectController::class,'deleteProject']);

// group APIs ////////////////////////////////////////////////////////
Route::get('/get_course_groups/{course}',[GroupController::class,'getCourseGroups']);
Route::get('/get_all_groups',[GroupController::class,'getAllGroups']);
Route::post('/add_group',[GroupController::class,'addGroup']);
Route::put('/edit_group',[GroupController::class,'editGroup']);
Route::delete('/delete_group',[GroupController::class,'deleteGroup']);

// session APIs ////////////////////////////////////////////////////////
Route::post('/add_session',[SessionController::class,'addSession']);
Route::post('/add_presence',[SessionController::class,'addPresence']);
Route::get('/get_sessions',[SessionController::class,'getSessions']);
Route::get('/get_student_sessions/{student_id}',[SessionController::class,'getStudentSessions']);
Route::get('/get_sessions_by_date/{date}',[SessionController::class,'getSessionsByDate']);

// consultation APIs /////////////////////////////////////////////////////
Route::post('/add_consultation',[ConsultationController::class,'addConsultation']);
Route::post('/edit_consultation',[ConsultationController::class,'editConsultation']);
Route::get('/get_consultations',[ConsultationController::class,'getConsultations']);
Route::get('/get_student_consultations/{student}',[ConsultationController::class,'getStudentConsultations']);

// payment APIs /////////////////////////////////////////////////////
Route::post('/add_payment',[PaymentController::class,'addPayment']);
Route::get('/get_payments',[PaymentController::class,'getPayments']);
Route::get('/get_student_rest/{student_id}',[PaymentController::class,'getStudentRest']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

