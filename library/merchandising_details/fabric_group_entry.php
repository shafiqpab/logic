<?php
/******************************************************************
|	Purpose			:Client Name-	Asrotex Group
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md Sakibul Islam
|	Creation date 	:	15.10.2023
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:	Taifur Rahman	
|	QC Date			:	
|	Comments		:Table-lib_fabric_group_entry
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Group Entry","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_fabric_group_info(operation)
	{

		if (form_validation('txt_fabric_group_name','Fabric Group')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_fabric_group_name*cbo_status*hidden_fabric_group_id*update_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/fabric_group_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_group_info_response;
		}
	}
function fnc_fabric_group_info_response()
{
	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');	
		show_msg(response[0]);
		show_list_view('','group_list_view','group_list_view','requires/fabric_group_entry_controller','setFilterGrid("list_view",-1)');
		reset_form('fabricGroupEntry_1','','');
		set_button_status(0, permission, 'fnc_fabric_group_info',1);	
		release_freezing(); 
	}

	
}

</script>
</head>
<body  onload="set_hotkey();">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:80%; margin-left:120px;">	
		<form name="fabricGroupEntry_1" id="fabricGroupEntry_1"  autocomplete="off">
            <fieldset style="width:350px;" align="center"><legend>Fabric Group Entry</legend>
            <table cellpadding="0" cellspacing="10" width="350" height="50" align="center">
                <tr>
                
                    <td width="100" align="center" class="must_entry_caption">Fabric Group Name:</td>
                    <td width="150"><input type="text" id="txt_fabric_group_name" name="txt_fabric_group_name" class="text_boxes" style="width:120" />
                    <input type="hidden" id="hidden_fabric_group_id" name="hidden_fabric_group_id" class="text_boxes" />
                    <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>

                </tr>
                <tr>
                	<td align="center" class="must_entry_caption">Status:</td>
                    <td>
						<?
							echo create_drop_down( "cbo_status", 135, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' ); 
						?>
                    </td>
                </tr>
                <tr>
                	<td colspan="4"></td>
                </tr>
                <tr>
                	<td colspan="4" align="center" style="padding-top:10px;" class="button_container">
						<?
							echo load_submit_buttons( $permission, "fnc_fabric_group_info", 0,0 ,"reset_form('fabricGroupEntry_1','','')",1) ; 
						?>
                    </td>
                </tr>
	        	<tr>
					<td colspan="4" align="center" id="group_list_view">
						<?
							$sql="select  fabric_group_name,status_active,id from   lib_fabric_group_entry where is_deleted=0 order by id desc";
							$arr=array (1=>$row_status);
							echo  create_list_view ( "list_view", "Fabric Group Name,Status", "230,120","350","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr , "fabric_group_name,status_active", "../merchandising_details/requires/fabric_group_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
						?>
					</td>
				</tr>

            </table>
            <br>
	 		</fieldset>
            <script> setFilterGrid("table_body2",-1); </script>
    </form>
	
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
