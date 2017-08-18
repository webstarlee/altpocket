<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Answer extends Model
{
    public function ago()
    {
      return $this->created_at->diffForHumans();
    }
}
