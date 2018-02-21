<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewCommentReply implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     protected $commentid;
     protected $userid;
     protected $replyid;

    public function __construct($replyid, $userid, $commentid)
    {
        $this->replyid = $replyid;
        $this->userid = $userid;
        $this->commentid = $commentid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('newReplyEvent');
    }

    public function broadcastWith()
    {
        return [
            'replyId' => $this->replyid,
            'userId' => $this->userid,
            'commentId' => $this->commentid,
        ];
    }

    public function broadcastAs()
    {
        return 'newReplyListner';
    }
}
