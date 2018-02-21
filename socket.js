var fs = require( 'fs' );
var app = require('express')();
var https        = require('https');
var io = require('socket.io')(https);
var HTTPSOptions = {
    cert: fs.readFileSync('/etc/nginx/ssl/Altpocket.io/203319/server.crt'),
    key: fs.readFileSync('/etc/nginx/ssl/Altpocket.io/203319/server.key'),
    requestCert: false,
    rejectUnauthorized: false,
};
HTTPSOptions.agent = new https.Agent(HTTPSOptions);
var httpsServer = https.createServer(HTTPSOptions, app);
io = io.listen(httpsServer, {
    log: false
});

var Redis = require('ioredis');
var redis = new Redis();

io.on('connection', function(socket){
  console.log("user connected woop");
});



redis.subscribe('test-channel', function(err, count) {
});

redis.on('message', function(channel, message) {
    console.log('Message Recieved: ' + message);
    message = JSON.parse(message);
    console.log(channel + ':' + message.event);
    io.emit(channel + ':' + message.event, message.data);
});

httpsServer.listen(3000, function(){
    console.log('Listening on Port 3000');
});
