<?
/*-------------------------------------------- Comments
Purpose			: 	General Item Serial No Wise Receive Issue
				
Functionality	:	
JS Functions	:
Created by		:	Nahin
Creation date 	: 	12-13-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../../includes/common.php');

if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
$user_level = $_SESSION['logic_erp']["user_level"];

echo load_html_head_contents("General Item Serial No Wise Receive Issue", "../../../", 1, 1, $unicode, 1, '');

?>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	
	function generate_report(type) {
		var cbo_company_name = $("#cbo_company_name").val();
	    if(form_validation('cbo_company_name', 'Company') == false) 
		{			
			return;
		} 
	
		var cbo_location_id = $("#cbo_location_id").val();
		var cbo_year = $("#cbo_year").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var cbo_item_group_id = $("#txt_item_group_id").val();
		var txt_item_account_id = $("#txt_item_account_id").val();
		var txt_req_no = $("#txt_req_no").val();
		var txt_mrr_number = $("#txt_mrr_number").val();
		var cbo_store_id = $("#cbo_store_id").val();
		var txt_serial_no = $("#txt_serial_no").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var txt_mrr_id=$("#txt_mrr_id").val();

		// alert(cbo_supplier_name);
		if(cbo_company_name!="" && (cbo_location_id==0 && cbo_supplier_name==0 && cbo_item_category_id==0 && cbo_item_group_id==0  && txt_item_account_id==0 && txt_req_no==""  && txt_mrr_number==""  && cbo_store_id==0  && txt_serial_no=='')){
			if (form_validation('txt_date_from*txt_date_to', 'Date From*Date To') == false) 
			{
			return;
			}
		}
		
		var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_location_id=" + cbo_location_id + "&cbo_year=" + cbo_year + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_item_category_id=" + cbo_item_category_id + "&cbo_item_group_id=" + cbo_item_group_id + "&txt_item_account_id=" + txt_item_account_id + "&txt_req_no=" + txt_req_no + "&txt_mrr_number=" + txt_mrr_number + "&cbo_store_id=" + cbo_store_id + "&txt_serial_no=" + txt_serial_no + "&txt_date_from=" + txt_date_from + "&txt_date_to=" + txt_date_to + "&report_type=" + type+ "&txt_mrr_id=" + txt_mrr_id ;

		var data = "action=generate_report" + dataString;
		freeze_window(3);
		http.open("POST", "requires/general_item_serial_no_wise_receive_issue_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;

	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Excel Preview" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
		function fnExportToExcel()
	{
		// $(".fltrow").hide();
		let tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
		let data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}

		let ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}

		let dt = new Date();
		document.getElementById("dlink").href = data_type + base64(format(template, ctx));
		document.getElementById("dlink").traget = "_blank";
		document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
		document.getElementById("dlink").click();
		// $(".fltrow").show();
		// alert('ok');
	}
	
	function new_window()
	{

			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="250px";
	}


	function openmypage_rcv(rcv_id, trans_id) {
		page_link = 'requires/general_item_serial_no_wise_receive_issue_report_controller.php?action=rcv_popup&rcv_id=' + rcv_id + '&trans_id=' + trans_id;

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Receive Qty Popup', 'width=300px, height=350px, center=1, resize=0, scrolling=0', '');
		emailwindow.onclose = function() {}
	}

	function fn_wo_chk(type) {
		if (type == 2) {
			$("#txt_wo_no").attr("disabled", true).val(null);
		} else {
			$("#txt_wo_no").attr("disabled", false)
		}
	}

	// function openmypage_item() {
		
	// 	if (form_validation('cbo_company_name*cbo_item_category_id', 'Company Name*Item Category') == false) {
	// 		return;
	// 	}
	// 	var company = $("#cbo_company_name").val();
	// 	var cbo_item_category = $("#cbo_item_category").val();
	// 	var page_link = 'requires/general_item_serial_no_wise_receive_issue_report_controller.php?action=group_popup&company=' + company + '&cbo_item_category=' + cbo_item_category;
	// 	var title = "Search Group Popup";
	// 	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=365px,height=370px,center=1,resize=0,scrolling=0', '../')
	// 	emailwindow.onclose = function() {
	// 		var theform = this.contentDoc.forms[0];
	// 		var item_id = this.contentDoc.getElementById("txt_selected_id").value; // product ID
	// 		var item_name = this.contentDoc.getElementById("txt_selected").value; // product Description
	// 		//var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
	// 		//alert(style_des_no);
	// 		$("#txt_item_group").val(item_name);
	// 		$("#txt_item_group_id").val(item_id);
	// 		//$("#txt_order_id_no").val(style_des_no);
	// 	}
	// }

	function openmypage_itemgroup()
	{
        //alert("hello kitti");
		if( form_validation('cbo_item_category_id','Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/item_inquiry_report_controller.php?action=item_group_search_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=450px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id);
			$("#txt_item_group_no").val(item_group_no);
		}
	}


	function openmypage_item_account() {
		if (form_validation('cbo_item_category_id', '*Item Category') == false) {
			return;
		}

		var data = document.getElementById('cbo_company_name').value + "_" + document.getElementById('cbo_item_category_id').value + "_" + document.getElementById('txt_item_group_id').value;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/department_wise_issue_report_v2_controller.php?action=item_account_popup&data=' + data, 'Item Account Popup', 'width=800px,height=480px,center=1,resize=0', '../../')

		emailwindow.onclose = function() {
			var theemail = this.contentDoc.getElementById("item_account_id");
			var theemailv = this.contentDoc.getElementById("item_account_val");
			var response = theemail.value.split('_');
			if (theemail.value != "") {
				freeze_window(5);
				document.getElementById("txt_item_account_id").value = response[0];
				document.getElementById("txt_item_acc").value = theemailv.value;
				//reset_form();
				get_php_form_data(response[0], "item_account_dtls_popup", "requires/department_wise_issue_report_v2_controller");
				release_freezing();
			}
		}
	}



	function openmypage_challan() {
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		var mrr_no = $("#txt_mrr_number").val();
		var mrr_id = $("#txt_mrr_id").val();
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/general_item_serial_no_wise_receive_issue_report_controller.php?action=challan_popup&cbo_company_name=' + cbo_company_name + '&selected_mrr=' + mrr_no + '&selected_mrr_id=' + mrr_id+'&cbo_supplier_name='+cbo_supplier_name, 'Challan Popup', 'width=640px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var data = this.contentDoc.getElementById("selected_id").value;
			//alert (data);
			var job = data.split('_');
			$("#txt_mrr_id").val(job[0]);
			$("#txt_mrr_number").val(job[1]);
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
	<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../../", $permission);  ?><br />
			<div style="width:1550px;" align="center">
				<h3 align="center" id="accordion_h1" style="width:1550px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			</div>
			<div style="width:1550px;" align="center" id="content_search_panel">
				<fieldset style="width:1550px;">
					<table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
						<thead>
							<tr>
								<th width="120" class="must_entry_caption">Company</th>
								<th width="130">Location</th>
								<th width="80">Year</th>
								<th width="100"> Supplier</th>
								<th width="130">Item Category</th>
								<th width="120">Item Group</th>
								<th width="120" >Item Account</th>
								<th width="120">Store</th>
								<th width="100" >Req/Wo Number</th>
								<th width="100">MRR Number</th>
								<th width="100" >Serial No</th>
								<th width="170" colspan="4" class="must_entry_caption">Trans. Data Range</th>
								<th><input align="center" type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
								?>
							</td>
							<td id="location_td">
								<? echo create_drop_down("cbo_location_id", 120, $blank_array, "", 1, "-- All  --", 0, "", 0); ?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
								?>
							</td>
							<td align="center" id="supplier_td">
								<?
								echo create_drop_down("cbo_supplier_name", 130, $blank_array, "", 1, "--Select Supplier--", "", "");
								?>
							</td>
							<td>
								<? echo create_drop_down("cbo_item_category_id", 120, $general_item_category, "", 1, "--- Select ---", $selected, "", "", "", 0); ?>
							</td>
							<td align="center">
								<input style="width:110px;" name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse" readonly />
								<input type="hidden" name="txt_item_group_id" id="txt_item_group_id" class="text_boxes"   />
							</td>
							
							<td align="center">
								<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" onDblClick="openmypage_item_account()" class="text_boxes" placeholder="Browse" readonly />
								<input type="hidden" name="txt_item_account_id" id="txt_item_account_id" /> <input type="hidden" name="txt_item_acc_no" id="txt_item_acc_no" />
							</td>
							<td id="store_td">
								<? echo create_drop_down("cbo_store_id", 120, $blank_array, "", 1, "-- All  --", 0, "", 0); ?>
							</td>

							<td>
								<input type="text" name="txt_req_no" id="txt_req_no" style="width:100px " class="text_boxes" placeholder="Write" />
							</td>

							<td align="center">
								<input style="width:120px;" name="txt_mrr_number" id="txt_mrr_number" class="text_boxes" placeholder="Write/Browse"ondblclick="openmypage_challan()" />
								<input type="hidden" name="txt_mrr_id" id="txt_mrr_id" value="">
							</td>
							<td>
								<input type="text" id="txt_serial_no" name="txt_serial_no" class="text_boxes" style="width:70px" placeholder="Write" />
							</td>

							<td colspan="2">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px;" />
							</td>
							<td>To</td>
							<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px;" /></td>
							<td>
								<input type="button" name="search1" id="search1" value="Recieve" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							</td>
							<td>
								<input type="button" name="search2" id="search2" value="Issue" onClick="generate_report(2)" style="width:80px" class="formbutton" />
							</td>

						</tr>
						<tr>
							<td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</div>
	</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<script type="text/javascript">
	set_multiselect('cbo_company_name', '0', '0', '0', '0');
	set_multiselect('cbo_location_id', '0', '0', '0', '0');
	//set_multiselect('cbo_item_cat_id', '0', '0', '0', '0');

	set_multiselect('cbo_store_id', '0', '0', '0', '0');

	$("#multi_select_cbo_company_name a").click(function() {
		getLocationName();
		//alert("ok")
	});

	function getLocationName() {
		var company = $("#cbo_company_name").val();
		load_drop_down('requires/general_item_serial_no_wise_receive_issue_report_controller', company, 'load_drop_down_location', 'location_td');
		load_drop_down('requires/general_item_serial_no_wise_receive_issue_report_controller', company, 'load_drop_down_store', 'store_td');
		load_drop_down('requires/general_item_serial_no_wise_receive_issue_report_controller', company, 'load_drop_down_supplier', 'supplier_td');
		set_multiselect('cbo_location_id', '0', '0', '0', '0');
		set_multiselect('cbo_store_id', '0', '0', '0', '0');
	}
</script>

</html>