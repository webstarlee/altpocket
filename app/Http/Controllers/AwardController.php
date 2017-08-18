<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\awarded;
use Redirect;
use Alert;


class AwardController extends Controller
{
    public function giveAward($username, Request $request){
        if(Auth::user()->isFounder() || Auth::user()->isAdmin())
        {
            $user = User::where('username', $username)->first();
            
            $awarded = new awarded;
            $awarded->userid = $user->id;
            $awarded->award_id = $request->get('award');
            $awarded->reason = $request->get('reason');
            $awarded->save();
            
            Alert::success('User successfully awarded.', 'User Awarded');
            return Redirect::back();
        }
    }
}
