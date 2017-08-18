@extends('layouts.app')


@section('title')
Awards
@endsection


@php
    $user_r1 = DB::table('users')->orderBy('impressed', 'desc')->first();
    $user_r2 = DB::table('users')->orderBy('impressed', 'desc')->skip(1)->first();
    $user_r3 = DB::table('users')->orderBy('impressed', 'desc')->skip(2)->first();

    $rank1_profit = DB::table('stats')->orderBy('legit', 'desc')->first();
    $rank1 = DB::table('users')->where('id', $rank1_profit->userid)->first();

    $rank2_profit = DB::table('stats')->orderBy('legit', 'desc')->skip(1)->first();
    $rank2 = DB::table('users')->where('id', $rank2_profit->userid)->first();

    $rank3_profit = DB::table('stats')->orderBy('legit', 'desc')->skip(2)->first();
    $rank3 = DB::table('users')->where('id', $rank3_profit->userid)->first();



@endphp



@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Altpocket.io Leaderboards</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content">
                    <div class="row">
                  <div class="col-xs-12 col-md-6">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">Most Verified Net Worth (Updated Weekly)</h2>
                          <hr>
                      </header>

                      <div class="card-body">
                          <div class="row">
                            <div class="col-xs-6 col-xs-offset-3" style="text-align:center;">
                                <p>Rank 1</p>
                                <a href="/user/{{$rank1->username}}">
                                @if($rank1->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$rank1->id}}/{{$rank1->avatar}}" class="img-circle" style="width:130px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:130px"><br>
                                @endif
                                {{$rank1->username}}</a>
                                <p>{{number_format($rank1_profit->legit, 2)}} <i class="fa fa-btc"></i>  Net Worth</p>
                            </div>
                          </div>
                          <hr>
                          <div class="row"  style="text-align:center;">
                              <div class="col-xs-6">
                                <p>Rank 2</p>
                                <a href="/user/{{$rank2->username}}">
                                @if($rank2->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$rank2->id}}/{{$rank2->avatar}}" class="img-circle" style="width:90px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:90px"><br>
                                @endif
                                {{$rank2->username}}</a>
                                <p>{{number_format($rank2_profit->legit, 2)}} <i class="fa fa-btc"></i>  Net Worth</p>
                              </div>
                              <div class="col-xs-6">
                                <p>Rank 3</p>
                                <a href="/user/{{$rank3->username}}">
                                @if($rank3->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$rank3->id}}/{{$rank3->avatar}}" class="img-circle" style="width:90px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:90px"><br>
                                @endif
                                {{$rank3->username}}</a>
                                <p>{{number_format($rank3_profit->legit, 2)}} <i class="fa fa-btc"></i> Net Worth</p>
                            </div>
                          </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-xs-12 col-md-6">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">Most Impressions</h2>
                          <hr>
                      </header>
                      <div class="card-body">
                          <div class="row">
                            <div class="col-xs-6 col-xs-offset-3" style="text-align:center;">

                                <p>Rank 1</p>
                                <a href="/user/{{$user_r1->username}}">
                                @if($user_r1->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$user_r1->id}}/{{$user_r1->avatar}}" class="img-circle" style="width:130px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:130px"><br>
                                @endif
                                {{$user_r1->username}}</a>
                                <p>{{$user_r1->impressed}} Impressions</p>
                            </div>
                          </div>
                          <hr>
                          <div class="row"  style="text-align:center;">
                              <div class="col-xs-6">
                                <p>Rank 2</p>
                                <a href="/user/{{$user_r2->username}}">
                                @if($user_r2->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$user_r2->id}}/{{$user_r2->avatar}}" class="img-circle" style="width:90px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:90px"><br>
                                @endif
                                {{$user_r2->username}}</a>
                                <p>{{$user_r2->impressed}} Impressions</p>
                              </div>
                              <div class="col-xs-6">
                                <p>Rank 3</p>
                                <a href="/user/{{$user_r3->username}}">
                                @if($user_r3->avatar != "default.jpg")
                                <img src="/uploads/avatars/{{$user_r3->id}}/{{$user_r3->avatar}}" class="img-circle" style="width:90px"><br>
                                @else
                                <img src="/assets/img/logo.png" class="img-circle" style="width:90px"><br>
                                @endif
                                {{$user_r3->username}}</a>
                                <p>{{$user_r3->impressed}} Impressions</p>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" style="display:none;">
                    <div class=" col-xs-12 col-md-6 col-md-offset-3">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">Most Verified Profit</h2>
                          <hr>
                      </header>
                      <div class="card-body">
                           <div class="row">
                            <div class="col-xs-6 col-xs-offset-3" style="text-align:center;">
                                <p>Rank 1</p>
                                <img src="/uploads/avatars/1/e3f3e3b464a7c8e5722fae379459e8a6.png" class="img-circle" style="width:130px"><br>
                                <a href="#">Edwin</a>
                                <p>$150 Profit</p>
                            </div>
                          </div>
                          <hr>
                          <div class="row"  style="text-align:center;">
                              <div class="col-xs-6">
                                <p>Rank 2</p>
                                <img src="/uploads/avatars/1/e3f3e3b464a7c8e5722fae379459e8a6.png" class="img-circle" style="width:90px"><br>
                                <a href="#">Edwin</a>
                                <p>110 Followers</p>
                              </div>
                              <div class="col-xs-6">
                                <p>Rank 3</p>
                                <img src="/uploads/avatars/1/e3f3e3b464a7c8e5722fae379459e8a6.png" class="img-circle" style="width:90px"><br>
                                <a href="#">Edwin</a>
                                <p>110 Followers</p>
                            </div>
                          </div>
                      </div>
                    </div>
                    </div>


                    </div>





                </div>
                </div>

            </div>
        </div>


</div>

@endsection
