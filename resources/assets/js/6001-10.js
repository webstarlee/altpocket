// app.js

import Echo from "laravel-echo";


window.io = require('socket.io-client');



window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'https://altpocket.io',
    auth: {
    headers: {
        'Authorization': 'Bearer ' + 'ddd5816d7fe7ad10c6d9f78dcbacd136'
    }
  }
});
