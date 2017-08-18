@extends('layouts.support')

@section('title', 'Edit your question')

@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>Reply to answer</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Reply answer</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->

<div class="our-community-area themeix-ptb" style="background-color:#eef4f9;">
  <div class="row" style="margin-right:0px!important;">
  <div class="col-md-12">
    <div class="themeix-section-title text-center">
        <h2>Reply to answer</h2>
        <p>Here you can reply to an answer.</p>
    </div>
  </div>
</div>

<div class="answers-area themeix-ptb" style="padding-top:0px!important;padding-bottom:0px!important;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="answers-wrapper">
                    <div class="dwqa-single-question">
<div class="dwqa-answers" style="margin-top:20px">
    <div class="dwqa-answers-list">
        <?php
          $u = DB::table('users')->where('id', $answer->userid)->first();
         ?>

        <div class="dwqa-answer-item @if($answer->tag == "best") dwqa-best-answer @endif " id="answer-{{$answer->id}}" style="border-bottom:1px solid #ddd">
            <div class="dwqa-answer-meta" style="width:100%;">
                <span><a style="color:#2196F3;" href="/user/{{$u->username}}">
                  @if($u->avatar != "default.jpg")
                  <img alt="authors" style="width:54px;" src="/uploads/avatars/{{$u->id}}/{{$u->avatar }}"/>
                  @else
                  <img alt="authors" style="width:54px;" src="/assets/img/default.png"/>
                  @endif
                  {{$u->username}}
                </a>
                answered {{$answer->created_at->diffForHumans()}}
              </span>
            </div>
            <div class="dwqa-answer-content">
                <p>{!!$answer->description!!}</p>
            </div>

        </div>
        <!-- End Answer item -->
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


  <div class="row" style="margin-right:0px!important;">
    <div class="col-md-4 col-md-offset-4">
      <div class="contact-form">
        <form action="/reply/{{$reply->id}}/edit" role="form" method="post">
         {{ csrf_field() }}
          <div class="form-group">
            <label for="sel1">Reply *</label>
            <textarea name="message" id="message" cols="30" rows="10" class="form-control" placeholder="Write your reply here.">{{$reply->reply}}</textarea>
          </div>
          <button type="submit" class="themeix-btn hover-bg">Edit reply to {{$u->username}}</button>
        </form>
      </div>
    </div>
  </div>

</div>

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

  <script>tinymce.init({
    selector:'textarea',
    plugins: 'link image code',
    branding: false,
    menubar: false


    });
  </script>


@endsection
