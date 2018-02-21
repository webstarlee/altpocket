@extends('layouts.app2')

@section('title')
Dashboard
@endsection

<?php
    use Jenssegers\Agent\Agent;

    $agent = new Agent();

    $refcount = count(DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->select('id')->get());
?>

@section('css')
    <link href="/css/jquery.atwho.css?v=1.0" rel="stylesheet">
    <link href="/css/blocks.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
    <link rel="stylesheet" href="/css/farbtastic.css" media="screen">
    <link rel="stylesheet" href="/css/custom-wang.css" media="screen">
    <link rel="stylesheet" href="/js/magnific/magnific-popup.css" media="screen">
    <link rel="stylesheet" type="text/css" href="{{asset('js/light-gallery/css/lightgallery.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/slim.min.css')}}">

    <style>
        #currencies > span {
            padding-left:25px;
            padding-right:25px;
        }
        .positive {
            color:#73c04d;
        }
        .negative {
            color:#de6b6b;
        }
        @if(!$agent->isMobile())
            #currencies > li {
                display:inline;
                list-style-type:none;
                padding-left:25px;
                padding-right:25px;
            }
        @endif

        @media screen and (max-width: 992px){
            #currencies > li {
                display:block;
                list-style-type:none;
                padding-left:0px;
                padding-right:0px;
            }
        }
        .swiper-container {
            padding-bottom: 40px;
        }
        .swiper-container {
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            z-index: 3;
        }
        .swiper-pagination {
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0);
        }
        .swiper-pagination-bullet {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #fff;
            margin: auto 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: all .3s ease;
            border: 2px solid #d9dbe7;
        }
        .swiper-pagination-bullet.swiper-pagination-bullet-active {
            background-color: #ff5e3a;
            border-color: transparent;
        }
        .swiper-container {
            width: 40%;
            overflow:hidden;
            float:left;
        }
        .post-video {
            border: 1px solid #e6ecf5;
            border-radius: 3px;
            overflow: hidden;
            margin: 20px 0;
        }
        .video-thumb {
            position: relative;
            float: left;
        }
        .video-thumb img {
            width: 100%;
        }
        .play-video {
            width: 64px;
            height: 64px;
            line-height: 68px;
            background-color: rgba(255, 94, 58, 0.7);
            border: 4px solid #fff;
            border-radius: 100%;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 5;
            transition: all .3s ease;
            outline: none;
        }
        .play-video svg {
            fill: #fff;
            width: 18px;
            height: 22px;
            margin-right: -2px;
        }
        .post-video .video-content {
            padding: 0px 20px;
            overflow: hidden;
        }
        .post-video .video-content .title {
            display: block;
            font-weight:300;
        }
        .post-video .video-content p {
            margin: 10px 0;
            font-size: 13px;
        }
        .post-video .video-content .link-site {
            font-size: 10px;
            color: #9a9fbf;
        }
        .skills-item {
            margin-bottom: 20px;
        }
        .w-pool .skills-item-info {
            margin-bottom: 0;
        }
        .skills-item-info {
            margin-bottom: 16px;
            color: #515365;
            font-size: 12px;
        }
        .skills-item-info {
            margin-bottom: 16px;
            color: #515365;
            font-size: 12px;
        }
        .w-pool .radio {
            color: #515365;
        }
        .radio {
            margin-bottom: 1rem;
        }
        .w-pool .radio label {
            padding-left: 30px;
        }
        .radio label {
            cursor: pointer;
            padding-left: 35px;
            position: relative;
        }
        label {
            display: inline-block;
            margin-bottom: .5rem;
            cursor: pointer;
        }
        .skills-item-info .skills-item-count {
            float: right;
        }
        .w-pool .counter-friends {
            margin: 10px 0;
        }
        .radio input[type=radio]:checked~.check, label.radio-inline input[type=radio]:checked~.check {
            background-color: #42a5f5;
            transform: scale3d(0.45, 0.45, 1)!important;
        }
        .friends-harmonic .all-users {
            line-height: 26px;
            opacity: .8;
        }
        .all-users {
            line-height: 34px;
            text-align: center;
            color: #fff!important;
            background-color: #42a5f5;
            font-size: 11px;
        }

    </style>
@endsection

<?php
    if(Auth::user()){
        $multiplier = Auth::user()->getMultiplier();
        $api = Auth::user()->api;
        $currency = Auth::user()->getCurrency();
        if($currency != 'BTC' && $currency != 'USD' && $currency != 'CAD'){
            $fiat = DB::table('multipliers')->where('currency', $currency)->first()->price;
            $symbol2 = Auth::user()->getSymbol();
            $symbol = "";
        } else {
            $fiat = 1;
            $symbol = Auth::user()->getSymbol();
            $symbol2 = "";
        }
    } else {
        $symbol = "$";
        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
        $api = "coinmarketcap";
        $currency = "USD";
        $fiat = 1;
        $symbol2 = "";
    }

    $followers = Auth::user()->followings()->select('id')->get();
    $array = array();
    foreach($followers as $follower){
        array_push($array, $follower->id);
    }

    $groups = \App\IoGroupUser::where('user_id', Auth::user()->id)->select('group_id')->get();
    $own_groups = \App\IoGroup::where('user_id', Auth::user()->id)->select('id')->get();
    $group_users = array();
    foreach ($groups as $group) {
        $users = \App\IoGroupUser::where('group_id', $group->group_id)->select('user_id')->get();
        foreach ($users as $user) {
            if (!in_array($user->user_id, $group_users) && $user->user_id != Auth::user()->id) {
                array_push($group_users, $user->user_id);
            }
        }

        $group_admin = \App\IoGroup::find($group->group_id);
        if (!in_array($group_admin->user_id, $group_users)) {
            array_push($group_users, $group_admin->user_id);
        }
    }

    foreach ($own_groups as $own_group) {
        $users = \App\IoGroupUser::where('group_id', $own_group->id)->select('user_id')->get();
        foreach ($users as $user) {
            if (!in_array($user->user_id, $group_users)) {
                array_push($group_users, $user->user_id);
            }
        }
    }

    foreach ($group_users as $group_user) {
        if (!in_array($group_user, $array)) {
            array_push($array, $group_user);
        }
    }
?>
@section('content')
    <div id="content_wrapper" class="">
        <input type="hidden" id="loading-gif-img-for-all" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
        <div id="header_wrapper" class="header-sm">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <header id="header">
                            <h1>Dashboard</h1>
                        </header>
                    </div>
                </div>
            </div>
        </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="dashboard_content" class="tab-content">
                    <div class="tab-pane fade active in" id="social">
                        <div class="col-md-3">
                            <div class="card type--profile">
                                <header class="card-heading card-background" id="card_img_02">
                                    @if(Auth::user()->avatar == "default.jpg")
                                        <img src="/assets/img/default.png" alt="" class="img-circle">
                                    @else
                                        <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle">
                                    @endif
                                    <ul class="card-actions icons  right-top">
                                        <li class="dropdown">
                                        </li>
                                    </ul>
                                </header>
                                <div class="card-body">
                                    <h3 class="name" style="color:{{Auth::user()->groupColor()}}">@if(Auth::user()->isStaff() || Auth::user()->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@elseif(Auth::user()->isDonator())<img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">@endif{{Auth::user()->username}}</h3>
                                    <span class="title">{{Auth::user()->bio}}</span>
                                    <a href="/user/{{Auth::user()->username}}" class="btn btn-primary">Profile</a>
                                </div>
                                <footer class="card-footer border-top">
                                    <div class="row row p-t-10 p-b-10">
                                        <div class="col-xs-4"><span class="count">{!! $symbol !!}{{number_format(Auth::user()->getInvested(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Invested</span></div>
                                        <div class="col-xs-4"><span class="count">{{Auth::user()->impressed}}</span><span>Impressions</span></div>
                                        @if(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) > 0)
                                            <div class="col-xs-4"><span class="count" style="color:#73c04d";>{!! $symbol !!}{{number_format(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Profit</span></div>
                                        @else
                                            <div class="col-xs-4"><span class="count" style="color:#de6b6b";>{!! $symbol !!}{{number_format(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Profit</span></div>
                                        @endif
                                    </div>
                                </footer>
                            </div>
                            <div class="ui-block">
                                <div class="ui-block-title">
                                    <div class="h6 title">Badges</div>
                                </div>
                                @if($refcount < 5)
                                    <div class="birthday-item inline-items badges">
                                        <div class="author-thumb">
                                            <img src="/awards/xl/heart2.png" alt="author" data-toggle="tooltip" title="This user has referred 5 users to Altpocket." style="cursor:pointer;">
                                        </div>
                                        <div class="skills-item">
                                            <div class="skills-item-meter">
                                                <span class="skills-item-meter-active skills-animate" style="width: {{20 * $refcount}}%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="{{$refcount}}/5 referred"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="birthday-item inline-items badges">
                                    <div class="author-thumb">
                                        <img src="/awards/xl/bronze-medal.png" alt="author" data-toggle="tooltip" title="This user has had over 200 unique visitors on their profile." style="cursor:pointer;">
                                    </div>
                                    <div class="skills-item">
                                        <div class="skills-item-meter">
                                            <span class="skills-item-meter-active skills-animate" style="width: {{min(0.5 * Auth::user()->visits, 100)}}%; opacity: 1;cursor:pointer;" data-toggle="tooltip" title="{{Auth::user()->visits}}/200 unique visitors"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ui-block">
                                <div class="ui-block-title">
                                    <div class="h6 title">Investment Timeline</div>
                                </div>
                                @foreach($unions as $investment)
                                    <?php
                                        $crypto = DB::table('cryptos')->where('symbol', $investment->currency)->select('name')->first();

                                        if($crypto === null)
                                        {
                                            $crypto = DB::table('world_coins')->where('symbol', $investment->currency)->select('name')->first();
                                        }

                                        if($crypto !== null)
                                        {
                                            $name = $crypto->name;
                                        } else {
                                            $name = "N/A";
                                        }
                                    ?>

                                    <div class="birthday-item inline-items badges">
                                        <div class="author-thumb">
                                        <img src="/icons/32x32/{{$investment->currency}}.png" alt="author">
                                        </div>
                                        <div class="birthday-author-name">
                                            <a href="/investments" class="h6 author-name">{{$name}}</a>
                                            @if($investment->date_bought)
                                                <div class="birthday-date">{{$investment->date_bought->diffForHumans()}}</div>
                                            @endif
                                        </div>
                                        <div class="investment-details" style="float:right;">
                                            <ul class="statistics-list-count">
                                                <li>
                                                    <div class="points">
                                                        <span style="float:right;">
                                                        Bought<br>
                                                        </span>
                                                    </div>
                                                    <div class="count-stat" style="font-size:16px;display:inline-block;">{{number_format($investment->amount, 2)}} {{$investment->currency}}</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card">
                                <header class="card-heading" style="padding-top:3px!important;padding-bottom:3px!important;">
                                    <h2>Make a status update</h2>
                                </header>
                                <div class="card-body" style="padding-top:0px!important;">
                                    <form action="/status/post" id="user-status-post-form" method="POST">
                                        {{csrf_field()}}
                                        <hr>
                                        <div class="author-thumb" style="position:absolute;top:100px;left:25px;">
                                            @if(Auth::user()->avatar != "default.jpg")
                                                <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author" style="border-radius:100%;width:36px;">
                                            @else
                                                <img src="/assets/img/default.png" alt="author" style="border-radius:100%;width:36px;">
                                            @endif
                                        </div>
                                        <div class="form-group label-floating is-empty" style="background-image:none!important;">
                                            <div class="status-post-stuff-container">
                                                <textarea id="new-post" class="form-control" style="min-height:100px;padding-left:70px;background-image:none!important;" placeholder="Share what you are thinking with Altpocket's community." name="status"></textarea>
                                                <div class="form-group post-media-container" id="status-post-media-container"></div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-footer" style="padding-top:0px!important;padding-bottom:0px!important;">
                                            <div class="media-upload-btn-container">
                                                <a href="javascript:void(0)" id="add-youtubeurl"><i class="zmdi zmdi-youtube" style="font-size:27px;"></i></a>
                                                <a href="javascript:void(0)" id="add-image"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                                                <a href="{{url('status/get-giphy')}}" id="add-image-gif"><i class="zmdi zmdi-gif" style="font-size:35px;position:absolute;"></i></a>
                                            </div>
                                            <button type="submit" id="post-status-submit-btn" class="btn btn-blue" style="float:right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Posting">Post</button>
                                            <a class="btn btn-primary" style="float:right;margin-right:15px;" data-toggle="modal" href="#poll_modal">Create Poll</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabpanel">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="active" role="presentation">
                                                <a href="#browse_all" data-toggle="tab" aria-expanded="true">Browse<div class="ripple-container"></div></a>
                                            </li>
                                            <li role="presentation">
                                                <a id="view-personal" href="#personal_feed" data-toggle="tab" aria-expanded="true">Personal Feed</a>
                                            </li>
                                            @if(Auth::user()->isStaff() || Auth::user()->isFounder())
                                                <li role="presentation"><a href="#moderate" data-toggle="tab" aria-expanded="true">Moderate</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fadeIn" id="personal_feed">
                                    <div id="post-data-following"></div>
                                    <div class="ajax-load text-center" style="display:none">
                                        <div class="preloader pl-lg">
                                            <svg class="pl-circular" viewBox="25 25 50 50">
                                                <circle class="plc-path" cx="50" cy="50" r="20"></circle>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                @if(Auth::user()->isStaff() || Auth::user()->isFounder())
                                    <div class="tab-pane fadeIn" id="moderate">
                                        <div id="post-data-moderate">
                                            @foreach(\App\Status::where('moderate', 'yes')->get() as $status)
                                                <?php
                                                    $user = \App\User::where('id', $status->userid)->first();
                                                    $comments = \App\StatusComment::where('statusid', $status->id)->get();
                                                    $text = $status->status;
                                                    $code = array('<', '>');
                                                    $text = str_replace($code, '', $text);
                                                    preg_match_all("/\::[^::]*\::/", $text, $matches);

                                                    foreach($matches[0] as $key => $match){
                                                        $brackets = array('::');
                                                        $coin = str_replace($brackets, '', $match);
                                                        $coin = strtoupper($coin);
                                                        if(DB::table('cryptos')->where('symbol', $coin)->exists())
                                                        {
                                                            $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                                                        } else {
                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                                                        }
                                                    }

                                                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                                                    $text = str_replace('[img]', '<div class="post-thumb"><img src="', $text);
                                                    $text = str_replace('[/img]', '"></div>', $text);
                                                ?>
                                                <div class="ui-block">
                                                    <article class="altpocket post">
                                                        <div class="post__author author vcard inline-items">
                                                            @if($user->avatar == "default.jpg")
                                                                <img src="/assets/img/default.png" alt="author">
                                                            @else
                                                                <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
                                                            @endif
                                                            <div class="author-date">
                                                                <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->isDonator())<img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">@endif @if($user->isStaff() || $user->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif <i style="color:#F44336" data-toggle="tooltip" title="Please accept or delete this post." class="fa fa-exclamation-triangle"></i> {{$user->username}}</a>
                                                                <div class="post__date">
                                                                    <time class="published" datetime="{{$status->created_at}}">
                                                                        {{$status->created_at->diffForHumans()}}
                                                                    </time>
                                                                </div>
                                                            </div>
                                                            @if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                                                                <ul class="card-actions icons right-top">
                                                                    <li class="dropdown">
                                                                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                                                                            <i class="zmdi zmdi-more"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-right btn-primary">
                                                                            <li>
                                                                                <a href="/status/{{$status->id}}/accept">Accept Post</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#" data-toggle="modal" data-target="#edit_post" class="edit-post" id="{{$status->id}}">Edit Post</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="/status/{{$status->id}}/delete">Delete Post</a>
                                                                            </li>
                                                                        </ul>
                                                                    </li>
                                                                </ul>
                                                            @endif
                                                        </div>
                                                        <p style="text-align:center;font-weight:700;color:#F44336;">This post was made by a new user and is only shown to staff.</p>
                                                        <hr>
                                                        <p>{!! nl2br($text) !!}</p>
                                                        <div class="post-additional-info inline-items">
                                                            <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLiked($status)) liked @endif" status="{{$status->id}}">
                                                                <i class="fa fa-heart"></i>
                                                                <span>{{count($status->likers()->get())}}</span>
                                                            </a>
                                                            @if(count($status->likers()->get()) >= 1)
                                                                <?php
                                                                    $likers = $status->likers()->get()->take(5);
                                                                    $like1 = $status->likers()->first();
                                                                    $count = count($status->likers()->get());
                                                                ?>
                                                                <ul class="friends-harmonic">
                                                                    @foreach($likers as $like)
                                                                        <li>
                                                                            <a href="/user/{{$like->username}}">
                                                                            @if($like->avatar != "default.jpg")
                                                                                <img src="/uploads/avatars/{{$like->id}}/{{$like->avatar}}" alt="friend">
                                                                            @else
                                                                                <img src="/assets/img/default.png" alt="friend">
                                                                            @endif
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                                <div class="names-people-likes">
                                                                    @if(Auth::user()->hasLiked($status))
                                                                        <a href="#">You</a>
                                                                    @else
                                                                        <a href="/user/{{$like1->username}}">{{$like1->username}}</a>
                                                                    @endif and
                                                                    <br>{{$count - 1}} more liked this
                                                                </div>
                                                            @endif

                                                            <div class="comments-shared">
                                                                <a href="?status={{$status->id}}"class="post-add-icon inline-items">
                                                                    <i class="zmdi zmdi-comments"></i>
                                                                    <span>{{count($comments)}}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </article>
                                                    <ul class="comments-list">
                                                        <ul class="comments-list">
                                                            <?php
                                                                $count = 0;
                                                            ?>
                                                            @foreach($comments as $comment)
                                                                <?php
                                                                    $count += 1;

                                                                    $u = \App\User::where('id', $comment->userid)->first();
                                                                    $text = $comment->comment;
                                                                    $code = array('<', '>');
                                                                    $text = str_replace($code, '', $text);

                                                                    preg_match_all("/\::[^::]*\::/", $text, $matches);

                                                                    foreach($matches[0] as $key => $match)
                                                                    {
                                                                        $brackets = array('::');
                                                                        $coin = str_replace($brackets, '', $match);
                                                                        $coin = strtoupper($coin);

                                                                        if(DB::table('cryptos')->where('symbol', $coin)->exists())
                                                                        {
                                                                            $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                                                                        } else {
                                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                                                                        }
                                                                    }

                                                                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                                                                ?>
                                                                <li class="li-comment status{{$status->id}}" @if($count > 5) style="display:none;" @endif>
                                                                    <div class="post__author author vcard inline-items">
                                                                        @if($u->avatar != "default.jpg")
                                                                            <img src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="author">
                                                                        @else
                                                                            <img src="/assets/img/default.png" alt="author">
                                                                        @endif

                                                                        <div class="author-date">
                                                                            <a class="h6 post__author-name fn" style="color:{{$u->groupColor()}}!important" href="/user/{{$u->username}}">@if($u->isStaff() || $u->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$u->username}}</a>
                                                                            <div class="post__date">
                                                                                <time class="published" datetime="{{$comment->created_at}}">
                                                                                    {{$comment->created_at->diffForHumans()}}
                                                                                </time>
                                                                            </div>
                                                                        </div>

                                                                        @if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                                                                            <ul class="card-actions icons right-top">
                                                                                <li class="dropdown">
                                                                                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="zmdi zmdi-more"></i>
                                                                                    </a>
                                                                                    <ul class="dropdown-menu dropdown-menu-right btn-primary">
                                                                                        <li>
                                                                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_comment" class="edit-comment" id="{{$comment->id}}">Edit Comment</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="/statuscomment/{{$comment->id}}/delete">Delete Comment</a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </li>
                                                                            </ul>
                                                                        @endif
                                                                    </div>

                                                                    <p>{!!nl2br($text)!!}</p>

                                                                    <a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment @if(Auth::user()->hasLiked($comment)) liked @endif" status="{{$comment->id}}">
                                                                    <i class="fa fa-heart"></i>
                                                                    <span>{{count($comment->likers()->get())}}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @if(count($comments) > 5)
                                                            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
                                                        @endif
                                                        <form class="comment-form inline-items" action="/comment/post/{{$status->id}}" method="POST">
                                                            {{csrf_field()}}
                                                            <div class="post__author author vcard inline-items">
                                                                @if(Auth::user()->avatar != "default.jpg")
                                                                    <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
                                                                @else
                                                                    <img src="/assets/img/default.png " alt="author">
                                                                @endif
                                                            </div>

                                                            <div class="form-group with-icon-right is-empty">
                                                                <textarea class="form-control form-control-2" placeholder="" style="background-image:none!important;" name="comment"></textarea>

                                                                <span class="material-input"></span><span class="material-input"></span><span class="material-input"></span>
                                                            </div>
                                                            <button type="submit" class="btn btn-blue" style="float:right;">Post<div class="ripple-container"></div></button>
                                                        </form>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="ajax-load text-center" style="display:none">
                                            <div class="preloader pl-lg">
                                                <svg class="pl-circular" viewBox="25 25 50 50">
                                                    <circle class="plc-path" cx="50" cy="50" r="20"></circle>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="tab-pane fadeIn active" id="browse_all">
                                    <div class="row" id="profile-timeline">
                                        <div class="col-xs-12 col-sm-12">
                                            @foreach(\App\Status::where([['moderate', '=', 'yes'], ['userid', '=', Auth::user()->id]])->orderBy('created_at', 'desc')->get() as $status)
                                                <?php
                                                    $user = \App\User::where('id', $status->userid)->first();
                                                    $comments = \App\StatusComment::where('statusid', $status->id)->get();
                                                    $text = $status->status;
                                                    $code = array('<', '>');
                                                    $text = str_replace($code, '', $text);
                                                    preg_match_all("/\::[^::]*\::/", $text, $matches);

                                                    foreach($matches[0] as $key => $match)
                                                    {
                                                        $brackets = array('::');
                                                        $coin = str_replace($brackets, '', $match);
                                                        $coin = strtoupper($coin);
                                                        if(DB::table('cryptos')->where('symbol', $coin)->exists())
                                                        {
                                                            $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                                                        } else {
                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                                                        }
                                                    }

                                                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                                                    $text = str_replace('[img]', '<div class="post-thumb"><img src="', $text);
                                                    $text = str_replace('[/img]', '"></div>', $text);
                                                ?>
                                                <div class="ui-block">
                                                    <article class="altpocket post">
                                                        <div class="post__author author vcard inline-items">
                                                            @if($user->avatar == "default.jpg")
                                                                <img src="/assets/img/default.png" alt="author">
                                                            @else
                                                                <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
                                                            @endif
                                                            <div class="author-date">
                                                                <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->isStaff() || $user->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$user->username}}</a>
                                                                <div class="post__date">
                                                                    <time class="published" datetime="{{$status->created_at}}">
                                                                        {{$status->created_at->diffForHumans()}}
                                                                    </time>
                                                                </div>
                                                            </div>
                                                            @if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                                                                <ul class="card-actions icons right-top">
                                                                    <li class="dropdown">
                                                                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                                                                            <i class="zmdi zmdi-more"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-right btn-primary">
                                                                            <li>
                                                                                <a href="#" data-toggle="modal" data-target="#edit_post" class="edit-post" id="{{$status->id}}">Edit Post</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="/status/{{$status->id}}/delete">Delete Post</a>
                                                                            </li>
                                                                        </ul>
                                                                    </li>
                                                                </ul>
                                                            @endif
                                                        </div>
                                                        <p>{!! nl2br($text) !!}</p>
                                                        <div class="post-additional-info inline-items">

                                                            <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLiked($status)) liked @endif" status="{{$status->id}}">
                                                                <i class="fa fa-heart"></i>
                                                                <span>{{count($status->likers()->get())}}</span>
                                                            </a>
                                                            @if(count($status->likers()->get()) >= 1)
                                                                <?php
                                                                    $likers = $status->likers()->get()->take(5);
                                                                    $like1 = $status->likers()->first();
                                                                    $count = count($status->likers()->get());
                                                                ?>
                                                                <ul class="friends-harmonic">
                                                                    @foreach($likers as $like)
                                                                        <li>
                                                                            <a href="/user/{{$like->username}}">
                                                                                @if($like->avatar != "default.jpg")
                                                                                    <img src="/uploads/avatars/{{$like->id}}/{{$like->avatar}}" alt="friend">
                                                                                @else
                                                                                    <img src="/assets/img/default.png" alt="friend">
                                                                                @endif
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>

                                                                <div class="names-people-likes">
                                                                    @if(Auth::user()->hasLiked($status))<a href="#">You</a> @else <a href="/user/{{$like1->username}}">{{$like1->username}}</a> @endif and
                                                                    <br>{{$count - 1}} more liked this
                                                                </div>
                                                            @endif

                                                            <div class="comments-shared">
                                                                <a href="?status={{$status->id}}"class="post-add-icon inline-items">
                                                                    <i class="zmdi zmdi-comments"></i>
                                                                    <span>{{count($comments)}}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </article>
                                                    <ul class="comments-list">
                                                        <ul class="comments-list">
                                                            <?php
                                                                $count = 0;
                                                            ?>
                                                            @foreach($comments as $comment)
                                                                <?php
                                                                    $count += 1;

                                                                    $u = \App\User::where('id', $comment->userid)->first();
                                                                    $text = $comment->comment;
                                                                    $code = array('<', '>');
                                                                    $text = str_replace($code, '', $text);
                                                                    preg_match_all("/\::[^::]*\::/", $text, $matches);

                                                                    foreach($matches[0] as $key => $match)
                                                                    {
                                                                        $brackets = array('::');
                                                                        $coin = str_replace($brackets, '', $match);
                                                                        $coin = strtoupper($coin);

                                                                        if(DB::table('cryptos')->where('symbol', $coin)->exists())
                                                                        {
                                                                            $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                                                                        } else {
                                                                            $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                                                                        }
                                                                    }

                                                                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                                                                ?>
                                                                <li class="li-comment status{{$status->id}}" @if($count > 5) style="display:none;" @endif>
                                                                    <div class="post__author author vcard inline-items">
                                                                        @if($u->avatar != "default.jpg")
                                                                            <img src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="author">
                                                                        @else
                                                                            <img src="/assets/img/default.png" alt="author">
                                                                        @endif

                                                                        <div class="author-date">
                                                                            <a class="h6 post__author-name fn" style="color:{{$u->groupColor()}}!important" href="/user/{{$u->username}}">@if($u->isStaff() || $u->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$u->username}}</a>
                                                                            <div class="post__date">
                                                                                <time class="published" datetime="{{$comment->created_at}}">
                                                                                    {{$comment->created_at->diffForHumans()}}
                                                                                </time>
                                                                            </div>
                                                                        </div>

                                                                        @if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                                                                            <ul class="card-actions icons right-top">
                                                                                <li class="dropdown">
                                                                                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                                                                                        <i class="zmdi zmdi-more"></i>
                                                                                    </a>
                                                                                    <ul class="dropdown-menu dropdown-menu-right btn-primary">
                                                                                        <li>
                                                                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_comment" class="edit-comment" id="{{$comment->id}}">Edit Comment</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="/statuscomment/{{$comment->id}}/delete">Delete Comment</a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </li>
                                                                            </ul>
                                                                        @endif
                                                                    </div>

                                                                    <p>{!!nl2br($text)!!}</p>

                                                                    <a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment @if(Auth::user()->hasLiked($comment)) liked @endif" status="{{$comment->id}}">
                                                                        <i class="fa fa-heart"></i>
                                                                        <span>{{count($comment->likers()->get())}}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @if(count($comments) > 5)
                                                            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
                                                        @endif
                                                        <form class="comment-form inline-items" action="/comment/post/{{$status->id}}" method="POST">
                                                            {{csrf_field()}}
                                                            <div class="post__author author vcard inline-items">
                                                                @if(Auth::user()->avatar != "default.jpg")
                                                                    <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
                                                                @else
                                                                    <img src="/assets/img/default.png " alt="author">
                                                                @endif
                                                            </div>
                                                            <div class="form-group with-icon-right is-empty">
                                                                <textarea class="form-control form-control-2" placeholder="" style="background-image:none!important;" name="comment"></textarea>
                                                                <span class="material-input"></span><span class="material-input"></span><span class="material-input"></span>
                                                            </div>
                                                            <button type="submit" class="btn btn-blue" style="float:right;">Post<div class="ripple-container"></div></button>
                                                        </form>
                                                    </ul>
                                                </div>
                                            @endforeach

                                            <div id="post-data">
                                                @include('module.posts', array('type'=>'all'))
                                            </div>

                                            <div class="ajax-load text-center" style="display:none">
                                                <div class="preloader pl-lg">
                                                    <svg class="pl-circular" viewBox="25 25 50 50">
                                                        <circle class="plc-path" cx="50" cy="50" r="20"></circle>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            $trackingcount = 0;
                        ?>
                        @foreach($trackings as $tracking)
                            <?php
                                $crypto = DB::table('cryptos')->where('symbol', $tracking->coin)->select('name', 'price_usd', 'percent_change_24h')->first();
                                $trackingcount += 1;
                            ?>

                            <div class="col-xl-12 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <div class="ui-block" style="text-align:left;">
                                    <div class="birthday-item inline-items">
                                        <ul class="card-actions icons right-top" style="right:20px;top:0px;">
                                            <li class="dropdown">
                                                <a href="#" class="change-tracking" id="{{$tracking->id}}" >
                                                    <i class="fa fa-cogs" style="font-size:12px;"></i>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="author-thumb" style="width:32px;height:32px;">
                                            <img src="/icons/32x32/{{$tracking->coin}}.png" alt="author">
                                        </div>
                                        <ul class="statistics-list-count">
                                            <li style="margin-bottom:0px;">
                                                <div class="points">
                                                    <span>
                                                        {{$crypto->name}}
                                                    </span>
                                                </div>
                                                <div class="count-stat" style="font-size:15px;">@if($crypto->price_usd > 100) {{number_format($crypto->price_usd, 1) }}$ @else {{number_format($crypto->price_usd, 4) }}$  @endif
                                                    <span class="indicator @if($crypto->percent_change_24h < 0) negative @else positive @endif"> {{number_format($crypto->percent_change_24h, 1)}}%</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($trackingcount < 4)
                            <?php
                                $ii = $trackingcount;
                            ?>
                            @for($i = 0; $i < 4 - $ii; $i++)
                                <?php
                                $crypto = DB::table('cryptos')->orderByRaw('RAND()')->first();
                                $trackingcount += 1;

                                ?>

                                <div class="col-xl-12 col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <div class="ui-block" style="text-align:left;">
                                        <div class="birthday-item inline-items">
                                            <ul class="card-actions icons right-top" style="right:20px;top:0px;">
                                                <li class="dropdown">
                                                    <a href="javascript:void(0)" class="change-tracking" id="{{$trackingcount}}">
                                                        <i class="fa fa-cogs" style="font-size:12px;"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="author-thumb" style="width:32px;height:32px;">
                                                <img src="/icons/32x32/{{$crypto->symbol}}.png" alt="author">
                                            </div>
                                            <ul class="statistics-list-count">
                                                <li style="margin-bottom:0px;">
                                                    <div class="points">
                                                        <span>
                                                            {{$crypto->name}}
                                                        </span>
                                                    </div>
                                                    <div class="count-stat" style="font-size:15px;">@if($crypto->price_usd > 100) {{number_format($crypto->price_usd, 1) }}$ @else {{number_format($crypto->price_usd, 4) }}$  @endif
                                                        <span class="indicator @if($crypto->percent_change_24h < 0) negative @else positive @endif"> {{number_format($crypto->percent_change_24h, 1)}}%</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        @endif

                        <div class="col-xl-12 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            @if(count($balances2) >= 1)
                                <div class="ui-block" data-mh="pie-chart">
                                    <div class="ui-block-title">
                                        <div class="h6 title">Your holdings</div>
                                    </div>
                                    <div class="ui-block-content">
                                        <div class="chart-with-statistic">
                                            <!-- Slider main container -->
                                            <div class="swiper-container">
                                                <!-- Additional required wrapper -->
                                                <div class="swiper-wrapper">
                                                    <!-- Slides -->
                                                    <?php $count = 1;
                                                        $total = 0;
                                                        $totalpercentchange = 0;
                                                    ?>
                                                    @foreach($balances2 as $balance)
                                                        <?php
                                                            $crypto = DB::table('cryptos')->where('symbol', $balance->currency)->select('price_btc')->first();

                                                            if($crypto === null)
                                                            {
                                                                $crypto = DB::table('world_coins')->where('symbol', $balance->currency)->select('price_btc')->first();
                                                            }
                                                            $total += $balance->amount * $crypto->price_btc;
                                                        ?>
                                                    @endforeach

                                                    @foreach($balances2 as $balance)
                                                        <?php
                                                            $crypto = DB::table('cryptos')->where('symbol', $balance->currency)->select('price_btc', 'percent_change_24h', 'name')->first();

                                                            if($crypto === null)
                                                            {
                                                                $crypto = DB::table('world_coins')->where('symbol', $balance->currency)->first();
                                                                $crypto->percent_change_24h = 0;
                                                            }

                                                            $percentchange = (($balance->amount * $crypto->price_btc) / $total) * $crypto->percent_change_24h;
                                                            $totalpercentchange += $percentchange;
                                                        ?>
                                                        @if($count%4 == 1)
                                                            <div class="swiper-slide">
                                                            <ul class="statistics-list-count" style="width:100%!important;">
                                                        @endif
                                                                <li>
                                                                    <div class="points">
                                                                        <span>
                                                                            <span id="{{$balance->currency}}" class="statistics-point point-color select-color" data-toggle="modal" data-target="#change_color" style="background-color:@if($balance->color == null){{sprintf('#%06X', mt_rand(0, 0xFFFFFF))}}@else{{$balance->color}}@endif;cursor:pointer;"></span>
                                                                            {{$crypto->name}} (<span @if($crypto->percent_change_24h > 0) style="color:#4caf50" @else style="color:#ff5722"@endif >{{$crypto->percent_change_24h}}%</span>)
                                                                        </span>
                                                                    </div>
                                                                    <div class="count-stat">{{number_format($balance->amount)}} {{$balance->currency}}</div>
                                                                    <div class="count-stat-minor">{{number_format($balance->amount * $crypto->price_btc, 4)}} BTC</div>
                                                                </li>
                                                        @if($count%4 == 0)
                                                            </ul>
                                                            </div>
                                                        @endif

                                                        <?php $count++; ?>
                                                    @endforeach
                                                @if($count%4 != 1)
                                                </ul>
                                                </div>
                                                @endif
                                            </div>
                                            <!-- If we need pagination -->
                                            <div class="swiper-pagination"></div>

                                            <!-- If we need navigation buttons -->
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
                                        </div>
                                        <div class="chart-js chart-js-pie-color">
                                            <canvas id="pie-color-chart2" width="180" height="180"></canvas>
                                            <div class="general-statistics">{{number_format($totalpercentchange,2)}}%
                                                <span>Portfolio Change Last 24H</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="ui-block">
                            <div class="ui-block-title">
                                <div class="h6 title"> Referrals </div>
                                <a href="javascript:void(0)" class="more more-referral"><i class="fa fa-plus referral-option"></i></a>
                            </div>
                            <div class="ui-block-content referral-tab hide" style="text-align:center;display:none;">
                                <p>Your referral URL</p>
                                @if(Auth::user()->affiliate_id)
                                    <div class="form-group">
                                        <input readonly="readonly" type="text" class="form-control" id="name" placeholder="Name" value="{{url('/').'/?ref='.Auth::user()->affiliate_id}}" style="text-align:center;width:50%;margin:0 auto;margin-top:-25px;">
                                    </div>
                                    <?php
                                        $refz = \App\User::where('referred_by', Auth::user()->affiliate_id)->orderby('id', 'DESC')->select('id', 'username', 'created_at', 'avatar')->get();
                                        $count = count($refz);
                                    ?>
                                @else
                                    @php
                                    $user = Auth::user();
                                    $user->affiliate_id = str_random(10);
                                    $user->save();
                                    @endphp
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="ui-block">
                                            <div class="ui-block-content">
                                                <ul class="statistics-list-count">
                                                    <li style="margin-bottom:0px;">
                                                        <div class="points">
                                                            <span>
                                                                Total Referrals
                                                            </span>
                                                        </div>
                                                        <div class="count-stat">@if(Auth::user()->affiliate_id){{ $count }} @else 0 @endif
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="ui-block">
                                            <div class="ui-block-content">
                                                <ul class="statistics-list-count">
                                                    <li style="margin-bottom:0px;">
                                                        <div class="points">
                                                            <span>Last Referral</span>
                                                        </div>
                                                        <div class="count-stat">@if(Auth::user()->affiliate_id && $count >= 1)<a href="/user/{{ DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->orderby('id', 'DESC')->first()->username}}">{{ DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->orderby('id', 'DESC')->first()->username}}  </a> @else 0 @endif
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="h6 title">Last 5 Referrals</div>

                                @foreach($refz->take(5) as $ref)
                                    <div class="ui-block" style="text-align:left;">
                                        <div class="birthday-item inline-items">
                                            <div class="author-thumb">
                                                @if($ref->avatar != "default.jpg")
                                                    <img src="/uploads/avatars/{{$ref->id}}/{{$ref->avatar}}" alt="author">
                                                @else
                                                    <img src="/assets/img/default.png" alt="author">
                                                @endif
                                            </div>
                                            <div class="birthday-author-name">
                                                <a href="/user/{{$ref->username}}" class="h6 author-name">{{$ref->username}} </a>
                                                <div class="birthday-date">{{$ref->created_at->diffForHumans()}}</div>
                                            </div>
                                            <a href="/user/{{$ref->username}}" class="btn btn-sm bg-blue">Go to profile</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="ui-block">
                        <div class="ui-block-title">
                            <div class="h6 title"> Last 5 Donations </div>
                        </div>
                        <div class="ui-block-content" style="text-align:center;">
                            <p>Thank you all for supporting Altpocket!</p>
                            <?php
                                $donations = \App\Donation::orderBy('created_at', 'desc')->where('status', '100')->get()->take(5);
                            ?>
                            @foreach($donations as $donation)
                                <?php
                                    $don = \App\User::where('id', $donation->userid)->select('avatar', 'id', 'username')->first();
                                ?>
                                <div class="ui-block" style="text-align:left;">
                                    <div class="birthday-item inline-items">
                                        <div class="author-thumb">
                                            @if($don->avatar != "default.jpg")
                                                <img src="/uploads/avatars/{{$don->id}}/{{$don->avatar}}" alt="author">
                                            @else
                                                <img src="/assets/img/default.png" alt="author">
                                            @endif
                                        </div>
                                        <div class="birthday-author-name">
                                            <a href="/user/{{$don->username}}" class="h6 author-name" style="color:{{$don->groupColor()}}">
                                                @if($don->isStaff() || $don->isFounder())
                                                    <img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">
                                                @elseif($don->isDonator())
                                                    <img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">
                                                @endif{{$don->username}}
                                            </a>
                                            <div class="birthday-date">{{number_format($donation->amount2, 4)}} {{$donation->currency2}}</div>
                                        </div>
                                        <a href="/user/{{$don->username}}" class="btn btn-sm bg-blue">Go to profile</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_post" tabindex="-1" role="dialog" aria-labelledby="edit_post">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="card m-0">
                    <header class="card-heading p-b-20">
                        @if(Auth::user()->avatar == "default.jpg")
                            <img src="/assets/img/default.png" alt="" class="img-circle img-sm pull-left m-r-10">
                        @else
                            <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
                        @endif
                        <h2 class="card-title m-t-5">{{Auth::user()->username}}</h2>
                        <ul class="card-actions icons right-top">
                            <li>
                                <a href="javascript:void(0)" data-dismiss="modal" aria-label="Close">
                                    <i class="zmdi zmdi-close"></i>
                                </a>
                            </li>
                        </ul>
                    </header>
                </div>
                <form class="form-horizontal" action="/status/edit/" method="post" id="edit-status-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="status_id_for_edit" id="status_id_for_edit" value="">
                    <div class="modal-body bg-white">
                        <div class="form-group m-0 row is-empty">
                            <label class="sr-only">Description: </label>
                            <div class="col-md-12 p-0">
                                <div class="status-post-stuff-container">
                                    <textarea class="form-control" rows="5" id="edit-status-field" name="edit-status-text" placeholder="Write a comment here.."></textarea>
                                    <div class="post-media-container" id="status-edit-media-container"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding-top:0px!important;padding-bottom:0px!important;">
                            <ul class="card-actions icons left-bottom m-b-15"></ul>
                            <div class="media-upload-btn-container">
                                <a href="javascript:void(0)" id="add-youtubeurl-edit"><i class="zmdi zmdi-youtube" style="font-size:27px;"></i></a>
                                <a href="javascript:void(0)" id="add-image-edit"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                                <a href="{{url('status/get-giphy')}}" id="add-image-gif-edit"><i class="zmdi zmdi-gif" style="font-size:35px;position:absolute;"></i></a>
                            </div>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="edit-status-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="edit_comment" tabindex="-1" role="dialog" aria-labelledby="edit_comment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="card m-0">
                    <header class="card-heading p-b-20">
                        @if(Auth::user()->avatar == "default.jpg")
                            <img src="/assets/img/default.png" alt="" class="img-circle img-sm pull-left m-r-10">
                        @else
                            <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
                        @endif
                        <h2 class="card-title m-t-5">{{Auth::user()->username}}</h2>
                        <ul class="card-actions icons right-top">
                            <li>
                                <a href="javascript:void(0)" data-dismiss="modal" aria-label="Close">
                                    <i class="zmdi zmdi-close"></i>
                                </a>
                            </li>
                        </ul>
                    </header>
                </div>
                <form class="form-horizontal" action="/statuscomment/edit/" method="post" id="edit-comment-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="comment_id_for_edit" id="comment_id_for_edit" value="">
                    <div class="modal-body bg-white">
                        <div class="form-group m-0 row is-empty">
                            <label class="sr-only">Description: </label>
                            <div class="col-md-12 p-0">
                                <div class="status-post-stuff-container">
                                    <textarea class="form-control" rows="5" id="edit-comment-field" name="comment" placeholder="Write a comment here.."></textarea>
                                    <div class="post-media-container" id="comment-edit-media-container"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding-top:0px!important;padding-bottom:0px!important;">
                            <ul class="card-actions icons left-bottom m-b-15"></ul>
                            <div class="media-upload-btn-container">
                                <a href="javascript:void(0)" id="comment-add-img-btn-edit"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                                <a href="{{url('status/get-giphy')}}" id="comment-add-giphy-btn-edit"><i class="zmdi zmdi-gif" style="font-size:35px;position:absolute;"></i></a>
                            </div>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="edit-comment-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="edit_comment_reply" tabindex="-1" role="dialog" aria-labelledby="edit_comment_reply">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="card m-0">
                    <header class="card-heading p-b-20">
                        @if(Auth::user()->avatar == "default.jpg")
                            <img src="/assets/img/default.png" alt="" class="img-circle img-sm pull-left m-r-10">
                        @else
                            <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
                        @endif
                        <h2 class="card-title m-t-5">{{Auth::user()->username}}</h2>
                        <ul class="card-actions icons right-top">
                            <li>
                                <a href="javascript:void(0)" data-dismiss="modal" aria-label="Close">
                                    <i class="zmdi zmdi-close"></i>
                                </a>
                            </li>
                        </ul>
                    </header>
                </div>
                <form class="form-horizontal" action="/statuscommentreply/edit/" method="post" id="edit-comment-reply-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="reply_id_for_edit" id="reply_id_for_edit" value="">
                    <div class="modal-body bg-white">
                        <div class="form-group m-0 row is-empty">
                            <label class="sr-only">Description: </label>
                            <div class="col-md-12 p-0">
                                <div class="status-post-stuff-container">
                                    <textarea class="form-control" rows="5" id="edit-comment-reply-field" name="comment-reply" placeholder="Write a comment here.."></textarea>
                                    <div class="post-media-container" id="reply-edit-media-container"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding-top:0px!important;padding-bottom:0px!important;">
                            <ul class="card-actions icons left-bottom m-b-15"></ul>
                            <div class="media-upload-btn-container">
                                <a href="{{url('status/get-giphy')}}" id="reply-add-giphy-btn-edit"><i class="zmdi zmdi-gif" style="font-size:35px;position:absolute;"></i></a>
                            </div>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="edit-reply-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="edit_tracking" tabindex="-1" role="dialog" aria-labelledby="edit_tracking">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel-2">Select a coin to track</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="form-horizontal" class="tracking-form" role="form" method="post" action="/asdf">
                        {{ csrf_field() }}
                        <div class="form-group is-empty">
                            <label for="" class="control-label">Coin/Currency</label>
                            <input type="text" class="form-control typeahead" id="autocomplete_states" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                        </div>
                        <button type="submit" class="btn btn-primary">Start Tracking</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="poll_modal" tabindex="-1" role="dialog" aria-labelledby="poll_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel-2">Create Poll</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <p>You may create up to 4 options/answers to vote on, you do not need to fill every option field.</p>
                    <form id="poll-create-form" class="tracking-form" role="form" method="post" action="/create/poll">
                        {{ csrf_field() }}
                        <div class="form-group is-empty">
                            <label for="" class="control-label">Question/Title</label>
                            <input type="text" class="form-control" id="question" autocomplete="off" placeholder="Poll Title" value="" name="question" required/>
                        </div>
                        <div class="form-group is-empty">
                            <label for="" class="control-label answer-number">Option/Answer #<span>1</span></label>
                            <input type="text" class="form-control poll-answer-field" autocomplete="off" placeholder="Answer" value="" name="answers[]" required/>
                        </div>
                        <div class="form-group is-empty">
                            <label for="" class="control-label answer-number">Option/Answer #<span>2</span></label>
                            <input type="text" class="second-field form-control poll-answer-field last" autocomplete="off" placeholder="Answer" value="" name="answers[]" required/>
                        </div>
                        <button type="submit" class="btn btn-primary poll-create-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Creating">Create Poll</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="change_color" tabindex="-1" role="dialog" aria-labelledby="change_color">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel-2">Change color of coin</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="form-horizontal" class="color-form" role="form" method="post" action="/balance/color/">
                        {{ csrf_field() }}
                        <div class="form-group is-empty">
                            <label for="" class="control-label">Color</label>
                            <input type="text" id="color" name="color" class="form-control" value="#123456" />
                        </div>
                        <div id="colorpicker" style="width:35%!important;margin:0 auto;display:block;"></div>
                        <button class="btn btn-primary" id="change-color">Change Color</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="add_youtubeurl" tabindex="-1" role="dialog" aria-labelledby="add_image">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel-2">Add Youtube url to post</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group is-empty">
                        <label for="" class="control-label">Youtube URL</label>
                        <input type="text" class="form-control typeahead" id="youtube_url" placeholder="Youtube url" required/>
                    </div>
                    <button class="btn btn-primary" id="add-youtubeurl-button" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Fetching">Add Url</button>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="add_youtubeurl-edit" tabindex="-1" role="dialog" aria-labelledby="add_image">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel-2">Add Youtube url to post</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group is-empty">
                        <label for="" class="control-label">Youtube URL</label>
                        <input type="text" class="form-control typeahead" id="youtube_url-edit" placeholder="Youtube url" required/>
                    </div>
                    <button class="btn btn-primary" id="add-youtubeurl-button-edit">Add Url</button>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="status_post_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Photo Upload</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="status_post_photo_upload_form" role="form" method="post" action="{{url('status/post/img')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="loading_img" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
                        <div class="slim-croper" id="status-post-slim">
                            <input type="file" class="upload-img-slim" name="slim[]"/>
                        </div>
    					<button type="submit" class="btn btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="status_edit_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Photo Upload</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="status_edit_photo_upload_form" role="form" method="post" action="{{url('status/post/img')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="loading_img" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
                        <div class="slim-croper" id="status-edit-slim">
                            <input type="file" class="upload-img-slim" name="slim[]"/>
                        </div>
    					<button type="submit" class="btn btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="comment_post_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Photo Upload</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="comment_post_photo_upload_form" role="form" method="post" action="{{url('status/post/img')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="loading_img" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
                        <input type="hidden" name="comment_status_id_img" id="comment_status_id_img" value="">
                        <div class="slim-croper" id="comment-post-slim">
                            <input type="file" class="upload-img-slim" name="slim[]"/>
                        </div>
    					<button type="submit" class="btn btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>

    <div class="modal fade" id="comment_edit_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Photo Upload</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <form id="comment_edit_photo_upload_form" role="form" method="post" action="{{url('status/post/img')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="loading_img" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
                        <input type="hidden" name="comment_status_id_img_edit" id="comment_status_id_img_edit" value="">
                        <div class="slim-croper" id="comment-edit-slim">
                            <input type="file" class="upload-img-slim" name="slim[]"/>
                        </div>
    					<button type="submit" class="btn btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
@endsection

@section('js2')
    <script src="{{asset('js/slim.kickstart.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/light-gallery/js/lightgallery-all.js')}}" type="text/javascript"></script>
    <script src="/js/magnific/jquery.magnific-popup.min.js"></script>
    <script src="/js/jquery.caret.min.js"></script>
    <script src="/js/jquery.atwho.min.js"></script>
    <script src="/js/Chart.min.js"></script>
    <script src="/js/chartjs-plugin-deferred.min.js"></script>
    <script src="/js/circle-progress.min.js"></script>
    <script src="/js/dashboard.js?v=1.4"></script>
    <script src="/js/swiper.jquery.min.js"></script>
    <script src="/js/farbtastic.js"></script>

    <script>
        var cropper_status = new Slim(document.getElementById('status-post-slim'));
        var cropper_status_edit = new Slim(document.getElementById('status-edit-slim'));
        var cropper_comment = new Slim(document.getElementById('comment-post-slim'));
        var cropper_comment_edit = new Slim(document.getElementById('comment-edit-slim'));
        // $('#status-post-slim').slim('parse');

        $('.js-zoom-gallery').each(function(){
            var $this = $(this);
            $this.lightGallery({
                thumbnail: true,
                selector: 'a'
            });
        });

        var new_status = pusher.subscribe('newStatusEvent');
        new_status.bind('newStatusListner', function(data){
            var new_event_base_url = "{{url('/status/get-push/')}}";
            var new_event_final_url = new_event_base_url+"/"+data.statusId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: new_event_final_url,
                type: "get"
            }).done(function(result){
                $("#post-data").prepend(result.html);

                if (result.ischeck > 0) {
                    $('#post-data-following').prepend(result.html);
                }
                $('.image-popup-link').magnificPopup({
                    type: 'image'
                });

                $('.youtube-popup-link').magnificPopup({
                    type: 'iframe'
                });

                $('.js-zoom-gallery').each(function(){
			        var $this = $(this);
			        $this.lightGallery({
			            thumbnail: true,
			            selector: 'a'
			        });
			    });

                set_imgcontainer_size();
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var edit_status = pusher.subscribe('editStatusEvent');
        edit_status.bind('editStatusListner', function(data){
            var edit_status_base_url = "{{url('/status/get-push-edit/')}}";
            var edit_status_final_url = edit_status_base_url+"/"+data.statusId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: edit_status_final_url,
                type: "get"
            }).done(function(result){
                $('.ui-block.posted_status_'+data.statusId).each(function(){
                    $(this).html(result.html);
                });

                $('.image-popup-link').magnificPopup({
                    type: 'image'
                });

                $('.youtube-popup-link').magnificPopup({
                    type: 'iframe'
                });

                $('.js-zoom-gallery').each(function(){
			        var $this = $(this);
			        $this.lightGallery({
			            thumbnail: true,
			            selector: 'a'
			        });
			    });
                set_imgcontainer_size();
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var delete_status = pusher.subscribe('deleteStatusEvent');
        delete_status.bind('deleteStatusListner', function(data){
            $('.ui-block.posted_status_'+data.statusId).each(function(){
                $(this).remove();
            });
        });

        var new_comment = pusher.subscribe('newCommentEvent');
        new_comment.bind('newCommentListner', function(data){
            var new_comment_base_url = "{{url('/comment/get-push/')}}";
            var new_comment_final_url = new_comment_base_url+"/"+data.commentId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: new_comment_final_url,
                type: "get"
            }).done(function(result){
                console.log(result);
                $('.ui-block.posted_status_'+data.statusId).each(function(){
                    $this = $(this);
                    var current_comment_container = $this.find('.all-comment-container');
                    current_comment_container.append(result.html);
                    var comment_count_span = $this.find('.comments-shared span');

                    var current_count = comment_count_span.html();
                    var new_count = parseInt(current_count) + 1;
                    comment_count_span.html(new_count);
                });
                $('.image-popup-link').magnificPopup({
                    type: 'image'
                });

            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var edit_comment = pusher.subscribe('editCommentEvent');
        edit_comment.bind('editCommentListner', function(data){
            var edit_comment_base_url = "{{url('/comment/get-push-edit/')}}";
            var edit_comment_final_url = edit_comment_base_url+"/"+data.commentId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: edit_comment_final_url,
                type: "get"
            }).done(function(result){
                $('.ui-block.posted_status_'+data.statusId).each(function(){
                    $this = $(this);
                    var current_comment_container = $this.find('.all-comment-container .comment-id_'+data.commentId);
                    current_comment_container.html(result.html);
                });
                $('.image-popup-link').magnificPopup({
                    type: 'image'
                });

            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var delete_comment = pusher.subscribe('deleteCommentEvent');
        delete_comment.bind('deleteCommentListner', function(data){
            $('.li-comment.comment-id_'+data.commentId).each(function(){
                var $this = $(this);
                var current_status = $this.parents('.ui-block.posted_status_'+data.statusId);
                var comment_count_span = current_status.find('.comments-shared span');

                var current_count = comment_count_span.html();
                var new_count = parseInt(current_count) - 1;
                comment_count_span.html(new_count);
                $this.remove();
            });
        });

        var new_reply = pusher.subscribe('newReplyEvent');
        new_reply.bind('newReplyListner', function(data){
            var new_reply_base_url = "{{url('/reply/get-push/')}}";
            var new_reply_final_url = new_reply_base_url+"/"+data.replyId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: new_reply_final_url,
                type: "get"
            }).done(function(result){
                console.log(result);
                $('.comment-id_'+data.commentId).each(function(){
                    var reply_container = $(this).find('ul.comment-reply-container');
                    reply_container.append(result.html);
                    reply_container.css({'display': 'block'});
                });
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var edit_reply = pusher.subscribe('editReplyEvent');
        edit_reply.bind('editReplyListner', function(data){
            var edit_reply_base_url = "{{url('/reply/get-push-edit/')}}";
            var edit_reply_final_url = edit_reply_base_url+"/"+data.replyId;
            var my_id = {{Auth::user()->id}};
            $.ajax({
                url: edit_reply_final_url,
                type: "get"
            }).done(function(result){
                console.log(result);
                $('.li-comment.comment-reply_'+data.replyId).each(function(){
                    var reply_container = $(this);
                    reply_container.html(result.html);
                });
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                console.log('server not responding...');
            });
        });

        var delete_reply = pusher.subscribe('deleteReplyEvent');
        delete_reply.bind('deleteReplyListner', function(data){
            $('.li-comment.comment-reply_'+data.replyId).each(function(){
                $(this).remove();
            });
        });

        var new_vote = pusher.subscribe('newVoteEvent');
        new_vote.bind('newVoteListner', function(data){
            var new_vote_base_url = "{{url('/poll/vote-get/')}}";
            var new_vote_final_url = new_vote_base_url+"/"+data.statusId;
            $.ajax({
                url: new_vote_final_url,
                type: 'get',
                success: function(result){
                    if (result == "fail") {
                        console.log("failed");
                    }else {
                        result.poll_answers.forEach(function(answer) {
                            // console.log(answer);
                            var answer_container = $('li.answer-container_'+answer.answer_id);
                            answer_container.find('.skills-item-info>.skills-item-count>.units').html(answer.vote_percent+"%");
                            // answer_container.find('.skills-item-info>.skills-item-title input.vote.option-input').attr('checked', true);
                            answer_container.find('.skills-item-meter>.skills-item-meter-active').css({'width': answer.vote_percent+"%"});
                            var counter_html = "";
                            if (answer.vote_count == 0) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">0</span> users voted on this';
                            } else if (answer.vote_count == 1) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">1</span> user voted on this';
                            } else if (answer.vote_count > 1) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">'+answer.vote_count+'</span> users voted on this';
                            }
                            answer_container.find('.counter-friends').html(counter_html);
                            var users_htmls = "";
                            var show_user_count = 0;
                            answer.vote_users.forEach(function(user) {
                                if (show_user_count <= 10) {
                                    users_htmls += '<li><a href="/user/'+user.username+'">';
                                    if (user.avatar != "default.jpg") {
                                        users_htmls += '<img src="/uploads/avatars/'+user.username+'/'+user.avatar+'" alt="friend">';
                                    } else {
                                        users_htmls += '<img src="/assets/img/default.png" alt="friend">';
                                    }
                                    users_htmls += '</a></li>';
                                }
                            });

                            if (answer.vote_count > 10) {
                                var minused_count = answer.vote_count - 10;
                                users_htmls += '<li><a href="#" class="all-users">+'+minused_count+'</a></li>';
                            }
                            answer_container.find('ul.friends-harmonic').html(users_htmls);
                        });
                        $('.ui-block.posted_status_'+data.statusId).each(function(e) {
                            $(this).find('article.altpocket').removeClass('voting');
                        });
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });

        $('.image-popup-link').magnificPopup({
            type: 'image'
            // other options
        });

        $('.youtube-popup-link').magnificPopup({
    		type: 'iframe'
    	});
        $('#colorpicker').farbtastic('#color');

        var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");

        var colors = [];
        //Function to convert rgb color to hex format
        function rgb2hex(rgb) {
            rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
        }

        function hex(x) {
            return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
        }

        $(".point-color").each(function(n){
            colors[n] = rgb2hex($(this).css('background-color'));
        });

        $(".select-color").click(function(){
            $(".color-form").attr('action', '/color/select/'+$(this).attr('id'));
        });

        var pieColorChart = document.getElementById("pie-color-chart2");
        if (pieColorChart !== null) {
            var ctx_pc = pieColorChart.getContext("2d");
            var data_pc = {
                labels: [
                    @foreach($balances2 as $balance)
                        <?php
                            $crypto = DB::table('cryptos')->where('symbol', $balance->currency)->select('name')->first();

                            if($crypto === null)
                            {
                                $crypto = DB::table('world_coins')->where('symbol', $balance->currency)->select('name')->first();
                            }
                        ?>

                        "{{$crypto->name}}",
                    @endforeach
                ],
                datasets: [
                    {
                        data: [
                            @foreach($balances2 as $balance)
                                <?php
                                    $crypto = DB::table('cryptos')->where('symbol', $balance->currency)->select('price_btc')->first();

                                    if($crypto === null)
                                    {
                                    $crypto = DB::table('world_coins')->where('symbol', $balance->currency)->select('price_btc')->first();
                                    }
                                ?>
                                {{number_format($balance->amount * $crypto->price_btc, 4)}},
                            @endforeach
                        ],
                        borderWidth: 0,
                        backgroundColor: colors
                    }
                ],
            };

            var pieColorEl = new Chart(ctx_pc, {
                type: 'doughnut',
                data: data_pc,
                options: {
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItem, data) {
                                return data['labels'][tooltipItem[0]['index']];
                            },
                            label: function(tooltipItem, data) {
                                return data['datasets'][0]['data'][tooltipItem['index']] + " BTC";
                            },
                            afterLabel: function(tooltipItem, data) {
                                var dataset = data['datasets'][0];
                                var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][0]['total']) * 100)
                                return percent + '% of holdings';
                            }
                        },
                        titleFontSize: 16,
                        titleFontColor: '#fff',
                        bodyFontColor: '#fafbfd',
                        bodyFontSize: 14,
                        displayColors: false
                    },
                    deferred: {           // enabled by default
                        delay: 300        // delay of 500 ms after the canvas is considered inside the viewport
                    },
                    cutoutPercentage:93,
                    legend: {
                        display: false
                    },
                    animation: {
                        animateScale: false
                    }
                }
            });
        }

        var mySwiper = new Swiper ('.swiper-container', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,

            // If we need pagination
            pagination: '.swiper-pagination',

            // Navigation arrows
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev'
        })

        if(window.location.href.indexOf("status") > -1) {

        } else {
            var page = 1;
            var mepage = 1;
            var followpage = 1;
            var reading = 0;
            $(window).scroll(function() {
                if($(window).scrollTop() + $(window).height() >= $(document).height() && reading == 0) {
                    reading = 1;
                    if($("#browse_all").is(":visible")){
                        page++;
                    } else if($("#personal_feed").is(":visible")){
                        followpage++;
                    }
                    loadMoreData(page, mepage, followpage);
                    reading = 0;
                }
            });

            function loadMoreData(page, mepage, followpage){
                if($("#browse_all").is(":visible")){
                    $.ajax({
                        url: '?page=' + page,
                        type: "get",
                        beforeSend: function(){
                            $('.ajax-load').show();
                        }
                    }).done(function(data){
                        if(data.html == " "){
                            $('.ajax-load').html("No more records found");
                            return;
                        }
                        $('.ajax-load').hide();
                        $("#post-data").append(data.html);
                        $('.image-popup-link').magnificPopup({
                            type: 'image'
                            // other options
                        });

                        $('.youtube-popup-link').magnificPopup({
                    		type: 'iframe'
                    	});
                    }).fail(function(jqXHR, ajaxOptions, thrownError){
                        alert('server not responding...');
                    });
                } else if($("#personal_feed").is(":visible")){
                    $.ajax({
                        url: '?page=' + followpage,
                        type: "get",
                        data: {type: "following"},
                        beforeSend: function(){
                            $('.ajax-load').show();
                        }
                    }).done(function(data){
                        if(data.html == " "){
                            $('.ajax-load').html("No more records found");
                            return;
                        }
                        $('.ajax-load').hide();
                        $("#post-data-following").append(data.html);
                        $('.image-popup-link').magnificPopup({
                            type: 'image'
                            // other options
                        });

                        $('.youtube-popup-link').magnificPopup({
                    		type: 'iframe'
                    	});
                    }).fail(function(jqXHR, ajaxOptions, thrownError){
                        alert('server not responding...');
                    });
                }
            }
        }

        var loadedMe = 0;
        var loadedFollowing = 0;

        $("#view-personal").click(function(){
            if(loadedFollowing == 0){
                $.ajax({
                    url: '?page=' + 1,
                    type: "get",
                    data: {type: "following"},
                    beforeSend: function(){
                        $('.ajax-load').show();
                    }
                }).done(function(data){
                    // console.log(data);
                    if(data.html == " "){
                        $('.ajax-load').html("No more records found");
                        return;
                    }
                    $('.ajax-load').hide();
                    $("#post-data-following").append(data.html);
                    $('.image-popup-link').magnificPopup({
                        type: 'image'
                        // other options
                    });

                    $('.youtube-popup-link').magnificPopup({
                        type: 'iframe'
                    });
                }).fail(function(jqXHR, ajaxOptions, thrownError){
                    alert('server not responding...');
                });
            }
            loadedFollowing = 1;
        });
    </script>
@endsection
