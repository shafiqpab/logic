<?php
/********************************* Comments *************************
*	Purpose			: 	This Form Will Create Dyeing Bill Report.
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Nuruzzaman 
*	Creation date 	: 	02-09-2015
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
*********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing Bill Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_inter_ref*txt_order_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*txt_exchange_rate',"../../../");
			freeze_window(3);
			http.open("POST","requires/dyeing_bill_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/dyeing_bill_report_controller.php?action=order_no_search_popup&companyID='+companyID;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#div_buyer').hide();
		$('#div_summary').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#div_buyer').show();
		$('#div_summary').show();
	}
	
	function openmypage_bill(po_id,type,tittle)
	{
		//alert("su..re"); return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_bill_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=800px, height=350px, center=1, resize=0, scrolling=0', '../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <form id="cost_breakdown_rpt">
        <div style="width:100%;" align="center">
            <?php echo load_freeze_divs ("../../../"); ?>
             <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div id="content_search_panel"> 
                <fieldset style="width:900px;">
                    <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>                   
                                <th width="150" class="must_entry_caption">Company Name</th>
                                <th width="150">Buyer Name</th>
                                <th width="60">Job Year</th>
                                <th width="70">Job No</th>
                                <th width="70">Internal Ref.</th>
                                <th width="80">Order No</th>
                                <th width="70">Exchange Rate</th>
                                <th width="100">Shipment Status</th>
                                <th width="130" colspan="2">Shipment Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                            </tr>
                         </thead>
                        <tbody>
                            <tr class="general">
                                <td><?php echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dyeing_bill_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                                <td id="buyer_td"><?php echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                                <td><?php echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                                <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                                <td><input type="text" name="txt_inter_ref" id="txt_inter_ref" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                                <td>
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                    <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                                </td>
                                 <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:60px; text-align:right;" value="83" /></td>
                                <td><?php echo create_drop_down( "shipping_status", 100, $shipment_status,"", 1, "-- All --", 0, "",0,'','','','','' ); ?></td>
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated();" /></td>
                            </tr>
                            <tr>
                                <td colspan="11" align="center"><?php echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
     </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>