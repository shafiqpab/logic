<?php 
/*.........................................
create by: Md Mahbubur Rahman
Date:21/03/2016
'''''''''''''''''''''''''''''''''''''''''*/
session_start();
if($_SESSION['logic_erp']['user_id']=='') header('location:login.php');
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Buyer Info","../../",1 ,1 ,$unicode,1,'' );
?>
<script>
function fnc_buyer_info(operation)
{
	
	
} 
</script>
</head>
<body onLoad="set_hotkey">
<div align="center" style="width:100%;">
<? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:800px">
    <legend>Contuct Name</legend>
        <form  name="buerifo_1" id="buerifo_1" autocomplete="off">
            <table width="650">
            <fieldset style="width:800px">
                
				
	<?php   

	echo date("r");

	?>

	                                   
                     </table>
            <table width="650">
            	<tr>  
                	<td>&nbsp;</td>
                </tr>
            
            	<tr>  
                	<td colspan="6" align="center" height="40" valign="middle" class="button_container">
					<?php echo load_submit_buttons($permission,"fnc_buyer_info",0,0 ,"reset_form('buerifo_1','','')",1); ?></td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script>set_multiselect('cbo_party_type*cbo_buyer_company','0*0','0','','0*0');</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>