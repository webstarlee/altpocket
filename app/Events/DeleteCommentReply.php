<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteCommentReply implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     protected $userid;
     protected $replyid;

    public function __construct($replyid, $userid)
    {
        $this->replyid = $replyid;
        $this->userid = $userid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('deleteReplyEvent');
    }

    public function broadcastWith()
    {
        return [
            'replyId' => $this->replyid,
            'userId' => $this->userid,
        ];
    }

    public function broadcastAs()
    {
        return 'deleteReplyListner';
    }
}
