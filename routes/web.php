<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// API shit
Route::group(['domain' => 'api.altpocket.io'], function () {
    Route::get('/', 'SupportController@index')->name('static.home');
});



Route::get('/formattt', 'InvestmentController@format');


// SupportController
Route::get('/support', 'SupportController@index');
Route::get('/about', 'SupportController@about');
Route::get('/updates', 'SupportController@updates');
Route::get('/questions', 'SupportController@questions');
Route::get('/question/{id}', 'SupportController@question');
Route::get('/question/{id}/upvote', 'SupportController@upvote');
Route::get('/question/{id}/downvote', 'SupportController@downvote');
Route::get('/answer/{id}/upvote', 'SupportController@upvote_a');
Route::get('/answer/{id}/downvote', 'SupportController@downvote_a');


Route::get('/importuser/{userid}', 'ImportController@dispatchImportUser');


//Blog

Route::get('/blog', 'BlogController@index');
Route::get('/post/{id}', 'BlogController@viewPost');


Route::get('/statuses/', 'HomeController@testA');

Route::get('/zsd', 'TrackingController@updateNicehash');
Route::get('/test1231', 'HomeController@updateProfit');

Route::post('/donate/post', 'DonationController@postDonation');





Auth::routes();

Route::get('/user/{username}', 'CommentController@showProfile');

Route::post('/pusher/auth', 'PusherController@postAuth');

Route::get('/signature/{username}', 'SignatureController@generateSignature');


Route::get('/users/', 'HomeController@viewUsers');


Route::get('/d/', 'HomeController@viewUsers');
Route::get('/data', 'HomeController@anyData');


Route::get('/coins/get/{id}', 'HomeController@getCoin');
Route::get('/', 'HomeController@landingPage');
Route::get('/importing-orders', 'HomeController@importGuide');


Route::get('/badges', function()
{
    return View::make('awards');
});

Route::get('/staff', function()
{
    return View::make('staff');
});

Route::get('/testlogin', function()
{
    return View::make('auth.newlogin');
});



Route::get('/reset', function()
{
    return View::make('reset');
});


Route::get('/leaderboards', 'LeaderController@index');

Route::get('/plain', function()
{
    return View::make('plain');
});



Route::group(['middleware' => ['auth']], function () {

//Status shit
Route::get('/status/{id}/like', 'StatusController@like');
Route::get('/status/{id}/delete', 'StatusController@delete');
Route::get('/status/{id}/accept', 'StatusController@accept');
Route::post('/status/post', 'StatusController@postStatus');
Route::post('/status/edit/{id}', 'StatusController@editStatus');

Route::get('/comment/{id}/like', 'StatusController@likeComment');
Route::get('/statuscomment/{id}/delete', 'StatusController@deleteComment');
Route::post('/comment/post/{id}', 'StatusController@postComment');
Route::post('/statuscomment/edit/{id}', 'StatusController@editComment');

Route::get('/status/get/{id}', 'StatusController@getStatus');
Route::get('/statuscomment/get/{id}', 'StatusController@getStatusComment');

// Tagging
Route::get('/api/users/', 'StatusController@getUsers');
Route::get('/api/coins/', 'StatusController@getCoins');

//Tracking
Route::post('/track/coin/{id}', 'StatusController@trackCoin');

//New Stuff 2017-07.07
Route::get('/get/coins', 'InvestmentController@getCoins');


//support

Route::get('/myquestions', 'SupportController@my');

Route::get('/ask', 'SupportController@ask');
Route::post('/ask/post', 'SupportController@postQuestion');
Route::post('/question/{id}/answer', 'SupportController@postAnswer');
Route::get('/question/{id}/delete', 'SupportController@delete');
Route::get('/question/{id}/edit', 'SupportController@edit');
Route::post('/question/{id}/edit', 'SupportController@update');
Route::get('/question/{id}/sticky', 'SupportController@sticky');


Route::get('/answer/{id}/delete', 'SupportController@delete_a');
Route::get('/answer/{id}/edit', 'SupportController@edit_a');
Route::get('/answer/{id}/best', 'SupportController@best');
Route::post('/answer/{id}/edit', 'SupportController@update_a');

Route::get('/reply/{id}', 'SupportController@reply');
Route::post('/reply/{id}', 'SupportController@postReply');
Route::get('/reply/{id}/delete', 'SupportController@delete_r');
Route::get('/reply/{id}/edit', 'SupportController@edit_r');
Route::post('/reply/{id}/edit', 'SupportController@update_r');


// Blog
Route::post('/post/{id}/comment', 'BlogController@leaveComment');
Route::get('/comment/{id}/delete', 'BlogController@deleteComment');

Route::get('/blog/post', 'BlogController@viewMake');
Route::get('/post/{id}/edit', 'BlogController@viewEdit');
Route::post('/post/{id}/edit', 'BlogController@editPost');
Route::post('/blog/post', 'BlogController@makePost');


  Route::group(['prefix' => 'shoutbox'], function() {
    Route::post('messages', ['as' => 'shoutbox-fetch', 'uses' => 'ShoutboxController@fetch']);
    Route::post('send', ['as' => 'shoutbox-send', 'uses' => 'ShoutboxController@send']);
  });



Route::get('/toggle-widget/', 'SignatureController@toggleWidget');
Route::get('/update-signature/', 'SignatureController@updateSignature');

Route::get('/coins', 'HomeController@coins');

Route::get('/stats', 'StatController@index');

//CSV stuff
Route::get('importExport', 'CsvController@importExport');
Route::get('downloadExcel/{type}', 'CsvController@downloadExcel');
Route::post('importExcel', 'CsvController@importExcel');


// New
Route::get('/investments', 'InvestmentController@viewInvestments');
Route::get('/investments2', 'InvestmentController@viewInvestments2');
//Manual Stuff
Route::post('/investments/add', 'InvestmentController@addInvestment');
Route::get('/investments/get/{id}', 'InvestmentController@getInvestment');
Route::post('/investments/edit/{id}', 'InvestmentController@editInvestment');
Route::post('/investments/sell/{id}', 'InvestmentController@sellInvestment');
Route::get('/investments/remove/{id}', 'InvestmentController@removeInvestment');
Route::get('/investments/remove/polo/{id}', 'InvestmentController@removePoloInvestment');
Route::get('/investments/remove/bittrex/{id}', 'InvestmentController@removeBittrexInvestment');
Route::get('/investments/remove/mining/{id}', 'InvestmentController@removeMining');
Route::post('/investments/sellMultiple', 'InvestmentController@sellMultiple');
//Mining
Route::post('/investments/add/mining', 'InvestmentController@addMining');
//Color
Route::post('/color/select/{coin}', 'InvestmentController@selectColor');
// Notes
Route::post('/investment/note/{exchange}/{id}', 'InvestmentController@writeNote');
//Private
Route::get('/investment/private/{exchange}/{id}', 'InvestmentController@makePrivate');

//New Investment stuff (08.07.2017)
Route::post('/investments2/add', 'InvestmentController@addInvestment2');
Route::get('/sources/delete/{id}', 'InvestmentController@deleteSource');
Route::get('/balances/delete/{id}', 'InvestmentController@deleteBalance');


Route::post('/sources/add/', 'InvestmentController@addSource');
Route::get('/coinbase/callback', 'SourceController@coinbaseCallback');
Route::get('/coinbase/test', 'SourceController@checkExpiry');




//coinbase import
Route::get('/coinbase/balances', 'ImportController@importBalancesCB');



// poll
Route::post('/create/poll', 'PollController@createPoll');
Route::get('/poll/vote/{id}', 'PollController@votePoll');
Route::get('/poll/{id}/{statusid}/delete', 'PollController@deletePoll');





Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');




Route::get('/coins/remove/{id}', 'HomeController@removeCoin');
Route::post('/coins/add', 'HomeController@addCoin');
Route::post('/coins/addmining', 'HomeController@addMining');
Route::get('/coins/reset', 'HomeController@resetCoins');
Route::get('/coins/sold/{id}', 'HomeController@soldCoin');
Route::post('/coins/edit/{id}', 'HomeController@editCoin');
Route::post('/coins/sell/{id}', 'HomeController@soldCoin');
Route::post('/coins/sellMultiple', 'HomeController@sellMultiple');
Route::post('/user/{username}/edit', 'HomeController@updateInfo');
Route::post('/user/{username}/editavatar', 'HomeController@avatar');
Route::post('/uploadlogo', 'HomeController@uploadLogo');
Route::post('/user/{username}/editheader', 'HomeController@header');
Route::post('/user/{username}/editsettings', 'HomeController@updateSettings');


Route::get('/dashboard', 'HomeController@index2')->name('home');
Route::get('/dashboard2', 'HomeController@index2')->name('home');


Route::get('/home', 'HomeController@index2')->name('home');
Route::get('/migrate', 'ImportController@migrate');
Route::post('/user/{username}/api', 'HomeController@updateApi');


Route::get('/change-theme', 'HomeController@changeTheme');
Route::get('/change-currency/{currency}', 'HomeController@changeCurrency');
Route::get('/change-api/{api}', 'HomeController@changeAPI');

//Comments
Route::get('/comment/get/{id}', 'HomeController@getComment');
Route::post('/comment/add/{id}', 'CommentController@addComment');
Route::post('/comment/edit/{id}', 'CommentController@editComment');
Route::get('/comment/delete/{id}', 'CommentController@deleteComment');


//Following
Route::get('/follow/{username}', 'FollowController@followUser');
Route::get('/unfollow/{username}', 'FollowController@unfollowUser');

//Awards
Route::post('/award/add/{username}', 'AwardController@giveAward');



//Notification
Route::get('/notification/read/{id}', 'NotificationController@readNotification');
Route::get('/notification/readall', 'NotificationController@readAll');
Route::get('/toggle-notifications', 'NotificationController@toggleEmail');
Route::get('/toggle-condensed', 'InvestmentController@toggleCondensed');

Route::post('/api/polo/1', 'CryptoController@poloLoadOrders');
Route::post('/api/polo/2', 'CryptoController@poloInsertBuys');
Route::post('/api/polo/3', 'CryptoController@poloInsertSales');


Route::post('/api/bittrex/1', 'CryptoController@bittrexLoadOrders');
Route::post('/api/bittrex/2', 'CryptoController@bittrexInsertBuys');
Route::post('/api/bittrex/3', 'CryptoController@bittrexInsertSales');




Route::post('/api/bittrex/newimport', 'CryptoController@bittrexLoadOrders2');





Route::post('/changepassword', 'HomeController@changePassword');



// New Import
Route::post('/keys/save/polo', 'ImportController@addPoloKeys');
Route::post('/keys/save/bittrex', 'ImportController@addBittrexKeys');
Route::post('/import/dispatch', 'ImportController@dispatchNew');



Route::post('/import/buys', 'ImportController@insertBuys');
Route::post('/import/trades', 'ImportController@importTrades');
Route::post('/import/deposits', 'ImportController@importDeposits');
Route::get('/deposits', 'ImportController@importDeposits');
Route::post('/import/withdraws', 'ImportController@importWithdraws');
Route::post('/import/sell', 'ImportController@insertSells');
Route::post('/import/balance', 'ImportController@importBalances');
Route::get('/import/b2', 'ImportController@importBalances3');



Route::post('/importpolo', 'ImportController@dispatchImport');
Route::post('/importbittrex', 'ImportController@dispatchImportB');

Route::get('/import/deposits/b', 'ImportController@importDepositsB');
Route::get('/import/trades/b', 'ImportController@importTradesB');
Route::get('/import/buys/b', 'ImportController@insertBuysB');
Route::get('/import/sell/b', 'ImportController@insertSellsB');
Route::get('/import/balance/b', 'ImportController@importBalancesB');
Route::get('/import/withdraws/b', 'ImportController@importWithdrawsB');

Route::get('/calc', 'ImportController@calculateInvested');

Route::get('/polo/reset', 'ImportController@resetPolo');
Route::get('/bittrex/reset', 'ImportController@resetBittrex');


Route::group(['middleware' => ['signedurl']], function () {

Route::get('/2fa/enable', 'Google2FAController@enableTwoFactor');
Route::get('/2fa/disable', 'Google2FAController@disableTwoFactor');

});



//Validations
Route::post('/2fa/enable', 'Google2FAController@activateTwoFactor');
Route::post('/2fa/disable', 'Google2FAController@deactivateTwoFactor');

// Emails
Route::get('/2fa/request', 'Google2FAController@requestTwoFactor');
Route::get('/2fa/request/disable', 'Google2FAController@requestTwoFactorDisable');




Route::get('/discord/', 'DiscordController@index');
Route::post('/discord/generate', 'DiscordController@generate');
});


Route::get('/realtime', 'AdminController@index');
Route::get('/jonathan', 'CryptoController@jonathan');


Route::get('/2fa/validate', 'Auth\LoginController@getValidateToken');
Route::post('/2fa/validate', ['middleware' => 'throttle:5', 'uses' => 'Auth\LoginController@postValidateToken']);








Route::get('/user/{username}/impressed', 'HomeController@impressed');


Route::get('/get/profit/{username}/{serverid}', 'HomeController@getProfit');
Route::get('/get/worth/{username}/{serverid}', 'HomeController@getWorth');
Route::get('/get/followers/{username}/{serverid}', 'HomeController@getFollowers');
Route::get('/get/invested/{username}/{serverid}', 'HomeController@getInvested');
Route::get('/get/profile/{username}/{serverid}', 'HomeController@getProfile');


Route::get('/discord/auth/{key}/{serverid}/{servername}', 'HomeController@discordAuth');

Route::get('/get/stats/{username}', 'HomeController@getInvestments');

Route::get('/chart/{coin}/{username}', function ($coin,$user) {
    return 'User '.$user;
});
