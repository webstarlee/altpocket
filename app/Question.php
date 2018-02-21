<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Cache;

class Question extends Model
{
  public function getPoster()
  {
    $user = Cache::remember('userData:'.$this->userid, 1440, function()
    {
        return User::where('id', $this->userid)->select('username', 'avatar', 'id', 'name')->first();
    });

    return $user;
  }
}
