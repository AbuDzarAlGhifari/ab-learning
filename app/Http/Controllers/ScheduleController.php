<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $req)
    {
        return Schedule::with('course')
            ->where('teacher_id', $req->user()->id)->get();
    }
    public function forStudent(Request $req)
    {
        $courseIds = $req->user()->enrollments()->pluck('course_id');
        return Schedule::with('course')->whereIn('course_id', $courseIds)->get();
    }
}
