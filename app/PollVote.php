<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\User;
use App\StatusComment;

class PollVote extends Model
{
  public function getVoter()
  {
    $user = Cache::remember('userData:'.$this->userid, 1440, function()
    {
        return User::where('id', $this->userid)->select('username', 'avatar', 'id')->first();
    });

    return $user;
  }
}
