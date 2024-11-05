<?php
/****************************************************************
|	Purpose			:	This Form Will Create User page permission
|	Functionality	:
|	JS Functions	:
|	Created by		:	Md. Zakaria
|	Creation date 	:	01-03-2020
|	Updated by 		:
|	Update date		:
|	QC Performed BY	:
|	QC Date			:
|	Comments		:
 ******************************************************************/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
}

require_once '../includes/common.php';
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("User Preference", "../", 1, 1, '', 1, '');
include '../includes/field_list_array.php';
?>
	<script type="text/javascript">
		var permission = '<? echo $permission; ?>';
	</script>
	 </head>

 	<body onLoad="set_hotkey()">
    <div align="center">
        <?php echo load_freeze_divs("../", $permission); ?>
