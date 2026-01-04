<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            CitySeeder::class,
            SuperAdminSeeder::class, // Super Admin must be seeded first
            AdminSeeder::class, // Admin must be seeded second
            UserSeeder::class,
            MakeSeeder::class,
            ModelSeeder::class,
            PlanSeeder::class,
            DealerSeeder::class,
            CarSeeder::class,
            FeatureSeeder::class,
            SafetyFeatureSeeder::class,
        ]);

    }
}
