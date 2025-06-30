<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_text' => 'nullable|string',
            'question_image' => 'nullable|string',
            'type' => 'required|in:multiple_choice,multiple_select,true_false,essay',
            'score' => 'nullable|integer|min:1',
            'choices' => 'array',
            'choices.*.text' => 'nullable|string',
            'choices.*.image' => 'nullable|string',
            'choices.*.is_correct' => 'boolean',
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $request->question_text,
            'question_image' => $request->question_image,
            'type' => $request->type,
            'score' => $request->score ?? 1,
        ]);

        if (in_array($question->type, ['multiple_choice', 'multiple_select', 'true_false'])) {
            foreach ($request->choices as $choice) {
                $question->choices()->create([
                    'choice_text' => $choice['text'] ?? null,
                    'choice_image' => $choice['image'] ?? null,
                    'is_correct' => $choice['is_correct'] ?? false,
                ]);
            }
        }

        return response()->json($question->load('choices'), 201);
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'nullable|string',
            'question_image' => 'nullable|string',
            'score' => 'nullable|integer|min:1',
            'choices' => 'array',
            'choices.*.id' => 'nullable|exists:choices,id',
            'choices.*.text' => 'nullable|string',
            'choices.*.image' => 'nullable|string',
            'choices.*.is_correct' => 'boolean',
        ]);

        $question->update([
            'question_text' => $request->question_text ?? $question->question_text,
            'question_image' => $request->question_image ?? $question->question_image,
            'score' => $request->score ?? $question->score,
        ]);

        if ($request->has('choices')) {
            $question->choices()->delete(); // reset all
            foreach ($request->choices as $choice) {
                $question->choices()->create([
                    'choice_text' => $choice['text'] ?? null,
                    'choice_image' => $choice['image'] ?? null,
                    'is_correct' => $choice['is_correct'] ?? false,
                ]);
            }
        }

        return response()->json($question->load('choices'));
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(['message' => 'Question deleted']);
    }
}
