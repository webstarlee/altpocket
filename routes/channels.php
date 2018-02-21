<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('private-App.User.{id}', function ($user) {
    return true;
});
Broadcast::channel('App.User.*', function ($user) {
    return 1; // (int) $user->id === (int) $userId
});

Broadcast::channel('presence-test-channel', function () {
    return 1;
});

Broadcast::channel('price-channel', function () {
    return true;
});

Broadcast::channel('presence-price-channel', function () {
    return true;
});

Broadcast::channel('chat', function ($user) {
    return ['id' => $user->id, 'username' => $user->username];
});
