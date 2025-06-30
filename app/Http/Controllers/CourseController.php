<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;

class CourseController extends Controller
{
    public function index()
    {
        return Course::paginate(10);
    }

    public function store(StoreCourseRequest $req)
    {
        return Course::create($req->validated());
    }

    public function show(Course $course)
    {
        return $course;
    }

    public function update(StoreCourseRequest $req, Course $course)
    {
        $course->update($req->validated());
        return $course;
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }
}
