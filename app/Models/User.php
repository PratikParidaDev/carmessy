<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'is_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }




    public function dealer()
    {
        return $this->hasOne(Dealer::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Role checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDealer(): bool
    {
        return $this->role === 'dealer';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function adminPreference()
    {
        return $this->hasOne(AdminPreference::class);
    }

    /**
     * Get admin color preferences or defaults
     */
    public function getAdminColors(): array
    {
        $preference = $this->adminPreference;
        
        if ($preference) {
            return [
                'sidebar_bg' => $preference->sidebar_bg,
                'sidebar_hover' => $preference->sidebar_hover,
                'sidebar_text' => $preference->sidebar_text,
                'sidebar_active' => $preference->sidebar_active,
                'content_bg' => $preference->content_bg,
                'primary_color' => $preference->primary_color,
            ];
        }
        
        return AdminPreference::getDefaults();
    }
}


