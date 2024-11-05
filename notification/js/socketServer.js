var socket = require('socket.io');
var express = require('express');
var os = require('os');
var http = require('http');
var cors = require('cors');
var bodyParser = require('body-parser');
const dbConfig = require('./config/config');
const Database = require('./config/database');

const db = new Database(dbConfig);

var app = express();
// Enable trust proxy setting
app.set('trust proxy', true);
app.use(cors());
// parse application/x-www-form-urlencoded
app.use(bodyParser.urlencoded({ extended: false }))
// parse application/json
app.use(bodyParser.json())

var server = http.createServer(app);
var io = socket.listen(server);
require('events').EventEmitter.prototype._maxListeners = 100;

var connection = null;
const dbCon = async function(){
  try {
    if(connection == null)
    {
      connection = await db.getConnection();
    }
  } catch (error) {
    console.log(error);
  }
}

io.on('connect', async function (client) {
  console.log('New client !');
  //await sendPendingNotificationsToUser(client);

  client.on('message', async function (data) {
    //console.log(data);
    if (data.approval_data) {
      var approval_data = data.approval_data;
      var cond = "";
      approval_data.forEach(row => {
         var menu_id = row.menu_id;
         var ref_id  = row.ref_id;
         var entry_form = row.entry_form;
         var message = row.desc;
         if(cond.length > 0)
         {
          cond = cond + ` or ( ENTRY_FORM = ${entry_form} and REF_ID = ${ref_id} )`;
         }
         else
         {
          cond =  ` and ( ( ENTRY_FORM = ${entry_form} and REF_ID = ${ref_id} )`;
         }
        
        
        // console.log(`ref_id = ${ref_id},cond = ${cond} `);
      });
      
     
      if(cond.length > 0)
      {
        cond = cond + ")";
      }
      var result = await db.findAllObjects("SELECT REF_ID,ENTRY_FORM,M_MENU_ID,USER_ID,IS_APPROVED,NOTIFI_DESC from APPROVAL_NOTIFICATION_ENGINE where IS_SEEN=0 " + cond);
      console.clear();
      var return_data = [];
      for(var row of result)
      {
        var obj = {
          ref_id : row.REF_ID,
          entry_form : row.ENTRY_FORM,
          m_menu_id:row.M_MENU_ID,
          user_id:row.USER_ID,
          is_approved:row.IS_APPROVED,
          desc : row.NOTIFI_DESC
         }
         return_data.push(obj);
      }
      console.log(return_data);
      io.emit('message', return_data);
    }
  });
  client.on('message_count', async function (data) {
    var user_id = data.user_id;
    //console.log('message_count event received');
    try {
        // No message provided, directly retrieve the message count
        var result = await db.findValueById(
          "select COALESCE(count(id), 0) as M_NOTI_COUNT  from APPROVAL_NOTIFICATION_ENGINE where IS_SEEN=0 and user_id = '"+user_id+"'"
        );
        result = result ? result : 0;
        var obj = {
          user_id : user_id,
          notification : result
        }
        io.emit('message_count', obj);
        //console.log(`Message count sent: ${result}`);
    } catch (error) {
      io.emit('error');
      //console.log(`Message count error occurs: ${error}`);
    }
  });
  client.on('login',async function (data) {
    const userId = data.userId; // Assuming you have the user ID available
    if(userId)
    {
      await sendPendingNotificationsToUser(client,userId);
    }

  });
  client.on('unapproved_req', async function (data) {
    if (data.approval_data) {
      var approval_data = data.approval_data;
      var cond = "";
      approval_data.forEach(row => {
         var menu_id = row.menu_id;
         var ref_id  = row.ref_id;
         var entry_form = row.entry_form;
         var message = row.desc;
         if(cond.length > 0)
         {
          cond = cond + ` or ( ENTRY_FORM = ${entry_form} and REF_ID = ${ref_id} )`;
         }
         else
         {
          cond =  ` and ( ( ENTRY_FORM = ${entry_form} and REF_ID = ${ref_id} )`;
         }
      });
      
     
      if(cond.length > 0)
      {
        cond = cond + ")";
      }
      var result = await db.findAllObjects("SELECT REF_ID,ENTRY_FORM,M_MENU_ID,USER_ID,IS_APPROVED,NOTIFI_DESC, UNAPPROVE_REQUEST from APPROVAL_NOTIFICATION_ENGINE where IS_APPROVED=1 and UNAPPROVE_REQUEST is not null " + cond);
      console.clear();
      var return_data = [];
      for(var row of result)
      {
        var obj = {
          ref_id : row.REF_ID,
          entry_form : row.ENTRY_FORM,
          m_menu_id:row.M_MENU_ID,
          user_id:row.USER_ID,
          is_approved:row.IS_APPROVED,
          desc : row.NOTIFI_DESC,
          unapprove_request : row.UNAPPROVE_REQUEST
         }
         return_data.push(obj);        
      }
      console.log(return_data);
      io.emit('unapproved_req', return_data);
    }
  });

  //for chat message
  client.on("chat_message",async function (data){
     const chat_message_id = data.chat_message_id ? data.chat_message_id : 0;
     const from_user_id    = data.from_user_id ? data.from_user_id : 0;
     const to_user_id      = data.to_user_id ? data.to_user_id : 0;
     var cond = " is_seen = 0 and is_deleted = 0 ";
     if(from_user_id > 0)
     {
        cond = cond + " and from_user_id = "+from_user_id;
     }
     if(to_user_id > 0)
     {
        cond = cond + " and to_user_id = "+to_user_id;
     }
     var chat_messages =  await db.find("CHAT_MESSAGE", cond);
     var send_data = [];
     chat_messages.forEach(message => {
        var cur_mess = '';
        if(message[0] == chat_message_id)
        {
          cur_mess = message[3];
        }
        var ob = {
           id : message[0],
           from_user_id : message[1],
           to_user_id : message[2],
           message: message[3],
           cur_mess : cur_mess
        }
        send_data.push(ob);
     });
     console.log(send_data);
     io.emit('chat_message', send_data);
  });
});



// When a user logs in, retrieve the pending notifications from the database and send them to the user
const  sendPendingNotificationsToUser = async function (userSocket,userId='') {
  try {
    //console.log(`userSocket.userId=${userId}`);
    // Retrieve the pending notifications for the user from the database
    //let connection = db.getConnection;
    //const pendingNotifications = await db.findAllObjects(`SELECT APP_NOTI_DESC as MESSAGE FROM NOTIFICATION_BREAK_DOWN WHERE NOTIFICATION_USER=${userId} AND IS_VIEWED = 0`,connection);
    //console.log(pendingNotifications);
    // Emit the pending notifications to the user
    // pendingNotifications.forEach(notification => {
    //   userSocket.emit('message', notification.MESSAGE);
    // });
    var result = await db.findValueById(
      "select sum(ID) as M_NOTI_COUNT  from APPROVAL_NOTIFICATION_ENGINE where IS_SEEN=0"
    );
    result = result ? result : 0;
    userSocket.emit('message', `Total Approval/Unapprove Notification Found: ${result}`);
  } catch (error) {
    console.error('Error sending pending notifications:', error);
  }
}

server.listen(9418,'192.168.11.242', () => {
  console.log(`app listening on `);
});
// server.listen(9418,'localhost', () => {
//   console.log(`app listening on `);
// });
