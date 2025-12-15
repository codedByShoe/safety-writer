<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'stripe_price_id',
        'credits',
        'price',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'credits' => 'integer',
            'price' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getPriceInDollarsAttribute(): float
    {
        return $this->price / 100;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        // Calculate discount compared to the "base" package (usually starter)
        $basePackage = static::active()->ordered()->first();

        if (! $basePackage || $basePackage->id === $this->id) {
            return null;
        }

        $baseUnitPrice = $basePackage->price / $basePackage->credits;
        $thisUnitPrice = $this->price / $this->credits;

        if ($thisUnitPrice >= $baseUnitPrice) {
            return null;
        }

        return (int) round((($baseUnitPrice - $thisUnitPrice) / $baseUnitPrice) * 100);
    }

    public function getOriginalPriceAttribute(): ?int
    {
        $discount = $this->discount_percentage;

        if (! $discount) {
            return null;
        }

        $basePackage = static::active()->ordered()->first();
        $baseUnitPrice = $basePackage->price / $basePackage->credits;

        return (int) ceil(($baseUnitPrice * $this->credits) / 100) * 100;
    }
}
