<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Material Allocation

Functionality	:
JS Functions	:
Created by		:	MONZU
Creation date 	: 	22-06-2013
Updated by 		: 	JAHID HASAN
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Material Allocation", "../../", 1, 1, $unicode);
?>
<style>
	.hilight { background: #33CC00; }
</style>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function openmypage_booking(page_link, title)
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=390px,center=1,resize=1,scrolling=0', '../')
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var booking_no = this.contentDoc.getElementById("selected_booking").value;
			var booking_type = this.contentDoc.getElementById("booking_type").value;
			var is_short = this.contentDoc.getElementById("is_short").value;
			var po_ids = this.contentDoc.getElementById("po_ids").value;


			if (booking_no != "")
			{
				freeze_window(5);
				reset_form('materialallocation_1', '', '', 'cbo_item_category,1*txt_allocation_date,<? echo date("d-m-Y"); ?>', '');
				get_php_form_data(booking_no, "populate_data_from_search_popup", "requires/yarn_allocation_controller");
				show_list_view(booking_no, 'fabric_description_list', 'booking_list', 'requires/yarn_allocation_controller', '');
				show_list_view(document.getElementById('txt_job_no').value+ '_' + po_ids+ '_' + booking_no, 'yarn_description_list', 'yarn_list', 'requires/yarn_allocation_controller', '');
				show_list_view(document.getElementById('txt_job_no').value + '_' + document.getElementById('cbo_item_category').value + '_' + document.getElementById('txt_booking_no').value+ '_' + booking_type+ '_' + is_short, 'show_item_active_listview', 'item_list_view', 'requires/yarn_allocation_controller', '');
				release_freezing();
			}
		}
	}

	function open_item_popup(page_link, title)
	{
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var cbo_item_category = document.getElementById('cbo_item_category').value;
		var txt_booking_qnty = document.getElementById('txt_booking_qnty').value
		var txt_item_id = document.getElementById('txt_item_id').value;
		var job_no = document.getElementById('txt_job_no').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		if (cbo_company_name == 0)
		{
			alert("Select Company Name");
			return;
		}

		page_link = page_link + '&cbo_company_name=' + cbo_company_name + '&cbo_item_category=' + cbo_item_category + '&txt_booking_qnty=' + txt_booking_qnty + '&txt_item_id=' + txt_item_id+ '&job_no=' + job_no+ '&txt_booking_no=' + txt_booking_no+ '&cbo_buyer_name=' + cbo_buyer_name;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1270px,height=450px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function ()
		{

			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("product_id");
			var theemail_number = this.contentDoc.getElementById("product_name");
			var theemail_qnty = this.contentDoc.getElementById("available_qnty");
			var theemail_uom = this.contentDoc.getElementById("unit_of_measurment");
			var dyed_type = this.contentDoc.getElementById("dyed_type").value*1;
			var item_avg_rate_usd = this.contentDoc.getElementById("item_avg_rate_usd").value*1;

			if(dyed_type==1)
			{
				//$("#txt_qnty").removeAttr("onClick").attr("placeholder", "Write");
				//$("#txt_order_no").val('');
				//$("#txt_order_id").val('');				
			}

			document.getElementById('hdn_dyed_type').value=dyed_type ;
			var is_stopexecution = 0;
			$("#grey_yarn_list tr td,#dyied_yarn_list tr td").find("input").each(function()
			{
				var product_id = this.value;
				if(theemail.value == product_id)
				{
					is_stopexecution = 1;
					alert("This Lot is already allocated in this Booking.");
					return;
				}
			});

			if (theemail.value != "" && (is_stopexecution==0))
			{
				freeze_window(5);
				document.getElementById('txt_item_id').value = theemail.value;
				document.getElementById('txt_item').value = theemail_number.value;
				document.getElementById('available_qnty').value = theemail_qnty.value;
				document.getElementById('cbo_uom').value = theemail_uom.value;
				document.getElementById('item_avg_rate_usd').value = item_avg_rate_usd;

				release_freezing();
			}
		}
	}

	function open_qnty_popup(page_link, title)
	{
		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var during_issue = document.getElementById('during_issue').value*1;
		var control_level = document.getElementById('control_level').value*1;
		var tolerant_percent = document.getElementById('tolerant_percent').value*1;
		var txt_order_id = document.getElementById('txt_order_id').value;
		var txt_item = document.getElementById('txt_item').value;
		var txt_item_id = document.getElementById('txt_item_id').value;
		var item_avg_rate_usd = document.getElementById('item_avg_rate_usd').value;		
		var available_qnty = document.getElementById('available_qnty').value;
		var txt_qnty = document.getElementById('txt_qnty').value;
		var qnty_breck_down = document.getElementById('qnty_breck_down').value;
		var pre_qnty_breck_down = document.getElementById('pre_qnty_breck_down').value;
		var txt_booking_qnty = document.getElementById('txt_booking_qnty').value;
		var txt_entry_form = document.getElementById('txt_entry_form').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var update_id = document.getElementById('update_id').value;
		var txt_old_qnty = document.getElementById('txt_old_qnty').value;

		if(update_id == "")
		{
			var txt_selectted_fabric = document.getElementById('txt_selectted_fabric').value;
			var txt_fabric_po = document.getElementById('txt_fabric_po').value;
			var txt_fab_booking_qnty = document.getElementById('txt_fab_booking_qnty').value;
		}
		else
		{
			var txt_selectted_fabric = "";
			var txt_fabric_po = "";
			var txt_fab_booking_qnty = "";
		}

		if (txt_entry_form != 108)
		{
			if (txt_order_id == 0)
			{
				alert("Select Order No");
				return;
			}
		}

		if (txt_item == "")
		{
			alert("You did not select any Yarn");
			document.getElementById('txt_item').focus();
			return;
		}


		if(during_issue==1 && control_level==2)
		{
			var width = "1320";	
		}else{
			var width = "920";	
		}

		page_link = page_link + '&txt_order_id=' + txt_order_id + '&available_qnty=' + available_qnty + '&txt_qnty=' + txt_qnty + '&qnty_breck_down=' + qnty_breck_down + '&txt_booking_qnty=' + txt_booking_qnty + '&txt_job_no=' + txt_job_no + '&txt_entry_form=' + txt_entry_form + '&txt_booking_no=' + txt_booking_no + '&update_id=' + update_id + '&txt_old_qnty=' + txt_old_qnty + '&txt_selectted_fabric='+txt_selectted_fabric + '&txt_fabric_po=' + txt_fabric_po + '&txt_fab_booking_qnty=' + txt_fab_booking_qnty + '&pre_qnty_breck_down=' + pre_qnty_breck_down + '&item_id='+ txt_item_id + '&item_avg_rate_usd='+ item_avg_rate_usd +  '&cbo_company_name='+ cbo_company_name + '&during_issue='+ during_issue + '&control_level='+ control_level+ '&tolerant_percent='+ tolerant_percent;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=350px,center=1,resize=0,scrolling=0', '../')
		
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("allocated_qnty");
			
			var theemail_number = this.contentDoc.getElementById("qnty_breck_down");
			if (theemail.value != "")
			{
				freeze_window(5);
				document.getElementById('txt_qnty').value = theemail.value;
				document.getElementById('qnty_breck_down').value = theemail_number.value;
				release_freezing();
			}
		}
	}

	function fnc_material_allocation_entry(operation)
	{
		var qnty = parseFloat(document.getElementById("txt_qnty").value);
		var txt_order_no = document.getElementById("txt_order_no").value;
		var txt_job_no = document.getElementById("txt_job_no").value;
		var dyied_qnty = parseFloat($("#txt_qnty").attr("data-dyied-qnty"));
		var txt_old_qnty =  document.getElementById("txt_old_qnty").value*1;
		
		/*
		if(operation==0 || operation==1)
		{
			if (qnty > (dyied_qnty+txt_old_qnty)) {
				alert("Quantity can not be greater than Dyied Yarn Received Quantity");
				return;
			}
		} 
		*/

		if(txt_order_no=="")
		{
			if (form_validation('txt_booking_no*txt_allocation_date*txt_item*txt_qnty', 'Booking No*Allocation Date*Yarn*Yarn Qty') == false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('txt_booking_no*txt_job_no*txt_allocation_date*txt_item*txt_qnty', 'Booking No*Job No*Allocation Date*Yarn*Yarn Qty') == false)
			{

				return;
			}
		}

		if(qnty < 0)
		{
			if (form_validation('txt_qnty', 'Qnty') == false)
			{
				return;
			}
		}

		if (confirm("Are You Sure?"))
		{
			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_job_no*txt_order_id*txt_item_id*txt_item_id_old*txt_qnty*qnty_breck_down*cbo_item_category*txt_allocation_date*update_id*txt_old_qnty*txt_booking_no*cbo_company_name*pre_qnty_breck_down*hdn_dyed_type*hdn_is_without_order*txt_remarks', "../../");
			freeze_window(operation);
			http.open("POST", "requires/yarn_allocation_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_allocation_entry_reponse;
		}
	}

	function fnc_material_allocation_entry_reponse()
	{
		if (http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse = trim(http.responseText).split('**');
			
			if (reponse[0] == 'vbqwor')
			{
				alert("Over Allocated Qnty Not Allowed than Booking Qnty");
				release_freezing();
				return;
			}			
			else if(trim(reponse[0])=='CTV')
			{
				alert("Yarn Count, Type and rate not match with Budget");
				release_freezing();
				return;
			}
			if (reponse[0].length > 2)
				reponse[0] = 10;
				
			show_msg(reponse[0]);
			if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)
			{
				show_list_view(document.getElementById('txt_job_no').value + '_' + document.getElementById('cbo_item_category').value + '_' + document.getElementById('txt_booking_no').value + '_' + document.getElementById('hdn_booking_type').value + '_' + document.getElementById('hdn_is_short').value, 'show_item_active_listview', 'item_list_view', 'requires/yarn_allocation_controller', '');
				reset_form('', '', 'txt_item_id_old*txt_item_id*txt_old_qnty*txt_item*txt_qnty*cbo_uom*available_qnty*qnty_breck_down*update_id*txt_selectted_fabric*txt_fabric_po*txt_fab_booking_qnty', 'cbo_item_category,1*txt_allocation_date,<? echo date("d-m-Y"); ?>', '');
				$("#txt_qnty").removeAttr("data-dyied-qnty");

				set_button_status(0, permission, 'fnc_material_allocation_entry', 1);
			}
			
			if (reponse[0] == 3)
			{
				alert(reponse[1]);
				set_button_status(0, permission, 'fnc_material_allocation_entry', 1);
			}
			else if (reponse[0] == 5)
			{
				alert(reponse[1]);
			}
			else if (reponse[0] == 6)
			{
				alert(reponse[1]);
			}
			else if (reponse[0] == 7)
			{
				alert(reponse[1]);
			}
		} 
		release_freezing();
	}

	function remove_qnty_popup(qnty_element, id, qnty)
	{
		/*if (id == 1) {
			$("#" + qnty_element).removeAttr("onClick").attr("data-dyied-qnty", qnty);
		} else {
			$("#" + qnty_element).attr("onClick", "open_qnty_popup( 'requires/yarn_allocation_controller.php?action=open_qnty_popup','Qnty List')").removeAttr("data-dyied-qnty");
		}*/
	}

	function fabric_order_list(i)
	{
		var txt_booking_no = document.getElementById('txt_booking_no').value;
		var fab_booking_qnty = document.getElementById('fab_booking_qnty_'+i).value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var order_no = document.getElementById('order_no_'+i).value;
		var lib_yarn_count_deter_id = document.getElementById('lib_yarn_count_deter_id_'+i).value;

		var item_number_id = document.getElementById('item_number_id_'+i).value;
		var gsm_weight = document.getElementById('gsm_weight_'+i).value;
		var width_dia_type = document.getElementById('width_dia_type_'+i).value;
		var thisSelector = document.getElementById('fab_list_'+i);
		$("#booking_list table tr").not(thisSelector).removeClass('hilight');
		if($(thisSelector).hasClass("hilight"))
		{
			$(thisSelector).removeClass('hilight');
			$("#txt_selectted_fabric").val("");
			$("#txt_fabric_po").val("");
			$("#txt_yarn_count_deterId").val("");
			$("#txt_fab_booking_qnty").val("");
			$("#fabric_po_list").html("");
		}
		else
		{
			$(thisSelector).addClass('hilight');
			$("#txt_selectted_fabric").val(item_number_id + "_" + gsm_weight + "_" + width_dia_type);
			$("#txt_fabric_po").val(order_no);
			$("#txt_yarn_count_deterId").val(lib_yarn_count_deter_id);
			$("#txt_fab_booking_qnty").val(fab_booking_qnty);

			show_list_view(txt_booking_no + '_' + txt_job_no + '_' + item_number_id + '_' + gsm_weight + '_' + width_dia_type + '_' + order_no + '_' + lib_yarn_count_deter_id, 'fabric_order_list', 'fabric_po_list', 'requires/yarn_allocation_controller', '');
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission); ?>
		<div style="float:left; margin-left:5px">
			<fieldset style="width:950px;height:auto;">
				<legend>Material Allocation</legend>
				<form name="materialallocation_1" id="materialallocation_1" autocomplete="off">
					<table cellpadding="0" cellspacing="2" width="100%">
						<tr>
							<td width="130" class="must_entry_caption"> Booking No</td>
							<td width="170">
								<input class="text_boxes" type="text" style="width:160px"
								onDblClick="openmypage_booking('requires/yarn_allocation_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
								<input type="hidden" name="txt_entry_form" id="txt_entry_form" placeholder="Display" class="text_boxes"/>
							</td>
							<td width="110" class="must_entry_caption" id="caption_job_no">Job No</td>
							<td width="190">
								<input name="txt_job_no" id="txt_job_no" placeholder="Display" readonly style="width:158px;" class="text_boxes"/>
							</td>
							<td width="110">Company</td>
							<td width="190">
								<?php
								echo create_drop_down("cbo_company_name", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "Display", "", "load_drop_down( 'requires/yarn_allocation_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )", 1);
								?>
								<input type="hidden" name="during_issue" id="during_issue"  readonly/>
								<input type="hidden" name="control_level" id="control_level"  readonly/>
								<input type="hidden" name="tolerant_percent" id="tolerant_percent" readonly/>

							</td>
						</tr>
						<tr>
							<td width="130">Buyer</td>
							<td id="buyer_td" width="170">
								<?php
								echo create_drop_down("cbo_buyer_name", 170, $blank_array, "", 1, "Display", "", "", 1, "", "", "", "");
								?>
							</td>
							<td width="130">Order No</td>
							<td width="170">
								<input type="text" name="txt_order_no" id="txt_order_no" style="width:160px "
								class="text_boxes" readonly/>  <!--placeholder="Click to Search"-->
								<input type="hidden" name="txt_order_id" id="txt_order_id" style="width:160px " readonly/>
							</td>
							<td width="100">Item Category</td>
							<td id="" width="200">
								<?php
								echo create_drop_down("cbo_item_category", 170, $item_category, "", "", "", 1, "", 1);
								?>
							</td>
						</tr>
						<tr>
							<td width="130" class="must_entry_caption">Allocation Date</td>
							<td width="170">
								<input type="text" name="txt_allocation_date" id="txt_allocation_date" style="width:160px"
								value="<? echo date("d-m-Y") ?>" class="datepicker" readonly/>
							</td>
							<td width="130">Allocated Yarn</td>
							<td width="170">
								<input type="text" name="txt_item" id="txt_item" style="width:160px;" placeholder="Click to Search" class="text_boxes"
								onClick="open_item_popup( 'requires/yarn_allocation_controller.php?action=open_item_popup','Item List' );"/>
								<input type="hidden" name="txt_item_id" id="txt_item_id" style="width:160px "/>
								<input type="hidden" name="txt_item_id_old" id="txt_item_id_old" style="width:160px "/>
								<input type="hidden" name="item_avg_rate_usd" id="item_avg_rate_usd" style="width:160px "/>
							</td>
							<td width="110" class="must_entry_caption">Qnty</td>
							<td id="section_td" width="190">
								<input type="text" name="txt_qnty" id="txt_qnty" style="width:90px;" value="" class="text_boxes_numeric" placeholder="Click" readonly 
								onClick="open_qnty_popup( 'requires/yarn_allocation_controller.php?action=open_qnty_popup','Qnty List' )"/>
								<?php
								echo create_drop_down("cbo_uom", 60, $unit_of_measurement, "", 1, "Display", $selected, "", 1, "", "", "", "");
								?>
								<input type="hidden" name="txt_old_qnty" id="txt_old_qnty" style="width:90px " value="" class="text_boxes_numeric"/>
								<input type="hidden" name="available_qnty" id="available_qnty" style="width:90px;" value="" class="text_boxes_numeric" readonly/>
								<input type="hidden" name="qnty_breck_down" id="qnty_breck_down" style="width:90px;" />
								<input type="hidden" name="pre_qnty_breck_down" id="pre_qnty_breck_down" style="width:90px;" />
								<input type="hidden" name="update_id" id="update_id" style="width:90px;" class="text_boxes" />
								<input type="hidden" name="txt_booking_qnty" id="txt_booking_qnty" value="" />
								<input type="hidden" name="txt_fab_booking_qnty" id="txt_fab_booking_qnty" value="" />
								<input type="hidden" name="txt_selectted_fabric" id="txt_selectted_fabric" value="" />
								<input type="hidden" name="txt_fabric_po" id="txt_fabric_po" value="" />
								<input type="hidden" name="txt_yarn_count_deterId" id="txt_yarn_count_deterId" value="" />
								<input type="hidden" name="hdn_dyed_type" id="hdn_dyed_type" readonly />
								<input type="hidden" name="hdn_booking_type" id="hdn_booking_type" readonly />
								<input type="hidden" name="hdn_is_short" id="hdn_is_short" readonly />
								<input type="hidden" name="hdn_is_without_order" id="hdn_is_without_order" readonly />
							</td>
						</tr>
						<tr>
							<td width="130">Remarks </td>
							<td colspan="2" >
								<input type="text" name="txt_remarks" id="txt_remarks" style="width:95%" class="text_boxes" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" class="button_container">
								<?
								$date = date('d-m-Y');
								echo load_submit_buttons($permission, "fnc_material_allocation_entry", 0, 0, "reset_form('materialallocation_1','','','cbo_item_category,1*txt_allocation_date," . $date . "','')", 1);
								?>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
			<fieldset style="width:950px; margin-top:10px; float:left">
				<legend>Allocation List</legend>
				<div id="item_list_view"></div>
			</fieldset>
		</div>
		<div style="float:left; margin-left:10px; margin-top:5px">
			<div id="booking_list"></div>
			<br/>
			<div id="fabric_po_list"></div>
			<br/>
			<div id="yarn_list"></div>
            <br/>
            <div id="yarn_po_list"></div>
		</div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
