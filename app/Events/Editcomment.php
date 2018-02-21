<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Editcomment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $commentid;
    protected $userid;
    protected $statusid;

    public function __construct($commentid, $userid, $statusid)
    {
        $this->commentid = $commentid;
        $this->userid = $userid;
        $this->statusid = $statusid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('editCommentEvent');
    }

    public function broadcastWith()
    {
        return [
            'commentId' => $this->commentid,
            'userId' => $this->userid,
            'statusId' => $this->statusid,
        ];
    }

    public function broadcastAs()
    {
        return 'editCommentListner';
    }
}
