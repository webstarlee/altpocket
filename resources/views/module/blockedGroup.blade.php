<div class="col-xl-8 order-xl-1 col-lg-8 col-md-8 col-sm-12 col-xs-12">
	<div class="ui-block">
		<?php
			$just_group_member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
			if ($just_group_member_check > 0) {
				$just_group_member = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->first();
			}
			$blocked_member_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 2)->count();
		?>
		<div class="ui-block-title">
			<h4 class="title">Blocked · {{$blocked_member_count}}</h4>
		</div>
		<ul class="notification-list friend-requests group-member-view-container">
			<?php
				$group_members = $group_members = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 2)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('io_group_users.user_level', 'users.*')->get();
			?>
			<?php foreach ($group_members as $group_member): ?>
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
					<?php if ($group->user_id == Auth::user()->id || ($just_group_member_check > 0 && $just_group_member->user_level == 1)): ?>
						<ul class="card-actions icons group-member-view-cardaction-more">
							<li class="dropdown">
								<a href="javascript:void(0)" data-toggle="dropdown">
									<i class="zmdi zmdi-more-vert"></i>
								</a>
								<ul class="dropdown-menu dropdown-menu-right btn-primary">
									<li><a href="{{url('group/remove/block-member/'.$group->id.'/'.$group_member->id)}}" class="group-blocked-member-remove">Remove block</a></li>
								</ul>
							</li>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
