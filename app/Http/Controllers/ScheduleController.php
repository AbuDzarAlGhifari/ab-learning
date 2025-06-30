<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $req)
    {
        return Schedule::with('course')
            ->where('teacher_id', $req->user()->id)
            ->get();
    }

    public function forStudent(Request $req)
    {
        $courseIds = $req->user()->enrollments()->pluck('course_id');
        return Schedule::with('course')
            ->whereIn('course_id', $courseIds)
            ->get();
    }


    public function store(Request $req)
    {
        $data = $req->validate([
            'course_id'  => 'required|exists:courses,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time'   => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);

        // Teacher hanya boleh buat schedule untuk kursus miliknya?
        // Jika ingin dibatasi, cek dulu ownership di sini.

        $schedule = Schedule::create([
            'course_id'  => $data['course_id'],
            'teacher_id' => $req->user()->id,
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
        ]);

        return response()->json($schedule, 201);
    }
}
