<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteGroupPost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $groupid;
    protected $groupPostid;
    protected $userid;

    public function __construct($groupid, $groupPostid, $userid)
    {
        $this->groupid = $groupid;
        $this->groupPostid = $groupPostid;
        $this->userid = $userid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('deleteGroupPostEvent');
    }

    public function broadcastWith()
    {
        return [
            'groupId' => $this->groupid,
            'postId' => $this->groupPostid,
            'userId' => $this->userid
        ];
    }

    public function broadcastAs()
    {
        return 'deleteGroupPostListner';
    }
}
