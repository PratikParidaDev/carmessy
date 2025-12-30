<?php

namespace App\Events;

use App\Models\Car;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $car;

    /**
     * Create a new event instance.
     */
    public function __construct(Car $car)
    {
        $this->car = $car->load(['make', 'model', 'city', 'dealer.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to a public channel for admin dashboard
        // Also broadcast to a private channel for the car owner
        return [
            new Channel('car-status-updates'),
            new PrivateChannel('user.' . $this->car->dealer->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'car.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'car_id' => $this->car->id,
            'status' => $this->car->status,
            'title' => $this->car->title,
            'make' => $this->car->make->name ?? null,
            'model' => $this->car->model->name ?? null,
            'price' => $this->car->price,
            'published_at' => $this->car->published_at?->toIso8601String(),
            'updated_at' => $this->car->updated_at->toIso8601String(),
        ];
    }
}

