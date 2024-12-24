<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DrawingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tool;
    public $updateData;
    public $origin;

    /**
     * Create a new event instance.
     */
    public function __construct( $tool, $updateData = null, $origin = null )
    {
        $this->tool       = $tool;
        $this->updateData = $updateData;
        $this->origin     = $origin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel( 'drawing' ),
        ];
    }
}
