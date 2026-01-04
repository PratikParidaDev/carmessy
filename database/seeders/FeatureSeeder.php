<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['name' => 'Air Conditioning', 'order' => 1, 'icon' => 'fas fa-snowflake'],
            ['name' => 'Power Steering', 'order' => 2, 'icon' => 'fas fa-steering-wheel'],
            ['name' => 'Power Windows', 'order' => 3, 'icon' => 'fas fa-window-maximize'],
            ['name' => 'Central Locking', 'order' => 4, 'icon' => 'fas fa-lock'],
            ['name' => 'Music System', 'order' => 5, 'icon' => 'fas fa-music'],
            ['name' => 'Bluetooth', 'order' => 6, 'icon' => 'fas fa-bluetooth'],
            ['name' => 'USB Port', 'order' => 7, 'icon' => 'fas fa-usb'],
            ['name' => 'Navigation System', 'order' => 8, 'icon' => 'fas fa-map-marked-alt'],
            ['name' => 'Sunroof', 'order' => 9, 'icon' => 'fas fa-sun'],
            ['name' => 'Alloy Wheels', 'order' => 10, 'icon' => 'fas fa-circle'],
            ['name' => 'Fog Lights', 'order' => 11, 'icon' => 'fas fa-lightbulb'],
            ['name' => 'Rear Camera', 'order' => 12, 'icon' => 'fas fa-camera'],
            ['name' => 'Parking Sensors', 'order' => 13, 'icon' => 'fas fa-radar'],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['name' => $feature['name']],
                $feature
            );
        }

        $this->command->info('Features seeded successfully!');
    }
}
