<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise production Wip Report for Team
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	24-1-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Code is poetry, I try to do that!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Wise production Wip Repor for Team", "../../", 1, 1,$unicode,1,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["gr_order_qty","gr_cut_qty","gr_cut_bal","gr_input_qty","gr_input_bal","gr_order_input_bal"],
			col: [9,10,11,12,13,14],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function fnc_open_job_no(type_id)
	{	
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company_name=$("#cbo_company_name").val();
		var buyer_name=$("#cbo_buyer_name").val();
		var page_link='requires/order_wise_prod_wip_report_controller.php?action=job_popup&buyer_name='+buyer_name+'&company_name='+company_name+'&type_id='+type_id;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var style=this.contentDoc.getElementById("hide_style_no").value;

			
			if(type_id==2)
			{
				$("#txt_order_no").val(style);
				$("#txt_order_no_id").val(job_id); 
			}
			else
			{
				$("#txt_job_no").val(job_no);
				$("#txt_style_no").val(style);
				$("#hidden_job_id").val(job_id); 	
			}
			 
		}
	}

	function fn_generate_report(type)
	{
		var job_no = document.getElementById('txt_job_no').value;
		var style_no = document.getElementById('txt_style_no').value;
		 
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From* Date To')==false )
			{
				return;
			}
		 
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_style_no*hidden_job_id*txt_date_from*txt_date_to*cbo_shipping_status*txt_order_no*txt_order_no_id',"../../")+'&type='+type+'&report_title='+report_title;
		 
		freeze_window(3);
		http.open("POST","requires/order_wise_prod_wip_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{			 
			 var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
			
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body2",-1);
			setFilterGrid("table_body3",-1);
			setFilterGrid("table_body4",-1);
			setFilterGrid("table_body5",-1);
			setFilterGrid("table_body6",-1);
			setFilterGrid("table_body7",-1);
			setFilterGrid("table_body8",-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none"; 
		document.getElementById('scroll_body4').style.overflow="auto";
		document.getElementById('scroll_body4').style.maxHeight="none";
		document.getElementById('scroll_body5').style.overflow="auto";
		document.getElementById('scroll_body5').style.maxHeight="none";
		document.getElementById('scroll_body6').style.overflow="auto";
		document.getElementById('scroll_body6').style.maxHeight="none";
		document.getElementById('scroll_body7').style.overflow="auto";
		document.getElementById('scroll_body7').style.maxHeight="none";
		document.getElementById('scroll_body8').style.overflow="auto";
		document.getElementById('scroll_body8').style.maxHeight="none";
		 $(".flt").css("display","none");
		// $("#table_body_accss tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		 document.getElementById('scroll_body').style.overflowY="scroll"; 
		 document.getElementById('scroll_body').style.maxHeight="480px";
		 document.getElementById('scroll_body2').style.overflowY="scroll"; 
		 document.getElementById('scroll_body2').style.maxHeight="480px";
		 document.getElementById('scroll_body3').style.overflowY="scroll"; 
		 document.getElementById('scroll_body3').style.maxHeight="480px";
		 document.getElementById('scroll_body4').style.overflowY="scroll"; 
		 document.getElementById('scroll_body4').style.maxHeight="480px";
		 document.getElementById('scroll_body5').style.overflowY="scroll"; 
		 document.getElementById('scroll_body5').style.maxHeight="480px";
		 document.getElementById('scroll_body6').style.overflowY="scroll"; 
		 document.getElementById('scroll_body6').style.maxHeight="480px";
		 document.getElementById('scroll_body7').style.overflowY="scroll"; 
		 document.getElementById('scroll_body7').style.maxHeight="480px";
		 document.getElementById('scroll_body8').style.overflowY="scroll"; 
		 document.getElementById('scroll_body8').style.maxHeight="480px";
		 $(".flt").css("display","block");
	} 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}	 
	 
	function openmypage(po_break_down_id,item_id,action,country_id)
	{
		 
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_prod_wip_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}	
	function openmypage_production_popup(search_string,type) 	 		 	 
	{
		var popup_width = 800;
		var popup_height = 400;
		var action = "production_popup";
		var title = (type==1) ? "Cutting Info" : "Sewing Input Info";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_prod_wip_report_controller.php?search_string='+search_string+'&action='+action+'&type='+type, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}
	function exportToExcel()
	{
		// $(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    var data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		var ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
	    document.getElementById("dlink").click();
		// $(".fltrow").show();
		// alert('ok');
	}
	function openmypage_job_popup(company_name,job_no,po_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_prod_wip_report_controller.php?po_id='+po_id+'&company_name='+company_name+'&job_no='+job_no+'&action='+action, 'Order Quantity', 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	
</script>
<style>
.accordion {
  transition: max-height 1s ease-in;
}

.active, .accordion:hover {
  background-color: #ccc; 
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
</style>
</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1050px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionWipReport_1">    
      <fieldset style="width:1050px;">
            <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                   <tr>
                        <th class="must_entry_caption" width="130" >Company</th>
                        <th width="130">Buyer</th>
                        <th width="100">Job Year</th>
                        <th class="must_entry_caption"  width="100">Job No</th>
                        <th class="must_entry_caption"  width="100">Style</th>
                        <th class="must_entry_caption"  width="100">Order No</th>
                        <th class="must_entry_caption"  width="100">Ship Status</th>
                        <th class="must_entry_caption"  width="200">Trans. Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('dateWiseProductionWipReport_1','report_container','','','')" /></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_prod_wip_report_controller', this.value, 'load_drop_down_buyer', 'td_buyer' );get_php_form_data( this.value, 'eval_multi_select', 'requires/order_wise_prod_wip_report_controller' );" );
                        ?>
                    </td>
                    <td align="center" id="td_buyer"> 
                        <?
                            echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"",1, "-- Select Buyer --", "", "" );
                        ?>
                    </td>
                    <td align="center" id="td_buyer"> 
                        <?
                            echo create_drop_down( "cbo_year", 100, $year,"",1, "-- All Select --", date("Y",time()), "" );
                        ?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="fnc_open_job_no(1)" placeholder="Write/Browse" title="write coma seperate like 10,12,13" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                       <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:100px" class="text_boxes" onDblClick="fnc_open_job_no(1)" placeholder="Write/Browse"/>
                    </td>
                    <td>
                       <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:100px" class="text_boxes" onDblClick="fnc_open_job_no(2)" placeholder="Write/Browse"/>
                       <input type="hidden" id="txt_order_no_id"  name="txt_order_no_id" />
                    </td>
                     <td align="center" id="td_buyer"> 
                        <?
						$shipment_status_fashionArr=array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment",4=>"Full Pending & Partial Shipment");
                           echo create_drop_down( "cbo_shipping_status", 100, $shipment_status_fashionArr,"", 0, "-- All --", 4, "","","1,2,4" );
                        ?>
                    </td>
                        
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date">
                    </td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="WIP" onClick="fn_generate_report(2)" />                                            
                         
                     </td>
                    
                </tr>
                <tr>
                	<td colspan="7">
                        <? echo load_month_buttons(1); ?>
                    </td> 
                </tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
