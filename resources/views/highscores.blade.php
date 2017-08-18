@extends('layouts.app')

@section('title')
Highscores
@endsection

@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Highscores</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
</div>
          <div id="content" class="container-fluid">
            <div class="content-body">
              <div class="row">
                <div class="col-xs-12">
                  <div class="card card-data-tables highscores-table-wrapper">
                      <br>
                        <div class="alert alert-success" role="alert">
                                <strong>Note:</strong> All the following values are verified by our automatic order grabbing system.<br>
                                If you are not on this list and already have Verified Investments, we advise you to reset your account and re-import.<br>
                               This is because we have recently changed our import function and it is now 100% accurate.
                              </div>
                    <header class="card-heading">
                      <small class="dataTables_info">
                        </small>
                      <div class="card-search">
                        <div id="productsTable_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="productsTable" class="mdl-data-table highscores-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">User</th>
                              <th class="col-xs-2">Group</th>
                              <th class="col-xs-2">Invested</th>
                              <th class="col-xs-2">Total Profit</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($users as $user)
                              @if($user->polo_api_key != "" || $user->api_key != "")
                              <?php
                                $invested = 0;
                                foreach(DB::table('investments')->where([['userid', '=', $user->id], ['bittrex_id', '!=', ''], ['sold_for', '=', null], ['market', '!=', 'manual']])->get() as $investment){
                                    $invested += $investment->usd_total;
                                }
                              
                                $investments = DB::table('investments')->where([['userid', $user->id], ['market', '!=', 'manual']])->get();
                                $spent = 0;
                                $sold = 0;
                                $networth = 0;
                                foreach($investments as $investment){
                                    $spent += $investment->usd_total;
                                    $sold += $investment->sold_for;
                                    if($investment->sold_at == null){
                                        if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                                        $networth += $investment->amount * DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } elseif(DB::table('polos')->where('symbol', $investment->crypto)->first()) {
                                        $networth += $investment->amount * DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } elseif(DB::table('bittrexes')->where('symbol', $investment->crypto)->first()) {
                                        $networth += $investment->amount * DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } else {
                                            $networth += 0;
                                        }
                                    }
                                }
                              
                              $profit = (($networth * $btc) - $spent) + $sold;
                              
                              ?>
                              
                            <tr>
                              <td><a style="color:{{$user->groupColor()}}" href="/user/{{$user->username}}"><span class="avatar">
                                                        @if($user->avatar == "default.jpg")
														<img src="/assets/img/logo.png"  alt="" class="img-circle max-w-35">
                                                        @else
														<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}"  alt="" class="img-circle max-w-35">
                                                        @endif
                                  
                                  </span>{{$user->username}}</a></td>
                              <td>@if($user->groupName())<span class="label" style="background:{{$user->groupColor()}}">{{$user->groupName()}}</span> @else <span class="label label-primary">Beginner</span> @endif </td>
                                <td><span style="color:#73c04d">{{number_format($spent, 2)}}$</span></td>
                              <td>
                                @if($profit > 0)  
                                <span style="color:#73c04d">{{number_format($profit, 2)}}$</span>
                                @elseif($profit == 0)
                                <span style="color:#73c04d">{{number_format($profit, 2)}}$</span>
                                @else  
                                <span style="color:#de6b6b">{{number_format($profit, 2)}}$</span>
                                @endif    
                                  
                                </td>
                            </tr>
                              @endif
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <section id="chat_compose_wrapper">
              <div class="tippy-top">
                <div class="recipient">Allison Grayce</div>
                <ul class="card-actions icons  right-top">
                  <li>
                    <a href="javascript:void(0)">
                      <i class="zmdi zmdi-videocam"></i>
                    </a>
                  </li>
                  <li class="dropdown">
                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                      <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu btn-primary dropdown-menu-right">
                      <li>
                        <a href="javascript:void(0)">Option One</a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">Option Two</a>
                      </li>
                      <li>
                        <a href="javascript:void(0)">Option Three</a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="javascript:void(0)" data-chat="close">
                      <i class="zmdi zmdi-close"></i>
                    </a>
                  </li>
                </ul>
              </div>
              <div class='chat-wrapper scrollbar'>
                <div class='chat-message scrollbar'>
                  <div class='chat-message chat-message-recipient'>
                    <img class='chat-image chat-image-default' src='assets/img/profiles/05.jpg' />
                    <div class='chat-message-wrapper'>
                      <div class='chat-message-content'>
                        <p>Hey Mike, we have funding for our new project!</p>
                      </div>
                      <div class='chat-details'>
                        <span class='today small'></span>
                      </div>
                    </div>
                  </div>
                  <div class='chat-message chat-message-sender'>
                    <img class='chat-image chat-image-default' src='assets/img/profiles/02.jpg' />
                    <div class='chat-message-wrapper '>
                      <div class='chat-message-content'>
                        <p>Awesome! Photo booth banh mi pitchfork kickstarter whatever, prism godard ethical 90's cray selvage.</p>
                      </div>
                      <div class='chat-details'>
                        <span class='today small'></span>
                      </div>
                    </div>
                  </div>
                  <div class='chat-message chat-message-recipient'>
                    <img class='chat-image chat-image-default' src='assets/img/profiles/05.jpg' />
                    <div class='chat-message-wrapper'>
                      <div class='chat-message-content'>
                        <p> Artisan glossier vaporware meditation paleo humblebrag forage small batch.</p>
                      </div>
                      <div class='chat-details'>
                        <span class='today small'></span>
                      </div>
                    </div>
                  </div>
                  <div class='chat-message chat-message-sender'>
                    <img class='chat-image chat-image-default' src='assets/img/profiles/02.jpg' />
                    <div class='chat-message-wrapper'>
                      <div class='chat-message-content'>
                        <p>Bushwick letterpress vegan craft beer dreamcatcher kickstarter.</p>
                      </div>
                      <div class='chat-details'>
                        <span class='today small'></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <footer id="compose-footer">
                <form class="form-horizontal compose-form">
                  <ul class="card-actions icons left-bottom">
                    <li>
                      <a href="javascript:void(0)">
                        <i class="zmdi zmdi-attachment-alt"></i>
                      </a>
                    </li>
                    <li>
                      <a href="javascript:void(0)">
                        <i class="zmdi zmdi-mood"></i>
                      </a>
                    </li>
                  </ul>
                  <div class="form-group m-10 p-l-75 is-empty">
                    <div class="input-group">
                      <label class="sr-only">Leave a comment...</label>
                      <input type="text" class="form-control form-rounded input-lightGray" placeholder="Leave a comment..">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-blue btn-fab  btn-fab-sm">
                          <i class="zmdi zmdi-mail-send"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </form>
              </footer>
            </section>
          </div>
        
<div>

@endsection