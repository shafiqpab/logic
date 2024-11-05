<?php
/**-------------------------------------------- Comments -----------------------
 * Purpose            :    This Form Will Create Fabric Receive Status Report For Sales Order.
 * Functionality    :
 * JS Functions    :
 * Created by        :    Jahid Hasan
 * Creation date    :    28-12-2016
 * Updated by        :
 * Update date        :
 * QC Performed BY    :
 * QC Date            :
 * Comments        :
 */
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
	require_once('../../includes/common.php');
	$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Fabric Receive Status Report", "../../", 1, 1, '', 1, 1,1);
	?>
	<script>
		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
		var permission = '<?php echo $permission; ?>';

		function fn_report_generated(type) {
			var job_no = $('#txt_job_no').val();
			var po_no = $('#txt_search_string').val();
			var date_from_po = $('#txt_date_from_po').val();

			if (form_validation('cbo_company_name', 'Comapny Name*Date Form*Date To') == false) {
				return;
			}

			if (job_no == "" && po_no == "" && file_no == "" && ref_no == "" && date_from_po == "") {
				if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To') == false) {
					return;
				}
			}
			if(type == 1){
				var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*txt_date_from*txt_date_to*txt_sales_job_no*cbo_year_selection*cbo_within_group*hide_job_id*hide_booking_id', "../../");
			}
			else 
			{
				var data = "action=report_generate_2" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*txt_date_from*txt_date_to*txt_sales_job_no*cbo_year_selection*cbo_within_group*hide_job_id*hide_booking_id', "../../");
			}

			freeze_window(3);
			http.open("POST", "requires/fabric_receive_status_report_sales_order_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}

		function fn_report_generated_reponse() {
			if (http.readyState == 4) {
				var response = trim(http.responseText).split("####");
				$('#report_container2').html(response[0]);
				document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>';
				$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="' + response[2] + '" style="text-decoration:none"><input type="button" value="Convert To Excel Short" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');
				
				show_msg('3');
				release_freezing();
			}
		}

		function openmypage(order_id, type, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type) {
			var popup_width = '';
			if (type == "yarn_issue_not") {
				popup_width = '1000px';
			}
			else {
				popup_width = '990px';
			}
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report_sales_order_controller.php?order_id=' + order_id + '&action=' + type + '&yarn_count=' + yarn_count + '&yarn_comp_type1st=' + yarn_comp_type1st + '&yarn_comp_percent1st=' + yarn_comp_percent1st + '&yarn_type_id=' + yarn_type, 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
		}

		function open_febric_receive_status_order_wise_popup(order_id, type, color, deter_id) 
		{
			var popup_width = '';
			if (type == "fabric_receive" || type == "fabric_purchase" || type == "grey_issue_popup" || type == "dye_qnty" || type == "finish_trans_popup" || type == "finish_trans_in_popup" || type == "finish_trans_out_popup") 
			{
				popup_width = '1090px';
			} 
			else if ( type == "grey_purchase_popup" || type == "grey_receive_by_batch_popup" || type == "batch_popup" || type == "dyeing_popup" || type == "production_qty_popup" || type == "fin_delv_to_store_popup" || type == "fin_rec_by_textile_popup" || type == "deli_to_garments_popup")
			{
				popup_width = '1050px';
			} 
			else if (type == "grey_receive_popup")
			{
				popup_width = '1250px';
			}
			else if (type == "grey_required_popup")
			{
				popup_width = '1320px';
			}
			 	
			else
			{
				popup_width = '1000px';
			}
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report_sales_order_controller.php?order_id=' + order_id + '&action=' + type + '&color=' + color + '&deter_id=' + deter_id, 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
		}

		function openmypage_job() 
		{
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}

			var companyID = $("#cbo_company_name").val();
			var buyerID = $("#cbo_buyer_name").val();
			var within_group = $("#cbo_within_group").val();
			var page_link = 'requires/fabric_receive_status_report_sales_order_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
			;
			var title = 'Style Ref./ Job No. Search';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=400px,center=1,resize=1,scrolling=0', '');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var job_no = this.contentDoc.getElementById("hide_job_no").value;
				var job_id = this.contentDoc.getElementById("hide_job_id").value;

				$('#txt_sales_job_no').val(job_no);
				$('#hide_job_id').val(job_id);
			}
		}

		function openmypage_booking() {
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
			var companyID = $("#cbo_company_name").val();
			var cbo_within_group = $("#cbo_within_group").val();
			var page_link = 'requires/fabric_receive_status_report_sales_order_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
			var title = 'Booking Search';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
				var booking_num = this.contentDoc.getElementById("hidden_booking_num").value;

				$('#txt_booking_no').val(booking_no);
				$('#hide_booking_id').val(booking_num);
			}
		}
		$(document).on("click", ".view_order", function (e) {
			e.preventDefault();
			var job_no = $(this).attr("data-job");
			var company_id = $("#cbo_company_name").val();
			dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report_sales_order_controller.php?company_id=' + company_id + '&job_no=' + job_no + '&action=order_popup', 'Order Popup', 'width=640px,height=350px,center=1,resize=1,scrolling=0', '')
		});
	</script>
	<script type="text/javascript">
		function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, entry_form, is_short, action) {
			var booking_format = trim(booking_no).split('-');

			 
			var booking_format_val=booking_format[1];
			var report_title = "";
			if(entry_form==88 && type==1)
			{
					report_title = "&report_title=Short Fabric Booking";
					var action_method = "action=show_fabric_booking_report_urmi";
					http.open("POST", "../../order/woven_order/requires/short_fabric_booking_controller.php", true);
			}
			else if( (entry_form==118 && type==1) || (entry_form==87 && type==2))
			{

				if(booking_format_val=="SM")
				{
					 report_title = "&report_title=Sample Fabric Booking Urmi";
					var action_method = "action=show_fabric_booking_report";
					http.open("POST", "../../order/woven_order/requires/sample_booking_controller.php", true);
				}
				else if(booking_format_val=="SMN")
				{
					report_title = "&report_title=Sample Fabric Booking Urmi"; 
					var action_method = "action=show_fabric_booking_report7";
					http.open("POST", "../../order/woven_order/requires/sample_booking_non_order_controller.php", true);
					
				}
				else
				{
					var action_method = "action=show_fabric_booking_report_urmi";
					report_title = "&report_title=Main Fabric Booking Urmi";
					http.open("POST", "../../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
				}
					
			}
			else if(entry_form==108)
			{
				var action_method = "action=show_fabric_booking_report_urmi";
				report_title = "&report_title=Partial Fabric Booking";
				http.open("POST", "../../order/woven_order/requires/partial_fabric_booking_controller.php", true);
			}


			var data = action_method + report_title +
			'&txt_booking_no=' + "'" + booking_no + "'" +
			'&cbo_company_name=' + "'" + company_id + "'" +
			'&txt_order_no_id=' + "'" + order_id + "'" +
			'&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
			'&cbo_fabric_source=' + "'" + fabric_source + "'" +
			'&id_approved_id=' + "'" + approved + "'" +
			'&id_approved_id=' + "'" + approved + "'" +
			'&txt_job_no=' + "'" + job_no + "'";
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}

		function generate_fabric_report_reponse() {
			if (http.readyState == 4) {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
			}

		}

		function fnc_fabric_sales_order_print(company_name, booking_id, booking_no, sales_job_no, entry_form, btn,report_print_btn,within_group) {
			var data = company_name + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*Fabric Sales Order Entry';
			if(btn==1) // Show button
			{
				if(entry_form==472)
				{
					window.open("../requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
				}
				if(entry_form==109)
				{
					if(report_print_btn==116) // Print 2
			        {			            
			            window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
			        }
			        else if(report_print_btn==115) // Print
			        {
			            if (within_group == 1)
			            {
			            	window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
			        	}
			        	else 
			        	{
			    			window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
			    		}
			        }
			        else if(report_print_btn==136) // Print 3
			        {
			        	// var data = company_name + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*Fabric Sales Order Entry';
			        	var update_id="0";
			        	var report_title="Fabric Sales Order Entry";
			        	var data="action=fabric_sales_order_print4"+'&companyId='+company_name+'&bookingId='+booking_id+'&bookingNo='+booking_no+'&salesOrderNo='+sales_job_no+'&formCaption='+report_title+'&update_id='+update_id;

			        	window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print4', true);
			        }

					// window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
				}
			}
			if(btn==2) // Report button
			{
				if(entry_form==472)
				{
					window.open("../requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
				}
				if(entry_form==109)
				{
					window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
				}
			}
		}
		function fnc_fabric_sales_order_entry_Reply_info() {
			if (http.readyState == 4) {
                //release_freezing(); alert(http.responseText);return;
                var response = trim(http.responseText).split('**');

                show_msg(response[0]);
                if ((response[0] == 0 || response[0] == 1)) {
                	document.getElementById('update_id').value = response[1];
                	document.getElementById('txt_job_no').value = response[2];

                	var color_from_library = $('#color_from_library').val();
                	var cbo_within_group = $('#cbo_within_group').val();

                	$('#cbo_company_id').attr('disabled', 'disabled');
                	$('#cbo_within_group').attr('disabled', 'disabled');

                	show_list_view(response[1] + "**" + color_from_library + "**" + cbo_within_group, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_controller', '');
                	set_button_status(1, permission, 'fnc_fabric_sales_order_entry', 1);
                	show_fabric_yarn_details(response[1]);
                	show_change_bookings();
                }

            }
        }

        function print_report_button_setting(report_ids) 
	    {	     
	        $('#show_button').hide();
	        $('#show_button2').hide();
	        var report_id=report_ids.split(",");
	        report_id.forEach(function(items){
	            if(items==108){$('#show_button').show();}
	            else if(items==107){$('#show_button2').show();}
	            });
	    }
    </script>
</head>
<body>
	<?php echo load_freeze_divs("../../", ''); ?>
	<form id="fabricReceiveStatusReport_1">
		<div align="center">
			<h3 style="width:960px;" align="left" id="accordion_h1" class="accordion_h"
			onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:970px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
					align="center">
					<thead>
						<th class="must_entry_caption">Company Name</th>
						<th>Buyer Name</th>
						<th>Within Group</th>
						<th>Sales Order No</th>
						<th colspan="2">Delivery Date Range</th>
						<th>Booking No</th>
						<th colspan="2"><input type="reset" name="res" id="res" value="Reset"
							onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')"
							class="formbutton" style="width:70px"/></th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?php
									echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_receive_status_report_sales_order_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/fabric_receive_status_report_sales_order_controller' );");
									?>
								</td>
								<td id="buyer_td">
									<?php
									echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
									?>
								</td>
								<td>
									<?php
									echo create_drop_down("cbo_within_group", 120, array(1 => "Yes", 2 => "No"), "", 1, "-- Select --", 0, "", 0, "");
									?>
								</td>
								<td>
									<input type="text" name="txt_sales_job_no" id="txt_sales_job_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_job();" />
									<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
									placeholder="From Date" readonly>
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
									placeholder="To Date" readonly>
								</td>                        
								<td id="search_by_td">
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"/>
									<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
								</td>
								<td>
									<input type="button" id="show_button" class="formbutton" style="width:70px; display: none;" value="Show"
									onClick="fn_report_generated(1)"/>
									<input type="button" id="show_button2" class="formbutton" style="width:70px; display: none;" value="Report"
									onClick="fn_report_generated(2)"/>
								</td>
							</tr>
						</tbody>
					</table>
					<div><?php echo load_month_buttons(1); ?></div>
				</fieldset>
			</div>
		</div>
		<div style="display:none" id="data_panel"></div>
		<div id="report_container" align="center"></div>
		<div id="report_container2" align="center"></div>
	</form>
</body>
</html>
