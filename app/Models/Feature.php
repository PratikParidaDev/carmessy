<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feature extends Model
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

        static::creating(function ($feature) {
            if (empty($feature->slug)) {
                $feature->slug = Str::slug($feature->name);
            }
        });

        static::updating(function ($feature) {
            if ($feature->isDirty('name') && empty($feature->slug)) {
                $feature->slug = Str::slug($feature->name);
            }
        });
    }

    /**
     * Get active features ordered by order column
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }
}
