<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\Dealer;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\City;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        $dealers = Dealer::all();
        $cities = City::all();
        $makes = Make::with('models')->get();

        // Ensure we have at least one city, dealer, make and model so ->random() won't fail
        if ($cities->isEmpty()) {
            $city = City::create(['name' => 'Sample City']);
            $cities = City::all();
        }

        if ($makes->isEmpty()) {
            $make = Make::create(['name' => 'Sample Make']);
            // create a default model for the make
            $make->models()->create([
                'name' => 'Sample Model',
                'year_start' => 2015,
                'year_end' => 2025,
            ]);
            $makes = Make::with('models')->get();
        } else {
            // ensure each make has at least one model
            foreach ($makes as $m) {
                if ($m->models->isEmpty()) {
                    $m->models()->create([
                        'name' => $m->name . ' Model',
                        'year_start' => 2015,
                        'year_end' => 2025,
                    ]);
                }
            }
            // reload makes to include any created models
            $makes = Make::with('models')->get();
        }

        if ($dealers->isEmpty()) {
            // create a simple dealer in the first available city
            $firstCity = $cities->first();
            Dealer::create([
                'business_name' => 'Sample Dealer',
                'phone' => '0000000000',
                'address' => 'Sample address',
                'city_id' => $firstCity ? $firstCity->id : null,
            ]);
            $dealers = Dealer::all();
        }

        $colors = ['White', 'Black', 'Silver', 'Red', 'Blue', 'Grey', 'Brown'];
        $features = [
            'Air Conditioning',
            'Power Steering',
            'Power Windows',
            'ABS',
            'Airbags',
            'Alloy Wheels',
            'Fog Lamps',
            'Bluetooth',
            'Touchscreen',
            'Rear Parking Sensors',
            'Rear Camera',
            'Sunroof',
            'Cruise Control',
            'Keyless Entry',
        ];

        $safetyFeatures = [
            'ABS',
            'Dual Airbags',
            'EBD',
            'Traction Control',
            'Hill Hold Control',
            'ISOFIX Mounts',
        ];

        foreach ($dealers as $dealer) {
            $numCars = rand(3, 8);
            
            for ($i = 0; $i < $numCars; $i++) {
                $make = $makes->random();
                $model = $make->models->random();
                $year = rand(2015, 2024);
                $condition = $year >= 2023 ? 'new' : 'used';

                $car = Car::create(
                    [
                    'title' => $make->name . ' ' . $model->name . ' ' . $year,
                    'make_id' => $make->id,
                    'model_id' => $model->id,
                    'dealer_id' => $dealer->id,
                    'city_id' => $cities->random()->id,
                    'year' => $year,
                    'price' => rand(300000, 5000000),
                    'condition' => $condition,
                    'mileage' => $condition === 'new' ? rand(0, 50) : rand(10000, 150000),
                    'fuel_type' => ['petrol', 'diesel', 'electric', 'hybrid'][rand(0, 3)],
                    'transmission' => ['manual', 'automatic'][rand(0, 1)],
                    'engine_capacity' => ['1.2L', '1.5L', '2.0L', '2.5L'][rand(0, 3)],
                    'power' => rand(70, 200),
                    'torque' => rand(100, 300),
                    'mileage_kmpl' => rand(12, 25),
                    'exterior_color' => $colors[rand(0, count($colors) - 1)],
                    'interior_color' => $colors[rand(0, count($colors) - 1)],
                    'seats' => [5, 7, 8][rand(0, 2)],
                    'doors' => [2, 4, 5][rand(0, 2)],
                    'features' => array_rand(array_flip($features), rand(5, 10)),
                    'safety_features' => array_rand(array_flip($safetyFeatures), rand(3, 6)),
                    'owners' => $condition === 'new' ? 0 : rand(1, 3),
                    'insurance_valid' => true,
                    'insurance_expiry' => now()->addMonths(rand(6, 24)),
                    'under_warranty' => $condition === 'new' || rand(0, 1),
                    'description' => "Well-maintained {$make->name} {$model->name} in excellent condition. All services done on time at authorized service center.",
                    'status' => 'approved',
                    'is_featured' => rand(0, 4) === 0, // 20% chance
                    'is_verified' => true,
                    'published_at' => now()->subDays(rand(1, 60)),
                    'views' => rand(50, 500),
                    'inquiries' => rand(2, 20),
                    ]
            );
            }
        }
    }
}


