<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'allowed_number_id',
        'direction',
        'content',
        'type',
        'ai_response',
        'prompt_tokens',
        'completion_tokens',
    ];

    protected function casts(): array
    {
        return [
            'ai_response' => 'array',
        ];
    }

    public function allowedNumber(): BelongsTo
    {
        return $this->belongsTo(AllowedNumber::class);
    }

    public function scopeIncoming($query)
    {
        return $query->where('direction', 'in');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'out');
    }
}
