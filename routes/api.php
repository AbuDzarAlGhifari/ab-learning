<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EssayGradingController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizStatsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentQuizController;
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

    // Schedule
    Route::post('schedules', [ScheduleController::class, 'store'])
        ->middleware(CheckRole::class . ':teacher');
    Route::get('schedules', [ScheduleController::class, 'index'])
        ->middleware(CheckRole::class . ':teacher');
    Route::get('schedules/me', [ScheduleController::class, 'forStudent'])
        ->middleware(CheckRole::class . ':student');

    // Enrollment
    Route::post('enrollments',    [EnrollmentController::class, 'store'])
        ->middleware(CheckRole::class . ':student');
    Route::get('my-courses',     [EnrollmentController::class, 'myCourses'])
        ->middleware(CheckRole::class . ':student');

    // Materials
    Route::get('courses/{course}/materials', [MaterialController::class, 'index'])
        ->middleware([CheckRole::class . ':teacher,student']);
    Route::post('courses/{course}/materials', [MaterialController::class, 'store'])
        ->middleware([CheckRole::class . ':teacher']);
    Route::patch('materials/{material}',       [MaterialController::class, 'update'])
        ->middleware([CheckRole::class . ':teacher']);
    Route::delete('materials/{material}',       [MaterialController::class, 'destroy'])
        ->middleware([CheckRole::class . ':teacher']);

    // Quizzes
    Route::prefix('quizzes')->middleware(CheckRole::class . ':teacher')->group(function () {
        Route::post('/', [QuizController::class, 'store']);
        Route::get('/', [QuizController::class, 'index']);
        Route::get('{quiz}', [QuizController::class, 'show']);
        Route::patch('{quiz}', [QuizController::class, 'update']);
        Route::delete('{quiz}', [QuizController::class, 'destroy']);

        // Questions
        Route::post('{quiz}/questions', [QuestionController::class, 'store']);
        Route::patch('questions/{question}', [QuestionController::class, 'update']);
        Route::delete('questions/{question}', [QuestionController::class, 'destroy']);
    });

    Route::middleware(CheckRole::class . ':student')->group(function () {
        Route::get('quizzes/{quiz}/start', [StudentQuizController::class, 'start']);
        Route::post('quizzes/{quiz}/submit', [StudentQuizController::class, 'submit']);
        Route::get('quiz-attempts/{attempt}', [StudentQuizController::class, 'result']);
        Route::get('available-quizzes', [StudentQuizController::class, 'available']);
    });

    Route::middleware(CheckRole::class . ':admin,teacher')->group(function () {
        Route::get('essay-answers', [EssayGradingController::class, 'index']);
        Route::patch('essay-answers/{answer}', [EssayGradingController::class, 'grade']);
        Route::get('quizzes/{quiz}/stats', [QuizStatsController::class, 'show']);
        Route::get('courses/{course}/quizzes/stats', [QuizStatsController::class, 'byCourse']);
        Route::get('quizzes/{quiz}/stats/export', [QuizStatsController::class, 'export']);
        Route::get('quizzes/{quiz}/questions/stats', [QuizStatsController::class, 'questionStats']);
    });
});
