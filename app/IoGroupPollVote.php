<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupPollVote extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_poll_votes';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'userid', 'answerid', 'pollid'
    ];
}
