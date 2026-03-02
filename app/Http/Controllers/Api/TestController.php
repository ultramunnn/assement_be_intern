<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Services\TestService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;

        $this->middleware('role:admin')->only([
            'store',
            'update',
            'destroy',
        ]);
    }

    public function index()
    {
        $tests = $this->testService->getAllTestsWithQuestions();

        return response()->json([
            'success' => true,
            'message' => 'Tests retrieved successfully',
            'data' => $tests
        ]);
    }

    public function show(Test $test)
    {
        $test = $this->testService->getTestWithQuestions($test->id);

        return response()->json([
            'success' => true,
            'message' => 'Test retrieved successfully',
            'data' => $test
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer',
        ]);

        $test = Test::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Test created successfully',
            'data' => $test
        ], 201);
    }

    public function update(Request $request, Test $test)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'duration_minutes' => 'sometimes|required|integer',
        ]);

        $test->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Test updated successfully',
            'data' => $test->fresh()
        ]);
    }

    public function destroy(Test $test)
    {
        $test->delete();

        return response()->json([
            'success' => true,
            'message' => 'Test deleted successfully',
        ]);
    }
}
