<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observation extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'title',
        'form_data',
        'response',
        'status',
        'user_id',
    ];

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
