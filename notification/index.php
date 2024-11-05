<?php 
include_once( dirname( __FILE__ ) . '/class/Database.class.php' );
$pdo = Database::getInstance()->getPdoObject();
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
		$query = $pdo->prepare( 'SELECT * FROM message ORDER BY id DESC' );
		$query->execute();						
		$messages = $query->fetchAll( PDO::FETCH_OBJ );
		?>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="index.php"></a>Messages
					<span id="notification_count"><?php echo count($messages);?></span>
				</div>
			</div>
		</div>

		<div class="container hidden">

			<input id="nameInput" type="hidden" class="input-medium" placeholder="Name" />
			<input id="messageInput1" type="text" class="input-xxlarge" placeHolder="Message" />			
			<input type="submit" value="Send" />
			<div>
				<ul id="messages">
					<?php 
					foreach( $messages as $message ):
						?>
						<li> <strong><?php echo $message->author; ?></strong> : <?php echo $message->message; ?> </li>
					<?php endforeach; ?>
				</ul>
			</div>
			<!-- End #messages -->
		</div>

		<div class="msg_box" style="right:290px">
			<div class="msg_head">Jahid Hasan
				<div class="close">x</div>
			</div>
			<div class="msg_wrap">
				<div class="msg_body">
					<?php 
					$query = $pdo->prepare( 'SELECT * FROM message' );
					$query->execute();						
					$messages = $query->fetchAll( PDO::FETCH_OBJ );
					foreach( $messages as $message ):
						?>
						<div class="msg_a"> <strong><?php echo $message->author; ?></strong> : <?php echo $message->message; ?> </div>

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
		var socket = io.connect('http://192.168.11.129:3000');
		/*console.log('check 1', socket.connected);
		socket.on('connect', function() {
		  console.log('check 2', socket.connected);
		});*/
		$('#msg_input').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var msg = $( this ).val();
				if(msg != ""){
					socket.emit( 'message', { name: window.location.host, message: msg } );
					socket.emit( 'message_count' );
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
			$( "#notification_count" ).html( data );
		});

	</script>
</body>
</html>