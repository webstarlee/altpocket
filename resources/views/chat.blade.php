<!doctype html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <title>Realtime Chat Widget using Pusher</title>

    <link rel="stylesheet/less" type="text/css" href="lib/twitter-bootstrap/lib/bootstrap.less">
    <script src="lib/less/less-1.1.5.min.js"></script>

    <link href="/assets/css/styles.css" rel="stylesheet" />
    <link href="/assets/css/pusher-chat-widget.css" rel="stylesheet" />

    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="/assets/js/PusherChatWidget.js"></script>
    <script>
      $(function() {
        var pusher = new Pusher("PUSHER_APP_KEY")
        var chatWidget = new PusherChatWidget(pusher, {
          appendTo: "#pusher_chat_widget"
        });
      });
    </script>
  </head>
  <body>

    <div class="container">

      <div class="topbar">
        <div class="fill">
          <div class="container">
            <a class="brand" href="/">Realtime Chat Widget using Pusher</a>
          </div>
        </div>
      </div>

      <div class="hero-unit">
        <h1>Realtime Chat Widget</h1>
        <p>This page demonstrates a Realtime Chat widget built using <a href="http://pusher.com">Pusher</a>. This functionality can easily be added to any PHP, Ruby or Node.js application.</p>
        <p>The widget can easily be added to any page and the channel name for the chat is generated based on the URL of the page. The chat message events are triggered via PHP, Ruby or Node.js using <a href="http://pusher.com/docs/server_libraries">Pusher libraries</a>.</p>
        <p><a href="https://github.com/pusher/pusher-realtime-chat-widget" class="btn primary large" target="_blank">Get the code &raquo;</a></p>
        <p><small>Other backend technologies will be added in the future. Feel free to <a href="https://github.com/pusher/pusher-realtime-chat-widget">fork</a> and try it for yourself.</small></p>
      </div>

      <section class="realtime-chat">

        <div class="page-header">
          <h1>Realtime Chat Widget Example</h1>
        </div>

        <div class="row">

          <div class="span5">
            <p>
              <p><strong>Why don't you provide your email address</strong> so your Gravatar can be looked up and used in the Chat Widget</p>
            </p>
          </div>
          <div class="span5" id="pusher_chat_widget">
          </div>

        </div>

      </section>

      <footer>
        <p></p>
      </footer>

    </div> <!-- /container -->

    <a href="https://github.com/pusher/pusher-realtime-chat-widget"><img style="position: absolute; top: 0; right: 0; border: 0; z-index: 10000;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>

  </body>
</html>
