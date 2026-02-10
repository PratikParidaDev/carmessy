<?php

namespace Database\Seeders;

use App\Models\PickupType;
use Illuminate\Database\Seeder;

class PickupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pickupTypes = [
            [
                'name' => 'Showroom',
                'slug' => 'showroom',
                'display_name' => 'Showroom Pickup',
                'sort_order' => 1,
                'is_active' => true,
                'description' => 'Customer will pick up the vehicle from the showroom',
            ],
            [
                'name' => 'Home Delivery',
                'slug' => 'home-delivery',
                'display_name' => 'Home Delivery',
                'sort_order' => 2,
                'is_active' => true,
                'description' => 'Vehicle will be delivered to customer\'s address',
            ],
        ];

        foreach ($pickupTypes as $pickupType) {
            PickupType::updateOrCreate(
                ['slug' => $pickupType['slug']],
                $pickupType
            );
        }
    }
}
