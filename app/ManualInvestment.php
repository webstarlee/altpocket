<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManualInvestment extends Model
{
    protected $dates = ['created_at', 'updated_at', 'date_bought'];
}
