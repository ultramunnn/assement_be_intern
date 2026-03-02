<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passage;
use App\Models\Test;
use Illuminate\Http\Request;

class PassageController extends Controller
{
    public function index(Test $test)
    {
        $passages = $test->passages()
            ->with(['questions.options'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Passages retrieved successfully',
            'data' => $passages
        ]);
    }

    public function show(Passage $passage)
    {
        $passage->load(['test', 'questions.options']);

        return response()->json([
            'success' => true,
            'message' => 'Passage retrieved successfully',
            'data' => $passage
        ]);
    }

    public function store(Request $request, Test $test)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $passage = $test->passages()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Passage created successfully',
            'data' => $passage
        ], 201);
    }

    public function update(Request $request, Passage $passage)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
        ]);

        $passage->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Passage updated successfully',
            'data' => $passage->fresh()
        ]);
    }

    public function destroy(Passage $passage)
    {
        $passage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Passage deleted successfully',
        ]);
    }
}

