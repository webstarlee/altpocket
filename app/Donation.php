<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;
use App\User;

class Donation extends Model
{
  public function getDonator()
  {
    $user = Cache::remember('userDataDonation:'.$this->userid, 1440, function()
    {
        return User::where('id', $this->userid)->select('username', 'avatar', 'id', 'name', 'public')->first();
    });

    return $user;
  }
}
