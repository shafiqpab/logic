<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Gate Pass Report

Functionality	:
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	16/005/2022
Updated by 		: 	Md Jakir Hosen
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
echo load_html_head_contents("Yarn Item Ledger", "../../", 1, 1, $unicode, 1, 1);
?>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";



	function openmypage_chalan() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_name").val();
		var category = $("#cbo_item_cat").val();

		var page_link = 'requires/daily_gate_pass_report_controller.php?action=chalan_surch&company=' + company + '&category=' + category;
		var title = "Search Item Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=340px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var system_id = this.contentDoc.getElementById("hidden_chalan_id").value; // product ID
			var system_no = this.contentDoc.getElementById("hidden_chalan_no").value; // product ID
			var search_by = this.contentDoc.getElementById("hidden_search_number").value; // product Description

			$("#txt_chalan_no").val(system_no);
			$("#txt_chalan_id").val(system_id);
			$("#txt_search_id").val(search_by);
		}
	}

	function generate_report(operation) {
		if (form_validation('txt_date_from*txt_date_to', 'Date From*Date To') == false) {
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_location = $("#cbo_location").val();
		var cbo_party_type = $("#cbo_party_type").val();
		var txt_challan = $("#txt_chalan_no").val();
		var txt_search_item = $("#txt_search_id").val();
		var cbo_search_by = $('#cbo_search_by').val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_gate_type = $("#cbo_gate_type").val();
		var cbo_within_group = $("#cbo_within_group").val();


		var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_location=" + cbo_location + "&cbo_gate_type=" + cbo_gate_type + "&txt_challan=" + txt_challan + "&txt_search_item=" + txt_search_item + "&txt_date_from=" + txt_date_from + "&txt_date_to=" + txt_date_to + "&cbo_search_by=" + cbo_search_by + "&cbo_party_type=" + cbo_party_type+ "&cbo_within_group=" + cbo_within_group;
		//alert(dataString);
		if (operation == 1) var data = "action=generate_report1" + dataString;
		freeze_window(3);
		http.open("POST", "requires/daily_gate_pass_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body", -1);
			setFilterGrid("table_body_1", -1);
			setFilterGrid("table_body_2", -1);
			setFilterGrid("table_body_3", -1);

			show_msg('3');
			release_freezing();

		}
	}


	function new_window() {
		//document.getElementById('scroll_body').style.visibility='visible';
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		$('.fltrow').hide();
		//$('#scroll_body table tbody tr:first').hide();
		//$('#scroll_body').css('overflow','visible');
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');

		//document.getElementById('caption').style.visibility='hidden';
		d.close();
		//$('#scroll_body table tbody tr:first').show();
		$('.fltrow').show();
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "400px";
	}


	function search_populate(str) {
		if (str == 1) {
			document.getElementById('search_by_th_up').innerHTML = "Buyer";
			//$('#search_by_th_up').css('color','blue');
		} else if (str == 2) {
			document.getElementById('search_by_th_up').innerHTML = "Supplier";
			//$('#search_by_th_up').css('color','blue');
		} else if (str == 3) {
			document.getElementById('search_by_th_up').innerHTML = "Other Party";
			//$('#search_by_th_up').css('color','blue');
		}
	}

	function gate_enable_disable(type) {
		var category = $("#cbo_item_cat").val();
		var sample_id = $("#cbo_sample").val();
		if (type == 1) {
			if (category != 0) $("#cbo_sample").attr("disabled", true);
			else $("#cbo_sample").attr("disabled", false);
		} else {
			if (sample_id != 0) $("#cbo_item_cat").attr("disabled", true);
			else $("#cbo_item_cat").attr("disabled", false);
		}
	}

	function generate_trims_print_report(company_id, sys_number, print_btn, location_id, emb_issue_ids, basis, returnable) {

		var report_title = "Gate Pass Entry";
		//   alert(print_btn);

		if (print_btn == 116) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + show_item + '&action=print_to_html_report&template_id=1', true);
		} else if (print_btn == 136) {
			if (basis == 13) {

				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + emb_issue_ids + '&action=get_out_entry_emb_issue_print&template_id=1', true);
			}
		} else if (print_btn == 137) {
			var show_item = 0;
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + show_item + '&action=print_to_html_report5&template_id=1', true);
		} else if (print_btn == 129) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}

			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=get_out_entry_print12&template_id=1', true);

			// return;
		} else if (print_btn == 191) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + show_item + '&action=print_to_html_report_13&template_id=1', true);

		} else if (print_btn == 196) {

			if ($("#cbo_basis").val() != 14) {
				alert('Report Generate only for Challan[Cutting Delivery] Basis');
			} else {
				var show_item = 0;
				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + show_item + '*' + emb_issue_ids + '*' + location_id + '&action=print_to_html_report6&template_id=1', true);

			}
		} else if (print_btn == 199) {


			if (basis != 4 && basis != 3) {
				alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
			} else {
				var show_item = 0;

				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + show_item + '*' + emb_issue_ids + '&action=print_to_html_report7&template_id=1', true);

			}
		} else if (print_btn == 207) {
			if (basis == 12) {
				var show_item = '';
				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=print_to_html_report9&template_id=1', true);
			} else {
				alert("This is for Garments Delivery Basis");
			}
		} else if (print_btn == 208) {

			if (basis == 28) {
				var show_item = '';

				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=print_to_html_report10&template_id=1', true);
			} else {
				alert("This is for Sample Delivery Basis");
			}
		} else if (print_btn == 212) {

			if (basis == 2) {
				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '&action=print_to_html_report11&template_id=1', true);
			} else {
				alert("This is for Yarn Basis Only");
			}
		} else if (print_btn == 271) {
			if (basis == 11) {
				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '&action=print_to_html_report14&template_id=1', true);
			} else {
				alert("This is for Finish Fabric Delivery to Store Basis");
			}
		} else if (print_btn == 707) {

			if (basis != 8) {
				alert("This Button Only For Subcon Knitting Delevery Basis");
				return;
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + basis + '*' + emb_issue_ids + '&action=print_to_html_report17&template_id=1', true);
		} else if (print_btn == 115) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=get_out_entry_print&template_id=1', true);
		} else if (print_btn == 161) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_print6&template_id=1', true);
		} else if (print_btn == 206) {
			var show_item = "0";
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=get_out_entry_print8_fashion&template_id=1', true);
			return;
		} else if (print_btn == 235) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_print9&template_id=1', true);
			return;
		} else if (print_btn == 274) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '*' + 1 + '&action=get_out_entry_print10&template_id=1', true);
		} else if (print_btn == 738) {
			if (basis == 13) {

				window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_printamt&template_id=1', true);
			} else {
				alert("This is for Embellishment Issue Entry");
			}
		} else if (print_btn == 747) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=get_out_entry_print14&template_id=1', true);

		} else if (print_btn == 241) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_pass_entry_print11&template_id=1', true);
			return;
		} else if (print_btn == 427) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_print20&template_id=1', true);
			return;
		} else if (print_btn == 28) {

			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_print21&template_id=1', true);
			return;
		} else if (print_btn == 437) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + location_id + '*' + report_title + '*' + show_item + '*' + basis + '*' + returnable + '&action=get_out_entry_print22&template_id=1', true);
			return;
		}
		else if (print_btn == 304) {
			var show_item = '';
			var r = confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r == true) {
				show_item = "1";
			} else {
				show_item = "0";
			}
			window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id + '*' + sys_number + '*' + report_title + '*' + show_item + '*' + basis + '*' + location_id + '&action=get_out_entry_print28&template_id=1', true);
			return;
		}

	}

	function check_last_update(rowNo) {
		var isChecked = $('#sample').is(":checked");
		//$('#sample').attr('checked',false);

		//$('#sample').val();
		//alert(rowNo);
		if (isChecked == true) {
			$("#cbo_item_cat").attr("disabled", true);
			$("#cbo_sample").attr("disabled", true);
			$('#sample').val(1);
			$('#cbo_sample').val('');
			$('#cbo_item_cat').val('');
		} else {
			$("#cbo_item_cat").attr("disabled", false);
			$("#cbo_sample").attr("disabled", false);
			$('#sample').val(0);
		}
	}

	// function print_report_button_setting(report_ids)
	// {
	//     $('#search').hide();
	//     $('#search2').hide();
	//     var report_id=report_ids.split(",");
	//     report_id.forEach(function(items)
	//     {
	//         if(items==222){$('#search').show();}
	//     });
	// }
</script>
</head>

<body onLoad="set_hotkey()">
	<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", $permission);  ?><br />

			<h3 align="left" id="accordion_h1" style="width:980px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:980px;" align="center" id="content_search_panel">
				<fieldset style="width:980px;">
					<table class="rpt_table" width="980" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th width="130">Company</th>
								<th width="120">Location</th>
								<th width="100">Challan No./ System ID</th>
								<th width="100">Party Type</th>
								<th width="120" id="search_by_th_up"> Buyer</th>
								<th width="120">Within Group</th>
								<th width="80"> Type</th>
								<th width="100" class="must_entry_caption">Date From</th>
								<th width="100" class="must_entry_caption">Date To</th>
								<th width="150"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
							</tr>
						</thead>
						<tr class="general">

							<td>
								<?
								echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'print_button_variable_setting','requires/daily_gate_pass_report_controller' );");
								?>
							</td>
							<td>

								<? echo create_drop_down("cbo_location", 120, "select id, location_name from lib_location where status_active=1 group by id,location_name order by location_name", "id,location_name", 1, "-- Select --", 0, ""); ?>

							</td>

							<td align="center">
								<input style="width:95px;" name="txt_chalan_no" id="txt_chalan_no" ondblclick="openmypage_chalan()" class="text_boxes" placeholder="Write/Browse " />
								<input type="hidden" name="txt_chalan_id" id="txt_chalan_id" />
								<input type="hidden" name="txt_search_id" id="txt_search_id" />
							</td>

							<td width="100" id="">
								<?
								$search_by = array(1 => 'Buyer', 2 => 'Supplier', 3 => 'Other Party');
								$dd = "search_populate(this.value)";
								$party_type_arr = array(1 => "Buyer", 2 => "Supplier", 3 => "Other Party");
								echo create_drop_down("cbo_party_type", 100, $party_type_arr, "", 1, "-- Select Party Type --", $selected, "load_drop_down( 'requires/daily_gate_pass_report_controller', this.value, 'load_drop_down_sent', 'sent_td');search_populate(this.value);", 0);
								?>
							</td>							
							<td id="sent_td">
								<?
								echo create_drop_down("cbo_search_by", 120, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
								?>
							</td>
							<td>
								<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,0, 1, "-- SELECT --", 0, "",'' ); ?>
							</td>
							<td>
								<?
								 $gate_type_arr = array(1 => "Gate In", 2 => "Gate Out", 3 => "Gate Out Pending", 4 => "Return Pending");
								 echo create_drop_down("cbo_gate_type", 80, $gate_type_arr, "", 1, "-Select One-", 0, "", 0);
								?>
							</td> 
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:95px;" readonly />
							</td>
							<td>
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:95px;" readonly />
							</td>
							<td rowspan="1">
								<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px; display: none; margin-top: 2px;" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</table>
				</fieldset>

			</div>
			<!-- Result Contain Start-->

			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>


			<!-- Result Contain END-->
		</div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_location', '0', '0', '', '0');
</script>
<script>
	$("#cbo_sample").val(0);
	gate_enable_disable(2);
</script>

</html>