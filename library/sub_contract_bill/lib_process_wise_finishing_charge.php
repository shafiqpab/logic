<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Process Wise Finishing Charge Set up
Functionality	:	
JS Functions	:
Created by		:	Jahid Hasan 
Creation date 	: 	30-07-2018
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
echo load_html_head_contents("Process Wise Finishing Charge Set up", "../../", 1, 1,$unicode,'1','',1);

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	
	function openmypage_fabric_description_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_cons_comp_id=$('#txt_cons_comp_id').val();
		page_link='requires/lib_process_wise_finishing_charge_controller.php?action=fabric_description_popup&cbo_company_id='+cbo_company_id+'&txt_cons_comp_id='+txt_cons_comp_id;
		var title='Fabric Description Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var composition_data=this.contentDoc.getElementById("hidden_com_id").value;
			var comp_data = composition_data.split("_");
			//alert(composition_data);
			$("#txt_cons_comp_id").val(comp_data[0]);
			$("#text_const_compo").val(comp_data[1]);
		}
	}	

	function fnc_dyeing_charge( operation )
	{
		if (form_validation('cbo_company_id*text_const_compo*cbo_process_id*cbo_machine_no*text_in_house_rate*cbo_uom','Company Name*Const Comp*Process Name*Machine No*In House Rate*UOM')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*text_const_compo*txt_cons_comp_id*cbo_process_id*cbo_machine_no*text_in_house_rate*cbo_uom*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/lib_process_wise_finishing_charge_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_charge_reponse;
		}
	}

	function fnc_dyeing_charge_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			document.getElementById('update_id').value  = reponse[1];
			show_msg(reponse[0]);
			show_list_view(reponse[2],'list_view_subcon_dying_charge','list_view_subcon_dying_charge','requires/lib_process_wise_finishing_charge_controller','setFilterGrid("list_view",-1)');
			reset_form('dyeingfinishincharge_1','','','');
			set_button_status(0, permission, 'fnc_dyeing_charge',1);
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs ("../../", $permission); ?>
		<fieldset style="width:800px;">
			<legend>Process Wise Finishing Charge</legend>
			<form name="dyeingfinishincharge_1" id="dyeingfinishincharge_1">
				<table cellpadding="0" cellspacing="2" width="790">
					<tr>
						<td colspan="6" align="center"></td>
					</tr>
					<tr align="left">
						<td class="must_entry_caption" width="110">Company Name<input type="hidden" name="update_id" id="update_id" value=""/></td>
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down('requires/lib_process_wise_finishing_charge_controller', this.value, 'load_drop_down_machine_no', 'machine_td' );");
							?>
						</td>
						<td class="must_entry_caption" width="110">Const. Compo. </td>
						<td colspan="3">
							<input type="text" name="text_const_compo" id="text_const_compo" class="text_boxes" style="width:405px" placeholder="Browse" onDblClick="openmypage_fabric_description_popup()"/>	
						</td>
						<input type="hidden" name="txt_cons_comp_id" id="txt_cons_comp_id" value=""/>
						<input type="hidden" name="save_string" id="save_string"/>						
					</tr>
					<tr>                        
					</td>
					<td class="must_entry_caption" width="110">Process Name</td>
					<td>
						<?
						echo create_drop_down( "cbo_process_id", 150, $conversion_cost_head_array,'', 1, "-- Select Name --", $selected, "","","","","","1,2,3,4,101,120,121,122,123,124");
						?>
					</td>
					<td class="must_entry_caption" width="110">Machine No</td>
					<td id="machine_td">
						<? 
						echo create_drop_down( "cbo_machine_no", 150, array(),'', 1, "-- Select Machine --", $selected, ""  );
						?>
					</td>
					<td class="must_entry_caption">In House Rate </td>
					<td><input type="text" name="text_in_house_rate" id="text_in_house_rate" class="text_boxes_numeric" style="width:140px;" /></td>
				</tr>
				<tr>
					<td class="must_entry_caption">UOM </td>
					<td><? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,'', 1, "--Select UOM--", $selected, "","","1,2,12,27"); ?></td>
					<td class="must_entry_caption"> Status </td>
					<td><? echo create_drop_down( "cbo_status", 150, $row_status,'', 2, "", $selected, "","" ,"1,2" ); ?></td>
				</tr>
				<tr>
					<td colspan="6" height="15"></td>
				</tr>
				<tr>
					<td colspan="6" align="center" class="button_container">
						<?
						echo load_submit_buttons( $permission, "fnc_dyeing_charge", 0,0,"reset_form('dyeingfinishincharge_1','','','')",1);
						?>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
	<br>
	<fieldset style="width:700px;">
		<legend>List View</legend>
		<table cellpadding="0" width="700" cellspacing="2">
			<tr>
				<td id="list_view_subcon_dying_charge">
					<?
					$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
					$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

					$arr= array(0=>$company_arr,2=>$conversion_cost_head_array,3=>$machine_arr,5=>$unit_of_measurement,6=>$row_status);

					echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Name,Machine No, In House Rate,UOM,Status", "90,130,70,70,100,60,60","650","250",1, "select id, company_id, const_comp, process_id, machine_no, rate, uom_id, status_active from lib_finish_process_charge where status_active!=0 and is_deleted=0 ", "get_php_form_data", "id","'load_php_data_to_form'", 1, "company_id,0,process_id,machine_no,0,uom_id,status_active", $arr, "company_id,const_comp,process_id,machine_no,rate,uom_id,status_active","requires/lib_process_wise_finishing_charge_controller", 'setFilterGrid("list_view",-1);',"0,0,0,0,2,0,0");
					exit();

					?> 
				</td>
			</tr>
		</table>
	</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>