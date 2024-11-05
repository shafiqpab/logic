<?
/*-------------------------------------------- Comments
Purpose			: This form will create Material Allocation For Sales
Functionality	:
JS Functions	:
Created by		: Zaman
Creation date 	: 28-04-2021
Updated by 		: MD Didar
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
echo load_html_head_contents("Material Allocation Sales", "../../", 1, 1, $unicode);
?>
<style>
	.hilight { background: #33CC00; }
</style>
<script>
	if ($('#index_page', window.parent.document).val() != 1)
		window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';


	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][475] );
	echo "var field_level_data= ". $data_arr . ";\n";
	?>

	function show_change_pending_fso(){
		if (form_validation('cbo_company_name', 'Company') == false) 
		{
			return;
		}
		else 
		{
			show_list_view($('#cbo_company_name').val(), 'show_change_pending_fso', 'list_change_pending_fso', 'requires/yarn_allocation_sales_controller', 'setFilterGrid(\'tbl_list_search_pending_fso\',-1);');
		}
	 }

	function btn_load_change_fso(){
        // Pending FSO Button
        $("#list_change_pending_fso").html("<span id='btn_span2' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_pending_fso()' type='button' class='formbutton' value='&nbsp;&nbsp;Allocation Pending FSO&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Pending FSO List'></span>");
     	(function blink() {
     		
            $('#btn_span2').fadeOut(900).fadeIn(900, blink);
     	})();
     }

	//func_sales_order_popup
	function func_sales_order_popup()
	{
		var page_link = 'requires/yarn_allocation_sales_controller.php?action=actn_sales_order_popup';
		var title = 'Sales Order Search';
		
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1220px,height=390px,center=1,resize=1,scrolling=0', '../')
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var hdn_data = this.contentDoc.getElementById("hdn_data").value;
			var split_hdn_data = hdn_data.split('**');
			var sales_order_id = split_hdn_data[0];
			var sales_order_no = split_hdn_data[1];
			var company_id = split_hdn_data[2];
			var customer_id = split_hdn_data[3];
			var customer_buyer_id = split_hdn_data[4];
			var within_group = split_hdn_data[5];
			var booking_no = split_hdn_data[6];
			var po_job_no = split_hdn_data[7];
			
			$('#txt_sales_order_no').val(sales_order_no);
			$('#hdn_sales_order_id').val(sales_order_id);
			$('#hdn_booking_no').val(booking_no);
			$('#hdn_po_job_no').val(po_job_no);
			
			$('#cbo_company_name').val(company_id);
			
			load_drop_down('requires/yarn_allocation_sales_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'customer_td');
			load_drop_down('requires/yarn_allocation_sales_controller', within_group + '_' + company_id, 'load_drop_down_cust_buyer', 'customer_buyer_td');
			
			$('#cbo_customer').val(customer_id).attr('disabled','disabled');
			$('#cbo_customer_buyer').val(customer_buyer_id).attr('disabled','disabled');
			
			if (sales_order_no != "")
			{
				freeze_window(5);
				//for fabric description listview
				show_list_view(sales_order_id, 'actn_fabric_description_listview', 'container_fabric_description_listview', 'requires/yarn_allocation_sales_controller', '');
				
				//for yarn description listview
				show_list_view(sales_order_id+'_'+booking_no, 'actn_yarn_description_listview', 'container_yarn_description_listview', 'requires/yarn_allocation_sales_controller', '');
				
				//for as per budget yarn description listview
				//show_list_view(po_job_no, 'actn_asper_budget_yarn_description_listview', 'container_asper_budget_yarn_description_listview', 'requires/yarn_allocation_sales_controller', '');

				//for as per booking  listview
				show_list_view(po_job_no+"**"+booking_no, 'actn_asper_yarn_purchase_requisition_listview', 'container_asper_yarn_purchase_requisition_listview', 'requires/yarn_allocation_sales_controller', '');

				//for allocation listview
				show_list_view(sales_order_no+'_'+company_id+'_'+customer_id+'_'+customer_buyer_id+'_'+booking_no+'_'+sales_order_id, 'actn_allocated_listview', 'container_allocated_listview', 'requires/yarn_allocation_sales_controller', '');
				release_freezing();

			 
				var hidden_date = $("#txt_allocation_date_hidden").val();
				// alert(hidden_date);
				$("#txt_allocation_date").val(hidden_date);

				set_field_level_access(company_id);
			}
		}
	}
	
	//func_item_popup
	function func_item_popup()
	{
		//open_item_popup( 'requires/yarn_allocation_controller.php?action=open_item_popup','Item List' );
		if (form_validation('txt_sales_order_no*cbo_company_name', 'Sales Order No*Company') == false)
		{
			return;
		}
		
		var sales_order_no = $('#txt_sales_order_no').val();
		var company_id = $('#cbo_company_name').val();
		var customer_id = $('#cbo_customer').val();
		var customer_buyer_id = $('#cbo_customer_buyer').val();
		var required_compositions = $('#hdn_required_compositions').val();
		var hdn_booking_no = $('#hdn_booking_no').val();
		var hdn_po_job_no = $('#hdn_po_job_no').val();

		var title = 'Item List';
		var page_link = 'requires/yarn_allocation_sales_controller.php?action=actn_item_popup&company_id=' + company_id + '&sales_order_no=' + sales_order_no + '&customer_id=' + customer_id + '&customer_buyer_id=' + customer_buyer_id+ '&required_compositions=' + required_compositions+ '&hdn_booking_no=' + hdn_booking_no+ '&hdn_po_job_no=' + hdn_po_job_no;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1215px,height=450px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var theemail = this.contentDoc.getElementById("product_id");
			var theemail_number = this.contentDoc.getElementById("product_name");
			var theemail_qnty = this.contentDoc.getElementById("available_qnty");
			//var theemail_uom = this.contentDoc.getElementById("unit_of_measurment");
			var dyed_type = this.contentDoc.getElementById("dyed_type").value*1;

			var is_stopexecution = 0;
			$("#grey_yarn_list tr td,#dyied_yarn_list tr td").find("input").each(function()
			{
				var product_id = this.value;

				if(theemail.value == product_id)
				{
					is_stopexecution = 1;
					alert("This Lot is already allocated in this Sales Order No.");
					return;
				}
			});

			if (theemail.value != "" && (is_stopexecution==0))
			{
				freeze_window(5);
				document.getElementById('txt_item_id').value = theemail.value;
				document.getElementById('txt_item').value = theemail_number.value;
				document.getElementById('available_qnty').value = theemail_qnty.value;
				//document.getElementById('cbo_uom').value = theemail_uom.value;
				release_freezing();
			}
		}
	}
	
	//func_save_update_delete
	function func_save_update_delete(operation)
	{
		var sales_order_no = $('#txt_sales_order_no').val();
		var company_id = $('#cbo_company_name').val();
		var customer_id = $('#cbo_customer').val();
		var customer_buyer_id = $('#cbo_customer_buyer').val();
		var qty = parseFloat($('#txt_qnty').val());

		if (form_validation('txt_sales_order_no*txt_allocation_date*txt_item', 'Sales Order No*Allocation Date*Yarn*Yarn Qty') == false)
		{
			return;
		}
		
		if(operation!=2 && qty <= 0 )
		{
			if (form_validation('txt_qnty', 'Qty') == false)
			{
				return;
			}
		}

		if (confirm("Are You Sure?"))
		{
			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_sales_order_no*cbo_company_name*cbo_customer*cbo_customer_buyer*txt_allocation_date*txt_item_id*txt_item_id_old*txt_qnty*txt_old_qnty*hdn_sales_order_id*hdn_booking_no*hdn_po_job_no*update_id*txt_remarks', "../../");
			freeze_window(operation);
			http.open("POST", "requires/yarn_allocation_sales_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = func_save_update_delete_reponse;
		}
	}
	
	//func_save_update_delete_reponse
	function func_save_update_delete_reponse()
	{
		if (http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse = trim(http.responseText).split('**');
			show_msg(reponse[0]);
			if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)
			{
				var sales_order_no = $('#txt_sales_order_no').val();
				var company_id = $('#cbo_company_name').val();
				var customer_id = $('#cbo_customer').val();
				var customer_buyer_id = $('#cbo_customer_buyer').val();
				var booking_no = $('#hdn_booking_no').val();
				var hdn_sales_order_id = $('#hdn_sales_order_id').val();
				show_list_view(sales_order_no+'_'+company_id+'_'+customer_id+'_'+customer_buyer_id+'_'+booking_no+'_'+hdn_sales_order_id, 'actn_allocated_listview', 'container_allocated_listview', 'requires/yarn_allocation_sales_controller', '');
				
				reset_form('', '', 'txt_item*txt_item_id*txt_item_id_old*txt_qnty*txt_old_qnty*available_qnty*update_id*txt_allocation_date*txt_remarks', '');
				$('#txt_item').removeAttr('disabled');
				set_button_status(0, permission, 'func_save_update_delete', 1);
			}
			else if (reponse[0] == 3 || reponse[0] == 5 || reponse[0] == 17)
			{
				alert(reponse[1]);
			}
			
		}
		var today = new Date();

		date = ('0'+today.getDate()).slice(-2)+'-'+('0'+(today.getMonth()+1)).slice(-2)+'-'+today.getFullYear();

		document.getElementById('txt_allocation_date').value = date;

		release_freezing();
	}
	
	function func_yarn_listview(i,sales_dtls_id)
	{
		var thisSelector = document.getElementById('yrn_list_'+i);
		$("#container_fabric_description_listview table tr").not(thisSelector).removeClass('hilight');
		if($(thisSelector).hasClass("hilight"))
		{
			$(thisSelector).removeClass('hilight');
		}
		else
		{
			$(thisSelector).addClass('hilight');
			//for yarn description listview
			show_list_view(sales_dtls_id, 'actn_yrn_desc_listview', 'container_yrn_desc_listview', 'requires/yarn_allocation_sales_controller', '');
		}
	} 
</script>
</head>
<body onLoad="set_hotkey(); btn_load_change_fso();">
	<? echo load_freeze_divs("../../", $permission); ?>
	<div style="width:200%;" align="left; "  >
		
		<div style="float:left; margin-left:5px;">
			<fieldset style="width:950px;height:auto;">
				<legend>Material Allocation</legend>
				<form style="float:left;" name="materialallocation_1" id="materialallocation_1" autocomplete="off">
					<table cellpadding="0" cellspacing="2" width="100%">
						<tr>
							<td width="130" class="must_entry_caption">Sales Order No</td>
							<td>
								<input class="text_boxes" type="text" style="width:140px"
								onDblClick="func_sales_order_popup('requires/yarn_allocation_sales_controller.php?action=actn_sales_order_popup', 'Sales Order Search')" readonly placeholder="Double Click" name="txt_sales_order_no" id="txt_sales_order_no"/>
								<input type="hidden" name="hdn_sales_order_id" id="hdn_sales_order_id" class="text_boxes"/>
							</td>
							<td width="110">Sales Job/Booking no</td>
							<td>
								<input type="text" name="hdn_booking_no" id="hdn_booking_no" class="text_boxes"  style="width:140px;" readonly disabled ="disabled" />
								<input type="hidden" name="hdn_po_job_no" id="hdn_po_job_no" class="text_boxes"  style="width:140px;" readonly disabled ="disabled" />
                            </td>
							<td width="110">Company</td>
							<td>
								<?php
								echo create_drop_down("cbo_company_name", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "Display", "", "", 1);
								?>
							</td>
						</tr>
						<tr>
							<td width="110">Customer</td>
							<td id="customer_td">
								<?php
								echo create_drop_down("cbo_customer", 150, $blank_array, "", 1, "Display", "", "", 1, "", "", "", "");
								?>
							</td>
                            <td>Cust. Buyer</td>
							<td id="customer_buyer_td">
								<?php
								echo create_drop_down("cbo_customer_buyer", 150, $blank_array, "", 1, "Display", "", "", 1, "", "", "", "");
								?>
							</td>
							<td class="must_entry_caption">Allocation Date</td>
							<td>
								<input type="text" name="txt_allocation_date" id="txt_allocation_date" style="width:140px" value="<? echo date("d-m-Y") ?>" class="datepicker" readonly />
								<input type="hidden" name="txt_allocation_date_hidden" id="txt_allocation_date_hidden" style="width:140px"
								value="<? echo date("d-m-Y") ?>" class="datepicker" readonly />
							</td>
							
						</tr>
						<tr>
							<td>Allocated Yarn</td>
							<td>
								<input type="text" name="txt_item" id="txt_item" style="width:140px;" placeholder="Click to Search" class="text_boxes"
								onClick="func_item_popup()" />
								<input type="hidden" name="txt_item_id" id="txt_item_id" style="width:140px "/>
								<input type="hidden" name="txt_item_id_old" id="txt_item_id_old" style="width:140px "/>
							</td>
                            <td class="must_entry_caption">Qnty</td>
							<td id="section_td">
								<input type="text" name="txt_qnty" id="txt_qnty" style="width:140px;" value="" class="text_boxes_numeric" 
                                placeholder="Write" />
								<input type="hidden" name="txt_old_qnty" id="txt_old_qnty" style="width:90px " value="" class="text_boxes_numeric"/>
								<input type="hidden" name="available_qnty" id="available_qnty" style="width:90px;" value="" class="text_boxes_numeric" readonly/>
								<input type="hidden" name="update_id" id="update_id" style="width:90px;" class="text_boxes" />
							</td>
                            <td>Remarks</td>
                            <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;" value="" />
                            </td>
						
						</tr>
						<tr>
							<td></td> 
							
						</tr>
						<tr>
							<td colspan="6" align="center" class="button_container">
								<?
								$date = date('d-m-Y');
								echo load_submit_buttons($permission, "func_save_update_delete", 0, 0, "reset_form('materialallocation_1','','','txt_allocation_date," . $date . "','')", 1);
								?>
							</td>
							
						</tr>
					</table>
				</form>
				
				
			</fieldset>
			<fieldset style="width:950px; margin-top:10px; float:left">
				<legend>Allocation List</legend>
				<div id="container_allocated_listview"></div>
			</fieldset>
		</div>
		<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		<div  id="list_change_pending_fso" style="max-height:300px; width:700px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		

		<div style="height: 275px;width: 100%;overflow: auto;white-space: nowrap;margin-left:5px;">
			<div id="container_fabric_description_listview" style="display: inline-block;margin-left:5px; margin-top:10px"></div>
			<div id="container_yrn_desc_listview" style="display: inline-block;margin-left:5px; "></div>
			<br/>
			<div id="container_yarn_description_listview" style="display: inline-block;margin-left:5px; "></div>
			<br/>
			<div id="container_asper_budget_yarn_description_listview" style="display: inline-block;margin-left:5px;"></div>
			<br/>
			<div id="container_asper_yarn_purchase_requisition_listview" style="display: inline-block;margin-left:5px;"></div>
		</div>
	
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>