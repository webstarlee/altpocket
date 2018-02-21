<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TestEvent extends Event implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct()
    {
        $this->data = array(
            'power'=> '10'
        );
    }

    public function broadcastWith()
    {
    return [
            'power'=> '10'
          ];
    }

    public function broadcastOn()
    {
        return new Channel("test-channel");
    }

    public function broadcastAs()
    {
        return 'Notification';
    }
}
