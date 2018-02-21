<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupPoll extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_polls';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'userid', 'question'
    ];
}
