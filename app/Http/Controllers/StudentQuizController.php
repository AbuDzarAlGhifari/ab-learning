<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    public function start(Request $request, Quiz $quiz)
    {
        $student = $request->user();

        // Cegah duplikat attempt aktif
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already started this quiz.'], 409);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
        ]);

        $quiz->load('questions.choices');

        return response()->json([
            'attempt_id' => $attempt->id,
            'quiz' => $quiz,
        ]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_text' => 'nullable', // bisa array (multiple)
        ]);

        $attempt = QuizAttempt::find($data['attempt_id']);

        // Cek ownership
        if ($attempt->student_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['message' => 'Quiz already submitted'], 409);
        }

        $score = 0;

        foreach ($data['answers'] as $ans) {
            $question = Question::with('choices')->find($ans['question_id']);
            $isCorrect = null;

            $userAnswer = $ans['answer_text'];

            // === Otomatis nilai untuk objective ===
            if (in_array($question->type, ['multiple_choice', 'true_false'])) {
                $correct = $question->choices->where('is_correct', true)->pluck('id')->toArray();
                $userAnswer = (int) $userAnswer;

                $isCorrect = in_array($userAnswer, $correct);
                if ($isCorrect) {
                    $score += $question->score;
                }
            }

            // === multiple_select (jawaban banyak) ===
            if ($question->type === 'multiple_select') {
                $correct = $question->choices->where('is_correct', true)->pluck('id')->sort()->values();
                $input   = collect($userAnswer)->map(fn($v) => (int)$v)->sort()->values();

                $isCorrect = $correct->toArray() === $input->toArray();
                if ($isCorrect) {
                    $score += $question->score;
                }
            }

            // Essay tidak otomatis dinilai
            if ($question->type === 'essay') {
                $isCorrect = null;
            }

            Answer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'answer_text' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
                'is_correct' => $isCorrect,
            ]);
        }

        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
        ]);

        return response()->json(['message' => 'Quiz submitted', 'score' => $score]);
    }

    public function result(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->student_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $attempt->load('answers.question');
        return response()->json($attempt);
    }

    public function available(Request $request)
    {
        $student = $request->user();

        $enrolledCourseIds = $student->enrollments()->pluck('course_id');

        $quizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->get();

        $attemptedQuizIds = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('completed_at')
            ->pluck('quiz_id')
            ->toArray();

        $availableQuizzes = $quizzes->filter(function ($quiz) use ($attemptedQuizIds) {
            return !in_array($quiz->id, $attemptedQuizIds);
        })->values();

        return response()->json($availableQuizzes);
    }
}
