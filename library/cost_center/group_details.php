<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Group List
					
Functionality	:	
				

JS Functions	:

Created by		:	Monzu 
Creation date 	: 	10-10-2012
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
//----------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Group Details", "../../", 1, 1,$unicode,'','');

?>

<script type="text/javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';
function fnc_group_details( operation )
{
	if (form_validation('txt_group_name*txt_group_short','Group Name*Group Short')==false)
	{
		return;
	}
	else // Save Here
	{
		eval(get_submitted_variables('txt_group_name*txt_group_short*txt_contact_person*txt_contact_no*cbo_country_id*txt_website*txt_email*txt_address*txt_remark*cbo_status*update_id'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_group_name*txt_group_short*txt_contact_person*txt_contact_no*cbo_country_id*txt_website*txt_email*txt_address*txt_remark*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/group_details_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_group_details_reponse;
	}
}


function fnc_group_details_reponse()
{
	if(http.readyState == 4) 
	{
		//alert (http.responseText)
		var reponse=trim(http.responseText).split('**');
		show_msg(reponse[0]);
		
		show_list_view(reponse[1],'group_details_view','group_details_view','../cost_center/requires/group_details_controller','setFilterGrid("list_view",-1)');
		reset_form('groupdetail_1','','');
		set_button_status(0, permission, 'fnc_group_details');
		release_freezing();
	}
}

 
 
 
 
 
 
</script>
</head>	





<body onLoad="set_hotkey()">
	<div align="center" style="width:90%; position:relative;  margin-bottom:5px; margin-top:5px">
    	<? echo load_freeze_divs ("../../",$permission);  ?>	     
    		
	<form name="groupdetail_1" id="groupdetail_1" autocomplete="off" enctype="multipart/form-data">
		<fieldset style="width:700px;">
		<legend>Group Information</legend>
			<table width="100%" border="0" cellpadding="0" cellspacing="2" >
                <tr>
                    <td width="130" class="must_entry_caption" align="right">Group Name</td>
                    <td  width="210"><input type="text" name="txt_group_name" id="txt_group_name" class="text_boxes" value="" style="width:200px;" maxlength="50" title="Maximum 50 Character" /></td>
                    <td  width="130" class="must_entry_caption" align="right">Group Short Name</td>
                    <td><input type="text" name="txt_group_short" id="txt_group_short" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/></td>
                </tr>
                <tr>
                    <td align="right">Contact Number</td>
                    <td><input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes_numeric" value="" style="width:200px;"  maxlength="50" title="Maximum 50 Character"/></td>
                    <td align="right">Country</td>
                    <td> 
                    <? echo create_drop_down( "cbo_country_id", 212, "select country_name,id from lib_country where is_deleted=0  and 
                    status_active=1 order by country_name", "id,country_name", 1, '--Select--', 0, $onchange_func  ); ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">Website</td>
                    <td><input type="text" name="txt_website" id="txt_website" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/>                   
                    </td>
                    <td align="right">Email</td>
                    <td><input type="text" name="txt_email" id="txt_email" class="text_boxes" value=""  style="width:200px;" maxlength="32" title="Maximum 32 Character"/></td>
                </tr>
                <tr>	
                    <td valign="top" align="right">Address</td>  
                    <td><textarea name="txt_address" id="txt_address" class="text_area"  style="width:200px;" maxlength="500" title="Maximum 500 Character" ></textarea></td>
                    <td align="right">Remark</td>
                    <td><textarea name="txt_remark" id="txt_remark" class="text_area"  style="width:200px;" maxlength="500" title="Maximum 500 Character"></textarea></td>
                </tr>
                <tr>
                    <td align="right">Status</td>
                    <td><? echo create_drop_down( "cbo_status", 212, $row_status,'', '', '', 1, '' ); ?></td>
                    <td align="right">Contact Person</td>
					<td colspan="2" valign="middle">
                    	<input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" value=""  style="width:200px;" maxlength="50" title="Maximum 50 Character"/>
                    </td>
                </tr>
                <tr>
                    
                   <td colspan="4" align="center">
                    	<div style="width:200px;" class="image_uploader" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'group_logo', 0 ,1,0,0)" align="center"> <strong>CLICK TO ADD IMAGE</strong></div>
                   
                   <input type="hidden" name="update_id" id="update_id"> 
                  </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" class="button_container" >
                    <? 
                        echo load_submit_buttons( $permission, "fnc_group_details", 0,0 ,"reset_form('groupdetail_1','','',1)");
                    ?> 
                    </td>
                  </tr>
                  <tr>
                      <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr>
                      <td colspan="4" id="group_details_view" >
                      <?
						$arr=array (5=>$row_status);
						//print_r($arr);
				echo  create_list_view ( "list_view", "Group Name,Contact Person,Contact No,Website,Address,Status", "170,90,100,130,80","700","220",1
						, "select  id,group_name,contact_person,contact_no,website,address,status_active from lib_group where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, 	"0,0,0,0,0,status_active", $arr, "group_name,contact_person,contact_no,website,address,status_active", "../cost_center/requires/group_details_controller", 'setFilterGrid("list_view",-1);',''); 
                      ?>
                    </td>
                </tr>	
			</table>
		</fieldset>
	</form>	
	
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
