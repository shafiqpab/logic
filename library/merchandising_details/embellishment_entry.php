<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create embellishment Name Array
					
Functionality	:	
				

JS Functions	:

Created by		:	Md. Saidul Islam REZA 
Creation date 	: 	01-12-2020
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
echo load_html_head_contents("Embellishment Name", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function fnc_embellishment_entry( operation )
{
	if (form_validation('txt_emb_name*cbo_emb_type','Emb. Name*Emb Type')==false)
	{
		return;
	}
	else
	{
		//eval(get_submitted_variables('txt_emb_name*cbo_emb_type*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_emb_name*cbo_emb_type*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/embellishment_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_embellishment_entry_reponse;
	}
}

function fnc_embellishment_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		get_emb_list(document.getElementById('cbo_emb_type').value);
		
		reset_form('sampleinfo_1','','');
		set_button_status(0, permission, 'fnc_embellishment_entry',1);
		release_freezing();
	}
}


function get_emb_list(type){
	show_list_view(type,'search_list_view','emb_list_view','../merchandising_details/requires/embellishment_entry_controller','setFilterGrid("list_view",-1)');
	document.getElementById('txt_emb_name').value='';
	document.getElementById('update_id').value='';
	set_button_status(0, permission, 'fnc_embellishment_entry',1);
}


</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
    
	<fieldset style="width:500px;">
		<legend>Embellishment Info</legend>
		<form name="sampleinfo_1" id="sampleinfo_1"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="75%">
			 	<tr>
					<td width="109" class="must_entry_caption">Embellishment Name</td>
					<td colspan="3">
						<input type="text" name="txt_emb_name" id="txt_emb_name" class="text_boxes" style="width:228px" maxlength="50" title="Maximum 50 Character"/>
					</td>
				</tr>
                <tr>
					<td width="109" class="must_entry_caption">Emb. Type</td>
					<td colspan="3">
						 <?
                           echo create_drop_down( "cbo_emb_type", 240, $emblishment_name_array,"", 1, "-- Select --", 0, "get_emb_list(this.value)","","" );
						 ?>
                         
					</td>
				</tr>			
				<tr >
					<td width="109">Status</td>
					<td valign="top" width="107" colspan="3">
                    	<?
                        echo create_drop_down( "cbo_status", 110, $row_status,"", "", "", 1, "" );
						?> 
					</td>
					 
				</tr>	
			  	<tr>
					 <td colspan="4" align="center">&nbsp;						
						<input type="hidden" name="update_id" id="update_id">
					</td>					
				</tr>
				<tr>
					<td colspan="4" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_embellishment_entry", 0,0 ,"reset_form('sampleinfo_1','','')",1);
				        ?> 
					</td>				
				</tr>	
			</table>
		</form>	
	</fieldset>	
	<div style="width:650px;" align="center">
		<fieldset style="width:500px;">
			<legend>Emb. Info List</legend>
			 
            	<table width="470" cellspacing="2" cellpadding="0" border="0">
                     
					<tr>
						<td colspan="3" id="emb_list_view"></td>
					</tr>
				</table>
			 
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
