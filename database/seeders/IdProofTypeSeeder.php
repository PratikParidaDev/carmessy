<?php

namespace Database\Seeders;

use App\Models\IdProofType;
use Illuminate\Database\Seeder;

class IdProofTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idProofTypes = [
            [
                'name' => 'Aadhar',
                'slug' => 'aadhar',
                'display_name' => 'Aadhar Card',
                'sort_order' => 1,
                'is_active' => true,
                'description' => 'Aadhar Card as ID proof',
            ],
            [
                'name' => 'Driving License',
                'slug' => 'driving-license',
                'display_name' => 'Driving License',
                'sort_order' => 2,
                'is_active' => true,
                'description' => 'Driving License as ID proof',
            ],
            [
                'name' => 'Passport',
                'slug' => 'passport',
                'display_name' => 'Passport',
                'sort_order' => 3,
                'is_active' => true,
                'description' => 'Passport as ID proof',
            ],
        ];

        foreach ($idProofTypes as $idProofType) {
            IdProofType::updateOrCreate(
                ['slug' => $idProofType['slug']],
                $idProofType
            );
        }
    }
}

