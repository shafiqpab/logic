<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create 'Item Wise Grey Fabric Stock Report' 
Functionality	:	
JS Functions	:
Created by		:	MD. REAZ UDDIN
Creation date 	: 	17.09.2018
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
echo load_html_head_contents("Item Wise Grey Fabric Stock Report", "../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	//=============================START=======================================================//
	
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var cboYear = $("#cbo_year").val()*1;
		var txtJobNo = $("#txt_job_no").val();
		var txtStyleNo = $("#txt_style_no").val();
		var txtOrderNo = $("#txt_order_no").val();
		var fromDate = $("#txt_date_from").val();
		var toDate = $("#txt_date_to").val();
		if( fromDate != "" && toDate != "" )
		{
			var fromDate1 = fromDate.split('-');
			var toDate1 = toDate.split('-');
			var newfromDate = fromDate1[2] + '-' + fromDate1[1] + '-' + fromDate1[0].slice(-2);
			var newtoDate = toDate1[2] + '-' + toDate1[1] + '-' + toDate1[0].slice(-2);
			if(Date.parse(newfromDate) > Date.parse(newtoDate) )
			{
				alert("Form date must less than to date.");
				return;
			}
			
		}
		
		
		if( txtOrderNo == "")
		{
			$("#hide_order_id").val('');
		}
		
		if( txtJobNo == "" && txtStyleNo == "" && txtOrderNo == "" && fromDate == "" && toDate == "" )
		{
			if(txtJobNo == "" && txtStyleNo == "" && txtOrderNo == ""){
				if( fromDate == "" && toDate == "")
				{
					if (form_validation('txt_order_no','Order No')==false) return;
				}
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_job_id*txt_style_no*txt_order_no*hide_order_id*txt_date_from*txt_date_to*txt_booking_date_from*txt_booking_date_to',"../../")+'&report_title='+report_title;

		freeze_window(operation);
		
		http.open("POST","requires/item_and_order_wise_grey_fabric_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_response;
	}
	
	function fn_report_generated_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 

			show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/item_and_order_wise_grey_fabric_stock_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/item_and_order_wise_grey_fabric_stock_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function openmypage_style_no()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/item_and_order_wise_grey_fabric_stock_report_controller.php?action=style_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Style No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_style_no").value;
			$('#txt_style_no').val(style_no);
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
	
	function openmypage_receive(po_id,prog_no,booking_no,knit_source,action)
	{
		var companyID = $("#cbo_company_name").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_and_order_wise_grey_fabric_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&knit_source='+knit_source+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_transfer_in(po_id,prog_no,booking_no,knit_source,action)
	{  
		var companyID = $("#cbo_company_name").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_and_order_wise_grey_fabric_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&knit_source='+knit_source+'&action='+action, 'Details View', 'width=600px, height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_transfer_out(po_id,prog_no,booking_no,knit_source,action)
	{ 
		var companyID = $("#cbo_company_name").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_and_order_wise_grey_fabric_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&knit_source='+knit_source+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_issue(po_id,prog_no,booking_no,action)
	{
		var companyID = $("#cbo_company_name").val();
		var popup_width='660px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_and_order_wise_grey_fabric_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}
	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var page_link='requires/item_and_order_wise_grey_fabric_stock_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_booking_no').val(booking_no);
			$('#txt_booking_id').val(booking_id);	 
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
	<form id="greyFabricStockReport_1">
		<div style="width:100%;" align="center">    
			<? echo load_freeze_divs ("../../",'');  ?>
			<h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel" >      
				<fieldset style="width:1200px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>
								<th class="must_entry_caption">Company Name</th>
								<th>Buyer Name</th>
								<th>Year</th>
								<th>Job No</th>
								<th>Style No</th>
								<th>Order No</th>
								<th>Production Date</th>
								<th>Booking Delivery Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('greyFabricStockReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
							</tr>
						</thead>
						<tbody>
							<tr align="center">
								<td> 
									<?
									echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/item_and_order_wise_grey_fabric_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
									?>
								</td>
								<td id="buyer_td">
									<? 
									echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --",date('Y'), "", 0, "");
									?>
								</td>
								<td style="background-color:#CCC;">
									<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
									<input type="hidden" id="txt_job_id" name="txt_job_id"/>
								</td>
								<td style="background-color:#CCC;">
									<input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:100px"  onDblClick="openmypage_style_no();" placeholder="Browse/Write" />
								</td>
								<td style="background-color:#CCC;">
									<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px" placeholder="Browse/Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
									<input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" />
									To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"  />
								</td>
								<td align="center">
									<input type="text" name="txt_booking_date_from" id="txt_booking_date_from" class="datepicker" style="width:55px" placeholder="From Date" />
									To
									<input type="text" name="txt_booking_date_to" id="txt_booking_date_to" class="datepicker" style="width:55px" placeholder="To Date"  />
								</td>
								<td>
									<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div id="report_container" align="center"></div>
		<div id="report_container2" align="left"></div>
	</form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
