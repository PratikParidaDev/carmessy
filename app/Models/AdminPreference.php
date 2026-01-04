<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sidebar_bg',
        'sidebar_hover',
        'sidebar_text',
        'sidebar_active',
        'content_bg',
        'primary_color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default color scheme
     */
    public static function getDefaults(): array
    {
        return [
            'sidebar_bg' => '#23282d',
            'sidebar_hover' => '#32373c',
            'sidebar_text' => '#b4b9be',
            'sidebar_active' => '#0073aa',
            'content_bg' => '#f0f0f1',
            'primary_color' => '#2271b1',
        ];
    }

    /**
     * Get predefined color schemes (WordPress-style)
     */
    public static function getPresets(): array
    {
        return [
            'default' => [
                'name' => 'Default (WordPress)',
                'sidebar_bg' => '#23282d',
                'sidebar_hover' => '#32373c',
                'sidebar_text' => '#b4b9be',
                'sidebar_active' => '#0073aa',
                'content_bg' => '#f0f0f1',
                'primary_color' => '#2271b1',
            ],
            'blue' => [
                'name' => 'Ocean Blue',
                'sidebar_bg' => '#1e3a5f',
                'sidebar_hover' => '#2a4f7a',
                'sidebar_text' => '#b8d4f0',
                'sidebar_active' => '#0073aa',
                'content_bg' => '#f5f7fa',
                'primary_color' => '#0073aa',
            ],
            'green' => [
                'name' => 'Forest Green',
                'sidebar_bg' => '#1e4620',
                'sidebar_hover' => '#2d5f30',
                'sidebar_text' => '#b8e5bb',
                'sidebar_active' => '#46b450',
                'content_bg' => '#f0f7f0',
                'primary_color' => '#46b450',
            ],
            'purple' => [
                'name' => 'Royal Purple',
                'sidebar_bg' => '#3d2a5f',
                'sidebar_hover' => '#4a3a7a',
                'sidebar_text' => '#d4c8f0',
                'sidebar_active' => '#826eb4',
                'content_bg' => '#f5f0fa',
                'primary_color' => '#826eb4',
            ],
            'red' => [
                'name' => 'Crimson Red',
                'sidebar_bg' => '#5f1e1e',
                'sidebar_hover' => '#7a2a2a',
                'sidebar_text' => '#f0b8b8',
                'sidebar_active' => '#dc3232',
                'content_bg' => '#faf0f0',
                'primary_color' => '#dc3232',
            ],
            'orange' => [
                'name' => 'Sunset Orange',
                'sidebar_bg' => '#5f3a1e',
                'sidebar_hover' => '#7a4a2a',
                'sidebar_text' => '#f0d4b8',
                'sidebar_active' => '#ff6b35',
                'content_bg' => '#faf5f0',
                'primary_color' => '#ff6b35',
            ],
            'dark' => [
                'name' => 'Dark Mode',
                'sidebar_bg' => '#1a1a1a',
                'sidebar_hover' => '#2a2a2a',
                'sidebar_text' => '#cccccc',
                'sidebar_active' => '#4a9eff',
                'content_bg' => '#0f0f0f',
                'primary_color' => '#4a9eff',
            ],
            'light' => [
                'name' => 'Light Mode',
                'sidebar_bg' => '#ffffff',
                'sidebar_hover' => '#f0f0f0',
                'sidebar_text' => '#333333',
                'sidebar_active' => '#0073aa',
                'content_bg' => '#f9f9f9',
                'primary_color' => '#0073aa',
            ],
        ];
    }
}

