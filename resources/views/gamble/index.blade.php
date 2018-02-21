@extends('layouts.app2')

@section('title')
All gambles
@endsection

@section('css')
<link href="/css/blocks.css" rel="stylesheet">
@endsection


@section('content')
    <div id="content_wrapper" class="">
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content" style="text-align:center;">
                    <div class="row">
                  <div class="col-md-6 col-md-offset-3">
                  @foreach(\App\Bet::get() as $bet)
                    <div class="ui-block">
  										<div class="ui-block-content">
                        <a href="javascript:void(0)" id="{{$bet->id}}" data-toggle="modal" class="btn btn-sm bg-blue flipcoin-btn" style="margin-top:20px!important;">Join Coin Flip ({{$bet->amount}} ETH)<div class="ripple-container"></div></a>
    									</div>
  									</div>
                  @endforeach
                  </div>
                    </div>
                </div>
            </div>
        </div>
</div>


@endsection


@section('js')
  <script>
    $(document).delegate('.flipcoin-btn', 'click', function() {
        // When player 2 clicks the button
        SubmitFlipCoin($(this).attr('id'), CreateClientKey());
        //var test = CreateClientKey();

        //console.log(test);
    });

    function CreateClientKey()
{
    var outStr = "", newStr;
    while(outStr.length < 20)
    {
        newStr = Math.random().toString(36).slice(2);
        outStr += newStr.slice(0, Math.min(newStr.length, (20 - outStr.length)));
    }
    console.log("Client string created: "+outStr.toUpperCase());
    return outStr.toUpperCase();
};

function SubmitFlipCoin(id, client)
{
    StopMoneyUpdate = 1;

    $.ajax({
        method: 'POST',
        url: '/gamble/join',
        data: {id: id, clientstr: client, _token: "{{csrf_token()}}"},
        dataType: 'JSON',
    })
    .done(function(result) {
        if(result.hasOwnProperty("error")) {
            console.log(result.error);
        }
        console.log(result.body);
    })
    .fail(function(error) {
        console.log(error);
    });
}
  </script>
@endsection
