<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Status;
use App\StatusComment;
use App\Crypto;
use App\User;
use App\Tracking;
use Redirect;
use Alert;
use App\Notifications\NewStatusComment;

class StatusController extends Controller
{

    public function trackCoin($id, Request $request)
    {
      if(Tracking::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->exists())
      {
        $crypto = Crypto::where('name', $request->coin)->first();
        $tracking = Tracking::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->first();
        $tracking->coin = $crypto->symbol;
        $tracking->save();

        return Redirect::back();
      } else {
        $crypto = Crypto::where('name', $request->coin)->first();

        $tracking = new Tracking;
        $tracking->userid = Auth::user()->id;
        $tracking->trackingid = $id;
        $tracking->coin = $crypto->symbol;
        $tracking->save();

        return Redirect::back();
      }
    }

    public function like($id)
    {
      $status = Status::where('id', $id)->first();
      if(!$status->isLikedBy(Auth::user())){
        Auth::user()->like($status);

        Alert::success('You liked the post!', 'Post Liked');
        return Redirect::back();
      } else {
        Auth::user()->unlike($status);

        Alert::success('You unliked the post!', 'Post Unliked');
        return Redirect::back();
      }
    }
    public function likeComment($id)
    {
      $comment = StatusComment::where('id', $id)->first();
      if(!$comment->isLikedBy(Auth::user())){
        Auth::user()->like($comment);

      } else {
        Auth::user()->unlike($comment);

      }
    }


    public function postStatus(Request $request)
    {
      $status = new Status;
      $status->userid = Auth::user()->id;
      $status->status = app('profanityFilter')->filter($request->get('status'));
      if(Auth::user()->created_at > date('Y-m-d H:i:s', time()-604800) && Auth::user()->accepted_posts < 3){
      $status->moderate = "yes";
      }
      $status->save();

      preg_match_all("/@(\w+)/i", $status->status, $matches);

      foreach($matches as $key => $match)
      {
      if($match != null){
        if(User::where('username', $match)->exists())
        {
          $notifiable = User::where('username', $match)->first();

          //Notification array
          $notification = [
              'icon' => 'fa fa-at',
              'title' => 'New Tag',
              'data' => 'You have been tagged in a status.',
              'type' => 'tag',
              'status' => $status->id,
              'category' => 'success'
          ];

          $notifiable->notify(new NewStatusComment($notification));
        }
      }
      }



      return Redirect::back();
    }

    public function editStatus($id, Request $request)
    {
      $status = Status::where('id', $id)->first();

      if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
      {
      $status->status = app('profanityFilter')->filter($request->get('comment'));
      $status->save();

      return Redirect::back();
    } else {
      Alert::success('You can´t edit someone elses status.', 'Edit failed');
      return Redirect::back();
    }
    }

    public function postComment($id, Request $request)
    {
      $comment = new StatusComment;
      $comment->userid = Auth::user()->id;
      $comment->statusid = $id;
      $comment->comment = app('profanityFilter')->filter($request->get('comment'));
      $comment->save();

      $status = Status::where('id', $comment->statusid)->first();

      // Get notify guy
      $notifiable = User::where('id', $status->userid)->first();

      //Notification array
      $notification = [
          'icon' => 'fa fa-reply',
          'title' => 'New Comment',
          'data' => 'Your status has a new comment',
          'type' => 'statuscomment',
          'status' => $status->id
      ];

      $notifiable->notify(new NewStatusComment($notification));


      preg_match_all("/@(\w+)/i", $comment->comment, $matches);

      if(count($matches) >= 1)
      {
      foreach($matches as $key => $match)
      {
        if($match != null){
        if(User::where('username', $match)->exists())
        {
          $user = User::where('username', $match)->first();
          //Notification array
          $notification = [
              'icon' => 'fa fa-at',
              'title' => 'New Tag',
              'data' => 'You have been tagged in a comment.',
              'type' => 'tag',
              'status' => $status->id
          ];

          $user->notify(new NewStatusComment($notification));
        }
    }
  }
    }


      return Redirect::back();
    }

    public function editComment($id, Request $request)
    {
      $comment = StatusComment::where('id', $id)->first();

        if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
        {
        $comment->comment = app('profanityFilter')->filter($request->get('comment'));
        $comment->save();

        return Redirect::back();
      } else {
        return Redirect::back();
      }
    }

    public function delete($id)
    {
      $status = Status::where('id', $id)->first();
      if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
      {
        $status->delete();

        $comments = StatusComment::where('statusid', $id)->get();

        foreach($comments as $comment)
        {
          $comment->delete();
        }
        Alert::success('Your post was deleted!', 'Post deleted');
        return Redirect::back();
      } else {
        Alert::success('Your can´t delete someone elses post!', 'Delete failed');
        return Redirect::back();
      }
    }

    public function accept($id)
    {
      $status = Status::where('id', $id)->first();
      $user = User::where('id', $status->userid)->first();
      if(Auth::user()->isFounder() || Auth::user()->isStaff())
      {
        $status->moderate = "no";
        $status->save();
        $user->accepted_posts += 1;
        $user->save();

        Alert::success('The post was accepted and is now visible to all.', 'Post accepted');
        return Redirect::back();
      }
    }


    public function deleteComment($id)
    {
      $comment = StatusComment::where('id', $id)->first();
      if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
      {
        $comment->delete();
        Alert::success('Your comment was deleted!', 'Comment deleted');
        return Redirect::back();
      } else {
        Alert::success('Your can´t delete someone elses comment!', 'Delete failed');
        return Redirect::back();
      }
    }

    public function getStatus($id){
        $status = Status::where('id', $id)->first();

        return $status;
    }
    public function getStatusComment($id){
        $comment = StatusComment::where('id', $id)->first();

        return $comment;
    }


    public function getUsers(Request $request)
    {
      $users = User::where([['username', 'LIKE', '%'.$request->get('q')."%"]])->select(array('username as name'))->get()->take(10);

      return $users;
    }

    public function getCoins(Request $request)
    {
      $coins = Crypto::where([['name', 'LIKE', '%'.$request->get('q')."%"]])->get()->take(10);

      return $coins;
    }


}
