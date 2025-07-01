<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    // Student: list assignments for a course
    public function assignments($courseId)
    {
        return Assignment::where('course_id', $courseId)->get();
    }

    // Student: submit file
    public function store(Request $req, Assignment $assignment)
    {
        if ($req->user()->role->name !== 'student') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $req->validate(['file' => 'required|file|max:5120']); // max 5MB
        $path = $req->file('file')->store('submissions', 'public');
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $req->user()->id,
            'file_url' => Storage::url($path),
            'submitted_at' => now(),
        ]);
        // notify teacher
        Notification::create([
            'user_id' => $assignment->created_by,
            'title' => 'Tugas diserahkan',
            'message' => 'Student ' . $req->user()->name . ' menyerahkan tugas "' . $assignment->title . '".',
            'type' => 'assignment',
        ]);
        return response()->json($submission, 201);
    }

    // Teacher: list submissions
    public function submissions(Assignment $assignment)
    {
        return $assignment->submissions()->with('student')->get();
    }

    // Teacher: grade submission
    public function grade(Request $req, Submission $submission)
    {
        $data = $req->validate([
            'score' => 'required|integer|min:0',
            'feedback' => 'nullable|string',
        ]);
        $submission->update(array_merge($data, [
            'graded_at' => now(),
            'graded_by' => $req->user()->id
        ]));
        // notify student
        Notification::create([
            'user_id' => $submission->student_id,
            'title' => 'Tugas dinilai',
            'message' => 'Tugas "' . $submission->assignment->title .
                '" dinilai dengan skor ' . $submission->score . '.',
            'type' => 'assignment',
        ]);
        return response()->json($submission);
    }
}
