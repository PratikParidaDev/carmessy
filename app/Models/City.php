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
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
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
