<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
    ];

    public function passages()
    {
        return $this->hasMany(Passage::class);
    }

    public function attempts()
    {
        return $this->hasMany(UserAttempt::class);
    }
}
