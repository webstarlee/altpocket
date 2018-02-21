<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Watchlist;
use App\Crypto;
use Auth;
use Redirect;

class WatchlistController extends Controller
{
    public function create(Request $request)
    {
      $token = Crypto::where('id', $request->get('token'))->first();
      $watchlist = Watchlist::where([['userid', '=', Auth::user()->id], ['token', '=', $token->symbol]])->first();

      if($watchlist)
      {
        return Redirect::back();
      } else {
        $watchlist = new Watchlist;
        $watchlist->userid = Auth::user()->id;
        $watchlist->token = $token->symbol;
        $watchlist->token_cmc_id = $token->cmc_id;
        $watchlist->token_name = $token->name;
        $watchlist->save();
        return Redirect::back();
      }
    }

    public function delete($id)
    {
      $watchlist = Watchlist::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->first();

      if($watchlist)
      {
        $watchlist->delete();
      }
      return Redirect::back();
    }
}
