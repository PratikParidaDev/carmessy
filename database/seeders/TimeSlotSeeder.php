<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeSlots = [
            ['name' => '09:00 AM - 11:00 AM', 'start_time' => '09:00', 'end_time' => '11:00', 'sort_order' => 1, 'is_active' => true],
            ['name' => '11:00 AM - 01:00 PM', 'start_time' => '11:00', 'end_time' => '13:00', 'sort_order' => 2, 'is_active' => true],
            ['name' => '01:00 PM - 03:00 PM', 'start_time' => '13:00', 'end_time' => '15:00', 'sort_order' => 3, 'is_active' => true],
            ['name' => '03:00 PM - 05:00 PM', 'start_time' => '15:00', 'end_time' => '17:00', 'sort_order' => 4, 'is_active' => true],
            ['name' => '05:00 PM - 07:00 PM', 'start_time' => '17:00', 'end_time' => '19:00', 'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($timeSlots as $slot) {
            TimeSlot::updateOrCreate(
                ['name' => $slot['name']],
                $slot
            );
        }
    }
}
