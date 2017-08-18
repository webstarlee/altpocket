@extends('layouts.app')


@section('title')
How Investment Importing Works
@endsection


@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>How investment importing works</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div id="card_content" class="tab-content">
                    <div class="row">
                  <div class="col-xs-12">
                    <div class="card">
                      <header class="card-heading">
                        <h2 class="card-title">Altpocket.io and investment importing</h2>
                          <hr>
                      </header>
                      <div class="card-body">
                        <p>One thing that I had in mind when I created Altpocket.io was that I wanted to have some kind of highscores on the platform, now I knew that this wouldn't be possible because when you add your investments you can write any values that you want. So I was thinking of ways to solve it and this is how I solved it.</p>
                        <h1>Introducing <span style="color:#5ecbf7;">Verified Investments<i class="material-icons" style="color:#5ecbf7;cursor:pointer;font-size:17px;" data-toggle="tooltip" title="Verified Investment">verified_user</i></span></h1>
                        <p>Verified investments are investments that has been automaticially grabbed from either <code>Bittrex</code> or <code>Poloniex</code>!</p>
                        <p>Using this will make it so you won't have to put in all your orders manually, simly create a new API key on either of the platforms and give the key read rights (so we only can read your previous orders).</p>
                        <h1 style="font-weight:600;">Must knows about <span style="color:#5ecbf7;">Verified Investments<i class="material-icons" style="color:#5ecbf7;cursor:pointer;font-size:17px;" data-toggle="tooltip" title="Verified Investment">verified_user</i></span></h1>
                          <p>Importing from both bittrex and poloniex is fully supported as of 18th of May 2017, however on the 21st of June 2017 we launched our investments system 2.0 which is just like the old system but completely rewritted and restructured.</p>
                          <p>Previously when you imported it would only import buy orders and sell orders in the BTC market. However with this update, we now also support USDT and ETH markets.</p>
                          <p>Please keep in mind that the system is still in its early stages, the importing process is not simple and a lot of things goes into consideration when importing.</p>
                          <p>The system now also imports your deposits, withdrawals and balances to give you a more complete and accurate view of your holdings and investments.</p>
                        <h1>How to start using <span style="color:#5ecbf7;">Verified Investments<i class="material-icons" style="color:#5ecbf7;cursor:pointer;font-size:17px;" data-toggle="tooltip" title="Verified Investment">verified_user</i></span></h1>
                        <h2><span style="color:#5ecbf7;">Step 1:</span> Generate an API key on Bittrex or Poloniex</h2>
                          <p>The first thing you want to do is to generate an API key for us on either Bittrex or Poloniex, to do this you will need to have 2FA enabled on your Bittrex/Poloniex account. Start by either going to Poloniex or Bittrex and head to the API key section.</p>
                          <p><strong>BITTREX:</strong> When you are at the API keys section, click Add New Key and then enable Read Info on it, then enter your 2FA code and click Generate Key.</p>
                          <p><strong>BITTREX:</strong> When the key is generated, you will be given a public and private key, use these two on the next step.</p>
                          <p><strong>Poloniex:</strong> When you enabled API access on your account you will be given a public and private key, these are the keys you will use on Altpocket.</p>
                          <p><strong>Poloniex:</strong> Be sure to disable trading and withdrawal so we only can read data.</p>
                          <img src="/img/1.png" style="width:70%"/>
                          <img src="http://i.imgur.com/Oy5hjq6.png" style="width:70%"/>
                        <h2><span style="color:#5ecbf7;">Step 2:</span> Submit the keys to Altpocket.io</h2>
                          <p>Now once you have the private and public key, click API keys in the main menu.</p>
                          <p>Now you should see a window where you can enter your API keys to either of the exchanges, select your exchange using the tabs and enter the API combination you got from step 1.</p>
                          <img src="http://i.imgur.com/sPcrLSP.png" style="width:70%"/>
                        <h2><span style="color:#5ecbf7;">Step 3:</span> Import your investments to Altpocket.io</h2>
                          <p>You are almost done, all you have to do now is to go to your investments (<a href="/investments">https://altpocket.io/investments</a>), click the + sign button to open the investments menu then click the button respresenting your exchange.</p>
                          <p>When you clicked the import button for your exchange your import will be put into the queue. Once your import has started you will recieve a push notification in the bottom right corner telling you that the import has started, you will also recieve a push notification when it is completed.</p>
                          <p>Once completed, go to your investments again or reload the page if it haven't already, you will now see your investments.</p>
                          <p>Once you have imported your investments, they will be marked as verified, Verified Investments can not be edited.</p>
                          <img src="http://i.imgur.com/GCcBDoT.gif" style="width:70%"/>
                        <h2><span style="color:#5ecbf7;">Step 4:</span> Done!</h2>
                          <p>Your investments are now imported to our system, all investments that are verified are eligible for our highscores.</p>
                          <br>
                          <h1>Thank you for using Altpocket.io! </h1>
                      </div>
                    </div>
                  </div>
                </div>
                </div>

            </div>
        </div>


</div>

@endsection
