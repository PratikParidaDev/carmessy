<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_id',
        'plan_id',
        'stripe_subscription_id',
        'status',
        'starts_at',
        'ends_at',
        'featured_listings_used',
        'regular_listings_used',
    ];


    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->ends_at->isFuture();
    }

    public function canCreateFeaturedListing(): bool
    {
        return $this->featured_listings_used < $this->plan->featured_listings;
    }

    public function canCreateRegularListing(): bool
    {
        return $this->regular_listings_used < $this->plan->regular_listings;
    }


}
