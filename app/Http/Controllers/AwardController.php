<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\awarded;
use Redirect;
use Alert;
use DB;


class AwardController extends Controller
{
    public function giveAward($username, Request $request){
        if(Auth::user()->isFounder() || Auth::user()->isAdmin() || Auth::user()->username == "Victor")
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

    public function discordAward()
    {
      $user = Auth::user();

      if(!awarded::where([['userid', '=', $user->id], ['award_id', '=', '17']])->exists())
      {
        $award = new awarded;
        $award->userid = $user->id;
        $award->award_id = 17;
        $award->reason = "Thank you for joining the discord server!";
        $award->save();

        Alert::success('You have now been awarded with the Discord badge!', 'Thank you!');
        return redirect('/');
      }
    }


    public function removeGroup($userid, $id)
    {
      if(Auth::user()->hasRights())
      {
        DB::table('user_group')->where([['user_id', '=', $userid], ['group_id', '=', $id]])->delete();
        Alert::success('Usergroup removed from user.', 'Success');
        return Redirect::back();
      } else {
        return Redirect::back();
      }
    }

    public function setGroup($username, Request $request)
    {
      if(Auth::user()->hasRights() || Auth::user()->username == "Victor")
      {
          $user = User::where('username', $username)->first();

          DB::table('user_group')->insert(
            ['user_id' => $user->id, 'group_id' => $request->get('group')]
          );

          Alert::success('Usergroup successfully set!', 'Success');
          return Redirect::back();
      }
    }
}
