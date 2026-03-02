<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\UserAttempt;
use App\Services\AttemptService;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    protected $attemptService;

    public function __construct(AttemptService $attemptService)
    {
        $this->attemptService = $attemptService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = UserAttempt::query()
            ->with(['answers.selectedOption', 'answers.question', 'test'])
            ->orderByDesc('id');

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $attempts = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Attempts retrieved successfully',
            'data' => $attempts
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'user_id' => 'nullable|exists:users,id',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        $userId = $user->hasRole('admin') && isset($validated['user_id'])
            ? $validated['user_id']
            : $user->id;

        $attempt = UserAttempt::create([
            'user_id' => $userId,
            'test_id' => $validated['test_id'],
            'started_at' => $validated['started_at'] ?? now(),
            'finished_at' => $validated['finished_at'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attempt created successfully',
            'data' => $attempt->load(['answers', 'test'])
        ], 201);
    }

    public function show(Request $request, UserAttempt $attempt)
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && $attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $attempt->load(['answers.selectedOption', 'answers.question', 'test']);

        return response()->json([
            'success' => true,
            'message' => 'Attempt retrieved successfully',
            'data' => $attempt
        ]);
    }

    public function update(Request $request, UserAttempt $attempt)
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && $attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'started_at' => 'sometimes|nullable|date',
            'finished_at' => 'sometimes|nullable|date|after_or_equal:started_at',
        ]);

        $attempt->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attempt updated successfully',
            'data' => $attempt->fresh()->load(['answers', 'test'])
        ]);
    }

    public function destroy(Request $request, UserAttempt $attempt)
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && $attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $attempt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attempt deleted successfully',
        ]);
    }

    public function submit(Request $request, Test $test)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected_option_id' => 'nullable|exists:options,id',
            'answers.*.answer_text' => 'nullable|string'
        ]);

        $attempt = $this->attemptService->submitAnswers(
            $request->user(),
            $test->id,
            $validated['answers']
        );

        return response()->json([
            'success' => true,
            'message' => 'Submission success',
            'data' => [
                'total_score' => $attempt->total_score,
                'answers' => $attempt->answers
            ]
        ]);
    }
}
