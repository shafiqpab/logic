<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Sample  List
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	04-10-2012
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Information", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_sample_info( operation )
{
	if (form_validation('txt_sample_name*cbo_sample_type','Sample Name*Sample Type')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('txt_sample_name*cbo_sample_type*cbo_business_nature*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sample_name*cbo_sample_type*cbo_invoice_mendatory*cbo_status*cbo_business_nature*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/sample_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_info_reponse;
	}
}

function fnc_sample_info_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'search_list_view','sample_list_view','requires/sample_info_controller','setFilterGrid("list_view",-1)');
		reset_form('sampleinfo_1','','');
		set_button_status(0, permission, 'fnc_sample_info',1);
		release_freezing();
	}
}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
	<fieldset style="width:500px;">
		<legend>Sample Info</legend>
		<form name="sampleinfo_1" id="sampleinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="75%">
			 	<tr>
					<td width="150" class="must_entry_caption">Sample Name</td>
					<td><input type="text" name="txt_sample_name" id="txt_sample_name" class="text_boxes" style="width:190px" maxlength="50" title="Maximum 50 Character"/></td>
				</tr>
                <tr>
					<td class="must_entry_caption">Sample Type</td>
					<td><?=create_drop_down( "cbo_sample_type", 200, $sample_type,"", 1, "-- Select --", 0, "" ); ?></td>
				</tr>
                <tr>
					<td >Invoice Mendatory In Delivery</td>
					<td><?=create_drop_down( "cbo_invoice_mendatory", 200, $yes_no,"", 1, "-- Select --", 0, "" ); ?></td>
				</tr>			
                <tr>
					<td class="must_entry_caption">Business Nature</td>
					<td><?=create_drop_down( "cbo_business_nature", 200, $business_nature_arr,"", 1, "-- Select --", 0, "" ); ?></td>
				</tr>			
				<tr>
					<td>Status</td>
					<td valign="top"><?=create_drop_down( "cbo_status", 200, $row_status,"", "", "", 1, "" ); ?></td>
				</tr>	
			  	<tr>
					 <td colspan="2" align="center">&nbsp;<input type="hidden" name="update_id" id="update_id"></td>					
				</tr>
				<tr>
					<td colspan="2" align="center" class="button_container"><?=load_submit_buttons( $permission, "fnc_sample_info", 0,0 ,"reset_form('sampleinfo_1','','')",1); ?></td>				
				</tr>	
			</table>
		</form>	
	</fieldset>	
	<div style="width:610px;" align="center">
		<fieldset style="width:600px;">
			<legend>Sample Info List</legend>
            	<table width="600" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td id="sample_list_view">&nbsp;</td>
					</tr>
				</table>
		</fieldset>	
	</div>
</div>
</body>
<script>
	show_list_view(0,'search_list_view','sample_list_view','requires/sample_info_controller','setFilterGrid("list_view",-1)');
</script>


<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
