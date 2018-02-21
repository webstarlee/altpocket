<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupRequest extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_requests';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'group_id', 'user_id', 'method'
    ];
}
