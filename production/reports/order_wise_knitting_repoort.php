﻿<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Order Wise Knitting Report
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar 
Creation date 	: 	11-03-2014
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
echo load_html_head_contents("Order Wise Knitting Report", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_order_no*hide_order_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
		freeze_window(5);
		http.open("POST","requires/order_wise_knitting_repoort_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_knitting_repoort_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/order_wise_knitting_repoort_controller.php?action=order_no_search_popup&companyID='+companyID;
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
	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="orderWiseKnitingReport_1" id="orderWiseKnitingReport_1"> 
         <h3 style="width:850px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:850px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th height="31" class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Order No</th>
                            <th>Shipment Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('orderWiseKnitingReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_knitting_repoort_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:160px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                     <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                                </td>
                                <td align="center">
                                	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>&nbsp; To
                    				<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly >
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="center">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>