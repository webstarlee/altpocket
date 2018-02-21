<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Editstatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $statusid;
    protected $userid;

    public function __construct($statusid, $userid)
    {
        $this->statusid = $statusid;
        $this->userid = $userid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('editStatusEvent');
    }

    public function broadcastWith()
    {
        return [
            'statusId' => $this->statusid,
            'userId' => $this->userid
        ];
    }

    public function broadcastAs()
    {
        return 'editStatusListner';
    }
}
