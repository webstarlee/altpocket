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
use App\User;

class Notification implements ShouldBroadcastNow
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
     protected $avatar;
     protected $senderid;
     protected $username;


    public function __construct($message, $category, $userid, $sender)
    {
        $this->message = $message;
        $this->userid = $userid;
        $this->category = $category;
        $this->sender = $sender;

        $user = User::where('id', $sender)->select('id', 'avatar', 'username')->first();

        $this->senderid = $user->id;
        $this->avatar = $user->avatar;
        $this->username = $user->username;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('test-weoo');
    }

    public function broadcastWith()
    {
    return [
      'value' => $this->username . " " . $this->message,
      'category' => $this->category,
      'sender' => $this->senderid,
      'avatar' => $this->username,
      'userid' => $this->userid,
      'username' => $this->username

          ];
    }

    public function broadcastAs()
    {
        return 'Notification';
    }

}
