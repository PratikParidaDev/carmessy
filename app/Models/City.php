<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;



class City extends Model 
{

    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'state',
        'is_popular',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($city) {
            if (empty($city->slug)) {
                $city->slug = Str::slug($city->name);
            }
        });

        static::updating(function ($city) {
            if ($city->isDirty('name') && empty($city->slug)) {
                $city->slug = Str::slug($city->name);
            }
        });
    }

    /**
     * Get active cities ordered by name
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function dealers()
    {
        return $this->hasMany(Dealer::class);
    }
}
