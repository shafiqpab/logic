<?php 
include_once( dirname( __FILE__ ) . '/class/OracleDb.class.php' );
$oracleDb = new OracleDatabase();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	
	<title>Real Time Notification</title>
	
	<link rel="stylesheet" href="css/bootstrap.css" />
	<style type="text/css">body { padding-top: 60px; }</style>
	<link rel="stylesheet" href="css/bootstrap-responsive.css" />
	
	<link rel="stylesheet" href="css/index.css" />
	<link href="css/chat_box.css" rel="stylesheet">
	<style type="text/css">
	#messages { width: 50%; }
	#messages li { border-bottom: 1px solid #ccc; font-size: 11px; margin-bottom: 5px; padding: 0 5px; }
</style>
</head>

<body>
	<form class="form-inline" id="messageForm">
		<?php 
		$messages = $oracleDb->sql_select( 'SELECT * FROM message ORDER BY id DESC' );
        // echo "<pre>";
        // print_r($messages);die;
		?>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="index.php"></a>Messages
					<span id="notification_count"><?php echo count($messages);?></span>
				</div>
			</div>
		</div>

		<div class="msg_box" style="right:290px">
			<div class="msg_head">Helal Uddin
				<div class="close">x</div>
			</div>
			<div class="msg_wrap">
				<div class="msg_body">
					<?php 
					$messages = $oracleDb->sql_select( 'SELECT * FROM message' );
					foreach( $messages as $message ):
						?>
						<div class="msg_a"> <strong><?php echo $message['AUTHOR']; ?></strong> : <?php echo $message['MESSAGE']; ?> </div>

					<?php endforeach; ?>
				</div>
				<div class="msg_footer"><textarea class="msg_input" id="msg_input" rows="2" placeholder="Write your message here..." ></textarea></div>
			</div>
		</div>
	</form>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
	<script src="js/bootstrap.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>	
	<script src="http://packetcode.com/apps/facebook-like-chat/script.js"></script>

	<script type="text/javascript">
		var socket = io.connect('http://localhost:3005');
		/*console.log('check 1', socket.connected);
		socket.on('connect', function() {
		  console.log('check 2', socket.connected);
		});*/
		/*
		$('#msg_input').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var msg = $( this ).val();
				if(msg != ""){
					// socket.emit( 'message', { name: window.location.host, message: msg } );
					// socket.emit( 'message_count' );
					socket.emit('message', { name: window.location.host, message: msg }, function() {
						socket.emit('message_count');
					});
					//socket.emit('message_count');
					// Ajax call for saving datas
					
					$( this ).val('');
					return false;
				}
			}

		});

		socket.on( 'message', function( data ) {
			$( ".msg_body" ).html( data );
			$('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
		});

		socket.on( 'message_count', function( data ) {
			console.log(`data = ${data}`);
			$( "#notification_count" ).html( data );
		});
		*/
	
		$('#msg_input').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if (keycode == '13') {
				var msg = $(this).val();
				if (msg != "") {
					socket.emit('message', { name: window.location.host, message: msg }, function() {
						// Callback function for the 'message' event
						socket.emit('message_count');
					});
					$(this).val('');
					return false;
				}
			}
		});

		socket.on('message', function(data) {
			$(".msg_body").html(data);
			$('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
			// After handling the 'message' event, emit the 'message_count' event
			socket.emit('message_count');
		});

		socket.on('message_count', function(data) {
			console.log(`data = ${data}`);
			$("#notification_count").html(data);
		});


	</script>
</body>
</html>