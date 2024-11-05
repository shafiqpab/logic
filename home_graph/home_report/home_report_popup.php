<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="ex_factory_popup")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,$multi_select,1);
	extract($_REQUEST);
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
?>
	<script>
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    </head>
	<body>
    <div align="center">
    <br>
        <fieldset style="width:200px;">
            <table align="center" width="200" cellpadding="0" cellspacing="0">
             	<tr>
                    <td width="130" colspan="2">&nbsp; </td>
                </tr>
                <tr>
                    <td width="30"><b>Year : </b></td>
                    <td width="100"><input type="text" style="width:100px;" name="txt_year" id="txt_year" class="text_boxes_numeric" maxlength="4"  value="<? echo date('Y'); ?>" /></td>
                </tr>
                <tr>
                    <td width="130" colspan="2">&nbsp; </td>
                </tr>
            </table>
             
            <div style="width:200px;" align="center">
            	<input type="button" name="close" class="formbutton" value="Generate" id="main_close" onClick="fnc_close();" style="width:100px" />
            </div>
        </fieldset>
    </div>
    </body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html> 
<script>
	

$(document).ready(function() {
    $('#txt_year').select();
});


</script> 
<?
exit();
}


if($action=="hourly_production_monitoring_report_popup")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,$multi_select,1);
	extract($_REQUEST);
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
?>
	<script>
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    </head>
	<body>
    <div align="center">
    <br>
        <fieldset style="width:260px;">
            <table align="center" width="260" cellpadding="3" cellspacing="3">
                <tr>
                	<td width="80" align="right"><strong>Production Date</strong></td>
                    <td><input id="txt_date_from" class="datepicker" type="text" value="<? echo date('d-m-Y'); ?>" placeholder="From Date" style="width:160px" name="txt_date_from"></td>
                </tr>
                <tr>
                    <td align="right"><strong>Efficiency %</strong></td>
                    <td><input type="text" style="width:160px;" name="txt_efficiency_per" id="txt_efficiency_per" class="text_boxes_numeric" maxlength="4"  value="60" /></td>
                </tr>
            </table>
             
            <div style="width:200px;" align="center">
            	<input type="button" name="close" class="formbutton" value="Generate" id="main_close" onClick="fnc_close();" style="width:100px" />
            </div>
        </fieldset>
    </div>
    </body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html> 
<script>
	

$(document).ready(function() {
    $('#txt_year').select();
});


</script> 
<?
exit();
}



function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}

?>