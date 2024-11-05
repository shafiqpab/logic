<?php
/*************************************************************************
|	Purpose			:	This Form Will Create Home Dash Board Privilege
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	27-07-2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
**************************************************************************/

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission'] = $permission;
	$user_arr= return_library_array( "select id, user_name from user_passwd where valid=1 order by user_name ASC", "id", "user_name");
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Home Dash Board Privilege", "../", 1, 1,'','','');
	?>	
	<script>
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
        var permission = '<? echo $permission; ?>';
        //fnc_home_dash_board_privilege
		function fnc_home_dash_board_privilege( operation )
		{
			var dataString=""; 
			var j=0; 
			var z=0; 
			var tot_row=0;
			$("#tbl_dashboard").find('tbody tr').each(function()
			{
				var cboModuleId		= $(this).find('select[name="cboModuleId[]"]').val();
				var cboItemId		= $(this).find('select[name="cboItemId[]"]').val();
				var txtSequinceNo	= $(this).find('input[name="txtSequinceNo[]"]').val();
				
				if(cboModuleId==0)
				{
					$(this).find('select[name="cboModuleId[]"]').focus();		
					return;
				}
				else if(cboItemId==0)
				{
					$(this).find('select[name="cboItemId[]"]').focus();		
					return;
				}
				else
				{
					j++;
					tot_row++;
					dataString += '&cboModuleId_' + j + '=' + cboModuleId + '&cboItemId_' + j + '=' + cboItemId + '&txtSequinceNo_' + j + '=' + txtSequinceNo;
				}
			});
			
			//alert(dataString); return;					
			var data = "action=save_update_delete&operation="+operation+get_submitted_data_string('cboUserId',"../")+dataString+'&total_row='+tot_row;
			freeze_window(operation);
			http.open("POST","requires/home_dash_board_privilege_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse;
		}
			
		//fnc_on_submit_reponse
		function fnc_on_submit_reponse()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText); return;
				var reponse=http.responseText.split('**');
				show_msg(trim(reponse[0]));
				reset_form("homedashboardprivilege_1","","","","$('#tbd_dashboard tr:not(:first)').remove();");
				set_button_status(0, permission, 'fnc_home_dash_board_privilege',1);
				release_freezing();
			}
		}
			
		//fnc_deleteRow
		function fnc_addRow(i, table_id, tr_id)
		{
			//alert(i);
			if (form_validation('cboUserId*cboModuleId_'+i+'*cboItemId_'+i,'User*Module*Item')==false)
			{
				return;
			}
			var prefix=tr_id.substr(0, tr_id.length-1);
			var row_num=$('#'+table_id+' tbody tr').length;
			
			if(i!=row_num) return;
			row_num++;
			
			var clone= $("#"+tr_id+i).clone();
			clone.attr({ id: tr_id + row_num, });

			clone.find("input,select").each(function(){
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return 0 }              
				});
			}).end();

			$("#"+tr_id+i).after(clone);
			$('#'+prefix+'increase_'+row_num).removeAttr("value").attr("value","+");
			$('#'+prefix+'decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#'+prefix+'increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#'+prefix+'decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$("#cboItemId_"+row_num +" option[value!='0']").remove();
			$('#cboModuleId_'+row_num).removeAttr("onchange").attr("onchange","set_item(this.value,'"+row_num+"');");
			$('#'+table_id+' tbody tr:last td:nth-child(2)').attr("id","itemtd_"+row_num);
			set_all_onclick();
		}
			
		//fnc_deleteRow
		function fnc_deleteRow( rowNo,table_id,tr_id,dtl_id)
		{
			var delete_id=$('#delete_id').val();
			if(dtl_id!='')
			{
				delete_id+=dtl_id+',';
			}
			$('#delete_id').val(delete_id);
			var numRow = $('#'+table_id+' tbody tr').length;
			if(numRow!=1)
			{
				$("#"+tr_id+rowNo).remove();
			}
		}
			
		function set_item( val, ids )
		{
			//alert(val);
			var data=val+'__'+ids
			load_drop_down( 'requires/home_dash_board_privilege_controller', data, 'load_drop_down_item', 'itemtd_'+ids);
		}
		
		function onchange_user(user_id)
		{
			//alert("su..re");
			show_list_view(user_id,'action_user_data','tbd_dashboard','requires/home_dash_board_privilege_controller','');
			var row_num=$('#tbl_dashboard tbody tr').length;
			if(row_num==1 && $('#cboModuleId_1').val()==0 && $('#cboItemId_1').val()==0)
			{
				set_button_status(0, permission, 'fnc_home_dash_board_privilege',1);
			}
			else
			{
				set_button_status(1, permission, 'fnc_home_dash_board_privilege',1);
			}
		}
    </script>
</head>
<body onLoad="set_hotkey()">
    <div align="center"> 
        <?php echo load_freeze_divs ("../../",$permission); ?>
        <form id="homedashboardprivilege_1" name="homedashboardprivilege_1" autocomplete="off">
            <fieldset style="width:600px"><legend>Home Dash Board Privilege</legend>
                <fieldset style="width:520px">
                    <table width="590" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
							<th colspan="4">User ID &nbsp;&nbsp;<?php echo create_drop_down( "cboUserId", 150, "select id, user_name from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '-- Select --', 0, "onchange_user(this.value);" ); ?></th>
                        </thead>
                        <thead>
							<th width="150">Module ID</th>
							<th width="150">Item ID</th>
							<th width="150">Sequence No</th>
                            <th width="140"></th>
                        </thead>
                   	</table>
                    <table id="tbl_dashboard" width="590" cellpadding="0" cellspacing="0" border="0" rules="all">
                        <tbody id="tbd_dashboard">
                        	<tr id="dashboard_1">
                            	<td width="150">
                                	<?php echo create_drop_down( "cboModuleId_1",150,$home_page_module,"",1,"-- Select --","","set_item( this.value,1 );","","","","","","","","cboModuleId[]" ); ?>
                                </td>
                                <td width="150" id="itemtd_1">
                                    <?php echo create_drop_down( "cboItemId_1",150,$blank_array,"",1,"-- Select --",0,"","","","","","","","","cboModuleId[]" ); ?>
                                </td>
                                <td width="150">
                                	<input type="text" id="txtSequinceNo_1" name="txtSequinceNo[]" class="text_boxes_numeric" style="width:138px; text-align:left;" />
                                </td>
                                <td width="140" align="center">
                                	<input type="button" id="dashboardincrease_1" name="dashboardincrease[]" value="+" class="formbuttonplasminus" style="width:60px;" onClick="fnc_addRow(1,'tbl_dashboard','dashboard_')" />
                                    <input type="button" id="dashboarddecrease_1" name="dashboarddecrease[]" value="-" class="formbuttonplasminus" style="width:60px;" onClick="fnc_deleteRow(1,'tbl_dashboard','dashboard_','')" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" align="center" style="padding-top:10px;" class="button_container">
								<?php 
									echo load_submit_buttons( $permission, "fnc_home_dash_board_privilege", 0,0 ,"reset_form('tiffinbillpolicy_1','','','','$(\'#tbd_dashboard tr:not(:first)\').remove();')",1); 
								?>
                               </td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </fieldset>
        </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>