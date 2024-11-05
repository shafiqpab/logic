<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	20-02-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Info","../", 1, 1, $unicode,'','');

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 
 
function fn_report_generated( operation )
{
	if( form_validation('cbo_variable_list*txt_date_from*txt_date_to','Manually Data Pulled*Date Range*Date Range')==false )
	{
		return;
	}	
	else
	{
		var data="action=report_generate"+get_submitted_data_string('cbo_variable_list*txt_date_from*txt_date_to',"../");
		freeze_window(3);
		http.open("POST","requires/rms_data_sinconization_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	 
function fn_report_generated_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText;
		$("#variable_settings_container").html(reponse);
		release_freezing();return;
		
		release_freezing();
	}
}

</script>
</head>
<body  onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
         
		<?php echo load_freeze_divs ("../../",$permission);  ?>
        
        <fieldset style="width:500px;">
            <legend>Auto Data Puller</legend>
            <form name="productionVariableSettings" id="productionVariableSettings" >
                    <table  width="500px" cellpadding="0" border="0">
                    	<tr>
                            <td width="120" align="right" class="must_entry_caption">Date Range &nbsp;&nbsp;</td>
                            <td width="200">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >&nbsp; To
                   				<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date"  >
                            </td>
                        </tr>
                        <tr>
                            <td width="120" align="right" class="must_entry_caption">Manually Data Pulled &nbsp;&nbsp;</td>
                            <td width="200">
                                 <?php 
								 	$process_arr=array(1=>"Knitting Production",2=>"Delivery to Store");
                                    echo create_drop_down( "cbo_variable_list", 180, $process_arr,'', '1', '---- Select ----', '',"",'',''); 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="120" align="center" class="must_entry_caption"></td>
                          
                            <td width="200">
                                <input type="button" value="Process" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/>
                            </td>
                        </tr>
                    </table>
				<div style="width:550px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>
            </form>
        </fieldset>
	</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>