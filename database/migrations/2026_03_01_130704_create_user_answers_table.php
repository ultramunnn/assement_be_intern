<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')
                ->constrained('user_attempts')
                ->onDelete('cascade');
            $table->foreignId('question_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('selected_option_id')
                ->nullable()
                ->constrained('options')
                ->nullOnDelete();
            $table->string('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->index(['attempt_id', 'question_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
