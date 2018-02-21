<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\IoGroup;
use App\IoGroupPost;
use App\IoGroupPoll;
use App\IoGroupPollVote;
use App\IoGroupPollAnswer;
use Illuminate\Http\Request;
use App\Events\NewGroupPost;
use App\Events\NewGroupVote;

class IoGroupPollController extends Controller
{
    public function store(Request $request) {
        $group = IoGroup::find($request->group_id_for_poll);
        if ($group) {
            if ($request->get('question') && $request->get('answers')) {
                $poll = new IoGroupPoll;
                $poll->userid = Auth::user()->id;
                $poll->question = $request->get('question');
                $poll->save();

                foreach ($request->answers as $answer) {
                    $new_answer = new IoGroupPollAnswer;
                    $new_answer->pollid = $poll->id;
                    $new_answer->answer = $answer;
                    $new_answer->save();
                }

                $group_post = new IoGroupPost;
                $group_post->user_id = Auth::user()->id;
                $group_post->group_id = $group->id;
                $group_post->poll = 1;
                $group_post->poll_id = $poll->id;
                $group_post->save();

                event(new NewGroupPost($group->id, $group_post->id, $group_post->user_id));
                return "success";
            } else {
                return "fail";
            }
        }
    }

    public function votePoll($id) {
        $answer = IoGroupPollAnswer::find($id);
        $vote = IoGroupPollVote::where([['userid', '=', Auth::user()->id], ['pollid', '=', $answer->pollid]])->first();
        $post = IoGroupPost::where('poll_id', $answer->pollid)->first();

        if($vote) {
            $vote->delete();
        }

        $vote = new IoGroupPollVote;
        $vote->userid = Auth::user()->id;
        $vote->pollid = $answer->pollid;
        $vote->answerid = $id;
        $vote->save();

        event(new NewGroupVote($post->id));
        return "success";
    }

    public function get_push_data($id) {
        $post = IoGroupPost::find($id);
        if ($post) {
            $user = User::where('id', $post->user_id)->select('username', 'avatar', 'id')->first();
            $poll = IoGroupPoll::find($post->poll_id);
            $pollanswers = IoGroupPollAnswer::where('pollid', $poll->id)->get();
            $votecount = IoGroupPollVote::where('pollid', $poll->id)->count();
            $final_vote_data = array();
            $voute_answers = array();
            foreach ($pollanswers as $answer) {
                $votes = IoGroupPollVote::where('answerid', $answer->id)->get();
                if($votecount != 0){
                    $percentvote = (100 / $votecount) * count($votes);
                } else {
                    $percentvote = 0;
                }
                $vote_users = array();
                $count = 0;
                foreach ($votes as $vote) {
                    $user = User::find($vote->userid);
                    $count++;
                    array_push($vote_users, $user);
                }
                $voute_answers[] = array('answer_id' => $answer->id, 'vote_percent' => number_format(min($percentvote,100)), 'vote_count' => $count, 'vote_users' => $vote_users);
            }
            $final_vote_data = array('post_id' => $post->id, 'poll_id' => $poll->id, 'poll_answers' => $voute_answers);
            return $final_vote_data;
        }
        return "fail";
    }
}
