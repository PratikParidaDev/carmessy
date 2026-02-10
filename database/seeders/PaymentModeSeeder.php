<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use Illuminate\Database\Seeder;

class PaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentModes = [
            [
                'name' => 'Cash',
                'slug' => 'cash',
                'display_name' => 'Cash Payment',
                'sort_order' => 1,
                'is_active' => true,
                'description' => 'Payment via cash',
            ],
            [
                'name' => 'Online',
                'slug' => 'online',
                'display_name' => 'Online Payment',
                'sort_order' => 2,
                'is_active' => true,
                'description' => 'Payment via online methods (UPI, Card, Net Banking)',
            ],
        ];

        foreach ($paymentModes as $paymentMode) {
            PaymentMode::updateOrCreate(
                ['slug' => $paymentMode['slug']],
                $paymentMode
            );
        }
    }
}
