<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SafetyFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($safetyFeature) {
            if (empty($safetyFeature->slug)) {
                $safetyFeature->slug = Str::slug($safetyFeature->name);
            }
        });

        static::updating(function ($safetyFeature) {
            if ($safetyFeature->isDirty('name') && empty($safetyFeature->slug)) {
                $safetyFeature->slug = Str::slug($safetyFeature->name);
            }
        });
    }

    /**
     * Get active safety features ordered by order column
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }
}
