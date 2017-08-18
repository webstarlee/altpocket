@extends('layouts.app')


@section('title')
Staff
@endsection


@php
$edwin = DB::table('users')->where('id', 1)->first();
$jonathan = DB::table('users')->where('id', 359)->first();
$frukt = DB::table('users')->where('id', 108)->first();
$hein = DB::table('users')->where('id', 1233)->first();
$bzrk = DB::table('users')->where('id', 462)->first();
$erik = DB::table('users')->where('id', 20)->first();

@endphp



@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Altpocket.io Team</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content">
                    <div class="row">
                  <div class="col-xs-12">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">Our team</h2>
                          <hr>
                      </header>
                      <div class="card-body">
                        <p>This is the Altpocket.io team, it currently consists of 1 developer, 1 Marketing/Community manager and 3 support staffs. <br> All of our staff can be conacted through our support discord or support center.</p>
                      </div>
                    </div>
                  </div>
                </div>
                </div>
                <div class="row">

                  <div class="col-md-12" style="text-align:center;">
                    <h1>Founders</h1>
                    <hr>
                    <br>
                  </div>

                </div>

                <div class="row">
                  <div class="col-md-4 col-md-offset-2">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/1/{{$edwin->avatar}}" alt="" class="img-circle">
                    </header>
                    <div class="card-body">
                      <h3 class="name">Edwin</h3>
                      <span class="title">Developer</span>
                      <a href="/user/edwin" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                  </div>
                  </div>
                  <div class="col-md-4">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/359/{{$jonathan->avatar}}" alt="" class="img-circle">
</header>
                    <div class="card-body">
                      <h3 class="name">Svensson</h3>
                      <span class="title">Marketing &amp; Community Manager</span>
                      <a href="/user/svensson" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                    </div>
                  </div>
                </div>


                <div class="row">

                  <div class="col-md-12" style="text-align:center;">
                    <h1>Support Team</h1>
                    <hr>
                    <br>
                  </div>

                </div>

                <div class="row">
                  <div class="col-md-4 col-md-offset-4">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/108/{{$frukt->avatar}}" alt="" class="img-circle">
                    </header>
                    <div class="card-body">
                      <h3 class="name">Frukt</h3>
                      <span class="title">Head Of Support</span>
                      <a href="/user/frukt" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                  </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/1233/{{$hein->avatar}}" alt="" class="img-circle">
                    </header>
                    <div class="card-body">
                      <h3 class="name">heinekens</h3>
                      <span class="title">Support Staff</span>
                      <a href="/user/heinekens" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                  </div>
                  </div>
                  <div class="col-md-4">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/462/{{$bzrk->avatar}}" alt="" class="img-circle">
</header>
                    <div class="card-body">
                      <h3 class="name">bzrk</h3>
                      <span class="title">Support Staff</span>
                      <a href="/user/bzrk" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                  <div class="card type--profile">
                    <header class="card-heading card-background" id="card_img_02">
                      <img src="/uploads/avatars/20/{{$erik->avatar}}" alt="" class="img-circle">
</header>
                    <div class="card-body">
                      <h3 class="name">Erik</h3>
                      <span class="title">Support Staff</span>
                      <a href="/user/erik" type="button" class="btn btn-primary btn-round">Profile</a>
                    </div>
                    </div>
                  </div>
                </div>


                </div>

            </div>
        </div>


</div>

@endsection
