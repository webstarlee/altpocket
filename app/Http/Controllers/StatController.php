<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
class StatController extends Controller
{
    public function index()
    {
      return view('stats.index');
    }

    public function getUsers()
    {
      $result = DB::select(DB::raw("select count(id), DATE_FORMAT(created_at, '%Y-%m-%d') as created_at from users group by DATE_FORMAT(created_at, '%Y-%m-%d')"));
      $days = array();
      $x= 0;
      foreach ($result as $user){
         $days[$x]["TimeStamp"] = $user->created_at;
          $v = key($user);
          $days[$x]["Users"] = $user->$v;
          $x++;
      }
     return $days;
    }


}
