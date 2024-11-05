<?php
/****************************************************************
|	Purpose			:	This Form Will Create User Preference
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Jwel 
|	Creation date 	:	11-29-2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
******************************************************************/

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission'] = $permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("User Preference", "../", 1, 1,'',1,'');
	include('../includes/field_list_array.php');
	?>	
	<script>

        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
		
        var permission = '<? echo $permission; ?>';
        //fnc_field_level_access
		function fnc_user_preference( operation )
		{
			var data_all='';
			//form_validation(control,msg_text) 
			if (form_validation('cbo_company_id*cbo_form_id','Company*Page Name')==false) 
			{
				return;
			}
			
			var row_num=$('#table_details tbody tr').length;
			
			for (var j=1; j<=row_num; j++)
			{
				//get_submitted_data_string( flds, path, session )
				data_all+=get_submitted_data_string('fieldName_'+j+'*defaultList_'+j+'*cbo_permission_id_'+j+'*detailsId_'+j,"../",j);
			}
			
			
			//get_submitted_data_string( flds, path, session )
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_company_id*cbo_form_id',"../",2)+data_all;
			
			//alert(data);
			//return;
			freeze_window(operation);
			http.open("POST","requires/user_preference_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse;
		}
			
		//fnc_on_submit_reponse
		function fnc_on_submit_reponse()
		{
			if(http.readyState == 4) 
			{
				var response=http.responseText.split('**');
				show_msg(trim(response[0]));
				if(response[0]==0 || response[0]==1)
				{
					set_button_status(1, permission, 'fnc_user_preference',1);
				}
				else if(response[0]==2)
				{
					reset_form('userpreference_1','load_drop_down_item_div','','','','');
				}
				release_freezing();
			}
		}

		function set_item(val,company_id)
		{
			//if(form_validation('cbo_user_id','User ID')==false) return;
			//load_drop_down( plink, data, action, container)
			load_drop_down( 'requires/user_preference_controller', val+"**"+company_id, 'load_drop_down_item', 'load_drop_down_item_div');
			var is_update=$('#is_update').val();	
			//alert(is_update);
			//set_button_status(is_update, permission, submit_func, btn_id, show_print)
			set_button_status(is_update, permission, 'fnc_user_preference',1);
			//alert(val+"**"+company_id);		
			//set_multiselect('cbo_field_id','0','0','','0');
		}
	</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center"> 
        <?php echo load_freeze_divs ("../",$permission); ?>
        <form id="userpreference_1" name="userpreference_1" autocomplete="off">
            <fieldset style="width:500px">
            <legend>User Preference</legend>
                <table width="450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150" class="must_entry_caption">Form/ Report Name</th>
                    </thead>
                </table>
                <table width="450" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tbody>
                        <tr class="general">
                            <td width="175">
                               <?php 
									echo create_drop_down( "cbo_company_id", 220, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "set_item(document.getElementById('cbo_form_id').value,this.value);" );
								 ?>
                            </td>
                            <td width="175">
                                <?php 
								//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
								echo create_drop_down("cbo_form_id",150,$entry_form,"",1,"-- Select --","","set_item(this.value,document.getElementById('cbo_company_id').value);", "","","","","","","","cbo_form_id[]"); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
				<table width="450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="150">Field Name</th>
                        <th width="150">Default List</th>
                        <th width="150">Is Editable</th>
                    </thead>
                </table>
                <table width="450" cellpadding="0" cellspacing="0" border="0" rules="all" id="table_details">
                    <tbody id="load_drop_down_item_div">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" align="center" style="padding-top:10px;" class="button_container">
                            <?php 
								//function load_submit_buttons( $permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve)
                                echo load_submit_buttons( $permission, "fnc_user_preference", 0,0 ,"reset_form('userpreference_1','load_drop_down_item_div','','','','')",1); 
                            ?>
                           </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            <div id="fieldlevel_list_view"></div>
        </form>
    </div>
</body>
<!--<script> set_multiselect('cbo_company_id','0','0','','0'); </script>-->
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>