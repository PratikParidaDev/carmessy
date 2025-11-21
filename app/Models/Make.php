<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;


class Make extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'country',
        'is_popular',
        'order',
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

        static::creating(function ($make) {
            if (empty($make->slug)) {
                $make->slug = Str::slug($make->name);
            }
        });
    }

    public function models()
    {
        return $this->hasMany(CarModel::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }


}
