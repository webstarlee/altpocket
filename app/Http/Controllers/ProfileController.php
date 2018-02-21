<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Models
use App\User;

//Functionality
use Auth;

class ProfileController extends Controller
{





    public function index($username)
    {
      $user = User::where('username', $username)->first();



      return view('profile.index', ['user' => $user]);
    }


    public function searchUsers(Request $request)
    {
      $users = User::where('username', 'LIKE', '%'.$request->get('query').'%')->select('username', 'avatar', 'id')->get()->take(10);

      return view('module.search', ['users' => $users]);
    }



}
