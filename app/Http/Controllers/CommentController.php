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


        if($user != null){
          $p_investments = Cache::remember('p_investments'.$user->id, 60, function() use ($user)
          {
            return PoloInvestment::where([['userid', '=', $user->id], ['amount', '>', 0]])->get();
          });

          $b_investments = Cache::remember('b_investments'.$user->id, 60, function() use ($user)
          {
            return BittrexInvestment::where([['userid', '=', $user->id], ['amount', '>', 0]])->get();
          });

          $m_investments = Cache::remember('m_investments'.$user->id, 60, function() use ($user)
          {
            return ManualInvestment::where([['userid', '=', $user->id], ['amount', '>', 0]])->get();
          });

          $balance = Cache::remember('balances'.$user->id, 60, function() use ($user)
          {
            return Balance::where([['userid', '=', $user->id], ['amount', '>', '0.00001']])->get();
          });
        }

        if($user != null){
            if($user->public == "on"){
                if(Auth::check())
                {
                return view('profile', ['user' => $user, 'networth' => $user->getNetWorthNew(Auth::user()->api), 'btc' => HomeController::btcUsd(), 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => $balance, 'n_minings' => Mining::where('userid', $user->id)->get()]);
              } else {
                return view('profile', ['user' => $user, 'networth' => $user->getNetWorthNew('coinmarketcap'), 'btc' => HomeController::btcUsd(), 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => $balance, 'n_minings' => Mining::where('userid', $user->id)->get()]);
              }

            } else {
                if(Auth::user()){
                    if($username == Auth::user()->username || Auth::user()->isFounder()){
                        return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => $user->getNetWorthNew(Auth::user()->api), 'btc' => HomeController::btcUsd(), 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'm_investments' => $m_investments, 'balances' => $balance, 'n_minings' => Mining::where('userid', $user->id)->get()]);
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
                'type' => 'news',
                'category' => 'success'
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
                'type' => 'comment',
                'category' => 'success'
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
                'type' => 'comment',
                'category' => 'success'
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
