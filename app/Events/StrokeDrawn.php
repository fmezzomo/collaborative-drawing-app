<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StrokeDrawn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $strokeData;

    public function __construct($strokeData)
    {
        $this->strokeData = $strokeData;
    }

    public function broadcastOn()
    {
        return new Channel('canvas.' . $this->strokeData['canvas_id']);
    }

    public function broadcastWith()
    {
        return $this->strokeData;
    }
}
