<?
/*-------------------------------------------- Comments
Purpose			: 	Yarn Item Allocation Report
Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	09-11-2017
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
	header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id = $_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Lot Wise Yarn Allocation Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function allocation_popup(ref_data)
	{
		var page_link='requires/lot_wise_yarn_allocation_report_controller.php?action=yarn_allocation_popup&ref_data='+ref_data; 
		var title="Yarn Allocation Statement";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function issue_popup(ref_data)
	{
		var page_link='requires/lot_wise_yarn_allocation_report_controller.php?action=yarn_issue_popup&ref_data='+ref_data; ; 
		var title="Issue Details Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function issue_rtn_popup(ref_data)
	{
		var page_link='requires/lot_wise_yarn_allocation_report_controller.php?action=yarn_issue_rtn_popup&ref_data='+ref_data;
		var title="Issue Return Quantity";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var cbo_is_sales = $("#cbo_is_sales").val();
		var txt_internal_ref = $("#txt_internal_ref").val();
		var txt_booking_no = $("#txt_booking_no").val();
		var txt_sales_no = $("#txt_sales_no").val();
		var txt_lot_no = $("#txt_lot_no").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_date_category = $("#cbo_date_category").val();
		var cbo_year_selection = $("#cbo_year_selection").val();

		if (txt_internal_ref=="" && txt_booking_no=="" && txt_sales_no=="" && txt_lot_no=="" && from_date=="")
		{
			if(form_validation('txt_internal_ref*txt_booking_no*txt_lot_no*from_date*to_date','Booking No*Lot No*From Date*To Date')==false )
				return;
		}
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_is_sales="+cbo_is_sales+"&txt_internal_ref="+txt_internal_ref+"&txt_booking_no="+txt_booking_no+"&txt_sales_no="+txt_sales_no+"&from_date="+from_date+"&to_date="+to_date+"&txt_lot_no="+txt_lot_no+"&cbo_date_category="+cbo_date_category+"&cbo_year_selection="+cbo_year_selection
		;
		if(operation==3)
		{
			var data="action=generate_report"+dataString;
		}
		else if(operation==5)
		{
			var data="action=generate_report_2"+dataString;
		}
		else if(operation==6)
		{
			var data="action=generate_report_3"+dataString;
		}
		else if(operation==7)
		{
			var data="action=generate_report_4"+dataString;
		}
		else
		{
			var data="action=generate_report_allocation_date_wise"+dataString;
		}
		freeze_window(operation);
		http.open("POST","requires/lot_wise_yarn_allocation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container").html(reponse[0]);
			document.getElementById('report_container2').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			var report_type = reponse[2];

			if(report_type==1)
			{
				var tableFilters = {
					col_0: "none",
					col_operation: {
						id: ["value_total_allocation_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_balance"],
						col: [12,13,14,15],
						operation: ["sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			else if(report_type==3)
			{
				var tableFilters = {
					col_0: "none",
					col_operation: {
						id: ["value_total_allocation_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_balance"],
						col: [14,15,16,17],
						operation: ["sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}




			show_msg('3');
			release_freezing();
			setFilterGrid("table_body",-1,tableFilters);

		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<h3 style="width:1300px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h"
			onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1300px;">
					<legend>Search Panel</legend>
					<table class="rpt_table" width="1300" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th width="140" class="must_entry_caption">Company</th>
								<th width="130">Buyer</th>
								<th width="100">Is Sales</th>
								<th width="90">Internal Ref</th>
								<th width="110">Booking No</th>
								<th width="110">Sales Order No</th>
								<th width="70">Lot</th>
								<th width="70">Date Category</th>
								<th>Date Range</th>
								<th width="170"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/lot_wise_yarn_allocation_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
							</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "");
								?>
							</td>

							<td id="is_sales_td">
								<?
								echo create_drop_down( "cbo_is_sales", 100, $yes_no,"", 0, "-- Select Is Sales --", 1, "");
								?>
							</td>

							<td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px" placeholder="Internal Ref"></td>
							<td>
								<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:70px" placeholder="Booking No" >
							</td>
							<td>
								<input name="txt_sales_no" id="txt_sales_no" class="text_boxes" style="width:70px" placeholder="Sales Order No" >
							</td>
							<td>
								<input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px" placeholder="Lot No" >
							</td>
							<td>
								<?
								$date_category = array(1=>"Booking Date",2=>"Shipment Date",3=>"Allocation Date");
								echo create_drop_down( "cbo_date_category", 100, $date_category,"", 1, "-- Select --", 0, "");
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px"/>
								To
								<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px"/>
							</td>
							<td>
								<input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
								<input type="button" name="search" id="search" value="Date Wise" onClick="generate_report(4)" style="width:70px" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="9"><? echo load_month_buttons(1);  ?></td>
                            <td>
								<input type="button" name="search_5" id="search_5" value="Show-2" onClick="generate_report(5)" style="width:70px;margin-left: 10px;" class="formbutton" />
								<input type="button" name="search_6" id="search_6" value="Show-3" onClick="generate_report(6)" style="width:70px;margin-left: 10px;" class="formbutton" />
								<input type="button" name="search_7" id="search_7" value="Show-4" onClick="generate_report(7)" style="width:70px;margin-left: 10px;" class="formbutton" />
                            </td>
						</tr>
					</table>
				</fieldset>
			</div>
			<br />
			<div id="report_container2" align="center"></div>
			<div id="report_container" align="center"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
