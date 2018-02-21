<?php
	$group_member_check_comment = \App\IoGroupUser::where('group_id', $comment->group_id)->where('user_id', Auth::user()->id)->count();
	$comment_group = \App\IoGroup::find($comment->group_id);
?>
<div class="posted-comment-single-container">
	@if($comment->avatar == "default.jpg")
		<img src="/assets/img/default.png" alt="" class="img-circle">
	@else
		@if (file_exists('uploads/avatars/'.$comment->user_id.'/'.$comment->avatar))
			<img src="{{asset('uploads/avatars/'.$comment->user_id.'/'.$comment->avatar)}}" alt="author">
		@else
			<img src="/assets/img/default.png" alt="" class="img-circle">
		@endif
	@endif
	<div class="posted-comment-text-container">
		<div class="posted-comment-text-div li-comment">
			<div class="single_comment_text" style="display:none;">{{$comment->comment}}</div>
			<p> <span>{{$comment->username}}: </span> {{$comment->comment}}</p>
		</div>
		<p class="ago-time-p">
			@if ($group_member_check_comment > 0 || $comment_group->user_id == Auth::user()->id)
				<a href="javascript:void(0)" class="inline-items like-heart like-comment @if(Auth::user()->hasLikedGroupComment($comment)) liked @endif" status="{{$comment->id}}">
				    <i class="fa fa-heart"></i>
				    <span>{{$comment->getLikes()}}</span>
				</a> ·
				<a href="javascript:;" class="group-reply-btn" data-comment_id="{{$comment->id}}">Reply</a> ·
			@endif
			{{$comment->created_at->diffForHumans()}}</p>
		@if ($comment->user_id == Auth::user()->id)
			<ul class="comment-action-more">
				<li class="dropdown">
					<a href="#" data-toggle="dropdown">
						<i class="zmdi zmdi-more"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right btn-primary">
						<li>
							<a href="javascript:void(0)" data-comment_id="{{$comment->id}}" class="group_post_comment_edit_btn">Edit Comment</a>
						</li>
						<li>
							<a href="javascript:void(0)" data-comment_id="{{$comment->id}}" class="group_post_comment_delete_btn">Delete</a>
						</li>
					</ul>
				</li>
			</ul>
		@endif
	</div>
</div>
<div class="posted-reply-container">
	<?php $replies = \App\IoGroupReply::where('comment_id', $comment->id)
									->join('users', 'users.id', '=', 'io_group_comment_replies.user_id')
									->select('io_group_comment_replies.*', 'users.username', 'users.avatar')->get();
	?>
	@if ($replies)
		@foreach ($replies as $reply)
			@include('module.groupSingleReply')
		@endforeach
	@endif
</div>
<div class="posted-comment-reply-form-container">
	@if(Auth::user()->avatar == "default.jpg")
		<img src="/assets/img/default.png" alt="" class="img-circle">
	@else
		@if (file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar))
			<img src="{{asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)}}" alt="author">
		@else
			<img src="/assets/img/default.png" alt="" class="img-circle">
		@endif
	@endif
	<form class="form-group is-empty comment-reply-form" action="{{route('group.reply.store')}}" method="post">
		<input type="hidden" name="comment_id_for_reply" value="{{$comment->id}}">
		<textarea class="form-control form-control-2 reply-post-field" style="background-image:none!important;" type="text" name="post_reply" placeholder="Write a reply ..." pellcheck="false" /></textarea>
		<a class="reply-send-btn" data-loading-text="<i class='fa fa-spinner fa-spin ' style='color:#ff5e3a;'></i>">
			<i class="zmdi zmdi-check"></i>
		</a>
		<p class="limit-comment-text reply-text"><span class="current_length">0</span>/2000</p>
	</form>
</div>
