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

class PushEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
     protected $userid;
     protected $category;
     protected $message;


    public function __construct($message, $category, $userid)
    {
        $this->message = $message;
        $this->userid = $userid;
        $this->category = $category;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.'.$this->userid);
    }

    public function broadcastWith()
    {
    return [
      'value' => $this->message,
      'category' => $this->category
          ];
    }

    public function broadcastAs()
    {
        return 'Illuminate\Notifications\Events\BroadcastNotificationCreated';
    }

}
