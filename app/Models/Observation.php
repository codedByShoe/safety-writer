<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Observation extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'form_data',
        'response',
        'status',
        'user_id',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function finalize(): void
    {
        $this->update(['status' => 'finalized']);
    }

    public function addResponse(string $response): void
    {
        $this->update(['response' => $response]);
    }
}
