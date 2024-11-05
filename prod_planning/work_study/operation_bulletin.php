<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Operation Bulletin Entry				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	31-01-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			: 
Comments		:

Note: Right site show_operation_list_view loading off when update and save;

*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//print_r($_SESSION['logic_erp']['mandatory_field'][149]);

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Operation Bulletin Entry", "../../", 1, 1, $unicode, 1, '');
if (!$TabIndexNo) {
	$TabIndexNo = 0;
}

$bulletin_copy_arr = array(1=>"New Bulletin", 2=>"Extended Bulletin");

$nameArray=sql_select("SELECT editable,gmt_num_rep_sty,id FROM  variable_order_tracking where variable_list=98 order by id DESC" );
// print_r($nameArray);
$method_type = $nameArray[0]['EDITABLE'];
$thread_length_calculation_type = $nameArray[0]['GMT_NUM_REP_STY'];
if($thread_length_calculation_type ==1){
	$thread_length = 'readonly="readonly"';
	$needle_title ='title="(Seam Length*Consumption Factor*Needle Thread %)"';
	$bobbin_title ='title="(Seam Length*Consumption Factor*Bobbin Thread %)"';
}
else{
	$thread_length = 'onKeyUp="calculate_thread(2)"';
	$needle_title ='';
	$bobbin_title ='';
}

if($method_type ==0 || $method_type==1){
	$needle1 = 0;
	$bobbin1 = 0;
}
else{
	$needle1 = 1;
	$bobbin1 = 2;
}

  
$ThreadFormArr = array(1=>'Needle Thread', 2=>'Bobbin Thread');
 
$bulletin_type_status_arr = return_library_array("SELECT bulletin_type,smv_editable from variable_settings_production where variable_list=11 and bulletin_type > 0 and is_deleted=0 and status_active=1", "bulletin_type", "smv_editable");
foreach ($bulletin_type_status_arr as $key => $val) {
	$bulletin_type_status_data[$key] = $key . '_' . $val;
}

$method_type_id = return_field_value("WORK_STUDY_MAPPING_ID", "VARIABLE_ORDER_TRACKING", "VARIABLE_LIST=92 and is_deleted=0 and status_active=1", "WORK_STUDY_MAPPING_ID");
$method_type_id = ($method_type_id) ? $method_type_id : 1;

?>
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	// Mandatory Field
	var mandatory_field_arr=[];
	
	<? 
	if($_SESSION['logic_erp']['mandatory_field'][149]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][149] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	//echo "alert(JSON.stringify(field_level_data));";
	?>
	/* <?

	//echo "var mandatory_field = '" . implode('*', $_SESSION['logic_erp']['mandatory_field'][149]) . "';\n";
	//echo "var field_message = '" . implode('*', $_SESSION['logic_erp']['field_message'][149]) . "';\n";

	?> */


	var bulletin_type_status_arr = Array();
	var bulletin_type_status_str = '<? echo implode(',', $bulletin_type_status_data); ?>';
	bulletin_type_status_str = bulletin_type_status_str.split(',');
	for (var i = 0; i < bulletin_type_status_str.length; i++) {
		var dataArr = bulletin_type_status_str[i].split('_');
		bulletin_type_status_arr[dataArr[0]] = dataArr[1];
	}





	var str_desc = [<? echo substr(return_library_autocomplete("select distinct(upper(thread_desc)) as thread_desc from ppl_thread_cons_op_dtls_entry", "thread_desc"), 0, -1); ?>];

	function add_auto_complete(i) {
		$("#txtThreadDesc_" + i).autocomplete({
			source: str_desc
		});
	}

	function openmypage_sysnum() {
		// alert(3)
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/ws_gsd_controller.php?action=systemid_popup&cbo_company_id='+document.getElementById("cbo_company_id").value, 'System ID Popup', 'width=1195px,height=350px,center=1,resize=1,scrolling=0', '../')

		emailwindow.onclose = function() {
			var theemail = this.contentDoc.getElementById("system_id");
			var response = theemail.value.split('_');
			if (theemail.value != "") {
				freeze_window(5);
				reset_form('gsdentry_1', 'reArrange_seqNo', '', '', '', 'cbo_company_id');
				document.getElementById("update_id").value = response[0];
				document.getElementById("cbo_buyer").value = response[1];
				document.getElementById("txt_style_ref").value = response[2];
				document.getElementById("cbo_gmt_item").value = response[3];
				document.getElementById("txt_working_hour").value = response[4];
				document.getElementById("txt_seqNo").value = response[5] * 1 + 1;

				document.getElementById("txt_operation_count").value = response[6];
				document.getElementById("txt_mcOperationCount").value = response[7];
				document.getElementById("cbo_product_department").value = response[14];
				document.getElementById("txt_custom_style").value = response[15];
				document.getElementById("txt_remarks").value = response[16];
				document.getElementById("txt_fabric_type").value = response[17];
				document.getElementById("cbo_colortype").value = response[18];
				document.getElementById("cbo_approved_status").value = response[19];
				document.getElementById("txt_product_description").value = response[20];
				document.getElementById("cbo_bulletin_type").value = response[21];


				document.getElementById("txt_applicable_period").value = response[22];
				if (response[21] == 4) {
					document.getElementById('txt_applicable_period').disabled = false;
				} else if (response[21] == 3) {
					document.getElementById('txt_applicable_period').disabled = true;
				} else {
					document.getElementById('txt_applicable_period').disabled = true;
					document.getElementById('txt_applicable_period').value = '';
				}

				document.getElementById("txt_internal_ref").value = response[24];
				document.getElementById("complexity_level").value = response[25];
				//document.getElementById("cbo_process_id").attr('disabled',true).value=response[26];
				$('#cbo_process_id').attr('disabled', true).val(response[26]).change();
				document.getElementById("txt_job_id").value = response[27];
				document.getElementById("txt_job_no").value = response[28];
				document.getElementById("cbo_company_id").value = response[29]*1;
				document.getElementById("txt_req_no").value = response[30];

				$('#cbo_buyer').attr('disabled', true);
				$('#cbo_gmt_item').attr('disabled', true);
				$('#cbo_bulletin_type').attr('disabled', true);

				if (eval(response[8]) > 0) var tot_smv = eval(response[8]).toFixed(2);
				else var tot_smv = '';
				if (eval(response[9]) > 0) var mc_smv = eval(response[9]).toFixed(2);
				else var mc_smv = '';
				if (eval(response[10]) > 0) var manual_smv = eval(response[10]).toFixed(2);
				else var manual_smv = '';
				if (eval(response[11]) > 0) var finishing_smv = eval(response[11]).toFixed(2);
				else var finishing_smv = '';

				$('#txt_tot_smv').val(tot_smv);
			    $('#txt_mc_smv').val(mc_smv);
				$('#txt_manual_smv').val(manual_smv);
				$('#txt_finishing_smv').val(finishing_smv);

				//document.getElementById("txt_tot_smv").value=response[8].toFixed(2);
				//document.getElementById("txt_mc_smv").value=response[9].toFixed(2);
				//document.getElementById("txt_manual_smv").value=response[10].toFixed(2);
				//document.getElementById("txt_finishing_smv").value=response[11].toFixed(2);

				document.getElementById("system_no").value = response[12];
				//document.getElementById("system_no").value=response[0];
				if (response[13] == "0") response[13] = '';
				document.getElementById("txt_ext_no").value = response[13];
				document.getElementById("cbo_bulletin_copy").value = 0; //response[23];
				$('#cbo_bulletin_copy').attr('disabled', false);
				document.getElementById("cbo_action").value = 1;

				// Body Part&&Prod. Dept&&Garments Item show option
				// show_list_view(response[3] + "__" + response[0] + "_" + response[14] + "_" + response[26], 'show_operation_list_view', 'list_operation_container', 'requires/ws_gsd_controller', 'setFilterGrid(\'list_view\',0);');

				show_list_view(response[0], 'load_php_dtls_form', 'gsd_entry_info_list', 'requires/ws_gsd_controller', 'setFilterGrid(\'tbl_details\',-1);');
				$('#tbl_operation_list tbody tr').remove();
				$('#tbl_operation_list2 tbody tr').remove();
				$('#tbl_list_search_tc tbody tr').remove();

				$("thead tr th").dblclick(function() {
					$("#tbl_scroll_body").animate({
						scrollTop: 0
					}, 'slow');
				});

 
				document.getElementById("txt_style_ref").title = response[2];
				document.getElementById("cbo_gmt_item").title = $("#cbo_gmt_item option:selected").text();

				get_php_form_data(document.getElementById("cbo_bulletin_type").value + '__' + document.getElementById("txt_style_ref").value + '__' + document.getElementById("cbo_gmt_item").value + '__' + document.getElementById("update_id").value, "check_production_against_this_bulletin", "requires/ws_gsd_controller");


				release_freezing();
			}
		}
	}

	function test(e) {
		alert(e)
	}

	function openmypage_operation() {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/ws_gsd_controller.php?action=operation_popup', 'Operation Popup', 'width=750px,height=350px,center=1,resize=1,scrolling=0', '../')
		emailwindow.onclose = function() {
			var theemail = this.contentDoc.getElementById("operation_id");
			var response = theemail.value.split('_');
			if (theemail.value != "") {
				freeze_window(5);
				document.getElementById("txt_operation").value = response[1];
				document.getElementById("cbo_resource").value = response[2];
				document.getElementById("txt_operator").value = response[3];
				document.getElementById("txt_helper").value = response[4];
				document.getElementById("hidden_operation").value = response[0];
				release_freezing();
			}
		}
	}

	function openmypage_attachment() {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/ws_gsd_controller.php?action=attachment_popup', 'Attachment Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0', '../')

		emailwindow.onclose = function() {
			var theemail = this.contentDoc.getElementById("attachment_id");
			var response = theemail.value.split('_');
			if (theemail.value != "") {
				freeze_window(5);
				document.getElementById("txt_attachment_id").value = response[0];
				document.getElementById("txt_attachment").value = response[1];
				//reset_form();
				//get_php_form_data( response[1], "load_php_data_to_form_attachment", "requires/ws_gsd_controller" );
				release_freezing();
			}
		}
	}

	function fnc_gsd_entry(operation)
	{
		if (operation == 2) {
			var response = return_global_ajax_value($('#update_id').val(), 'is_balanced_entry', '', 'requires/ws_gsd_controller');
			if (response == 1) {
				var r = confirm("Balancing Entry Found. Press \"OK\" to Delete This Breakdown and it's related Data.");
				if (r == false) {
					return;
				}
			} else {
				var r = confirm("Delete Sure? Press \"OK\" to Delete This Breakdown.");
				if (r == false) {
					return;
				}
			}
		}

		if ($('#update_parmission').val() != 1) {
			alert('Update restricted, This Information is used in another Table');
			return;
		}

		if ($('#txt_dtls_id').val() == '') {
			var update_id = $('#update_id').val();
			var response = return_global_ajax_value(update_id, 'max_sequence_no', '', 'requires/ws_gsd_controller');
			document.getElementById("txt_seqNo").value = response;
		}
  
		if (operation != 2) {
			if (form_validation('txt_style_ref*cbo_buyer*cbo_gmt_item*txt_working_hour*txt_seqNo*cbo_body_part*txt_operation*cbo_resource', 'Style Ref.*Buyer Name*Garment Item*Working Hour*Sequance*Body Part*Operation*Resource') == false) {
				return;
			}
			/* if (mandatory_field) {
				if (form_validation(mandatory_field, field_message) == false) { txt_job_no
					release_freezing();
					return;
				}
			} */
			if (document.getElementById("txt_operator").value == '' && document.getElementById("txt_helper").value == '') {
				if (form_validation('txt_operator', ' Machine SMV') == false && form_validation('txt_helper', ' Manual SMV') == false) {
					return;
				}
			}
		}
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][149]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][149]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][149]);?>')==false)
			{
				return;
			}
		}
		// cbo_spi cbo_needle_size cbo_risk_factor
		var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_style_ref*cbo_buyer*cbo_gmt_item*txt_working_hour*cbo_action*txt_seqNo*cbo_body_part*hidden_operation*cbo_resource*txt_attachment_id*txt_operator*txt_helper*txt_efficiency*txt_tgt_perc*txt_tgt_eff*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_manual_smv*txt_finishing_smv*cbo_spi*cbo_needle_size*cbo_risk_factor*txt_dlts_remarks*hidden_quotation_id*update_id*txt_ext_no*cbo_bulletin_copy*system_no*cbo_product_department*txt_custom_style*txt_remarks*txt_fabric_type*cbo_bulletin_type*cbo_colortype*cbo_approved_status*txt_product_description*txt_applicable_period*txt_internal_ref*complexity_level*cbo_process_id*txt_job_id*txt_job_no*txt_style_id*cbo_company_id*txt_req_no', "../../"); //

		if (document.getElementById("cbo_bulletin_type").value == 4 || document.getElementById("cbo_bulletin_type").value == 3) {
			if (form_validation('txt_applicable_period', 'Applicable period') == false) {
				return;
			}
		}

		var className = ".tr_" + document.getElementById("hidden_operation").value;
		$(className).css("background-color", "green");
		freeze_window(operation);
		http.open("POST", "requires/ws_gsd_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_gsd_entry_response;
	}

	function fnc_gsd_entry_response() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split('**');
			if (trim(response[0]) == 'approved') {
				alert("This Operation Bulletin is Approved");
				release_freezing();
				return;
			} else if (trim(response[0]) == 'duplicate') {
				alert("This Operation Bulletin Duplicate [Note: style ref, item,Buyer,Process,Bulletin type>Budget & Marketing] Duplicate not Allow");
				release_freezing();
				return;
			}
			//console.log(response[0]);

			show_msg(response[0]);

			if (response[0] == 0 || response[0] == 1) {
				document.getElementById('update_id').value = response[1];
				show_list_view(response[1], 'load_php_dtls_form', 'gsd_entry_info_list', 'requires/ws_gsd_controller', 'setFilterGrid(\'tbl_details\',-1);');
				//show_list_view($('#cbo_gmt_item').val()+"__"+response[1],'show_operation_list_view','list_operation_container','requires/ws_gsd_controller','setFilterGrid(\'list_view\',0);');

				reset_form('', 'reArrange_seqNo', 'txt_seqNo*txt_operation*hidden_operation*cbo_resource*txt_attachment*txt_attachment_id*txt_operator*txt_helper*txt_tgt_perc*txt_tgt_eff*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_manual_smv*txt_finishing_smv', '', '');
				document.getElementById("txt_seqNo").value = response[2];
				document.getElementById('system_no').value = response[4];

				var datas = response[3].split("_");
				$('#txt_operation_count').val(datas[0]);
				$('#txt_mcOperationCount').val(datas[1]);
				$('#txt_tot_smv').val(datas[2]);
				$('#txt_mc_smv').val(datas[3]);
				$('#txt_manual_smv').val(datas[4]);
				$('#txt_finishing_smv').val(datas[5]);

				$('#cbo_bulletin_type').attr('disabled', true);
				$('#cbo_gmt_item').attr('disabled', true);
				$('#cbo_process_id').attr('disabled', true);
				set_button_status(0, permission, 'fnc_gsd_entry', 1);

				var gmts_item_id = $('#cbo_gmt_item').val();
				//var body_part_id=$('#cbo_body_part').val();
				var body_part_id = 0;


				//show_list_view(gmts_item_id+"_"+body_part_id+"_"+response[1],'show_operation_list_view','list_operation_container','requires/ws_gsd_controller','setFilterGrid(\'list_view\',-1);');
			} else if (response[0] == 2) {
				reset_form('gsdentry_1', 'reArrange_seqNo*gsd_entry_info_list*list_operation_container', '', 'txt_working_hour,10', '');
				document.getElementById("txt_seqNo").value = 1;
				$('#cbo_bulletin_copy').attr('disabled', true);
			} else if (response[0] == 19) {
				if ($('#cbo_approved_status').val() == 2) {
					document.getElementById('cbo_approved_status').value = '1';
					document.getElementById('approve1').value = 'Un-Approved';
				} else {
					document.getElementById('cbo_approved_status').value = '2';
					document.getElementById('approve1').value = 'Approved';
				}
			}
			release_freezing();
		}
	}

	function dlt_operation() {

		var sequenceArr = [];
		$.each($("input[name='sequence_id']:checked"), function(){
			sequenceArr.push($(this).val());
		}); 
		var txt_dtls_id_str = sequenceArr.join(",");
		 
		if (txt_dtls_id_str == "") {
			alert("Select Saved Operation To Delete.");
			return;
		} 

		var response = return_global_ajax_value(txt_dtls_id_str, 'is_operation_balanced_entry', '', 'requires/ws_gsd_controller');
		if (response == 1) {
			var r = confirm("Balancing Entry Found. Press \"OK\" to Delete This Operation and it's related Data.");
			if (r == false) {
				return;
			}
		} 
		else {
			var r = confirm("Sure Delete? Press \"OK\" to Delete This Breakdown and it's related Data.");
			if (r == false) {
				return;
			}
		}

		var data = "action=delete_operation&txt_dtls_id_str="+txt_dtls_id_str + get_submitted_data_string('update_id', "../../");
		freeze_window(2);
		http.open("POST", "requires/ws_gsd_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_dlt_operation_response;
	}

	function fnc_dlt_operation_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');
			show_msg(response[0]);
			if (response[0] == 2) {
				updateSeqNoAuto(response[2]);
				show_list_view($('#update_id').val(), 'load_php_dtls_form', 'gsd_entry_info_list', 'requires/ws_gsd_controller', 'setFilterGrid(\'tbl_details\',-1);');
				reset_form('', 'reArrange_seqNo', 'txt_seqNo*txt_operation*hidden_operation*cbo_resource*txt_attachment*txt_attachment_id*txt_operator*txt_helper*txt_efficiency*txt_tgt_perc*txt_tgt_eff*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_manual_smv*txt_finishing_smv', '', '');
				document.getElementById("txt_seqNo").value = response[1];
				set_button_status(0, permission, 'fnc_gsd_entry', 1);

				get_php_form_data($('#update_id').val(), "totalSMVAfterDelete", "requires/ws_gsd_controller");

			} else if (response[0] == 13) {
				alert("Blancing Entry Found Against This Opeartion. So, Delete not allowed.");
			} else if (response[0] == 15) {
				alert("This Opeartion Approved. So, Delete not allowed.");
			}
			release_freezing();
		}
	}



	function updateSeqNoAuto(data = '') {
		var operation = 1;
		var data = "action=update_seq_no&operation=" + operation + '&data=' + data;
		//freeze_window(operation);
		http.open("POST", "requires/ws_gsd_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function() {
			if (http.readyState == 4) {
				//release_freezing();
				//alert(data);
			}
		};
	}


	function fnc_copy_bulletin() {
		if (form_validation('system_no*cbo_bulletin_copy*txt_style_ref', 'System ID*Copy*Style Ref') == false) {
			return;
		}
		var data = "action=copy_bulletin" + get_submitted_data_string('update_id*cbo_bulletin_copy*cbo_gmt_item*cbo_bulletin_type*txt_style_ref*cbo_buyer*cbo_process_id', "../../");

		freeze_window(operation);
		http.open("POST", "requires/ws_gsd_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_copy_bulletin_response;
	}

	function fnc_copy_bulletin_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');

			if (response[0] == 11) {
				showMsgText('Duplicate Buyer,Style Ref, Bulletin Type $ GMT Item not allow when copy new bulletin');
			} else if (response[0] == 12) {
				showMsgText('Style Ref, Bulletin Type,Buyer,Gmt Item change not allow for create Extended Bulletin');
			} else if (response[0] == 100) {
				alert("Data Copied Successfully");
				document.getElementById('update_id').value = response[1];
				show_list_view(response[1], 'load_php_dtls_form', 'gsd_entry_info_list', 'requires/ws_gsd_controller', 'setFilterGrid(\'tbl_details\',-1);');
				reset_form('', 'reArrange_seqNo', 'txt_seqNo*hidden_operation*cbo_resource*txt_attachment*txt_attachment_id*txt_operator*txt_helper*txt_efficiency*txt_tgt_perc*txt_tgt_eff*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_manual_smv*txt_finishing_smv', '', '');
				document.getElementById("txt_seqNo").value = response[2];
				document.getElementById('system_no').value = response[3];
				document.getElementById('txt_ext_no').value = response[4];
				if (document.getElementById('cbo_bulletin_type').value == 3) {
					document.getElementById('txt_applicable_period').value = response[5];
				}


				$('#cbo_gmt_item').attr('disabled', true);
				$('#cbo_bulletin_type').attr('disabled', true);
				$('#cbo_buyer').attr('disabled', true);
				/*if(document.getElementById("cbo_bulletin_copy").value==1){
					$('#cbo_buyer').attr('disabled',false);
				}*/

				set_button_status(0, permission, 'fnc_gsd_entry', 1);
			} else {
				alert("Invalid Operation");
			}
			release_freezing();
		}
	}

	function load_operation() {

		// alert(2);

		/*			var gmts_item_id=$('#cbo_gmt_item').val();
					var body_part_id=$('#cbo_body_part').val();
					var product_dept=$('#cbo_product_department').val();
		*/
		/*var gmts_item_id=($('#cbo_gmt_item_serch').val())?$('#cbo_gmt_item_serch').val():$('#cbo_gmt_item').val();
		var body_part_id=($('#cbo_body_part_serch').val())?$('#cbo_body_part_serch').val():$('#cbo_body_part').val();
		var product_dept=($('#cbo_product_department_serch').val())?$('#cbo_product_department_serch').val():$('#cbo_product_department').val();*/
		var cbo_process_id = $('#cbo_process_id').val();
		var gmt_item_serch = $('#cbo_gmt_item_serch').val() * 1;
		var body_part_serch = $('#cbo_body_part_serch').val() * 1;
		var product_department_serch = $('#cbo_product_department_serch').val() * 1;

		
		 

		// if(gmt_item_serch=="NaN"){gmt_item_serch=0;}
		// if(body_part_serch=="NaN"){body_part_serch=0;}
		// if(product_department_serch=="NaN"){product_department_serch=0;}


		if (isNaN(gmt_item_serch)) {
			gmt_item_serch = 0;
		}
		if (isNaN(body_part_serch)) {
			body_part_serch = 0;
		}
		if (isNaN(product_department_serch)) {
			product_department_serch = 0;
		}


		var gmts_item_id = 0;
		var body_part_id = 0;
		var product_dept = 0;
		//if(gmt_item_serch!=0) gmts_item_id=gmt_item_serch*1; else gmts_item_id=$('#cbo_gmt_item').val()*1;
		//if(body_part_serch!=0) body_part_id=body_part_serch; else body_part_id=$('#cbo_body_part').val()*1;
		//if(product_department_serch!=0) product_dept=product_department_serch; else product_dept=$('#cbo_product_department').val();


		body_part_id = body_part_serch;
		gmts_item_id = gmt_item_serch * 1;
		product_dept = product_department_serch;



		var update_id = $('#update_id').val() * 1;

		

		show_list_view(gmts_item_id + "_" + body_part_id + "_" + update_id + "_" + product_dept + "_" + cbo_process_id + "_" + $('#cbo_company_id').val(), 'show_operation_list_view', 'list_operation_container', 'requires/ws_gsd_controller', 'setFilterGrid(\'list_view\',0);');

		// 


		//$('#cbo_gmt_item_serch').val(gmts_item_id);
		//$('#cbo_body_part_serch').val(body_part_id);
		//$('#cbo_product_department_serch').val(product_dept);
	}

	function load_product_code(iterm_id){
		show_list_view(iterm_id, 'show_product_code_view', 'show_product_code', 'requires/ws_gsd_controller', '');// setFilterGrid(\'list_view\',0);
		 
	}

	function re_arrange_seq() {
		if (form_validation('update_id', 'System ID') == false) {
			return;
		}
		var update_id = $('#update_id').val();
		show_list_view(update_id, 'show_details_list_view', 'reArrange_seqNo', 'requires/ws_gsd_controller', '');
		set_all_onclick();
	}

	function js_set_value(data) {
		//var update_id=$('#update_id').val();
		//var response=return_global_ajax_value(update_id, 'max_sequence_no', '', 'requires/ws_gsd_controller');
		//document.getElementById("txt_seqNo").value=response;

		var datas = data.split("_");
		document.getElementById("hidden_operation").value = datas[0];
		document.getElementById("txt_operation").value = datas[1];
		document.getElementById("cbo_resource").value = datas[2];
		document.getElementById("txt_operator").value = datas[3];
		document.getElementById("txt_helper").value = datas[4];
		document.getElementById("cbo_body_part").value = datas[5];
		document.getElementById("txt_seam_length").value = datas[7];
		fnc_smv_active();
		calculate_target();
	}

	function js_set_save_value(para) {
		fnc_gsd_entry(para);
	}
	
	function calculate_target() {
		var smv = $('#txt_operator').val() * 1 + $('#txt_helper').val() * 1;
		if (smv > 0) {
			var target_full_perc = 60 / smv;
			$('#txt_tgt_perc').val(Math.round(target_full_perc));

			var txt_efficiency = $('#txt_efficiency').val() * 1;
			if (txt_efficiency > 0) {
				var target_perc_effi = (target_full_perc / 100) * txt_efficiency;
				$('#txt_tgt_eff').val(Math.round(target_perc_effi));
			} else {
				$('#txt_tgt_eff').val('');
			}
		} else {
			$('#txt_tgt_perc').val('');
			$('#txt_tgt_eff').val('');
		}
	}

	function fnc_smv_active() {
		var resource = $("#cbo_resource").val();

		if (resource == 40 || resource == 41 || resource == 43 || resource == 44 || resource == 48 || resource == 53 || resource == 54 || resource == 55 || resource == 56 || resource == 68 || resource == 69 || resource == 70 || resource == 90 || resource == 147 || resource == 176 ) {
			$('#txt_helper').removeAttr('readOnly', 'readOnly');
			$('#txt_operator').attr('readOnly', 'readOnly');
			if (bulletin_type_status_arr[$('#cbo_bulletin_type').val()] == 2) {
				$('#txt_operator').attr('readonly', true);
				$('#txt_helper').attr('readonly', true);
			}

		} else {
			$('#txt_helper').attr('readOnly', 'readOnly');
			$('#txt_operator').removeAttr('readOnly', 'readOnly');
			if (bulletin_type_status_arr[$('#cbo_bulletin_type').val()] == 2) {
				$('#txt_helper').attr('readonly', true);
				$('#txt_operator').attr('readonly', true);
			}

		}
		//if($('#cbo_bulletin_type').val())
	}

	function duplication_check(row_id) {
		var row_num = $('#gsd_tbl tbody tr').length;
		var txt_seq = trim($('#seqNo_' + row_id).val());

		if (txt_seq != "") {
			for (var j = 1; j <= row_num; j++) {
				if (j == row_id) {
					continue;
				} else {
					var txt_seq_check = trim($('#seqNo_' + j).val());

					if (txt_seq == txt_seq_check) {
						alert("Duplicate Seq No. " + txt_seq);
						$('#seqNo_' + row_id).val('');
						return;
					}
				}
			}
		}
	}

	function duplication_check_all() {
		var row_num = $('#gsd_tbl tbody tr').length;
		var seq_no_arr = new Array();
		var breakOut = true;
		for (var j = 1; j <= row_num; j++) {
			var txt_seq = trim($('#seqNo_' + j).val());
			if (form_validation('seqNo_' + j, 'Seq. NO') == false) {
				breakOut = false;
				return false;
			} else {
				if (jQuery.inArray(txt_seq, seq_no_arr) > -1) {
					alert("Duplicate Seq No. " + txt_seq);
					$('#seqNo_' + j).val('');
					breakOut = false;
					return false;
				} else {
					seq_no_arr.push(txt_seq);
				}
			}
		}

		return breakOut;
	}

	function arrange_table() {
		table_arr = [];
		table_tr_id = [];
		var new_table_data = "";
		var tr_next_id = $('#gsd_tbl tbody tr:last').attr('id');

		var next_id = tr_next_id.split('_');
		tr_count = next_id[1];
		//alert (tr_count);
		for (i = 1; i <= tr_count; i++) {
			if ($('#seqNo_' + i).val() != undefined) {
				user_tr_index = $('#seqNo_' + i).val();
				tr_id = $('#gsd_' + i).val();
				table_arr.push(user_tr_index);
			}
		}

		table_arr = table_arr.sort(function(a, b) {
			return a - b;
		});

		count_sorted_array = table_arr.length;
		//alert(count_sorted_array);
		var t = 1;
		for (j = 0; j < count_sorted_array; j++) {
			new_tr = table_arr[j];
			for (i = 1; i <= tr_count; i++) {
				user_tr_index = $('#seqNo_' + i).val();
				var dtlsId = $('#dtlsIdS_' + i).val();
				//alert(user_tr_index+"="+dtlsId);

				if (user_tr_index == new_tr) {
					new_table_data += '<tr id="gsd_' + t + '"><td align="center"><input type="radio" name="seqRa" id="seqRa_' + t + '" value="' + t + '" /></td><td align="center" width=45><input type="text" name="seqNo[]" class="text_boxes_numeric" id="seqNo_' + t + '" value="' + user_tr_index + '" style="width:30px;"/><input type="hidden" name="dtlsIdS[]" id="dtlsIdS_' + t + '" value="' + dtlsId + '"/></td>'; // onBlur="duplication_check('+t+')"
					$('#gsd_' + i + ' td').each(function(index, element) {
						//alert(index);
						//if(index != 0)
						if (index > 1) {
							td_data = $(this).html();
							//if(index==4 || index==5)
							if (index == 5 || index == 6) {
								new_table_data += '<td align="right">' + td_data + '</td>';
							} else {
								new_table_data += '<td>' + td_data + '</td>';
							}
						}

					});
					new_table_data += '</tr>';
					t++;
				}
			}
		}
		//alert(new_table_data);
		$('#gsd_tbl tbody').html('');
		$('#gsd_tbl tbody').append(new_table_data);

	}

	function fnc_save() {
		if (duplication_check_all() == false) {
			return false;
		}
		arrange_table();
		update_seqNo();
	}

	function re_arrange_table() {
		if ($('input[name=seqRa]').is(":checked")) {
			var rowId = $("input[name='seqRa']:checked").val();
			var txt_seq = parseInt(trim($('#seqNo_' + rowId).val()));

			if (txt_seq == 0) {
				alert("Sequance Zero Not Allowed.");
				return;
			}

			var row_num = $('#gsd_tbl tbody tr').length;
			var tr_id = rowId;
			for (var j = 1; j <= row_num; j++) {
				if (j == rowId) {
					continue;
				} else {
					var txt_seq_curr = parseInt($('#seqNo_' + j).val());
					if (txt_seq_curr == txt_seq) {
						//$('#seqNo_'+j).val(txt_seq);
						tr_id = j;
						break;
					}
				}
			}
			//return;

			for (var j = tr_id; j <= row_num; j++) {
				if (j == rowId) {
					continue;
				} else {
					txt_seq++;
					$('#seqNo_' + j).val(txt_seq);
				}
			}

			arrange_table();
			update_seqNo();
		} else {
			alert("Please Select Seq. NO");
			return;
		}
	}

	function update_seqNo() {
		var data = '';
		$("#gsd_tbl").find('tbody tr').each(function() {
			var seqNo = trim($(this).find('input[name="seqNo[]"]').val());
			var dtlsId = trim($(this).find('input[name="dtlsIdS[]"]').val());
			if (data == "") {
				data = seqNo + "_" + dtlsId;
			} else {
				data += "|" + seqNo + "_" + dtlsId;
			}
		});

		var operation = 1;
		var data = "action=update_seq_no&operation=" + operation + '&data=' + data;

		freeze_window(operation);

		http.open("POST", "requires/ws_gsd_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_rearrange_seq_no_Reply_info;
	}

	function fnc_rearrange_seq_no_Reply_info() {
		if (http.readyState == 4) {
			var response = trim(http.responseText);
			//release_freezing();	return;
			//var response=2;
			if (response == 1) {
				show_msg(trim(response));
				reset_form('', 'reArrange_seqNo', '', '', '');
				var update_id = document.getElementById('update_id').value;
				show_list_view(update_id, 'load_php_dtls_form', 'gsd_entry_info_list', 'requires/ws_gsd_controller', 'setFilterGrid(\'tbl_details\',-1);');
			} else {
				show_msg('10');
			}
			release_freezing();
		}
	}

	function fnc_move_cursor(val, id, field_id, lnth, max_val) {
		var str_length = val.length;
		if (str_length == lnth) {
			$('#' + field_id).select();
			$('#' + field_id).focus();
		}
		if (val > max_val) {
			document.getElementById(id).value = max_val;
		}
	}

	$(document).ready(function() {
		$('#example').tabs();
		$('#example').tabs('paging', {
			cycle: true,
			follow: true
		});
		$('#example').tabs('select', <?php echo $TabIndexNo; ?>);
	});

	function showData() {
		$('#list_operation_container').show();
		$('#reArrange_seqNo').show();
		$('#list_operation_container_thread').hide();
	}

	function showResult() {
		var update_id = document.getElementById('update_id').value;
		if (update_id != '') {
			//alert()
			//reset_form('','list_operation_container*reArrange_seqNo','','','');
			//$('#tbl_operation_list2 tbody tr').remove();
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#list_operation_container_thread').hide();

			var totRows = $('#tbl_operation_list tbody tr').length;
			//if(totRows<1)
			//{
			get_php_form_data(update_id, "populate_data_from_breakdown", "requires/balancing_entry_controller");
			var bl_update_id = document.getElementById('bl_update_id').value;
			
			// alert(bl_update_id);
			var list_view_opration = return_global_ajax_value(update_id + "**" + bl_update_id, 'details_list_view', '', 'requires/balancing_entry_controller');
			$('#operation_details').html(trim(list_view_opration));
			calculate_total();
		}
		//}
	}

	function resetForm() {
		//reset_form('gsdentry_2','tbl_details','','','breakdown_id');
		$('#tbl_operation_list tbody tr').remove();
		showResult();
	}

	function showResult2() {
		//$('#tbl_operation_list tbody tr').remove();
		var update_id = document.getElementById('update_id').value;
		if (update_id != '') {
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#list_operation_container_thread').hide();

			var totRows = $('#tbl_operation_list2 tbody tr').length;
			if (totRows < 1) {
				get_php_form_data(update_id, "populate_data_from_breakdown", "requires/balancing2_entry_controller");
				var bl_update_id = document.getElementById('bl2_update_id').value;

				var list_view_opration = return_global_ajax_value(update_id + "**" + bl_update_id, 'details_list_view', '', 'requires/balancing2_entry_controller');
				$('#operation_details2').html(trim(list_view_opration));
				if (bl_update_id != "") {
					var list_view_header = return_global_ajax_value(bl_update_id, 'details_list_view_header', '', 'requires/balancing2_entry_controller');
					var headerData = trim(list_view_header).split("__");
					var header = headerData[0];
					var smvData = headerData[1];
					var targetData = headerData[2];
					var wlData = headerData[3];
					var footer = headerData[4];
					var j = 0;
					$("#tbl_operation_list2 thead").find('tr').each(function() {
						j++;
						$(this).find('th').each(function() {
							var ind = $(this).index();
							if (j == 1) {
								if (ind > 3) {
									$(this).remove();
								}
							} else {
								if (ind > 0) {
									$(this).remove();
								}
							}
						});
					});

					$("#tf_1").find('th').each(function() {
						var ind = $(this).index();
						if (ind > 3) {
							$(this).remove();
						}
					});

					$("#th_1").append(header);
					$("#th_2").append(smvData);
					$("#th_3").append(targetData);
					$("#th_4").append(wlData);
					$("#tf_1").append(footer);
				} else {
					calculate_total2();
				}
			}
		}
	}

	function resetForm2() {
		$('#tbl_operation_list2 tbody tr').remove();
		showResult2();
	}

	function showResult3() {
		var update_id = document.getElementById('update_id').value;
		$('#list_operation_container').hide();
		$('#reArrange_seqNo').hide();
		$('#list_operation_container_thread').show();

		var prev_data = $('#tbl_list_search_tc tbody tr').length;

		 
		//if(prev_data<1)
		//{	
		if (update_id != "") {
			show_list_view(update_id, 'show_operation_list', 'list_operation_container_thread', 'requires/thread_consumption_controller', 'setFilterGrid(\'list_view_tc\',-1);');
			get_php_form_data(update_id, "populate_data_from_breakdown", "requires/thread_consumption_controller");
			var bl_update_id = document.getElementById('bl3_update_id').value;
		 
			show_list_view(bl_update_id, 'details_list_view', 'operation_details_tc', 'requires/thread_consumption_controller', '');
		}

		resetFormTC();
		//}
	}

	function calculate_thread(i) {
		var txt_seam_length = trim($('#txt_seam_length').val());
		var threadLength = trim($('#txtThreadLength_' + i).val());
		var allowance = trim($('#txtAllowance_' + i).val());

		var length = txt_seam_length * threadLength;
		var req_qty = length * 1 + (length * 1 / 100) * allowance;
		$('#txtRequired_' + i).val(req_qty.toFixed(2));

		calculate_total_req();
		calculate_thread_all();
	}

	function calculate_thread_all() {
		
		var txt_seam_length = trim($('.seam_length').val());
		
		var method_type = trim($('#method_type').val());
		var thread_length_calculation_type = trim($('#thread_length_calculation_type').val());
		
		if(thread_length_calculation_type !=0){
			var txt_consumption_factor = trim($('#txt_consumption_factor').val());
		    var txt_needle_thread = trim($('#txt_needle_thread').val());
			var txt_bobbin_thread = trim($('#txt_bobbin_thread').val());
			var needle = (txt_seam_length*txt_consumption_factor*txt_needle_thread)/100;
		    var bobbin = (txt_seam_length*txt_consumption_factor*txt_bobbin_thread)/100;
		}

		var numRow = $('#tbl_list_search_tc tbody tr').length;
		for (var i = 1; i <= numRow; i++) {
			var threadLength = trim($('#txtThreadLength_' + i).val());
			var allowance = trim($('#txtAllowance_' + i).val());
			var cboTheardForm = trim($('#cboTheardForm_' + i).val());
			var cboFrequency = trim($('#txtFrequency_' + i).val());
			
			if(thread_length_calculation_type !=0){
				if(cboTheardForm==1){
				$('#txtThreadLength_' + i).val(needle);
				}
				else if(cboTheardForm==2){
					$('#txtThreadLength_' + i).val(bobbin);
				}
			}
			

			if(method_type==1){
				var length = txt_seam_length * threadLength;
			    var req_qty = length * 1 + (length * 1 / 100) * allowance;
			}
            // (Thread Length*Frequency*Allowance)
			if(method_type==2){
				var length = cboFrequency * threadLength * allowance;
			    var req_qty = length;
			}

			
			if (req_qty * 1 > 0) {
				$('#txtRequired_' + i).val(req_qty.toFixed(2));
			} else {
				$('#txtRequired_' + i).val('');
			}
		}
		calculate_total_req();
	}

	function calculate_total_req() {
		//var ddd={ dec_type:4, comma:0, currency:1}
		//var tot_row=$('#tbl_list_search_tc tbody tr').length;
		//math_operation( "totReq", "txtRequired_", "+", tot_row,ddd );

		var total_req = 0;
		$("#tbl_list_search_tc").find('tbody tr').each(function() {
			var txtRequired = $(this).find('input[name="txtRequired[]"]').val();
			total_req = total_req * 1 + txtRequired * 1
		});

		$('#totReq').val(total_req.toFixed(2));
	}

	function copy_value(value, i) {
		var rowCount = $('#tbl_list_search_tc tbody tr').length;
		for (var j = i; j <= rowCount; j++) {
			document.getElementById('txtAllowance_' + j).value = value;
		}

		calculate_thread_all();
	}

	function copy_data(i, tbl_no) {
		if ($('input[name=copyRow]').is(":checked")) {
			var rowId = $("input[name='copyRow']:checked").val();
			var hiddenData = $('#hiddenData_' + rowId).val();
			var stNo = $('#trSt_' + i).find('td:first').text();
			var datas = hiddenData.split("**");
			var gsdDtlsId = datas[0];
			var breakOut = true;

			if (tbl_no == 2) var tbl_id = "tbl_list_layout2";
			else var tbl_id = "tbl_list_layout";

			$("#" + tbl_id).find('tbody tr').each(function() {
				if (breakOut == false) {
					return;
				}

				var trId = $(this).attr('id').split('_');
				var row_num = trId[1];

				if (i != row_num) {
					var currStNo = $(this).find("td:eq(0)").text();
					var currGsdDtlsId = $(this).find('input[name="gsdDtlsId[]"]').val();

					if (stNo == currStNo && gsdDtlsId == currGsdDtlsId) {
						alert("Duplicate Operation Not Allowed In Same Work Station.");
						breakOut = false;
						return false;
					}
				}
			});

			if (breakOut == true) { //alert(i+'**'+datas[1]);
				$("#gsdDtlsId_" + i).val(datas[0]);
				$("#rescId_" + i).val(datas[1]);
				$("#seqNoL_" + i).val(datas[2]);
				$("#sewingId_" + i).val(datas[3]);
				$("#smv_" + i).val(datas[4]);
				$("#tgtPercL_" + i).val(datas[5]);
				$("#operation_" + i).val(datas[6]);
				$("#rescName_" + i).val(datas[7]);
			}
		}
	}

	function add_break_down_tr(i) {
		var row_num = $('#tbl_list_search_tc tbody tr').length;
		if (row_num != i) {
			return false;
		} else {
			i++;

			$("#tbl_list_search_tc tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) {
						var id = id.split("_");
						return id[0] + "_" + i
					},
					'name': function(_, name) {
						return name
					},
					'value': function(_, value) {
						return ''
					}
				});
			}).end().appendTo("#tbl_list_search_tc");

			$("#tbl_list_search_tc tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);

			$('#txtThreadDesc_' + i).removeAttr("onFocus").attr("onFocus", "add_auto_complete(" + i + ");");
			$('#cboTheardForm_' + i).removeAttr("onchange").attr("onchange", "select_theard_form(" + i + ");");
			// $('#cboTheardForm_' + i).removeAttr("onchange").attr("onchange", "select_theard_form(" + value +"_"+ i + ");");
			$('#txtThreadLength_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_thread(" + i + ");");
			$('#txtAllowance_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_thread(" + i + ");");
			$('#txtAllowance_' + i).removeAttr("onBlur").attr("onBlur", "copy_value(this.value," + i + ");");

			$('#tr_' + i).find("td:eq(0)").text(i);
			
			$('#increaseT_' + i).removeAttr("value").attr("value", "+");
			$('#decreaseT_' + i).removeAttr("value").attr("value", "-");
			$('#increaseT_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
			$('#decreaseT_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
		}

		add_auto_complete(i);
		set_all_onclick();
	}

	function fn_deleteRow(rowNo) {
		var numRow = $('#tbl_list_search_tc tbody tr').length;
		if (rowNo != 1 && rowNo == numRow) {
			$('#tr_' + rowNo).remove();
			calculate_total_req();
		} else {
			return false;
		}
	}

	function select_theard_form(i) {
 
		var id = $('#cboTheardForm_' + i).val();
		 
		var txt_seam_length = trim($('.seam_length').val());
		var thread_length_calculation_type = trim($('#thread_length_calculation_type').val());
		
		if(thread_length_calculation_type !=0){
			var txt_consumption_factor = trim($('#txt_consumption_factor').val());
		    var txt_needle_thread = trim($('#txt_needle_thread').val());
			var txt_bobbin_thread = trim($('#txt_bobbin_thread').val());
			var needle = (txt_seam_length*txt_consumption_factor*txt_needle_thread)/100;
		    var bobbin = (txt_seam_length*txt_consumption_factor*txt_bobbin_thread)/100;
		}

		if(thread_length_calculation_type !=0){
			if(id==1){
			$('#txtThreadLength_' + i).val(needle);
			}
			else if(id==2){
				$('#txtThreadLength_' + i).val(bobbin);
			}
		}
 
		calculate_total_req();
	}


	function calculate_round_up() {
		if ($('#check_round_up').is(':checked')) {

			var numRow = $("#tbl_operation_list tbody tr").length;
			for (var i = 1; i <= numRow; i++) {
				var theoriticalMp = $('#txtTheoriticalMp_' + i).val();
				$('#txtlayOut_' + i).val(Math.ceil(theoriticalMp));
			}

			calculate_total();

		}

	}


	var method_type = <?= $method_type_id; ?>;

	function calculate_total(fromField) {
		if (!fromField) var fromField = 0;

		var totSmv = 0;
		var totTheoriticalMp = 0;
		var totLayOut = 0;
		var weight = '';
		var mpSumm = [];
		var resource_array = <? echo json_encode($production_resource); ?>;
		var helperSmv = 0;
		var machineSmv = 0;
		var sQISmv = 0;
		var fIMSmv = 0;
		var fQISmv = 0;
		var polyHelperSmv = 0;
		var pkSmv = 0;
		var htSmv = 0;
		var imSmv = 0;
		var helperMp = 0;
		var machineMp = 0;
		var sQiMp = 0;
		var fImMp = 0;
		var fQiMp = 0;
		var polyHelperMp = 0;
		var pkMp = 0;
		var htMp = 0;
		var imMp = 0;

		var numRow = $("#tbl_operation_list tbody tr").length;
		for (var i = 1; i <= numRow; i++) {
			var smv = trim($('#totalSmv_' + i).text());
			var layOut = trim($('#txtlayOut_' + i).val());
			var rescId = $('#rescId_' + i).val();


			if (rescId * 1 == 40 || rescId * 1 == 41 || rescId * 1 == 43 || rescId * 1 == 44 || rescId * 1 == 48 || rescId * 1 == 68 || rescId * 1 == 70 || rescId * 1 == 147) {

				helperSmv = helperSmv * 1 + smv * 1;
				helperMp = helperMp * 1 + layOut * 1;
			} else if (rescId * 1 == 53) {
				fIMSmv = fIMSmv * 1 + smv * 1;
				fImMp = fImMp * 1 + layOut * 1;
			} else if (rescId * 1 == 54) {
				fQISmv = fQISmv * 1 + smv * 1;
				fQiMp = fQiMp * 1 + layOut * 1;
			} else if (rescId * 1 == 55) {
				polyHelperSmv = polyHelperSmv * 1 + smv * 1;
				polyHelperMp = polyHelperMp * 1 + layOut * 1;
			} else if (rescId * 1 == 56) {
				pkSmv = pkSmv * 1 + smv * 1;
				pkMp = pkMp * 1 + layOut * 1;
			} else if (rescId * 1 == 90) {
				htSmv = htSmv * 1 + smv * 1;
				htMp = htMp * 1 + layOut * 1;
			} else if (rescId * 1 == 176 || rescId * 1 == 69) {
				imSmv = imSmv * 1 + smv * 1;
				imMp = imMp * 1 + layOut * 1;
			} else {
				machineSmv = machineSmv * 1 + smv * 1;
				machineMp = machineMp * 1 + layOut * 1;

				if (mpSumm[rescId] == undefined) mpSumm[rescId] = 0;
				//mpSumm[rescId]+= parseInt(layOut);
				mpSumm[rescId] += layOut * 1;
			}

			totSmv = totSmv * 1 + smv * 1;
			totLayOut = totLayOut * 1 + layOut * 1;

			weight = '';
			//if(parseInt(layOut)>0)
			if (layOut * 1 > 0) {
				weight = ((smv * 1) / (layOut * 1)).toFixed(2);
			}
			$('#weight_' + i).val(weight);
		}

		var html = '';
		var bgcolor = '';
		x = 1;
		var tot = 0;
		for (var i in mpSumm) {
			if (x % 2 == 0) bgcolor = '#E9F3FF';
			else bgcolor = '#FFFFFF';

			var mp_s = '';
			var str = mpSumm[i];
			var n = str.toString().indexOf(".");
			//alert(mpSumm[i]);
			if (n == -1) {
				mp_s = mpSumm[i];
			} else {
				mp_s = mpSumm[i].toFixed(2);
			}

			html += '<tr bgcolor="' + bgcolor + '"><td width="150">' + resource_array[i] + '</td><td align="right" style="padding-right:5px">' + mp_s + '</td></tr>';
			x++;
			tot = tot * 1 + mpSumm[i] * 1;
		}
		if (x % 2 == 0) bgcolor = '#E9F3FF';
		else bgcolor = '#FFFFFF';

		var n = tot.toString().indexOf(".");
		if (n != -1) {
			tot = tot.toFixed(2);
		}

		html += '<tr bgcolor="' + bgcolor + '"><td align="right" width="150"><b>Total</b></td><td align="right" style="padding-right:5px">' + tot + '</td></tr>';
		$('#tbl_mp_summ').html(html);

		$('#sh').text(helperSmv.toFixed(2));
		$('#sm').text(machineSmv.toFixed(2));
		$('#sq').text(sQISmv.toFixed(2));
		$('#fim').text(fIMSmv.toFixed(2));
		$('#fq').text(fQISmv.toFixed(2));
		$('#ph').text(polyHelperSmv.toFixed(2));
		$('#pk').text(pkSmv.toFixed(2));
		$('#ht').text(htSmv.toFixed(2));
		$('#im').text(imSmv.toFixed(2));

		var totSmvSumm = helperSmv * 1 + machineSmv * 1 + sQISmv * 1 + fIMSmv * 1 + fQISmv * 1 + polyHelperSmv * 1 + pkSmv * 1 + htSmv * 1 + imSmv * 1;
		$('#totSmvSumm').text(totSmvSumm.toFixed(2));

		var n = helperMp.toString().indexOf(".");
		if (n != -1) {
			helperMp = helperMp.toFixed(2);
		}

		var n = machineMp.toString().indexOf(".");
		if (n != -1) {
			machineMp = machineMp.toFixed(2);
		}

		var n = sQiMp.toString().indexOf(".");
		if (n != -1) {
			sQiMp = sQiMp.toFixed(2);
		}

		var n = fImMp.toString().indexOf(".");
		if (n != -1) {
			fImMp = fImMp.toFixed(2);
		}

		var n = fQiMp.toString().indexOf(".");
		if (n != -1) {
			fQiMp = fQiMp.toFixed(2);
		}

		var n = polyHelperMp.toString().indexOf(".");
		if (n != -1) {
			polyHelperMp = polyHelperMp.toFixed(2);
		}

		var n = pkMp.toString().indexOf(".");
		if (n != -1) {
			pkMp = pkMp.toFixed(2);
		}

		var n = htMp.toString().indexOf(".");
		if (n != -1) {
			htMp = htMp.toFixed(2);
		}

		$('#shm').text(helperMp);
		$('#smm').text(machineMp);
		$('#sqm').text(sQiMp);
		$('#fimm').text(fImMp);
		$('#fqm').text(fQiMp);
		$('#phm').text(polyHelperMp);
		$('#pkm').text(pkMp);
		$('#htm').text(htMp);
		$('#imm').text(imMp);

		var totMPSumm = helperMp * 1 + machineMp * 1 + sQiMp * 1 + fImMp * 1 + fQiMp * 1 + polyHelperMp * 1 + pkMp * 1 + htMp * 1 + imMp * 1;
		var n = totMPSumm.toString().indexOf(".");
		if (n != -1) {
			totMPSumm = totMPSumm.toFixed(2);
		}

		$('#totMPSumm').text(totMPSumm);

		if (totLayOut * 1 > 0 && fromField == 1) {
			$('#txt_allocated_mp').val(totLayOut);
		}

		var txt_allocated_mp = trim($('#txt_allocated_mp').val());
		var pitch_time = '';
		if (txt_allocated_mp * 1 > 0) {
			pitch_time = (totSmv / txt_allocated_mp).toFixed(2);
		}

		$('#totSmv').text(totSmv.toFixed(2));
		if (totLayOut * 1 > 0) {
			totLayOut = totLayOut.toFixed(2);
		}
		$('#totLayOut').text(totLayOut);
		$('#txt_pitch_time').val(pitch_time);

		for (var i = 1; i <= numRow; i++) {
			var smv = trim($('#totalSmv_' + i).text());
			var layOut = trim($('#txtlayOut_' + i).val());

			var theoriticalMp = '';
			var workLoad = '';
			if (pitch_time * 1 > 0) {

				if (method_type == 2) {
					var txt_efficiency_bl = trim($('#txt_efficiency_bl').val());
					theoriticalMp = (((smv / pitch_time) * txt_efficiency_bl) / 100).toFixed(2);
				} else {
					theoriticalMp = (smv / pitch_time).toFixed(2);
				}


				if (layOut * 1 > 0) {
					workLoad = (((smv / layOut) / (pitch_time)) * 100).toFixed(2);
				}
			}

			$('#txtTheoriticalMp_' + i).val(theoriticalMp);
			$('#workLoad_' + i).val(workLoad);
			totTheoriticalMp = totTheoriticalMp * 1 + theoriticalMp * 1;
		}

		$('#totTheoriticalMp').text(totTheoriticalMp.toFixed(2));

		var txt_efficiency_bl = trim($('#txt_efficiency_bl').val());
		var txt_working_hour_bl = trim($('#txt_working_hour_bl').val());
		var target = '';
		if (txt_efficiency_bl * 1 > 0 && totSmv > 0) {
			var eff_perc = txt_efficiency_bl / 100;
			target = (eff_perc * txt_allocated_mp * txt_working_hour_bl * 60) / totSmv;
			target = Math.round(target);
		}
	     $('#txt_target').val(target);
	}

	function fnc_balancing_entry(operation) {
		
		if (operation == 2) {
			if (confirm("You are going to delete all PO & country data. Are you sure?")) {
				// your deletion code
			}
			else{
				return;
			}
		}

		if (operation == 4) {
			var report_title = $("div.form_caption").html();
			var breakdown_id = $('#breakdown_id').val()
			var bl_update_id = $('#bl_update_id').val()
			generate_report_file(bl_update_id + '**' + report_title+'**'+breakdown_id, 'balancing_print', 'requires/balancing_entry_controller');
			return;
		}

		if (form_validation('txt_style_ref_bl*txt_allocated_mp*txt_efficiency_bl', 'Style Ref.*Allocated MP*Efficiency') == false) {
			return;
		}

		var txt_allocated_mp = trim($('#txt_allocated_mp').val()) * 1;
		var totLayOut = $('#totLayOut').text() * 1;
		if (txt_allocated_mp != totLayOut) {
			var r = confirm("Press \"OK\" to Excess Manpower Allocation \nPress \"Cancel\" Back To Edit");
			if (r == false) {
				return;
			}
			//alert("Allocated MP and Total Layout MP Should Be Same.");
			//return;
		}

		calculate_total(1);

		var dataString = '';
		var numRow = $("#tbl_operation_list tbody tr").length;
		for (var j = 1; j <= numRow; j++) {
			var seqNo = trim($('#seqNoB_' + j).val());
			var dtlsId = trim($('#dtlsId_' + j).val());
			var bodyPart = trim($('#bodyPart_' + j).val());
			var sewingId = trim($('#sewingId_' + j).val());
			var rescId = trim($('#rescId_' + j).val());
			var smv = trim($('#totalSmv_' + j).text());
			var tgtPerc = trim($('#tgtPerc_' + j).text());
			var cycleTime = trim($('#cycleTime_' + j).text());
			var txtTheoriticalMp = trim($('#txtTheoriticalMp_' + j).val());
			var layOut = trim($('#txtlayOut_' + j).val());
			var workLoad = trim($('#workLoad_' + j).val());
			var weight = trim($('#weight_' + j).val());
			var workerTracking = trim($('#workerTracking_' + j).val());

			dataString += '&seqNo' + j + '=' + seqNo + '&dtlsId' + j + '=' + dtlsId + '&bodyPart' + j + '=' + bodyPart + '&sewingId' + j + '=' + sewingId + '&rescId' + j + '=' + rescId + '&smv' + j + '=' + smv + '&tgtPerc' + j + '=' + tgtPerc + '&cycleTime' + j + '=' + cycleTime + '&txtTheoriticalMp' + j + '=' + txtTheoriticalMp + '&layOut' + j + '=' + layOut + '&workLoad' + j + '=' + workLoad + '&weight' + j + '=' + weight + '&workerTracking' + j + '=' + workerTracking;
		}

		var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + numRow + get_submitted_data_string('txt_style_ref*breakdown_id*txt_allocated_mp*txt_line_no*txt_efficiency_bl*txt_pitch_time*txt_target*bl_update_id*cbo_learning_cub_method_bl', "../../") + dataString;

		freeze_window(operation);
		http.open("POST", "requires/balancing_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_balancing_entry_response;
	}

	function fnc_balancing_entry_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');

			if (trim(response[0]) == 'exit') {
				alert("Not deleted balancing because operation found in layout.");
				release_freezing();
				return;
			}

			if (trim(response[0]) == 'approved') {
				alert("This Operation Bulletin is Approved");
				release_freezing();
				return;
			}

			show_msg(response[0]);

			if (response[0] == 0 || response[0] == 1) {
				$('#cbo_resource').attr('disabled', true);
				document.getElementById('bl_update_id').value = response[1];
				set_button_status(1, permission, 'fnc_balancing_entry', 2);
				drawChart(generateChartData());
			}
			release_freezing();
		}
	}

	function print_breakdown(type) {

		if (type == 2) {
			var action = "breakdown_print";
		}
		if(type == 3){
			var action = "breakdown_print2";
		}

		if (type == 2) {
			var update_id = $('#update_id').val();
			var report_title = $("div.form_caption").html();
			generate_report_file(update_id + '**' + report_title, action, 'requires/ws_gsd_controller');
			return;
		}
		if (type == 3) {
			var update_id = $('#update_id').val();
			var report_title = $("div.form_caption").html();
			generate_report_file(update_id + '**' + report_title, action, 'requires/ws_gsd_controller');
			return;
		}
	}

	function print_balancing(type) {

		if (type == 2) {
			var action = "balancing_print2";
		} else if (type == 3) {
			var action = "balancing_print3";
		} else if (type == 5) {
			var action = "balancing_print5";
		} else if (type == 7) {
			var action = "balancing_print7";
		} else if (type == 8) {
			var action = "balancing_print8";
		} else if (type == 10) {
			var action = "balancing_print10";
		}else if (type == 9) {
			var action = "balancing_print9";
		} else if (type == 4) {
			var action = "balancing_print4";

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/balancing_entry_controller.php?action=job_list_by_style_popup&txt_style_ref_bl=' + document.getElementById("txt_style_ref_bl").value + '&update_id=' + document.getElementById("update_id").value, 'Job List', 'width=440px,height=350px,center=1,resize=1,scrolling=0', '../')

			emailwindow.onclose = function() {
				var response = this.contentDoc.getElementById("data_string").value;
				if (response != "") {
					freeze_window(5);
					var report_title = $("div.form_caption").html();
					generate_report_file(response + '***' + $('#bl_update_id').val(), action, 'balancing_entry_controller');
					release_freezing();
				}
			}

		} else if (type == 6) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  W.Load% &  Weight\nPress  \"OK\"  to Show W.Load% &  Weight");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}

			var action = "balancing_print6";
			var bl_update_id = $('#bl_update_id').val();
			if (bl_update_id == "") {
				alert("Save First.");
				return;
			}
			var report_title = $("div.form_caption").html();
			generate_report_file(bl_update_id + '**' + report_title + '**' + show_item, action, 'requires/balancing_entry_controller');
			return;

		}


		if (type == 2 || type == 3 || type == 5 || type == 7 || type == 8 || type == 9 || type == 10) {
			var bl_update_id = $('#bl_update_id').val();
			if (bl_update_id == "") {
				alert("Save First.");
				return;
			}
			var report_title = $("div.form_caption").html();
			generate_report_file(bl_update_id + '**' + report_title, action, 'requires/balancing_entry_controller');
			return;
		}
	}

	$(document).ready(function() {

		// draw graph at launch
		//drawChart(generateChartData());

		$("#graph").click(function() {
			// redraw chart with graph data
			var bl_update_id = $('#bl_update_id').val();
			var breakdown_id = $('#breakdown_id').val();
			var data = breakdown_id+'_'+bl_update_id;
			// alert(bl_update_id);
			if (bl_update_id != "") {
				var graphData=trim(return_global_ajax_value( data, 'load_graph_data', '', 'requires/balancing_entry_controller')).split("**");
				var seqNos =eval(graphData[0]);
				var weights =eval(graphData[1]);
				//alert(seqNos);
				//balanceGraph();
				drawChart(generateChartData());
			}
		});

		$("#graph2").click(function() {
			// redraw chart with graph data
			var bl2_update_id = $('#bl2_update_id').val();
			if (bl2_update_id != "") {
				drawChart2(generateChartData2());
			}
		});

	});

	var canvas_html = '<canvas id="canvas" height="200" width="860"></canvas>';
	var drawChart = function(data) {
		// reinit canvas
		$('#canvas_container').html(canvas_html);

		// redraw chart
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(data, {
			responsive: true
		});
	};

	var canvas_html2 = '<canvas id="canvas2" height="200" width="860"></canvas>';
	var drawChart2 = function(data) {
		// reinit canvas
		$('#canvas_container2').html(canvas_html2);

		// redraw chart
		var ctx = document.getElementById("canvas2").getContext("2d");
		window.myLine = new Chart(ctx).Line(data, {
			responsive: true
		});
	};

	function calculate_total2() {
		var txt_worker = trim($('#txt_worker').val());
		var txt_efficiency_bl2 = trim($('#txt_efficiency_bl2').val());
		var txt_working_hour_bl2 = trim($('#txt_working_hour_bl2').val());
		var txt_tot_smv2 = trim($('#txt_tot_smv2').val()) * 1;
		var target = '';
		if (txt_worker * 1 > 0 && txt_efficiency_bl2 * 1 > 0) {
			var eff_perc = txt_efficiency_bl2 / 100;
			target = (eff_perc * txt_worker * txt_working_hour_bl2 * 60) / txt_tot_smv2;
			target = Math.round(target);
		}
		$('#txt_tgt_per_day').val(target);

		var pitch_time = '';
		if (txt_worker * 1 > 0) {
			pitch_time = (txt_tot_smv2 / txt_worker).toFixed(2);
		}
		$('#txt_pitch_time2').val(pitch_time);

		var totSmv = 0;
		var numRow = $("#tbl_operation_list2 tbody tr").length;
		for (var i = 1; i <= numRow; i++) {
			var smv = trim($('#totalSmv2_' + i).text());
			totSmv = totSmv * 1 + smv * 1;
		}
		$('#totSmv2').text(totSmv.toFixed(2));

		if (txt_worker * 1 > 0) {
			for (var k = 1; k <= txt_worker; k++) {
				$('#tgt_3_' + k).val(target);
			}
		}
	}

	function list_generate() {
		var txt_worker = trim($('#txt_worker').val()) * 1;
		var table_width = (txt_worker * 55) + 350;
		if (table_width <= 870) table_width = 870;
		var target = trim($('#txt_tgt_per_day').val());

		$('#tbl_operation_list2').removeAttr("width").attr("width", table_width);
		var numRow = $("#tbl_operation_list2 tbody tr").length;
		//var index=$('#th_1').cells.length;
		var index = document.getElementById('tbl_operation_list2').rows[0].cells.length;
		var checkIndex = index - 4;
		if (index <= 4) {
			var html = '';
			for (var j = 1; j <= txt_worker; j++) {
				html += '<th>W.' + j + '</th>';
			}
			$("#th_1").append(html);

			var html = '';
			for (var j = 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="smv[]" id="smv_2_' + j + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_2").append(html);

			var html = '';
			for (var j = 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="tgt[]" id="tgt_3_' + j + '" value="' + target + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_3").append(html);

			var html = '';
			for (var j = 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="wl[]" id="wl_4_' + j + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_4").append(html);

			for (var i = 1; i <= numRow; i++) {
				var html = '';
				for (var j = 1; j <= txt_worker; j++) {
					html += '<td align="center"><input type="text" name="wSmv[]" id="wSmv_' + i + '_' + j + '" class="text_boxes_numeric" style="width:40px" onkeyup="calculate_smv(' + i + ',' + j + ')"/></td>';
				}
				$("#trBl2_" + i).append(html);
			}

			var html = '';
			for (var j = 1; j <= txt_worker; j++) {
				html += '<th id="td_f_' + j + '"></th>';
			}
			$("#tf_1").append(html);
		} else if (txt_worker * 1 > checkIndex) {
			var html = '';
			for (var j = checkIndex + 1; j <= txt_worker; j++) {
				html += '<th>W.' + j + '</th>';
			}
			$("#th_1").append(html);

			var html = '';
			for (var j = checkIndex + 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="smv[]" id="smv_2_' + j + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_2").append(html);

			var html = '';
			for (var j = checkIndex + 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="tgt[]" id="tgt_3_' + j + '" value="' + target + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_3").append(html);

			var html = '';
			for (var j = checkIndex + 1; j <= txt_worker; j++) {
				html += '<th><input type="text" name="wl[]" id="wl_4_' + j + '" class="text_boxes_numeric" style="width:40px" readonly="readonly"/></th>';
			}
			$("#th_4").append(html);

			for (var i = 1; i <= numRow; i++) {
				var html = '';
				for (var j = checkIndex + 1; j <= txt_worker; j++) {
					html += '<td align="center"><input type="text" name="wSmv[]" id="wSmv_' + i + '_' + j + '" class="text_boxes_numeric" style="width:40px" onkeyup="calculate_smv(' + i + ',' + j + ')"/></td>';
				}
				$("#trBl2_" + i).append(html);
			}

			var html = '';
			for (var j = checkIndex + 1; j <= txt_worker; j++) {
				html += '<th id="td_f_' + j + '"></th>';
			}
			$("#tf_1").append(html);
		} else if (txt_worker * 1 < checkIndex) {
			var noOfWorker = txt_worker + 3;
			var j = 0;
			var noOfWorker2 = txt_worker;
			$("#tbl_operation_list2").find('tr').each(function() {
				j++;
				$(this).find('th').each(function() {
					var ind = $(this).index();
					if (j == 2 || j == 3 || j == 4) {
						if (ind > noOfWorker2) {
							$(this).remove();
						}
					} else {
						if (ind > noOfWorker) {
							$(this).remove();
						}
					}
				});

				$(this).find('td').each(function() {
					var ind = $(this).index();
					if (ind > noOfWorker) {
						$(this).remove();
					}
				});
			});

			/*$('#th_2').find('th:not(:first-child)').each (function() 
			{
				var ind=$(this).index();
				if(ind>noOfWorker)
				{
					$(this).remove();
				}
			});*/
		}
		set_all_onclick();
		calculate_total2();
	}

	function calculate_smv(i, j) {
		var numRow = $("#tbl_operation_list2 tbody tr").length;
		var tot_smv = trim($('#totalSmv2_' + i).text());
		var wSmv = trim($('#wSmv_' + i + '_' + j).val());
		var txt_worker = trim($('#txt_worker').val()) * 1;
		var totOpWsmv = 0;
		for (var k = 1; k <= txt_worker; k++) {
			totOpWsmv = (totOpWsmv * 1) + parseFloat(trim($('#wSmv_' + i + '_' + k).val()) * 1);
		}
		//totWsmv=totWsmv.toFixed(2);
		if (isNaN(totOpWsmv)) totOpWsmv = 0;
		else totOpWsmv = Math.round(totOpWsmv * 1e12) / 1e12;
		if ((totOpWsmv * 1) > (tot_smv * 1)) {
			alert("Worker SMV Exceeds Operation SMV");
			$('#wSmv_' + i + '_' + j).val('');
		}

		var totWsmv = 0;
		for (var z = 1; z <= numRow; z++) {
			var wSmv = trim($('#wSmv_' + z + '_' + j).val());
			if (isNaN(wSmv)) wSmv = 0;
			totWsmv = (totWsmv * 1) + wSmv * 1;
		}

		if (isNaN(totWsmv)) totWsmv = 0;
		else totWsmv = totWsmv.toFixed(2);
		$('#smv_2_' + j).val(totWsmv);
		$('#td_f_' + j).text(totWsmv);

		var pitch_time = $('#txt_pitch_time2').val();
		if (pitch_time * 1 > 0 && totWsmv > 0) {
			var wLoad = ((totWsmv / pitch_time) * 100).toFixed(2);
			$('#wl_4_' + j).val(wLoad);
		} else {
			$('#wl_4_' + j).val('');
		}
	}

	function fnc_balancing2_entry(operation) {
		if (operation == 2) {
			return;
		}

		if (operation == 4) {
			var report_title = $("div.form_caption").html();
			generate_report_file($('#bl2_update_id').val() + '**' + report_title, 'balancing_print', 'requires/balancing2_entry_controller');
			return;
		}

		if (form_validation('txt_worker*txt_efficiency_bl2*txt_max_wl*txt_min_wl*cbo_gmt_item_bl2', 'No. Of Worker*Efficiency*Max Work Load %*Max Work Load %*Min Work Load %*Garments Item') == false) {
			return;
		}

		var smvString = '';
		var tgtString = '';
		var wlString = '';
		var txt_worker = trim($('#txt_worker').val());
		for (var z = 1; z <= txt_worker * 1; z++) {
			if (smvString == "") smvString = $('#smv_2_' + z).val();
			else smvString += "_" + $('#smv_2_' + z).val();
			if (tgtString == "") tgtString = $('#tgt_3_' + z).val();
			else tgtString += "_" + $('#tgt_3_' + z).val();
			if (wlString == "") wlString = $('#wl_4_' + z).val();
			else wlString += "_" + $('#wl_4_' + z).val();
		}

		var dataString = '';
		var numRow = $("#tbl_operation_list2 tbody tr").length;
		for (var j = 1; j <= numRow; j++) {
			var sewingId = trim($('#sewingId_' + j).val());
			var seqNo = trim($('#seqNoBl_' + j).val());
			var rescId = trim($('#rescId_' + j).val());
			var dtlsId = trim($('#dtlsId_' + j).val());

			dataString += '&sewingId' + j + '=' + sewingId + '&seqNo' + j + '=' + seqNo + '&rescId' + j + '=' + rescId + '&dtlsId' + j + '=' + dtlsId;
			for (var z = 1; z <= txt_worker * 1; z++) {
				dataString += get_submitted_data_string('wSmv_' + j + '_' + z, "../../", 2);
			}
		}

		var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + numRow + get_submitted_data_string('breakdown_id2*txt_worker*txt_efficiency_bl2*txt_max_wl*txt_min_wl*txt_tot_smv2*txt_tgt_per_day*txt_pitch_time2*bl2_update_id*cbo_learning_cub_method_bl2', "../../", 1) + dataString + '&smvString=' + smvString + '&tgtString=' + tgtString + '&wlString=' + wlString;

		freeze_window(operation);
		http.open("POST", "requires/balancing2_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_balancing2_entry_response;
	}

	function fnc_balancing2_entry_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');
			if (trim(response[0]) == 'approved') {
				alert("This Operation Bulletin is Approved");
				release_freezing();
				return;
			}
			show_msg(response[0]);

			if (response[0] == 0 || response[0] == 1) {
				document.getElementById('bl2_update_id').value = response[1];
				set_button_status(1, permission, 'fnc_balancing2_entry', 3);
				drawChart2(generateChartData2());
			}
			release_freezing();
		}
	}

	function fnc_thread_consumption_entry(operation) {
		if (operation == 4) {
			print_report($('#bl3_update_id').val(), "print", "requires/thread_consumption_controller");
			return;
		}

		if (form_validation('breakdown_id3*txt_operation_name*cbo_uom', 'Breakdown System ID*Operation Name*Input UOM') == false) {
			return;
		}

		var dataString = '';
		var i = 0;
		var numRow = $("#tbl_list_search_tc tbody tr").length;
		for (var j = 1; j <= numRow; j++) {
			var cboThreadType = trim($('#cboThreadType_' + j).val());
			var txtThreadDesc = trim($('#txtThreadDesc_' + j).val());
			var txtThreadLength = trim($('#txtThreadLength_' + j).val());
			var txtAllowance = trim($('#txtAllowance_' + j).val());
			var txtRequired = trim($('#txtRequired_' + j).val());

			if (cboThreadType * 1 > 0 && txtRequired * 1 > 0) {
				i++;
				dataString += '&cboThreadType' + i + '=' + cboThreadType + '&txtThreadDesc' + i + '=' + txtThreadDesc + '&txtThreadLength' + i + '=' + txtThreadLength + '&txtAllowance' + i + '=' + txtAllowance + '&txtRequired' + i + '=' + txtRequired;
			}
		}

		if (i < 1) {
			alert('No data');
			return;
		}

		var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + i + get_submitted_data_string('txt_body_size*txt_cons_date*cbo_uom*breakdown_id3*bl3_update_id*operation_id*dtlsId_gsd*update_dtlsId*txt_seam_length', "../../", 1) + dataString;

		freeze_window(operation);
		http.open("POST", "requires/thread_consumption_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_thread_consumption_entry_response;
	}

	function fnc_thread_consumption_entry_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');
			if (trim(response[0]) == 'approved') {
				alert("This Operation Bulletin is Approved");
				release_freezing();
				return;
			}
			if (response[0] == 40) {
				alert(response[1]);
				release_freezing();
				return;
			}

			show_msg(response[0]);
			if (response[0] == 0 || response[0] == 1 || response[0] == 2) {
				document.getElementById('bl3_update_id').value = response[1];
				document.getElementById('txt_tot_required').value = response[2];
				document.getElementById('txt_required_into_meter').value = response[3];
				show_list_view(response[1], 'details_list_view', 'operation_details_tc', 'requires/thread_consumption_controller', '');

				if (response[0] == 0) {
					var tot_row = $('#list_view_tc tbody tr').length;
					var rowNo = $('#rowNo').val() * 1 + 1;
					for (var i = rowNo * 1; i <= tot_row; i++) {
						var is_machine = $('#is_machine_' + i).val();

						if (is_machine == 1) {
							$('#trTc_' + i).trigger('click');
							var e_color = document.getElementById('trTc_' + i).style.backgroundColor;
							change_color_tr(i, e_color);
							break;
						}
					}
				} else {
					var tot_row = $('#list_view_tc tbody tr').length;
					for (var i = 1; i < tot_row; i++) {
						if (i % 2 == 0) Bcolor = "#E9F3FF";
						else Bcolor = "#FFFFFF";

						document.getElementById("trTc_" + i).bgColor = Bcolor;
					}

					$('#txt_operation_name').val('');
					$('#operation_id').val('');
					$('#dtlsId_gsd').val('');
					$('#txt_seam_length').val('');
					$('#cbo_resource_tc').val(0);
					$('#txt_fabric_description').val('');
				}

				/*$('#tbl_list_search_tc tbody tr:not(:first)').remove();
				var html='<tr id="tr_2" bgcolor="#E9F3FF" align="center"><td align="left">2</td><td><? echo create_drop_down("cboThreadType_2", 130, $size_color_sensitive, "", 1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]"); ?></td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_2" placeholder="Write" class="text_boxes" style="width:180px" onFocus="add_auto_complete( 2 )" /></td><td><input type="text" name="txtThreadLength[]" id="txtThreadLength_2" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(2)" /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_2" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(2)" onblur="copy_value(this.value,2);" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_2" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td><td align="center"><input type="button" id="increaseT_2" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(2)"/><input type="button" id="decreaseT_2" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(2);" /></td></tr><tr id="tr_3" bgcolor="#FFFFFF" align="center"><td align="left">3</td><td><? echo create_drop_down("cboThreadType_3", 130, $size_color_sensitive, "", 1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]"); ?></td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_3" placeholder="Write" class="text_boxes" style="width:180px" onFocus="add_auto_complete( 3 )" /></td><td><input type="text" name="txtThreadLength[]" id="txtThreadLength_3" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(3)" /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_3" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(3)" onblur="copy_value(this.value,3);" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_3" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td><td align="center"><input type="button" id="increaseT_3" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(3)"/><input type="button" id="decreaseT_3" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(3);" /></td></tr><tr id="tr_4" bgcolor="#E9F3FF" align="center"><td align="left">4</td><td><? echo create_drop_down("cboThreadType_4", 130, $size_color_sensitive, "", 1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]"); ?></td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_4" placeholder="Write" class="text_boxes" style="width:180px" onFocus="add_auto_complete( 4 )" /></td><td><input type="text" name="txtThreadLength[]" id="txtThreadLength_4" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(4)" /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_4" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(4)" onblur="copy_value(this.value,4);" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_4" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td><td align="center"><input type="button" id="increaseT_4" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(4)"/><input type="button" id="decreaseT_4" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(4);" /></td></tr>';
				
				$('#txtThreadLength_1').val('');
				$('#txtRequired_1').val('');
				
				$("#tbl_list_search_tc tbody").append(html);*/

				$("#tbl_list_search_tc").find('tbody tr').each(function() {
					$(this).find('input[name="txtThreadLength[]"]').val('');
					$(this).find('input[name="txtRequired[]"]').val('');
				});

				$('#update_dtlsId').val('');
				$('#totReq').val('');
				$('#txtThreadLength_1').focus();

				set_button_status(0, permission, 'fnc_thread_consumption_entry', 4, 1);
			} else if (response[0] == 11) {
				alert("Duplicate Operation Not Allowed.");
			}
			release_freezing();
		}
	}

	function js_set_value_tc(data) {
	 
		var datas = data.split("_");
		var operation_id = datas[1];
		get_php_form_data(datas[0], "populate_data_from_operation", "requires/thread_consumption_controller");
		$('#dtlsId_gsd').val(datas[0]);
		$('#rowNo').val(datas[2]);
	}

	function resetFormTC() {
	     //$needle $bobbin
		$('#tbl_list_search_tc tbody tr').remove();

		var html = '<tr id="tr_1" bgcolor="#FFFFFF" align="center"><td align="left">1</td><td><? echo create_drop_down("cboThreadType_1", 100, $size_color_sensitive, "", 1, "-- Select --", 1, "", "", "1,3", "", "", "", "", "", "cboThreadType[]"); ?></td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_1" placeholder="Write" class="text_boxes" style="width:130px" onFocus="add_auto_complete( 1 )" /></td><td><? echo create_drop_down("cboTheardForm_1", 100, $ThreadFormArr, "", 1, "-- Select --", $needle1, "select_theard_form(this.value)", "", "", "", "", "", "", "", "cboTheardForm[]"); ?></td><td><input type="text" name="txtFrequency[]" id="txtFrequency_1" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread(1)"/></td><td <?=$needle_title;?>><input type="text" name="txtThreadLength[]" id="txtThreadLength_1" placeholder="Write" class="text_boxes_numeric" style="width:90px" <?=$thread_length;?> /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_1" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread(1)" onblur="copy_value(this.value,1);" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_1" class="text_boxes_numeric" style="width:90px" placeholder="Calculative" readonly="readonly" /></td><td><input type="button" id="increaseT_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"/><input type="button" id="decreaseT_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /></td></tr><tr id="tr_2" bgcolor="#E9F3FF" align="center"><td align="left">2</td><td><? echo create_drop_down("cboThreadType_2", 100, $size_color_sensitive, "", 1, "-- Select --", 1, "", "", "1,3", "", "", "", "", "", "cboThreadType[]"); ?></td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_2" placeholder="Write" class="text_boxes" style="width:130px" onFocus="add_auto_complete( 2 )" /></td><td><? echo create_drop_down("cboTheardForm_2", 100, $ThreadFormArr, "", 1, "-- Select --", $bobbin1, "select_theard_form(this.value)", "", "", "", "", "", "", "", "cboTheardForm[]"); ?></td><td><input type="text" name="txtFrequency[]" id="txtFrequency_2" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread(2)"/></td><td  <?=$bobbin_title;?>><input type="text" name="txtThreadLength[]" id="txtThreadLength_2" placeholder="Write" class="text_boxes_numeric" style="width:90px" <?=$thread_length;?> /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_2" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread(2)" onblur="copy_value(this.value,2);" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_2" class="text_boxes_numeric" style="width:90px" placeholder="Calculative" readonly="readonly" /></td><td align="center"><input type="button" id="increaseT_2" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(2)"/><input type="button" id="decreaseT_2" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(2);" /></td></tr>';

		$('#txt_operation_name').val('');
		$('#operation_id').val('');
		$('#dtlsId_gsd').val('');
		$('#update_dtlsId').val('');
		$('#txt_seam_length').val('');
		$('#totReq').val('');
		$('#cbo_resource_tc').val(0);
		$('#txt_fabric_description').val('');

		$("#tbl_list_search_tc tbody").html(html);

		var bl_update_id = document.getElementById('bl3_update_id').value;
		if (bl_update_id == "") var print_button = 0;
		else var print_button = 1;
		set_button_status(0, permission, 'fnc_thread_consumption_entry', 4, print_button);
	}

	function generate_report_file(data, action, page) {
		window.open(page + ".php?data=" + data + '&action=' + action, true);
	}

	function showResultLayout() {
		var update_id = document.getElementById('update_id').value;
		if (update_id != '') {
			//reset_form('','list_operation_container*reArrange_seqNo','','','');
			//$('#tbl_operation_list2 tbody tr').remove();
			$('#list_operation_container').hide();
			$('#reArrange_seqNo').hide();
			$('#list_operation_container_thread').show();

			var totRows = $('#tbl_layout_list tbody tr').length;
			var lo_update_id = '';
			if (totRows < 1) {
				get_php_form_data(update_id, "populate_data_from_breakdown", "requires/layout_entry_controller");
				show_list_view(update_id, 'show_summary', 'summary_list_view', 'requires/layout_entry_controller', '');
				lo_update_id = $('#lo_update_id').val();
			}
			show_list_view(update_id + "_" + lo_update_id, 'show_operation_list', 'list_operation_container_thread', 'requires/layout_entry_controller', 'setFilterGrid(\'list_view_lo\',-1);');
		}
	}

	function add_tr(i, table_id) {
		var row_num = $('#txt_tot_row').val();
		row_num++;

		var clone = $("#trSt_" + i).clone();
		clone.attr({
			id: "trSt_" + row_num,
		});

		clone.find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) {
					var id = id.split("_");
					return id[0] + "_" + row_num
				},
				'name': function(_, name) {
					return name
				},
				'value': function(_, value) {
					return value
				}
			});
		}).end();

		$("#trSt_" + i).after(clone);
		$('#trSt_' + row_num).find(":input:not(:button)").val('');

		$('#txtOrginal_' + row_num).removeAttr("value").attr("value", "0");
		$('#rescName_' + row_num).removeAttr("onclick").attr("onclick", "copy_data(" + row_num + "," + table_id + ");");

		$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_tr(" + row_num + "," + table_id + ");");
		$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "deleteRow(" + row_num + "," + table_id + ");");

		$('#txt_tot_row').val(row_num);
		set_all_onclick();
	}

	function deleteRow(rowNo, table_id) {
		var txtOrginal = $('#txtOrginal_' + rowNo).val() * 1;
		if (txtOrginal == 0) {
			$("#trSt_" + rowNo).remove();
		} else {
			$("#rescName_" + rowNo).val('');
			$("#rescId_" + rowNo).val('');
			$("#gsdDtlsId_" + rowNo).val('');
			$("#seqNoL_" + rowNo).val('');
			$("#operation_" + rowNo).val('');
			$("#sewingId_" + rowNo).val('');
			$("#smv_" + rowNo).val('');
			$("#tgtPercL_" + rowNo).val('');
		}
	}

	function resetLayout() {
		$('#tbl_layout_list tbody tr').remove();
		showResultLayout();
	}

	function load_data() {
		var update_id = document.getElementById('update_id').value;
		var line_shape = document.getElementById('cbo_line_shape').value;
		var lo_update_id = document.getElementById('lo_update_id').value;
		var balanceId = document.getElementById('balanceId').value;
		var no_of_work_st = document.getElementById('txt_no_of_work_st').value;

		if (form_validation('cbo_line_shape', 'Line Shape') == false) {
			return;
		}

		if (balanceId != "") {
			var list_view_opration = return_global_ajax_value(update_id + "**" + balanceId + "**" + line_shape + "**" + lo_update_id + "**" + no_of_work_st, 'details_list_view', '', 'requires/layout_entry_controller');
			$('#layout_list_view').html(trim(list_view_opration));
		}
	}

	function fnc_layout_entry(operation) {
		if (operation == 4) {
			print_report($('#lo_update_id').val() + "**" + $('#balanceId').val(), "layout_print", "requires/layout_entry_controller");
			return;
		}
		if (operation == 2) {

			if (confirm("You are going to delete all PO & country data. Are you sure?")) {
				// your deletion code
			}
			else{
				return;
			} 
		}
		if (form_validation('txt_style_ref_lo*cbo_line_shape*txt_no_of_work_st', 'Style Ref.*Line Shape*No Of Work Station') == false) {
			return;
		}

		var dataString = '';
		var i = 0;
		$("#tbl_list_layout").find('tbody tr').each(function() {
			var rescId = $(this).find('input[name="rescId[]"]').val();
			var gsdDtlsId = $(this).find('input[name="gsdDtlsId[]"]').val();
			var seqNo = $(this).find('input[name="seqNoL[]"]').val();
			var sewingId = $(this).find('input[name="sewingId[]"]').val();
			var smv = $(this).find('input[name="smv[]"]').val();
			var tgtPerc = $(this).find('input[name="tgtPercL[]"]').val();
			var stNo = $(this).find("td:eq(0)").text();

			//if(rescId*1>0)
			if (smv * 1 > 0) {
				i++;
				dataString += '&rescId' + i + '=' + rescId + '&gsdDtlsId' + i + '=' + gsdDtlsId + '&seqNo' + i + '=' + seqNo + '&sewingId' + i + '=' + sewingId + '&smv' + i + '=' + smv + '&tgtPerc' + i + '=' + tgtPerc + '&stNo' + i + '=' + stNo;
			}
		});

		if ($('#cbo_line_shape').val() == 2) {
			$("#tbl_list_layout2").find('tbody tr').each(function() {
				var rescId = $(this).find('input[name="rescId[]"]').val();
				var gsdDtlsId = $(this).find('input[name="gsdDtlsId[]"]').val();
				var seqNo = $(this).find('input[name="seqNoL[]"]').val();
				var sewingId = $(this).find('input[name="sewingId[]"]').val();
				var smv = $(this).find('input[name="smv[]"]').val();
				var tgtPerc = $(this).find('input[name="tgtPercL[]"]').val();
				var stNo = $(this).find("td:eq(0)").text();

				if (smv * 1 > 0) //rescId
				{
					i++;
					dataString += '&rescId' + i + '=' + rescId + '&gsdDtlsId' + i + '=' + gsdDtlsId + '&seqNo' + i + '=' + seqNo + '&sewingId' + i + '=' + sewingId + '&smv' + i + '=' + smv + '&tgtPerc' + i + '=' + tgtPerc + '&stNo' + i + '=' + stNo;
				}
			});
		}

		if (i < 1) {
			alert('No data');
			return;
		}
 

		var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + i + get_submitted_data_string('cbo_line_shape*txt_no_of_work_st*txt_layout_date*breakdown_id4*lo_update_id*balanceId', "../../", 1) + dataString;

		freeze_window(operation);
		http.open("POST", "requires/layout_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_layout_entry_response;
	}

	function fnc_layout_entry_response() {
		if (http.readyState == 4) {
			//release_freezing(); return;
			var response = trim(http.responseText).split('**');
			if (trim(response[0]) == 'approved') {
				alert("This Operation Bulletin is Approved");
				release_freezing();
				return;
			}
			if (trim(response[0]) == 'exit') {
				alert("Not deleted layout because thread consumption found.");
				release_freezing();
				return;
			}
			show_msg(response[0]);

			if (response[0] == 0 || response[0] == 1) {
				document.getElementById('lo_update_id').value = response[1];
				var update_id = $('#breakdown_id4').val();
				show_list_view(update_id + "_" + response[1], 'show_operation_list', 'list_operation_container_thread', 'requires/layout_entry_controller', 'setFilterGrid(\'list_view_lo\',-1);');
				set_button_status(1, permission, 'fnc_layout_entry', 5);
			}
			release_freezing();
		}
	}

	function fnc_excle_layout_entry() {
		var data = "action=excel_print" + get_submitted_data_string('lo_update_id*balanceId', "../../");
		http.open("POST", "requires/layout_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_excle_layout_entry_response;
	}

	function fnc_excle_layout_entry_response() {
		if (http.readyState == 4) {
			release_freezing();
			var file_data = http.responseText.split("****");
			if (file_data[2] == 100) {
				$('#data_panel').html(file_data[0]);
				$('#print_excel').removeAttr('href').attr('href', 'requires/' + trim(file_data[1]));
				//$('#print_excel20')[0].click();
				document.getElementById('print_excel').click();
			} else {
				$('#data_panel').html(file_data[0]);
			}
		}
	}

	function change_color_tr(v_id, e_color) {
		var tot_row = $('#list_view_tc tbody tr').length;
		//alert(tot_row);
		for (var i = 1; i < tot_row; i++) {
			if (v_id == i) {
				document.getElementById("trTc_" + v_id).bgColor = "#33CC00";
			} else {
				if (i % 2 == 0) Bcolor = "#E9F3FF";
				else Bcolor = "#FFFFFF";
				document.getElementById("trTc_" + i).bgColor = Bcolor;
			}
		}
	}

	function variable_setting_work_study() {
		var response = return_global_ajax_value($("#cbo_company_id").val(), 'variable_setting_work_study', '', 'requires/ws_gsd_controller');
		var response = response.trim();
		if (response == 1) {
			// browse from quick costing 
			$('#txt_style_ref').attr("ondblclick", "openmypage_quotation(1)");
			$("#txt_style_ref").removeAttr("placeholder");
			$("#txt_style_ref").attr("placeholder", 'Browse');
			$("#txt_style_id").val(0);
			$("#txt_style_ref").val('');
		} else if (response == 2) {
			// browse from Quotation Inquery
			$('#txt_style_ref').attr("ondblclick", "openmypage_quotation(2)");
			$("#txt_style_ref").removeAttr("placeholder");
			$("#txt_style_ref").attr("placeholder", 'Browse');
			$("#txt_style_id").val(0);
			$("#txt_style_ref").val('');

		} else {
			$('#txt_style_ref').attr("ondblclick", "");
			$("#txt_style_ref").removeAttr("placeholder");
			$("#txt_style_ref").attr("placeholder", '');
			$("#txt_style_ref").val('');
			$("#txt_style_id").val(0);
		}

		$('#pending_style_btn').attr(`onclick`, `fn_pending_style_popup(${response})`);

		
	}


	function fn_pending_style_popup(type){

		if (form_validation('cbo_buyer', 'Buyer Name') == false) {
			return;
		}


		var page_link = 'requires/ws_gsd_controller.php?action=pending_style_popup&type='+type+'&buyer_id='+$('#cbo_buyer').val();
		var title = "Pending Style List";
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=400px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function() {
				$("#txt_style_ref").val(this.contentDoc.getElementById("hidden_selected_style_ref").value);
				$("#cbo_gmt_item").val(this.contentDoc.getElementById("hidden_selected_item_id").value);
				$("#txt_offer_qnty").val(this.contentDoc.getElementById("hidden_selected_offer_qnty").value);
				$("#txt_style_id").val('');
			}
	}

	function openmypage_quotation(type) {
 
		if (type == 1) {
			var page_link = 'requires/ws_gsd_controller.php?action=quotation_popup_quick_costing&cbo_company_id='+document.getElementById("cbo_company_id").value;
			var title = "Search Popup";
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var datStr = this.contentDoc.getElementById("hidden_inquiry_id").value; // mrr number
				 
				var dataArr = datStr.split('_');
				var quotation_id = dataArr[0];
				var buyer_id = dataArr[1];
				$("#hidden_quotation_id").val(quotation_id);
				if(buyer_id !=''){
					$('#cbo_buyer').val(buyer_id);
					document.getElementById('cbo_buyer').disabled = true;
				}
				get_php_form_data(quotation_id, "populate_data_from_data_quick_costing", "requires/ws_gsd_controller");
			}
		}
		else {
			 
			var page_link = 'requires/ws_gsd_controller.php?action=quotation_popup_inq&cbo_company_id='+document.getElementById("cbo_company_id").value;
			var title = "Search  Popup";
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0];
				var quotation_id = this.contentDoc.getElementById("hidden_inquiry_id").value; // mrr number
				$("#hidden_quotation_id").val(quotation_id);
				get_php_form_data(quotation_id, "populate_data_from_data", "requires/ws_gsd_controller");
			}
		}
	}

	function openmypage_style_ref() {
		var cbo_buyer = $('#cbo_buyer').val();
		var title = 'Style Ref';
		var page_link = 'requires/ws_gsd_controller.php?action=style_ref_popup&cbo_buyer=' + cbo_buyer;

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0', '../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0] //("search_order_frm"); //Access the form inside the modal window
			var style_id = this.contentDoc.getElementById("hidden_style_id").value;
			var buyer_id = this.contentDoc.getElementById("hidden_buyer_id").value;
			var style_ref = this.contentDoc.getElementById("hidden_style_ref").value;
			var product_dep_id = this.contentDoc.getElementById("hidden_product_dep_id").value;
			load_drop_down('requires/ws_gsd_controller', style_id, 'load_drop_down_gmt_item', 'gmt_item_td');


			$('#txt_style_id').val(style_id);
			$('#cbo_buyer').val(buyer_id);
			$('#txt_style_ref').val(style_ref);
			$('#cbo_product_department').val(product_dep_id);

			$('#cbo_buyer').attr('disabled', true);
			$('#txt_style_ref').attr('readonly', true);
			//$('#txt_internal_ref').attr('readonly',true);

		}
	}

	function change_colors(v_id, e_color) {
		var clss;
		$('td').click(function() {
			var myCol = $(this).index();
			clss = 'res' + myCol;

		});

		if (document.getElementById(v_id).bgColor == "yellow") {
			document.getElementById(v_id).bgColor = e_color;
			$('.' + clss).removeAttr('bgColor');
		} else {
			document.getElementById(v_id).bgColor = "yellow";
			$('.' + clss).attr('bgColor', 'yellow');
		}
	}

	function fnc_itemChange(val) {
		if (val == 1) {
			$('#cbo_buyer').attr('disabled', false);
			$('#cbo_gmt_item').attr('disabled', false);
			$('#cbo_bulletin_type').attr('disabled', false);
			$('.btn_copy_extension').val('Copy Bulletin');
		} else {
			$('#cbo_buyer').attr('disabled', true);
			$('#cbo_gmt_item').attr('disabled', true);
			$('#cbo_bulletin_type').attr('disabled', true);
			$('.btn_copy_extension').val('Extention Bulletin');
		}
	}


	/*
	  $(document).bind('paste', function(e) {
	       alert('Paste is not allowed ');
	       e.preventDefault();
	    });

	 */

	$(function() {
		$(':text,textarea').bind('paste input', removeAlphaChars);
	})

	function removeAlphaChars(e) {
		var self = $(this);
		setTimeout(function() {
			var initVal = self.val(),
				// outputVal = initVal.replace(/[&\/\\#,+()$~%.'"^_:*?<>{}]/g, '');
				//outputVal = initVal.replace(/[&\/\\#()$~'"^_*]/g, ''); //.-,%@!/<>?+[]{};:
				outputVal = initVal.replace(/[&\\#()$~'"^_*]/g, ''); //.-,%@!/<>?+[]{};:
			if (initVal != outputVal) self.val(outputVal);
		});
	}



	let all_operation_check = () => {
		$('#tbl_details').find('input[type="checkbox"]').each(function() {
			$(this).prop('checked', document.getElementById('all_operation_check').checked);
		});
	}


	let generate_operation_sticker = () => {
		var operationIdArr = Array();
		$('#tbl_details').find('input[type="checkbox"]').each(function() {
			if ($(this).prop('checked')) {
				operationIdArr.push($(this).val());
			}
		});

		if (operationIdArr.length == 0) {
			release_freezing();
			alert("Please select Operation.");
			return;
		} else {
			let operationIdStr = operationIdArr.join(',');

			var data = "action=generate_operation_sticker&operation=" + operation + '&operationidstr=' + operationIdStr + '&update_id=' + $('#update_id').val() + '&cbo_process_id=' + $('#cbo_process_id').val();

			freeze_window(operation);
			http.open("POST", "requires/ws_gsd_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = () => {
				if (http.readyState == 4) {
					window.open(http.responseText);
					release_freezing();
				}

			}
		}

	}
 
	function openmypage_job() {
		var cbo_buyer = $('#cbo_buyer').val();
		var txt_style_ref = $('#txt_style_ref').val();
		var cbo_bulletin_type = $('#cbo_bulletin_type').val();
		var txt_internal_ref = $('#txt_internal_ref').val();

		if (cbo_bulletin_type != 4) {
			document.getElementById('cbo_bulletin_type').style.backgroundImage = '-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			showMsgText('Please Select Bulletin Type Production')
			return;
		}


		var page_link = 'requires/ws_gsd_controller.php?action=job_no_popup&txt_style_ref=' + txt_style_ref + '&cbo_bulletin_type=' + cbo_bulletin_type + '&cbo_buyer=' + cbo_buyer+ '&txt_internal_ref=' + txt_internal_ref;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Job No', 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');
		emailwindow.onclose = function() {

			//var theform=this.contentDoc.forms[0];
			var datStr = this.contentDoc.getElementById("hidden_data_str").value;
			var dataArr = datStr.split('**');

			var is_entry_found = return_global_ajax_value(dataArr[0], 'check_duplicate_entry', '', 'requires/ws_gsd_controller');
			if(is_entry_found != 0){
				alert("This Job already add in Operation Bulletin\n" + is_entry_found);
			}
		
			$('#txt_job_id').val(dataArr[0]);
			$('#txt_job_no').val(dataArr[1]);
			$('#cbo_gmt_item').val(dataArr[2]);
			$('#txt_style_ref').val(dataArr[3]);
			$('#cbo_buyer').val(dataArr[4]);
			$('#txt_internal_ref').val(dataArr[5]);
		}
	}

	function openmypage_sampleReq()
	{
		var cbo_buyer = $('#cbo_buyer').val();
		var txt_style_ref = $('#txt_style_ref').val();
		var cbo_bulletin_type = $('#cbo_bulletin_type').val();
		var txt_internal_ref = $('#txt_internal_ref').val();

		if (cbo_bulletin_type == 0 || cbo_bulletin_type == 3 || cbo_bulletin_type == 4) {
			document.getElementById('cbo_bulletin_type').style.backgroundImage = '-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			showMsgText('Please Select Bulletin Type RnD Or Marketting')
			return;
		}
		var page_link = 'requires/ws_gsd_controller.php?action=sampleReq_popup&txt_style_ref=' + txt_style_ref + '&cbo_bulletin_type=' + cbo_bulletin_type + '&cbo_buyer=' + cbo_buyer+ '&txt_internal_ref=' + txt_internal_ref;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sample Req.', 'width=720px, height=370px, center=1, resize=1, scrolling=0', '../');
		emailwindow.onclose = function() {

			//var theform=this.contentDoc.forms[0];
			var datStr = this.contentDoc.getElementById("sample_hidden_data_str").value;
			var dataArr = datStr.split('**');
			//alert(dataArr);

			var is_entry_found = return_global_ajax_value(dataArr[0], 'check_duplicate_entry', '', 'requires/ws_gsd_controller');
			if(is_entry_found != 0){
				alert("This Req. already add in Operation Bulletin\n" + is_entry_found);return;
			}
			else{
				$('#txt_job_id').val(dataArr[0]);
				$('#txt_req_no').val(dataArr[1]);
				$('#txt_style_ref').val(dataArr[2]);
				$('#cbo_buyer').val(dataArr[3]); 
			}
		
			
		}
	}
</script>
</head>

<body onLoad="set_hotkey();variable_setting_work_study();">
	<div style="width:100%;">
		<? echo load_freeze_divs("../../", $permission); ?>
		<div style="width:880px; float:left;" align="center">
			<div id="examples" style="width:880px;"></div>
			<div id="example" style="width:880px; margin-top:10px;">
				<ul class="tabs">
					<li><a href="#gsd_entry" onClick="showData();">Breakdown</a></li>
					<li><a href="#balancing_entry" onClick="showResult();" id="graph">Balancing.</a></li>
					<li><a href="#balancing2_entry" onClick="showResult2();" id="graph2">Balancing2</a></li>
					<li><a href="#layout_entry" onClick="showResultLayout();">Layout</a></li>
					<li><a href="#thread_consumption" onClick="showResult3();">Thread Consumption</a></li>
				</ul>
				<div id="gsd_entry"><?php include('gsd_entry.php'); ?></div>
				<div id="balancing_entry"><?php include('balancing_entry.php'); ?></div>
				<div id="balancing2_entry"><?php include('balancing2_entry.php'); ?></div>
				<div id="layout_entry"><?php include('layout_entry.php'); ?></div>
				<div id="thread_consumption"><?php include('thread_consumption.php'); ?></div>
			</div>
		</div> 
		<div style="width:10px; overflow:auto; float:left; padding-top:1px; margin-top:1px; position:relative;"></div>
		<div style="float:left; padding-top:1px; margin-top:1px; position:relative;">

			<h3 id="accordion_h1" style="width:425px" class="accordion_h" onClick="accordion_menu( this.id,'list_operation_container', '')">-</h3>

			<div id="list_operation_container"></div>
			<div id="reArrange_seqNo" style="margin-top:5px;overflow:auto;"></div>
			<div id="list_operation_container_thread" style="margin-top:5px;overflow:auto; "></div>
		</div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var generateChartData = function() {
		 
		var bl_update_id = $('#bl_update_id').val();

		var breakdown_id = $('#breakdown_id').val();
	   var data = breakdown_id+'_'+bl_update_id;
 
		var seqNos = '';
		var weights = '';
		var ucls = '';
		var pitchTimes = '';
		var lcls = '';
		if (bl_update_id != "") {
			var graphData = trim(return_global_ajax_value(data, 'load_graph_data', '', 'requires/balancing_entry_controller')).split("**");
			seqNos = eval(graphData[0]);
			weights = eval(graphData[1]);
			ucls = eval(graphData[2]);
			pitchTimes = eval(graphData[3]);
			lcls = eval(graphData[4]);
		}
		return {
			labels: seqNos,
			datasets: [{
					//label: "My First dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#7D60A0",
					pointColor: "#7D60A0",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#7D60A0",
					data: weights
				},
				{
					//label: "My Second dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#BE4B48",
					pointColor: "#BE4B48",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#BE4B48",
					data: ucls
				},
				{
					//label: "My Third dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#4A7EBB",
					pointColor: "#4A7EBB",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#4A7EBB",
					data: pitchTimes
				},
				{
					//label: "My Fourth dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#98B954",
					pointColor: "#98B954",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#98B954",
					data: lcls
				}
			]
		};
	};

	var generateChartData2 = function() {
		var bl2_update_id = $('#bl2_update_id').val();
		var tittles = '';
		var weights = '';
		var maxWl = '';
		var minWl = '';
		if (bl2_update_id != "") {
			var graphData = trim(return_global_ajax_value(bl2_update_id, 'load_graph_data', '', 'requires/balancing2_entry_controller')).split("**");
			tittles = eval(graphData[0]);
			weights = eval(graphData[1]);
			maxWl = eval(graphData[2]);
			minWl = eval(graphData[3]);
		}
		return {
			labels: tittles,
			datasets: [{
					//label: "My First dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#7D60A0",
					pointColor: "#7D60A0",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#7D60A0",
					data: weights
				},
				{
					//label: "My Fourth dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#BE4B48",
					pointColor: "#BE4B48",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#BE4B48",
					data: maxWl
				},
				{
					//label: "My Second dataset",
					fillColor: "rgba(220,220,220,0.2)",
					strokeColor: "#98B954",
					pointColor: "#98B954",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "#98B954",
					data: minWl
				}
			]
		};
	};
</script>
<script>
	$(document).ready(function() { 
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>