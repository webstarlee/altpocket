var Discordie = require("discordie");
var request = require('request');
var fs = require("fs");
var client = new Discordie();


let keys = JSON.parse(fs.readFileSync("./keys.json", "utf8"));



client.connect({
  // replace this sample token
  token: "MzQ2OTM3MzIxMTM4MDI4NTQ2.DHRG0g.ey3TDu2WoKT-rTQkQZ50ucMqNi0"
});

client.Dispatcher.on("GATEWAY_READY", e => {
  console.log("Connected as: " + client.User.username);
});

client.Dispatcher.on("MESSAGE_CREATE", e => {

  let prefix = "!";

  // Use this to authenticate server

  if(e.message.content.startsWith(prefix + "setup"))
  {
    const args = e.message.content.split(/\s+/g).slice(1);
    let key = args[0]; // yes, start at 0, not 1.

      request('https://altpocket.io/discord/auth/' + key + "/" + e.message.guild.id, (err, res, body) => {
        if (err) { return console.log(err); }
        if(body == "Complete")
        {
          e.message.reply("This server is authenticated with Altpocket.");
        } else if(body == "Valid"){
          e.message.reply("Server successfully authenticated with Altpocket.");
        } else if(body == "Used") {
          e.message.reply("This API key has already been used to authenticate to Altpocket.");
        } else if(body == "Invalid")
        {
          e.message.reply("Invalid API key, not found in database.");
        }

      });

  }


  //Gets profit
  if (e.message.content.startsWith(prefix + "profit")) {
    const args = e.message.content.split(/\s+/g).slice(1);
    let username = args[0]; // yes, start at 0, not 1.

    if(username == null)
    {
      e.message.reply("Please specify a name.");
      return false;
    }
    request('https://altpocket.io/get/profit/' + username + "/" + e.message.guild.id, (err, res, body) => {
      if (err) { return console.log(err); }
      if(body != "private" && body != "Failed" && body != "none")
      {
        let emoji = "";
        if(body < 0) {
          emoji = ":chart_with_downwards_trend:";
        } else {
          emoji = ":chart_with_upwards_trend:";
        }

        e.message.reply(username + "s profit is $" + body + ". " + emoji);
      } else if(body == "Failed") {
        e.message.reply("This server is not authenticated with Altpocket.");
      } else if(body == "none") {
        e.message.reply("No user with this username exists.");
      } else {
        e.message.reply('sorry but ' + username + ' has a private profile. :spy:');
      }
    });
  }

  if (e.message.content.startsWith(prefix + "worth")) {
    const args = e.message.content.split(/\s+/g).slice(1);
    let username = args[0]; // yes, start at 0, not 1.

    if(username == null)
    {
      e.message.reply("Please specify a name.");
      return false;
    }
    request('https://altpocket.io/get/worth/' + username + "/" + e.message.guild.id, (err, res, body) => {
      if (err) { return console.log(err); }
      if(body != "private" && body != "Failed" && body != "none")
      {
        e.message.reply(username + "s networth is $" + body + ".");
      } else if(body == "Failed") {
        e.message.reply("This server is not authenticated with Altpocket.");
      } else if(body == "none") {
        e.message.reply("No user with this username exists.");
      }  else {
        e.message.reply('sorry but ' + username + ' has a private profile. :spy:');

      }
    });
  }

  if (e.message.content.startsWith(prefix + "invested")) {
    const args = e.message.content.split(/\s+/g).slice(1);
    let username = args[0]; // yes, start at 0, not 1.

    if(username == null)
    {
      e.message.reply("Please specify a name.");
      return false;
    }
    request('https://altpocket.io/get/invested/' + username + "/" + e.message.guild.id, (err, res, body) => {
      if (err) { return console.log(err); }
      if(body != "private" && body != "Failed" && body != "none")
      {
        e.message.reply(username + " has invested $" + body + ". " + ":money_with_wings:");
      } else if(body == "Failed") {
        e.message.reply("This server is not authenticated with Altpocket.");
      } else if(body == "none") {
        e.message.reply("No user with this username exists.");
      }  else {
        e.message.reply('sorry but ' + username + ' has a private profile. :spy:');

      }
    });
  }

  if (e.message.content.startsWith(prefix + "followers")) {
    const args = e.message.content.split(/\s+/g).slice(1);
    let username = args[0]; // yes, start at 0, not 1.

    if(username == null)
    {
      e.message.reply("Please specify a name.");
      return false;
    }
    request('https://altpocket.io/get/followers/' + username + "/" + e.message.guild.id, (err, res, body) => {
      if (err) { return console.log(err); }
      if(body != "private" && body != "Failed" && body != "none")
      {
        e.message.reply(username + " has " + body + " followers.");
      } else if(body == "Failed") {
        e.message.reply("This server is not authenticated with Altpocket.");
      } else if(body == "none") {
        e.message.reply("No user with this username exists.");
      }  else {
        e.message.reply('sorry but ' + username + ' has a private profile. :spy:');

      }
    });
  }

  if(e.message.content.startsWith(prefix + "profile"))
  {
    const args = e.message.content.split(/\s+/g).slice(1);
    let username = args[0]; // yes, start at 0, not 1.
    if(username == null)
    {
      e.message.reply("Please specify a name.");
      return false;
    }
    request('https://altpocket.io/get/profile/' + username + "/" + e.message.guild.id, (err, res, body) => {
      if (err) { return console.log(err); }
      if(body != "private" && body != "Failed" && body != "none")
      {
        var data = JSON.parse(body);
        var invested = data['invested'];
        var worth = data['worth'];
        var followers = data['followers'];
        var impressions = data['impressions'];
        var bio = data['bio'];
        var avatar = data['avatar'].split(' ').join('%20');
        var userid = data['id'];
        var profit = data['profit'];

        var avatar_url = "https://altpocket.io/uploads/avatars/" + userid + "/" + avatar;
        if(avatar == "default.jpg")
        {
          avatar_url = "https://altpocket.io/assets/img/default.png";
        }

        const embed = {
      "title": username + "s Altpocket Profile",
      "description": bio,
      "url": "https://altpocket.io/user/" + username,
      "color": 12930474,
      "footer": {
        "icon_url": "https://altpocket.io/assets/logo.png",
        "text": "Not registered to Altpocket? Register today!"
      },
      "thumbnail": {
        "url": avatar_url
      },
      "fields": [
        {
          "name": "Invested",
          "value": "$" + invested
        },
        {
          "name": "Profit",
          "value": "$" + profit
        },
        {
          "name": "Net Worth",
          "value": "$" + worth
        },
        {
          "name": "Impressions",
          "value": impressions,
          "inline": true
        },
        {
          "name": "Followers",
          "value": followers,
          "inline": true
        }
      ]
    };

        e.message.channel.sendMessage("Here is "+username+"s Altpocket Profile", false, embed);
      } else if(body == "Failed") {
        e.message.reply("This server is not authenticated with Altpocket.");
      } else if(body == "none") {
        e.message.reply("No user with this username exists.");
      }  else {
        e.message.reply('sorry but ' + username + ' has a private profile. :spy:');

      }
    });
  }

  if (e.message.content.startsWith(prefix + "commands")) {
    e.message.channel.sendMessage('```== Beep Boop == \n!profile {username} - Returns Profile Information \n!profit {username} - Returns User Profit \n!worth {username} - Returns User Networth \n!invested {username} - Returns User Invested \n!followers {username} - Returns User Followers```');
  }
});
