<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupPollAnswer extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_poll_answers';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'pollid', 'answer'
    ];
}
