<?php
    $this_comment = \App\IogGroupComment::find($reply->comment_id);
	$group_member_check_reply = \App\IoGroupUser::where('group_id', $this_comment->group_id)->where('user_id', Auth::user()->id)->count();
	$reply_group = \App\IoGroup::find($this_comment->group_id);
?>
@if($reply->avatar == "default.jpg")
    <img src="/assets/img/default.png" alt="" class="img-circle">
@else
    @if (file_exists('uploads/avatars/'.$reply->user_id.'/'.$reply->avatar))
        <img src="{{asset('uploads/avatars/'.$reply->user_id.'/'.$reply->avatar)}}" alt="author">
    @else
        <img src="/assets/img/default.png" alt="" class="img-circle">
    @endif
@endif
<div class="posted-reply-text-container">
    <div class="posted-comment-text-div li-comment">
        <div class="single_reply_text" style="display:none;">{{$reply->reply}}</div>
        <p> <span>{{$reply->username}}: </span> {{$reply->reply}}</p>
    </div>
    <p class="ago-time-p">
        @if ($group_member_check_reply > 0 || $reply_group->user_id == Auth::user()->id)
            <a href="javascript:void(0)" class="inline-items like-heart like-reply @if(Auth::user()->hasLikedGroupReply($reply)) liked @endif" status="{{$reply->id}}">
                <i class="fa fa-heart"></i>
                <span>{{$reply->getLikes()}}</span>
            </a> ·
            <a href="javascript:;" class="group-reply-btn" data-comment_id="{{$reply->comment_id}}">Reply</a> ·
        @endif
        {{$reply->created_at->diffForHumans()}}</p>
    @if ($reply->user_id == Auth::user()->id)
        <ul class="reply-action-more">
            <li class="dropdown">
                <a href="#" data-toggle="dropdown">
                    <i class="zmdi zmdi-more"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right btn-primary">
                    <li>
                        <a href="javascript:void(0)" data-reply_id="{{$reply->id}}" class="group-reply-edit-btn">Edit Reply</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" data-reply_id="{{$reply->id}}" class="group-reply-delete-btn">Delete</a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
</div>
