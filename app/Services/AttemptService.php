<?php

namespace App\Services;

use App\Models\Test;
use App\Models\UserAttempt;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttemptService
{
  public function submitAnswers($user, $testId, array $answers)
  {
    return DB::transaction(function () use ($user, $testId, $answers) {

      Test::query()->findOrFail($testId);

      $attempt = UserAttempt::create([
        'user_id' => $user->id,
        'test_id' => $testId,
        'started_at' => now(),
        'finished_at' => now(),
      ]);

      $totalScore = 0;

      foreach ($answers as $answer) {

        $questionId = $answer['question_id'];
        $question = Question::query()
          ->where('id', $questionId)
          ->whereHas('passage', fn ($query) => $query->where('test_id', $testId))
          ->firstOrFail();

        $isCorrect = false;

        $selectedOptionId = $answer['selected_option_id'] ?? null;
        $answerText = $answer['answer_text'] ?? null;

        if (!is_null($selectedOptionId)) {
          $optionBelongsToQuestion = $question->options()
            ->where('id', $selectedOptionId)
            ->exists();

          if (!$optionBelongsToQuestion) {
            throw ValidationException::withMessages([
              'answers' => ["selected_option_id $selectedOptionId does not belong to question_id $questionId"],
            ]);
          }

          $isCorrect = $question->options()
            ->where('id', $selectedOptionId)
            ->where('is_correct', true)
            ->exists();
        } elseif (!is_null($answerText) && !is_null($question->correct_answer)) {
          $isCorrect = mb_strtolower(trim($answerText)) === mb_strtolower(trim($question->correct_answer));
        }

        if ($isCorrect) {
          $totalScore += $question->score;
        }

        UserAnswer::create([
          'attempt_id' => $attempt->id,
          'question_id' => $question->id,
          'selected_option_id' => $selectedOptionId,
          'answer_text' => $answerText,
          'is_correct' => $isCorrect,
        ]);
      }

      $attempt->update([
        'total_score' => $totalScore,
      ]);

      return $attempt->load('answers');
    });
  }
}
