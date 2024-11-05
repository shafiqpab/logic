<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Store Wise Receive Issue Summary Report Sales
Functionality	:
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	14/12/2021
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
echo load_html_head_contents("Grey Store Wise Receive Issue Summary Report Sales","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation:
		{
			/*id: ["value_total_receive_qnty","value_total_trans_in_qnty","value_total_roll_weight"],
			col: [30,31,34],
			operation: ["sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML"]*/

			id: ["value_total_receive_qnty","value_total_trans_in_qnty"],
			col: [31,32],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/grey_store_recv_issue_summary_report_sales_controller.php?action=order_no_search_popup&companyID='+company+'&buyer_name='+buyer;
		var title="Search Sales Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_order').val(exdata[1]);
				$('#txt_order_id').val(exdata[0]);
			}
		}
	}

	function reset_field()
	{
		reset_form('item_receive_issue_1','report_container2','','','','');
	}

	function  generate_report(rptType)
	{
		var cbo_company_name    = $("#cbo_company_name").val();
		var cbo_buyer_name      = $("#cbo_buyer_name").val();
		var cbo_cust_buyer_name = $("#cbo_cust_buyer_name").val();
        var txt_booking_no      = $("#txt_booking_no").val();
		var txt_order           = $("#txt_order").val();
		var txt_order_id        = $("#txt_order_id").val();
		var txt_date_from       = $("#txt_date_from").val();
		var txt_date_to         = $("#txt_date_to").val();
		var cbo_based_on        = $("#cbo_based_on").val();
		var cbo_order_type      = $("#cbo_order_type").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var cbo_store_name      = $("#cbo_store_name").val();

		if(txt_order=="" && txt_booking_no=="")
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Form Date*To Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		}

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&cbo_order_type="+cbo_order_type+"&cbo_knitting_source="+cbo_knitting_source+"&cbo_cust_buyer_name="+cbo_cust_buyer_name+"&txt_booking_no="+txt_booking_no+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name;

		if(rptType == 1)
		{
			var data="action=generate_report_receive"+dataString;
		}
		else if (rptType == 2)
		{
			var data="action=generate_report_issue"+dataString;
		}
		else
		{
			var data="action=generate_report_receive_issue_summary"+dataString;
		}

		freeze_window(5);
		http.open("POST","requires/grey_store_recv_issue_summary_report_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			show_msg('3');
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}


	function open_mypage_roll(company_id,store_id,barcode_nos,entry_form)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_store_recv_issue_summary_report_sales_controller.php?barcode_nos='+barcode_nos+"&company_id="+company_id+"&store_id="+store_id+"&entry_form="+entry_form+'&action=barcode_popup', 'Details Veiw', 'width=950, height=350px,center=1,resize=1,scrolling=0','../../');
	}
	function fn_change_base(base)
	{
		if(base == 1){
			$("#date_header").html("<p style='color:blue;'>Transaction Date Range</p");
		}else{
			$("#date_header").html("<p style='color:blue;'>Insert Date Range</p>");
		}
	}

	function report_dtls(fso_no,booking_no,within_group)
	{
		var company_id    = $("#cbo_company_name").val();
		var form_caption ='Fabric Sales Order Entry v2';

		//alert(company_id+'='+fso_no+'='+booking_no+'='+within_group);
		var data = $('#cbo_company_name').val() + '*' + '' + '*' + booking_no + '*' + fso_no + '*' + form_caption;
		if (within_group == 2)
        {
			window.open("../../../production/requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
		}
		else
		{
			alert("This report available for Within Group No");
		}

		return;
	}
	function generate_rcv_report_dtls(type,txt_system_id,update_id,location_name,store_name,entry_form,knitting_location_name,knitting_source)
    {

        var company_id=$('#cbo_company_name').val();
		var report_title='Fabric Roll Receive';

		if(entry_form==58)
		{
			if(type==1)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+location_name+"*"+store_name,'grey_fabric_receive_print');
				return;
			}
			else if(type==2)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+location_name+'*'+'print2'+'*'+$('#txtBasis_1').val(),'grey_fabric_receive_print');
				return;
			}
			else if(type==3)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+'print3'+'*'+$('#txtBasis_').val()+'*'+knitting_location_name+'*'+knitting_source,'grey_fabric_receive_print3');
				return;
			}
			else if(type==4)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id,'receive_challan_print');
				return;
			}
			else if(type==5)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id,'fabric_details_print');
				return;
			}
			else if(type==6)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+'print3'+'*'+$('#txtBasis_').val()+'*'+knitting_location_name+'*'+knitting_source,'grey_fabric_receive_print');
				return;
			}
			else if(type==7)
			{
				generate_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+location_name+"*"+store_name,'grey_fabric_receive_printmg');
				return;
			}
		}
    }

	function generate_report_file(data,action)
    {
        window.open('../../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=' + data+'&action='+action, true );
    }

	function generate_delivery_report_dtls(type,txt_system_id,update_id,knitting_source,delivery_floor_id,entry_form,knit_company,knitting_location_name)
    {

        var company_id=$('#cbo_company_name').val();
		var report_title='Delivery Challan';

		if(entry_form==56)
		{
			if(type==1)
			{
				generate_deliveryreport_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+knitting_source+"*"+delivery_floor_id+"*"+knit_company+"*"+knitting_location_name,'grey_delivery_print_15');
				return;

			}
			else if(type==2)
			{
				generate_deliveryreport_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title+'*'+knitting_source+"*"+delivery_floor_id+"*"+1,'grey_delivery_printmg');
				return;
			}
		}
    }

	function generate_deliveryreport_file(data,action)
    {
        window.open('../../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=' + data+'&action='+action, true );
    }

	function generate_issue_report_dtls(type,txt_system_id,update_id,entry_form)
    {
        var company_id=$('#cbo_company_name').val();
		var report_title='Delivery Challan';

		if(entry_form==61)
		{
			if(type==451)
			{
				generate_grey_issue_report_file( company_id+'*'+txt_system_id+'*'+update_id,'roll_issue_challan_print3');
				return;
			}
			else if(type==860)
			{
				generate_grey_issue_report_file( company_id+'*'+txt_system_id+'*'+update_id+'*'+report_title,'print_mg_two');
				return;
			}
			else if(type==848)
			{

				generate_grey_issue_report_file( company_id+'*'+txt_system_id+'*'+update_id,'sales_roll_issue_challan_print_mg');
				return;
			}
		}
    }

	function generate_grey_issue_report_file(data,action)
    {
        window.open('../../../inventory/grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=' + data+'&action='+action, true );
    }

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?><br />
		<form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
			<h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div style="width:1200px;" align="center" id="content_search_panel">
				<fieldset style="width:1200px;">
					<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr>
								<th width="100" class="must_entry_caption">Company</th>
								<th width="100">Customer Name</th>
								<th width="100">Cust. Buyer </th>
								<th width="100">Store Name</th>
								<th width="80">Order Type</th>
								<th width="80">Grey Source</th>
								<th width="100">FSO No.</th>
								<th width="100"> Sales Job/ Booking No.</th>
								<th width="80">Based On</th>
								<th width="170" class="must_entry_caption" id="date_header">Transaction Date Range</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_store_recv_issue_summary_report_sales_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/grey_store_recv_issue_summary_report_sales_controller',this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );load_drop_down( 'requires/grey_store_recv_issue_summary_report_sales_controller',this.value, 'load_drop_down_store', 'store_td' );" );
								?>
							</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
								?>
							</td>
                            <td id="cust_buyer_td">
								<?
								echo create_drop_down( "cbo_cust_buyer_name", 100, $blank_array,"", 1, "-- All Cust Buyer --", $selected, "",0,"" );
								?>
							</td>
							<td id="store_td"><?
							echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
							?>
						</td>
						<td>
							<?
							$order_type=array(1=>"With Order",2=>"Without Order");
							echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
							?>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_knitting_source", 80, $knitting_source,"", 1, "ALL", 0, "","","1,3" );
							?>
						</td>

						<td align="center">
							<input type="text" style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse"  readonly />
							<input type="hidden" name="txt_order_id" id="txt_order_id"/>
						</td>

                        <td align="center">
							<input type="text" style="width:100px;"  name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Write"/>
						</td>

						<td>
							<?
							$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
							echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
							?>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
						</td>

						<td>
							<input type="button" name="search" id="search" value="Receive" onClick="generate_report(1)" style="width:55px" class="formbutton" />
							<input type="button" name="search" id="search" value="Issue" onClick="generate_report(2)" style="width:55px" class="formbutton" />
							<input type="button" name="search" id="search" value="Summary" onClick="generate_report(3)" style="width:55px" class="formbutton" />
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

	</form>
</div>
</body>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
