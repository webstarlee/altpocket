@extends('layouts.support')

@section('title', 'Edit your question')

@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>Edit answer</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Edit answer</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->

<div class="our-community-area themeix-ptb" style="background-color:#eef4f9;">
  <div class="col-md-12">
    <div class="themeix-section-title text-center">
        <h2>Edit an answer</h2>
        <p>Here you can edit your answer.</p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="contact-form">
        <form action="/answer/{{$answer->id}}/edit" role="form" method="post">
         {{ csrf_field() }}
          <div class="form-group">
            <label for="sel1">Answer *</label>
            <textarea name="message" id="message" cols="30" rows="10" class="form-control" placeholder="Write your description here.">{{$answer->description}}</textarea>
          </div>
          <button type="submit" class="themeix-btn hover-bg">Edit Answer</button>
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
