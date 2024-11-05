<?
/*-------------------------------------------- Comments
Purpose			: 	Job Wise Yarn Allocation Report
Functionality	:
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	08-09-2021
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
echo load_html_head_contents("Job Wise Yarn Allocation","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_name*txt_date_from','Company Name*From Date*To Date')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var txt_booking_no = $("#txt_booking_no").val();
		var txt_lot_no = $("#txt_lot_no").val();
		var from_date = $("#txt_date_from").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var txt_kg = $("#txt_kg").val();
		var cbo_year_selection = $("#cbo_year_selection").val();

		var data = "action=generate_report&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_year="+cbo_year+"&txt_job_no="+txt_job_no+"&txt_booking_no="+txt_booking_no+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&cbo_value_with="+cbo_value_with+"&txt_kg="+txt_kg+"&cbo_year_selection="+cbo_year_selection;

		freeze_window(operation);
		http.open("POST","requires/job_wise_yarn_allocation_controller.php",true);
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

			var tableFilters = {
				col_0: "none",
				col_operation: {
					id: ["value_total_allocation_qty","value_total_requisition_qty","value_total_requisition_balance_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_net_issued_qty","value_total_need_unallocated_qty"],
					col: [13,14,15,16,17,18,19],
					operation: ["sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body", -1, tableFilters);

			show_msg('3');
			release_freezing();
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

	function openmypage(issue_id,prod_id,type,tittle)
	{
		//alert(Issue_id);
		var company_id = $("#cbo_company_name").val();
		var popup_width='';
		if(type=="yarn_issue_return_popup")
		{
			popup_width='380px';
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_yarn_allocation_controller.php?issue_id='+issue_id+'&prod_id='+prod_id+'&action='+type+'&company_id='+company_id, tittle, 'width='+popup_width+', height=320px, center=1, resize=0, scrolling=0', '../../');
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<h3 style="width:1090px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1020px;">
					<legend>Search Panel</legend>
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Job Year</th>
								<th>Job No</th>
								<th>Buyer</th>
								<th>Booking No</th>
								<th>Lot</th>
								<th>Date Range</th>
								<th>Stock Qty</th>
                                <th>Need Unallocation (kg)</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_wise_yarn_allocation_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
							</td>
                            <td><? echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y", time()), "",0,"" ); ?>
                            </td>
                        	<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:110px" /></td>
							<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", 0, ""); ?></td>
							<td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:110px" placeholder="Booking No" ></td>
							<td><input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:110px" placeholder="Lot No" /></td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:70px"/>
								<!--To
								<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px"/>-->
							</td>
                            <td>
                            <?
                            $valueWithArr=array(0=>'With 0',1=>'Without 0');
                            echo create_drop_down( "cbo_value_with", 120, $valueWithArr,"",0,"",1,"","1","");
                            ?>
                            </td>
							<td><input name="txt_kg" id="txt_kg" class="text_boxes" style="width:110px" /></td>
							<td>
								<input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="10"><? echo load_month_buttons(1);  ?></td>
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