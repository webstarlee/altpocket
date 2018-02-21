<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewGroupComment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $groupPostid;
    protected $groupCommentid;
    protected $userid;

    public function __construct($groupPostid, $groupCommentid, $userid)
    {
        $this->groupPostid = $groupPostid;
        $this->groupCommentid = $groupCommentid;
        $this->userid = $userid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('newGroupCommentEvent');
    }

    public function broadcastWith()
    {
        return [
            'postId' => $this->groupPostid,
            'commentId' => $this->groupCommentid,
            'userId' => $this->userid
        ];
    }

    public function broadcastAs()
    {
        return 'newGroupCommentListner';
    }
}
