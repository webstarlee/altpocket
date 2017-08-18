@extends('layouts.new')

@section('title', 'Make Post')


@section('content')
  <!--start banner Area-->
  <section class="banner-area parallax-bg" data-stellar-background-ratio="0.001" style="background:none;">
      <div class="container">
          <div class="banner-content">
              <h2>Create Blog Post</h2>
          </div>
      </div>
  </section>
  <!--End banner area-->

  <!-- start single-blog-area-->
  <section class="blog-area single-blog-area">
      <div class="container">
          <div class="row">
                <form action="/blog/post" method="POST" enctype="multipart/form-data" class="contact-form">
                  {{ csrf_field() }}
                  <div class="row">
                    <div class="col-md-12 col-md-offset-3">
                    <input type="text" class="form-control" placeholder="Post Title" name="title"><br><br><br>
                  </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <textarea class="form-control" placeholder="message" name="message"></textarea>
                    </div>
                  </div>
                  <div class="row" style="padding:50px;">
                    <div class="col-md-12">
                     <div class="slim">
                         <input type="file"/>
                     </div>
                   </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <input type="submit" value="Send Message" class="btn sub_btn sub_btn-two">
                  </div>
                </div>
                </form>
          </div>
      </div>
  </section>
  <!-- End single-blog-area-->

@endsection

@section('js')
<script>
tinymce.init({
  selector:'textarea',
  plugins: 'link image code',
  branding: false,
  menubar: false


  });
</script>
@endsection
