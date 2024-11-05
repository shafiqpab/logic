<?php
error_reporting(0);
session_start();
extract($_REQUEST);

if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location: login.php");
}
$from_menu = 1;

list($sss, $serverurl) = explode("/", $_SERVER['REQUEST_URI']);

if ($_SESSION['project_url'] == '') {
	$_SESSION['project_url'] = $serverurl;
} else {
	if ($_SESSION['project_url'] != $serverurl)
		header("location: logout.php");
}

$user_id = $_SESSION['logic_erp']['user_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<LINK REL="SHORTCUT ICON" HREF="images/logic_logo.png">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Logic ERP Solution</title>

	<link href="css/style_common.css" rel="stylesheet" type="text/css" />
	<script src="js/ajaxpage_loader.js" type="text/javascript"></script>
	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="includes/functions.js" type="text/javascript"></script>
	<link href="css/modal_window.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/modal_window.js"></script>

	<!-- Include SweetAlert2 CSS -->
	<link rel="stylesheet" href="sweetalert2/sweetalert2.css">
	<!-- Include SweetAlert2 JavaScript -->
	<script  type="text/javascript" src="sweetalert2/sweetalert2.all.js"></script>
	<script src="js/socket.io.js"></script>
	<style>
		/* Generic context menu styles */
		.contextMenu {
			position: absolute;
			width: 225px;
			z-index: 99999;
			border: solid 1px #CCC;
			background: #EEE;
			padding: 0px;
			margin: 0px;
			display: none;
		}

		.contextMenu LI {
			list-style: none;
			padding: 0px;
			margin: 0px;
		}

		.contextMenu A {
			color: #333;
			text-decoration: none;
			display: block;
			line-height: 25px;
			height: 25px;
			background-position: 6px center;
			background-repeat: no-repeat;
			outline: none;
			padding: 1px 5px;
			padding-left: 10px;
		}

		.contextMenu LI.hover A {
			color: #FFF;
			background-color: #3399FF;
		}

		.contextMenu LI.disabled A {
			color: #AAA;
			cursor: default;
		}

		.contextMenu LI.hover.disabled A {
			background-color: transparent;
		}

		.contextMenu LI.separator {
			border-top: solid 1px #CCC;
		}

		.btn {
			display: inline-block;
			padding: 6px 10px;
			font-size: 16px;
			text-align: center;
			text-decoration: none;
			cursor: pointer;
			border-radius: 4px;
			border: none;
			transition: background-color 0.3s ease-in-out;
		}

		.btn-primary {
			background-color: #007bff;
			color: #fff;
		}

		.btn-secondary {
			background-color: red;
			color: #fff;
		}

		.btn:hover {
			background-color: #0056b3;
		}

		.item-list {
			display: flex;
			flex-wrap: wrap;
			cursor:pointer;
		}

		.row {
			width: 100%;
			display: flex;
			justify-content: space-between;
		}

		.col {
			flex-basis: calc(50%);
			padding: 10px;
			box-sizing: border-box;
			cursor:pointer;
		}
		table, th, td {
			border-collapse: collapse;
		}
		.head{
			background-color:#ffff;
			text-align:left; 
			font-size:14px; 
			padding:10px 0px 10px 10px; 
			border-bottom:2px solid #88AAD6
		}
		.content {
			text-align: left;
			padding: 10px 10px;
			border-bottom: 1px solid #4076B2;
			cursor: pointer;
			border-top: 1px solid #fff;
			border-left:4px solid;
			border-color: rgba(111,111,111,0.2) transparent transparent;
		}
		.content:hover{ border-left:4px solid #171717;transition:.5s ease}
		#rightSidebar {
			display: none;
			position: fixed;
			top: 0;
			right: 0;
			height: 100%;
			width: 50%;
			z-index: 9999;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
			transition: transform 0.3s ease-in-out;
			transform: translateX(100%);
		}

		#rightSidebar.show {
			display: block;
			transform: translateX(0);
		}

		#sidebarCloseButton {
			position: absolute;
			top: 10px;
			left: -37px;
			top: 5px;
		}

		/* Add your custom styles for the background here */
		#rightSidebar.show {
			background-color: rgba(255,255,255,.96);
			color: black;
		}

		#contend_break_down_list_view{
			position: fixed;
			height: 98%;
			left: 0;
			width:62%;
			padding:5px;
			background-image:-moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
		}
		#contend_main_list_view{
			position: fixed;
			height: 98%;
			right: 0;
			width:35%;
			padding:5px;
			border-left:1px solid #ccc;
			box-shadow:3px 0 13px -9px rgba(0,0,0,.3) inset;
			background:rgba(237,242,249,.5);
		}
		.not-badge-success,.not-badge-warning
		{
			padding: 0 4px 1px 4px;
			border-radius: 6px;
		}
		.not-badge-success {
			background-color: rgba(24,170,62,.6);
			border:1px solid #198754;
		}
		.not-badge-warning {
			background-color: rgba(255,202,44,.6);
			border:1px solid #FFCA2C;
		}
		.margin-bottom-5{ margin-bottom:5px;}
		.time-ago{ font-style:italic; color:#444; }
		.notification-list{ text-decoration:none;color:#000;}
		.notification-list:hover{ color:#171717}
	</style>


	<script type="text/javascript">
		var socket;
		var login_user_id;
		var socket_url = '<?=$_SESSION['socket_url'];?>';
		$( document ).ready(function() {
			try {
				login_user_id = '<?=$user_id?>';
				if(socket_url !="")
				{
					socket =io.connect('<?=$_SESSION['socket_url'];?>');
					socket.on( 'message_count', function( data ) {
						if(data.user_id == login_user_id)
						{
							console.log(`data = ${data.notification}`);
							//document.getElementById("message_count_show").text = data;
							//showNotification(`Total Approval found: ${data}`);
							$(".badge").text(`${data.notification}`);
							$(".badge").css("display","block");
							loadRightPanel();
						}
						//document.getElementById("notification_count_top").text = data;
					});
					
					socket.emit('message_count', { user_id: '<?=$_SESSION['logic_erp']['user_id'];?>'});
					socket.on('message',function (approval_notification_data) {
						var user_data = [];
						approval_notification_data.forEach(noti_data=>{
							console.log(noti_data.user_id, '==', login_user_id);
							if(noti_data.user_id == login_user_id)
							{
								showNotification(noti_data.desc);
							}
						})
					});
					socket.on('unapproved_req',function (approval_notification_data) {
						var user_data = [];
						approval_notification_data.forEach(noti_data=>{
							console.log(noti_data.user_id, '==', login_user_id);
							if(noti_data.user_id == login_user_id)
							{
								showNotification(noti_data.unapprove_request);
							}
						})
					});
					showNotificationToNewLoginUser();
				}
			}
			catch (error)
			{
				console.error("Unable to connect to the server:", error);
				//showNotification(error,'error');
			}
		});

		function showNotificationToNewLoginUser()
		{	
			
			if(localStorage.getItem('login_notification')=='')
			{
				console.clear();
				localStorage.setItem('login_notification','Logged');
				socket.emit('login', { userId: login_user_id });
				//console.log('chat_message',login_user_id);
				//socket.emit('chat_message', { chat_message_id: '',from_user_id: '',to_user_id:login_user_id }, function() {});
			}
		}

		function loadRightPanel()
		{
			var rightsidebar = document.getElementById("rightSidebar");
			if( rightsidebar.style.display != 'none' ) 
			{
				var dtls_id = document.getElementById('hidden_dtls_page_id').value;
				var module_id = document.getElementById('hidden_module_id').value ;
				loadNotification();
				if(module_id != "")
				{
					load_sub_manu(module_id);
				}
				if(dtls_id!='')
				{
					load_break_down_manu(module_id,dtls_id);
				}
			}
		}

		<?
		if ($_SESSION['logic_erp']['scr_width'] == "") { ?>
			window.location.href = "screen.php?width=" + screen.width + "&height=" + screen.height + "&type=1";
		<?php }
		$heigt_scr = $_SESSION['logic_erp']['scr_height'] - 235; // 265
		$width_scr = $_SESSION['logic_erp']['scr_width'] - 255;
		?>

		function Button1_onclick(button) {
			var menu = document.getElementById('menu_conts');
			var wid = <?php echo $width_scr; ?>;
			var height = <? echo $heigt_scr; ?>;

			if (menu.style.display != 'none') {
				menu.style.display = 'none';
				button.value = 'Show Menu';
				document.getElementById('main_body').style.width = '100%';

			} else {
				menu.style.display = 'block';
				button.value = 'Hide Menu'
				document.getElementById('main_body').style.width = wid + "px";

			}
		}




		function open_calculator() {
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'calculator.php', "~~~ Logic Calculator ~~~", 'width=320px,height=320px,center=1,resize=0,scrolling=0', 'home');
		}

		function full_screen() {
			var docElm = document.documentElement;
			if (docElm.requestFullscreen) {
				docElm.requestFullscreen();
			} else if (docElm.mozRequestFullScreen) {
				docElm.mozRequestFullScreen();
			} else if (docElm.webkitRequestFullScreen) {
				docElm.webkitRequestFullScreen();
			}
		}

		$(document).ready(function() {
			$("#sidebarButton").click(function() {
				$("#rightSidebar").addClass("show");
				loadNotification();
			});

			$("#sidebarCloseButton").click(function() {
				$("#rightSidebar").removeClass("show");
				// document.getElementById('contend_main_list_view').innerHTML = '';
				// document.getElementById('contend_details_list_view').innerHTML = '';
				// document.getElementById('contend_break_down_list_view').innerHTML = '';
				// document.getElementById('hidden_dtls_page_id').value = '';
				// document.getElementById('hidden_module_id').value = '';
			});
		});

		function loadNotification()
		{
			var url = "notification_list.php?action=main_list";
			fetch(url)
			.then((response) => {
				return response.text();
			})
			.then((html) => {
				document.getElementById('contend_main_list_view').innerHTML = html;
			});
		}

		function load_sub_manu(module_id)
		{
			return;
			var url = "notification_list.php?action=get_dtls_view&data="+module_id;
			fetch(url)
			.then((response) => {
				return response.text();
			})
			.then((html) => {
				document.getElementById('contend_details_list_view').innerHTML = html;
				document.getElementById('hidden_module_id').value = module_id;
			});
			load_break_down_manu(module_id);
		}
		function load_break_down_manu(dtls_id)
		{
			var url = "notification_list.php?action=get_break_down_list_view&data="+dtls_id;
			fetch(url)
			.then((response) => {
				return response.text();
			})
			.then((html) => {
				document.getElementById('contend_break_down_list_view').innerHTML = html; 
				document.getElementById('hidden_dtls_page_id').value = dtls_id;
			});
		}
	</script>

</head>

<body class="bodys">
	<div style="width:100%; height:100%;">

		<table width="100%" height="99%" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td colspan="2" height="55" valign="middle" id="top_panel">
					<div class="top_container" align="center">
						<div style="width:300px; height:100%; float:left; ">
							<img src="<?= $_SESSION['logic_erp']["group_logo"]; ?>" alt="Logic Logo" width="auto" height="45" align="Left" style="margin:3px;">
							<input type="hidden" name="index_page" id="index_page" value="1" />
						</div>
						<div style="float:left; margin-left:100px;">
							<div id="messagebox_main" class="messagebox" style="width:350px;"></div>
						</div>
						<div style="width:450px; height:45px; float:right;">
							<?
							include('top_right.php');
							?>
						</div>
					</div>
				</td>
			</tr>

			<tr>
				<td colspan="2" height="30">
					<div class="top_module_container" align="center">
						<div style="position:absolute; height:25px; width:170px; left:5px; top:5px">

							<input id="Button1" type="button" value="Hide Menu" language="javascript" style="height:25px; width:75px; border-radius:5px; background:#C2DCFF; color:#000; background-image: -moz-linear-gradient(bottom, rgb(255,255,255)  7%, rgb(136,170,214) 10%, rgb(255,255,255) 96%);" onClick="return Button1_onclick(this)" /> &nbsp; &nbsp;<input id="view-fullscreen" type="hidden" value="Full Screen" style="height:25px; width:75px; border-radius:8px; background:#C2DCFF;  color:#000; background-image: -moz-linear-gradient(bottom, rgb(255,255,255)  7%, rgb(136,170,214) 10%, rgb(255,255,255) 96%);" />


						</div>
						<div class="idTabs" style="margin-left:223px;">
							<ul>
								<?php
								$user_id	= $_SESSION['logic_erp']["user_id"];
								$module_id	= $_GET['module_id'];
								foreach ($_SESSION['logic_erp']['user_menu'] as $module) {
								?>
									<li <?php if ($module['module_id'] == $module_id) echo "class=\"active\""; ?>>
										<a <?php if ($module['module_id'] == $module_id) echo "class=\"active\""; ?> href="index.php?module_id=<?php echo $module['module_id']; ?>"><?php echo $module['module_name']; ?></a>
									</li>
								<?php }

								?>
								<!-- <li>
									<a   href="index.php?module_id=ewew">Sample Development</a>
								</li> -->
							</ul>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100%" height="<?php echo $heigt_scr; ?>" valign="top" class="body1">
					<div style="width:100%; vertical-align:top; min-height:<?php echo "$heigt_scr" . "px"; ?>; max-height:<?php echo "$heigt_scr" . "px"; ?>">
						<div id="menu_conts" style="margin-top:5px; width:230px; height:<?php echo "$heigt_scr" . "px"; ?>; float:left; z-index:-1 ; overflow-y:scroll;overflow-x:hidden;">
							<? include('left_menu.php'); ?>
						</div>
						<div id="main_body" style="width:<?php echo "$width_scr" . "px"; ?>; float:left; height:<?php echo "$heigt_scr" . "px"; ?>;" align="center">
							<?php

							if ($_SESSION['logic_erp']['user_id'] != "") {
								include('home_graph/dashborad/graph_index.php');
							}

							?>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<aside id="rightSidebar">
			<div id="contend_break_down_list_view">
				<div  class="head">▣ All Notifications</div>
				<h3 style="text-align:center; margin-top:20px;">No notification available.</h3>
			</div>
			<div id="contend_main_list_view"></div>
			<button id="sidebarCloseButton" class="btn btn-secondary">&times;</button>
			<input type="hidden" id="hidden_module_id">
			<input type="hidden" id="hidden_dtls_page_id"> 
			
		</aside>
	</div>
</body>
<script>
	(function() {
		var viewFullScreen = document.getElementById("view-fullscreen");
		if (viewFullScreen) {
			viewFullScreen.addEventListener("click", function() {
				var docElm = document.documentElement;
				if (docElm.requestFullscreen) {
					docElm.requestFullscreen();
				} else if (docElm.msRequestFullscreen) {
					docElm.msRequestFullscreen();
				} else if (docElm.mozRequestFullScreen) {
					docElm.mozRequestFullScreen();
				} else if (docElm.webkitRequestFullScreen) {
					docElm.webkitRequestFullScreen();
				}
			}, false);
		}


	})();


	$("#logininfo").contextMenu({menu: 'myMenu'},function(action, el, pos) {});
	// $("#view-fullscreen").trigger('click');
</script>
</html>

<? //include_once('firebug_user_control.php'); ?>

 