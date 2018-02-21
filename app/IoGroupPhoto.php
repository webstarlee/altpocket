<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupPhoto extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_photos';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'group_id', 'user_id', 'photo'
    ];
}
