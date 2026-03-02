<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Question;
use App\Models\UserAnswer;
use App\Models\UserAttempt;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{
    public function index(Request $request, UserAttempt $attempt)
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && $attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $answers = $attempt->answers()
            ->with(['question', 'selectedOption'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Answers retrieved successfully',
            'data' => $answers
        ]);
    }

    public function show(Request $request, UserAnswer $answer)
    {
        $user = $request->user();
        $answer->load(['attempt', 'question', 'selectedOption']);

        if (!$user->hasRole('admin') && $answer->attempt?->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer retrieved successfully',
            'data' => $answer
        ]);
    }

    public function store(Request $request, UserAttempt $attempt)
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && $attempt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:options,id',
            'answer_text' => 'nullable|string',
        ]);

        $computed = $this->computeIsCorrect(
            $attempt,
            $validated['question_id'],
            $validated['selected_option_id'] ?? null,
            $validated['answer_text'] ?? null,
        );

        $answer = $attempt->answers()->create([
            'question_id' => $validated['question_id'],
            'selected_option_id' => $validated['selected_option_id'] ?? null,
            'answer_text' => $validated['answer_text'] ?? null,
            'is_correct' => $computed['is_correct'],
        ]);

        $this->recalculateTotalScore($attempt);

        return response()->json([
            'success' => true,
            'message' => 'Answer created successfully',
            'data' => $answer->load(['question', 'selectedOption'])
        ], 201);
    }

    public function update(Request $request, UserAnswer $answer)
    {
        $user = $request->user();
        $answer->load('attempt');

        if (!$user->hasRole('admin') && $answer->attempt?->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'selected_option_id' => 'sometimes|nullable|exists:options,id',
            'answer_text' => 'sometimes|nullable|string',
        ]);

        $selectedOptionId = array_key_exists('selected_option_id', $validated)
            ? $validated['selected_option_id']
            : $answer->selected_option_id;

        $answerText = array_key_exists('answer_text', $validated)
            ? $validated['answer_text']
            : $answer->answer_text;

        $computed = $this->computeIsCorrect(
            $answer->attempt,
            $answer->question_id,
            $selectedOptionId,
            $answerText,
        );

        $answer->update([
            'selected_option_id' => $selectedOptionId,
            'answer_text' => $answerText,
            'is_correct' => $computed['is_correct'],
        ]);

        $this->recalculateTotalScore($answer->attempt);

        return response()->json([
            'success' => true,
            'message' => 'Answer updated successfully',
            'data' => $answer->fresh()->load(['question', 'selectedOption'])
        ]);
    }

    public function destroy(Request $request, UserAnswer $answer)
    {
        $user = $request->user();
        $answer->load('attempt');

        if (!$user->hasRole('admin') && $answer->attempt?->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $attempt = $answer->attempt;
        $answer->delete();

        if ($attempt) {
            $this->recalculateTotalScore($attempt);
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer deleted successfully',
        ]);
    }

    private function computeIsCorrect(
        UserAttempt $attempt,
        int $questionId,
        ?int $selectedOptionId,
        ?string $answerText,
    ): array {
        $question = Question::query()
            ->where('id', $questionId)
            ->whereHas('passage', fn ($query) => $query->where('test_id', $attempt->test_id))
            ->firstOrFail();

        if (!is_null($selectedOptionId)) {
            $optionBelongsToQuestion = Option::query()
                ->where('id', $selectedOptionId)
                ->where('question_id', $questionId)
                ->exists();

            if (!$optionBelongsToQuestion) {
                throw ValidationException::withMessages([
                    'selected_option_id' => ["selected_option_id $selectedOptionId does not belong to question_id $questionId"],
                ]);
            }

            $isCorrect = Option::query()
                ->where('id', $selectedOptionId)
                ->where('question_id', $questionId)
                ->where('is_correct', true)
                ->exists();

            return ['is_correct' => $isCorrect];
        }

        if (!is_null($answerText) && !is_null($question->correct_answer)) {
            $isCorrect = mb_strtolower(trim($answerText)) === mb_strtolower(trim($question->correct_answer));
            return ['is_correct' => $isCorrect];
        }

        return ['is_correct' => false];
    }

    private function recalculateTotalScore(UserAttempt $attempt): void
    {
        $attempt->loadMissing(['answers.question']);

        $totalScore = $attempt->answers
            ->filter(fn ($answer) => (bool) $answer->is_correct)
            ->sum(fn ($answer) => (int) ($answer->question?->score ?? 0));

        $attempt->update(['total_score' => $totalScore]);
    }
}

