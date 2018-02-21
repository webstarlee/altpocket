<?php
	$group_member_check = \App\IoGroupUser::where('group_id', $group_post->group_id)->where('user_id', Auth::user()->id)->count();
	$current_group = \App\IoGroup::find($group_post->group_id);
?>
<!-- Comments -->
<article class="hentry post group-post-single-container">
	<div class="post__author author vcard inline-items">
		<a href="{{url('user/'.$group_post->poster_name)}}" target="_blank">
			@if($group_post->poster_avatar == "default.jpg")
					<img src="/assets/img/default.png" alt="" class="img-circle">
			@else
				<img src="{{asset('uploads/avatars/'.$group_post->user_id.'/'.$group_post->poster_avatar)}}" alt="author">
			@endif
		</a>
		<div class="author-date">
			<a class="h6 post__author-name fn" href="{{url('user/'.$group_post->poster_name)}}" target="_blank">{{$group_post->poster_name}}</a>
			@if ($group_post->description)
				<span style="font-size:12px;"><?php echo html_entity_decode($group_post->description);?></span>
			@endif
			<div class="post__date">
				<time class="published" datetime="2017-03-24T18:18">{{$group_post->created_at->diffForHumans()}}</time>
			</div>
		</div>
		@if ($group_post->user_id == Auth::user()->id)
			<ul class="card-actions icons right-top">
				<li class="dropdown">
					<a href="#" data-toggle="dropdown">
						<i class="zmdi zmdi-more-vert"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-right btn-primary">
						@if ($group_post->poll == 0)
							@if ($group_post->editable == 1)
								<li>
									<a href="{{url('group/post/get/'.$group_post->id)}}" class="group-post-edit-btn">Edit Post</a>
								</li>
							@endif
							<li>
								<a href="{{url('group/post/delete/'.$group_post->id)}}" data-post_id="{{$group_post->id}}" class="group-post-delete-btn">Delete Post</a>
							</li>
						@elseif ($group_post->poll == 1)
							<li>
								<a href="{{url('group/post/delete/'.$group_post->id)}}" data-post_id="{{$group_post->id}}" class="group-post-delete-btn">Delete Poll</a>
							</li>
						@endif
					</ul>
				</li>
			</ul>
		@endif
	</div>
	@if ($group_post->status)
		<p class="group-status-post-text">{{$group_post->status}}</p>
	@endif

	@if ($group_post->photo_ids || $group_post->giphys)
		<?php
			$count_photo = 0;
			if ($group_post->photo_ids) {
				$photo_array = unserialize($group_post->photo_ids);
				foreach ($photo_array as $photos) {
					$count_photo += 1;
				}
			}

			if ($group_post->giphys) {
				$giphy_array = unserialize($group_post->giphys);
				foreach ($giphy_array as $giphy) {
					$count_photo += 1;
				}
			}

			$real_count = 0;
		?>
		<div class="group_posted_photo_container js-zoom-gallery">
			@if ($group_post->photo_ids)
				@foreach ($photo_array as $photo)
					<?php
						$real_count += 1;
						$group_post_photo = \App\IoGroupPhoto::find($photo);
						$class1 = "post-photo-".$count_photo;
						if ($count_photo > 5) {
							$class1 = "post-photo-more";
						}
						$class2 = "subphoto-".$real_count;
						if ($real_count > 5) {
							$class2 = "subphoto-more";
						}
						$class = $class1." ".$class2;
					?>
					@if ($group_post_photo && file_exists('assets/images/group/'.$current_group->url.'/'.$group_post_photo->photo) )
						<a class="{{$class}}" href="{{asset('assets/images/group/'.$current_group->url.'/'.$group_post_photo->photo)}}">
							<img src="{{asset('assets/images/group/'.$current_group->url.'/'.$group_post_photo->photo)}}" alt="">
						</a>
					@endif
				@endforeach
			@endif
			@if ($group_post->giphys)
				@foreach ($giphy_array as $giphy)
					<?php
						$real_count += 1;
						$class1 = "post-photo-".$count_photo;
						if ($count_photo > 5) {
							$class1 = "post-photo-more";
						}
						$class2 = "subphoto-".$real_count;
						if ($real_count > 5) {
							$class2 = "subphoto-more";
						}
						$class = $class1." ".$class2;
					?>
					<a class="{{$class}}" href="{{$giphy}}">
						<img src="{{$giphy}}" alt="">
					</a>
				@endforeach
			@endif
		</div>
	@endif

	@if ($group_post->youtubes)
		<?php
			$youtube_array = unserialize($group_post->youtubes);
		?>
		<div class="grou-post-youtube-container">
			@foreach ($youtube_array as $youtube)
				<?php
					$info = \Alaouy\Youtube\Facades\Youtube::getVideoInfo($youtube);
					if (strlen($info->snippet->description) > 100) {
						$description = substr($info->snippet->description, 0, 100).'...';
					} else {
						$description = $info->snippet->description;
					}
				?>
				<div class="youtube youtube-single">
					<a href="https://www.youtube.com/watch?v={{$youtube}}" target="_blank">
						<div class="youtube img-container" style="background-image: url({{$info->snippet->thumbnails->medium->url}});"></div>
						<div class="youtube title-description-container">
							<p class="title">{{$info->snippet->title}}</p>
							<p class="description">{{$description}}</p>
							<p class="youtube-com">youtube.com</p>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	@endif

	<?php
		$comments = \App\IogGroupComment::where('post_id', $group_post->id)
		->join('users', 'users.id', '=', 'io_group_comments.user_id')
		->select('io_group_comments.*', 'users.username', 'users.avatar')->get();

		$default_img_url = asset('assets/img/default.png');

		if (Auth::user()->avatar != "default.jpg" && file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)) {
			$default_img_url = asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar);
		}
	?>

	<div class="posted-comment-container">
		<input type="hidden" name="default_user_photo_url" value="{{$default_img_url}}">
		<input type="hidden" name="default_user_username" value="{{Auth::user()->username}}">
		<input type="hidden" name="default_delete_url" value="{{url('group/comment/delete/')}}">
		<input type="hidden" name="default_edit_url" value="{{route('group.comment.update')}}">
		@if (count($comments) > 0)
			<div class="posted-comment-separator"></div>
		@endif
		@foreach ($comments as $comment)
			@include('module.groupSingleComment')
		@endforeach
	</div>
	@if ($group_member_check > 0 || $current_group->user_id == Auth::user()->id)
		<div class="group-post-comment-container-div">
			<div class="post__author author vcard inline-items">
				@if(Auth::user()->avatar == "default.jpg")
					<img src="/assets/img/default.png" alt="" class="img-circle">
				@else
					@if (file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar))
						<img src="{{asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)}}" alt="author">
					@else
						<img src="/assets/img/default.png" alt="" class="img-circle">
					@endif
				@endif
				<form class="form-group with-icon-right is-empty group-post-comment-form" action="{{route('group.comment.store')}}" method="post">
					<input type="hidden" name="group_post_id_for_comment" value="{{$group_post->id}}">
					<textarea class="form-control form-control-2" style="background-image:none!important;" type="text" name="post_comment" placeholder="Write a comment ..." pellcheck="false" /></textarea>
                    <div class="add-group-post-photo">
						<a class="group-post-photo post-comment-add-btn" data-loading-text="<i class='fa fa-spinner fa-spin' style='color:#4fc5ea;'></i>">
							<i class="zmdi zmdi-check"></i>
						</a>
					</div>
					<p class="limit-comment-text"><span class="current_length">0</span>/2000</p>
				</form>
			</div>
		</div>
	@endif
</article>
<!-- Comment Form  -->
