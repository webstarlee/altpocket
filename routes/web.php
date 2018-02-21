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

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'JWTAuthController@login');
    Route::post('logout', 'JWTAuthController@logout');
    Route::post('refresh', 'JWTAuthController@refresh');
    Route::post('getme', 'JWTAuthController@me');

});





Route::group(['domain' => 'altp.io'], function () {
    Route::get('/', 'SupportController@index')->name('static.home');
});

Route::group(['domain' => 'admin.altpocket.io'], function () {
  Route::get('/redirect', function()
  {
    return redirect('/admin');
  });
});




Route::get('/admin/clear/{username}', 'InvestmentController@clearCacheOther');


Route::get('/formattt', 'InvestmentController@format');
Route::get('/importhi', 'HistoryController@importHistocialETH');
Route::get('/hitbtc/{userid}', 'NewImportController@importTradesHitBTC');

Route::get('/redirect', function()
{
    return View::make('errors.redirect');
});

Route::get('/raiseemail', function()
{
    return View::make('emails.raiseemail');
});

Route::get('/main', function()
{
    return View::make('test.test');
});


Route::get('/history', 'HistoryController@fix');
Route::get('/realtimechart', 'StatController@getUsers');
Route::get('/realtimechart2/{days}', 'StatController@getUsersChart');

Route::get('/coinbase/notificataion', function()
{
    return 1;
});

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



Route::post('/donate/post', 'DonationController@postDonation');





Auth::routes();

Route::get('/user/{username}', 'CommentController@showProfile');

Route::get('/u/{username}', 'ProfileController@index');



Route::post('/pusher/auth', 'PusherController@postAuth');

Route::get('/signature/{username}', 'SignatureController@generateSignature');


Route::get('/users/', 'HomeController@viewUsers');





Route::get('/coins/get/{id}', 'HomeController@getCoin');
Route::get('/', 'HomeController@landingPage');
Route::get('/importing-orders', 'HomeController@importGuide');


Route::get('/badges', function()
{
    return View::make('awards');
});
Route::get('/reset', function()
{
    return View::make('reset');
});




Route::get('/leaderboards', 'LeaderController@index');

Route::get('/leave/', 'AdminController@unImpersonate');






Route::group(['middleware' => ['auth']], function () {
      Route::get('/raise', 'AdminController@raise');
      Route::get('/hall-of-fame', 'AdminController@fame');
// Admin
Route::group(['middleware' => ['role:staff']], function () {
  Route::prefix('admin')->group(function () {
      Route::get('/', 'AdminController@show');
      Route::get('/impersonate/{username}', 'AdminController@impersonate');


      Route::get('/setadmin', 'AdminController@setAdmin');
      Route::prefix('roles')->group(function () {
          Route::get('/', 'Admin\RoleController@index');
          Route::get('/get', 'Admin\RoleController@get');
          Route::get('/create', 'Admin\RoleController@create');
          Route::post('/create', 'Admin\RoleController@new');
          Route::get('/edit/{name}', 'Admin\RoleController@edit');
          Route::post('/edit/{name}', 'Admin\RoleController@update');
          Route::get('/delete/{name}', 'Admin\RoleController@delete');
      });

      Route::prefix('permissions')->group(function () {
          Route::get('/', 'Admin\PermissionController@index');
          Route::get('/get', 'Admin\PermissionController@get');
          Route::get('/create', 'Admin\PermissionController@create');
          Route::post('/create', 'Admin\PermissionController@new');
          Route::get('/edit/{name}', 'Admin\PermissionController@edit');
          Route::post('/edit/{name}', 'Admin\PermissionController@update');
          Route::get('/delete/{name}', 'Admin\PermissionController@delete');
      });

      Route::prefix('awards')->group(function () {
          Route::get('/', 'Admin\AwardController@index');
          Route::get('/get', 'Admin\AwardController@get');
      });

      Route::prefix('users')->group(function () {
          Route::get('/', 'Admin\UserController@index');
          Route::get('/get', 'Admin\UserController@get');
      });
  });
});





//Automatic awards
Route::get('/discord/badge/2017-10-17/', 'AwardController@discordAward');

// Chat Testing
Route::get('/chat', 'ChatController@index');

Route::get('/chat/send/{to}/{message}', 'ChatController@sendMessage');

Route::get('/block/user/{id}', 'ChatController@blockUser');

Route::get('/get/messages/{user1}/{user2}', 'ChatController@getMessages');

Route::post('/chat/send', 'ChatController@sendMessage');

//SettingsController
Route::get('/settings', 'SettingsController@index');
Route::post('/settings/header', 'SettingsController@changeHeader');
Route::post('/settings/avatar', 'SettingsController@changeAvatar');
Route::post('/settings/password', 'SettingsController@changePassword');
Route::post('/settings/information', 'SettingsController@changeInfo');
Route::post('/settings/settings', 'SettingsController@changeSettings');
Route::post('/settings/delete', 'DeleteController@domoArigato');

//Status shit
Route::get('/status/{id}/like', 'StatusController@like');
Route::get('/status/{id}/delete', 'StatusController@delete');
Route::get('/status/{id}/accept', 'StatusController@accept');
Route::get('/status/{id}/hide', 'StatusController@hide');
Route::post('/status/post', 'StatusController@postStatus');
Route::post('/status/edit', 'StatusController@editStatus');
Route::post('/status/post/img', 'StatusController@uploadStatus_img');
Route::post('/status/post/img-gif', 'StatusController@uploadStatus_img_gif');
Route::get('/status/get-giphy/{quesry}/{page}', 'StatusController@status_get_giphy');

Route::get('/comment/{id}/like', 'StatusController@likeComment');
Route::get('/statuscomment/{id}/delete', 'StatusController@deleteComment');
Route::post('/comment/post/{id}', 'StatusController@postComment');
Route::get('/commentreply/{id}/like', 'StatusController@likeCommentReply');
Route::post('/commentreply/post/{id}', 'StatusController@postCommentReply');
Route::get('/commentreply/delete/{id}', 'StatusController@deleteCommentReply');
Route::post('/statuscomment/edit', 'StatusController@editComment');
Route::post('/statuscommentreply/edit', 'StatusController@editCommentReply');

Route::get('/status/get/{id}', 'StatusController@getStatus');
Route::get('/statuscomment/get/{id}', 'StatusController@getStatusComment');
Route::get('/statuscommentreply/get/{id}', 'StatusController@getStatusCommentReply');

// Tagging
Route::get('/api/users/', 'StatusController@getUsers');
Route::get('/api/coins/', 'StatusController@getCoins');
Route::get('/api/coins2/', 'StatusController@getCoinsNew');

Route::get('/api/search', 'ProfileController@searchUsers');

//Tracking
Route::post('/track/coin/{id}', 'StatusController@trackCoin');

//New Stuff 2017-07.07
Route::get('/get/coins', 'InvestmentController@getCoins');
Route::get('/get/coins2', 'InvestmentController@getCoins2');


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
Route::get('/investments', 'InvestmentController@viewInvestments2');
Route::get('/portfolio', 'InvestmentController@viewPortfolio');

Route::get('/admin/investments/{id}', 'InvestmentController@adminInvestments');
Route::get('/admin/removegroup/{userid}/{id}', 'AwardController@removeGroup');
Route::post('/admin/addgroup/{username}', 'AwardController@setGroup');
Route::get('/old-investments', 'InvestmentController@viewInvestments');
Route::get('/investments2', function(){
  return redirect('/investments');
});
//Manual Stuff


Route::get('/investments/get/{id}', 'InvestmentController@getInvestment');
Route::post('/investments/edit/{id}', 'InvestmentController@editInvestment');
Route::get('/investments/remove/{id}', 'InvestmentController@removeInvestment');
Route::get('/investments/remove/polo/{id}', 'InvestmentController@removePoloInvestment');
Route::get('/investments/remove/bittrex/{id}', 'InvestmentController@removeBittrexInvestment');
Route::get('/investments/remove/coinbase/{id}', 'InvestmentController@removeCoinbaseInvestment');
Route::get('/investments/remove/mining/{id}', 'InvestmentController@removeMining');
Route::post('/investments/sellMultiple', 'InvestmentController@sellMultiple');
//Mining
Route::post('/investments/add/mining', 'InvestmentController@addMining');
//Color
Route::post('/color/select/{coin}', 'InvestmentController@selectColor');
Route::post('/color/select/mining/{coin}', 'InvestmentController@selectColorMining');
// Notes
Route::post('/investment/note/{exchange}/{id}', 'InvestmentController@writeNote');
//Private
Route::get('/investment/private/{exchange}/{id}', 'InvestmentController@makePrivate');
// Set price of deposits
Route::post('/investment/set/{exchange}/{id}', 'InvestmentController@setInvestmentBuy');
// Set Invested
Route::post('/invested/set', 'InvestmentController@setInvested');

//New Investment stuff (08.07.2017)
Route::post('/investments/add', 'InvestmentController@addInvestment2');
Route::post('/investments/addbalance', 'InvestmentController@addBalance');
Route::post('/investments/make/{currency}', 'InvestmentController@makeInvestment');

Route::post('/investments/sell/{id}', 'InvestmentController@sellInvestment2');
Route::get('/sources/delete/{id}', 'InvestmentController@deleteSource');
Route::get('/balances/delete/{id}', 'InvestmentController@deleteBalance');
Route::post('/balances/set/{id}', 'InvestmentController@setBalance');



// Chart Stuff by Depixel
Route::get('/user/{username}/history', "ChartController@getHistory");
Route::get('/chart/{days}/{currency}', "ChartController@getPortfolioHistory");


Route::post('/sources/add/', 'InvestmentController@addSource');
Route::get('/coinbase/callback', 'SourceController@coinbaseCallback');
Route::get('/coinbase/test', 'SourceController@checkExpiry');


    Route::prefix('group')->group(function(){
        Route::get('/', 'GroupController@index')->name('group.explore');
        Route::get('/invite/{id}/{string}', 'GroupController@invite_check');
        Route::get('/invite-key/{id}', 'GroupController@generate_invite_key');
        Route::get('/invite-expire/{id}/{status}', 'GroupController@set_expire_key');
        Route::post('/autouser', 'GroupController@autocomplete_user')->name('group.autouser');
        Route::post('/post', 'GroupController@create')->name('group.post');
        Route::post('/update', 'GroupController@update')->name('group.update');
        Route::get('/view/{url}', 'GroupController@view_single')->name('group.single.view');
        Route::get('/view/{url}/about', 'GroupController@view_single_about')->name('group.single.view.about');
        Route::get('/view/{url}/members', 'GroupController@view_single_members')->name('group.single.view.members');
        Route::get('/view/{url}/photos', 'GroupController@view_single_photos')->name('group.single.view.photos');
        Route::get('/view/{url}/blocked', 'GroupController@view_single_blocked_users')->name('group.single.view.blocked');
        Route::post('/photo/upload', 'GroupController@coverphoto_upload_ready')->name('group.coverphoto.upload');
        Route::post('/post-photo/upload', 'GroupController@post_photo_upload')->name('group.post.photo.upload');
        Route::get('/photo/save/{id}', 'GroupController@cover_photo_save');
        Route::get('/photo/delete/{id}', 'GroupController@cover_photo_delete');
        Route::post('/cover-photo/crop', 'GroupController@coverphoto_crop')->name('group.coverphoto.crop');
        Route::post('/cover-photo/choose', 'GroupController@coverphoto_choose')->name('group.coverphoto.choose');
        //request part
        Route::get('/join/{id}', 'GroupController@join_group')->name('group.join');
        Route::get('/accept/user/{id}', 'GroupController@accept_group_user');
        Route::get('/reject/user/{id}', 'GroupController@reject_group_user');
        Route::get('/request-delete/user/{id}', 'GroupController@delete_group_user_request');
        Route::get('/add/member/{group}/{user}', 'GroupController@add_group_member_request');
        Route::get('/decline_group/{id}', 'GroupController@decline_request_group')->name('group.decline.group');
        Route::post('/member-add', 'GroupController@group_member_add')->name('group.add.member');
        Route::post('/agroupadd-utouser/{id}', 'GroupController@autocomplete_user_add_member');
        Route::get('/join-user/{id}', 'GroupController@join_group_user');
        Route::get('/join-user-on/{id}', 'GroupController@join_group_user_on');
        Route::get('/join-user-cancel/{id}', 'GroupController@join_group_user_cancel');
        Route::get('/join-user-cancel-on/{id}', 'GroupController@join_group_user_cancel_on');
        Route::get('/own-delete/{id}', 'GroupController@own_group_delete');
        Route::get('/leave-group/{id}', 'GroupController@leave_group');
        Route::post('/post-create', 'GroupController@group_post_create')->name('group.post.create');
        Route::post('/poll-create', 'IoGroupPollController@store')->name('group.poll.create');
        Route::get('/poll/vote/{id}', 'IoGroupPollController@votePoll');
        Route::get('/poll/vote-get/{id}', 'IoGroupPollController@get_push_data');
        Route::get('/post/get-push/{id}','GroupController@get_just_post' );
        Route::get('/post/get-push-edit/{id}','GroupController@get_just_post_edited' );
        Route::post('/post-update', 'GroupController@group_post_update')->name('group.post.update');
        Route::post('/comment/store', 'IoGroupCommentController@store')->name('group.comment.store');
        Route::get('/comment/get-push/{id}', 'IoGroupCommentController@get_just_comment');
        Route::get('/comment/get-push-edit/{id}', 'IoGroupCommentController@get_edited_comment');
        Route::get('/post/delete/{id}', 'GroupController@group_post_destroy');
        Route::get('/post/get/{id}', 'GroupController@group_post_get');
        Route::get('/comment/delete/{id}', 'IoGroupCommentController@destroy');
        Route::get('/comment/{status}/like', 'IoGroupCommentController@likeComment');
        Route::get('/comment-reply/{status}/like', 'IoGroupCommentController@likeReply');
        Route::post('/comment/edit', 'IoGroupCommentController@update')->name('group.comment.update');
        Route::post('/reply/store', 'IoGroupCommentController@store_reply')->name('group.reply.store');
        Route::post('/reply/edit', 'IoGroupCommentController@update_reply')->name('group.reply.update');
        Route::get('/reply/delete/{id}', 'IoGroupCommentController@destroy_reply');
        Route::get('/reply/get-push/{id}', 'IoGroupCommentController@get_just_reply');
        Route::get('/reply/get-push-edit/{id}', 'IoGroupCommentController@get_edited_reply');
        // group member manage
        Route::get('/make-user/admin/{group}/{id}', 'GroupMemberController@make_admin_user');
        Route::get('/make-request/admin/{group}/{id}', 'GroupMemberController@make_admin_request');
        Route::get('/remove/admin/{group}/{id}', 'GroupMemberController@remove_as_admin_user');
        Route::post('/remove/member/', 'GroupMemberController@remove_group_member')->name('group.remove.member');
        Route::get('/remove/block-member/{group}/{id}', 'GroupMemberController@remove_group_blocked_member');
    });


//coinbase import
Route::get('/coinbase/balances', 'ImportController@importBalancesCB');
Route::get('/coinbase/buys', 'ImportController@insertBuysCB');
Route::get('/coinbase/withdraws', 'ImportController@insertWithdrawsCB');
Route::get('/coinbase/sells', 'ImportController@insertSellCB');

// poll
Route::post('/create/poll', 'PollController@createPoll');
Route::get('/poll/vote/{id}', 'PollController@votePoll');
Route::get('/poll/vote-get/{status_id}', 'PollController@get_push_data');
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
Route::get('/status/get-push/{id}', 'HomeController@get_just_status');
Route::get('/status/get-push-edit/{id}', 'HomeController@get_edited_status');
Route::get('/comment/get-push/{id}', 'HomeController@get_just_comment');
Route::get('/comment/get-push-edit/{id}', 'HomeController@get_edited_comment');
Route::get('/reply/get-push/{id}', 'HomeController@get_just_reply');
Route::get('/reply/get-push-edit/{id}', 'HomeController@get_edited_reply');


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

//Friendships
Route::get('/add/{username}', 'FriendController@beFriend');
Route::get('/accept/{username}', 'FriendController@accept');

//Awards
Route::post('/award/add/{username}', 'AwardController@giveAward');



//Notification
Route::get('/notification/read/{id}', 'NotificationController@readNotification');
Route::get('/notification/readall', 'NotificationController@readAll');
Route::get('/investments/clear/cache', 'InvestmentController@clearCache');

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
Route::get('/coinbase/reset', 'ImportController@resetCoinbase');

Route::group(['middleware' => ['signedurl']], function () {

Route::get('/2fa/enable', 'Google2FAController@enableTwoFactor');
Route::get('/2fa/disable', 'Google2FAController@disableTwoFactor');



});

//miner
Route::get('/miner/toggle', 'MinerController@toggleMiner');
Route::get('/miner/refresh', 'MinerController@refreshMiner');
Route::get('/miner/addthread', 'MinerController@addThread');
Route::get('/miner/removethread', 'MinerController@removeThread');
//Validations
Route::post('/2fa/enable', 'Google2FAController@activateTwoFactor');
Route::post('/2fa/disable', 'Google2FAController@deactivateTwoFactor');

// Emails
Route::get('/2fa/request', 'Google2FAController@requestTwoFactor');
Route::get('/2fa/request/disable', 'Google2FAController@requestTwoFactorDisable');




Route::get('/discord/', 'DiscordController@index');
Route::post('/discord/generate', 'DiscordController@generate');


Route::post('/trade/new', 'TradeController@newTransaction');







// New portfolio
Route::get('/my/', 'PortfolioController@index');
Route::post('/watchlist/add', 'WatchlistController@create');
Route::get('/watchlist/delete/{id}', 'WatchlistController@delete');
Route::post('/exchange/add', 'ExchangeController@addExchange');
Route::get('/exchange/delete/{exchange}', 'ExchangeController@deleteExchange');

Route::get('/polo-test', 'PortfolioController@test');
Route::post('/bittrex-test', 'PortfolioController@bittrex');
Route::get('/coinbase-test', 'PortfolioController@coinbase');




Route::get('/exchanges/', 'PortfolioController@getExchanges');

Route::get('/get/fiat/{fiat}/{date}', 'PortfolioController@getFiat');
Route::get('/get/history/{coin}/{when}', 'PortfolioController@getHistorical');
Route::get('/get/historyeth/{when}', 'PortfolioController@getEthHistorical');
Route::get('/get/holdings/{token}/{currency}', 'PortfolioController@getSummary');
Route::get('/ggg', function(){
 Auth::user()->getPortfolioHistory(6, 'USD', 'Value');
});


Route::post('/portfolio/add', 'PortfolioController@addTransaction');
Route::get('/portfolio/clear', 'PortfolioController@clearPortfolio');
Route::get('/portfolio/toggle/{id}', 'PortfolioController@toggleDeposit');
Route::post('/portfolio/profit', 'PortfolioController@setProfit');


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
