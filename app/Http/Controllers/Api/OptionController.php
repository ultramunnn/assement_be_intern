<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index(Question $question)
    {
        $options = $question->options()->get();

        return response()->json([
            'success' => true,
            'message' => 'Options retrieved successfully',
            'data' => $options
        ]);
    }

    public function show(Option $option)
    {
        $option->load(['question.passage.test']);

        return response()->json([
            'success' => true,
            'message' => 'Option retrieved successfully',
            'data' => $option
        ]);
    }

    public function store(Request $request, Question $question)
    {
        $validated = $request->validate([
            'option_label' => 'required|string',
            'option_text' => 'required|string',
            'is_correct' => 'nullable|boolean',
        ]);

        $option = $question->options()->create([
            'option_label' => $validated['option_label'],
            'option_text' => $validated['option_text'],
            'is_correct' => $validated['is_correct'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Option created successfully',
            'data' => $option
        ], 201);
    }

    public function update(Request $request, Option $option)
    {
        $validated = $request->validate([
            'option_label' => 'sometimes|required|string',
            'option_text' => 'sometimes|required|string',
            'is_correct' => 'sometimes|boolean',
        ]);

        $option->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Option updated successfully',
            'data' => $option->fresh()
        ]);
    }

    public function destroy(Option $option)
    {
        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option deleted successfully',
        ]);
    }
}

