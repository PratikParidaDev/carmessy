<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Car extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, Searchable, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'make_id',
        'model_id',
        'dealer_id',
        'city_id',
        'year',
        'price',
        'condition',
        'mileage',
        'vin',
        'registration_number',
        'fuel_type',
        'transmission',
        'engine_capacity',
        'power',
        'torque',
        'mileage_kmpl',
        'exterior_color',
        'interior_color',
        'seats',
        'doors',
        'features',
        'safety_features',
        'owners',
        'insurance_valid',
        'insurance_expiry',
        'under_warranty',
        'service_history',
        'description',
        'status',
        'is_featured',
        'is_verified',
        'admin_notes',
        'meta_title',
        'meta_description',
        'views',
        'inquiries',
        'featured_until',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'safety_features' => 'array',
            'insurance_valid' => 'boolean',
            'under_warranty' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean',
            'insurance_expiry' => 'date',
            'featured_until' => 'datetime',
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'mileage_kmpl' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($car) {
            if (empty($car->slug)) {
                $baseSlug = Str::slug($car->title);
                $car->slug = $baseSlug;
                
                $count = 1;
                while (static::where('slug', $car->slug)->exists()) {
                    $car->slug = $baseSlug . '-' . $count++;
                }
            }
        });
    }

    // Scout Configuration
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'make' => $this->make->name,
            'model' => $this->model->name,
            'year' => $this->year,
            'price' => $this->price,
            'condition' => $this->condition,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'city' => $this->city->name,
            'state' => $this->city->state,
            'mileage' => $this->mileage,
            'exterior_color' => $this->exterior_color,
            'dealer_name' => $this->dealer->business_name,
        ];
    }

    public function searchableAs(): string
    {
        return 'cars_index';
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');
    }

    // Relationships
    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>', now());
            });
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // Helpers
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isFavorited(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->favorites()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getFormattedPrice(): string
    {
        return '₹ ' . number_format($this->price, 0);
    }

}
