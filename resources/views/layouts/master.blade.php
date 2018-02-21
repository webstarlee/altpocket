<!doctype html>
<html lang="en">
<head>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font: 13px Helvetica, Arial; }
  form { background: #000; padding: 3px; position: fixed; bottom: 0; width: 100%; }
  form input { border: 0; padding: 10px; width: 90%; margin-right: .5%; }
  form button { width: 9%; background: rgb(130, 224, 255); border: none; padding: 10px; }
  #messages { list-style-type: none; margin: 0; padding: 0; }
  #messages li { padding: 5px 10px; }
  #messages li:nth-child(odd) { background: #eee; }





  /*
   * Note that this is toastr v2.1.3, the "latest" version in url has no more maintenance,
   * please go to https://cdnjs.com/libraries/toastr.js and pick a certain version you want to use,
   * make sure you copy the url from the website since the url may change between versions.
   * */
  .toast-title {
    font-weight: bold;
  }
  .toast-message {
    -ms-word-wrap: break-word;
    word-wrap: break-word;
  }
  .toast-message a,
  .toast-message label {
    color: #FFFFFF;
  }
  .toast-message a:hover {
    color: #CCCCCC;
    text-decoration: none;
  }
  .toast-close-button {
    position: relative;
    right: -0.3em;
    top: -0.3em;
    float: right;
    font-size: 20px;
    font-weight: bold;
    color: #FFFFFF;
    -webkit-text-shadow: 0 1px 0 #ffffff;
    text-shadow: 0 1px 0 #ffffff;
    opacity: 0.8;
    -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80);
    filter: alpha(opacity=80);
    line-height: 1;
  }
  .toast-close-button:hover,
  .toast-close-button:focus {
    color: #000000;
    text-decoration: none;
    cursor: pointer;
    opacity: 0.4;
    -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=40);
    filter: alpha(opacity=40);
  }
  .rtl .toast-close-button {
    left: -0.3em;
    float: left;
    right: 0.3em;
  }
  /*Additional properties for button version
   iOS requires the button element instead of an anchor tag.
   If you want the anchor version, it requires `href="#"`.*/
  button.toast-close-button {
    padding: 0;
    cursor: pointer;
    background: transparent;
    border: 0;
    -webkit-appearance: none;
  }
  .toast-top-center {
    top: 0;
    right: 0;
    width: 100%;
  }
  .toast-bottom-center {
    bottom: 0;
    right: 0;
    width: 100%;
  }
  .toast-top-full-width {
    top: 0;
    right: 0;
    width: 100%;
  }
  .toast-bottom-full-width {
    bottom: 0;
    right: 0;
    width: 100%;
  }
  .toast-top-left {
    top: 12px;
    left: 12px;
  }
  .toast-top-right {
    top: 12px;
    right: 12px;
  }
  .toast-bottom-right {
    right: 12px;
    bottom: 12px;
  }
  .toast-bottom-left {
    bottom: 12px;
    left: 12px;
  }
  #toast-container {
    position: fixed;
    z-index: 999999;
    pointer-events: none;
    /*overrides*/
  }
  #toast-container * {
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
  }
  #toast-container > div {
    position: relative;
    pointer-events: auto;
    overflow: hidden;
    margin: 0 0 6px;
    padding: .4rem;
    width: 300px;
    background-position: 15px center;
    background-repeat: no-repeat;
    -moz-box-shadow: 0 0 12px #999999;
    -webkit-box-shadow: 0 0 12px #999999;
    box-shadow: 0 0 12px #999999;
    color: #FFFFFF;
    font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
  }
  #toast-container > div.rtl {
    direction: rtl;
    padding: 15px 50px 15px 15px;
    background-position: right 15px center;
  }
  #toast-container > div:hover {
    -moz-box-shadow: 0 0 12px #000000;
    -webkit-box-shadow: 0 0 12px #000000;
    box-shadow: 0 0 12px #000000;
    opacity: 1;
    -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=100);
    filter: alpha(opacity=100);
    cursor: pointer;
  }
  #toast-container.toast-top-center > div,
  #toast-container.toast-bottom-center > div {
    width: 300px;
    margin-left: auto;
    margin-right: auto;
  }
  #toast-container.toast-top-full-width > div,
  #toast-container.toast-bottom-full-width > div {
    width: 96%;
    margin-left: auto;
    margin-right: auto;
  }
  .toast {
    background-color: #030303;
  }
  .toast-success {
    background-color: rgba(50, 182, 67, 1);
    border: 1px solid #32b643;
  }
  .toast-error {
    background-color: #BD362F;
  }
  .toast-info {
    background-color: #2F96B4;
  }
  .toast-warning {
    background: rgba(255, 183, 0, 1);
    border-color: #ffb700;
  }
  .toast-progress {
    position: absolute;
    left: 0;
    bottom: 0;
    height: 4px;
    background-color: #000000;
    opacity: 0.4;
    -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=40);
    filter: alpha(opacity=40);
  }
  /*Responsive Design*/
  @media all and (max-width: 240px) {
    #toast-container > div {
      padding: 8px 8px 8px 50px;
      width: 11em;
    }
    #toast-container > div.rtl {
      padding: 8px 50px 8px 8px;
    }
    #toast-container .toast-close-button {
      right: -0.2em;
      top: -0.2em;
    }
    #toast-container .rtl .toast-close-button {
      left: -0.2em;
      right: 0.2em;
    }
  }
  @media all and (min-width: 241px) and (max-width: 480px) {
    #toast-container > div {
      padding: 8px 8px 8px 50px;
      width: 18em;
    }
    #toast-container > div.rtl {
      padding: 8px 50px 8px 8px;
    }
    #toast-container .toast-close-button {
      right: -0.2em;
      top: -0.2em;
    }
    #toast-container .rtl .toast-close-button {
      left: -0.2em;
      right: 0.2em;
    }
  }
  @media all and (min-width: 481px) and (max-width: 768px) {
    #toast-container > div {
      padding: 15px 15px 15px 50px;
      width: 25em;
    }
    #toast-container > div.rtl {
      padding: 15px 50px 15px 15px;
    }
  }
</style>
    <meta charset="UTF-8">
    <title>FiveOne Socket.io</title>
      <meta name="csrf-token" content="{{ csrf_token() }}" />

</head>
<body>
@yield('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@yield('footer')
</body>
</html>
