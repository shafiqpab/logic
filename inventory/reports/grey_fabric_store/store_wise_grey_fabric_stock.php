<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create File Wise Grey Fabrics Stock Report

Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	11-07-2019
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("File Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	var tableFilters = {

	}
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_order_no*txt_date_from*txt_hide_booking_id*txt_booking_no*cbo_store_name',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		http.open("POST","requires/store_wise_grey_fabric_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
		freeze_window(2);
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1,tableFilters);
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
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();

		var page_link='requires/store_wise_grey_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();

        var page_link='requires/store_wise_grey_fabric_stock_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
        var title='Booking No Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
        	var theform=this.contentDoc.forms[0];
        	var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
        	var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
        	$('#txt_booking_no').val(booking_no);
        	$('#txt_hide_booking_id').val(booking_id);
        }
    }

    function openpage_fabric_booking(action,po_id)
    {
    	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_grey_fabric_stock_controller.php?action='+action+'&po_id='+po_id, 'Booking Details Info', 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../');
    }

    function openpage_stock(action,data)
    {
    	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_grey_fabric_stock_controller.php?action='+action+'&data='+data, 'Details Info', 'width=1200px,height=390px,center=1,resize=0,scrolling=0','../../');
    }
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission); ?>
		<form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
			<h3 style="width:1000px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1000px;">
					<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Buyer</th>
								<th>Year</th>
								<th>Job</th>
								<th>Booking No</th>
								<th>Order No.</th>
								<th>Store</th>
								<th class="must_entry_caption">Transaction Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/store_wise_grey_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/store_wise_grey_fabric_stock_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/store_wise_grey_fabric_stock_controller' );" );
								?>
							</td>
							<td id="buyer_td">
								<? echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 ); ?>
							</td>
							<td>
								<? echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
							</td>
							<td>
								<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_job_id" name="txt_job_id"/>
							</td>
							<td>
								<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" >
								<input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
							</td>
							<td>
								<input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" placeholder="Write"/>
							</td>
							<td id="store_td">
								<? echo create_drop_down( "cbo_store_name", 200, $blank_array,"", 1, "-- All Store --", $storeName, "",0 ); ?>
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:70px;" readonly/>
							</td>
							<td>
								<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_store_name','0','0','0');
</script>
</html>
