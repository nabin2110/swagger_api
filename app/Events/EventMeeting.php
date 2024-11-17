<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventMeeting implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $meeting;
    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('meeting-name'),
        ];
    }
    public function broadcastWith()
    {
        return [
            'id' => $this->meeting->id,
            'title' => $this->meeting->title,
            'start' => $this->meeting->start,
            'end' => $this->meeting->end,
            'type' => $this->meeting->wasRecentlyCreated ? 'add' : ($this->meeting->wasChanged() ? 'update' : 'delete'),
        ];
    }

    public function broadcastAs()
    {
        return 'meeting-event';
    }
}
