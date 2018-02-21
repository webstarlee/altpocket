@extends('layouts.app2')
@section('title')
Explore Groups
@endsection
@section('css_plugin')
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('Bootstrap/dist/css/bootstrap-reboot.css')}}"> --}}
	<link rel="stylesheet" type="text/css" href="{{asset('Bootstrap/dist/css/bootstrap-grid.css')}}">
	<link href="{{asset('js/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('js/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{asset('js/sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css" />

	<!-- Main Styles CSS -->
	<link rel="stylesheet" type="text/css" href="{{asset('css/main.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/fonts.min.css')}}">

	<!-- Main Font -->
	<script src="{{asset('js/webfontloader.min.js')}}"></script>
	<script>
		WebFont.load({
			google: {
				families: ['Roboto:300,400,500,700:latin']
			}
		});
	</script>
@endsection
@section('content')
	<div id="content_wrapper">
		<div id="header_wrapper" class="header-sm">
	        <div class="container-fluid">
	            <div class="row">
	                <div class="col-xs-12">
	                    <header id="header">
	                        <h1>Groups</h1>
	                    </header>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="main-header">
			<div class="content-bg-wrap">
				<div class="content-bg bg-group"></div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
						<div class="main-header-content">
							<h1>Manage your friend Groups</h1>
						</div>
					</div>
				</div>
			</div>
			<img class="img-bottom" src="img/group-bottom.png" alt="friends">
		</div>
		<!-- ... end Main Header Groups -->

		<!-- Main Content Groups -->
		<div class="container">
			<header class="card-heading p-0">
				<div class="tabpanel m-b-20">
					<ul class="nav nav-tabs nav-justified group-tap-header">
						<li role="presentation" class="active"><a href="#your-manage-group" data-toggle="tab" aria-expanded="true">Groups<div class="ripple-container"></div></a></li>
						{{-- <li role="presentation" class=""><a href="#your-joined-group" data-toggle="tab" aria-expanded="false">Your Group<div class="ripple-container"></div></a></li> --}}
						<li role="presentation" class=""><a href="#discover-group" data-toggle="tab" aria-expanded="false">Discover<div class="ripple-container"></div></a></li>
						<li role="presentation" class=""><a href="#group-join-request" data-toggle="tab" aria-expanded="false">Pending Invite<div class="ripple-container"></div></a></li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<div class="tab-pane fadeIn active" id="your-manage-group">
							<div class="row">
								<div class="col-md-12">
									<h1 style="margin-top: 0; margin-bottom: 20px;">Manage Your Groups</h1>
								</div>
							</div>
							<div class="row">
								<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-xs-6">
									<div class="friend-item ui-block explore-group-single friend-groups create-group" data-mh="friend-groups-item" style="min-height: 150px;">
										<a href="#" class="  full-block" data-toggle="modal" data-target="#create-friend-group-1"></a>
										<div class="content">
											<a href="#" class="  btn btn-control bg-blue" data-toggle="modal" data-target="#create-friend-group-1">
												<svg class="olymp-plus-icon"><use xlink:href="svg-icons/sprites/icons.svg#olymp-plus-icon"></use></svg>
											</a>
											<div class="author-content">
												<a href="#" class="h5 author-name" data-toggle="modal" data-target="#create-friend-group-1">Create New Group</a>
											</div>
										</div>
									</div>
								</div>
								@foreach ($groups as $group)
									@if ($group->user_id == Auth::user()->id)
										<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-xs-6">
											<div class="ui-block explore-group-single" data-mh="friend-groups-item">
												<div class="friend-item friend-groups">
													<div class="friend-item-content">
														<div class="friend-avatar">
															<div class="author-thumb">
																@if ($group->image != "" && file_exists('assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail))
																	<a href="{{url('group/view/'.$group->url)}}"><img src="{{'assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail}}" alt="Olympus"></a>
																@else
																	<a href="{{url('group/view/'.$group->url)}}"><img src="{{asset('img/default_group_pic.png')}}" alt="Olympus"></a>
																@endif
															</div>
															<div class="author-content">
																<a href="{{url('group/view/'.$group->url)}}" class="h5 author-name">{{$group->name}}</a>
															</div>
														</div>
														<?php
															$group_member_count_1 = \App\IoGroupUser::where('group_id', $group->id)->count();
															$group_member_count_2 = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->count();
															$total_member = $group_member_count_1+1;

															$group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
															$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
															$group_owner = \App\User::find($group->user_id);
															$foreach_count = 0;
														?>
														<ul class="friends-harmonic">
															<li>
																<a href="javascript:void(0)">
																	@if($group_owner->avatar == "default.jpg")
																			<img src="/assets/img/default.png" alt="" class="photo">
																	@else
																		@if (file_exists('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar))
																			<img src="{{asset('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar)}}" alt="photo">
																		@else
																			<img src="/assets/img/default.png" alt="" class="photo">
																		@endif
																	@endif
																</a>
															</li>
															@foreach ($group_members as $group_member)
																<?php $foreach_count += 1; ?>
																@if ($foreach_count < 5)
																	<li>
																		<a href="javascript:void(0)">
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
																@endif
															@endforeach
														</ul>
														<div class="country">{{$total_member}} members in the Group</div>
														<div class="control-block-button">
															<a  class="btn btn-danger full-width own-group-delete-btn" href="{{url('group/own-delete/'.$group->id)}}" style="padding: 10px;margin:0;"><i class="zmdi zmdi-delete"></i> Delete Group</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									@endif
								@endforeach
							</div>
							<div class="row">
								<?php $isyourgroup_count = 0; ?>
								@foreach ($groups as $group)
									<?php
										$joined_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->first();
										if ($joined_count && $joined_count->user_level != 2) {
											$isyourgroup_count += 1;
										}
									?>
								@endforeach

								@if ($isyourgroup_count > 0)
									<div class="col-md-12">
										<h1 style="margin-top: 20px; margin-bottom: 20px;">Your Groups</h1>
									</div>
								@endif
							</div>
							<div class="row">
								@foreach ($groups as $group)
									<?php
										$joined_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->first();
									?>
									@if ($joined_count && $joined_count->user_level != 2 && $group->user_id != Auth::user()->id)
										<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-xs-6">
											<div class="ui-block explore-group-single" data-mh="friend-groups-item">
												<div class="friend-item friend-groups">
													<div class="friend-item-content">
														<div class="friend-avatar">
															<div class="author-thumb">
																@if ($group->image != "" && file_exists('assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail))
																	<a href="{{url('group/view/'.$group->url)}}"><img src="{{'assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail}}" alt="Olympus"></a>
																@else
																	<a href="{{url('group/view/'.$group->url)}}"><img src="{{asset('img/default_group_pic.png')}}" alt="Olympus"></a>
																@endif
															</div>
															<div class="author-content">
																<a href="{{url('group/view/'.$group->url)}}" class="h5 author-name">{{$group->name}}</a>
															</div>
														</div>
														<?php
															$group_member_count_1 = \App\IoGroupUser::where('group_id', $group->id)->count();
															$group_member_count_2 = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->count();
															$total_member = $group_member_count_1 + $group_member_count_2 +1;

															$group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
															$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
															$group_owner = \App\User::find($group->user_id);
															$foreach_count = 0;
														?>
														<ul class="friends-harmonic">
															<li>
																<a href="javascript:void(0)">
																	@if($group_owner->avatar == "default.jpg")
																			<img src="/assets/img/default.png" alt="" class="photo">
																	@else
																		@if (file_exists('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar))
																			<img src="{{asset('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar)}}" alt="photo">
																		@else
																			<img src="/assets/img/default.png" alt="" class="photo">
																		@endif
																	@endif
																</a>
															</li>
															@foreach ($group_members as $group_member)
																<?php $foreach_count += 1; ?>
																@if ($foreach_count < 6)
																	<li>
																		<a href="javascript:void(0)">
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
																@endif
															@endforeach
														</ul>
														<div class="country">{{$total_member}} Friends in the Group</div>
														<div class="control-block-button">
															<a  class="btn btn-danger full-width user_leave_group_btn" href="{{url('group/leave-group/'.$group->id)}}" style="padding: 10px;margin:0;">Leave Group</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									@endif
								@endforeach
							</div>
						</div>
						<div class="tab-pane fadeIn" id="discover-group">
							<div class="row">
								<div class="col-md-12">
									<h1 style="margin-top: 0; margin-bottom: 20px;">Discover Group</h1>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php
										$discover_group_count = 0;
										foreach ($groups as $group){
											$joined_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
											if ($joined_count == 0 && $group->user_id != Auth::user()->id && $group->private == 0) {
												$discover_group_count += 1;
											}
										}
									?>
									@if ($discover_group_count > 0)
										@foreach ($groups as $group)
											<?php
												$joined_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
												$pending_invite_count = \App\IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 1)->count();
												$check_sent_request_user = \App\IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();
											?>
											@if ($joined_count == 0 && $group->user_id != Auth::user()->id && $group->private == 0 && $pending_invite_count == 0)
												<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-xs-6">
													<div class="ui-block explore-group-single" data-mh="friend-groups-item">
														<div class="friend-item friend-groups">
															<div class="friend-item-content">
																<div class="friend-avatar">
																	<div class="author-thumb">
																		@if ($group->image != "" && file_exists('assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail))
																			<a href="{{url('group/view/'.$group->url)}}"><img src="{{'assets/images/group/'.$group->url.'/thumbnail'.'/'.$group->thumbnail}}" alt="Olympus"></a>
																		@else
																			<a href="{{url('group/view/'.$group->url)}}"><img src="{{asset('img/default_group_pic.png')}}" alt="Olympus"></a>
																		@endif
																	</div>
																	<div class="author-content">
																		<a href="{{url('group/view/'.$group->url)}}" class="h5 author-name">{{$group->name}}</a>
																	</div>
																</div>
																<?php
																	$group_member_count_1 = \App\IoGroupUser::where('group_id', $group->id)->count();
																	$group_member_count_2 = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->count();
																	$total_member = $group_member_count_1+1;

																	$group_members = \App\IoGroupUser::where('group_id', $group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
																	$sent_requests = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
																	$group_owner = \App\User::find($group->user_id);
																	$foreach_count = 0;
																?>
																<ul class="friends-harmonic">
																	<li>
																		<a href="javascript:void(0)">
																			@if($group_owner->avatar == "default.jpg")
																					<img src="/assets/img/default.png" alt="" class="photo">
																			@else
																				@if (file_exists('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar))
																					<img src="{{asset('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar)}}" alt="photo">
																				@else
																					<img src="/assets/img/default.png" alt="" class="photo">
																				@endif
																			@endif
																		</a>
																	</li>
																	@foreach ($group_members as $group_member)
																		<?php $foreach_count += 1; ?>
																		@if ($foreach_count < 6)
																			<li>
																				<a href="javascript:void(0)">
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
																		@endif
																	@endforeach
																</ul>
																<div class="country">{{$total_member}} Friends in the Group</div>
																<div class="control-block-button">
																	<a  class="btn btn-danger full-width cancel-group-join-request-user" @if ($check_sent_request_user > 0 ) style="display:block" @else style="display:none" @endif data-join_cancel_url="{{url('group/join-user-cancel/'.$group->id)}}" style="padding: 10px;margin:0;"><i class="zmdi zmdi-check"></i> Reguest sent</a>
																	<a  class="btn btn-primary full-width send-group-join-request-user" @if ($check_sent_request_user == 0 ) style="display:block" @else style="display:none" @endif data-join_url="{{url('group/join-user/'.$group->id)}}" style="padding: 10px;margin:0;"><i class="zmdi zmdi-plus" style="font-weight: 100;"></i> Join Group</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											@endif
										@endforeach
									@else
										<h1>Group Not found</h1>
									@endif
								</div>
							</div>
						</div>
						<div class="tab-pane fadeIn" id="group-join-request">
							<div class="row">
								<div class="col-md-12">
									<h1 style="margin-top: 0; margin-bottom: 20px;">Pending Invites</h1>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@if ($invite_groups == "null")
										<h1>Invite Not found</h1>
									@else
										@foreach ($invite_groups as $invite_group)
											<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-xs-6">
												<div class="ui-block explore-group-single" data-mh="friend-groups-item">
													<div class="friend-item friend-groups">
														<div class="friend-item-content">
															<div class="friend-avatar">
																<div class="author-thumb">
																	@if ($group->image != "" && file_exists('assets/images/group/'.$invite_group->url.'/thumbnail'.'/'.$invite_group->thumbnail))
																		<a href="{{url('group/view/'.$invite_group->url)}}"><img src="{{'assets/images/group/'.$invite_group->url.'/thumbnail'.'/'.$invite_group->thumbnail}}" alt="Olympus"></a>
																	@else
																		<a href="{{url('group/view/'.$invite_group->url)}}"><img src="{{asset('img/default_group_pic.png')}}" alt="Olympus"></a>
																	@endif
																</div>
																<div class="author-content">
																	<a href="{{url('group/view/'.$invite_group->url)}}" class="h5 author-name">{{$invite_group->name}}</a>
																</div>
															</div>
															<?php
																$group_member_count_1 = \App\IoGroupUser::where('group_id', $invite_group->id)->count();
																$group_member_count_2 = \App\IoGroupRequest::where('group_id', $invite_group->id)->where('method', 1)->count();
																$total_member = $group_member_count_1+1;

																$group_members = \App\IoGroupUser::where('group_id', $invite_group->id)->join('users', 'users.id', '=', 'io_group_users.user_id')->select('users.*')->get();
																$sent_requests = \App\IoGroupRequest::where('group_id', $invite_group->id)->where('method', 1)->join('users', 'users.id', '=', 'io_group_requests.user_id')->select('io_group_requests.created_at as receive_time','io_group_requests.id as request_id','users.*')->get();
																$group_owner = \App\User::find($group->user_id);
																$foreach_count = 0;
															?>
															<ul class="friends-harmonic">
																<li>
																	<a href="javascript:void(0)">
																		@if($group_owner->avatar == "default.jpg")
																				<img src="/assets/img/default.png" alt="" class="photo">
																		@else
																			@if (file_exists('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar))
																				<img src="{{asset('uploads/avatars/'.$group_owner->id.'/'.$group_owner->avatar)}}" alt="photo">
																			@else
																				<img src="/assets/img/default.png" alt="" class="photo">
																			@endif
																		@endif
																	</a>
																</li>
																@foreach ($group_members as $group_member)
																	<?php $foreach_count += 1; ?>
																	@if ($foreach_count < 6)
																		<li>
																			<a href="javascript:void(0)">
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
																	@endif
																@endforeach
															</ul>
															<div class="country">{{$total_member}} Friends in the Group</div>
															<div class="control-block-button">
																<a href="{{url('group/join/'.$invite_group->id)}}" class="btn btn-primary full-width" style="padding: 10px;margin:0;"><i class="zmdi zmdi-plus" style="font-weight: 100;"></i> Join </a>
																<a href="{{url('group/decline_group/'.$invite_group->id)}}" id="group_join_request_decline_btn" class="btn btn-danger full-width" style="padding: 10px;margin:0;"><i class="zmdi zmdi-check"></i> Decline</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										@endforeach
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>
		</div>
		<!-- Window-popup Create Friends Group -->
		@include('module.newGroupForm')
		<!-- ... end Window-popup Create Friends Group -->
	</div>
@endsection
@section('js_plugin')
    <script src="{{asset('js/jquery.appear.js')}}"></script>
    <script src="{{asset('js/jquery.mousewheel.js')}}"></script>
    <script src="{{asset('js/perfect-scrollbar.js')}}"></script>
    <script src="{{asset('js/jquery.matchHeight.js')}}"></script>
    <script src="{{asset('js/svgxuse.js')}}"></script>
    <script src="{{asset('js/imagesloaded.pkgd.js')}}"></script>
    <script src="{{asset('js/Headroom.js')}}"></script>
    <script src="{{asset('js/velocity.js')}}"></script>
    <script src="{{asset('js/ScrollMagic.js')}}"></script>
    <script src="{{asset('js/jquery.waypoints.js')}}"></script>
    <script src="{{asset('js/jquery.countTo.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{asset('js/material.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-select.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/jquery.stellar.js')}}"></script>

	<script src="{{asset('js/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

	<script src="{{asset('js/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/select2/components-select2.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/groups/autosize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/groups/group.js')}}" type="text/javascript"></script>

    <script src="{{asset('js/smooth-scroll.js')}}"></script>
    <script src="{{asset('js/selectize.js')}}"></script>
    <script src="{{asset('js/swiper.jquery.js')}}"></script>
    <script src="{{asset('js/moment.js')}}"></script>
    <script src="{{asset('js/daterangepicker.js')}}"></script>
    <script src="{{asset('js/simplecalendar.js')}}"></script>
    <script src="{{asset('js/fullcalendar.js')}}"></script>
    <script src="{{asset('js/isotope.pkgd.js')}}"></script>
    <script src="{{asset('js/ajax-pagination.js')}}"></script>
    <script src="{{asset('js/Chart.js')}}"></script>
    <script src="{{asset('js/chartjs-plugin-deferred.js')}}"></script>
    <script src="{{asset('js/circle-progress.js')}}"></script>
    <script src="{{asset('js/loader.js')}}"></script>
    <script src="{{asset('js/run-chart.js')}}"></script>
    <script src="{{asset('js/jquery.magnific-popup.js')}}"></script>
    <script src="{{asset('js/jquery.gifplayer.js')}}"></script>
    <script src="{{asset('js/mediaelement-playlist-plugin.min.js')}}"></script>

    <script src="{{asset('js/base-init.js')}}"></script>
@endsection
