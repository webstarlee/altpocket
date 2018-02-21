<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Giphy;
use App\Status;
use App\StatusComment;
use App\StatusCommentReply;
use App\StatusAction;
use App\Crypto;
use App\User;
use App\Tracking;
use Redirect;
use Alert;
use App\Slim;
use Validator;
use App\Notifications\NewStatusComment;
use App\Notifications\NewTag;
use App\Notifications\NewStatus;
use App\Events\Newstatus as NewStatusEvent;
use App\Events\Editstatus;
use App\Events\Deletestatus;
use App\Events\Newcomment;
use App\Events\Editcomment;
use App\Events\Deletecomment;
use App\Events\NewCommentReply;
use App\Events\EditCommentReply;
use App\Events\DeleteCommentReply;
use Cache;
class StatusController extends Controller
{

    public function trackCoin($id, Request $request) {
      if(Tracking::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->exists())
      {
        $crypto = Crypto::where('id', $request->get('coin'))->first();

        if(!$crypto)
        {
          Alert::error('No coin with that name was found in our system, please request it to be added in the support panel!', 'No Coin Found')->persistent('Okay');
          return Redirect::back();
        }

        $tracking = Tracking::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->first();
        $tracking->coin = $crypto->symbol;
        $tracking->save();

        return Redirect::back();
      } else {
        $crypto = Crypto::where('id', $request->get('coin'))->first();

        if(!$crypto)
        {
          Alert::error('No coin with that name was found in our system, please request it to be added in the support panel!', 'No Coin Found')->persistent('Okay');
          return Redirect::back();
        }

        $tracking = new Tracking;
        $tracking->userid = Auth::user()->id;
        $tracking->trackingid = $id;
        $tracking->coin = $crypto->symbol;
        $tracking->save();

        return Redirect::back();
      }
    }

    public function like($id) {
      $status = Status::where('id', $id)->first();
      Cache::forget(Auth::user()->id.':hasLiked:'.$status->id);
      if(!$status->isLikedBy(Auth::user())){
        Auth::user()->like($status);
        Alert::success('You liked the post!', 'Post Liked');
        Cache::forget('statusLikers:'.$status->id);

        return Redirect::back();
      } else {
        Auth::user()->unlike($status);
        Cache::forget('statusLikers:'.$status->id);
        Alert::success('You unliked the post!', 'Post Unliked');

        return Redirect::back();
      }
    }

    public function likeComment($id) {
      $comment = StatusComment::where('id', $id)->first();
      Cache::forget(Auth::user()->id.':hasLikedComment:'.$comment->id);
      if(!$comment->isLikedBy(Auth::user())){
        Auth::user()->like($comment);
        Cache::forget('commentLikes:'.$comment->id);

      } else {
        Auth::user()->unlike($comment);
        Cache::forget('commentLikes:'.$comment->id);
      }
    }

    public function likeCommentReply($id) {
      $reply = StatusCommentReply::find($id);
      Cache::forget(Auth::user()->id.':hasLikedCommentReply:'.$reply->id);
      if(!$reply->isLikedBy(Auth::user())){
        Auth::user()->like($reply);
        Cache::forget('replyLikes:'.$reply->id);

      } else {
        Auth::user()->unlike($reply);
        Cache::forget('replyLikes:'.$reply->id);
      }
    }


    public function postStatus(Request $request){
        if($request->get('status') || $request->get('status_image') || $request->get('status_youtubeurl') || $request->get('status_giphy')){
            $status = new Status;
            $status->userid = Auth::user()->id;
            if ($request->get('status')) {
                $status->status = app('profanityFilter')->filter($request->get('status'));
            }
            if ($request->get('status_image')) {
                $status->images = serialize($request->get('status_image'));
            }
            if ($request->get('status_giphy')) {
                $status->giphys = serialize($request->get('status_giphy'));
            }
            if ($request->get('status_youtubeurl')) {
                $status->youtubes = serialize($request->get('status_youtubeurl'));
            }
            if(Auth::user()->created_at > date('Y-m-d H:i:s', time()-604800) && Auth::user()->accepted_posts < 3){
            // $status->moderate = "yes";
            }
            if($request->get('type')){
                $status->statustype = $request->get('type');
            } else {
                $status->statustype = "default";
            }
            $status->save();

            preg_match_all("/@(\w+)/i", $status->status, $matches);

            foreach($matches as $key => $match){
                if($match != null){
                    if(User::where('username', $match)->exists()){
                        $notifiable = User::where('username', $match)->first();
                        //Notification array
                        $notification = [
                            'icon' => 'fa fa-at',
                            'title' => 'New Tag',
                            'data' => 'You have been tagged in a status.',
                            'type' => 'tag',
                            'status' => $status->id,
                            'username' => Auth::user()->username,
                            'tagtype' => 'status',
                            'comment' => $status->status
                        ];

                        $notifiable->notify(new NewTag($notification));
                    }
                }
            }

            $followers = Auth::user()->followers()->get();

            foreach($followers as $user)
            {
              //Notification array
              $notification = [
                  'icon' => 'fa fa-reply',
                  'title' => 'New Status',
                  'data' => Auth::user()->username.' has made a new status!',
                  'type' => 'statuscomment',
                  'status' => $status->id,
                  'username' => Auth::user()->username
              ];

              $user->notify(new NewStatus($notification));
            }

            event(new NewStatusEvent($status->id, $status->userid));
            return 'success';
        } else {
            return "fail";
        }
    }

    public function editStatus(Request $request){
        $status = Status::find($request->status_id_for_edit);
        if ($status) {
            if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff()){
                if ($request->get('edit-status-text')) {
                    $status->status = app('profanityFilter')->filter($request->get('edit-status-text'));
                }
                if ($request->get('status_image')) {
                    $status->images = serialize($request->get('status_image'));
                }else {
                    $status->images = "";
                }
                if ($request->get('status_giphy')) {
                    $status->giphys = serialize($request->get('status_giphy'));
                }else {
                    $status->giphys = "";
                }
                if ($request->get('status_youtubeurl')) {
                    $status->youtubes = serialize($request->get('status_youtubeurl'));
                }else {
                    $status->youtubes = "";
                }
                $status->save();

                event(new Editstatus($status->id, $status->userid));
                return "success";
            } else {
                return "fail";
            }
        }
    }

    public function postComment($id, Request $request){
        if($request->get('comment') || $request->get('comment_image') || $request->get('status_giphy')) {
            $comment = new StatusComment;
            $comment->userid = Auth::user()->id;
            $comment->statusid = $id;
            if ($request->get('comment')) {
                $comment->comment = app('profanityFilter')->filter($request->get('comment'));
            }
            if ($request->get('comment_image')) {
                $comment->images = serialize($request->get('comment_image'));
            }
            if ($request->get('status_giphy')) {
                $comment->giphys = serialize($request->get('status_giphy'));
            }
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
                'status' => $status->id,
                'username' => Auth::user()->username,
                'comment' => $comment->comment
            ];

            $notifiable->notify(new NewStatusComment($notification));


            preg_match_all("/@(\w+)/i", $comment->comment, $matches);

            if(count($matches) >= 1){
                foreach($matches as $key => $match){
                    if($match != null){
                        if(User::where('username', $match)->exists()){
                            $user = User::where('username', $match)->first();
                            if($user->id != $status->userid){
                                //Notification array
                                $notification = [
                                    'icon' => 'fa fa-at',
                                    'title' => 'New Tag',
                                    'data' => 'You have been tagged in a comment.',
                                    'type' => 'tag',
                                    'status' => $status->id,
                                    'username' => Auth::user()->username,
                                    'tagtype' => 'comment',
                                    'comment' => $comment->comment
                                ];

                                $user->notify(new NewTag($notification));
                            }
                        }
                    }
                }
            }

            event(new Newcomment($comment->id, $comment->userid, $comment->statusid));

            Cache::forget('statusComments:'.$status->id);
            return "success";
        } else {
            return "fail";
        }
    }

    public function postCommentReply($id, Request $request){
        if($request->get('comment_reply') || $request->get('status_giphy')) {
            $reply = new StatusCommentReply;
            $reply->userid = Auth::user()->id;
            $reply->commentid = $id;
            if ($request->get('comment_reply')) {
                $reply->reply = app('profanityFilter')->filter($request->get('comment_reply'));
            }
            if ($request->get('status_giphy')) {
                $reply->giphys = serialize($request->get('status_giphy'));
            }
            $reply->save();

            event(new NewCommentReply($reply->id, Auth::user()->id, $reply->commentid));

            return "success";
        } else {
            return "fail";
        }
    }

    public function deleteCommentReply($id){
        $reply = StatusCommentReply::find($id);
        if($reply){
            if (Auth::user()->id == $reply->userid || Auth::user()->isFounder() || Auth::user()->isStaff()) {
                $reply->delete();
                event(new DeleteCommentReply($reply->id, $reply->userid));
                return "success";
            }else {
                return "fail";
            }
        } else {
            return "fail";
        }
    }

    public function editComment(Request $request) {
        $comment = StatusComment::find($request->comment_id_for_edit);

        if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff()){
            if ($request->get('comment') || $request->get('comment_image') || $request->get('status_giphy')) {
                if ($request->get('comment')) {
                    $comment->comment = app('profanityFilter')->filter($request->get('comment'));
                }else {
                    $comment->comment = "";
                }
                if ($request->get('comment_image')) {
                    $comment->images = serialize($request->get('comment_image'));
                }else {
                    $comment->images = "";
                }
                if ($request->get('status_giphy')) {
                    $comment->giphys = serialize($request->get('status_giphy'));
                }else {
                    $comment->giphys = "";
                }
                $comment->save();
                Cache::forget('statusComments:'.$comment->statusid);
                event(new Editcomment($comment->id, $comment->userid, $comment->statusid));
                return "success";
            }else {
                return "fail";
            }
        } else {
            return "fail";
        }
    }

    public function editCommentReply(Request $request) {
        $reply = StatusCommentReply::find($request->reply_id_for_edit);

        if(Auth::user()->id == $reply->userid || Auth::user()->isFounder() || Auth::user()->isStaff()) {
            if ($request->get('comment-reply') || $request->get('comment-status_giphy')) {
                if ($request->get('comment-reply')) {
                    $reply->reply = app('profanityFilter')->filter($request->get('comment-reply'));
                }else {
                    $reply->reply = "";
                }
                if ($request->get('status_giphy')) {
                    $reply->giphys = serialize($request->get('status_giphy'));
                }else {
                    $reply->giphys = "";
                }

                $reply->save();
                event(new EditCommentReply($reply->id, $reply->userid));
                return "success";
            }else {
                return "fail";
            }
        } else {
            return "fail";
        }
    }

    public function delete($id){
        $status = Status::join('users', 'users.id', '=', 'statuses.userid')->select('statuses.*', 'users.username')->find($id);
        if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff()) {

            $comments = StatusComment::where('statusid', $id)->get();

            foreach($comments as $comment) {
                $comment_replies = StatusCommentReply::where('commentid', $comment->id)->get();
                foreach ($comment_replies as $reply) {
                    $reply->delete();
                }
                $comment->delete();
            }
            if ($status->images) {
                $images = unserialize($status->images);
                foreach ($images as $image) {
                    if (file_exists('assets/images/status/'.$status->username."/".$image)) {
                        unlink('assets/images/status/'.$status->username."/".$image);
                    }
                }
            }

            event(new Deletestatus($status->id, $status->userid));
            $status->delete();
            return 'success';
        } else {
            return "fail";
        }
    }

    public function accept($id){
        $status = Status::where('id', $id)->first();
        $user = User::where('id', $status->userid)->first();
        if(Auth::user()->isFounder() || Auth::user()->isStaff()){
            $status->moderate = "no";
            $status->created_at = date('Y-m-d H:i:s');
            $status->save();
            $user->accepted_posts += 1;
            $user->save();

            Alert::success('The post was accepted and is now visible to all.', 'Post accepted');
            return Redirect::back();
        }
    }

    public function hide($id)
    {
      Cache::forget(Auth::user()->id.":hidden:".$id);
      $hidden = Cache::remember(Auth::user()->id.":hide:".$id, 1440, function() use ($id){

      if(StatusAction::where([['userid', '=', Auth::user()->id], ['status', '=', $id], ['type', '=', 'hide']])->exists())
      {
        $action = StatusAction::where([['userid', '=', Auth::user()->id], ['status', '=', $id], ['type', '=', 'hide']])->first();
      } else {
        $action = new StatusAction;
        $action->userid = Auth::user()->id;
        $action->status = $id;
        $action->type = "hide";
        $action->save();
      }
      return $action;
    });
    return Redirect::back();
    }


    public function deleteComment($id) {
        $comment = StatusComment::find($id);
        $statusid = $comment->statusid;
        if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff()) {

            $comment_replies = StatusCommentReply::where('commentid', $comment->id)->get();
            foreach ($comment_replies as $reply) {
                $reply->delete();
            }

            event(new Deletecomment($comment->id, $comment->userid, $comment->statusid));
            $comment->delete();
            Cache::forget('statusComments:'.$statusid);
            return 'success';
        } else {
            return "fail";
        }
    }

    public function getStatus($id){
        $status = Status::join('users', 'users.id', '=', 'statuses.userid')->select('statuses.*', 'users.username')->find($id);
        if ($status) {
            $single_status = array();
            $status_text = "";
            $status_imgs = array();
            $status_giphys = array();
            $status_urls = array();
            if ($status->status) {
                $status_text = $status->status;
            }

            if ($status->images) {
                $allimages = unserialize($status->images);
                foreach ($allimages as $singleimg) {
                    $image_url = asset('assets/images/status/'.$status->username.'/'.$singleimg);
                    $status_imgs[] = array('img_name' => $singleimg, 'img_url' => $image_url);
                }
            }

            if ($status->giphys) {
                $allgiphys = unserialize($status->giphys);
                foreach ($allgiphys as $singlegiphys) {
                    array_push($status_giphys, $singlegiphys);
                }
            }

            if ($status->youtubes) {
                $allyoutubes = unserialize($status->youtubes);
                foreach ($allyoutubes as $singleyoutube) {
                    array_push($status_urls, $singleyoutube);
                }
            }
            $single_status = array('status_id' => $status->id, 'status_text' => $status_text, 'status_imgs' => $status_imgs, 'status_giphys' => $status_giphys, 'status_urls' => $status_urls);
            return $single_status;
        }else {
            return "fail";
        }
    }
    public function getStatusComment($id){
        $comment = StatusComment::join('users', 'users.id', '=', 'status_comments.userid')->select('status_comments.*', 'users.username')->find($id);

        $single_comment = array();
        $comment_text = "";
        $comment_imgs = array();
        $comment_giphys = array();
        if ($comment->comment) {
            $comment_text = $comment->comment;
        }

        if ($comment->images) {
            $allimages = unserialize($comment->images);
            foreach ($allimages as $singleimg) {
                $image_url = asset('assets/images/status/'.$comment->username.'/'.$singleimg);
                $comment_imgs[] = array('img_name' => $singleimg, 'img_url' => $image_url);
            }
        }

        if ($comment->giphys) {
            $allgiphys = unserialize($comment->giphys);
            foreach ($allgiphys as $singlegiphys) {
                array_push($comment_giphys, $singlegiphys);
            }
        }

        $single_comment = array('comment_id' => $comment->id, 'comment_text' => $comment_text, 'comment_imgs' => $comment_imgs, 'comment_giphys' => $comment_giphys);
        return $single_comment;
    }

    public function getStatusCommentReply($id){
        $reply = StatusCommentReply::find($id);

        $single_reply = array();
        $replyt_text = "";
        $reply_giphys = array();
        if ($reply->reply) {
            $replyt_text = $reply->reply;
        }

        if ($reply->giphys) {
            $allgiphys = unserialize($reply->giphys);
            foreach ($allgiphys as $singlegiphys) {
                array_push($reply_giphys, $singlegiphys);
            }
        }

        $single_reply = array('reply_id' => $reply->id, 'reply_text' => $replyt_text, 'reply_giphys' => $reply_giphys);
        return $single_reply;
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

    public function getCoinsNew(Request $request)
    {
      $coins = Crypto::where([['name', 'LIKE', '%'.$request->get('q')."%"]])->orWhere([['symbol', 'LIKE', '%'.$request->get('q')."%"]])->get()->take(10);
      /*
      $test = json_encode(['tokens' => $coins], JSON_PRETTY_PRINT);
      echo "<pre>".$test."</pre>";*/

      return json_encode(['tokens' => $coins]);
    }

    public function uploadStatus_img(Request $request) {

        $imageRand = rand(1000, 9999);
        $random_name = time()."_".$imageRand;

        if(!is_dir(public_path('assets/images/status/'.Auth::user()->username))){
            mkdir(public_path('assets/images/status/'.Auth::user()->username));
        }

        $dst = public_path('assets/images/status/'.Auth::user()->username."/");

        $rules = [
            'file' => 'image',
            'slim[]' => 'image'
            ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors();

        if($validator->fails()){
            return "image_fail";
        }

        // Get posted data
        $images = Slim::getImages();

        // No image found under the supplied input name
        if ($images == false) {
            return "image_fail";
        }
        else {
            foreach ($images as $image) {

                $files = array();

                // save output data if set
                if (isset($image['output']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);
                    $name = $random_name.".".$file_type;

                    // We'll use the output crop data
                    $data = $image['output']['data'];

                    $output = Slim::saveFile($data, $name, $dst, false);

                    $img_url = asset('assets/images/status/'.Auth::user()->username.'/'.$name);
                    $data_array = array('img_name' => $name, 'img_url' => $img_url);
                    return response()->json($data_array);
                    array_push($files, $output);
                }

                // save input data if set
                if (isset ($image['input']['data'])) {

                    // Save the file
                    $origine_name = $image['input']['name'];
                    $file_type = pathinfo($origine_name, PATHINFO_EXTENSION);

                    $name = $random_name.".".$file_type;

                    $data = $image['input']['data'];
                    $input = Slim::saveFile($data, $name, $dst, false);

                    $img_url = asset('assets/images/status/'.Auth::user()->username.'/'.$name);
                    $data_array = array('img_name' => $name, 'img_url' => $img_url);
                    return response()->json($data_array);
                    array_push($files, $output);
                }
            }
        }
    }

    public function uploadStatus_img_gif(Request $request)
    {
        if($request->post_status_gif)
        {
            $result_validate = $this->validate($request, [
                'post_status_gif' => 'required|image|mimes:gif',
            ]);

            $imageRand = rand(1000, 9999);
            $random_name = time()."_".$imageRand;

            $img = $request->post_status_gif;

            if(!is_dir(public_path('assets/images/status/'.Auth::user()->username))){
                mkdir(public_path('assets/images/status/'.Auth::user()->username));
            }

            $dst = public_path('assets/images/status/'.Auth::user()->username."/");

            $imageName = $random_name.'.'.$img->getClientOriginalExtension();

            $img->move($dst, $imageName);

            $img_url = asset('assets/images/status/'.Auth::user()->username.'/'.$imageName);
            $data_array = array('img_name' => $imageName, 'img_url' => $img_url);
            return response()->json($data_array);
        }
        return "fail";
    }

    public function status_get_giphy($search, $page){
        $search_string = "soccer";
        if ($search != "null" && $search != "") {
            $search_string = $search;
        }

        $giphy = Giphy::search($search_string, $limit = 10, $offset = $page);

        return response()->json($giphy);
    }
}
