<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::where('created_by', $request->user()->id)->with('course')->get();
        return response()->json($quizzes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $quiz = Quiz::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($quiz, 201);
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.choices');
        return response()->json($quiz);
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'sometimes|exists:courses,id',
        ]);

        $quiz->update($data);
        return response()->json($quiz);
    }

    public function destroy(Request $request, Quiz $quiz)
    {
        if ($quiz->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted']);
    }
}
