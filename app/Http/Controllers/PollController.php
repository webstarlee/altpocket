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
use App\Events\Newstatus as NewStatusEvent;
use App\Events\Editstatus;
use App\Events\Deletestatus;
use App\Events\NewVote;

class PollController extends Controller
{
    public function createPoll(Request $request) {
        if ($request->get('question') && $request->get('answers')) {
            $poll = new Poll;
            $poll->userid = Auth::user()->id;
            $poll->question = $request->get('question');
            $poll->save();
            foreach ($request->answers as $answer) {
                $new_answer = new PollAnswer;
                $new_answer->pollid = $poll->id;
                $new_answer->answer = $answer;
                $new_answer->save();
            }

            $status = new Status;
            $status->userid = Auth::user()->id;
            $status->status = $poll->id;
            if(Auth::user()->created_at > date('Y-m-d H:i:s', time()-604800) && Auth::user()->accepted_posts < 3){
                $status->moderate = "yes";
            }
            $status->type = "poll";
            $status->save();
            event(new NewStatusEvent($status->id, $status->userid));
            return "success";
        } else {
            return "fail";
        }
    }

    public function votePoll($id) {
        $answer = PollAnswer::find($id);
        $vote = PollVote::where([['userid', '=', Auth::user()->id], ['pollid', '=', $answer->pollid]])->first();
        $status = Status::where('status', $answer->pollid)->first();

        if($vote) {
            $vote->delete();
        }

        $vote = new PollVote;
        $vote->userid = Auth::user()->id;
        $vote->pollid = $answer->pollid;
        $vote->answerid = $id;
        $vote->save();
        event(new NewVote($status->id));
        return $status;
    }

    public function get_push_data($id) {
        $status = Status::find($id);
        if ($status) {
            $user = User::where('id', $status->userid)->select('username', 'avatar', 'id')->first();
            $likers = $status->getLikers();
            $poll = Poll::where('id', $status->status)->first();
            $pollanswers = PollAnswer::where('pollid', $poll->id)->get();
            $votecount = PollVote::where('pollid', $poll->id)->count();
            $final_vote_data = array();
            $voute_answers = array();
            foreach ($pollanswers as $answer) {
                $votes = PollVote::where('answerid', $answer->id)->get();
                if($votecount != 0){
                    $percentvote = (100 / $votecount) * count($votes);
                } else {
                    $percentvote = 0;
                }
                $vote_users = array();
                $count = 0;
                foreach ($votes as $vote) {
                    $user = $vote->getVoter();
                    $count++;
                    array_push($vote_users, $user);
                }
                $voute_answers[] = array('answer_id' => $answer->id, 'vote_percent' => number_format(min($percentvote,100)), 'vote_count' => $count, 'vote_users' => $vote_users);
            }
            $final_vote_data = array('status_id' => $status->id, 'poll_id' => $poll->id, 'poll_answers' => $voute_answers);
            return $final_vote_data;
        }
        return "fail";
    }

    public function deletePoll($id, $statusid){
        $poll = Poll::where('id', $id)->first();
        $pollanswers = PollAnswer::where('pollid', $id)->get();
        $pollvotes = PollVote::where('pollid', $id)->get();
        $status = Status::where('id', $statusid)->first();

        if($status->userid == Auth::user()->id || Auth::user()->hasRights()) {
            // Delete status
            $status->delete();
            event(new Deletestatus($status->id, $status->userid));
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

            return "success";
        }
        return "fail";
    }
}
