<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\CheckRole;

Route::post('login',    [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Ambil user
    Route::get('user', function (Request $req) {
        return $req->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin-only Courses CRUD
    Route::post('courses',    [CourseController::class, 'store'])
        ->middleware(CheckRole::class . ':admin');
    Route::get('courses',    [CourseController::class, 'index'])
        ->middleware(CheckRole::class . ':admin');
    Route::get('courses/{course}', [CourseController::class, 'show'])
        ->middleware(CheckRole::class . ':admin');
    Route::patch('courses/{course}', [CourseController::class, 'update'])
        ->middleware(CheckRole::class . ':admin');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])
        ->middleware(CheckRole::class . ':admin');

    // Teacher & Admin can list students
    Route::get('courses/{course}/students', [CourseController::class, 'students'])
        ->middleware(CheckRole::class . ':admin,teacher');

    // Student enroll
    Route::post('enrollments',    [EnrollmentController::class, 'store'])
        ->middleware(CheckRole::class . ':student');
    Route::get('my-courses',     [EnrollmentController::class, 'myCourses'])
        ->middleware(CheckRole::class . ':student');
});
