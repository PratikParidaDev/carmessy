<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'vehicle_type',
        'full_name',
        'email',
        'phone_number',
        'city',
        'preferred_booking_date',
        'preferred_time_slot',
        'pickup_type',
        'payment_mode',
        'id_proof_type',
        'id_proof_number',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'preferred_booking_date' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        // For now, we only support cars
        // This will always return a Car relationship
        // When bikes are added, we can use a morphTo relationship
        return $this->belongsTo(Car::class, 'vehicle_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'vehicle_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // Helper methods
    public function getVehicleTitleAttribute()
    {
        if ($this->vehicle_type === 'car' && $this->vehicle) {
            return $this->vehicle->title ?? 'N/A';
        }
        return 'N/A';
    }
}
