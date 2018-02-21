<div class="col-xl-12">
	<div class="ui-block">
		<div class="top-header top-header-favorit">
			<div class="top-header-thumb">
				<div class="group-cover-photo-div">
					<div class="group-cover-photo-view-container">
						@if ($group->image != "" && file_exists('assets/images/group/'.$group->url.'/'.$group->image))
							<img class="group-cover-photo-view-img show-edit-btn" src="{{asset('assets/images/group/'.$group->url.'/'.$group->image)}}" alt="nature">
						@else
							<img class="group-cover-photo-view-img show-edit-btn" src="{{asset('img/top-header2.jpg')}}" alt="nature">
							@if ($group->user_id == Auth::user()->id)
								<div class="group-select-image-alert-div">
									<div class="button-container-div">
										<a href="#" type="button" class="upload-group-couver-pic" data-toggle="modal" data-target="#group_cover_photo_upload">Photo Upload<div class="ripple-container"></div></a>
									</div>
								</div>
							@endif
						@endif
						@if ($group->user_id == Auth::user()->id)
							<div class="group-cover-photo-setting-btn-div">
								<li class="dropdown">
									<a href="javascript:void(0)" class="group-cover-photo-setting-btn-parent" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-pencil"></i> Cover Photo setting</a>
									<ul class="dropdown-menu btn-primary dropdown-menu-right">
										@if ($group->image != "")
											<li><a class="choose-method upload-group-couver-pic" data-toggle="modal" href="#group_cover_photo_upload">Change photo...</a></li>
											<li><a class="choose-method remove-group-couver-pic" data-delete_url="{{url('group/photo/delete')}}" href="javascript:void(0);">Remove photo...</a></li>
										@else
											<li><a class="choose-method upload-group-couver-pic" data-toggle="modal" data-target="#group_cover_photo_upload">Upload photo...</a></li>
										@endif
									</ul>
								</li>
							</div>
						@endif
					</div>
				</div>
			</div>
			<div class="profile-section">
				<div class="row">
					<div class="col-xl-8 m-auto col-lg-8 col-md-12">
						<ul class="profile-menu">
							@if ($group->user_id == Auth::user()->id || $member_check>0)
								<li><a class="group-bottom-action-btn" data-toggle="modal" data-target="#add-group-member-modal">Add Member</a></li>
							@elseif ($request_check > 0)
								<li><a class="group-bottom-action-btn" href="{{url('group/join/'.$group->id)}}">Accept</a></li>
								<li><a class="group-bottom-action-btn background-danger-btn" id="group_join_request_decline_btn_on" href="{{url('group/decline_group/'.$group->id)}}" >Decline</a></li>
							@elseif ($user_request_check > 0)
								<li class="dropdown">
									<a href="javascript:void(0)" class="group-bottom-action-btn" data-toggle="dropdown">Pending</a>
									<ul class="dropdown-menu cancel-pending-btn">
										<li><a href="{{url('group/join-user-cancel-on/'.$group->id)}}">Cancel Request</a></li>
									</ul>
								</li>
							@else
								<li><a class="group-bottom-action-btn" href="{{url('group/join-user-on/'.$group->id)}}">Join Group</a></li>
							@endif
							<li><a class="group-bottom-action-btn" data-toggle="modal" href="#create-friend-group-1">Create New Group</a></li>
							<li class="dropdown">
								<a href="javascript:void(0)" class="group-bottom-action-btn" data-toggle="dropdown" ><i class="zmdi zmdi-more"></i> More</a>
								<ul class="dropdown-menu dropdown-menu-right btn-primary group-more-dropdown-btn">
									<?php
										$group_member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
									?>
									@if ($group->private == 0)
										<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/about')}}">About</a></li>
										<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url)}}">Discuss</a></li>
										<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/photos')}}">Photos</a></li>
										<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/members')}}">Members</a></li>
										@if ($group_member_check > 0 || $group->user_id == Auth::user()->id)
											<li><a class="group-bottom-action-btn" data-toggle="modal" href="#invite-link-modal">Invite Link</a></li>
										@endif
									@elseif ($group->private == 1)
										@if ($group_member_check > 0 || $group->user_id == Auth::user()->id)
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/about')}}">About</a></li>
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url)}}">Discuss</a></li>
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/photos')}}">Photos</a></li>
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/members')}}">Members</a></li>
											<li><a class="group-bottom-action-btn" data-toggle="modal" href="#invite-link-modal">Invite Link</a></li>
										@else
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/about')}}">About</a></li>
											<li><a class="group-bottom-action-btn" href="{{url('group/view/'.$group->url.'/members')}}">Members</a></li>
										@endif
									@endif
									@if ($group->user_id == Auth::user()->id)
										<li><a href="#group_info_edit_modal" data-toggle="modal" class="group-bottom-action-btn">Edit Group</a></li>
										<li><a href="{{url('group/own-delete/'.$group->id)}}" id="own-group-delete-btn-on" class="group-bottom-action-btn background-danger-btn">Delete Group</a></li>
									@elseif ($member_check>0)
										<li><a class="group-bottom-action-btn background-danger-btn" id="user_leave_group_btn_on" href="{{url('group/leave-group/'.$group->id)}}">Leave Group</a></li>
									@endif
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
