<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    // Teacher: list assignments per course
    public function index($courseId)
    {
        return Assignment::where('course_id', $courseId)->get();
    }

    // Teacher: create
    public function store(Request $req, $courseId)
    {
        $data = $req->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);
        $data['course_id'] = $courseId;
        $data['created_by'] = $req->user()->id;
        $assignment = Assignment::create($data);
        return response()->json($assignment, 201);
    }

    // Teacher: update
    public function update(Request $req, Assignment $assignment)
    {
        if ($assignment->created_by != $req->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $req->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);
        $assignment->update($data);
        return response()->json($assignment);
    }

    // Teacher: delete
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return response()->noContent();
    }
}
