<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\CheckRole;

Route::post('login',    [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
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

    // CREATE schedule (Teacher)
    Route::post('schedules', [ScheduleController::class, 'store'])
        ->middleware(CheckRole::class . ':teacher');

    // READ schedules (Teacher)
    Route::get('schedules', [ScheduleController::class, 'index'])
        ->middleware(CheckRole::class . ':teacher');

    // READ schedules for student
    Route::get('schedules/me', [ScheduleController::class, 'forStudent'])
        ->middleware(CheckRole::class . ':student');

    // Student enroll
    Route::post('enrollments',    [EnrollmentController::class, 'store'])
        ->middleware(CheckRole::class . ':student');
    Route::get('my-courses',     [EnrollmentController::class, 'myCourses'])
        ->middleware(CheckRole::class . ':student');


    // Teacher: manage materials per course
    Route::get('courses/{course}/materials', [MaterialController::class, 'index'])
        ->middleware([CheckRole::class . ':teacher']);
    Route::post('courses/{course}/materials', [MaterialController::class, 'store'])
        ->middleware([CheckRole::class . ':teacher']);
    Route::patch('materials/{material}',       [MaterialController::class, 'update'])
        ->middleware([CheckRole::class . ':teacher']);
    Route::delete('materials/{material}',       [MaterialController::class, 'destroy'])
        ->middleware([CheckRole::class . ':teacher']);

    // Student: view materials
    Route::get('courses/{course}/materials', [MaterialController::class, 'index'])
        ->middleware([CheckRole::class . ':student']);
});
