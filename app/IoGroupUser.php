<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupUser extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_users';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'group_id', 'user_id', 'user_level'
    ];
}
