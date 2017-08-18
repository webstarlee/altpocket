@extends('layouts.support')

@section('title', 'Altpocket Updates')

@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>Updates & Changelog</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Updates & Chnagelog</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->

  <!-- Start Undate News  -->
  <div class="update-news-wrapper themeix-ptb">
    <div class="container">
           <div class="row">
             @foreach($updates as $update)
               <?php
                  $bugs = \App\Change::where([['updateid', '=', $update->id], ['type', '=', 'bug']])->get();
                  $news = \App\Change::where([['updateid', '=', $update->id], ['type', '=', 'new']])->get();
                ?>
                {{$update->id}}
               <div class="col-sm-6 col-md-4">
                   <div class="update-box">
                       <div class="update-version">
                           <h2>V.{{$update->version}}</h2>
                           <span class="badge update-badge">new</span>
                       </div>
                       <h3>{{$update->name}}</h3>
                       <p>{{$update->description}}</p>
                       <button type="button" class="update-btn" data-toggle="modal" data-target="#update-{{$update->id}}" >See Changelog</button>
                       <div id="update-{{$update->id}}" class="modal fade">
                           <div class="modal-dialog modal-lg">
                               <div class="modal-content">
                                   <div class="modal-heading clearfix">
                                       <h3 class="modal-title">V.{{$update->version}} changelog</h3>
                                   </div>
                                   <div class="modal-body clearfix">
                                       <div class="col-sm-6">
                                        <h4 class="modal-subtitle">Bug Fixes</h4>
                                        <div class="bug-list">
                                        <ul>
                                          @foreach($bugs as $bug)
                                            <li>{{$bug->description}}</li>
                                          @endforeach
                                        </ul>
                                        </div>
                                       </div>
                                       <div class="col-sm-6">
                                        <h4 class="modal-subtitle">New Stuff</h4>
                                        <div class="update-list">
                                        <ul>
                                          @foreach($news as $new)
                                            <li>{{$new->description}}</li>
                                          @endforeach
                                         </ul>
                                        </div>
                                       </div>
                                   </div>
                                   <div class="modal-footer clearfix">
                                       <button type="button" class="themeix-btn danger-bg" data-dismiss="modal">close</button>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
             @endforeach
           </div>
       </div>
  </div>
  <!-- End Update News  -->
  <!-- Start About-us Bg -->
  <div class="about-us-bg" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="about-us-info">
                      <h2>Altpocket is here to stay, register today and track your cryptocurrency investment among thousands of users.</h2>
                      <a class="themeix-btn primary-bg" href="/login">Register Today</a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End About-us Bg -->


@endsection
