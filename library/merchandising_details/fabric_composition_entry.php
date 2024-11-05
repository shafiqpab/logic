<?php
/******************************************************************
|	Purpose			:	This form will create Fabric Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Rakib
|	Creation date 	:	20.12.2020
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
echo load_html_head_contents("Fabric Composition Entry", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_fabric_composition_entry( operation )
	{
		if(operation!=0){
		var update_id = document.getElementById('update_id').value;
		var response=return_global_ajax_value( update_id, 'is_used_fabric_composition', '', 'requires/fabric_composition_entry_controller');
		if(response == 1){
			alert("Update or Delete restricted ! Because this Composition used in Another Field.");
			return;
		}

    }
		if (form_validation('txt_fabric_composition_name','Fabric Composition Name')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_fabric_composition_name*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/fabric_composition_entry_controller.php",true);
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
			if(reponse[0]==13)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_list_view('','fabric_composition_list_view','fabric_composition_list_view','requires/fabric_composition_entry_controller','setFilterGrid("list_view",-1)');
			reset_form('compositionentry_1','','');
			if(reponse[0]==1 || reponse[0]==0)
			{
				$('#txt_fabric_composition_name').removeAttr('disabled','disabled');
				$('#cbo_status').removeAttr('disabled','disabled');
			}
			set_button_status(0, permission, 'fnc_fabric_composition_entry',1);
			release_freezing();
		}
	}
	
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="compositionentry_1" id="compositionentry_1"  autocomplete="off">
            <fieldset style="width:550px;"><legend>Fabric Composition Entry</legend>
			<table cellpadding="0" cellspacing="2" width="520px">
			 	<tr>
					<td width="130" align="right" class="must_entry_caption">Fabric Composition Name</td>
					<td width="160"><input type="text" name="txt_fabric_composition_name" id="txt_fabric_composition_name" class="text_boxes" style="width:330px" /></td>
                </tr>
                <tr>
                    <td align="right">Status</td>
                    <td><? echo create_drop_down( "cbo_status", 340, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 ); ?></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;<input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding-top:10px;" class="button_container">
                        <?php echo load_submit_buttons( $permission, "fnc_fabric_composition_entry", 0, 0,"reset_form('compositionentry_1','','','','','')"); ?>						
                    </td>
                </tr>
		   </table>
		</fieldset>	
        <fieldset style="width:550px;">
            <div id="fabric_composition_list_view">
				<?php
                $arr=array (1=>$row_status);
                echo  create_list_view ( "list_view", "Fabric Composition Name,Status", "400,100","550","220",0, "select id, fabric_composition_name, status_active from lib_fabric_composition where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "fabric_composition_name,status_active", "requires/fabric_composition_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
                ?>
            </div>
        </fieldset>
	</form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
