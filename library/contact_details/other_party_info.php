<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//----------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Other Party Info", "../../", 1, 1, $unicode,'','');
 
?>

 
<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';

 
function fnc_other_party_info( operation )
{
	if(operation==2)
		{
	      var delt=confirm('Reday to Delete ?');
		  if(!delt) return;
		}
	
	
	if (form_validation('txt_other_party_name*txt_short_name*txt_contact_person*txt_designation','Other Party Name*Short Name*Contract Person*Designation')==false)
	{
		return;
	}
	else // Save Here
	{
		eval(get_submitted_variables('txt_other_party_name*txt_short_name*txt_contact_person*txt_designation*txt_contact_no*txt_email*txt_web_site*txt_address*cbo_country*txt_remark*cbo_status*update_id'));
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_other_party_name*txt_short_name*txt_contact_person*txt_designation*txt_contact_no*txt_email*txt_web_site*txt_address*cbo_country*txt_remark*cbo_status*update_id',"../../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/other_party_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_other_party_info_reponse;
	}
}

function fnc_other_party_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=http.responseText.split('**');
		if(reponse[0]==50)
		{
			alert (reponse[1]);
			release_freezing();
			return;
		}
		show_msg(trim(reponse[0]));
		show_list_view(reponse[1],'other_party_list_view','other_party_list_view','../contact_details/requires/other_party_info_controller','setFilterGrid("list_view",-1)');
		reset_form('otherpartyinfo_1','','');
		set_button_status(0, permission, 'fnc_other_party_info');
		release_freezing();
	}
} 
 
</script>
</head>	
<body onLoad="set_hotkey()">

<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	
	<fieldset style="width:900px;">
		<legend>Other Party Info</legend>
		<form name="otherpartyinfo_1" id="otherpartyinfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="50%">
					<table  cellpadding="0" cellspacing="2" width="100%">				
						<tr>
							<td width="30%" class="must_entry_caption"> Other Party Name </td>
							<td>
								<input type="text" name="txt_other_party_name" id="txt_other_party_name" class="text_boxes" style="width:250px" />								
							</td>
						</tr>
                        <tr>
							<td width="30%" class="must_entry_caption"> Short Name </td>
							<td>
								<input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:250px" />								
							</td>
						</tr>			
						<tr>
							<td class="must_entry_caption">
								Contact Person
							</td>
							<td>
								<input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:250px" />						
							</td>
						</tr>
                        <tr>
							<td class="must_entry_caption">
								Designation
							</td>
							<td>
								<input type="text" name="txt_designation" id="txt_designation" class="text_boxes" style="width:250px" />						
							</td>
						</tr>				
						<tr>
							<td>
								Contact No
							</td>
							<td>
								<input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes_numeric" style="width:250px" />						
							</td>
						</tr>
						
						
						<tr>
							<td>
								Email
							</td>
							<td>
								<input type="text" name="txt_email" id="txt_email" class="text_boxes" style="width:250px;" maxlength="100" title="Maximum 100 Character" />						
							</td>
						</tr>
						<tr>
							<td>
								http://www.
							</td>
							<td>
								<input type="text" name="txt_web_site" id="txt_web_site" class="text_boxes" style="width:250px" />						
							</td>
						</tr>
					<tr>
											
					</table>
				</td>
				<td width="50%" valign="top">
					<table  cellpadding="0" cellspacing="2" width="100%">
                    
						<td>
							Address
						</td>
						<td>
							<textarea  name="txt_address" id="txt_address" class="text_area"  style="width:250px; "></textarea>
												
						</td>
					</tr>
                    <tr>
							<td>
								Country Name
							</td>
							<td>
								 <? echo create_drop_down( "cbo_country", 262, "select id,country_name from  lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name", 1, "-- Select Country --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?>
                                						
							</td>
                            
						</tr>	
					<tr>
						<td>
							Remark</td>
						<td>
						<textarea name="txt_remark" id="txt_remark" class="text_area"  style="width:250px; "></textarea>
											
						</td>
					</tr>
					
					<tr>
						<td>
							Status
						</td>
						<td>
							<?
						 echo create_drop_down( "cbo_status", 86, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' );
						 ?>
						</td>
					</tr>
                    <tr>
                    <td>
                        Image
                    </td>
                    <td height="25" valign="middle">
                    	<input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'other_party_info', 0 ,1)">
                    </td>
                   </tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">&nbsp;			
					<input type="hidden" name="update_id" id="update_id" >
					
				</td>					
			</tr>
			<tr>
				<td colspan="2" align="center" class="button_container">
					<? 
                        echo load_submit_buttons( $permission, "fnc_other_party_info", 0,0 ,"reset_form('otherpartyinfo_1','','')",1);
                    ?> 
				</td>					
			</tr>				
			</table>
		</form>	
	</fieldset>	
	
	<div style="width:100%;  margin:auto" align="center">
		<fieldset style="width:900px; margin-top:20px">
			<legend>List View</legend>
			<form>
            	<table width="900" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td colspan="3" id="other_party_list_view">
                        <?
						//	id,	other_party_name, short_name, contact_person, designation, email,contact_no, web_site,address,country_id, remark,inserted_by, 	insert_date, updated_by, update_date, status_active,is_deleted
							$arr=array (5=>$row_status);
							echo  create_list_view ( "list_view", "ID,Party Name,Contact Person,Designation,Contact NO,Email,Status", "50,150,150,130,100,180","900","220",0, "select id,  other_party_name,contact_person,designation,contact_no,email,status_active from lib_other_party where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,status_active,0", $arr , "id,other_party_name,contact_person,designation,contact_no,email,status_active", "../contact_details/requires/other_party_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0') ;
							
							 ?>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
