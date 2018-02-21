<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroupPost extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_posts';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'group_id', 'user_id', 'photo_ids', 'status', 'discription', 'type'
    ];
}
