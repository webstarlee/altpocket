<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Cache;
use App\User;
class StatusComment extends Model
{
    use CanBeLiked;


    public function getLikes()
    {
      $likes = Cache::rememberForever('commentLikes:'.$this->id, function()
      {
        return count($this->likers()->select('id')->get());
      });
      return $likes;
    }

    public function getPoster()
    {
      $user = Cache::remember('userData:'.$this->userid, 1440, function()
      {
          return User::where('id', $this->userid)->select('username', 'avatar', 'id', 'name')->first();
      });

      return $user;
    }
}
