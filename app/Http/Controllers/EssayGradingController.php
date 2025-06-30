<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class EssayGradingController extends Controller
{
    public function index()
    {
        $answers = Answer::with(['question', 'attempt.student'])
            ->whereHas('question', fn($q) => $q->where('type', 'essay'))
            ->whereNull('score')
            ->get();

        return response()->json($answers);
    }

    public function grade(Request $request, Answer $answer)
    {
        if ($answer->question->type !== 'essay') {
            return response()->json(['message' => 'Only essay can be graded manually'], 400);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0|max:' . $answer->question->score,
            'is_correct' => 'nullable|boolean',
        ]);

        $answer->update([
            'score' => $data['score'],
            'is_correct' => $data['is_correct'] ?? null,
        ]);

        $attempt = $answer->attempt;
        $totalScore = $attempt->answers()->sum('score');
        $attempt->update(['score' => $totalScore]);

        return response()->json(['message' => 'Answer graded', 'new_score' => $totalScore]);
    }
}
