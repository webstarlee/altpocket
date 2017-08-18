@extends('layouts.app')

@section('title')
Data
@endsection



@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Altpocket.io Stats</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
       <div id="content" class="container-fluid">
            <div class="content-body">

                                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Registered users</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">{{count(DB::table('users')->get())}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Registered Today</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">{{count(DB::table('users')->whereDate('created_at', DB::raw('CURDATE()'))->get())}}</h1>
                      </div>
                    </div>
                  </div>
                </div>
           </div>
        </div>
    

</div>



@endsection