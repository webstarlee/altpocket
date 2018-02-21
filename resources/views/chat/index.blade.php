@extends('layouts.app2')


@section('css')
<link rel="stylesheet" type="text/css" href="/version3/css/theme-styles.css">
<link rel="stylesheet" type="text/css" href="/version3/css/blocks.css?version=1.9">
<link rel="stylesheet" type="text/css" href="/version3/css/jquery.mCustomScrollbar.min.css">


@if(Auth::user()->theme == "dark")
<style>
.fixed-sidebar-right {
      background-color: #232323;
      border-left: 1px solid #2b2b2b;
}
.search-friend {
      box-shadow: 0 -50px 45px -3px rgba(19, 19, 19, 0.7);
      background-color: #292929
}
.unread {
  position: absolute;
  margin-top: 25px;
  z-index: 5000;
  padding: .2em .6em .2em;
  margin-left: 1px;
}
.olympus-chat .olympus-chat-title {
    text-transform: uppercase;
    color: #fff;
    margin-right: 40px;
    margin-bottom: 0;
}
h6, .h6 {
    font-size: 0.875rem!important;
}
.sidebar--large .olympus-chat {
    padding: 30px 15px 15px 50px;
}

.olympus-chat {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 25px 22px;
    background-color: #7c5ac2;
    fill: #fff;
    height: 70px;
}
.left-msg {
    float: right;
    padding-left: 0;
    padding-right: 10px    }
.right {
    float:right!important;
}
</style>
@else
<style>
.olympus-chat {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 25px 22px;
    background-color: #7c5ac2;
    fill: #fff;
    height: 70px;
}
.unread {
  position: absolute;
  margin-top: 25px;
  z-index: 5000;
  padding: .2em .6em .2em;
  margin-left: 1px;
}
.left-msg {
    float: right;
    padding-left: 0;
    padding-right: 10px    }
.right {
    float:right!important;
}
</style>

@endif
<style>
.popup-chat .form-group {
    margin-bottom: 0!important;
    padding-bottom:0!important;
}
.form-group {
    position: relative!important;
    margin-bottom: 1.4rem!important;
}
.label-floating textarea.form-control {
    padding: 1.5rem 1.1rem .2rem!important;
}
.form-group.label-floating textarea {
    padding: 1.3rem 1.1rem .2rem!important;
}
.label-floating .form-control, .label-floating input, .label-floating select {
    padding: 1.3rem 1.1rem .4rem!important;
    line-height: 1.8!important;
}
.popup-chat textarea {
    min-height: 60px!important;
    height: 60px!important;
    transition: all .3s ease!important;
    border-radius: 0!important;
}
.form-group textarea {
    resize: none!important;
}
input, .form-control {
    color: #515365!important;
    line-height: inherit!important;
    font-size: .875rem!important;
}
select, input, .form-control {
    background-color: transparent!important;
}
.form-control {
    display: block!important;
    width: 100%!important;
    padding: 1.1rem 1.1rem!important;
    font-size: 0.812rem!important;
    line-height: 1.25!important;
    color: #495057!important;
    background-color: #fff!important;
    background-image: none!important;
    background-clip: padding-box!important;
    border: 1px solid #e6ecf5!important;
    border-radius: 0.25rem!important;
    transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s!important;
}
textarea {
    overflow: auto!important;
    resize: vertical!important;
    margin-bottom:0!important;
}
.form-group.label-static label.control-label, .form-group.label-placeholder label.control-label, .form-group.label-floating2 label.control-label {
    position: absolute!important;
    pointer-events: none!important;
    transition: 0.3s ease all!important;
}
.form-group.label-floating label.control-label, .form-group.label-placeholder label.control-label {
    top: 16px!important;
    font-size: 14px!important;
    line-height: 1.42857!important;
    left: 20px!important;
}
label.control-label {
    color: #888da8!important;
}
label {
    display: inline-block!important;
    margin-bottom: .5rem!important;
}
.form-group.label-static label.control-label, .form-group.label-floating.is-focused label.control-label, .form-group.label-floating:not(.is-empty) label.control-label, .form-group.has-bootstrap-select label.control-label {
    top: 10px!important;
    font-size: 11px!important;
    line-height: 1.07143!important;
}
.popup-chat textarea:focus {
  min-height: 100px!important; }
  .form-control:focus {
    color: #495057!important;
    background-color: transparent!important;
    border-color: #ffc6ba!important;
    outline: none!important;
}
.my-message {
    background-color: #4FC5EA!important;
    color: #fff;
}
.tooltip.fade {
    -webkit-transition: opacity .15s linear!important;
    -o-transition: opacity .15s linear!important;
    transition: opacity .15s linear!important;
}
.tooltip-inner {
  font-size:10px!important;
}
</style>
@endsection

@section('js')


@endsection

@section('content')

  <div id="content_wrapper" class="">
  <div id="header_wrapper" class="header-sm">
      <div class="container-fluid">
          <div class="row">
              <div class="col-xs-12">
                  <header id="header">
                      <h1>Chat Testing - Use Console</h1>
                  </header>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="fixed-sidebar right">
	<div class="fixed-sidebar-right sidebar--small" id="sidebar-right">
    @php
      //Prepare chat

      //Those you follow and they follow back for now!
      $chatters = Auth::user()->followings()->get();


    @endphp
		<div class="mCustomScrollbar" data-mcs-theme="dark">
			<ul class="chat-users">
        @foreach($chatters as $user)
          @if($user->isFollowing(Auth::user()))
  				<li class="inline-items js-chat-open" data-user="{{$user->id}}" data-username="{{$user->username}}">
  					<div class="author-thumb">
              @if($user->avatar != "default.jpg")
  						<img alt="author" src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" class="avatar" style="width:32px;">
              @else
  						<img alt="author" src="/assets/img/default.png" class="avatar" style="width:32px;">
              @endif

              @if($user->isOnline())
				        <span user="{{$user->id}}" class="icon-status online"></span>
              @else
                <span user="{{$user->id}}" class="icon-status disconected"></span>
              @endif
  					</div>
  				</li>
        @endif
      @endforeach

			</ul>
		</div>

		<div class="search-friend inline-items">
			<a href="#" class="js-sidebar-open">
				<svg class="olymp-menu-icon"><use xlink:href="/version3/icons/icons.svg#olymp-menu-icon"></use></svg>
			</a>
		</div>

		<a href="#" class="olympus-chat inline-items">
			<svg class="olymp-chat---messages-icon"><use xlink:href="/version3/icons/icons.svg#olymp-chat---messages-icon"></use></svg>
		</a>

	</div>

  <div class="fixed-sidebar-right sidebar--large" id="sidebar-right-1">

		<div class="mCustomScrollbar" data-mcs-theme="dark">

			<div class="ui-block-title ui-block-title-small">
				<a href="#" class="title">Friends</a>
				<a href="/settings">Settings</a>
			</div>

			<ul class="chat-users">
        @foreach($chatters as $user)
          @if($user->isFollowing(Auth::user()))
  				<li class="inline-items js-chat-open" data-user-large="{{$user->id}}" data-username="{{$user->username}}">

  					<div class="author-thumb">
              @if($user->avatar != "default.jpg")
  						<img alt="author" src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" class="avatar" style="width:32px;">
              @else
  						<img alt="author" src="/assets/img/default.png" class="avatar" style="width:32px;">
              @endif
              @if($user->isOnline())
				        <span user="{{$user->id}}" class="icon-status online"></span>
              @else
                <span user="{{$user->id}}" class="icon-status disconected"></span>
              @endif
  					</div>

  					<div class="author-status">
  						<a href="/user/{{$user->username}}" class="h6 author-name">{{$user->username}}</a>
              @if($user->isOnline())
    						<span data-user-status="{{$user->id}}" class="status">ONLINE</span>
              @else
                <span data-user-status="{{$user->id}}" class="status">OFFLINE</span>
              @endif
  					</div>

  					<div class="more"><svg class="olymp-three-dots-icon"><use xlink:href="/version3/icons/icons.svg#olymp-three-dots-icon"></use></svg>

  						<ul class="more-icons">
  							<li>
  								<svg data-toggle="tooltip" data-original-title="START CONVERSATION" class="olymp-comments-post-icon"><use xlink:href="/version3/icons/icons.svg#olymp-comments-post-icon"></use></svg>
  							</li>

                @if(Auth::user()->hasBlocked($user->id))
    							<li style="float:right" class="block-user" user-id="{{$user->id}}">
    								<svg data-toggle="tooltip" data-placement="top" data-original-title="UNBLOCK FROM CHAT" class="olymp-block-from-chat-icon"><use xlink:href="/version3/icons/icons.svg#olymp-block-from-chat-icon"></use></svg>
    							</li>
                @else
                  <li style="float:right" class="block-user" user-id="{{$user->id}}">
    								<svg data-toggle="tooltip" data-placement="top" data-original-title="BLOCK FROM CHAT" class="olymp-block-from-chat-icon"><use xlink:href="/version3/icons/icons.svg#olymp-block-from-chat-icon"></use></svg>
    							</li>
                @endif
  						</ul>

  					</div>

  				</li>
        @endif
        @endforeach

			</ul>

		</div>


		<a href="#" class="olympus-chat inline-items">

			<h6 class="olympus-chat-title" style="font-size:15px;margin-top:-12px;">ALTPOCKET CHAT</h6>
			<svg class="olymp-chat---messages-icon" style="margin-top:-5px;"><use xlink:href="/version3/icons/icons.svg#olymp-chat---messages-icon"></use></svg>
		</a>

	</div>
</div>



<div class="ui-block popup-chat popup-chat-responsive">
	<div class="ui-block-title" style="background-color:#4FC5EA">
		<span class="icon-status online" style="margin-bottom:3px;"></span>
		<h6 class="title" >Chat</h6>
		<div class="more">
			<svg class="olymp-little-delete js-chat-close"><use xlink:href="/version3/icons/icons.svg#olymp-little-delete"></use></svg>
		</div>
	</div>
	<div class="mCustomScrollbar">
		<ul class="notification-list chat-message chat-message-field">

		</ul>
	</div>
  <div class="typing" style="display:none;">
<ul style="margin-bottom: 0;">
    <li style="margin-bottom: 40px;
    overflow: hidden;
    padding: 9px 25px;
    border-bottom: none;
    display: block;
    position: relative;
    transition: all .3s ease;"><div class="author-thumb"><img id="loaderavatar" width="32px" height="32px" src="https://altpocket.io/assets/img/default.png" alt="1636" class="mCS_img_loaded"></div><div class="notification-event" style="    max-width: 90%;
    margin-bottom: 0;
    display: inline-block;"><span class="chat-message-item"><img style="width:20%" src="/sounds/typing.svg"/></span></div></li>
</ul>
</div>

  <form>
		<div class="form-group label-floating is-empty">
			<label class="control-label">Press enter to post...</label>
			<textarea class="form-control msg-input"></textarea>
			<div class="add-options-message"  style="display:none;">
				<a href="#" class="options-message">
					<svg class="olymp-computer-icon"><use xlink:href="/version3/icons/icons.svg#olymp-computer-icon"></use></svg>
				</a>
				<div class="options-message smile-block" style="display:none;">

					<svg class="olymp-happy-sticker-icon"><use xlink:href="/version3/icons/icons.svg#olymp-happy-sticker-icon"></use></svg>

					<ul class="more-dropdown more-with-triangle triangle-bottom-right">
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat1.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat2.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat3.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat4.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat5.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat6.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat7.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat8.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat9.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat10.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat11.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat12.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat13.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat14.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat15.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat16.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat17.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat18.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat19.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat20.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat21.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat22.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat23.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat24.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat25.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat26.png" alt="icon">
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/version3/img/icon-chat27.png" alt="icon">
							</a>
						</li>
					</ul>
				</div>
			</div>
			 </div>

	</form>


</div>



@endsection


@section('javascript')
<script src="/version3/js/theme-plugins.js?version=1.1"></script>

<script>
var $window = $(window),
  $document = $(document),
  $body = $('body'),
  swipers = {},
  $progress_bar = $('.skills-item'),
  $sidebar = $('.fixed-sidebar'),
  $header = $('#header--standard'),
  $counter = $('.counter');

// Toggle aside panels
$(".js-sidebar-open").on('click', function () {
      $(this).toggleClass('active');
      $(this).closest($sidebar).toggleClass('open');
      return false;
  } );

// Close on "Esc" click
  $window.keydown(function (eventObject) {
      if (eventObject.which == 27 && $sidebar.is(':visible')) {
          $sidebar.removeClass('open');
      }
  });

  // Close on click outside elements.
  $document.on('click', function (event) {
      if (!$(event.target).closest($sidebar).length && $sidebar.is(':visible')) {
          $sidebar.removeClass('open');
      }
  });


$(".block-user").click(function(){
  $.ajax({
			dataType: "json",
			url: '/block/user/'+$(this).attr('user-id')
});

if($(this).find('svg').attr('data-original-title') == "BLOCK FROM CHAT")
{
  $(this).find('svg').attr('data-original-title', 'UNBLOCK FROM CHAT');
} else {
  $(this).find('svg').attr('data-original-title', 'BLOCK FROM CHAT');
}
});


// actual chatting lmao
$(".js-chat-open").on('click', function() {
     if($(this).data("user")){
     openChat( $(this).data("user"), $(this).data("username") );
     } else if($(this).data("user-large")){
     openChat( $(this).data("user-large"), $(this).data("username") );
     }
     return false
 });

 $(".js-chat-close").on('click', function() {
      $('.popup-chat-responsive').toggleClass('open-chat');
  });

 var scrollpage = 1;
 var currentuser = "";

function current()
{
  if (open()){
      return $(".popup-chat").data("dest");
  }else{
      return undefined
  }
}

function open()
{
  return $(".popup-chat").hasClass("open-chat");
}



 function openChat(user, username)
 {
   //this handled unread symbol
   if($("li[data-user='"+user+"']").length)
   {
     if(($("li[data-user='"+user+"']").hasClass('unread-message')))
     {
       $("li[data-user='"+user+"']").removeClass('unread-message');
       $("li[data-user='"+user+"']").children(":first").remove();
     }
     if(($("li[data-user-large='"+user+"']").hasClass('unread-message')))
     {
       $("li[data-user-large='"+user+"']").removeClass('unread-message');
       $("li[data-user-large='"+user+"']").children(":first").remove();
     }
   }
     $('.chat-users').children('li').each(function() {
      if( user==$(this).data('user') ) {
          status = $(this).eq(0).children("div").children("span").attr("class").split(" ")[1];
              $(".popup-chat-responsive").eq(0).children("div").eq(0).children("span").removeClass().addClass("icon-status "+status);
              $(".popup-chat-responsive").eq(0).children("div").eq(0).children("span").attr("title",status)
      }
  });
   if(!($('.popup-chat-responsive').hasClass('open-chat')))
   {
     $('.popup-chat-responsive').toggleClass('open-chat');
   }
   $(".title").text("Chatting with "+ username);
   $(".notification-list ").text("");
   $(".msg-input").data("dest",user);
   $(".popup-chat").data("dest",user);
   $(".typing").hide();

   $('.chat-users').children('li').each(function() {
    if( user==$(this).data('user') ) {
        theavatar = $(this).children("div").children("img").attr("src");
        $("#loaderavatar").attr('src', theavatar);
    }
    });

   $.ajax({
    url: "/get/messages/"+user+"/"+"{{Auth::user()->id}}"
}).done(function(r) {
    msgs = JSON.parse(r);
    avatar = msgs.avatar;
    for(i in msgs["messages"]["data"].reverse()) {
        if (i => 0) {
            pushMsg(msgs["messages"]["data"][i].sender, msgs["messages"]["data"][i].message, msgs["messages"]["data"][i].created_at, false, avatar);
        }
        i++;

    }
    $(function () {
      $('[data-toggle=tooltip]').tooltip();
    });
 });

 scrollpage++;
 currentuser = user;
 }

 function loadMore(user, page)
 {
   $.ajax({
    url: "/get/messages/"+user+"/"+"{{Auth::user()->id}}?page="+page
}).done(function(r) {
    msgs = JSON.parse(r);
    avatar = msgs.avatar;
    var height = $(".chat-message").height();
    for(i in msgs["messages"]["data"]) {
        if (i => 0) {
            pushMsg(msgs["messages"]["data"][i].sender, msgs["messages"]["data"][i].message, msgs["messages"]["data"][i].created_at, true, avatar);
        }
        i++;

    }
      $(".popup-chat .mCustomScrollbar").mCustomScrollbar("scrollTo",$(".chat-message").height()-height,{scrollInertia:0});
      scrollpage++;
      $(function () {
        $('[data-toggle=tooltip]').tooltip();
      });
 });
 }

 //Push messages
 function pushMsg(from, msg, date, before, avatar)
 {
    message = msg;
    append(from, message, date, before, avatar);
    $(".popup-chat .mCustomScrollbar").mCustomScrollbar("scrollTo","-9999999999",{scrollInertia:0});
 }

function append(from, message, date, before, avatar)
{
  var me = "{{Auth::user()->id}}";
  var myavatar2 = "{{Auth::user()->avatar}}"

  if(avatar == "default.jpg")
  {
    avatar = "https://altpocket.io/assets/img/default.png";
  } else {
    avatar = "https://altpocket.io/uploads/avatars/"+from+"/"+avatar;
  }

  if(myavatar2 == "default.jpg")
  {
    myavatar = "https://altpocket.io/assets/img/default.png";
  } else {
    myavatar = "https://altpocket.io/uploads/avatars/"+from+"/"+myavatar2;
  }

  if(from == me)
  {
    msg = '<li><div class="author-thumb right"><img  width="32px" height="32px" src="'+myavatar+'" class="mCS_img_loaded"></div><div class="notification-event right" style="padding-right:10px!important;padding-left:0px!important;"> <span  class="chat-message-item my-message right">' + message + '</span><span style="width:100%" class="notification-date right"><time style="float:right;" class="entry-date updated" title="'+date+'">' + date+ '</time></span></div></li>'
  } else {
    msg = '<li><div class="author-thumb"><img  width="32px" height="32px" src="'+avatar+'" alt="'+from+'" class="mCS_img_loaded"></div><div class="notification-event"><span class="chat-message-item">' + message + '</span><span class="notification-date" style="width:100%"><time class="entry-date updated" title="'+date+'" style="float:left;">' + date + '</time></span></div></li>'
  }

  if(before) {
    $(".notification-list ").prepend(msg);
}else{
    $(".notification-list ").append(msg);
    $(".popup-chat .mCustomScrollbar").mCustomScrollbar("scrollTo","-9999999999",{scrollInertia:0});
}

}

  $(".popup-chat .mCustomScrollbar").mCustomScrollbar({
    callbacks:{
      onTotalScrollBack: function(){
        loadMore(currentuser, scrollpage);

      }
}
  });


  function convertTimestamp(timestamp) {
    var d = new Date(timestamp * 1000),
        hh = d.getHours(),
        h = hh,
        min = ('0' + d.getMinutes()).slice(-2),
        ampm = 'AM',
        time;
    if (hh > 12) {
        h = hh - 12;
        ampm = 'PM';
    } else if (hh === 12) {
        h = 12;
        ampm = 'PM';
    } else if (hh == 0) {
        h = 12;
    }
    time = h + ':' + min + ' ' + ampm;
    return time;
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


 var x=0;
$(".msg-input").keydown(function (e) {
     if($(this).val().replace(/^\s+|\s+$|\s+(?=\s)/g, "")!="") {
         if (e.which == 13 && !e.shiftKey) {
           e.preventDefault();
             if($(this).data("dest")!=""){
                 //socket.emit('message', {"to":$(this).data("dest"),"msg":$(".msg-input").val() });

                 $.ajax({
                    type: "POST",
                    url: '/chat/send/',
                    data: {to: $(this).data("dest"), message: $(".msg-input").val()}
                  });

                 $(this).val("");
             }
         } else if(e.which == 8) {
           Echo.private('App.User.'+$(this).data("dest")).whisper('typing',{id: '{{Auth::user()->id}}', chars: $(this).val().length});
         } else {
             x++;
             if(x==1) {
                 if ($(this).data("dest") != "") {
                    Echo.private('App.User.'+$(this).data("dest")).whisper('typing',{id: '{{Auth::user()->id}}', chars: $(this).val().length});
                    x=0;
                 }
             }
         }
     }
 });


$(".msg-input.textarea").change(function(){

if($(this).val().length == 0)
{
  console.log($(this).val().length == 0)
  Echo.private('App.User.'+$(this).data("dest")).whisper('typing',{id: '{{Auth::user()->id}}', chars: $(this).val().length});
}

});

</script>

@endsection
