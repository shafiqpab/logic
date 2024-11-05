<?php
extract( $_GET );
if( $type == 1 ) {
	date_default_timezone_set('UTC');
	session_start();
	
	$_SESSION['logic_erp']["scr_width"]		= $_GET['width'];
	$_SESSION['logic_erp']["scr_height"]	= $_GET['height'];
	
	header('location: index.php');
}
if( $type == 2 ) {
	date_default_timezone_set('UTC');
	session_start();
	
	$_SESSION["job_no"] = $_GET['job_no'];
}
?> 