<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Cache;
use App\User;

class IogGroupComment extends Model
{
    use CanBeLiked;
    
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_comments';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'group_id', 'post_id', 'user_id', 'comment'
    ];

    public function getLikes(){
      $likes = Cache::rememberForever('goroupCommentLikes:'.$this->id, function()
      {
        return count($this->likers()->select('id')->get());
      });
      return $likes;
    }

}
