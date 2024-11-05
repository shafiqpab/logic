<?php
		//echo phpinfo(); die;
		session_start();
		extract($_REQUEST);
		if( $_SESSION['logic_erp']['user_id'] == "" ){  header("location: login.php"); }
		 
		$from_menu=1;
		
		
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
	<script type="text/javascript">
	

 	//alert(add_days( "2012-01-08", '6' ))
		<?
	
			if( $_SESSION['logic_erp']['scr_width'] == "" ) { ?>
			window.location.href = "screen.php?width=" + screen.width + "&height=" + screen.height+ "&type=1";
			<?php }
			$heigt_scr = $_SESSION['logic_erp']['scr_height'] -235; // 265
			$width_scr = $_SESSION['logic_erp']['scr_width'] - 255;
		
		?>
		 
		function Button1_onclick( button ) {
			var menu = document.getElementById ('menu_conts');
			var wid = <?php echo $width_scr; ?>;
			var height=<? echo $heigt_scr; ?>;
			
			if( menu.style.display != 'none' ) {
				menu.style.display ='none';
				button.value = 'Show Menu';
				document.getElementById('main_body').style.width='100%';
				
			}
			else {
				menu.style.display ='block';
				button.value = 'Hide Menu'
				document.getElementById('main_body').style.width=wid+"px";
				
			}
		}
		
		function open_calculator()
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'calculator.php', "~~~ Logic Calculator ~~~", 'width=320px,height=320px,center=1,resize=0,scrolling=0','home');
		}
		function full_screen()
		{
			var docElm = document.documentElement;
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            }
            else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            }
            else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            }
		}
		
	</script>
    
</head>

<body class="bodys">
    <div style="width:100%; height:100%;">
    
        <table width="100%" height="99%" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td colspan="2" height="40" valign="middle" id="top_panel">
						<div class="top_container" align="center">
                    		<div style="width:300px; height:100%; float:left; ">
                             	<img src="images/header/header_logo.jpg" alt="" width="50" height="40"align="Left">
                                <img src="images/header/header_name.jpg" alt="" width="200" height="40"align="Left"> 
                        		<!-- <a href="##" onclick="full_screen()" class="sp_tools"> Full Screen </a> &nbsp;&nbsp;&nbsp;<a href="##" onclick="open_calculator()" class="sp_tools"> Calculator </a> -->
                        		<input type="hidden" name="index_page" id="index_page" value="1" />
                          </div>
                          <div style="float:left; margin-left:100px;">  
                               <div id="messagebox_main" class="messagebox" style="width:350px;"></div>
						  </div>
						  <div style="width:450px; height:40px; float:right;">
							<table width="100%" height="100%">
								<tr>
                                	<!--<td width="100px" valign="middle" align="right" style="font-size:14px;">
                                      <strong>User&nbsp;:</strong>&nbsp;<? //echo $_SESSION['logic_erp']['user_name']; ?>
                                     </td>-->
									<td valign="middle" align="right">
                                       <b>Login By: </b> <? echo $_SESSION['logic_erp']['user_name']; ?>&nbsp;&nbsp;
                                    </td>
									<td width="110" valign="middle" align="center" style="border-left:1px; border-left-style:solid;">
                                    &nbsp;&nbsp;<a href="logout.php" style="text-decoration:none">
                                        <img src="images/logic/Logout.png" width="100" height="30" /></a>
									</td>
                                    <td width="250" align="right" valign="middle" style="border-left:1px; border-left-style:solid;">
									&nbsp;&nbsp;<img src="images/logic/Platfrom.png" width="240" height="30" />
                                    </td>
								</tr>
							</table>
						</div>
					</div>
				</td>
			</tr>
            
			<tr>
				<td colspan="2" height="30">
					<div class="top_module_container" align="center">
						<div style="position:absolute; height:25px; width:80px; left:5px; top:5px">
							<input id="Button1" type="button" value="Hide Menu" language="javascript" style="height:25px; width:75px; border-radius:8px; background:#C2DCFF; color:#000; background-image: -moz-linear-gradient(bottom, rgb(255,255,255)  7%, rgb(136,170,214) 10%, rgb(255,255,255) 96%);" onClick="return Button1_onclick(this)" />
						</div>
						<div class="idTabs" style="margin-left:223px;">
							<ul>
								<?php
								$user_id	= $_SESSION['logic_erp']["user_id"];
								$module_id	= $_GET['module_id'];
								foreach( $_SESSION['logic_erp']['user_menu'] AS $module ) {
								?>
								<li <?php if( $module['module_id'] == $module_id ) echo "class=\"active\""; ?>>
									<a <?php if( $module['module_id'] == $module_id ) echo "class=\"active\""; ?> href="index.php?module_id=<?php echo $module['module_id']; ?>"><?php echo $module['module_name']; ?></a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100%" height="<?php echo $heigt_scr; ?>" valign="top" class="body1">
					<div style="width:100%; vertical-align:top; min-height:<?php echo "$heigt_scr"."px"; ?>; max-height:<?php echo "$heigt_scr"."px"; ?>">
						<div id="menu_conts" style="margin-top:5px; width:230px; height:<?php echo "$heigt_scr"."px"; ?>; float:left; z-index:-1 ; overflow-y:scroll;overflow-x:hidden ">
							 <? include('left_menu.php'); ?>
						</div>
						<div id="main_body" style="width:<?php echo "$width_scr"."px"; ?>; float:left; height:<?php echo "$heigt_scr"."px"; ?>;" align="center">
							<?php  /// include("barsOrColumns.html") ;
							
							if( $_SESSION['logic_erp']['user_id'] != "" )
							{
								if($g==1)
									include("dash_board.php");
								else if($g==2)
									include("today_production_graph.php");	
								else if($g==3)
									include("trend_monthly_graph.php");	
								else if($g==4)
									include("trend_daily_graph.php");
								/*else if($g==5)
									include("graph.php");*/			
								else
									//include("home_dash_board_circle.php");
									include("home_page_kaiyum.php");	
							}
								//include("graph_new.php");
							?>
						</div>
					</div>
				</td>
			</tr>
		</table>
         
	</div>
</body>
</html>

<?
/*
if( mail("smbsintl@gmail.com","Work Order Report","Test Body",$headers))
	echo "sumon yes";
else
	echo "sumon no";
 */

?>