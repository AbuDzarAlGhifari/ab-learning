<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        $user = $req->user();
        $role = $user->role->name;

        return match ($role) {
            'admin'   => $this->adminStats(),
            'teacher' => $this->teacherStats($user->id),
            'student' => $this->studentStats($user->id),
            'finance' => $this->financeStats(),
            default   => response()->json([], 403),
        };
    }

    protected function adminStats()
    {
        return response()->json([
            'total_users'      => DB::table('users')->count(),
            'total_courses'    => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount'),
        ]);
    }

    protected function teacherStats(int $teacherId)
    {
        $courses = Course::where('created_by', $teacherId)->pluck('id');
        return response()->json([
            'my_courses_count'   => $courses->count(),
            'students_count'     => Enrollment::whereIn('course_id', $courses)->distinct('student_id')->count(),
            'pending_assignments' => DB::table('submissions')
                ->join('assignments', 'submissions.assignment_id', '=', 'assignments.id')
                ->where('assignments.created_by', $teacherId)
                ->whereNull('submissions.graded_at')
                ->count(),
            'upcoming_schedules' => DB::table('schedules')
                ->where('teacher_id', $teacherId)
                ->where('start_time', '>=', now())
                ->count(),
        ]);
    }

    protected function studentStats(int $studentId)
    {
        return response()->json([
            'my_courses'         => Enrollment::where('student_id', $studentId)->count(),
            'completed_quizzes'  => QuizAttempt::where('student_id', $studentId)
                ->whereNotNull('completed_at')
                ->count(),
            'pending_assignments' => DB::table('submissions')
                ->where('student_id', $studentId)
                ->whereNull('submitted_at')
                ->count(),
            'upcoming_schedules' => DB::table('enrollments')
                ->join('schedules', 'enrollments.course_id', '=', 'schedules.course_id')
                ->where('enrollments.student_id', $studentId)
                ->where('schedules.start_time', '>=', now())
                ->count(),
        ]);
    }

    protected function financeStats()
    {
        return response()->json([
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'paid_payments'    => Payment::where('status', 'paid')->count(),
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount'),
        ]);
    }
}
