@extends('layouts.new')

@section('title', 'Our Blog')

@section('content')
  <!--start banner Area-->
  <section class="banner-area parallax-bg" data-stellar-background-ratio="0.001" style="background:none;">
      <div class="container">
          <div class="banner-content">
              <h2>Our Blog</h2>
              <p>Latest news on Altpocket, Dev blogs and much more.</p>
          </div>
      </div>
  </section>
  <!--End banner area-->

  <!-- start blog area-->
  <section class="blog-area">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="blog-section full-width-blog">
                      @foreach($posts as $post)
                        <?php
                          $user = DB::table('users')->where('id', $post->userid)->first();
                         ?>
                      <article class="blog-items">
                          <a href="/post/{{$post->id}}" class="blog-img">
                              <img  class="img-responsive" src="/uploads/blog/{{$post->id}}/{{$post->image}}" alt="">
                          </a>
                          <div class="blog-content">
                              <a href="/post/{{$post->id}}"><h2>{{$post->title}}</h2></a>
                              <p>{!!substr($post->description, 0, 450)!!}...</p>
                              <ul class="post-info">
                                  <li>Posted: <span>{{$post->created_at}}</span></li>
                                  <li>Author: <a href="/user/{{$user->username}}"> {{$user->username}}</a></li>
                              </ul>
                          </div>
                      </article>
                    @endforeach

                      @if(count($posts) > 15)
                      <div class="row m0 blog-pagination nav">
                          <ul>
                              <li class="active"><a href="#">1</a></li>
                              <li><a href="#">2</a></li>
                              <li><a href="#">3</a></li>
                              <li><a href="#">4</a></li>
                              <li><a href="#">5</a></li>
                              <li><a href="#">...</a></li>
                              <li><a href="#">12</a></li>
                              <li><a href="#">Next<i class="lnr lnr-arrow-right"></i></a></li>
                          </ul>
                      </div>
                    @endif
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- End blog area-->

@endsection
