<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12 group-intro-block">
	<div class="group-left-sidebar-container">
		<h2 class="group-title">
			<a href="#">{{$group->name}}</a><br />
			<?php if ($group->private == 1): ?>
				<span class=""><i class="zmdi zmdi-lock"></i> Private group </span>
			<?php else: ?>
				<span class=""><i class="zmdi zmdi-globe"></i> Public group </span>
			<?php endif; ?>
		</h2>
		<ul class="group-sidebar-menu-container">
			<?php
				$group_member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
			?>
			@if ($group->private == 0)
				<li @if( Route::currentRouteName()=='group.single.view.about') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif > <a href="{{url('group/view/'.$group->url.'/about')}}">About</a> </li>
				<li @if( Route::currentRouteName()=='group.single.view') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url)}}">Discussion</a> </li>
				<li @if( Route::currentRouteName()=='group.single.view.members' || Route::currentRouteName()=='group.single.view.blocked') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url.'/members')}}">Members</a> </li>
				<li @if( Route::currentRouteName()=='group.single.view.photos') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url.'/photos')}}">Photos</a> </li>
				@if ($group_member_check > 0 || $group->user_id == Auth::user()->id)
					<li class="group-sidemenu-item"> <a href="{{url('group/invite-key/'.$group->id)}}" class="get-group-invite-key-btn">Invite Link</a> </li>
				@endif
			@elseif ($group->private == 1)
				@if ($group_member_check > 0 || $group->user_id == Auth::user()->id)
					<li @if( Route::currentRouteName()=='group.single.view.about') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif > <a href="{{url('group/view/'.$group->url.'/about')}}">About</a> </li>
					<li @if( Route::currentRouteName()=='group.single.view') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url)}}">Discussion</a> </li>
					<li @if( Route::currentRouteName()=='group.single.view.members' || Route::currentRouteName()=='group.single.view.blocked') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url.'/members')}}">Members</a> </li>
					<li @if( Route::currentRouteName()=='group.single.view.photos') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url.'/photos')}}">Photos</a> </li>
					<li class="group-sidemenu-item"> <a data-toggle="modal" href="#invite-link-modal">Invite Link</a> </li>
				@else
					<li @if( Route::currentRouteName()=='group.single.view.about') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif > <a href="{{url('group/view/'.$group->url.'/about')}}">About</a> </li>
					<li @if( Route::currentRouteName()=='group.single.view.members' || Route::currentRouteName()=='group.single.view.blocked') class="group-sidemenu-item active" @else class="group-sidemenu-item"  @endif> <a href="{{url('group/view/'.$group->url.'/members')}}">Members</a> </li>
				@endif
			@endif
		</ul>
	</div>
</div>
