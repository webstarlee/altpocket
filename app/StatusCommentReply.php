<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Cache;
use App\User;

class StatusCommentReply extends Model
{
    use CanBeLiked;
    
    protected $table = 'status_comment_replies';

    public function getLikes()
    {
      $likes = Cache::rememberForever('replyLikes:'.$this->id, function() {
        return count($this->likers()->select('id')->get());
      });
      return $likes;
    }
}
