<div class="col-xl-8 order-xl-1 col-lg-8 col-md-8 col-sm-12 col-xs-12">
	<div class="ui-block" style="margin-bottom: 30px;padding-bottom:30px;">
		<?php
			$just_group_member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
			if ($just_group_member_check > 0) {
				$just_group_member = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->first();
			}
		?>
		<div class="ui-block-title">
			<h4 class="title">Members · {{$total_member_count}}</h4>
		</div>
		<?php if ($just_group_member_check > 0): ?>
			<?php if ($just_group_member->user_level == 0): ?>
				<ul class="notification-list friend-requests group-member-view-container">
					<li>
						<div class="author-thumb">
							<a href="{{url('user/'.Auth::user()->username)}}" target="_blank">
								@if(Auth::user()->avatar == "default.jpg")
										<img src="/assets/img/default.png" alt="" class="photo">
								@else
									@if (file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar))
										<img src="{{asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)}}" alt="photo">
									@else
										<img src="/assets/img/default.png" alt="" class="photo">
									@endif
								@endif
							</a>
						</div>
						<div class="notification-event">
							<a href="#" class="h6 notification-friend group-member-view-single-name">{{Auth::user()->username}}</a>
							<span class="chat-message-item">{{Auth::user()->email}}</span>
						</div>
						<ul class="card-actions icons group-member-view-cardaction-more">
							<li class="dropdown">
								<a href="javascript:void(0)" data-toggle="dropdown">
									<i class="zmdi zmdi-more-vert"></i>
								</a>
								<ul class="dropdown-menu dropdown-menu-right btn-primary">
									<li>
										<a href="">Leave Group</a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			<?php endif; ?>
		<?php endif; ?>
		{{-- view all admins of this group --}}
		<div class="group-member-view-type-title-container">
			<p class="title">Admins</p>
		</div>
		<ul class="notification-list friend-requests group-member-view-container">
			{{-- if you are group admin view first line --}}
			<?php if ($just_group_member_check > 0 && $just_group_member->user_level == 1): ?>
				<li>
					<div class="author-thumb">
						<a href="{{url('user/'.Auth::user()->username)}}" target="_blank">
							@if(Auth::user()->avatar == "default.jpg")
									<img src="/assets/img/default.png" alt="" class="photo">
							@else
								@if (file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar))
									<img src="{{asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)}}" alt="photo">
								@else
									<img src="/assets/img/default.png" alt="" class="photo">
								@endif
							@endif
						</a>
					</div>
					<div class="notification-event">
						<a href="#" class="h6 notification-friend group-member-view-single-name">{{Auth::user()->username}}</a>
						<span class="chat-message-item">{{Auth::user()->email}}</span>
					</div>
					<ul class="card-actions icons group-member-view-cardaction-more">
						<li class="dropdown">
							<a href="javascript:void(0)" data-toggle="dropdown">
								<i class="zmdi zmdi-more-vert"></i>
							</a>
							<ul class="dropdown-menu dropdown-menu-right btn-primary">
								<li><a href="{{url('group/leave-group/'.$group->id)}}">Leave Group</a></li>
								<li><a href="">Remove as Admin</a></li>
							</ul>
						</li>
					</ul>
				</li>
			<?php endif; ?>
			{{-- end --}}

			{{-- Group master(top admin) --}}
			<li>
				<div class="author-thumb">
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
				</div>
				<div class="notification-event">
					<a href="#" class="h6 notification-friend group-member-view-single-name">{{$group_creator->username}}</a>
					<span class="chat-message-item">{{$group_creator->email}}</span>
				</div>
			</li>
			{{-- end --}}

			{{-- view all group admin --}}
			<?php
				$group_admin_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 1)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->count();
				$group_admins = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 1)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
			?>
			<?php foreach ($group_admins as $group_admin): ?>
				{{-- check this admin is you , if this is you, should not show, because you already show on first line of admin tap--}}
				<?php if (Auth::user()->id != $group_admin->id): ?>
					<li>
						<div class="author-thumb">
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
						</div>
						<div class="notification-event">
							<a href="#" class="h6 notification-friend group-member-view-single-name">{{$group_admin->username}}</a>
							<span class="chat-message-item">{{$group_admin->email}}</span>
						</div>
						{{-- check you are top admin --}}
						<?php if (Auth::user()->id == $group->user_id): ?>
							<ul class="card-actions icons group-member-view-cardaction-more">
								<li class="dropdown">
									<a href="javascript:void(0)" data-toggle="dropdown">
										<i class="zmdi zmdi-more-vert"></i>
									</a>
									<ul class="dropdown-menu dropdown-menu-right btn-primary">
										<li><a href="{{url('group/remove/admin/'.$group->id.'/'.$group_admin->id)}}" class="group-member-remove-as-admin">Remove as Admin</a></li>
										<li><a href="javascript:void(0);" data-request_id="{{$group_admin->id}}" data-requester_name="{{$group_admin->username}}" class="group-member-remove-admin">Remove from Group</a></li>
									</ul>
								</li>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
			{{-- end --}}
		</ul>
		{{-- end --}}

		{{-- view all member include top admin and admins --}}
		<div class="group-member-view-type-title-container">
			<p class="title">All members</p>
		</div>
		<ul class="notification-list friend-requests group-member-view-container">
			{{-- this is top admin(group creator) --}}
			<li>
				<div class="author-thumb">
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
				</div>
				<div class="notification-event">
					<a href="#" class="h6 notification-friend group-member-view-single-name">{{$group_creator->username}}</a>
					<span class="chat-message-item">{{$group_creator->email}}</span>
				</div>
			</li>
			{{-- end --}}

			{{-- current group member foreach --}}
			<?php
				$group_members = $group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('io_group_users.user_level', 'users.*')->get();
			?>
			<?php foreach ($group_members as $group_member): ?>
				{{-- check this member is ban user --}}
				<?php if ($group_member->user_level != 2): ?>
					<li>
						<div class="author-thumb">
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
						</div>
						<div class="notification-event">
							<a href="#" class="h6 notification-friend group-member-view-single-name">{{$group_member->username}}</a>
							<span class="chat-message-item">{{$group_member->email}}</span>
						</div>
						{{-- check your level --}}
						<?php if ($group->user_id == Auth::user()->id || ($just_group_member_check > 0 && $just_group_member->user_level == 1)): ?>
							{{-- check this member is admin --}}
							<?php if ($group_member->user_level == 1): ?>
								{{-- if this member is admin, only top admin can control this member --}}
								<?php if ($group->user_id == Auth::user()->id): ?>
									<ul class="card-actions icons group-member-view-cardaction-more">
										<li class="dropdown">
											<a href="javascript:void(0)" data-toggle="dropdown">
												<i class="zmdi zmdi-more-vert"></i>
											</a>
											<ul class="dropdown-menu dropdown-menu-right btn-primary">
												<li><a href="{{url('group/remove/admin/'.$group->id.'/'.$group_admin->id)}}" class="group-member-remove-as-admin">Remove as Admin</a></li>
												<li><a href="javascript:void(0);" data-request_id="{{$group_admin->id}}" data-requester_name="{{$group_admin->username}}" class="group-member-remove-admin">Remove from Group</a></li>
											</ul>
										</li>
									</ul>
								<?php endif; ?>
								{{-- end --}}
								{{-- if this member is you, you can control yourself --}}
								<?php if (Auth::user()->id == $group_member->id): ?>
									<ul class="card-actions icons group-member-view-cardaction-more">
										<li class="dropdown">
											<a href="javascript:void(0)" data-toggle="dropdown">
												<i class="zmdi zmdi-more-vert"></i>
											</a>
											<ul class="dropdown-menu dropdown-menu-right btn-primary">
												<li><a href="{{url('group/leave-group/'.$group->id)}}">Leave Group</a></li>
												<li><a href="">Remove as Admin</a></li>
											</ul>
										</li>
									</ul>
								<?php endif; ?>
								{{-- end --}}
							{{-- this member is not admin, top admin and admin can control this member --}}
							<?php else: ?>
								<ul class="card-actions icons group-member-view-cardaction-more">
									<li class="dropdown">
										<a href="javascript:void(0)" data-toggle="dropdown">
											<i class="zmdi zmdi-more-vert"></i>
										</a>
										<ul class="dropdown-menu dropdown-menu-right btn-primary">
											<li><a href="{{url('group/make-user/admin/'.$group->id.'/'.$group_member->id)}}" class="group-user-make-admin">Make Admin</a></li>
											<li><a href="javascript:void(0);" data-request_id="{{$group_member->id}}" data-requester_name="{{$group_member->username}}" class="group-member-remove-user">Remove from Group</a></li>
										</ul>
									</li>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
			{{-- end --}}

			{{-- top admin or admin sent join request to user, so, this is requests foreach --}}
			<?php
				$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
			?>
			<?php foreach ($sent_requests as $sent_request): ?>
				<li>
					<div class="author-thumb">
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
					</div>
					<div class="notification-event">
						<a href="#" class="h6 notification-friend group-member-view-single-name">{{$sent_request->username}}</a>
						<span class="chat-message-item">{{$sent_request->email}}</span>
					</div>
					{{-- check your level is admin or top admin --}}
					<?php if ($group->user_id == Auth::user()->id || ($just_group_member_check > 0 && $just_group_member->user_level == 1)): ?>
						<ul class="card-actions icons group-member-view-cardaction-more">
							<li class="dropdown">
								<a href="javascript:void(0)" data-toggle="dropdown">
									<i class="zmdi zmdi-more-vert"></i>
								</a>
								<ul class="dropdown-menu dropdown-menu-right btn-primary">
									<li><a href="{{url('group/make-request/admin/'.$group->id.'/'.$sent_request->id)}}" class="group-user-make-admin">Make Admin</a></li>
									<li><a href="javascript:void(0);" data-request_id="{{$sent_request->id}}" data-requester_name="{{$sent_request->username}}" class="group-member-remove-request">Remove from Group</a></li>
								</ul>
							</li>
						</ul>
					<?php endif; ?>
					{{-- end --}}
				</li>
			<?php endforeach; ?>
			{{-- end --}}
		</ul>
		{{-- end --}}
	</div>
</div>

<div class="modal fade" id="group-member-remove-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="group-member-remove-modal-form" action="{{route('group.remove.member')}}" method="post">
				<input type="hidden" name="group_id" value="{{$group->id}}">
				<input type="hidden" name="remove_user_id" id="remove_user_id" value="">
				<input type="hidden" name="remove_member_level" id="remove_member_level" value="">
				{{ csrf_field() }}
				<div class="modal-header">
					<h4 class="modal-title">Remove member</h4>
					<ul class="card-actions icons right-top">
						<li>
							<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
								<i class="zmdi zmdi-close"></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="modal-body">
					<h4>Remove <span class="remove-member-name">member</span> from the group?</h4>
					<div class="remove-type-select-container">
						<div class="form-group">
							<div class="togglebutton m-b-15">
							  <label><input type="checkbox" class="toggle-info" name="post_delete"> Delete all of <span class="remove-member-name">member</span>'s posts</label>
							</div>
						</div>
						<div class="form-group">
							<div class="togglebutton m-b-15">
							  <label><input type="checkbox" class="toggle-info" name="comment_delete"> Delete <span class="remove-member-name">member</span>'s comments</label>
							</div>
						</div>
					</div>
					<div class="remove-member-as-block-container">
						<div class="form-group">
							<div class="togglebutton m-b-15">
							  <label><input type="checkbox" class="toggle-info" name="block_member"> Block member </label>
							  <p><span>Member</span> won't be able to find, see or join this group.</p>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary">Remove</button>
				</div>
			</form>
		</div>
	</div>
</div>
