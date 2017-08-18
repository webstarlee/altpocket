<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shoutbox extends Model
{
  protected $table = 'shoutbox';
  protected $fillable = ['user', 'message', 'mentions'];

public function poster()
{
  return $this->belongsTo('App\User', 'user');
}
}
