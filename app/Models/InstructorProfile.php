<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'expertise',
        'cv_path',
        'portfolio_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

