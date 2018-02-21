<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteGroupCommentReply implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $groupReplyid;
    protected $userid;

    public function __construct($groupReplyid, $userid)
    {
        $this->groupReplyid = $groupReplyid;
        $this->userid = $userid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('deleteGroupCommentReplyEvent');
    }

    public function broadcastWith()
    {
        return [
            'replyId' => $this->groupReplyid,
            'userId' => $this->userid
        ];
    }

    public function broadcastAs()
    {
        return 'deleteGroupCommentReplyListner';
    }
}
