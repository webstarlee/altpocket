<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Poll;
use App\PollAnswer;
use App\PollVote;
use App\User;
use App\Status;
use Redirect;
use Alert;
class PollController extends Controller
{
    public function createPoll(Request $request)
    {
      $poll = new Poll;
      $poll->userid = Auth::user()->id;
      $poll->question = $request->get('question');
      $poll->save();

      if($request->get('answer1'))
      {
        $answer = new PollAnswer;
        $answer->pollid = $poll->id;
        $answer->answer = $request->get('answer1');
        $answer->save();
      }
      if($request->get('answer2'))
      {
        $answer = new PollAnswer;
        $answer->pollid = $poll->id;
        $answer->answer = $request->get('answer2');
        $answer->save();
      }
      if($request->get('answer3'))
      {
        $answer = new PollAnswer;
        $answer->pollid = $poll->id;
        $answer->answer = $request->get('answer3');
        $answer->save();
      }
      if($request->get('answer4'))
      {
        $answer = new PollAnswer;
        $answer->pollid = $poll->id;
        $answer->answer = $request->get('answer4');
        $answer->save();
      }

      $status = new Status;
      $status->userid = Auth::user()->id;
      $status->status = $poll->id;
      if(Auth::user()->created_at > date('Y-m-d H:i:s', time()-604800) && Auth::user()->accepted_posts < 3){
      $status->moderate = "yes";
      }
      $status->type = "poll";
      $status->save();

      return Redirect::back();
    }

    public function votePoll($id)
    {
      $answer = PollAnswer::where('id', $id)->first();
      $vote = PollVote::where([['userid', '=', Auth::user()->id], ['pollid', '=', $answer->pollid]])->first();

      if($vote)
      {
        $vote->delete();
      }

      $vote = new PollVote;
      $vote->userid = Auth::user()->id;
      $vote->pollid = $answer->pollid;
      $vote->answerid = $id;
      $vote->save();
    }
    public function deletePoll($id, $statusid)
    {
      $poll = Poll::where('id', $id)->first();
      $pollanswers = PollAnswer::where('pollid', $id)->get();
      $pollvotes = PollVote::where('pollid', $id)->get();
      $status = Status::where('id', $statusid)->first();

      if($status->userid == Auth::user()->id)
      {
        // Delete status
        $status->delete();
        // Delete options
        foreach($pollanswers as $answer)
        {
          $answer->delete();
        }
        //Delete votes
        foreach($pollvotes as $vote)
        {
          $vote->delete();
        }
        //Delete Poll
        $poll->delete();

        Alert::success('Your poll was deleted.', 'Poll deleted');
        return Redirect::back();
      }
    }
}
