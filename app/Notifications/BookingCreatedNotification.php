<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tourist_name' => $this->booking->user->name,
            'listing_location' => $this->booking->annonce->location,
            'start_date' => $this->booking->start_date->format('Y-m-d'),
            'end_date' => $this->booking->end_date->format('Y-m-d'),
            'total_price' => $this->booking->total_price,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id, // Include notification ID for marking as read
            'tourist_name' => $this->booking->user->name,
            'listing_location' => $this->booking->annonce->location,
            'start_date' => $this->booking->start_date->format('Y-m-d'),
            'end_date' => $this->booking->end_date->format('Y-m-d'),
            'total_price' => $this->booking->total_price,
        ]);
    }

    /**
     * Get the broadcast channel for the notification.
     */
    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\Channel('proprietaire.' . $this->booking->annonce->user_id);
    }
}