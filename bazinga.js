var Discordie = require("discordie");
var request = require('request');
var client = new Discordie();

client.connect({
  // replace this sample token
  token: "MzQ3MDIwMDQ5ODI5MzMwOTU1.DHSTUQ.zeX-lM_O1D5DTk77AQVPss2h7P4"
});

client.Dispatcher.on("GATEWAY_READY", e => {
  console.log("Connected as: " + client.User.username);
});

client.Dispatcher.on("MESSAGE_CREATE", e => {

  var array = ['bulgaria', 'bazoongle', 'bass', 'baptize', 'baker'];
  var rand = array[Math.floor(Math.random() * array.length)];

  if(e.message.content.startsWith("sheldon"))
  {
    const args = e.message.content.split(/\s+/g).slice(1);
    let key = args[0]; // yes, start at 0, not 1.

      e.message.reply(rand);

  }

});
