<?php

namespace App\Services;

use App\Models\Test;

class TestService
{
  public function getAllTestsWithQuestions()
  {
    return Test::with([
      'passages.questions.options'
    ])->get();
  }

  public function getTestWithQuestions($testId)
  {
    return Test::with([
      'passages.questions.options'
    ])->findOrFail($testId);
  }
}
