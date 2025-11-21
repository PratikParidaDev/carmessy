<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dealer_id',
        'car_id',
        'rating',
        'comment',
        'is_verified_purchase',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

}
