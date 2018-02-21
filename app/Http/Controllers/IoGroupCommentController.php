<?php

namespace App\Http\Controllers;

use Auth;
use Cache;
use App\User;
use App\IoGroup;
use App\IoGroupUser;
use App\IoGroupPost;
use App\IoGroupPhoto;
use App\IoGroupReply;
use App\IoGroupRequest;
use App\IogGroupComment;
use Illuminate\Http\Request;
use App\Events\NewGroupComment;
use App\Events\EditGroupComment;
use App\Events\DeleteGroupComment;
use App\Events\NewGroupCommentReply;
use App\Events\EditGroupCommentReply;
use App\Events\DeleteGroupCommentReply;

class IoGroupCommentController extends Controller
{
    public function store(Request $request) {
        $group_post = IoGroupPost::find($request->group_post_id_for_comment);
        if ($group_post) {
            $post_comment = new IogGroupComment;
            $post_comment->group_id = $group_post->group_id;
            $post_comment->post_id = $group_post->id;
            $post_comment->user_id = Auth::user()->id;
            $post_comment->comment = $request->post_comment;
            $post_comment->save();

            $comment = IogGroupComment::join('users', 'users.id', '=', 'io_group_comments.user_id')
			->select('io_group_comments.*', 'users.username', 'users.avatar')->find($post_comment->id);

            $view = view('module.groupSingleComment',compact('comment'))->render();
            event(new NewGroupComment($group_post->id, $post_comment->id, $post_comment->user_id));

            return response()->json(['html'=>$view]);
        }

        return "fail";
    }

    public function store_reply(Request $request) {
        $group_comment = IogGroupComment::find($request->comment_id_for_reply);
        if ($group_comment) {
            $post_reply = new IoGroupReply;
            $post_reply->comment_id = $group_comment->id;
            $post_reply->user_id = Auth::user()->id;
            $post_reply->reply = $request->post_reply;
            $post_reply->save();

            event(new NewGroupCommentReply($post_reply->comment_id, $post_reply->id , $post_reply->user_id));

            $reply = IoGroupReply::join('users', 'users.id', '=', 'io_group_comment_replies.user_id')
            ->select('io_group_comment_replies.*', 'users.username', 'users.avatar')->find($post_reply->id);
            $view = view('module.groupSingleReply',compact('reply'))->render();
            return response()->json(['html'=>$view]);
        }

        return "fail";
    }

    public function get_just_reply($id) {
        $reply = IoGroupReply::join('users', 'users.id', '=', 'io_group_comment_replies.user_id')
        ->select('io_group_comment_replies.*', 'users.username', 'users.avatar')->find($id);
        if ($reply) {
            $view = view('module.groupSingleReply',compact('reply'))->render();
            return response()->json(['html'=>$view]);
        }
        return "fail";
    }

    public function get_edited_reply($id) {
        $reply = IoGroupReply::join('users', 'users.id', '=', 'io_group_comment_replies.user_id')
        ->select('io_group_comment_replies.*', 'users.username', 'users.avatar')->find($id);
        if ($reply) {
            $view = view('module.groupSingleReplyEdit',compact('reply'))->render();
            return response()->json(['html'=>$view]);
        }
        return "fail";
    }

    public function get_just_comment($id) {
        $comment = IogGroupComment::join('users', 'users.id', '=', 'io_group_comments.user_id')
        ->select('io_group_comments.*', 'users.username', 'users.avatar')->find($id);
        if ($comment) {
            $view = view('module.groupSingleComment',compact('comment'))->render();
            return response()->json(['html'=>$view]);
        }
    }

    public function get_edited_comment($id) {
        $comment = IogGroupComment::join('users', 'users.id', '=', 'io_group_comments.user_id')
        ->select('io_group_comments.*', 'users.username', 'users.avatar')->find($id);
        if ($comment) {
            $view = view('module.groupSingleCommentEdit',compact('comment'))->render();
            return response()->json(['html'=>$view]);
        }
    }

    public function update(Request $request) {
        $group_comment = IogGroupComment::find($request->group_post_comment_id_for_edit);
        if ($group_comment) {
            $group_comment->comment = $request->post_comment_edit;
            $group_comment->save();

            $comment = IogGroupComment::join('users', 'users.id', '=', 'io_group_comments.user_id')
			->select('io_group_comments.*', 'users.username', 'users.avatar')->find($group_comment->id);

            $view = view('module.groupSingleCommentEdit',compact('comment'))->render();
            event(new EditGroupComment($group_comment->id, $group_comment->user_id));
            return response()->json(['html'=>$view]);
        }
        return "fail";
    }

    public function update_reply(Request $request) {
        $group_reply = IoGroupReply::find($request->reply_id_for_edit);
        if ($group_reply) {
            $group_reply->reply = $request->post_reply_edit;
            $group_reply->save();
            event(new EditGroupCommentReply($group_reply->comment_id, $group_reply->id , $group_reply->user_id));
            $reply = IoGroupReply::join('users', 'users.id', '=', 'io_group_comment_replies.user_id')
            ->select('io_group_comment_replies.*', 'users.username', 'users.avatar')->find($group_reply->id);
            $view = view('module.groupSingleReplyEdit',compact('reply'))->render();
            return response()->json(['html'=>$view]);
        }
        return "fail";
    }

    public function destroy($id) {
        $group_comment = IogGroupComment::find($id);
        if ($group_comment) {
            event(new DeleteGroupComment($group_comment->id, $group_comment->user_id));
            $comment_reply = IoGroupReply::where('comment_id', $group_comment->id)->delete();
            $group_comment->delete();
            return "success";
        }

        return "fail";
    }

    public function destroy_reply($id) {
        $group_reply = IoGroupReply::find($id);
        if ($group_reply) {
            event(new DeleteGroupCommentReply($group_reply->id, $group_reply->user_id));
            $group_reply->delete();
            return "success";
        }

        return "fail";
    }

    public function likeComment($id) {
        $comment = IogGroupComment::find($id);
        Cache::forget(Auth::user()->id.':hasLikedGroupComment:'.$comment->id);
        if(!$comment->isLikedBy(Auth::user())){
            Auth::user()->like($comment);
            Cache::forget('goroupCommentLikes:'.$comment->id);
        } else {
            Auth::user()->unlike($comment);
            Cache::forget('goroupCommentLikes:'.$comment->id);
        }
    }

    public function likeReply($id) {
        $reply = IoGroupReply::find($id);
        Cache::forget(Auth::user()->id.':hasLikedGroupReply:'.$reply->id);
        if(!$reply->isLikedBy(Auth::user())){
            Auth::user()->like($reply);
            Cache::forget('goroupReplyLikes:'.$reply->id);
        } else {
            Auth::user()->unlike($reply);
            Cache::forget('goroupReplyLikes:'.$reply->id);
        }
    }
}
