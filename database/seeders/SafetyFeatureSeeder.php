<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SafetyFeature;

class SafetyFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $safetyFeatures = [
            ['name' => 'ABS', 'order' => 1, 'icon' => 'fas fa-shield-alt'],
            ['name' => 'EBD', 'order' => 2, 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Airbags', 'order' => 3, 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Traction Control', 'order' => 4, 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Stability Control', 'order' => 5, 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Hill Assist', 'order' => 6, 'icon' => 'fas fa-mountain'],
            ['name' => 'Tire Pressure Monitor', 'order' => 7, 'icon' => 'fas fa-tachometer-alt'],
            ['name' => 'ISOFIX', 'order' => 8, 'icon' => 'fas fa-child'],
            ['name' => 'Reverse Camera', 'order' => 9, 'icon' => 'fas fa-camera'],
            ['name' => 'Parking Sensors', 'order' => 10, 'icon' => 'fas fa-radar'],
        ];

        foreach ($safetyFeatures as $safetyFeature) {
            SafetyFeature::updateOrCreate(
                ['name' => $safetyFeature['name']],
                $safetyFeature
            );
        }

        $this->command->info('Safety features seeded successfully!');
    }
}

