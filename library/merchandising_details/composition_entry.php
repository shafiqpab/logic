<?php
/******************************************************************
|	Purpose			:	This form will create Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	05.07.2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Composition Entry", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_composition_entry( operation )
	{
		if(operation!=0)
		{
			var update_id = document.getElementById('update_id').value;
			var response=return_global_ajax_value( update_id, 'is_used_composition', '', 'requires/composition_entry_controller');
			if(response == 1)
			{	
				// const cb = document.querySelector('#chk_fabric');
				// alert(cb.checked);
				// if(cb.checked==false)
				// {
					alert("Update or Delete restricted ! Because this Composition used in Another Field.");
					return;
				//}
			}
    	}
		if (form_validation('txt_composition_name','Composition Name')==false)
		{
			return;
		}
		else
		{
			// if(operation==1 || operation==2)
			// {
			// 	var update_id=$('#update_id').val();
			// 	var status_id=$('#cbo_status').val();
			// 	var response=trim(return_global_ajax_value( update_id, 'check_composition', '', 'requires/composition_entry_controller'));
			// 	var response=response.split("_");
				
			// 	if(status_id!=2)
			// 	{
			// 		if(response[0]==1)
			// 		{
			// 				alert("This composition is already used another page");
			// 				return;
			// 		}
			// 	}
				
			// }
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_composition_name*txt_composition_short_name*cbo_yarn_type*cbo_status*update_id*chk_fabric',"../../");
			freeze_window(operation);
			http.open("POST","requires/composition_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_composition_entry_reponse;
		}
	}
	
	function fnc_composition_entry_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse = trim(http.responseText).split('**');
			show_msg(reponse[0]);
			show_list_view('','composition_list_view','composition_list_view','requires/composition_entry_controller','setFilterGrid("list_view",-1)');
			reset_form('compositionentry_1','','');
			if(reponse[0]==1 || reponse[0]==0)
		{
			$('#txt_composition_name').removeAttr('disabled','disabled');
			$('#txt_composition_short_name').removeAttr('disabled','disabled');
			$('#cbo_yarn_type').removeAttr('disabled','disabled');
			$('#cbo_status').removeAttr('disabled','disabled');
			$('#chk_fabric').removeAttr('disabled','disabled');
		}
			set_button_status(0, permission, 'fnc_composition_entry',1);
			release_freezing();
		}
	}

	
	function fnc_chk_fabric(type)
	{
		if(type==1)
		{
			if(document.getElementById('chk_fabric').checked==true)
			{
				document.getElementById('chk_fabric').value=1;
			}
			else if(document.getElementById('chk_fabric').checked==false)
			{
				document.getElementById('chk_fabric').value=2;
			}
		}
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="compositionentry_1" id="compositionentry_1"  autocomplete="off">
            <fieldset style="width:550px;"><legend>Composition Entry</legend>
			<table cellpadding="0" cellspacing="2" width="520px">
			 	<tr>
					<td width="130" align="right" class="must_entry_caption">Composition Name</td>
					<td width="160"><input type="text" name="txt_composition_name" id="txt_composition_name" class="text_boxes" style="width:330px" /></td>
                </tr>
				<tr>
					<td width="130" align="right">Composition Short Name</td>
					<td width="160"><input type="text" name="txt_composition_short_name" id="txt_composition_short_name" class="text_boxes" style="width:330px" /></td>
                </tr>
				<tr>
					<td  align="right" >Category </td>
					<td valign="top" >
						<?
							echo create_drop_down( "cbo_yarn_type", 340, $yarn_type_for_entry,"", 1, "--Select--", 0, "", "", "198,208,436,485" );
						?>	
					</td>
				</tr>
                <tr>
                    <td align="right">Status</td>
                    <td><? echo create_drop_down( "cbo_status", 150, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 ); ?> &nbsp;
					<label for="vehicle1">Fabric Composition</label>
					<input type="checkbox" id="chk_fabric"  name="chk_fabric" 
					onClick="fnc_chk_fabric(1);" value="2"></td>
                </tr>
	
                <tr>
                    <td colspan="2">&nbsp;<input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding-top:10px;" class="button_container">
                        <?php echo load_submit_buttons( $permission, "fnc_composition_entry", 0, 0,"reset_form('compositionentry_1','','','','disable_enable_fields(\'txt_composition_name*txt_composition_short_name*cbo_yarn_type*cbo_status*chk_fabric\')','')"); ?>					
                    </td>
                </tr>
		   </table>
		</fieldset>	
        <fieldset style="width:550px;">
            <div id="composition_list_view">
				<?php
                $arr=array (1=>$yarn_type_for_entry,2=>$row_status,3=>$yes_no);
                echo  create_list_view ( "list_view", "Composition Name,Category,Status,Fabric/Yarn Status", "400,100,100,100","750","220",0, "select id, composition_name, yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,yarn_category_type,status_active,is_fabric,0", $arr, "composition_name,yarn_category_type,status_active,is_fabric", "requires/composition_entry_controller", 'setFilterGrid("list_view",-1);','0,0,0');
                ?>
            </div>
        </fieldset>
	</form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
