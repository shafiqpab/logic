<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Country and Color Wise Production Tracking Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	12-04-2022
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
echo load_html_head_contents("Country and color wise production tracking report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();
		var order_no = $("#txt_order_no").val();

		if(job_no !="" || style_ref_no !="" || order_no !="")
		{
			if( form_validation('cbo_company_id','Company')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*Date from*Date To')==false )
			{
				return;
			}
			
		}
			

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*txt_order_no*txt_date_from*txt_date_to*hiden_order_id*cbo_year*cbo_shipment_status*cbo_status*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
		
		// alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/country_and_color_wise_production_tracking_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);  
				
				release_freezing();
				//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
				//document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(type==1)
				{
					setFilterGrid("html_search",-1,tableFilters);
				}
				else
				{
					// setFilterGrid("table_body",-1,tableFilters2);
				}				
				
				show_msg('3');
				release_freezing();
			}
		}   
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$('#html_search tr:first-child').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#html_search tr:first-child').show(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function openProdPopup(param)
	{		
		var page_link='requires/country_and_color_wise_production_tracking_report_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_job_no() // For Line number
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var year = $("#cbo_year").val();
		var page_link='requires/country_and_color_wise_production_tracking_report_controller.php?action=openJobNoPopup&lc_company='+company+'&buyer='+buyer+'&year='+year;  
		var title="Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var job_no=this.contentDoc.getElementById("txt_selected_job").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var job_no_arr = job_no.split(',');
			var unique_job_arr = Array.from(new Set(job_no_arr));
			var jobNo = unique_job_arr.join(',');

			$("#hiden_order_id").val(orderIds); 
			$("#txt_job_no").val(jobNo);
		}
	}

	function openmypage_style_ref() 
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var lc_company = $("#cbo_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var year = $("#cbo_year").val();
		var page_link='requires/country_and_color_wise_production_tracking_report_controller.php?action=style_ref_no_popup&lc_company='+lc_company+'&buyer='+buyer+'&year='+year;  
		var title="Style Ref. Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var style_no=this.contentDoc.getElementById("txt_selected_style").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var style_no_arr = style_no.split(',');
			var unique_style_arr = Array.from(new Set(style_no_arr));
			var styleNo = unique_style_arr.join(',');

			$("#hiden_order_id").val(orderIds); 
			$("#txt_style_ref_no").val(styleNo);
		}
	}

	function openmypage_order_no() 
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var lc_company = $("#cbo_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var year = $("#cbo_year").val();
		var page_link='requires/country_and_color_wise_production_tracking_report_controller.php?action=order_no_popup&lc_company='+lc_company+'&buyer='+buyer+'&year='+year;  
		var title="Order No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var style_no=this.contentDoc.getElementById("txt_selected_style").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var style_no_arr = style_no.split(',');
			var unique_style_arr = Array.from(new Set(style_no_arr));
			var styleNo = unique_style_arr.join(',');

			$("#hiden_order_id").val(orderIds); 
			$("#txt_order_no").val(styleNo);
		}
	}

	function exportToExcel()
	{
		$(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
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
		$(".fltrow").show();
	}
	
</script>
<script src="../../ext_resource/hschart/hschart.js"></script>
</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1320px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1320px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company Name</th>
                    <th width="120" class="">Buyer</th>
                    <th width="60" class="">Job Year</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref. No.</th>
                    <th width="100">Order No.</th>
                    <th width="120" class="">Shiping Status</th>
                    <th width="120" class="">Active Status</th>
                    <th width="120" class="">Order Status</th>
                    <th width=""  id="search_by_th_up" class="must_entry_caption">Country Ship Date</th>
                    <th width="70">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="cbo_lc_company_td">
							<? 
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/country_and_color_wise_production_tracking_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            ?>                            
                        </td>   
                        <td>
							<? 
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "-- All --", date('Y'), "" );
                            ?>                            
                        </td>                    
                        <td>
                         <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" placeholder="Write or Browse" onDblClick="openmypage_job_no()"/>
                         <input type="hidden" name="hiden_order_id" id="hiden_order_id" value="">
                        </td>
                        <td>                          
                         <input type="text" id="txt_style_ref_no"  name="txt_style_ref_no"  style="width:100px" class="text_boxes"  placeholder="Write or Browse" onDblClick="openmypage_style_ref()"/>                       	
                        </td> 
                        <td>                          
                         <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:100px" class="text_boxes"  placeholder="Write or Browse" onDblClick="openmypage_order_no()"/>                       	
                        </td>  
                        <td>
							<? 
								array_push($shipment_status, "Full Pending & Partial Ship");
                                echo create_drop_down( "cbo_shipment_status", 120, $shipment_status,"", 1, "-- All --", 0, "" );
                            ?>                            
                        </td>   
                        <td>
							<? 
                                echo create_drop_down( "cbo_status", 120, $row_status,"", 1, "-- All --", 0, "" );
                            ?>                            
                        </td>   
                        <td>
							<? 
                                echo create_drop_down( "cbo_order_status", 120, $order_status,"", 1, "-- All --", 0, "" );
                            ?>                            
                        </td>                        
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                        </td>                        
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="11" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px 0;"></div>
    
    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
