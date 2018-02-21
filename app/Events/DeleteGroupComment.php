<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteGroupComment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $groupCommentid;
    protected $userid;

    public function __construct($groupCommentid, $userid)
    {
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
        return new Channel('deleteGroupCommentEvent');
    }

    public function broadcastWith()
    {
        return [
            'commentId' => $this->groupCommentid,
            'userId' => $this->userid
        ];
    }

    public function broadcastAs()
    {
        return 'deleteGroupCommentListner';
    }
}
