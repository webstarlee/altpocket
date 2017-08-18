@if($investment->date_sold == null && $investment->market == "USDT")
<?php
  $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
  $multiplier = Auth::user()->getMultiplier();
  $btctotal = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_for) / $investment->btc_price_bought_usd;
  $btceach = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_at) / $investment->btc_price_bought_usd;
  $btcnow = (($investment->amount * $price)) / $btc;
  $previous = 1;
  $decimal1 = 2;
  $decimal2 = 2;
  if(Auth::user()->getCurrency() == "USD")
  {
    $previous = $investment->btc_price_bought_usd;
    $previousmultiplier = $investment->btc_price_bought_usd;

  } elseif(Auth::user()->getCurrency() == "BTC")
  {
    $previous = $investment->btc_price_bought_usd;
    $decimal1 = 5;
    $decimal2 = 9;
    $previousmultiplier = 1;
  } else {
    $previous = $investment->btc_price_bought_usd;
    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
  }

 ?>
 <figure class="col-xs-12 col-sm-4 col-md-4">
   <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
       <header class="card-heading">
         <ul class="card-actions icons left-top">
           <li>
             <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
             </li>
           </ul>
         <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
             <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
           </ul>
           <ul class="card-actions icons right-top">
             <li class="dropdown">
               <a href="#" data-toggle="dropdown" aria-expanded="false">
                 <i class="zmdi zmdi-more-vert"></i>
               </a>
               <ul class="dropdown-menu btn-primary dropdown-menu-right">

                 <li>
                   <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                 </li>
               </ul>
             </li>
             <li>
              <a href="https://poloniex.com/exchange#USDT_{{$investment->currency}}"><img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was done with USDT." width="24" height="24"></a>
               </li>
             </ul>
       </header>
     <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
         <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
     </div>
     <div class="card-body">
       <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->amount * $price),2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>
       <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

        @if(((($investment->amount * $price) / ($btc) * $multiplier)) > (($investment->bought_for) / ($previous) * $previousmultiplier))
         <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 2)}}" data-html="true">
           {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

         <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
         {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
         </span>

       @else
       <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $price) / $multiplier) - (($investment->amount * $investment->bought_at) / $multiplier), 5)}}" data-html="true">
         {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
         <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
           {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
         </span>
       @endif
         <hr style="margin-top:40px;">
         <div class="usd">
         <span style="float:left;">Before</span>
         <span style="float:right;">After</span>
         <br>
         <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_for / ($previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
         <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
         <br>
         <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->bought_at)), 2)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
         <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
         <br>
         </div>
     </div>
   </div>
 </figure>
 @endif
