<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create FSO Wise Finish Fabric Stock Report.
Functionality	:
JS Functions	:
Created by		:
Creation date 	: 	14-10-2018
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
echo load_html_head_contents("FSO Wise Finish Fabric Stock Report", "../../", 1, 1,'',1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';

	var tbl_list_dtls_search =
	{
		col_operation: {
			id: ["kg_total_book_qnty","kg_total_deliv_qnty","kg_total_bal_qnty","yds_total_book_qnty","yds_total_deliv_qnty","yds_total_bal_qnty"],
			col: [11,12,13,14,15,16],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters =
	{
		col_operation: {
			id: ["total_booking_qnty","total_delivery_qnty","total_balance_qnty"],
			col: [2,3,4],
			operation: ["sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_fso()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/delivery_date_wise_fin_fab_status_report_controller.php?action=fso_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='FSO Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_fso_no').val(exdata[1]);
				$('#hidden_fso_id').val(exdata[0]);
			}
		}
	}

	function fn_report_generate(type)
	{
		var txt_fso_no = trim($("#txt_fso_no").val());
		var cbo_buyer_id =  trim($("#cbo_buyer_id").val());
		var validate_id = "";
		var validate_msg = "";
		if(txt_fso_no == "" && cbo_buyer_id =="")
		{
			validate_id += "*txt_date_from*txt_date_to";
			validate_msg += "**Date From*Date To";
		}
		if (form_validation('cbo_company_id'+validate_id,'Comapny Name'+validate_msg)==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_lc_company_id*cbo_buyer_id*cbo_year*txt_fso_no*hidden_fso_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/delivery_date_wise_fin_fab_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1,tableFilters);
			setFilterGrid("tbl_list_dtls_search",-1,tbl_list_dtls_search);
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
			'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	function openmypage_delivery(company_id,fso_id,color_id,uom,date)
	{
		var page_link='requires/delivery_date_wise_fin_fab_status_report_controller.php?action=delivery_popup&companyID='+company_id+'&fso_id='+fso_id+'&color_id='+color_id+'&uom='+uom+'&date='+date;
		var title='Delivery Quantity Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=1,scrolling=0','../../');
	}

	function fnc_bookingpopup(val)
	{
		if(val==1)
		{
			$('#txt_booking_no').attr('placeholder','Browse/Write');
			$('#txt_booking_no').removeAttr("onDblClick").attr("onDblClick","openmypage_booking_no();");
		}
		else
		{
			$('#txt_booking_no').attr('placeholder','Write');
			$('#txt_booking_no').removeAttr('onDblClick','onDblClick');
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<form id="delivery_date_wise_frm">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../",'');  ?>
			<h3 style="width:830px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel" >
				<fieldset style="width:830px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption">Working Company</th>
							<th>LC Company</th>
							<th>Buyer</th>
							<th>Booking Year</th>
							<th>FSO</th>
							<th>Deliverable Date Range</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('delivery_date_wise_frm','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
						</thead>
						<tbody>
							<tr align="center" class="general">
								<td><? echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select-", $selected, "" ); ?>
							</td>
							<td id="pocompany_td"><?
							echo create_drop_down( "cbo_lc_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select-", $selected, "load_drop_down( 'requires/delivery_date_wise_fin_fab_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );?>
						</td>
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" ); ?></td>
						<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-All-", 0, "",0,"" ); ?></td>
						<td>
							<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_fso();" onChange="$('#hidden_fso_id').val('');" readonly >
							<input type="hidden" name="hidden_fso_id" id="hidden_fso_id" readonly>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" placeholder="From" class="datepicker" style="width:55px" readonly/>
							<input type="text" name="txt_date_to" id="txt_date_to" placeholder="To" class="datepicker" style="width:55px" readonly/>
						</td>
						<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generate(1)" /></td>
					</tr>
					<tr>
						<td colspan="16" align="center"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
</div>

<div id="report_container" align="center"></div>
<br>
<div id="report_container2" align="left"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
