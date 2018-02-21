@extends('layouts.app2')

@section('title')
{{$user->username}}'s Profile
@endsection


@section('css')
  <link rel="stylesheet" type="text/css" href="/version3/css/theme-styles.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/blocks.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/fonts.css">

  <style>
  @media (min-width: 1400px)
.container {
    max-width: 1300px;
}

@media (min-width: 1199px)
.container {
    max-width: 1110px;
}
@media (min-width: 1024px)
.container {
    max-width: 900px;
}
@media (min-width: 800px)
.container {
    max-width: 680px;
}
@media (min-width: 540px)
.container {
    max-width: 600px;
}
.container {
    margin-right: auto;
    margin-left: auto;
    padding-right: 15px;
    padding-left: 15px;
    padding-top:40px;
    width:100%;
    max-width:1300px;
}
.top-header-thumb img {
  height:420px;
}
.top-header-author .author-thumb img {
  width:124px;
}
.ml-auto {
    margin-left: auto !important;
}
.row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -15px;
  margin-left: -15px;
}
.emblem {
  width:18px;
  vertical-align:middle;
  position:absolute;
  margin-left:-20px;
}
.Founder {
  color: #375492!important;
}
.VIP {
  color: rgb(85, 179, 167)!important;
}
.Premium {
  color: rgb(85, 148, 179)!important;
}
.Mod {
  color: #4a9c51!important;
}
.Admin {
  color: #c3403f!important;
}
.Staff {
  color: #009688!important;
}
.Donator {
  color: #ffb95f!important;
}
.Advisor {
  color: #03a9f4!important;
}
.btn-control .more-dropdown {
  font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
}
.control-block-button svg {
  display: block;
  margin: 0 auto;
  vertical-align: middle;
  margin-top: 14px;
}
.control-block-button .btn-control:last-child {
  margin-right:20px;
}
</style>

@endsection



@section('content')


<div id="content_wrapper" class="">
  <div class="container">
  	<div class="row">
  		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
  			<div class="ui-block">
  				<div class="top-header">
  					<div class="top-header-thumb">
              @if($user->header != "default")
  						<img src="/uploads/headers/{{$user->id}}/{{$user->header}}" alt="Header">
            @else
              <img src="https://altpocket.io/assets/img/headers/header-lg-03.jpg" alt="Header">
            @endif
  					</div>
  					<div class="profile-section">
  						<div class="row">
  							<div class="col-lg-5 col-md-5 ">
  								<ul class="profile-menu">
  									<li>
  										<a href="02-ProfilePage.html" class="active">Timeline</a>
  									</li>
  									<li>
  										<a href="05-ProfilePage-About.html">About</a>
  									</li>
  									<li>
  										<a href="06-ProfilePage.html">Friends</a>
  									</li>
  								</ul>
  							</div>
  							<div class="col-lg-5 ml-auto col-md-5">
  								<ul class="profile-menu">
  									<li>
  										<a href="07-ProfilePage-Photos.html">Photos</a>
  									</li>
  									<li>
  										<a href="09-ProfilePage-Videos.html">Videos</a>
  									</li>
  									<li>
  										<div class="more">
  											<svg class="olymp-three-dots-icon"><use xlink:href="/version3/icons/icons.svg#olymp-three-dots-icon"></use></svg>
  											<ul class="more-dropdown more-with-triangle">
  												<li>
  													<a href="#">Report Profile</a>
  												</li>
  												<li>
  													<a href="#">Block Profile</a>
  												</li>
  											</ul>
  										</div>
  									</li>
  								</ul>
  							</div>
  						</div>

    						<div class="control-block-button">
              @if(Auth::user() != $user)
    							<a href="javascript:void(0)" class="btn btn-control bg-blue" id="addFriend" data-toggle="tooltip" data-placement="top" data-original-title="Add {{$user->username}} as friend">
    								<svg class="olymp-happy-face-icon"><use xlink:href="/version3/icons/icons.svg#olymp-happy-face-icon"></use></svg>
    							</a>

                  @if(Auth::user()->isFollowing($user))
                    <a href="javascript:void(0)" id="unFollowUser" class="btn btn-control bg-purple" data-toggle="tooltip" data-placement="top" data-original-title="Unfollow {{$user->username}}">
      								<svg class="olymp-magnifying-glass-icon"><use xlink:href="/version3/icons/icons.svg#olymp-magnifying-glass-icon"></use></svg>
      							</a>
                    <a href="javascript:void(0)" id="followUser" class="btn btn-control bg-purple" data-toggle="tooltip" data-placement="top" data-original-title="Follow {{$user->username}}" style="display:none;">
                      <svg class="olymp-magnifying-glass-icon"><use xlink:href="/version3/icons/icons.svg#olymp-magnifying-glass-icon"></use></svg>
                    </a>
                  @else
                    <a href="javascript:void(0)" id="followUser" class="btn btn-control bg-purple" data-toggle="tooltip" data-placement="top" data-original-title="Follow {{$user->username}}">
      								<svg class="olymp-magnifying-glass-icon"><use xlink:href="/version3/icons/icons.svg#olymp-magnifying-glass-icon"></use></svg>
      							</a>
                    <a href="javascript:void(0)" id="unFollowUser" class="btn btn-control bg-purple" data-toggle="tooltip" data-placement="top" data-original-title="Unfollow {{$user->username}}" style="display:none;">
                      <svg class="olymp-magnifying-glass-icon"><use xlink:href="/version3/icons/icons.svg#olymp-magnifying-glass-icon"></use></svg>
                    </a>
                  @endif

                  <a href="javascript:void(0)" class="btn btn-control bg-red"  data-toggle="tooltip" data-placement="top" data-original-title="Like {{$user->username}}'s portfolio">
                    <svg class="olymp-magnifying-glass-icon"><use xlink:href="/version3/icons/icons.svg#olymp-heart-icon"></use></svg>
                  </a>
                @else
    							<div class="btn btn-control bg-primary more">
    								<svg class="olymp-settings-icon"><use xlink:href="/version3/icons/icons.svg#olymp-settings-icon"></use></svg>

    								<ul class="more-dropdown more-with-triangle triangle-bottom-right">
    									<li>
    										<a href="#" data-toggle="modal" data-target="#update-header-photo">Update Profile Photo</a>
    									</li>
    									<li>
    										<a href="#" data-toggle="modal" data-target="#update-header-photo">Update Header Photo</a>
    									</li>
    									<li>
    										<a href="/settings">Account Settings</a>
    									</li>
    								</ul>
    							</div>
                @endif
  						</div>
  					</div>
  					<div class="top-header-author">
  						<a href="02-ProfilePage.html" class="author-thumb">
                @if($user->avatar != "default.jpg")
    							<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="avatar">
                @else
                  <img src="https://altpocket.io/assets/img/default.png" alt="avatar">
                @endif
  						</a>
  						<div class="author-content">
  							<a href="02-ProfilePage.html" class="h4 author-name {{$user->groupName()}}" style="color:{{$user->groupColor()}}!important">
                  @if($user->hasRights())<img class="emblem" src="https://altpocket.io/awards/admin.png">@elseif($user->isDonator())<img class="emblem" src="https://altpocket.io/awards/diamondd.png">@endif
                  {{$user->username}}</a>
  							<div class="country">{{$user->bio}}</div>
  						</div>
  					</div>
  				</div>
  			</div>
  		</div>
  	</div>
  </div>

</div>



@endsection

@section('js')
<script>
// For notifications
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "0",
  "hideDuration": "0",
  "timeOut": "0",
  "extendedTimeOut": "0",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}



$("#followUser").click(function(){
  $.ajax({
      dataType: "json",
      url: '/follow/{{$user->username}}',
      success: function(data){
      }
  });
  $(this).hide();
  $("#unFollowUser").show();
  Command: toastr['success']('You started following {{$user->username}}.');
});

$("#unFollowUser").click(function(){
  $.ajax({
      dataType: "json",
      url: '/unfollow/{{$user->username}}',
      success: function(data){
      }
  });
  $(this).hide();
  $("#followUser").show();
  Command: toastr['success']('You have unfollowed {{$user->username}}.');
});

$("#addFriend").click(function(){
  $.ajax({
      url: '/add/{{$user->username}}'
  }).done(function(data){
        notify(data)
  });
});

function notify(data)
{
  var type = "success";

  if(data != "You sent a friend request to {{$user->username}}!")
  {
    type = "warning";
  }
  Command: toastr[type](data);
}

</script>

@endsection
