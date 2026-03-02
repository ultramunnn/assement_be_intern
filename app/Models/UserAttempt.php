<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'total_score',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }
}
