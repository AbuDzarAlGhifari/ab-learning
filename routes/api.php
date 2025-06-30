<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizStatsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\EssayGradingController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsurePaymentVerified;

Route::post('login',    [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // --- AUTH ---
    Route::get('user',    fn(Request $req) => $req->user());
    Route::post('logout', [AuthController::class, 'logout']);

    // --- NOTIFICATIONS ---
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    // --- COURSES (Admin) ---
    Route::middleware(CheckRole::class . ':admin')->group(function () {
        Route::apiResource('courses', CourseController::class);
    });

    // --- SCHEDULES ---
    Route::post('schedules',    [ScheduleController::class, 'store'])
        ->middleware(CheckRole::class . ':teacher');
    Route::get('schedules',     [ScheduleController::class, 'index'])
        ->middleware(CheckRole::class . ':teacher');

    // --- ENROLLMENT & PAYMENTS & STUDENT-ONLY ROUTES ---
    Route::post('enrollments',                            [EnrollmentController::class, 'store'])
        ->middleware(CheckRole::class . ':student');
    Route::post('enrollments/{enrollment}/pay',           [PaymentController::class,    'store'])
        ->middleware(CheckRole::class . ':student');
    Route::get('my-payments',                             [PaymentController::class,    'myPayments'])
        ->middleware(CheckRole::class . ':student');

    Route::middleware(CheckRole::class . ':admin,finance')->group(function () {
        Route::get('payments',                           [PaymentController::class, 'index']);
        Route::patch('payments/{payment}/confirm',       [PaymentController::class, 'confirm']);
        Route::patch('payments/{payment}/reject',        [PaymentController::class, 'reject']);
    });

    Route::middleware([
        CheckRole::class . ':student',
        EnsurePaymentVerified::class,
    ])->group(function () {
        // My Courses & Schedule
        Route::get('my-courses',                         [EnrollmentController::class, 'myCourses']);
        Route::get('schedules/me',                       [ScheduleController::class,   'forStudent']);

        // Materials
        Route::get('courses/{course}/materials',         [MaterialController::class,   'index']);

        // Quiz (Student)
        Route::get('available-quizzes',                  [StudentQuizController::class, 'available']);
        Route::get('quizzes/{quiz}/start',               [StudentQuizController::class, 'start']);
        Route::post('quizzes/{quiz}/submit',             [StudentQuizController::class, 'submit']);
        Route::get('quiz-attempts/{attempt}',            [StudentQuizController::class, 'result']);
    });

    // --- MATERIALS (Teacher) ---
    Route::middleware(CheckRole::class . ':teacher')->group(function () {
        Route::post('courses/{course}/materials',        [MaterialController::class, 'store']);
        Route::patch('materials/{material}',             [MaterialController::class, 'update']);
        Route::delete('materials/{material}',            [MaterialController::class, 'destroy']);
    });

    // --- QUIZ & QUESTIONS (Teacher) ---
    Route::prefix('quizzes')->middleware(CheckRole::class . ':teacher')->group(function () {
        // Quiz CRUD
        Route::post('/',                               [QuizController::class,     'store']);
        Route::get('/',                                [QuizController::class,     'index']);
        Route::get('{quiz}',                           [QuizController::class,     'show']);
        Route::patch('{quiz}',                         [QuizController::class,     'update']);
        Route::delete('{quiz}',                        [QuizController::class,     'destroy']);
        // Questions
        Route::post('{quiz}/questions',                [QuestionController::class, 'store']);
        Route::patch('questions/{question}',           [QuestionController::class, 'update']);
        Route::delete('questions/{question}',          [QuestionController::class, 'destroy']);
    });

    // --- ESSAY GRADING & STATS (Admin & Teacher) ---
    Route::middleware(CheckRole::class . ':admin,teacher')->group(function () {
        // Essay grading
        Route::get('essay-answers',                    [EssayGradingController::class, 'index']);
        Route::patch('essay-answers/{answer}',         [EssayGradingController::class, 'grade']);
        // Quiz stats
        Route::get('quizzes/{quiz}/stats',             [QuizStatsController::class,    'show']);
        Route::get('courses/{course}/quizzes/stats',   [QuizStatsController::class,    'byCourse']);
        Route::get('quizzes/{quiz}/stats/export',      [QuizStatsController::class,    'export']);
        Route::get('quizzes/{quiz}/questions/stats',   [QuizStatsController::class,    'questionStats']);
    });
});
