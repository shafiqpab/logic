<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan 
Creation date 	: 	24-05-2023
Updated  		: 	
Update date 	: 
Purpose			:
QC Performed BY	:		
QC Date			:	
Comments		: 
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
$u_id = $_SESSION['logic_erp']['user_id'];
$level = return_field_value("user_level", "user_passwd", "id='$u_id' and valid=1 ", "user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Wash Receive", "../../", 1, 1, $unicode, '', '');

function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}

?>

<script>
	function dynamic_must_entry_caption(data)
	{
		if(data==1)
		{
			//$('#cbo_company_name').css('color','blue');
			$('#locations').css('color','blue');
			$('#floors').css('color','blue');
		}
		else
		{
			//$('#cbo_company_name').css('color','blue');
			$('#locations').css('color','black');
			$('#floors').css('color','black');
		}
	}
		<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][198]);
		if ($data_arr)
			echo "var field_level_data= " . $data_arr . ";\n";
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][198] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		// echo "alert(JSON.stringify(field_level_data));";
		?>
		var tableFilters = {}
		var permission = '<? echo $permission; ?>';
		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

		$(document).ready(function(e) {
		
			$("#txt_ex_quantity").keyup(function() {
				// alert('ok');
				var txt_user_lebel = $('#txt_user_lebel').val();
				var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;
				var sewing_production_variable = $('#sewing_production_variable').val() * 1;
				var hidden_preceding_process = $('#hidden_preceding_process').val() * 1;
				var txt_yet_quantity = $('#txt_yet_quantity').val() * 1;
				var txt_cumul_quantity = $('#txt_cumul_quantity').val() * 1;
				var is_update_mood = $('#is_update_mood').val() * 1;
				var placeholderVal = document.getElementById("txt_ex_quantity").getAttribute("placeholder") * 1;
				if (is_update_mood) {
					txt_yet_quantity = txt_yet_quantity + placeholderVal;
				}
				// if (is_update_mood) {txt_yet_quantity = txt_yet_quantity+txt_cumul_quantity;}
				// alert(is_update_mood);
				var delvery_qty = $(this).val();

				if (sewing_production_variable == 1) {
					if (delvery_qty * 1 > txt_yet_quantity * 1) {
						if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
							alert("Qnty Excceded by " + txt_yet_quantity);
							$(this).val('');
						} else {
							if (confirm("Qnty Excceded by " + txt_yet_quantity))
								void(0);
							else {
								$(this).val('');
							}
						}

					}
				}
			});
		});

	



	function fnc_washReceive_entry(operation) 
	{		
			if (form_validation('cbo_company_name*txt_order_no*txt_issue_date*cbo_source*cbo_location*cbo_wash_company*cbo_wash_location*cbo_floor', 'Company Name*Order No*Date*Source*Location*Wash Company*Wash Location*Floor') == false) 
			{
				 return;
				
			}
			else
			{
				freeze_window(operation);
				var sewing_production_variable = $("#sewing_production_variable").val();
				if (sewing_production_variable == "" || sewing_production_variable == 0) {
					sewing_production_variable = 3;
				}
				// alert (sewing_production_variable);
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i = 0;
				var colorIDvalue = '';
				if (sewing_production_variable == 2) //color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if ($(this).val() != '') {
							if (i == 0) {
								colorIDvalue = colorList[i] + "*" + $(this).val();
							} else {
								colorIDvalue += "**" + colorList[i] + "*" + $(this).val();
							}
						}
						i++;
					});
				} else if (sewing_production_variable == 3) //color and size level
				{
					$("input[name=colorSize]").each(function(index, element) {
						if ($(this).val() != '') {
							if (i == 0) {
								colorIDvalue = colorList[i] + "*" + $(this).val();
							} else {
								colorIDvalue += "***" + colorList[i] + "*" + $(this).val();
							}
						}
						i++;
					});
				}
				// alert('cbo_company_name');
				var data = "action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue + get_submitted_data_string('garments_nature*txt_system_id*txt_system_no*cbo_company_name*styleOrOrderWisw*variable_is_controll*txt_qty_source*is_update_mood*hidden_variable_cntl*hidden_preceding_process*cbo_location*cbo_source*cbo_wash_company*cbo_wash_location*cbo_floor*txt_issue_date*txt_challan*txt_remark*cbo_country_name*txt_order_no*hidden_po_break_down_id*hid_job_num*txt_job_no*cbo_buyer_name*txt_style_ref*cbo_item_name*sewing_production_variable*hidden_colorSizeID*txt_trimming_qty*txt_receive_qty*hidden_break_down_html*hidden_colorSizeID*txt_cumul_quantity*txt_yet_quantity*txt_mst_id', "../../");

				// alert(data);
				
				http.open("POST", "requires/wash_receive_controller.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_washReceive_entry_Reply_info;
				
			}	
			
		}
	

	function fnc_washReceive_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			// var variableSettingsReject=$('#finish_production_variable_rej').val();
			var item_id=$('#cbo_item_name').val();
			// var po_id=$('#hidden_po_break_down_id').val();
			var country_id = $("#cbo_country_name").val();
			var pack_type='';
			
			var reponse=trim(http.responseText).split('**');
			
	
			if(reponse[0]==0)
			{ 
				document.getElementById('txt_system_no').value = reponse[2];
				document.getElementById('txt_system_id').value = reponse[3];
				var po_id = reponse[1];
				var system_id = reponse[3];
				
			
				show_msg(reponse[0]);
				
				show_list_view(system_id,'show_listview','wash_receive_list_view','requires/wash_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			
				
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/wash_receive_controller" );
				release_freezing();
				set_button_status(0, '____1', 'fnc_washReceive_entry',1,1);		
			}
			if(reponse[0]==1)
			{
				// alert()
				
				document.getElementById('txt_system_id').value = reponse[2];
				var po_id = reponse[1];
				var system_id = reponse[2];
				show_msg(reponse[1]);
			
				show_list_view(system_id,'show_listview','wash_receive_list_view','requires/wash_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			
				release_freezing();

				set_button_status(0, permission, 'fnc_washReceive_entry',1,0);
			}
			if(reponse[0]==2)
			{
				if(reponse[4]==2)
				{
					var po_id = reponse[1];
					show_msg(trim(reponse[0]));
					// var txt_job_no=$('#txt_job_no').val();
					show_list_view(reponse[3],'show_dtls_listview','wash_receive_list_view','requires/wash_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
					// reset_form('','breakdown_td_id','txt_finishing_qty*txt_carton_qty*txt_remark*txt_finish_input_qty*txt_cumul_delivery_qty*txt_yet_to_delivery*hidden_break_down_html*txt_mst_id*txt_order_no*hidden_po_break_down_id*txt_job_no*txt_style_no*txt_order_qty*cbo_item_name*cbo_country_name*txt_country_qty*cbo_buyer_name','','','txt_delivery_date');
					// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/garments_finishing_delivery_entry_controller" );
					
					release_freezing();
					
				}
				if(reponse[4]==1)
				{
					release_freezing();
					location.reload();
				}	
				set_button_status(0, permission, 'fnc_washReceive_entry',1,0);
			}

			if(reponse[0]==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			
		}
	} 

	function childFormReset() 
	{
		reset_form('', '', '*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_receive_qty*txt_cumul_quantity*txt_yet_quantity', '', '');
		$('#txt_receive_qty').attr('placeholder', ''); //placeholder value initilize
		$('#txt_cumul_quantity').attr('placeholder', ''); //placeholder value initilize
		$('#txt_yet_quantity').attr('placeholder', ''); //placeholder value initilize wash_receive_list_view
		//$("#wash_receive_list_view").html('');
		$("#breakdown_td_id").html('');
		//disable_enable_fields( "cbo_location*cbo_transport_company*txt_issue_date*txt_truck_no*txt_lock_no*txt_driver_name*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_destination*cbo_forwarder", 0 );
		disable_enable_fields("cbo_location*cbo_company_name*txt_challan_no*cbo_source*cbo_wash_company*cbo_wash_location*cbo_floor*cbo_country_name*cbo_buyer_name*txt_style_ref*cbo_item_name", 0);



	}

	

	function fn_total(tableName, index) // for color and size level
	{
		var filed_value = $("#colSize_" + tableName + index).val();
		var placeholder_value = $("#colSize_" + tableName + index).attr('placeholder');
		
		var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;
		if (filed_value * 1 > placeholder_value * 1) {
			if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
				alert("Qnty Excceded by" + (placeholder_value - filed_value));
				$("#colSize_" + tableName + index).val('');
				$("#txt_receive_qty").val('');
			} else {
				if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
					void(0);
				else {
					$("#colSize_" + tableName + index).val('');
				}
			}

		}

		var totalRow = $("#table_" + tableName + " tr").length;
		//alert(tableName);
		math_operation("total_" + tableName, "colSize_" + tableName, "+", totalRow);
		if ($("#total_" + tableName).val() * 1 != 0) {
			$("#total_" + tableName).html($("#total_" + tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_receive_qty").val(totalVal);
		

	
	}
	

	
	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_" + index).val();
		var placeholder_value = $("#colSize_" + index).attr('placeholder');
		var txt_user_lebel = $('#txt_user_lebel').val();
		var hidden_variable_cntl = $('#hidden_variable_cntl').val() * 1;
		if (filed_value * 1 > placeholder_value * 1) {
			if (hidden_variable_cntl == 1 && txt_user_lebel != 2) {
				alert("Qnty Excceded by" + (placeholder_value - filed_value));
				$("#colSize_" + index).val('');
				$("#txt_receive_qty").val('');
			} else {
				if (confirm("Qnty Excceded by" + (placeholder_value - filed_value)))
					void(0);
				else {
					$("#colSize_" + index).val('');
				}
			}

		}

		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation("total_color", "colSize_", "+", totalRow);
		$("#txt_receive_qty").val($("#total_color").val());
		
		
	}


	function delivery_sys_popup() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_name").val();
		var company_id = $("#cbo_company_name").val();
		var title = "Wash Recive Popup";
		/*var page_link='requires/wash_receive_controller.php?action=sys_surch_popup&company='+company;*/
		var page_link = 'requires/wash_receive_controller.php?action=sys_surch_popup&company_id=' + company_id;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1140px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose = function() {
			var delivery_id = this.contentDoc.getElementById("hidden_delivery_id").value;
			// alert(delivery_id);return;
		
			if (delivery_id != "") {
				// $("#txt_order_qty").val(po_qnty);
				// $("#cbo_item_name").val(item_id);
				// $("#cbo_country_name").val(country_id);
				// freeze_window(5);
				
				var ex_data=delivery_id.split('_');
				let delivery_mst_id=ex_data[0];
				$('#txt_system_no').val(ex_data[0]);
				$('#cbo_company_name').val(ex_data[1]);
				load_drop_down( 'requires/wash_receive_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				
			
				
				if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				load_drop_down('requires/wash_receive_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_order_no').val(ex_data[8]);
				$('#txt_job_no').val(ex_data[9]);
				$('#txt_issue_date').val(ex_data[10]);
		
				
				$('#txt_challan').val(ex_data[14]);
				$('#txt_remark').val(ex_data[15]);
				$('#txt_style_ref').val(ex_data[16]);
				$('#cbo_buyer_name').val(ex_data[17]);
				$("#cbo_company_name").attr('disabled', 'true');
				
				get_php_form_data(delivery_mst_id, "populate_mst_form_data", "requires/wash_receive_controller");
				
				show_list_view(delivery_mst_id,'show_listview','wash_receive_list_view','requires/wash_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			
				
				// reset_form('', 'breakdown_td_id"', 'hidden_break_down_html**txt_issue_date*hidden_colorSizeID*txt_trimming_qty*txt_remark*txt_receive_qty*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_style_ref*txt_order_qty*cbo_item_name*cbo_country_name*cbo_buyer_name*txt_order_no', '', '');

		

				// setFilterGrid("details_table",-1);
				set_button_status(0, permission, 'fnc_washReceive_entry', 1, 1);
				release_freezing();

			}

		}

	}

	
	function openmypage(page_link,title)
{
	var company = $("#cbo_company_name").val();
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
		// alert(po_id);return

		var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
	
		// var trim_qnty=this.contentDoc.getElementById("hidden_plancut_qnty").value;
		var country_id=this.contentDoc.getElementById("hidden_country_id").value;
		var job_num=this.contentDoc.getElementById("hid_job_num").value;
		var company_id=this.contentDoc.getElementById("hid_company_id").value;

		if (po_id!="")
		{
			// freeze_window(5);
			$("#cbo_item_name").val(item_id);
			//$("#txt_order_qty").val(po_qnty);
			//$("#txt_trim_qty").val(trim_qnty);
			$("#cbo_country_name").val(country_id);
			$("#hid_job_num").val(job_num);
			//$("#txt_job_no").val(job_num);
			$("#cbo_company_name").val(company_id);
			$("#hidden_po_break_down_id").val(po_id);
			// fnc_company_check(3);
			

	//			load_drop_down( 'requires/trimming_complete_controller',company_id, 'load_drop_down_location', 'location_td' );
			get_php_form_data(company_id,'load_variable_settings','requires/wash_receive_controller');
			//get_php_form_data(company_id,'load_variable_settings_reject','requires/wash_receive_controller');
			//console.log('variable_reject');
			console.log('reset');
			get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/wash_receive_controller" );
			// console.log('#hidden_preceding_process');
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			//var variableSettingsReject=$('#cutting_production_variable_reject').val();
			var garments_nature=$('#garments_nature').val();
			console.log(variableSettings);

			if(variableSettings!=1)
			{
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/wash_receive_controller" );
			}
			// else
			// {
			// 	$("#txt_trim_qty").removeAttr("readonly");
			// }

			// if(variableSettingsReject!=1)
			// {
			// 	$("#txt_reject_qty").attr("readonly");
			// }
			// else
			// {
			// 	$("#txt_reject_qty").removeAttr("readonly");
			// }

			show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','requires/wash_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id,'show_country_listview','list_view_country','requires/wash_receive_controller','');
 			set_button_status(0, permission, 'fnc_cutting_update_entry',0);
 			load_drop_down( 'requires/wash_receive_controller', po_id, 'load_drop_down_color_type', 'color_type_td');
 				release_freezing();
 			
		}
		$("#cbo_company_name").attr("disabled","disabled");
		
		childFormReset();//child from reset
	}
}
	
	function active_placeholder_qty(color_id) {
		$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
			if ($('#set_all_' + color_id).prop('checked') == true) {
				if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
					$(this).val($(this).attr('placeholder'));
				}
			} else {
				$(this).val('');
			}
		});

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_ex_quantity").val(totalVal);		

		// ================== set delivery amount ======================
		let rate = $('#txt_commission_amt').val();
		let amount = rate*totalVal*1;

		$('#txt_order_amt').val(amount);
	}



	function refres_function()
	{
		$("#cbo_source").val(0);
		$("#cbo_del_company").val(0);

		$("#cbo_source").removeAttr('disabled', '');
		$("#cbo_del_company").removeAttr('disabled', '');
	}	

	function fnc_load_party(type)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_source').val(1);
			return;
		}
		var source=$('#cbo_source').val();
		var company = $('#cbo_company_name').val();
		var working_company = $('#cbo_wash_company').val();
		var location_name = $('#cbo_location').val();
		
		if(source==1 && type==1)
		{
			load_drop_down( 'requires/wash_receive_controller', company+'_'+1, 'load_drop_down_working_com', 'wash_company_td' );
		}
		else if(source==3 && type==1)
		{
			load_drop_down( 'requires/wash_receive_controller', company+'_'+3, 'load_drop_down_working_com', 'wash_company_td' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/wash_receive_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs("../", $permission);  ?>
		<div style="width:1180px; float:left" align="center">
			<form name="washReceive_1" id="washReceive_1" autocomplete="off">
				<fieldset style="width:1180px;">
					<!-- <legend>Production Module</legend> -->
					<fieldset>
						<table width="1180px" border="0">
							<tr>
								<td colspan="4" align="right"><b>Receive NO : </b></td>
								<td colspan="4">
									<input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:160px" onDblClick="delivery_sys_popup()" placeholder="Browse or Search" />
									<input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" style="width:160px" />
								</td>
							</tr>	
							
							<tr>
								<td width="100" class="must_entry_caption">Lc. Company</td>
								<td width="140"><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/wash_receive_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_source').value);get_php_form_data(this.value,'load_variable_settings','requires/wash_receive_controller');",0); ?>
								<input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
								<input type="hidden" id="styleOrOrderWisw" />
								<input type="hidden" id="variable_is_controll" />
							
								<input type="hidden" id="txt_qty_source" />
								<input type="hidden" id="is_update_mood" />
							
								<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
								<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
								</td>

								<td width="100" class="must_entry_caption">Lc Location</td>
								<td width="140" id="location_td"><? echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "--Select Location--", $selected, "" ); ?></td>

								<td width="100" class="must_entry_caption">Source</td>
								<td width="140"><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "--Select Source--", $selected, "fnc_load_party(1,this.value); dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>

								<td width="100" class="must_entry_caption">WC. Company</td>
								<td id="wash_company_td"><? echo create_drop_down( "cbo_wash_company", 130, $blank_array,"", 1, "-Wash. Company-", $selected, "" ); ?></td>

								<td id="locations" class="must_entry_caption">WC. Location</td>
								<td id="working_location_td"><? echo create_drop_down( "cbo_wash_location", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
			
                       		</tr>
					
                            <tr>
					                         
								<td id="floors" class="must_entry_caption" >Floor</td>
								<td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1, "--Select Floor--", $selected, "" ); ?></td>

								<td class="must_entry_caption">Issue Date</td>
								<td><input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;"  /></td>

								<td>Challan No</td>
								<td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:120px" /></td>
								<td>Remarks</td>
								<td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:120px"  /></td>

								<td width="130">Country</td>
								<td width="170"><? echo create_drop_down( "cbo_country_name", 130, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?> 
								</td>
						
                        	</tr>
						                      			
							<tr>
								
								<td width="110" class="must_entry_caption">Order No</td>
								<td>
									 <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/wash_receive_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:100px " readonly />
									 <input type="hidden" id="hidden_po_break_down_id" value="" />
									 <input type="hidden" id="hid_job_num" value="" />
								 </td>
											
								<td>Job.NO</td>
								<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" disabled />
							    </td>

								<td>Buyer Name</td>
								<td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
							
								<td>Style Ref.</td>
								<td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" disabled />
							</td>
						
							
								<td> Gmts. Item</td>
								<td>
									<? echo create_drop_down( "cbo_item_name", 130, $garments_item,"0", 1, "-- Select Item --", $selected, "",1,0 );	?>
								</td>  
								
							</tr>
							
						</table>
					</fieldset>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td width="30%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										
									<table  width="350px">
										<tr>
											<td width="100" >Total Trimming. Qty</td>
											<td>
												<input type="text" name="txt_trimming_qty" id="txt_trimming_qty" class="text_boxes_numeric"  style="width:100px"  >
											
										</tr>
											
										<tr>
											<td>Receive In  Wash</td>
											<td>
												<input type="text" name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric"  style="width:100px"  >
												<input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
												<input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
											</td>
										</tr>
										<tr>
											<td>Cumulative Receive</td>
											<td>
												<input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity"  class="text_boxes_numeric"  style="width:100px"  ></td>
											</td>
										</tr>
										<tr>
											<td >Yet To Receive</td>
											<td>
												<input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes" style="width:100px" />
											</td>
										</tr>
                          
									</table>
								</fieldset>
							</td>
						
							<td width="41%" valign="top">
								<div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
								<div style="max-height:130px; font-size:24px; color:red; overflow-y:scroll" id="posted_msg_td_id" align="center"></div>
							</td>
						</tr>
					</table>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_washReceive_entry", 0, 1, "reset_form('washReceive_1','wash_receive_list_view*list_view_country','','','childFormReset()');refres_function()", 1);
								?>
								<input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly value="0">


								

							</td>
						</tr>
					</table>
					<div style="width:1000px; margin-top:5px;" >
               		<!-- <div style="width:900px; margin-top:5px;" id="printing_cost_list_view" align="center"></div> -->
					</div>
				</fieldset>
			</form>
		</div>
		<div  style="width:700px;float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px" id="wash_receive_list_view" align="center"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$(function(){
		for (var property in mandatory_field_arr) {			
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>