<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Tracking Report [Sweater]
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	15-01-2020
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
echo load_html_head_contents("Bundle Tracking Report [Sweater]","../../", 1, 1, $unicode,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
		{
			col_operation: {
				id: ["sizeqty_td"],
				col: [13],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}	
		} 

	var tableFilters1 = 
		{
			col_operation: {
				id: ["sizeqty_td","fstInspIssueQty_td","fstInspIssueQtyTwo_td","fstInspRecQty_td","knitQcQty_td","linkingIssueQty_td","linkingRecQty_td","linkingInQty_td","linkingOutQty_td","bundleIssueToWashQty_td"],
				col: [13,18,19,20,21,22,23,24,25,26],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		} 

	function generate_report(type)
	{
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();
		var order_no = $("#txt_order_no").val();
		var lotratio_no = $("#txt_lotratio_no").val();
		var bundle_no = $("#txt_bundle_no").val();
		var qr_no = $("#txt_qr_no").val();
		
		if(job_no=="" && style_ref_no=="" && order_no=="" && lotratio_no=="" && bundle_no=="" && qr_no=="")
		{
			if( form_validation('cbo_company_id*txt_qr_no','Company*QR Code')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id','Company')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*hiden_order_id*txt_order_no*txt_lotratio_no*txt_bundle_no*txt_qr_no*cbo_job_year',"../../")+'&report_title='+report_title+'&type='+type;
		
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/bundle_track_report_sweater_controller.php",true);
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
								
				show_msg('3');
				release_freezing();
			}
		}   
	}
	function generate_report_production_details(type)
	{
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();
		var order_no = $("#txt_order_no").val();
		var lotratio_no = $("#txt_lotratio_no").val();
		var bundle_no = $("#txt_bundle_no").val();
		var qr_no = $("#txt_qr_no").val();
		
		if(job_no=="" && style_ref_no=="" && order_no=="" && lotratio_no=="" && bundle_no=="" && qr_no=="")
		{
			if( form_validation('cbo_company_id*txt_qr_no','Company*QR Code')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id','Company')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate_production_details"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*hiden_order_id*txt_order_no*txt_lotratio_no*txt_bundle_no*txt_qr_no*cbo_job_year',"../../")+'&report_title='+report_title+'&type='+type;
		
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/bundle_track_report_sweater_controller.php",true);
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
				
				setFilterGrid("table_body_production_details",-1,tableFilters1);
								
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
		var page_link='requires/bundle_track_report_sweater_controller.php?action=openJobNoPopup&companyID='+company+'&buyer_name='+buyer;  
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
		var page_link='requires/bundle_track_report_sweater_controller.php?action=order_no_popup&lc_company='+lc_company+'&buyer='+buyer+'&job_no='+job_no;  
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
		var page_link='requires/bundle_track_report_sweater_controller.php?action=lotratio_popup&company_id='+company_id; 
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/bundle_track_report_sweater_controller.php?companyid='+companyid+'&bundleNo='+bundleNo+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bundleTrackReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1100px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="120">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="80">Job Year</th>
                    <th width="100">Style Ref. No.</th>
                    <th width="100">Order No.</th>
                    <th width="80">Lot Ratio No</th>
                    <th width="100">Bundle No</th>
                    <th width="100">QR Code</th>
                    <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/bundle_track_report_sweater_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?></td>                       
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" style="width:70px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_job_no();"/>
                            <input type="hidden" name="hiden_order_id" id="hiden_order_id" value="">
                        </td>
                        <td>
                        	<? 
							$selected_year=date("Y");                               
							echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "--Select Year--",$selected_year,'',0);
                            ?>                            
                        </td> 
                        <td><input type="text" id="txt_style_ref_no" name="txt_style_ref_no" style="width:90px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_job_no();"/></td> 
                        <td><input type="text" id="txt_order_no" name="txt_order_no" style="width:90px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_order_no();"/></td>
                        <td><input type="text" id="txt_lotratio_no" name="txt_lotratio_no" style="width:70px" class="text_boxes" placeholder="Wr/Br" onDblClick="openmypage_lotratio();"/></td>
                        <td><input type="text" id="txt_bundle_no" name="txt_bundle_no" style="width:90px" class="text_boxes" placeholder="Wr" onDblClick=""/></td>
                        <td><input type="text" id="txt_qr_no" name="txt_qr_no" style="width:90px" class="text_boxes" placeholder="Wr" onDblClick=""/></td>
                        <td><input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" /></td>
                        <td><input type="button" name="search1" id="search1" value="Production Details" onClick="generate_report_production_details(1);" style="width:120px" class="formbutton" /></td>
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
