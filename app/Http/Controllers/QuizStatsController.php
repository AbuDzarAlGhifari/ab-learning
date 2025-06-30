<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class QuizStatsController extends Controller
{
    public function show(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->whereNotNull('completed_at')->with('student')->get();

        if ($attempts->isEmpty()) {
            return response()->json(['message' => 'No attempts yet.'], 404);
        }

        $sorted = $attempts->sortByDesc('score')->values();

        return response()->json([
            'quiz_id' => $quiz->id,
            'quiz_title' => $quiz->title,
            'participants' => $attempts->count(),
            'average_score' => round($attempts->avg('score'), 2),
            'highest_score' => $attempts->max('score'),
            'lowest_score' => $attempts->min('score'),
            'ranking' => $sorted->map(function ($attempt, $index) {
                return [
                    'rank' => $index + 1,
                    'student_id' => $attempt->student->id,
                    'student_name' => $attempt->student->name,
                    'score' => $attempt->score,
                    'submitted_at' => $attempt->completed_at,
                ];
            }),
        ]);
    }

    public function byCourse(Course $course)
    {
        $quizzes = $course->quizzes()->with('attempts.student')->get();

        $data = $quizzes->map(function ($quiz) {
            $attempts = $quiz->attempts->whereNotNull('completed_at');
            return [
                'quiz_id' => $quiz->id,
                'quiz_title' => $quiz->title,
                'participants' => $attempts->count(),
                'average_score' => round($attempts->avg('score'), 2),
                'highest_score' => $attempts->max('score'),
                'lowest_score' => $attempts->min('score'),
            ];
        });

        return response()->json($data);
    }

    public function export(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->whereNotNull('completed_at')->with('student')->orderByDesc('score')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="quiz_' . $quiz->id . '_ranking.csv"',
        ];

        $callback = function () use ($attempts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Rank', 'Student Name', 'Score', 'Submitted At']);
            foreach ($attempts as $i => $attempt) {
                fputcsv($handle, [
                    $i + 1,
                    $attempt->student->name,
                    $attempt->score,
                    $attempt->completed_at
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function questionStats(Quiz $quiz)
    {
        // dd($quiz);
        $questions = $quiz->questions()->with('answers')->get();

        $data = $questions->map(function ($q) {
            $total = $q->answers->count();
            $correct = $q->answers->where('is_correct', true)->count();
            $incorrect = $q->answers->where('is_correct', false)->count();
            $not_graded = $q->answers->where('is_correct', null)->count();

            return [
                'question_id' => $q->id,
                'question_text' => $q->question_text,
                'type' => $q->type,
                'total_answered' => $total,
                'correct' => $correct,
                'incorrect' => $incorrect,
                'not_graded' => $not_graded
            ];
        });

        return response()->json($data);
    }
}
