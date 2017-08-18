@extends('layouts.support')


@section('title', 'Ask a question')

@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>Ask a question</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Ask</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->

<div class="our-community-area themeix-ptb" style="background-color:#eef4f9;">
  <div class="col-md-12">
    <div class="themeix-section-title text-center">
        <h2>Ask a question</h2>
        <p>Here you can post a question which can be answered by the community or the Altpocket team.</p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="contact-form">
        <form action="/ask/post" role="form" method="post">
         {{ csrf_field() }}
          <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Title *" required="">
          </div>
          <div class="form-group">
            <label for="sel1">Category *</label>
            <select class="form-control" id="sel1" name="category" required="">
              <option value="General">General</option>
              <option value="Technical">Technical</option>
              <option value="Bug">Bug</option>
            </select>
          </div>

          <div class="form-group">
            <label for="sel1">Description *</label>
            <textarea name="message" id="message" cols="30" rows="10" class="form-control" placeholder="Write your description here."></textarea>
          </div>
          <button type="submit" class="themeix-btn hover-bg">Ask Question</button>
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
