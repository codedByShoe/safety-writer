<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Climactic\Credits\Traits\HasCredits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use Billable;
    use HasCredits {
        HasCredits::creditBalance insteadof Billable;
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    public function recentObservations()
    {
        return Observation::where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'status'])
            ->map(fn ($obs) => [
                'id' => $obs->id,
                'title' => $obs->title,
                'status' => $obs->status,
            ]);
    }

    public function deductCredits(int $amount, array $data): void
    {
        $metaData = [
            'user_id' => $this->id,
            'observation_id' => $data['observation_id'],
            'tags' => ['observation'],
        ];
        $this->creditDeduct($amount, 'Obsrevation Generated', $metaData);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
