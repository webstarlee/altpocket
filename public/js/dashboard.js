$("textarea").atwho({
                at: "@", limit: 5,
       callbacks: {
                        remoteFilter: function (t, e) {
                            t.length <= 2 || $.getJSON("/api/users", {q: t}, function (t) {
                                e(t)
                            })
                        }
                    }
                }
      );
$("textarea").atwho({
        at: "::", limit: 5, displayTpl: "<li>${name} <img src='/icons/32x32/${symbol}.png' style='width:20px;'/>'", insertTpl: '::${symbol}::',
     callbacks: {
                      remoteFilter: function (t, e) {
                          t.length <= 2 || $.getJSON("/api/coins", {q: t}, function (t) {
                              e(t)
                          })
                      }
                  }
              }
            );


$('body').on("click", ".edit-post", function(){
    $.ajax({
        dataType: "json",
        url: '/status/get/'+$(this).attr('id'),
        success: function(data){
        $("#edit-status-field").val(data["status"]);
        $("#edit-status-form").attr('action', '/status/edit/'+data["id"]);
    }
    });

});

$('body').on("click", ".edit-comment", function(){
  $.ajax({
      dataType: "json",
      url: '/statuscomment/get/'+$(this).attr('id'),
      success: function(data){
      $("#edit-comment-field").val(data["comment"]);
      $("#edit-comment-form").attr('action', '/statuscomment/edit/'+data["id"]);
  }
  });
});

$('body').on("click", ".more-comments", function(){
  id = $(this).attr('id');

    $("."+id).css('display',' block');
    $(this).hide();

});

$('body').on("click", ".like-status", function(){
  status = $(this).attr('status');

    if($(this).hasClass("liked"))
    {
      $(this).children("span").text(parseInt($(this).children().text())-1);
      $(this).removeClass("liked");
    } else {
      $(this).children("span").text(parseInt($(this).children().text())+1);
      $(this).addClass("liked");
    }



  $.ajax({
    url: "/status/"+status+"/like",
  }).done(function() {
  });
});

$('body').on("click", ".like-comment", function(){
  status = $(this).attr('status');

    if($(this).hasClass("liked"))
    {
      $(this).children("span").text(parseInt($(this).children().text())-1);
      $(this).removeClass("liked");
    } else {
      $(this).children("span").text(parseInt($(this).children().text())+1);
      $(this).addClass("liked");
    }



  $.ajax({
    url: "/comment/"+status+"/like",
  }).done(function() {
  });
});

$(".more-referral").click(function(){

  if($(".referral-tab").hasClass( "hide" )){
    console.log("hey");
    $(".referral-tab").removeClass('hide');
    $(".referral-tab").css('display', 'block');
    $(".referral-option").removeClass('fa-plus');
    $(".referral-option").addClass('fa-minus');
  } else {
    $(".referral-tab").css('display', 'none');
    $(".referral-tab").addClass('hide');
    $(".referral-option").removeClass('fa-minus');
    $(".referral-option").addClass('fa-plus');
  }

});


$(".change-tracking").click(function(){

  $("#edit_tracking").modal('toggle');

  $(".tracking-form").attr('action', '/track/coin/'+$(this).attr('id'));

});

$("#add-image").click(function(){
  $("#add_image").modal('toggle');
});

$("#add-image-button").click(function(){
  image_url = $("#image_url").val();

  $("textarea#new-post").val($("textarea#new-post").val() + "[img]" + image_url + "[/img]");
  $("#image_url").val("");
  $("#add_image").modal('toggle');
});
