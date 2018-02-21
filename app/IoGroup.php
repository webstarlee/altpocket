<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IoGroup extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_groups';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'name', 'description', 'image', 'url', 'user_id', 'private', 'conversation_id', 'extra_info', 'settings'
    ];
    
}
