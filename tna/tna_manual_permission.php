<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA manual Permission","../", 1, 1, $unicode,'','');

$sql="select TNA_TASK_ID from TNA_TASK_TEMPLATE_DETAILS where STATUS_ACTIVE=1 and IS_DELETED=0";
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$taskArr[$row[TNA_TASK_ID]]=$tna_task_name[$row[TNA_TASK_ID]];
	}//echo $data;
	
?>
<script>

if($('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';	

	function fnc_tna_manual_permission( operation )
	{
		
		if ( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		
		var taskStr='<? echo implode(',',array_keys($taskArr)); ?>';
		var taskArr=taskStr.split(',');
		var dataStr='';
		$.each(taskArr, function( index, value ) {
			if(dataStr==''){var strSep="";}else{var strSep="*";}

			 
		//	dataStr+=strSep+$('#txt_tna_task_id_'+value).val()+'_'+$('#cbo_plan_'+value).val()+'_'+$('#cbo_actual_'+value).val();
			dataStr+=strSep+$('#txt_tna_task_id_'+value).val()+'_'+$('#cbo_plan_'+value).val()+'_'+$('#selected_plan_user_id_'+value).val()+'_'+$('#cbo_actual_'+value).val()+'_'+$('#selected_actual_user_id_'+value).val();
		});
		//alert(dataStr);

		var data="action=save_update_delete&operation="+operation+"&cbo_company="+$('#cbo_company_id').val()+"&data_string="+dataStr;
		freeze_window(operation);
		http.open("POST","requires/tna_manual_permission_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_manual_permission_reponse; 
	}

	function fnc_tna_manual_permission_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			set_button_status(1, permission, 'fnc_tna_manual_permission',1);
			release_freezing();
			
		}
	}

	function ManualUserPopUp(type, taskid, hidden_id, text_id)
	{
		var taskid = taskid;
		var hidden_id = hidden_id;
		var text_id = text_id;
		
		var page_link='requires/tna_manual_permission_controller.php?action=openpopup_user_manual_permission&serial='+type+'&user_ids='+$('#'+hidden_id+taskid).val()+'&user_names='+$('#'+text_id+taskid).val(); 
 
		emailwindow=dhtmlmodal.open('User List', 'iframe', page_link, 'User List','width=250px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{ 
			var txt_selected_user_id = this.contentDoc.getElementById("txt_selected_user_id").value;
			var txt_selected_user_name = this.contentDoc.getElementById("txt_selected_user_name").value;
			$('#'+hidden_id+taskid).val(txt_selected_user_id);
			$('#'+text_id+taskid).val(txt_selected_user_name);
		}
	}
	function YesNo(id, taskid, input_id){
		if(id == 1){
			$('#'+input_id+taskid).removeAttr('disabled');
		}
		else{
			$('#'+input_id+taskid).prop("disabled", true);
		}
	}
	
	var tableFilters='';
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
    <? echo load_freeze_divs ("../",$permission);  ?>
	<fieldset style="width:700px;">
		<legend>TNA manual Permission</legend>
		<form name="tnamanualpermission_1" id="tnamanualpermission_1">	
			<table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%" class="rpt_table">
			 	<thead>
                <tr>
					<td colspan="8" align="center" class="must_entry_caption"><strong>Company Name</strong>
					   <?
                           echo create_drop_down( "cbo_company_id", 170,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", 0, "show_list_view(this.value,'tna_task_list_view','list_container','requires/tna_manual_permission_controller','');setFilterGrid('list_container',-1,tableFilters);" );
                       ?>
					</td>
               </tr>
                <tr>
					<th width="35"><strong>SL</strong></th>
					<th width="100"><strong>Task Type</strong></th>
					<th width="200"><strong>Task Name</strong></th>
					<th width="200"><strong>Task Short Name</strong></th>
					<th width="85"><strong>Plan Edit</strong></th>                                		
					<th width="85"><strong>Plan User Edit</strong></th>                                		
					<th width="85"><strong>Actual Edit</strong></th>
					<th width="85"><strong>Actual User Edit</strong></th>                             		
                </tr>
                </thead>
                <tbody id="list_container">
                </tbody>

			</table>
		</form>	
	</fieldset>
		
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
