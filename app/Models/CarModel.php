<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;



class CarModel extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'make_id',
        'name',
        'slug',
        'body_type',
        'year_start',
        'year_end',
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

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->make->name . ' ' . $model->name);
            }
        });
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'model_id');
    }
}
