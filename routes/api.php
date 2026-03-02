<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\PassageController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\AnswerController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('tests', TestController::class)->only(['index', 'show']);
    Route::post('tests/{test}/submit', [AttemptController::class, 'submit']);

    Route::apiResource('tests.passages', PassageController::class)
        ->shallow()
        ->only(['index', 'show']);

    Route::apiResource('passages.questions', QuestionController::class)
        ->shallow()
        ->only(['index', 'show']);

    Route::apiResource('questions.options', OptionController::class)
        ->shallow()
        ->only(['index', 'show']);

    Route::apiResource('attempts', AttemptController::class);

    Route::apiResource('attempts.answers', AnswerController::class)
        ->shallow();

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('tests', TestController::class)
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('tests.passages', PassageController::class)
            ->shallow()
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('passages.questions', QuestionController::class)
            ->shallow()
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('questions.options', OptionController::class)
            ->shallow()
            ->only(['store', 'update', 'destroy']);
    });

});
