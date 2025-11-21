<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Car;
use App\Notifications\NewCarMatchAlert;
use Illuminate\Support\Facades\Notification;

class AlertService
{
    public function checkAlertsForCar(Car $car): void
    {
        $alerts = Alert::where('is_active', true)
            ->get();

        foreach ($alerts as $alert) {
            if ($alert->matchesCar($car)) {
                $alert->user->notify(new NewCarMatchAlert($car, $alert));
                $alert->update(['last_notified_at' => now()]);
            }
        }
    }

    public function processNewCar(Car $car): void
    {
        if ($car->status === 'approved' && $car->published_at) {
            $this->checkAlertsForCar($car);
        }
    }
}
