<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'passage_id',
        'question_text',
        'question_type',
        'correct_answer',
        'score',
    ];

    public function passage()
    {
        return $this->belongsTo(Passage::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
