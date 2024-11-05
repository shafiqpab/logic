<?php
/******************************************************************
|	Purpose			:	This form will create Fabric Type Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md Helal Uddin
|	Creation date 	:	09-08-2021
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
echo load_html_head_contents("Fabric Construction Entry", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_fabric_type_entry( operation )
	{
		
		if (form_validation('txt_fabric_type_name','Composition Name')==false)
		{
			return;
		}
		else
		{
			/*if(operation==1 || operation==2)
			{
				var update_id=$('#update_id').val();
				var status_id=$('#cbo_status').val();
				var response=trim(return_global_ajax_value( update_id, 'check_fabric_composition', '', 'requires/fabric_type_entry_controller'));
				var response=response.split("_");
				
				if(status_id!=2)
				{
					if(response[0]==1)
					{
							alert("This Fabric composition is already used another page");
							return;
					}
				}
				
			}*/
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_fabric_type_name*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/fabric_type_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_type_entry_reponse;
		}
	}
	
	function fnc_type_entry_reponse()
	{
		if(http.readyState == 4) 
		{  
		
		//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			if(reponse[0]==121)
			{
				alert('Yarn count determination found . Can not be deleted');
				release_freezing();
				return;
			}
			else{
				show_msg(reponse[0]);
				show_list_view('','fabric_construction_list_view','fabric_construction_list_view','requires/fabric_type_entry_controller','setFilterGrid("list_view",-1)');
				reset_form('constructionentry_1','','');
				set_button_status(0, permission, 'fnc_fabric_type_entry',1);
				release_freezing();
			}
			
		}
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="constructionentry_1" id="constructionentry_1"  autocomplete="off">
            <fieldset style="width:550px;"><legend>Fabric Type Entry</legend>
			<table cellpadding="0" cellspacing="2" width="520px">
			 	<tr>
					<td width="130" align="right" class="must_entry_caption">Fabric Type Name</td>
					<td width="160"><input type="text" name="txt_fabric_type_name" id="txt_fabric_type_name" class="text_boxes" style="width:330px" /></td>
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
                        <?php echo load_submit_buttons( $permission, "fnc_fabric_type_entry", 0, 0,"reset_form('constructionentry_1','','','','','')"); ?>						
                    </td>
                </tr>
		   </table>
		</fieldset>	
        <fieldset style="width:550px;">
            <div id="fabric_construction_list_view">
				<?php
                $arr=array (1=>$row_status);
                echo  create_list_view ( "list_view", "Construction Name,Status", "400,100","550","220",0, "select id, fabric_type_name, status_active from lib_fabric_type where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr, "fabric_type_name,status_active", "requires/fabric_type_entry_controller", 'setFilterGrid("list_view",-1);','0,0');
                ?>
            </div>
        </fieldset>
	</form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
