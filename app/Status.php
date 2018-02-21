<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Cache;
use App\User;
use App\StatusComment;
use App\StatusAction;
use Auth;

class Status extends Model
{
    use CanBeLiked;


    public function getLikers()
    {
      $likers = Cache::rememberForever('statusLikers:'.$this->id, function()
      {
        return $this->likers()->select('avatar', 'username', 'id')->get();
      });
      return $likers;
    }

    public function getPoster()
    {
      $user = Cache::remember('userData:'.$this->userid, 1440, function()
      {
          return User::where('id', $this->userid)->select('username', 'avatar', 'id', 'name')->first();
      });

      return $user;
    }


    public function getComments()
    {
      $comments = Cache::rememberForever('statusComments:'.$this->id, function()
      {
        return StatusComment::where('statusid', $this->id)->get();
      });
      return $comments;
    }

    public function isHidden()
    {
      $hidden = Cache::remember(Auth::user()->id.":hidden:".$this->id, 1440, function(){
        return StatusAction::where([['userid', '=', Auth::user()->id], ['status', '=', $this->id], ['type', '=', 'hide']])->exists();
      });
      return $hidden;
    }
}
