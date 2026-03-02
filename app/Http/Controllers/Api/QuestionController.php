<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passage;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Passage $passage)
    {
        $questions = $passage->questions()
            ->with(['options'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Questions retrieved successfully',
            'data' => $questions
        ]);
    }

    public function show(Question $question)
    {
        $question->load(['passage.test', 'options']);

        return response()->json([
            'success' => true,
            'message' => 'Question retrieved successfully',
            'data' => $question
        ]);
    }

    public function store(Request $request, Passage $passage)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'correct_answer' => 'nullable|string',
            'score' => 'nullable|integer|min:0',
        ]);

        $question = $passage->questions()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully',
            'data' => $question
        ], 201);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'sometimes|required|string',
            'question_type' => 'sometimes|required|string',
            'correct_answer' => 'sometimes|nullable|string',
            'score' => 'sometimes|nullable|integer|min:0',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully',
            'data' => $question->fresh()
        ]);
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully',
        ]);
    }
}

