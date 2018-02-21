<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use App\User;
use Cache;

class Answer extends Model
{
    public function ago()
    {
      return $this->created_at->diffForHumans();
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
