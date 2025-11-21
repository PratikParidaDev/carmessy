<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Dealer extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'description',
        'phone',
        'whatsapp',
        'address',
        'city_id',
        'pincode',
        'latitude',
        'longitude',
        'gst_number',
        'website',
        'working_hours',
        'is_verified',
        'is_premium',
        'premium_until',
        'rating',
        'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'working_hours' => 'array',
            'is_verified' => 'boolean',
            'is_premium' => 'boolean',
            'premium_until' => 'datetime',
            'rating' => 'decimal:1',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dealer) {
            if (empty($dealer->slug)) {
                $dealer->slug = Str::slug($dealer->business_name);
            }
        });
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('photos')
            ->useDisk('public');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    // Helpers
    public function isPremium(): bool
    {
        return $this->is_premium && 
               $this->premium_until && 
               $this->premium_until->isFuture();
    }

    public function updateRating(): void
    {
        $this->rating = $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->where('is_approved', true)->count();
        $this->save();
    }


}
