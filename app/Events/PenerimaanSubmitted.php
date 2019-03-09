<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Penerimaan;

class PenerimaanSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $penerimaan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Penerimaan $penerimaan)
    {
        $this->penerimaan = $penerimaan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
