@extends('layouts.new')

@section('title', $post->title)


@section('content')
  <!--start banner Area-->
  <section class="banner-area parallax-bg" data-stellar-background-ratio="0.001" style="background:none;">
      <div class="container">
          <div class="banner-content">
              <h2>{{$post->title}}</h2>
          </div>
      </div>
  </section>
  <!--End banner area-->

  <!-- start single-blog-area-->
  <section class="blog-area single-blog-area">
      <div class="container">
          <div class="row">
              <div class="col-md-12 full-width-single2">
                  <div class="blog-section">
                      <article class="blog-items single-post">
                          <a href="#" class="blog-img">
                              <img  class="img-responsive" src="/uploads/blog/{{$post->id}}/{{$post->image}}" alt="">
                          </a>
                          <div class="blog-content">
                              <a href="#"><h2>{{$post->title}} </h2></a>
                              {!! $post->description !!}
                              <ul class="post-info">
                                  <li>Posted: {{$post->created_at}}</li>
                                  <li>Author: <a href="/user/{{$user->username}}">{{$user->username}}</a></li>
                              </ul>
                          </div>
                      </article>
                      <div class="comments row">
                          <h3 class="comment-title">{{count($comments)}} Responses</h3>
                          @foreach($comments as $comment)
                            <?php
                              $u = DB::table('users')->where('id', $comment->userid)->first();
                             ?>
                          <div class="media comment">
                              @if($u->avatar != "default.jpg")
                                <div class="media-left"><img class="img-circle" src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="blog-comment" style="width:80px;"></div>
                              @else
                                <div class="media-left"><img class="img-circle" src="/assets/img/default.png" alt="blog-comment"></div>
                              @endif
                              <div class="media-body">
                                @if(Auth::user())
                                  @if(Auth::user()->id == $comment->userid || Auth::user()->isStaff() || Auth::user()->isFounder())
                                    <span style="float:right;"><a href="/comment/{{$comment->id}}/delete"<i class="fa fa-trash"></i></a></span>
                                  @endif
                                @endif

                                  <h5 class="commenter-name">{{$u->username}}</h5>
                                  <h6>{{$comment->created_at}}</h6>
                                  <p>{{$comment->comment}}</p>
                              </div>
                          </div>
                        @endforeach

                      </div>
                      @if(Auth::user())
                      <div class="row m0">
                          <h2 class="comment-title">Leave a Comment</h2>
                          <form action="/post/{{$post->id}}/comment" method="POST" class="contact-form">
                            {{csrf_field()}}
                              <textarea class="form-control" placeholder="Write your comment here.." name="comment" maxlength="150"></textarea>
                              <input type="submit" value="Leave Comment" class="btn sub_btn sub_btn-two">
                          </form>
                      </div>
                    @endif
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- End single-blog-area-->

@endsection
