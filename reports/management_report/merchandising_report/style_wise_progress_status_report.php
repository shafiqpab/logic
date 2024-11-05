<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Progress Status Report
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	08-05-2021
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
echo load_html_head_contents("Style Wise Progress Status Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_operation: {
		id: ["td_order_qty","td_ex_factory_qty","td_invoice_qty","td_main_finish_fabric_req_qnty","td_main_grey_fabric_req_qnty","td_short_finish_fabric_req_qnty","td_short_grey_fabric_req_qnty","td_fin_main_sort","td_grey_main_sort","td_total_issued","td_total_issued_value","td_total_grey_roll_recv_in","td_total_grey_roll_recv_out","td_knit_gray_roll_rec_qty","td_total_knitting_bill_qty_in","td_total_knitting_bill_qty_out","td_knitting_bill_qty","td_knitting_bill_value","td_finish_delevToStore_qnty","td_finish_delevToStore_gry_qnty","td_batch_qnty","td_total_dyeing_bill_qty_in","td_total_dyeing_bill_qty_out","td_total_finishing_bill_qty","td_dyeing_bill_qty","td_dyeing_bill_value"],
		col: [7,8,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function fn_report_generated(type)
	{
		var txt_job_no = $("#txt_job_no").val();
		var cbo_ship_status = $("#cbo_ship_status").val();
		var txt_ref_no = $("#txt_ref_no").val();
		var txt_style = $("#txt_style").val();
		var txt_conv_rate = $("#txt_conv_rate").val();

		if(txt_conv_rate=="")
		{
			if(form_validation('txt_conv_rate','Conversion Rate')==false)
			{
				return;
			}
		}
		
		if(txt_job_no!="" || txt_ref_no!="" || txt_style!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{				
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Search Type*Shipment Form Date*Shipment To Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
			var action="action=report_generate";
		}

		var data=action+get_submitted_data_string('cbo_company_name*cbo_buyer_id*txt_conv_rate*txt_job_no*txt_date_from*txt_date_to*cbo_ship_status*txt_ref_no*txt_style',"../../../")+'&report_title='+report_title+'&type='+type;
		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/style_wise_progress_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[3]);
			//console.log(reponse);
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(reponse[2]+'='+reponse[3]);
			setFilterGrid("table_body2",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}	
	
	function new_window(search_type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 

		$('#table_body2 tr:first').show();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function open_po_popup(po_id,title,action,type,conv_rate='')
	{
		var width=950+'px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_progress_status_report_controller.php?action='+action+'&po_id='+po_id+'&type='+type+'&conv_rate='+conv_rate, title, 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function open_bill_popup(po_id,title,action,type,conv_rate='')
	{
		var companyID = $("#cbo_company_name").val();
		var width=580+'px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_progress_status_report_controller.php?action='+action+'&po_id='+po_id+'&type='+type+'&conv_rate='+conv_rate+'&companyID='+companyID, title, 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			 <? echo load_freeze_divs ("../../../",$permission);  ?>
            <h3 align="left" id="accordion_h1" style="width:985px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:985px;">
                <table class="rpt_table" width="985" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="130">Company</th>
                        <th  width="60">Buyer</th>
                        <th  width="60">Internal Ref.</th>
                        <th  width="100">Job No</th>
                        <th  width="100">Style Ref.</th>
                        <th  width="120">Shipping Status</th>
                        <th width="160" class="must_entry_caption">Pub. Shipment Date</th>
                        <th width="100" class="must_entry_caption">Conversion Rate</th>
                        <th width="50"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:55px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_progress_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"> 
							<?
                            echo create_drop_down( "cbo_buyer_id", 110, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:60px"  placeholder="Write"  />                           
                        </td>
                        <td align="center">
                            <input style="width:100px;" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write"/>           
                        </td>
                        <td>
                            <input type="text" id="txt_style" name="txt_style" class="text_boxes" style="width:80px"  placeholder="Write"  />                           
                        </td> 
                        <td align="center">
				            <?
							$ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
							echo create_drop_down( "cbo_ship_status", 100, $ship_status_arr,"", 1,"-All-","", "",0,"" );
				            //echo create_drop_down( "cbo_ship_status", 120, $shipment_status,"",0, "", 0,'',0 );?>
				        </td>

                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>                       
                        <td>
                        	<input type="text" id="txt_conv_rate" name="txt_conv_rate" class="text_boxes" style="width:80px"  placeholder="Write" value="80" />     
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:55px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
