<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Linking Receive Report
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	13-02-2021
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
echo load_html_head_contents("Date Wise Linking Receive Report","../../../", 1, 1, $unicode,1); 
?>	 
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
		{
			col_operation: {
				id: ["value_order_qty"],
				col: [6],
				operation: ["sum"], 
				write_method: ["innerHTML"]
			}	
		}
		var tableFilters_1 = 
		{
			col_operation: {
				id: ["value_order_qty_1","value_linking_rec_qty_1"],
				col: [6,8],
				operation: ["sum","sum"], 
				write_method: ["innerHTML","innerHTML"]
			}	
		}

	function generate_report(type)
	{
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();
		
		var qr_no = $("#txt_qr_no").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_company_id = $("#cbo_company_id").val();
		
		if(cbo_company_id =="" && job_no=="" && style_ref_no=="" && qr_no=="")
		{
			if( form_validation('txt_date_from','Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*cbo_pro_process*txt_date_from*txt_date_to','Company,Production, date, date')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
	

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*hiden_order_id*txt_date_from*txt_date_to*cbo_wo_company_name*cbo_ship_status*cbo_pro_process',"../../../")+'&report_title='+report_title+'&type='+type;
		
		
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/date_wise_knitting_report_controller.php",true);
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
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body_1",-1,tableFilters_1);
								
				show_msg('3');
				release_freezing();
			}
		}   
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}
	
	function openmypage_job_no() // For Line number
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var page_link='requires/date_wise_knitting_report_controller.php?action=openJobNoPopup&companyID='+company+'&buyer_name='+buyer;  
		var title="Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			//var po_id=this.contentDoc.getElementById("hide_job_id").value; // product ID
			var job_no=this.contentDoc.getElementById("hide_job_no").value; // product Description
			var style_des_no=this.contentDoc.getElementById("hide_style_no").value; // product Description

			$("#txt_job_no").val(job_no);
			$("#txt_style_ref_no").val(style_des_no);
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
		var job_no = $("#txt_job_no").val();
		var page_link='requires/date_wise_knitting_report_controller.php?action=order_no_popup&lc_company='+lc_company+'&buyer='+buyer+'&job_no='+job_no;  
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
	
	function openmypage_lotratio()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		} 
		var company_id=$("#cbo_company_id").val();
		var page_link='requires/date_wise_knitting_report_controller.php?action=lotratio_popup&company_id='+company_id; 
		var title="Lot Ratio No. Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
			var sysNumber = this.contentDoc.getElementById("hide_cutno").value; 
			//var sysNumber=sysNumber.value.split('_');
			
			$("#txt_lotratio_no").val(sysNumber);
		}
	}
	
	function fnc_bundelDtls(companyid,bundleNo,action)
	{
		var popup_width='1100px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_knitting_report_controller.php?companyid='+companyid+'&bundleNo='+bundleNo+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
function pro_process()
{
	var process = $("#cbo_pro_process").val();	
   // alert(process);
   if(process==1){		
		$("#process_name").text(" All Date ");
	}else if(process==1984){
				$("#process_name").text(" Knit Date ");							
	}else if(process==1986){
					$("#process_name").text("Linking Date ");
	}else if(process==2013){				
					$("#process_name").text("Trimming Date ");
	}else if(process==2018){			
					$("#process_name").text(" Mending Date ");
	}else if(process==1987){				
					$("#process_name").text(" Wash Date ");
	}else if(process==1988){				
					$("#process_name").text(" Attachment Date ");
	}else if(process==1989){				
					$("#process_name").text(" Sewing Date ");
	}else if(process==2020){				
					$("#process_name").text(" PQC Date ");
	}else if(process==1548){
					$("#process_name").text("Iron Date ");
	}else if(process==1550){			
					$("#process_name").text(" Packing/Finishing Date ");
	}
  
	
}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bundleTrackReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1030px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="140" class="must_entry_caption">Company Name</th>
					<th class="">Working Company</th>
                    <th width="120">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref. No.</th>                
					<th width="100">Ship Status</th>
					<th width="110" class="must_entry_caption">Production Process</th>
					<th  width="160" class="must_entry_caption" id="process_name"> Date </th>
                    <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/date_wise_knitting_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td>
                        	<? echo create_drop_down( "cbo_wo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?>
                        </td>

					    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?></td>                       
                        <td>
                             <input type="text" id="txt_job_no" name="txt_job_no" style="width:70px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_job_no();"/>
                            <input type="hidden" name="hiden_order_id" id="hiden_order_id" value="">
                        </td>
                        <td><input type="text" id="txt_style_ref_no" name="txt_style_ref_no" style="width:90px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_job_no();"/></td> 
                        
						<td><? 						
						$shipStatus=array(1 => "Partial or Pending", 2 => "Full Shipped");
						echo create_drop_down( "cbo_ship_status", 100, $shipStatus,"", 1, "--Select--", 1, "",0,"" ); ?></td>
						<td onchange="pro_process();"><? 						
						$productionProcess=array(1 => "All", 1984 => "Knit", 1986 => "Linking", 2013 => "Trimming", 2018 => "Mending", 1987 => "Wash", 1988 => "Attachment", 1989 => "Sewing", 2020 => "PQC", 1548 => "Iron",1550 => "Packing/Finishing");
						echo create_drop_down( "cbo_pro_process", 110, $productionProcess,"", 1, "--Select--", 0, "",0,"" ); ?></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker"   style="width:55px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  placeholder="To Date"  ></td>
                        <td><input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />
						
						</td>
                    </tr>
                </tbody>
            </table>
			<table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
					  
                </tr>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
