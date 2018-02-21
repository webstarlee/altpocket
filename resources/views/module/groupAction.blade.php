<div class="col-xl-4 order-xl-2 col-lg-4 order-lg-1 col-md-4 col-sm-12 col-xs-12 group-action-container">
	<?php
		$new_request_counts = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 0)->count();
		$group_admin_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
	?>
	@if ($new_request_counts > 0 && ( $group_admin_check->user_level == 0 || $group->user_id == Auth::user()->id))
		<div class="ui-block">
			<div class="ui-block-title">
				<h6 class="title">Received Join Requests</h6>
			</div>
			<div class="ui-block-content">
				<?php
					$new_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 0)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
				?>
				@foreach ($new_requests as $new_request)
					<div class="group-join-request-single">
						<div class="post__author author vcard inline-items">
							<a href="{{url('user/'.$new_request->username)}}" target="_blank">
								@if($new_request->avatar == "default.jpg")
										<img src="/assets/img/default.png" alt="" class="img-circle">
								@else
									@if (file_exists('uploads/avatars/'.$new_request->id.'/'.$new_request->avatar))
										<img src="{{asset('uploads/avatars/'.$new_request->id.'/'.$new_request->avatar)}}" alt="author">
									@else
										<img src="/assets/img/default.png" alt="" class="img-circle">
									@endif
								@endif
							</a>
							<div class="author-date">
								<a class="h6 post__author-name fn" href="{{url('user/'.$new_request->username)}}" target="_blank">{{$new_request->username}}</a>
								<div class="post__date">
									<?php
										$time_ago = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $new_request->receive_time);
									?>
									<time class="published" datetime="2017-03-24T18:18">{{$time_ago->diffForHumans()}}</time>
								</div>
							</div>
							<ul class="card-actions icons">
								<li class="dropdown">
									<a href="javascript:void(0)" data-toggle="dropdown">
										<i class="zmdi zmdi-more-vert"></i>
									</a>
									<ul class="dropdown-menu dropdown-menu-right btn-primary">
										<li>
											<a href="{{url('group/accept/user/'.$new_request->request_id)}}">Accept</a>
										</li>
										<li>
											<a href="{{url('group/reject/user/'.$new_request->request_id)}}">Decline</a>
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	@endif
	<div class="ui-block">
		<div class="ui-block-title">
			<h6 class="title">MEMBERS</h6>
			<?php
				$group_member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
			?>
			@if ($group_member_check > 0 || $group->user_id == Auth::user()->id)
				<ul class="card-actions icons" style="top:10px;right:10px;">
					<li class="dropdown">
						<a href="javascript:void(0)" data-toggle="dropdown">
							<i class="zmdi zmdi-more-vert"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-right btn-primary">
							<li>
								<a href="javascript:void(0)" data-toggle="modal" data-target="#add-group-member-modal">Add more members</a>
							</li>
						</ul>
					</li>
				</ul>
			@endif
		</div>
		<div class="ui-block-content group-member-contents">
			<ul class="widget">
				<?php
					$group_founder = \App\User::find($group->user_id);
					$group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('io_group_users.user_level', 'users.*')->get();
				?>
				<li style="text-align:center;display:inline-block;">
					<a href="{{url('user/'.$group_founder->username)}}" target="_blank">
						@if($group_founder->avatar == "default.jpg")
								<img src="/assets/img/default.png" alt="" class="photo">
						@else
							@if (file_exists('uploads/avatars/'.$group_founder->id.'/'.$group_founder->avatar))
								<img src="{{asset('uploads/avatars/'.$group_founder->id.'/'.$group_founder->avatar)}}" alt="photo">
							@else
								<img src="/assets/img/default.png" alt="" class="photo">
							@endif
						@endif
					</a>
				</li>
				@foreach ($group_members as $group_member)
					<li style="text-align:center;display:inline-block;">
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
				@endforeach
				<?php
					$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
				?>
				@foreach ($sent_requests as $sent_request)
					@if ($sent_request->id != Auth::user()->id)
						<li  style="text-align:center;display:inline-block;">
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
					@endif
				@endforeach
			</ul>
		</div>
	</div>
	<?php if (Route::currentRouteName()=='group.single.view.members'): ?>
		<?php
			$group_block_members_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 2)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->count();
		?>
		<?php if ($group_block_members_count > 0): ?>
			<div class="ui-block">
				<div class="ui-block-title">
					<h6 class="title">Blocked User</h6>
				</div>
				<div class="ui-block-content group-member-contents" style="padding-bottom: 5px;">
					<ul class="widget">
						<?php
							$group_block_members = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 2)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
						?>
						@foreach ($group_block_members as $group_block_member)
							<li style="text-align:center;display:inline-block;">
								<a href="{{url('user/'.$group_block_member->username)}}" target="_blank">
									@if($group_block_member->avatar == "default.jpg")
											<img src="/assets/img/default.png" alt="" class="photo">
									@else
										@if (file_exists('uploads/avatars/'.$group_block_member->id.'/'.$group_block_member->avatar))
											<img src="{{asset('uploads/avatars/'.$group_block_member->id.'/'.$group_block_member->avatar)}}" alt="photo">
										@else
											<img src="/assets/img/default.png" alt="" class="photo">
										@endif
									@endif
								</a>
							</li>
						@endforeach
					</ul>
				</div>
				<div class="view-all-group-members-container">
					<a href="{{url('group/view/'.$group->url.'/blocked')}}">See blocked members</a>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php
		$random_users = \App\User::orderBy(DB::raw('RAND()'))->take(10)->get();
	?>
	<div class="ui-block">
		<div class="ui-block-title">
			<h6 class="title">Recommened Users for this Group</h6>
		</div>
		<div class="ui-block-content">
			@foreach ($random_users as $random_user)
				<?php
					$exist_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', $random_user->id)->count();
				?>
				@if ($exist_check == 0 && $group->user_id != $random_user->id && Auth::user()->id != $random_user->id)
					<div class="group-join-request-single">
						<div class="post__author author vcard inline-items">
							<a href="{{url('user/'.$random_user->username)}}" target="_blank">
								@if($random_user->avatar == "default.jpg")
										<img src="/assets/img/default.png" alt="" class="img-circle">
								@else
									@if (file_exists('uploads/avatars/'.$random_user->id.'/'.$random_user->avatar))
										<img src="{{asset('uploads/avatars/'.$random_user->id.'/'.$random_user->avatar)}}" alt="author">
									@else
										<img src="/assets/img/default.png" alt="" class="img-circle">
									@endif
								@endif
							</a>
							<div class="author-date">
								<a class="h6 post__author-name fn" href="{{url('user/'.$random_user->username)}}" target="_blank">{{$random_user->username}}</a>
							</div>
							@if ($member_check > 0 || $group->user_id == Auth::user()->id)
								<ul class="card-actions icons">
									<li class="dropdown">
										<a href="javascript:void(0)" data-toggle="dropdown">
											<i class="zmdi zmdi-more-vert"></i>
										</a>
										<ul class="dropdown-menu dropdown-menu-right btn-primary">
											<li>
												<a href="{{url('group/add/member/'.$group->id.'/'.$random_user->id)}}">Add as Member</a>
											</li>
										</ul>
									</li>
								</ul>
							@endif
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
</div>
