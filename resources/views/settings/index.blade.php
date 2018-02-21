@extends('layouts.app2')

@section('title')
My Settings
@endsection

@section('css')

<link type="text/css" rel="stylesheet" href="/version2/css/blocks.css?v=1.4">
<link type="text/css" rel="stylesheet" href="/version2/css/bootstrap-select.css?v=2.7">
<link type="text/css" href="/css/slim.min.css" rel="stylesheet" type="text/css">
<style>
.slim {
    height:200px;
    width:200px;
    margin:0 auto;
}
.main-header {
    padding: 70px 0 190px 0;
    position: relative;
    background-position: 50% 50%;
    margin:0px !important;
    max-width:100%;
}
.content-bg-wrap {
    position: absolute;
    overflow: hidden;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
}
.content-bg {
    position: absolute;
    height: 100%;
    width: 100%;
    animation-name: sideupscroll;
    animation-duration: 30s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    animation-fill-mode: both;
    will-change: transform;
    z-index: -1;
}
.bg-account:before {
    background-image: url(https://theme.crumina.net/html-olympus/img/top-header3.png);
}
.content-bg:before {
    position: absolute;
    z-index: 1;
    left: 0;
    width: 200%;
    height: 400%;
    content: "";
}
.main-header-content {
    color: #fff;
    text-align: center;
}
.main-header-content > *:first-child {
    font-weight: 300;
    margin-bottom: 20px;
}
.main-header-content > * {
    color: inherit;
}
.main-header-content p {
    font-weight: 400;
    margin-bottom: 0;
}
.img-bottom {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translate(-50%, 0);
    max-width: 100%;
}
@keyframes sideupscroll {
  0% {
    /* transform: translate3D(0%, 0%, 0); */
    }
  50% {
    /* transform: translate3D(-50%, 0, 0); */
    }
  100% {
    transform: translate3D(-100%, 0, 0);
    } }
    h1, .h1 {
        font-size: 2.5rem;
    }
    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        margin-bottom: 0.5rem;
        font-family: inherit;
        font-weight: 700;
        line-height: 1.3;
        color: #515365;
    }
    .card-header a {
        color: #515365;
        display: block;
    }
    .mb-0 {
        margin-bottom: 0!important;
        margin-top:0px!important;
    }
    h6, .h6 {
        font-size: 0.875rem;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        background-color: #fff;
        border-bottom: 1px solid #e6ecf5;
    }
    .card-header:first-child {
        border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
    }
    .card-header i, .card-header svg {
        float: right;
        transition: all .3s ease;
        margin-top: 8px;
    }
    .card {
      margin-bottom:0px!important;
    }
    .form-group2 {
        position: relative!important;
    }
    .form-group2 {
    margin-bottom: 1rem!important;
}
.form-group2.label-static label.control-label2, .form-group2.label-placeholder label.control-label2, .form-group2.label-floating2 label.control-label2 {
    position: absolute;
    pointer-events: none;
    transition: 0.3s ease all;
}
.form-group2.label-floating2 label.control-label2, .form-group2.label-placeholder label.control-label2 {
    top: 16px;
    font-size: 14px;
    line-height: 1.42857;
    left: 20px;
}
.label-floating2 .form-control2, .label-floating2 input, .label-floating2 select {
    padding: 1.3rem 1.1rem .4rem;
    line-height: 1.8;
}
input, .form-control2 {
    color: #515365;
    line-height: inherit;
    font-size: .875rem;
}
select, input, .form-control2 {
    background-color: transparent;
}
@if(Auth::user()->theme == "normal")
.form-control2 {
    display: block;
    width: 100%;
    padding: 1.1rem 1.1rem;
    font-size: 0.812rem;
    line-height: 1.25;
    color: #464a4c;
    background-color: #fff;
    background-image: none;
    background-clip: padding-box;
    border: 1px solid #e6ecf5;
    border-radius: 0.25rem;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}
.form-control2:focus {
border-color: #3a94ff!important;
}
.togglebutton label input[type=checkbox]:checked + .toggle {
    background-color:#4FC5EA;
}

@else
.form-control2 {
    display: block;
    width: 100%;
    padding: 1.1rem 1.1rem;
    font-size: 0.812rem;
    line-height: 1.25;
    color: #464a4c;
    background-color: #252525;
    background-image: none;
    background-clip: padding-box;
    border: 1px solid #1f1f1f;
    border-radius: 0.25rem;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}
.form-control2:focus {
border-color: #70ea4f!important;
}
.togglebutton label input[type=checkbox]:checked + .toggle {
    background-color: #70ea4f;
}
@endif
a, area, button, [role="button"], input, label, select, summary, textarea {
    touch-action: manipulation;
}
input, button, select, textarea {
    line-height: inherit;
}
button, input, optgroup, select, textarea {
    font-family: sans-serif;
    font-size: 100%;
    line-height: 1.15;
    margin: 0;
}
label.control-label2 {
    color: #888da8;
    display: inline-block;
    margin-bottom: .5rem;
    font-weight:100!important;
}
.form-group2.is-focused .togglebutton label:hover, .form-group2.is-focused .togglebutton label:focus {
  color: rgba(0, 0, 0, 0.54); }
.form-group2.is-focused .checkbox label:hover, .form-group2.is-focused .checkbox label:focus {
color: rgba(0, 0, 0, 0.54); }
.news-feed-form .form-group2.label-floating2.is-focused .control-label2 {
top: 16px; }
.label-floating2.with-icon label.control-label2,
.label-placeholder.with-icon label.control-label2 {
  left: 70px; }

  .form-group2.label-floating2 label.control-label2,
  .form-group2.label-placeholder2 label.control-label2 {
    top: 16px;
    font-size: 14px;
    line-height: 1.42857;
    left: 20px; }

  .form-group2.label-static label.control-label2,
  .form-group2.label-floating2.is-focused label.control-label2,
  .form-group2.label-floating2:not(.is-empty) label.control-label2 {
    top: 10px;
    font-size: 11px;
    line-height: 1.07143; }

    .form-group2.label-static label.control-label2,
    .form-group2.label-placeholder label.control-label2,
    .form-group2.label-floating2 label.control-label2 {
      position: absolute;
      pointer-events: none;
      transition: 0.3s ease all; }

      .form-group2.with-icon i {
          display: block;
          position: absolute;
          left: 0;
          top: 0;
          height: 100%;
          width: 50px;
          text-align: center;
          line-height: 3.5rem;
          border-right: 1px solid #e6ecf5;
          font-size: 20px;
      }
      .label-floating2.with-icon .form-control2, .label-floating2.with-icon input, .label-floating2.with-icon textarea {
          padding-left: 70px;
      }
      .label-floating2.with-icon label.control-label2, .label-placeholder2.with-icon label.control-label2 {
          left: 70px;
      }
      .form-group2.label-static label.control-label2, .form-group2.label-placeholder label.control-label2, .form-group2.label-floating2 label.control-label2 {
          position: absolute;
          pointer-events: none;
          transition: 0.3s ease all;
      }
      .togglebutton label .toggle, .togglebutton label input[type=checkbox][disabled]+.toggle {
          content: "";
          display: inline-block;
          width: 66px;
          height: 30px;
          background-color: #899cb2;
          border-radius: 15px;
          margin-right: 15px;
          transition: background .3s ease;
          vertical-align: middle;
      }
      .togglebutton label input[type=checkbox]:checked+.toggle:after {
          left: 37px;
      }
      .togglebutton label .toggle:after {
    content: "";
    display: inline-block;
    width: 24px;
    height: 24px;
    line-height: 26px;
    background-color: #FFFFFF;
    border-radius: 20px;
    position: relative;
    left: 3px;
    top: 3px;
    transition: left 0.3s ease, background 0.3s ease, box-shadow 0.1s ease;
    text-align: center;
}
.active > a {
  color:#515365!important;
}
.notification-list .selectize-dropdown-content > *:hover, .notification-list li:hover {
    background-color: #fafbfd;
}
.notification-list .selectize-dropdown-content > *, .notification-list li {
    padding: 20px;
    border-bottom: 1px solid #e6ecf5;
    display: block;
    position: relative;
    transition: all .3s ease;
}
.notification-list .author-thumb {
    height: 40px;
    width: 40px;
}
.notification-list .selectize-dropdown-content > * > *, .notification-list li > * {
    margin-bottom: 0;
    display: inline-block;
    vertical-align: middle;
}
.olymp-three-dots-icon {
    width: 16px;
    height: 4px;
}
.olymp-little-delete {
    width: 8px;
    height: 8px;
}
.h1, .h2, .h3, .h4, .h5, .h6 {
    color: #515365!important;
}
ul {
    list-style: none;
    padding: 0;
}
.accept-request {
  color: white!important;
}
</style>
@endsection


@section('content')
    <div id="content_wrapper" class="">
      <div class="main-header">
      	<div class="content-bg-wrap">
      		<div class="content-bg bg-account"></div>
      	</div>
      	<div class="container">
      		<div class="row">
      			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      				<div class="main-header-content">
      					<h1>Your Account Dashboard</h1>
      					<p>Welcome to your account dashboard! Here you’ll find everything you need to change your
      						profile information, settings, read notifications and requests, view your latest messages,
      						change your pasword and much more! Also you can create or manage your own favourite page, have fun!
      					</p>
      				</div>
      			</div>
      		</div>
      	</div>

      	<img class="img-bottom" src="https://theme.crumina.net/html-olympus/img/account-bottom.png" alt="friends">
      </div>
      <div class="container" style="margin-top:25px;">
      	<div class="row">
      		<div class="col-xl-3 pull-xl-9 col-lg-3 pull-lg-9 col-md-12 col-sm-12 col-xs-12">
      			<div class="ui-block">
      				<div class="your-profile">
      					<div class="ui-block-title ui-block-title-small">
      						<h6 class="title">Your PROFILE</h6>
      					</div>

      					<div id="accordion" role="tablist" aria-multiselectable="true">
      						<div class="card">
      							<div class="card-header" role="tab" id="headingOne">
      								<h6 class="mb-0">
      									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
      										Profile Settings
      										<svg class="olymp-dropdown-arrow-icon"><use xlink:href="/version2/icons/icons.svg#olymp-dropdown-arrow-icon"></use></svg>
      									</a>
      								</h6>
      							</div>

      							<div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
      								<ul class="your-profile-menu">
      									<li class="active" role="presentation">
      										<a href="#profile-information" data-toggle="tab" aria-expanded="true">Profile Information</a>
      									</li>
      									<li class="" role="presentation">
      										<a href="#account-settings" data-toggle="tab" aria-expanded="true">Account Settings</a>
      									</li>
                        <li class="" role="presentation">
      										<a href="#change-password" data-toggle="tab" aria-expanded="true">Change Password</a>
      									</li>
                        <li class="" role="presentation">
      										<a href="#change-avatar" data-toggle="tab" aria-expanded="true">Change Avatar</a>
      									</li>
                        <li class="" role="presentation">
                          <a href="#change-header" data-toggle="tab" aria-expanded="true">Change Header</a>
                        </li>
                        <li class="" role="presentation">
                          <a href="#stat-widget-settings" data-toggle="tab" aria-expanded="true">Stat Widget</a>
                        </li>
                        <li class="" role="presentation">
                          <a href="#remove-account" data-toggle="tab" aria-expanded="true">Delete Account</a>
                        </li>
      								</ul>
      							</div>
      						</div>
      					</div>
                <div class="ui-block-title" role="presentation">
                  @if(Auth::user()->google2fa_secret)
      						<a href="/2fa/request/disable" class="h6 title"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Disable 2FA</a>
                  @else
                  <a href="/2fa/request" class="h6 title"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Enable 2FA</a>
                  @endif
      					</div>
                <div class="ui-block-title" role="presentation">
      						<a href="#notifications-tab" data-toggle="tab" aria-expanded="true" class="h6 title">Friend Requests</a>
      						<a href="#" class="items-round-little bg-blue" style="color:white;background-color:#ff5e3a!important;">{{count(Auth::user()->getFriendRequests())}}</a>
      					</div>
      					<div class="ui-block-title">
      						<a href="#" class="h6 title">Chat (Soon)</a>
      					</div>
      					<div class="ui-block-title ui-block-title-small">
      						<h6 class="title">GROUPS</h6>
      					</div>
      					<div class="ui-block-title">
      						<a href="#" class="h6 title">Create Group (Soon)</a>
      					</div>
      					<div class="ui-block-title">
      						<a href="#" class="h6 title">Group Settings (Soon)</a>
      					</div>
      				</div>
      			</div>
      		</div>


          <div class="col-xl-9 push-xl-3 col-lg-9 push-lg-3 col-md-12 col-sm-12 col-xs-12">
					<div class="tab-content">

            <div class="ui-block tab-pane fadeIn" id="notifications-tab">
              <div class="ui-block-title">
                <h6 class="title">Friend Requests</h6>
              </div>
              <ul class="notification-list friend-requests">
                @php
                  $friendrequests = Auth::user()->getFriendRequests();
                @endphp

                @foreach($friendrequests as $friend)
                  @if($friend->recipient_id == Auth::user()->id)
                    @php
                      $user = App\User::where('id', $friend->sender_id)->select('username', 'avatar', 'id')->first();
                    @endphp
          					<li>
          						<div class="author-thumb">
          							<img src="{{$user->getAvatar()}}" alt="author">
          						</div>
          						<div class="notification-event">
          							<a href="/user/{{$user->username}}" class="h6 notification-friend">{{$user->username}}</a>
          						</div>
          						<span class="notification-icon">
          							<a href="/accept/{{$user->username}}" class="accept-request">
          								<span class="icon-add">
          									<svg class="olymp-happy-face-icon"><use xlink:href="/version3/icons/icons.svg#olymp-happy-face-icon"></use></svg>
          								</span>
          								Accept Friend Request
          							</a>

          							<a href="/deny/{{$user->username}}" class="accept-request request-del">
          								<span class="icon-minus">
          									<svg class="olymp-happy-face-icon"><use xlink:href="/version3/icons/icons.svg#olymp-happy-face-icon"></use></svg>
          								</span>
          							</a>

          						</span>

          					</li>
                  @endif
                @endforeach

                <!--
      					<li class="accepted">
      						<div class="author-thumb">
      							<img src="/version3/img/avatar17-sm.jpg" alt="author">
      						</div>
      						<div class="notification-event">
      							You and <a href="#" class="h6 notification-friend">Mary Jane Stark</a> just became friends. Write on <a href="#" class="notification-link">his wall</a>.
      						</div>
      						<span class="notification-icon">
      							<svg class="olymp-happy-face-icon"><use xlink:href="/version3/icons/icons.svg#olymp-happy-face-icon"></use></svg>
      						</span>

      					</li>-->


      				</ul>

            </div>


            <div class="ui-block tab-pane fadeIn" id="stat-widget-settings">
              <div class="ui-block-title">
                <h6 class="title">Stat Widget</h6>
              </div>
              <div class="ui-block-content">
                <p>This is your official Altpocket widget, the widget updates periodically with your Altpocket stats.</p>

                <div class="row" style="text-align:center;">
                  <img src="https://altpocket.io/signature/{{Auth::user()->username}}" style="padding:15px;margin:0 auto;">
                  <br>
                  <p>Image URL</img>
                  <textarea style="width:100%" rows=1>https://altpocket.io/signature/{{Auth::user()->username}}</textarea>
                  <p>BB Code</p>
                  <textarea style="width:100%" rows=3>[align=center][url=http://altpocket.io/user/{{Auth::user()->username}}/][img]https://altpocket.io/signature/{{Auth::user()->username}}[/img][/url][/align]</textarea>
                  <p>HTML Code</p>
                  <textarea style="width:100%" rows=3><center><a href="http://altpocket.io/user/{{Auth::user()->username}}/"><img src="https://altpocket.io/signature/{{Auth::user()->username}}"/></a></center></textarea>
                </div>

              </div>
            </div>

            <div class="ui-block tab-pane fadeIn" id="remove-account">
              <div class="ui-block-title">
                <h6 class="title">Delete Account</h6>
              </div>
              <div class="ui-block-content">
                <p>Account deletion is currently under development, please contact an admin to have your account removed.</p>

                <form id="form-horizontal" role="form" method="post" action="/settings/delete">
                  {{ csrf_field() }}
                  <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
    								<div class="form-group label-floating">
    									<label class="control-label">Why do you want to delete your account?</label>
    									<textarea class="form-control" placeholder="Write what made you want to delete your account, a better competitor, missing function, safety/privacy issue.." name="why"></textarea>
    								<span class="material-input"></span></div>
    							</div>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group label-floating">
                      <label class="control-label">What can we improve on?</label>
                      <textarea class="form-control" placeholder="Missing exchanges, safety issue or anything else you see we need to improve on." name="improve"></textarea>
                    <span class="material-input"></span></div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group2 label-floating2 is-empty">
                      <label class="control-label2">Confirm Password</label>
                      <input class="form-control2" placeholder="" type="password" name="currentpwd">
                    </div>
                  </div>
    							</div>
                  <div class="row">
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="description-toggle">
                    <div class="description-toggle-content" style="color:#888da8">
                      <div class="h6">Keep updated</div>
                      <p>We save your email for future updates to keep you updated on what we improve and fix.</p>
                    </div>
                    <div class="togglebutton">
                      <label>
                        <input type="checkbox" name="updates" checked>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">


                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
                  <button class="btn btn-danger btn-md sfull-width" type="submit">Delete account<div class="ripple-container"></div></button>
                </div>

              </div>
                </form>

              </div>
            </div>


            <div class="ui-block tab-pane fadeIn" id="change-header">
              <div class="ui-block-title">
                <h6 class="title">Change Header</h6>
              </div>
              <div class="ui-block-content">
                <form id="form-horizontal" role="form" method="post" action="/settings/header" accept-charset="UTF-8" enctype="multipart/form-data">
                  <div class="row">
                    {{csrf_field()}}
                    <div class="slim" style="width:900px!important;height:400px!important;max-width:100%;">
                        <input type="file"/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
                      <button class="btn btn-primary btn-md sfull-width" type="submit">Save Header<div class="ripple-container"></div></button>
                    </div>

                  </div>

                </form>
              </div>
            </div>
            <div class="ui-block tab-pane fadeIn" id="change-avatar">
              <div class="ui-block-title">
                <h6 class="title">Change Avatar</h6>
              </div>
              <div class="ui-block-content">
                <form id="form-horizontal" role="form" method="post" action="/settings/avatar" accept-charset="UTF-8" enctype="multipart/form-data">
                  <div class="row">
                    {{csrf_field()}}
                    <div class="slim" data-size="300,300" data-force-size="300,300" data-crop="0,0,300,300">

                        <input type="file"/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
                      <button class="btn btn-primary btn-md sfull-width" type="submit">Save Avatar<div class="ripple-container"></div></button>
                    </div>

                  </div>

                </form>
              </div>
            </div>
            <div class="ui-block tab-pane fadeIn" id="change-password">
              <div class="ui-block-title">
                <h6 class="title">Change Password</h6>
              </div>
              <div class="ui-block-content">
                <form id="form-horizontal" role="form" method="post" action="/settings/password">
                  <div class="row">
                    {{csrf_field()}}
      							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      								<div class="form-group2 label-floating2 is-empty">
      									<label class="control-label2">Confirm Current Password</label>
      									<input class="form-control2" placeholder="" type="password" name="currentpwd">
      								</div>
      							</div>

      							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      								<div class="form-group2 label-floating2 is-empty">
      									<label class="control-label2">Your New Password</label>
      									<input class="form-control2" placeholder="" type="password" name="newpwd">
      								</div>
      							</div>
      							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      								<div class="form-group2 label-floating2 is-empty">
      									<label class="control-label2">Confirm New Password</label>
      									<input class="form-control2" placeholder="" type="password" name="cnewpwd">
      								</div>
      							</div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
      								<button class="btn btn-primary btn-md sfull-width" type="submit">Change Password<div class="ripple-container"></div></button>
      							</div>

      						</div>

                </form>
              </div>
            </div>

            <div class="ui-block tab-pane fadeIn active" id="profile-information">
              <div class="ui-block-title">
                <h6 class="title">Profile Information</h6>
              </div>
              <div class="ui-block-content">
                <form id="form-horizontal" role="form" method="post" action="/settings/information" >
                  <div class="row">
                    {{csrf_field()}}
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group2 label-floating2">
                            <label class="control-label2">Username</label>
                            <input class="form-control2" placeholder="" type="text" name="username" value="{{Auth::user()->username}}">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group2 label-floating2">
                            <label class="control-label2">Email</label>
                            <input class="form-control2" placeholder="" type="text" name="email" value="{{Auth::user()->email}}">
                          </div>
                        </div>
                    </div>
                    <!--
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group2 label-floating2">
                          <label class="control-label2">Display Name</label>
                          <input class="form-control2" placeholder="" type="text" name="displayname" value="{{Auth::user()->name}}">
                        </div>
                      </div>
                    </div>
                  -->
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group2 label-floating2">
                          <label class="control-label2">Biography</label>
                          <input class="form-control2" placeholder="" type="text" name="bio" value="{{Auth::user()->bio}}">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group2 with-icon label-floating2">
                      <label class="control-label2">Your Facebook Page ID</label>
                      <input class="form-control2" type="text" name="facebook" value="{{Auth::user()->facebook}}">
                      <i class="fa fa-facebook c-facebook" aria-hidden="true"></i>
                    </div>
                  </div>
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group2 with-icon label-floating2">
                      <label class="control-label2">Your Twitter Username</label>
                      <input class="form-control2" type="text" name="twitter" value="{{Auth::user()->twitter}}">
                      <i class="fa fa-twitter c-twitter" aria-hidden="true"></i>
                    </div>
                  </div>
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group2 with-icon label-floating2">
                      <label class="control-label2">Your Youtube URL</label>
                      <input class="form-control2" type="text" name="youtube" value="{{Auth::user()->youtube}}">
                      <i class="fa fa-youtube" style="color: #ff6161;" aria-hidden="true"></i>
                    </div>
                  </div>
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="description-toggle">
                    <div class="description-toggle-content" style="color:#888da8">
                      <div class="h6">Public Profile</div>
                      <p>Your profile will be public and viewable by anyone.</p>
                    </div>
                    <div class="togglebutton">
                      <label>
                        <input type="checkbox" name="public" @if(Auth::user()->public == "on") checked @endif>
                      </label>
                    </div>
                  </div>
                </div>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
    								<button class="btn btn-primary btn-md sfull-width">Save all Changes<div class="ripple-container"></div></button>
    							</div>
                </form>
              </div>
            </div>
          </div>

          <div class="ui-block tab-pane fadeIn" id="account-settings">
            <div class="ui-block-title">
              <h6 class="title">Account Settings</h6>
            </div>
            <div class="ui-block-content">
              <form id="form-horizontal" role="form" method="post" action="/settings/settings" >
                {{csrf_field()}}
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>Theme</label>
                    <select class="select form-control" name="theme" style="padding:15px;">
                          <option value="Day" @if(Auth::user()->theme == "normal") selected="selected" @endif>Day Theme</option>
                          <option value="Night" @if(Auth::user()->theme == "dark") selected="selected" @endif>Night Theme</option>
                        </select>
                  </div>

                  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>Display currency</label>
                    <select class="select form-control" name="currency" style="padding:15px;">
                          <option value="BTC" @if(Auth::user()->currency == "BTC") selected="selected" @endif>Bitcoins (BTC)</option>
                          <option value="USD" @if(Auth::user()->currency == "USD") selected="selected" @endif>US Dollars (USD)</option>
                          <option value="EUR" @if(Auth::user()->currency == "EUR") selected="selected" @endif>Euro (EUR)</option>
                          <option value="GBP" @if(Auth::user()->currency == "GBP") selected="selected" @endif>British Pound (GBP)</option>
                          <option value="SEK" @if(Auth::user()->currency == "SEK") selected="selected" @endif>Swedish Krona (SEK)</option>
                          <option value="NOK" @if(Auth::user()->currency == "NOK") selected="selected" @endif>Norwegian Kroner (NOK)</option>
                          <option value="DKK" @if(Auth::user()->currency == "DKK") selected="selected" @endif>Danish Krona (DKK)</option>
                          <option value="SGD" @if(Auth::user()->currency == "SGD") selected="selected" @endif>Singapore Dollar (SGD)</option>
                          <option value="CAD" @if(Auth::user()->currency == "CAD") selected="selected" @endif>Canadian Dollar (CAD)</option>
                          <option value="AUD" @if(Auth::user()->currency == "AUD") selected="selected" @endif>Australian Dollar (AUD)</option>
                          <option value="INR" @if(Auth::user()->currency == "INR") selected="selected" @endif>Indian Rupee (INR)</option>
                          <option value="MYR" @if(Auth::user()->currency == "MYR") selected="selected" @endif>Malaysian Ringgit (MYR)</option>
                          <option value="ZAR" @if(Auth::user()->currency == "ZAR") selected="selected" @endif>South African Rand (ZAR)</option>
                          <option value="HKD" @if(Auth::user()->currency == "HKD") selected="selected" @endif>Hong Kong Dollar (HKD)</option>
                        </select>
    							</div>
                  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <label>Price API</label>
                  <select class="select form-control" name="api" style="padding:15px;">
                        <option value="coinmarketcap" @if(Auth::user()->api == "coinmarketcap") selected="selected" @endif>CoinMarketCap.com (Default)</option>
                        <option value="worldcoinindex" @if(Auth::user()->api == "worldcoinindex") selected="selected" @endif>WorldCoinIndex.com</option>
                        <option value="bittrex" @if(Auth::user()->api == "bittrex") selected="selected" @endif>Bittrex.com</option>
                        <option value="poloniex" @if(Auth::user()->api == "poloniex") selected="selected" @endif>Poloniex.com</option>
                      </select>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="description-toggle">
    									<div class="description-toggle-content" style="color:#888da8">
    										<div class="h6">Email Notifications</div>
    										<p>You will receive notifications to your Email and on-site.</p>
    									</div>
    									<div class="togglebutton">
    										<label>
    											<input type="checkbox" name="email-notifications" @if(Auth::user()->email_notifications == "on") checked @endif>
    										</label>
    									</div>
    								</div>
                  </div>
                  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <div class="description-toggle">
                    <div class="description-toggle-content" style="color:#888da8">
                      <div class="h6">Condensed Investments</div>
                      <p>Same currencies will be summed together on investments view based on Exchanges.</p>
                    </div>
                    <div class="togglebutton">
                      <label>
                        <input type="checkbox" name="condensed-investments" @if(Auth::user()->summed == "1") checked @endif>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="description-toggle">
                  <div class="description-toggle-content" style="color:#888da8">
                    <div class="h6">Table Investment View</div>
                    <p>Your investments will be displayed in a table view instead of in blocks.</p>
                  </div>
                  <div class="togglebutton">
                    <label>
                      <input type="checkbox" name="table-view" @if(Auth::user()->tableview == "1") checked="" @endif>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="description-toggle">
                <div class="description-toggle-content" style="color:#888da8">
                  <div class="h6">Advanced Invested Algorithm</div>
                  <p>Your "Invested" will be calculated using your deposits and withdrawals from exchanges.</p>
                </div>
                <div class="togglebutton">
                  <label>
                    <input type="checkbox" name="algorithm" @if(Auth::user()->algorithm == "2") checked="" @endif>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="description-toggle">
              <div class="description-toggle-content" style="color:#888da8">
                <div class="h6">Selling Investments adds balance</div>
                <p>Enabling this will make it so when you sell an investment it will add the token received to your balance.</p>
              </div>
              <div class="togglebutton">
                <label>
                  <input type="checkbox" name="selltobalance" @if(Auth::user()->selltobalance == "1") checked="" @endif>
                </label>
              </div>
            </div>
          </div>
          <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="description-toggle">
            <div class="description-toggle-content" style="color:#888da8">
              <div class="h6">Selling Investments adds investment</div>
              <p>Enabling this will make it so when you sell an investment it will add the token received as a Investment.</p>
            </div>
            <div class="togglebutton">
              <label>
                <input type="checkbox" name="selltoinvestment" @if(Auth::user()->selltoinvestment == "1") checked="" @endif>
              </label>
            </div>
          </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="description-toggle">
          <div class="description-toggle-content" style="color:#888da8">
            <div class="h6">Add Investment removes balance</div>
            <p>Enabling this will make it so when you add an investment it will remove ETH/BTC from your balance if you have any.</p>
          </div>
          <div class="togglebutton">
            <label>
              <input type="checkbox" name="addfrombalance" @if(Auth::user()->addfrombalance == "1") checked="" @endif>
            </label>
          </div>
        </div>
      </div>
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="description-toggle">
        <div class="description-toggle-content" style="color:#888da8">
          <div class="h6">Old Investments View</div>
          <p>Enabling this will allow you to display the old investments page, keep in mind that this is only for viewing and not for handling investments.</p>
        </div>
        <div class="togglebutton">
          <label>
            <input type="checkbox" name="oldinvestments" @if(Auth::user()->oldinvestments == "1") checked="" @endif>
          </label>
        </div>
      </div>
    </div>


                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align:center;width:100%;">
                  <button class="btn btn-primary btn-md sfull-width">Save all Changes<div class="ripple-container"></div></button>
                </div>
            </div>
          </form>
          </div>
        </div>
      </div>
      	</div>
      </div>

      <!-- ... end Your Account Personal Information -->
    </div>



 @endsection

 @section('earlyjs')
   <script src="/version2/js/jquery-3.2.0.min.js"></script>
   <!-- Js effects for material design. + Tooltips -->
   <script src="/version2/js/material.min.js?v=1.3"></script>
   <!-- Helper scripts (Tabs, Equal height, Scrollbar, etc) -->
   <script src="/version2/js/theme-plugins.js?v=1.0"></script>
   <!-- Init functions -->
   <script src="/version2/js/main.js?v=225555"></script>

   <script src="/js/slim.kickstart.min.js" type="text/javascript"></script>


@endsection
