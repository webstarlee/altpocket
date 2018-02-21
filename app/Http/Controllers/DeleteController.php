<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Alert;
use Redirect;
use DB;
use App\Answer;
use App\Balance;
use App\BittrexInvestment;
use App\BittrexTrade;
use App\BlogComment;
use App\awarded;
use App\Deposit;
use App\Impressed;
use App\Key;
use App\Investment;
use App\Mining;
use App\Poll;
use App\PollVote;
use App\PoloInvestment;
use App\PoloTrade;
use App\Question;
use App\Reply;
use App\Status;
use App\StatusComment;
use App\Trade;
use App\User;
use App\Withdraw;
use App\ManualInvestment;
use App\Tracking;
use App\Delete;


class DeleteController extends Controller
{
    public function domoArigato(Request $request)
    {
      // :(
      $user = Auth::user();
      $id = $user->id;


      if(Hash::check($request->get('currentpwd'), Auth::user()->getAuthPassword())){
      //Answers
      $answers = Answer::where('userid', $id)->get();
      foreach($answers as $answer)
      {
        $answer->delete();
      }

      //Awarded
      $awarded = awarded::where('userid', $id)->get();
      foreach($awarded as $awarded)
      {
        $awarded->delete();
      }

      //Balances
      $balances = Balance::where('userid', $id)->get();
      foreach($balances as $balance)
      {
        $balance->delete();
      }

      //Balances
      $bittrexinvestments = BittrexInvestment::where('userid', $id)->get();
      foreach($bittrexinvestments as $bittrex)
      {
        $bittrex->delete();
      }

      //Balances
      $bittrextrades = BittrexTrade::where('userid', $id)->get();
      foreach($bittrextrades as $bittrex)
      {
        $bittrex->delete();
      }

      //Blog Comments
      $blogcomments = BlogComment::where('userid', $id)->get();
      foreach($blogcomments as $blogcomment)
      {
        $blogcomment->delete();
      }

      //Deposits
      $deposits = Deposit::where('userid', $id)->get();
      foreach($blogcomments as $deposit)
      {
        $deposit->delete();
      }

      DB::table('followables')->where('user_id', $id)->delete();

      $investments = Investment::where('userid', $id)->get();
      foreach($investments as $investment)
      {
        $investment->delete();
      }

      $investments = Investment::where('userid', $id)->get();
      foreach($investments as $investment)
      {
        $investment->delete();
      }

      $keys = Key::where('userid', $id)->get();
      foreach($keys as $key)
      {
        $key->delete();
      }

      $manualinvestments = ManualInvestment::where('userid', $id)->get();
      foreach($manualinvestments as $manualinvestment)
      {
        $manualinvestment->delete();
      }

      $minings = Mining::where('userid', $id)->get();
      foreach($minings as $mining)
      {
        $mining->delete();
      }

      $polls = Poll::where('userid', $id)->get();
      foreach($polls as $poll)
      {
        $poll->delete();
      }

      $pollvotes = PollVote::where('userid', $id)->get();
      foreach($pollvotes as $pollvote)
      {
        $pollvote->delete();
      }

      $poloinvestments = PoloInvestment::where('userid', $id)->get();
      foreach($poloinvestments as $poloinvestment)
      {
        $poloinvestment->delete();
      }

      $polotrades = PoloTrade::where('userid', $id)->get();
      foreach($polotrades as $polotrade)
      {
        $polotrade->delete();
      }

      $questions = Question::where('userid', $id)->get();
      foreach($questions as $question)
      {
        $question->delete();
      }

      $replies = Reply::where('userid', $id)->get();
      foreach($replies as $replie)
      {
        $replie->delete();
      }

      $statuses = Status::where('userid', $id)->get();
      foreach($statuses as $statuse)
      {
        $statuse->delete();
      }

      $statuscomments = StatusComment::where('userid', $id)->get();
      foreach($statuscomments as $statuscomment)
      {
        $statuscomment->delete();
      }

      $trackings = Tracking::where('userid', $id)->get();
      foreach($trackings as $tracking)
      {
        $tracking->delete();
      }

      $trades = Trade::where('userid', $id)->get();
      foreach($trades as $trade)
      {
        $trade->delete();
      }

      $withdraws = Withdraw::where('userid', $id)->get();
      foreach($withdraws as $withdraw)
      {
        $withdraw->delete();
      }

      $email = $user->email;

      $delete = new Delete;
      $delete->userid = $user->id;
      $delete->why = $request->get('why');
      $delete->improve = $request->get('improve');
      if($request->get('updates')){
        $delete->email = $email;
      }
      $delete->save();

      $user->delete();
      return Redirect::back();
    } else {
      Alert::error('Your current password was wrong!', 'Failed');
      return Redirect::back();
    }
    }
}
