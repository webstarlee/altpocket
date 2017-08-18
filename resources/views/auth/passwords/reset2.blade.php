    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <title>Altpocket.io - Reset Password</title>
  <link rel="stylesheet" href="/assets/css/vendor.bundle.css">
  <link rel="stylesheet" href="/assets/css/app.bundle.css">
  <link rel="stylesheet" href="/assets/css/theme-a.css">
</head>
<body id="auth_wrapper" >
  <div id="login_wrapper" style="margin-top:150px!important;">
    <div class="logo">
      <img src="/img/logo.gif" alt="logo" class="logo-img" style="margin-top:5px;">
    </div>
    <div id="login_content">
      <h1 class="login-title">
        Change your password
      </h1>
      <div class="login-body">
                @if (count($errors))
                        @foreach($errors->all() as $error)
                            <div class="alert alert-danger" role="alert">
                                                <strong>Oh snap!</strong> {{$error}}
                                              </div>
                        @endforeach
                @endif
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif          
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
             {{ csrf_field() }}
                        
                        
                        
          <div class="form-group label-floating is-empty">
            <label class="control-label">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required>
          </div>
                        
          <div class="form-group label-floating is-empty">
            <label class="control-label">Password</label>
            <input id="password" type="password" class="form-control" name="password" required>
          </div>                        
                        
          <div class="form-group label-floating is-empty">
            <label class="control-label">Confirm Password</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
          </div>        
                        
                        
                        
          <button type="submit" class="btn btn-info btn-block m-t-40">Change Password</button>
        </form>
      </div>
    </div>
  </div>
  <script src="/assets/js/vendor.bundle.js"></script>
  <script src="/assets/js/app.bundle.js"></script>
</body>
</html>
