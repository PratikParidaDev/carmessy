<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'make_id',
        'model_id',
        'city_id',
        'min_price',
        'max_price',
        'min_year',
        'max_year',
        'fuel_type',
        'transmission',
        'is_active',
        'last_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_notified_at' => 'datetime',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function matchesCar(Car $car): bool
    {
        if ($this->make_id && $car->make_id !== $this->make_id) {
            return false;
        }

        if ($this->model_id && $car->model_id !== $this->model_id) {
            return false;
        }

        if ($this->city_id && $car->city_id !== $this->city_id) {
            return false;
        }

        if ($this->min_price && $car->price < $this->min_price) {
            return false;
        }

        if ($this->max_price && $car->price > $this->max_price) {
            return false;
        }

        if ($this->min_year && $car->year < $this->min_year) {
            return false;
        }

        if ($this->max_year && $car->year > $this->max_year) {
            return false;
        }

        if ($this->fuel_type && $car->fuel_type !== $this->fuel_type) {
            return false;
        }

        if ($this->transmission && $car->transmission !== $this->transmission) {
            return false;
        }

        return true;
    }


}
