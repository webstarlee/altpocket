@extends('layouts.support')

<?php

  $user = DB::table('users')->where('id', $question->userid)->first()

 ?>

@section('title', $question->title)

@section('content')

  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>{{$question->title}}</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Question</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->
  <!-- Start Answers Area -->
  <div class="answers-area themeix-ptb">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="answers-wrapper">
                      <div class="dwqa-single-question">
                          <div class="dwqa-breadcrumbs">
                              <a href="/questions">Questions</a>
                              <span class="dwqa-sep"> › </span>
                              <a href="#">Category : {{$question->category}}</a>
                              <span class="dwqa-sep"> › </span>
                              <span class="dwqa-current">{{$question->title}}</span>
                          </div>
                          <!-- Start Question Item -->
                          <div class="dwqa-question-item">
                              <div class="dwqa-question-vote">
                                  <span class="dwqa-vote-count">{{$question->votes}}</span>
                                  <a class="dwqa-vote dwqa-vote-up" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'question'], ['questionid', '=', $question->id]])->exists()) style="border-color:transparent transparent #71c668;" @endif href="/question/{{$question->id}}/upvote">Vote Up</a>
                                  <a class="dwqa-vote dwqa-vote-down" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'question'], ['questionid', '=', $question->id]])->exists()) style="border-color:#f26c4f transparent transparent;" @endif href="/question/{{$question->id}}/downvote">Vote Down</a>
                              </div>
                              <div class="dwqa-question-meta">
                                  <span>
                                  <a href="/user/{{$user->username}}">
                                    @if($user->avatar != "default.jpg")
                                      <img class="avatar avatar-48 photo" alt="Author" style="width:54px;" src="/uploads/avatars/{{$user->id}}/{{$user->avatar }}">
                                    @else
                                      <img class="avatar avatar-48 photo" alt="Author" style="width:54px;" src="/assets/img/default.png">
                                    @endif
                                    @if(Auth::user())
                                    @if(\App\User::where('id', $user->id)->first()->isStaff() || \App\User::where('id', $user->id)->first()->isFounder())
                                      <span class="label label-success">Staff</span>
                                    @endif
                                  @endif
                                  {{$user->username}}
                                  </a>
                                  asked {{$question->created_at->diffForHumans()}}
                                  </span>
                                  <span class="dwqa-question-actions" style="float:right;">
                                    @if(Auth::user() && Auth::user()->id == $question->userid || Auth::user() && Auth::user()->isFounder() || Auth::user() && Auth::user()->isStaff())
                                    <a href="javascript:void(0)" id="delete-question" data-toggle="tooltip" title="Delete" style="margin-right:5px;"><i class="fa fa-trash"></i></a>
                                    <a href="/question/{{$question->id}}/edit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                    @endif
                                  </span>
                              </div>
                              <div class="dwqa-question-content">
                              <p>{!! $question->question !!}</p>
                              </div>
                              <footer class="dwqa-question-footer">
                                  <div class="dwqa-question-meta"></div>
                              </footer>

                          </div>
                          <!-- End Questions Ityem -->
                          <!-- Start Answer item -->
                          <div class="dwqa-answers" style="margin-top:130px">
                              <div class="dwqa-answers-title"><span>{{count(DB::table('answers')->where('questionid', $question->id)->get())}} Answers</span></div>
                              <div class="dwqa-answers-list">
                                @if($answer)
                                  <?php
                                    $u = DB::table('users')->where('id', $answer->userid)->first();
                                    $replies = DB::table('replies')->where('answerid', $answer->id)->get();
                                   ?>

                                  <div class="dwqa-answer-item @if($answer->tag == 1) dwqa-best-answer @endif " id="answer-{{$answer->id}}">
                                      <div class="dwqa-answer-vote">
                                          <span class="dwqa-vote-count">{{$answer->votes}}</span>
                                          <a class="dwqa-vote dwqa-vote-up" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'answer'], ['questionid', '=', $answer->id]])->exists()) style="border-color:transparent transparent #71c668;" @endif href="/answer/{{$answer->id}}/upvote">Vote Up</a>
                                          <a class="dwqa-vote dwqa-vote-down" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'answer'], ['questionid', '=', $answer->id]])->exists()) style="border-color:#f26c4f transparent transparent;" @endif href="/answer/{{$answer->id}}/downvote">Vote Down</a>
                                      </div>
                                        <span class="dwqa-pick-best-answer">Best Answer</span>
                                      <div class="dwqa-answer-meta" style="width:100%;">
                                          <span><a style="color:#2196F3;" href="/user/{{$u->username}}">
                                            @if($u->avatar != "default.jpg")
                                            <img alt="authors" style="width:54px;" src="/uploads/avatars/{{$u->id}}/{{$u->avatar }}"/>
                                            @else
                                            <img alt="authors" style="width:54px;" src="/assets/img/default.png"/>
                                            @endif
                                            @if(\App\User::where('id', $u->id)->first()->isStaff() || \App\User::where('id', $u->id)->first()->isFounder())
                                              <span class="label label-success">Staff</span>
                                            @endif
                                            {{$u->username}}
                                          </a>
                                          answered {{$answer->created_at->diffForHumans()}}
                                        </span>
                                        <span class="dwqa-answer-actions" style="float:right;">
                                          <a href="/reply/{{$answer->id}}" class="reply-answer" id="{{$answer->id}}" data-toggle="tooltip" title="Reply" style="margin-right:5px;color:#2196F3;"><i class="fa fa-reply"></i></a>
                                          @if(Auth::user() && Auth::user()->id == $answer->userid || Auth::user() && Auth::user()->isFounder() || Auth::user() && Auth::user()->isStaff())
                                          <a href="javascript:void(0)" class="delete-answer" id="{{$answer->id}}" data-toggle="tooltip" title="Delete" style="margin-right:5px;color:#2196F3;"><i class="fa fa-trash"></i></a>
                                          <a href="/answer/{{$answer->id}}/edit" data-toggle="tooltip" title="Edit" style="color:#2196F3;"><i class="fa fa-pencil"></i></a>
                                          @endif
                                          @if(Auth::user())
                                          @if(Auth::user()->isStaff() || Auth::user()->isFounder() || Auth::user()->id == $question->userid)
                                            <a href="/answer/{{$answer->id}}/best" id="{{$answer->id}}" data-toggle="tooltip" title="Best Answer" style="margin-right:5px;color:#2196F3;"><i class="fa fa-check"></i></a>
                                          @endif
                                        @endif
                                        </span>
                                      </div>
                                      <div class="dwqa-answer-content">
                                          <p>{!!$answer->description!!}</p>
                                      </div>

                                      <div class="dwqa-comments">
                                          <div class="dwqa-comments-list">
                                            @foreach($replies as $reply)
                                              <?php
                                                $rep = DB::table('users')->where('id', $reply->userid)->first();
                                              ?>
                                              <div class="dwqa-comment">
                                                  <div class="dwqa-comment-meta">
                                                      <a href="/user/{{$rep->username}}">
                                                        @if($rep->avatar != "default.jpg")
                                                          <img alt="author" style="width:24px!important;height:24px!important;" src="/uploads/avatars/{{$rep->id}}/{{$rep->avatar}}" class="avatar avatar-16 photo" />
                                                        @else
                                                          <img alt="author" style="width:24px!important;height:24px!important;" src="/assets/img/default.png" class="avatar avatar-16 photo" />
                                                        @endif
                                                        @if(\App\User::where('id', $rep->id)->first()->isStaff() || \App\User::where('id', $rep->id)->first()->isFounder())
                                                          <span class="label label-success">Staff</span>
                                                        @endif
                                                      {{$rep->username}}
                                                      </a>
                                                      replied {{\Carbon\Carbon::parse($reply->created_at)->diffForHumans()}}
                                                      <div class="dwqa-comment-actions" style="float:right;">
                                                        @if(Auth::user() && Auth::user()->id == $reply->userid || Auth::user() && Auth::user()->isFounder() || Auth::user() && Auth::user()->isStaff())
                                                        <a href="javascript:void(0)" class="delete-reply" id="{{$reply->id}}" data-toggle="tooltip" title="Delete" style="margin-right:5px;color:#2196F3;"><i class="fa fa-trash"></i></a>
                                                        <a href="/reply/{{$reply->id}}/edit" data-toggle="tooltip" title="Edit" style="color:#2196F3;"><i class="fa fa-pencil"></i></a>
                                                        @endif
                                                      </div>
                                                  </div>
                                                  <p>{!!$reply->reply!!}</p>
                                              </div>
                                            @endforeach
                                              <!-- #comment-## -->
                                          </div>
                                      </div>
                                  </div>
                                @endif

                                @foreach($answers as $answer)
                                  <?php
                                    $u = DB::table('users')->where('id', $answer->userid)->first();
                                    $replies = DB::table('replies')->where('answerid', $answer->id)->get();
                                   ?>

                                  <div class="dwqa-answer-item @if($answer->tag == 1) dwqa-best-answer @endif " id="answer-{{$answer->id}}" style="margin-top:40px;">
                                      <div class="dwqa-answer-vote">
                                          <span class="dwqa-vote-count">{{$answer->votes}}</span>
                                          <a class="dwqa-vote dwqa-vote-up" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'up'], ['type', '=', 'answer'], ['questionid', '=', $answer->id]])->exists()) style="border-color:transparent transparent #71c668;" @endif href="/answer/{{$answer->id}}/upvote">Vote Up</a>
                                          <a class="dwqa-vote dwqa-vote-down" @if(Auth::user() && DB::table('votes')->where([['userid', '=', Auth::user()->id], ['vote_type', '=', 'down'], ['type', '=', 'answer'], ['questionid', '=', $answer->id]])->exists()) style="border-color:#f26c4f transparent transparent;" @endif href="/answer/{{$answer->id}}/downvote">Vote Down</a>
                                      </div>
                                        <span class="dwqa-pick-best-answer">Best Answer</span>
                                      <div class="dwqa-answer-meta" style="width:100%;">
                                          <span><a style="color:#2196F3;" href="/user/{{$u->username}}">
                                            @if($u->avatar != "default.jpg")
                                            <img alt="authors" style="width:54px;" src="/uploads/avatars/{{$u->id}}/{{$u->avatar }}"/>
                                            @else
                                            <img alt="authors" style="width:54px;" src="/assets/img/default.png"/>
                                            @endif
                                            @if(\App\User::where('id', $u->id)->first()->isStaff() || \App\User::where('id', $u->id)->first()->isFounder())
                                              <span class="label label-success">Staff</span>
                                            @endif
                                            {{$u->username}}
                                          </a>
                                          answered {{$answer->created_at->diffForHumans()}}
                                        </span>
                                        <span class="dwqa-answer-actions" style="float:right;">
                                          <a href="/reply/{{$answer->id}}" class="reply-answer" id="{{$answer->id}}" data-toggle="tooltip" title="Reply" style="margin-right:5px;color:#2196F3;"><i class="fa fa-reply"></i></a>
                                          @if(Auth::user() && Auth::user()->id == $answer->userid || Auth::user() && Auth::user()->isFounder() || Auth::user() && Auth::user()->isStaff())
                                          <a href="javascript:void(0)" class="delete-answer" id="{{$answer->id}}" data-toggle="tooltip" title="Delete" style="margin-right:5px;color:#2196F3;"><i class="fa fa-trash"></i></a>
                                          <a href="/answer/{{$answer->id}}/edit" data-toggle="tooltip" title="Edit" style="color:#2196F3;"><i class="fa fa-pencil"></i></a>
                                          @if(Auth::user()->isStaff() || Auth::user()->isFounder() || Auth::user()->id == $question->userid)
                                            <a href="/answer/{{$answer->id}}/best" id="{{$answer->id}}" data-toggle="tooltip" title="Best Answer" style="margin-right:5px;color:#2196F3;"><i class="fa fa-check"></i></a>
                                          @endif
                                          @endif
                                        </span>
                                      </div>
                                      <div class="dwqa-answer-content">
                                          <p>{!!$answer->description!!}</p>
                                      </div>

                                      <div class="dwqa-comments">
                                          <div class="dwqa-comments-list">
                                            @foreach($replies as $reply)
                                              <?php
                                                $rep = DB::table('users')->where('id', $reply->userid)->first();
                                              ?>
                                              <div class="dwqa-comment">
                                                  <div class="dwqa-comment-meta">
                                                      <a href="/user/{{$rep->username}}">
                                                        @if($rep->avatar != "default.jpg")
                                                          <img alt="author" style="width:24px!important;height:24px!important;" src="/uploads/avatars/{{$rep->id}}/{{$rep->avatar}}" class="avatar avatar-16 photo" />
                                                        @else
                                                          <img alt="author" style="width:24px!important;height:24px!important;" src="/assets/img/default.png" class="avatar avatar-16 photo" />
                                                        @endif
                                                        @if(\App\User::where('id', $rep->id)->first()->isStaff() || \App\User::where('id', $rep->id)->first()->isFounder())
                                                          <span class="label label-success">Staff</span>
                                                        @endif
                                                      {{$rep->username}}
                                                      </a>
                                                      replied {{\Carbon\Carbon::parse($reply->created_at)->diffForHumans()}}
                                                      <div class="dwqa-comment-actions" style="float:right;">
                                                        @if(Auth::user() && Auth::user()->id == $reply->userid)
                                                        <a href="javascript:void(0)" class="delete-reply" id="{{$reply->id}}" data-toggle="tooltip" title="Delete" style="margin-right:5px;color:#2196F3;"><i class="fa fa-trash"></i></a>
                                                        <a href="/reply/{{$reply->id}}/edit" data-toggle="tooltip" title="Edit" style="color:#2196F3;"><i class="fa fa-pencil"></i></a>
                                                        @endif
                                                      </div>
                                                  </div>
                                                  <p>{!!$reply->reply!!}</p>
                                              </div>
                                            @endforeach
                                              <!-- #comment-## -->
                                          </div>
                                      </div>
                                  </div>
                                @endforeach
                                  <!-- End Answer item -->
                              </div>
                          </div>
                          @if(Auth::user())
                          <div class="post_answer" style="margin-top:100px;">
                            <div class="col-md-12">
                              <div class="themeix-section-title text-center">
                                  <h2>Answer question</h2>
                              </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                              <div class="contact-form" style="margin-top:15px;">
                              <form action="/question/{{$question->id}}/answer" method="post">
                                {{csrf_field()}}
                                <div class="form-group">
                                  <label for="sel1">Answer *</label>
                                  <textarea name="message" id="message" cols="30" rows="10" class="form-control" placeholder="Write your answer here."></textarea>
                                </div>
                                <button type="submit" class="themeix-btn hover-bg">Answer Question</button>
                              </form>
                            </div>
                            </div>
                          </div>
                          </div>
                        @endif
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Answers Area -->
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


@section('js')

  <script>
  tinymce.init({
    selector:'textarea',
    plugins: 'link image code',
    branding: false,
    menubar: false


    });


    $(document).ready(function(){


    $('[data-toggle="tooltip"]').tooltip();

    $("#delete-question").click(function(){
        swal({
          title: "Are you sure you want to delete this question?",
          text: "You will not be able to undo this action.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(){
            window.location.replace("/question/{{$question->id}}/delete");
        });
            });

            $(".delete-answer").click(function(){
                var id = $(this).attr('id');
                deleteAnswer(id);
            });

            $(".delete-reply").click(function(){
                var id = $(this).attr('id');
                deleteReply(id);
            });

            function deleteAnswer(id)
            {
              swal({
                title: "Are you sure you want to delete this answer?",
                text: "You will not be able to undo this action.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
              },
              function(){
                  window.location.replace("/answer/"+id+"/delete");
              });
            }

            function deleteReply(id)
            {
              swal({
                title: "Are you sure you want to delete this reply?",
                text: "You will not be able to undo this action.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
              },
              function(){
                  window.location.replace("/reply/"+id+"/delete");
              });
            }


    });
  </script>


@endsection
