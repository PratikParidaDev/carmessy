<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Make;
use App\Models\CarModel;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsData = [
            'Maruti Suzuki' => [
                ['name' => 'Swift', 'body_type' => 'hatchback', 'year_start' => 2005],
                ['name' => 'Baleno', 'body_type' => 'hatchback', 'year_start' => 2015],
                ['name' => 'Dzire', 'body_type' => 'sedan', 'year_start' => 2008],
                ['name' => 'Brezza', 'body_type' => 'suv', 'year_start' => 2016],
                ['name' => 'Alto', 'body_type' => 'hatchback', 'year_start' => 2000],
                ['name' => 'Wagon R', 'body_type' => 'hatchback', 'year_start' => 1999],
            ],
            'Hyundai' => [
                ['name' => 'Creta', 'body_type' => 'suv', 'year_start' => 2015],
                ['name' => 'Venue', 'body_type' => 'suv', 'year_start' => 2019],
                ['name' => 'i20', 'body_type' => 'hatchback', 'year_start' => 2008],
                ['name' => 'Verna', 'body_type' => 'sedan', 'year_start' => 2006],
                ['name' => 'Grand i10', 'body_type' => 'hatchback', 'year_start' => 2013],
            ],
            'Tata' => [
                ['name' => 'Nexon', 'body_type' => 'suv', 'year_start' => 2017],
                ['name' => 'Harrier', 'body_type' => 'suv', 'year_start' => 2019],
                ['name' => 'Safari', 'body_type' => 'suv', 'year_start' => 1998],
                ['name' => 'Tiago', 'body_type' => 'hatchback', 'year_start' => 2016],
                ['name' => 'Punch', 'body_type' => 'suv', 'year_start' => 2021],
            ],
            'Mahindra' => [
                ['name' => 'Scorpio', 'body_type' => 'suv', 'year_start' => 2002],
                ['name' => 'XUV700', 'body_type' => 'suv', 'year_start' => 2021],
                ['name' => 'Thar', 'body_type' => 'suv', 'year_start' => 2010],
                ['name' => 'Bolero', 'body_type' => 'suv', 'year_start' => 2001],
            ],
            'Honda' => [
                ['name' => 'City', 'body_type' => 'sedan', 'year_start' => 1998],
                ['name' => 'Amaze', 'body_type' => 'sedan', 'year_start' => 2013],
                ['name' => 'WR-V', 'body_type' => 'suv', 'year_start' => 2017],
                ['name' => 'Civic', 'body_type' => 'sedan', 'year_start' => 2006],
            ],
        ];

        foreach ($modelsData as $makeName => $models) {
            $make = Make::where('name', $makeName)->first();
            
            if ($make) {
                foreach ($models as $modelData) {
                    CarModel::create(array_merge(['make_id' => $make->id], $modelData));
                }
            }
        }
    }

    
}
