@extends('layouts.master')

@section('content')
  <ul id="messages"></ul>
  <form action="">
    <input id="m" autocomplete="off" /><button>Send</button>
  </form>
@stop

@section('footer')
<script src="{{ elixir('js/6001-9.js') }}"></script>
<script>
Echo.channel('test-channel')
    .listen(".Notification", (event) => {
    console.log(event);
});
Echo.channel('announcements')
    .listen(".Notification", (event) => {
    console.log(event);
});

Echo.channel('test-weoo')
.listen(".Notification", (event) => {
  toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "0",
    "hideDuration": "0",
    "timeOut": "0",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
    Command: toastr[event.category](event.value)

    if(event.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments")
    {
      location.reload();
    }
    if(event.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments/")
    {
      location.reload();
    }
        });

</script>
@stop
