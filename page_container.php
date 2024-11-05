<?
error_reporting(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ERP</title>
	<script type="text/javascript" src="includes/functions.js"></script>
    <script language="JavaScript">
        document.onkeydown = checkKeycode;
        // sandbox="allow-scripts"
    </script>
</head>
<body>
<?php
session_start();
if($_SESSION['menu_id']==$_GET["mid"])
{
	//echo "double"; die;
}

$_SESSION['menu_id']="";
$_SESSION['menu_id']=$_GET["mid"];

$natt=explode("__",$_GET["fnat"]);
$_SESSION['fabric_nature']=isset($natt[1])?$natt[1]:0 ;
$_SESSION['page_title']=isset($natt[0])?$natt[0]:"" ;	
$_SESSION['iso_string']=isset($natt[2])?$natt[2]:"" ;

	$form_loc=$_REQUEST["m"];
	$heigt_scr =  $_SESSION['logic_erp']['scr_height'] - 230;//245

//echo $_SESSION['menu_id']."-------".$_GET["module_id"]."--------".$form_loc;

?>
<iframe style="width:100%; height:<?php echo "$heigt_scr"."px"; ?>" src="<?php echo $form_loc; ?>" iso_string="<?php echo $_SESSION['iso_string']; ?>" align="left" id="page_container">
</iframe>

<?php /*?><iframe id="content_iframe" style="width:100%; height:<?php echo "$heigt_scr"."px"; ?>" src="<?php echo $form_loc; ?>" align="left" id="page_container"></iframe><?php */?>

</body>
 
</html>
