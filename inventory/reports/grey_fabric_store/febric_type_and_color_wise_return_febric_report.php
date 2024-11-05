<?
/*-------------------------------------------- Comments
Purpose			: 	Fabric Type and Colorwise Return Fabric Report sales

Functionality	:
JS Functions	:
Created by		:	Mostafizur Rahman
Creation date 	: 	06-nov-2023
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
echo load_html_head_contents("Order Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation:
		{
			id: ["final_booking_qty","final_tot_rcv_qnty","final_issue_rtn_qty","final_trans_in_qty","final_trans_out_qty","final_rcv_total","final_rcv_blnc","final_rcv_issue_qty","final_total_current_issue","final_total_issue_blnc_qty","final_total_stock_Qty"],
			col: [7,8,9,10,11,12,13,14,14,16,17],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	
	function generate_report(rpt_type)
	{
		
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		if(rpt_type==1)
		{
			var report_title=$( "div.form_caption" ).html();
			var action="report_generate3";
			var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_cust_buyer_name*cbo_within_group*txt_fso_no*booking_no*cbo_value*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
           
		
		}
		if(rpt_type==2)
		{
			var report_title=$( "div.form_caption" ).html();
			var action="report_generate2";
			var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_cust_buyer_name*cbo_within_group*txt_fso_no*booking_no*cbo_value*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		
		}
		if(rpt_type==3)
		{
			var report_title=$( "div.form_caption" ).html();
			var action="report_generate";
			var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_cust_buyer_name*cbo_within_group*txt_fso_no*booking_no*cbo_value*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

			
		}
		freeze_window(3);
			http.open("POST","requires/febric_type_and_color_wise_return_febric_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Export to Excel" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body_1",-1,tableFilters);
			show_msg('3');
			release_freezing();
			//alert(reponse[0]);
			
			
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
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	

    function openmypage_fso()
	{
		var cbo_company_name=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/febric_type_and_color_wise_return_febric_report_controller.php?action=order_no_search_popup&companyID='+cbo_company_name,'Fso Popup', 'width=900px,height=370px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_fso_no').val(exdata[1]);
				$('#txt_fso_id').val(exdata[0]);	 
			}
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission); ?>
		<form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
			<h3 style="width:1180px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1180px;">
					<table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Buyer</th>
								<th>Cust Buyer</th>
                                <th>Within Group</th>
                                <th>Fso</th>
								<th>Booking</th>
                                <th>Value</th>
								<th class="must_entry_caption">Date From</th>
								<th class="must_entry_caption">Date To</th>
								<th colspan='2'><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/febric_type_and_color_wise_return_febric_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/febric_type_and_color_wise_return_febric_report_controller', this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );" );
								?>
							</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
								?>
							</td>
                            <td id="cust_buyer_td">
								<? 
								echo create_drop_down( "cbo_cust_buyer_name", 100, $blank_array,"", 1, "-- All Cust Buyer --", $selected, "",0,"" );
								?>
							</td>
                            <td><? $yes_no = array(2=>"yes",1=>"no");
                            echo create_drop_down( "cbo_within_group", 70, $yes_no,"", 1, "--Select--", 0, "fnc_bookingpopup(this.value);" ); ?></td>
                        
                            <td>
                            	<input style="width:140px;" name="txt_fso_no" id="txt_fso_no" class="text_boxes" onDblClick="openmypage_fso()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_fso_id" id="txt_fso_id" style="width:90px;"/>
                            </td>
							
                            <td>
                            	<input style="width:140px;" name="booking_no" id="booking_no" class="text_boxes" placeholder="write"  />
                                
                            </td>
                            <td>
                              <? $value = array(1=>"value with 0",2=>"value without 0");
                              echo create_drop_down( "cbo_value", 70, $value,"", 1, "--Select--", 0, "fnc_bookingpopup(this.value);" ); ?>
                            </td>
							
							
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="hidden" name="show" id="show" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
								<input type="button" name="show" id="show" value="Show" onClick="generate_report(3)" style="width:80px" class="formbutton" />
							</td>
							<td>
								<input type="button" name="show2" id="show2" value="Show2" onClick="generate_report(2)" style="width:80px" class="formbutton" />
							</td>
						</tr>
                        <tr>
                        <td colspan="10" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
	
</script>

</html>
