<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use Auth;
use Alert;
use Redirect;
use Validator;
use DB;
use App\ManualInvestment;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\Mining;
use App\Balance;
use App\PageView;
use Cache;

//Profile Stuff
use App\User;
use App\Investment;
use App\awarded;
use App\Notifications\NewComment;
use App\Notifications\NewThing;


class CommentController extends HomeController
{

    protected $comments;

    public function __construct(Comment $comments){
        $this->comments = $comments;
    }

    public function profile($username, Request $request){

        $comments = $this->comments->latest('created_at')->paginate(5);
        $user = User::where('username', $username)->first();


        if($request->ajax()){
            return view('module.comment', ['comments' => $comments])->render();
        }


        if($user != null)
        {
            if($user->public == "on")
            {
                return view('profile2', ['user' => $user, 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id), 'comments' => $comments]);

            } else
            {
                if(Auth::user())
                {
                    if(Auth::user()->username = $user->username)
                    {
                return view('profile2', ['user' => $user, 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id), 'comments' => Comment::where('userid', $user->id)->paginate(5)]);
                    } else
                    {
                        return view('private');
                    }
                } else
                {
                    return view('private');
                }

            }
        } else
        {
            return view('404');

        }

    }


  public function showProfile($username, Request $request)
    {
        $user = User::where('username', $username)->first();


        if($user && $user->public == "on")
        {

          if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
          }

          if(!PageView::where([['ip', '=', $_SERVER['REMOTE_ADDR']], ['userid', '=', $user->id]])->exists())
          {
            $pageview = new PageView;
            $pageview->userid = $user->id;
            $pageview->ip = $_SERVER['REMOTE_ADDR'];
            $pageview->save();

            $user->visits += 1;
            $user->save();
          }
        }

        if($user && $user->visits >= 200 && !awarded::where([['userid', '=', $user->id], ['award_id', '=', 14]])->exists())
        {
          $award = new awarded;
          $award->userid= $user->id;
          $award->award_id = 14;
          $award->reason = "Congratulations for recieving over 200 page views!";
          $award->save();
        }


        if($user){
            $comments = $this->comments->where([['userid', '=', $user->id]])->orderBy('created_at', 'desc')->paginate(5);
            $p_investments = PoloInvestment::where([['userid', '=', $user->id]])->get();
            $b_investments = BittrexInvestment::where([['userid', '=', $user->id]])->get();

            $m_investments = ManualInvestment::where([['userid', '=', $user->id]])->get();
        }

        if($request->ajax()){
            return view('module.comment', ['comments' => $comments])->render();
        }

        if($user != null){
            if(User::where('username', $username)->first()->public == "on"){
                if(Auth::user())
                {
                return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => $user->getNetWorthNew(Auth::user()->api), 'btc' => HomeController::btcUsd(), 'comments' => $comments, 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => Balance::where('userid', $user->id)->get(), 'n_minings' => Mining::where('userid', $user->id)->get()]);
              } else {
                return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => $user->getNetWorthNew('coinmarketcap'), 'btc' => HomeController::btcUsd(),'comments' => $comments, 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => Balance::where('userid', $user->id)->get(), 'n_minings' => Mining::where('userid', $user->id)->get()]);
              }

            } else {
                if(Auth::user()){
                    if($username == Auth::user()->username || Auth::user()->isFounder()){
                        return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => $user->getNetWorthNew(Auth::user()->api), 'btc' => HomeController::btcUsd(), 'comments' => $comments, 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => Balance::where('userid', $user->id)->get(), 'n_minings' => Mining::where('userid', $user->id)->get()]);
                    } else {
                    return view('private');
                    }
                } else {
                    return view('private');
                }
            }
        } else {
            return view('404');
        }
    }


    public function notify(){
        $users = User::all();
            $notification = [
                'icon' => 'fa fa-download',
                'title' => 'Importing fixed',
                'data' => 'Importing has been fixed and improved.',
                'type' => 'news'
            ];

        foreach($users as $user){
            $user->notify(new NewThing($notification));
        }

    }



    public function addComment($id, Request $request)
    {
        if(!Auth::user()->isMuted()){

        if(!Comment::where([['commenter', '=', Auth::id()], ['userid', '=', $id]])->whereRaw('created_at >= now() - interval 5 minute')->first()){

        $messages = [
                'comment.required' => 'Your comment can not be empty.',
                'comment.max'  => 'Your comment can max be 120 characters long.',
                'comment.min'  => 'Your comment must be atleast 10 characters long.'
        ];

        $rules = [
            'comment' => 'required|max:120|min:10|'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $errors = $validator->errors();

            if($validator->fails()){
                Alert::error($errors->first('comment'), 'Comment failed');
                return Redirect::back();
            }

            $text = preg_replace("/[\r\n]+/", "\n", $request->get('comment'));

            $comment = new Comment;
            $comment->userid = $id;
            $comment->commenter = Auth::id();
            $comment->comment = app('profanityFilter')->filter($text);
            $comment->save();

            // Get notify guy
            $notifiable = User::where('id', $id)->first();

            //Notification array
            $notification = [
                'icon' => 'fa fa-comment',
                'title' => 'New comment',
                'data' => 'You have a new comment on your profile.',
                'type' => 'comment'
            ];

                $notifiable->notify(new NewComment($notification));

            Alert::success('Your comment was successfully posted.', 'Comment posted');
            return Redirect::back();
           } else {
            Alert::error('You can only comment once per 10 minutes.', 'Comment failed');
            return Redirect::back();
        }
        } else {
            Alert::error('Due to abuse you have been muted.', 'Comment failed');
            return Redirect::back();
        }
    }


    public function editComment($id, Request $request){

        $messages = [
                'comment.required' => 'Your comment can not be empty.',
                'comment.max'  => 'Your comment can max be 120 characters long.',
                'comment.min'  => 'Your comment must be atleast 10 characters long.'
        ];

        $rules = [
            'comment' => 'required|max:120|min:10|'
            ];


        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = $validator->errors();

        if($validator->fails()){
            Alert::error($errors->first('comment'), 'Edit failed');
            return Redirect::back();
        }


        $comment = Comment::where('id', $id)->first();
            if($comment->commenter == Auth::user()->id){
                $text = preg_replace("/[\r\n]+/", "\n", $request->get('comment'));
            $comment->comment = app('profanityFilter')->filter($text);
            $comment->save();

        Alert::success('Your comment was successfully edited.', 'Comment edited');
        return Redirect::back();
        } else {
        Alert::error('You can not edit someone elses comment!', 'Edit failed');
        return Redirect::back();
        }
    }


    public function deleteComment($id){
        $comment = Comment::where('id', $id)->first();

        if(Auth::id() == $comment->userid || Auth::id() == $comment->commenter){
            $comment->delete();
            Alert::success('The comment was successfully deleted.', 'Comment deleted');
            return Redirect::back();
        }  else {
            Alert::error('You can not delete someone elses comment!', 'Delete failed');
            return Redirect::back();
        }
    }


    public function globalComment(){
        $users = User::all();

        foreach($users as $user){
            //Notification array
            $notification = [
                'icon' => 'fa fa-comment',
                'title' => 'New comment',
                'data' => 'You have a new comment on your profile.',
                'type' => 'comment'
            ];

            $user->notify(new NewComment($notification));


            $comment = new Comment;
            $comment->userid = $user->id;
            $comment->commenter = 1;
            $comment->comment = "There are now comments on profiles! Enjoy!";
            $comment->save();




        }


    }


}
