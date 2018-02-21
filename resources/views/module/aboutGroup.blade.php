<div class="col-xl-8 order-xl-1 col-lg-8 col-md-8 col-sm-12 col-xs-12">
	<div class="ui-block">
		<div class="ui-block-title">
			<h4 class="title">About this group</h4>
		</div>
		<div class="ui-block-content">
			<ul class="widget w-personal-info item-block">
				<li style="padding-top: 0;">
					<span class="title">Discription:</span>
					<span class="text">{{$group->description}}</span>
				</li>
				<li>
					<span class="title">Created By:</span>
					<span class="text" style="margin-top:5px;">
			            <a href="{{url('user/'.$group_creator->username)}}" target="_blank">
			            @if($group_creator->avatar != "default.jpg")
			              <img style="width:40px;border-radius:50%;" src="{{asset('uploads/avatars/'.$group_creator->id.'/'.$group_creator->avatar)}}" alt="author">
			            @else
			              <img style="width:40px;border-radius:50%;" src="{{asset('assets/img/default.png')}}" alt="author">
			            @endif
			            {{$group_creator->username}}</a>
			       </span>
				</li>
				<li style="padding-bottom: 0;">
					<span class="title">Created date:</span>
					<span class="text" style="margin-top:5px;">
						<?php
						$resultdate = DateTime::createFromFormat('Y-m-d H:i:s', $group->created_at);
						$create_date = $resultdate->format('d M Y');
						$create_time = $resultdate->format('H:i');
						echo $create_date." at ".$create_time;
						?>
					</span>
				</li>
			</ul>
		</div>
	</div>
	<div class="ui-block">
		<div class="ui-block-title">
			<h4 class="title">Members · {{$total_member_count}}</h4>
		</div>
		<div class="ui-block-content about-group-member-contents">
			<ul class="widget w-personal-info item-block">
				<li style="text-align:center;display:inline-block;padding: 5px 0;">
					<a href="{{url('user/'.$group_creator->username)}}" target="_blank">
						@if($group_creator->avatar == "default.jpg")
								<img src="/assets/img/default.png" alt="" class="photo">
						@else
							@if (file_exists('uploads/avatars/'.$group_creator->id.'/'.$group_creator->avatar))
								<img src="{{asset('uploads/avatars/'.$group_creator->id.'/'.$group_creator->avatar)}}" alt="photo">
							@else
								<img src="/assets/img/default.png" alt="" class="photo">
							@endif
						@endif
					</a>
				</li>
				<?php
					$group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('io_group_users.user_level', 'users.*')->get();
				?>
				<?php foreach ($group_members as $group_member): ?>
					<?php if ($group_member->user_level != 2): ?>
						<li style="text-align:center;display:inline-block;padding: 5px 0;">
							<a href="{{url('user/'.$group_member->username)}}" target="_blank">
								@if($group_member->avatar == "default.jpg")
										<img src="/assets/img/default.png" alt="" class="photo">
								@else
									@if (file_exists('uploads/avatars/'.$group_member->id.'/'.$group_member->avatar))
										<img src="{{asset('uploads/avatars/'.$group_member->id.'/'.$group_member->avatar)}}" alt="photo">
									@else
										<img src="/assets/img/default.png" alt="" class="photo">
									@endif
								@endif
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php
					$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
				?>
				@foreach ($sent_requests as $sent_request)
					<li  style="text-align:center;display:inline-block;padding: 5px 0;">
						<a href="{{url('user/'.$sent_request->username)}}" target="_blank">
							@if($sent_request->avatar == "default.jpg")
									<img src="/assets/img/default.png" alt="" class="photo">
							@else
								@if (file_exists('uploads/avatars/'.$sent_request->id.'/'.$sent_request->avatar))
									<img src="{{asset('uploads/avatars/'.$sent_request->id.'/'.$sent_request->avatar)}}" alt="photo">
								@else
									<img src="/assets/img/default.png" alt="" class="photo">
								@endif
							@endif
						</a>
					</li>
				@endforeach
				<li style="padding: 2px 0;"><span class="title">Admins</span> </li>
				<?php
					$group_admin_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 1)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->count();
					$group_admins = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 1)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
				?>
				<li style="text-align:center;display:inline-block;padding: 5px 0;">
					<a href="{{url('user/'.$group_creator->username)}}" target="_blank">
						@if($group_creator->avatar == "default.jpg")
								<img src="/assets/img/default.png" alt="" class="photo">
						@else
							@if (file_exists('uploads/avatars/'.$group_creator->id.'/'.$group_creator->avatar))
								<img src="{{asset('uploads/avatars/'.$group_creator->id.'/'.$group_creator->avatar)}}" alt="photo">
							@else
								<img src="/assets/img/default.png" alt="" class="photo">
							@endif
						@endif
					</a>
				</li>
				<?php foreach ($group_admins as $group_admin): ?>
					<li style="text-align:center;display:inline-block;padding: 5px 0;">
						<a href="{{url('user/'.$group_admin->username)}}" target="_blank">
							@if($group_admin->avatar == "default.jpg")
									<img src="/assets/img/default.png" alt="" class="photo">
							@else
								@if (file_exists('uploads/avatars/'.$group_admin->id.'/'.$group_admin->avatar))
									<img src="{{asset('uploads/avatars/'.$group_admin->id.'/'.$group_admin->avatar)}}" alt="photo">
								@else
									<img src="/assets/img/default.png" alt="" class="photo">
								@endif
							@endif
						</a>
					</li>
				<?php endforeach; ?>
				<li style="padding: 2px 0;">
					<span class="title">
						<a href="{{url('user/'.$group_creator->username)}}" target="_blank">{{$group_creator->username}}</a>
						<?php if ($group_admin_count > 0): ?>
							<?php foreach ($group_admins as $group_admin): ?>
								and
								<a href="{{url('user/'.$group_admin->username)}}" target="_blank">{{$group_admin->username}}</a>
							<?php endforeach; ?> are Admins
						<?php else: ?>
							is Admin
						<?php endif; ?>
					</span>
				</li>
			</ul>
		</div>
		<div class="view-all-group-members-container">
			<a href="{{url('group/view/'.$group->url.'/members')}}">See all members</a>
		</div>
	</div>
	<div class="ui-block">
		<div class="ui-block-title">
			<h4 class="title">Top recent posts</h4>
		</div>
		<div class="ui-block-content about-group-post-single-div">
			<?php
				$foreach_count = 0;
				foreach ($group_posts as $group_post) {
					$foreach_count += 1;
				}
				$real_foreach_count = 0;
			?>
			<?php if ($foreach_count > 0): ?>
				@foreach ($group_posts as $group_post)
					<?php if ($real_foreach_count < 3): ?>
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
							</div>
							@if ($group_post->status)
								<p class="group-status-post-text">{{$group_post->status}}</p>
							@endif
							@if ($group_post->photo_ids)
								<?php
									$photo_array = unserialize($group_post->photo_ids);
									$count_photo = 0;
									foreach ($photo_array as $photos) {
										$count_photo += 1;
									}
									$real_count = 0;
								?>
								<div class="group_posted_photo_container js-zoom-gallery">
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
										@if ($group_post_photo && file_exists('assets/images/group/'.$group->url.'/'.$group_post_photo->photo) )
											<a class="{{$class}}" href="{{asset('assets/images/group/'.$group->url.'/'.$group_post_photo->photo)}}">
												<img src="{{asset('assets/images/group/'.$group->url.'/'.$group_post_photo->photo)}}" alt="">
											</a>
										@endif
									@endforeach
								</div>
							@endif
						</article>
						<?php if ($real_foreach_count <2): ?>
							<div class="group-about-separator"></div>
						<?php endif; ?>
					<?php endif; ?>
					<?php $real_foreach_count += 1; ?>
				@endforeach
			<?php else: ?>
				<p style="margin-top: 0; margin-bottom: 23px;">Group post not found</p>
			<?php endif; ?>
		</div>
		<div class="view-all-group-members-container">
			<a href="{{url('group/view/'.$group->url)}}">See all Posts</a>
		</div>
	</div>
	<div class="ui-block">
		<div class="ui-block-title">
			<h4 class="title">Photos</h4>
		</div>
		<div class="ui-block-content">
			<?php
				$count_photo = 0;
				foreach ($group_photos as $group_photo) {
					$count_photo += 1;
				}
				$real_count = 0;
			?>
			<?php if ($count_photo > 0): ?>
				<div class="group_posted_photo_container js-zoom-gallery">
					<?php foreach ($group_photos as $group_photo): ?>
						<?php
							$real_count += 1;
							$class1 = "post-photo-".$count_photo;
							if ($count_photo > 5) {
								$class1 = "post-photo-5";
							}
							$class2 = "subphoto-".$real_count;
							$class = $class1." ".$class2;
						?>
						<?php if ($real_count < 6): ?>
							@if (file_exists('assets/images/group/'.$group->url.'/'.$group_photo->photo) )
								<a class="{{$class}}" href="{{asset('assets/images/group/'.$group->url.'/'.$group_photo->photo)}}">
									<img src="{{asset('assets/images/group/'.$group->url.'/'.$group_photo->photo)}}" alt="">
								</a>
							@endif
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<p style="margin: 0;">Group photo not found</p>
			<?php endif; ?>
		</div>
		<div class="view-all-group-members-container">
			<a href="{{url('group/view/'.$group->url.'/photos')}}">See all Photos</a>
		</div>
	</div>
</div>
