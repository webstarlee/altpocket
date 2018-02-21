<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Cache;
use App\User;

class IoGroupReply extends Model
{
    use CanBeLiked;

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'io_group_comment_replies';
    /**
     * [$fillable description]
     * @var [type]
     */
    protected $fillable = [
        'user_id', 'comment_id', 'reply'
    ];

    public function getLikes(){
      $likes = Cache::rememberForever('goroupReplyLikes:'.$this->id, function()
      {
        return count($this->likers()->select('id')->get());
      });
      return $likes;
    }
}
