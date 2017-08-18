@extends('layouts.app')


<?php
use Jenssegers\Agent\Agent;

$agent = new Agent();


 ?>

@section('title')
Badges
@endsection

@section('css')
<link href="/css/blocks.css" rel="stylesheet">
<style>
.main-header {
    padding: 70px 0 190px 0;
    position: relative;
    background-position: 50% 50%;
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
.bg-badges:before {
    background-image: url(https://theme.crumina.net/html-olympus/img/top-header4.png);
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

</style>
@endsection


@section('content')
    <div id="content_wrapper" class="">
      <div class="main-header">
      	<div class="content-bg-wrap">
      		<div class="content-bg bg-badges"></div>
      	</div>
      	<div class="container">
      		<div class="row">
      			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      				<div class="main-header-content">
      					<h1>Collect your Badges!</h1>
      					<p>Welcome to your badge collection! Here you’ll find all the badges you can unlock to show on your
      						profile. Learn how to achive the goal to adquire them and collect them all. Some have leveled
      						tiers and other don’t. You can challenge your friends to see who gets more and let the fun begin!
      					</p>
      				</div>
      			</div>
      		</div>
      	</div>

      	<img class="img-bottom" src="https://theme.crumina.net/html-olympus/img/badges-bottom.png" alt="friends">
      </div>



        <div id="content" class="container-fluid">
          @foreach(DB::table('awards')->where([['name', '!=', 'Tiger']])->get() as $award)
            <div class="ui-block">
  				<div class="birthday-item inline-items badges">
  					<div class="author-thumb">
  						<img src="/awards/xl/{{$award->image}}" alt="author">
  					</div>
  					<div class="birthday-author-name">
  						<a href="#" class="h6 author-name">{{$award->name}}</a>
  						<div class="birthday-date">{{$award->description}}</div>


              @if($award->type == null)
                @if(Auth::user() && \App\awarded::where([['award_id', $award->id], ['userid', '=', Auth::user()->id]])->exists())
                  <div class="skills-item" style="float:left;">
        						<div class="skills-item-meter" style="width:120px;">
        							<span class="skills-item-meter-active skills-animate" style="width: 100%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="" data-original-title="1/1"></span>
        						</div>
        					</div>
                @else
                <div class="skills-item" style="float:left;">
      						<div class="skills-item-meter" style="width:120px;">
      							<span class="skills-item-meter-active skills-animate" style="width: 0%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="" data-original-title="Manually given."></span>
      						</div>
      					</div>
              @endif
            @elseif($award->type == "pageviews")
              <div class="skills-item" style="float:left;">
    						<div class="skills-item-meter" style="width:120px;">
    							<span class="skills-item-meter-active skills-animate" style="width: @if(Auth::user()){{min(100/$award->requirement * Auth::user()->visits, 100)}}@else 0 @endif%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="" data-original-title="@if(Auth::user()) {{Auth::user()->visits}}/{{$award->requirement}}@endif"></span>
    						</div>
    					</div>
            @elseif($award->type == "reffers")
              <div class="skills-item" style="float:left;">
    						<div class="skills-item-meter" style="width:120px;">
    							<span class="skills-item-meter-active skills-animate" style="width: @if(Auth::user()){{min(100/$award->requirement * DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->count(), 100)}}@else 0 @endif%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="" data-original-title="@if(Auth::user()) {{DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->count()}}/{{$award->requirement}}@endif"></span>
    						</div>
    					</div>
            @endif
  					</div>


            @if($agent->isMobile())
              <br><br>
                          @endif
            <a href="javascript:void(0)" data-target="#who_has_award_{{$award->id}}" data-toggle="modal" class="btn btn-sm bg-blue" style="margin-top:20px!important;@if($agent->isMobile())float:left!important;display:block!important;margin:0 auto!important;margin-left:125px!important;;text-align:center!important;@endif">Who has it?<div class="ripple-container"></div></a>
  				</div>
  			</div>


        <div class="modal fade" id="who_has_award_{{$award->id}}" tabindex="-1" role="dialog" aria-labelledby="who_has_award_1">
              <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">

        <h4 class="modal-title" id="myModalLabel-2">Who has the award: {{$award->name}}</h4>
        <ul class="card-actions icons right-top">
        <li>
          <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
            <i class="zmdi zmdi-close"></i>
          </a>
        </li>
        </ul>
        </div>
        <div class="modal-body">
          @foreach(\App\awarded::where('award_id', $award->id)->orderBy('created_at', 'desc')->get() as $awarded)
            <?php
              $user = DB::table('users')->where('id', $awarded->userid)->first();
             ?>
            <div class="ui-block" style="text-align:left;">
      				<div class="birthday-item inline-items">
      					<div class="author-thumb">
                    @if($user->avatar != "default.jpg")
                      <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
                    @else
                      <img src="/assets/img/default.png" alt="author">
                    @endif
      					</div>
      					<div class="birthday-author-name">
      						<a href="/user/{{$user->username}}" class="h6 author-name">{{$user->username}} </a>
      						<div class="birthday-date">{{$awarded->created_at->diffForHumans()}}</div>
				          <p style="font-size:12px;">"{{$awarded->reason}}"</p>
                </div>
      				</div>
        			</div>

          @endforeach
        </div>
        </div>
        <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
        </div>

      @endforeach
            <div class="content-body">
            </div>

            </div>
        </div>


</div>
@endsection
