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

class MessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     protected $message;
     protected $channel;
     protected $sender;
     protected $receiver;
     protected $channels;
     protected $avatar;



    public function __construct($message, $sender, $receiver, $avatar)
    {
      $this->message = $message;
      $this->sender = $sender;
      $this->receiver = $receiver;
      $this->channels = ['private-App.User.'.$receiver, 'private-App.User.'.$sender];
      $this->avatar = $avatar;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return $this->channels;
    }

    public function broadcastWith()
    {
    return [
      'message' => $this->message,
      'sender' => $this->sender,
      'timestamp' => date('Y-m-d H:i:s', strtotime(\Carbon\Carbon::now())),
      'avatar' => $this->avatar
          ];
    }

    public function broadcastAs()
    {
        return 'PrivateMessage';
    }
}
