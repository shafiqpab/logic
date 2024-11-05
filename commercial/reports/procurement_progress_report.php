<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Procurement Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	23-12-2013
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
echo load_html_head_contents("Procurement Progress Report", "../../",  1, 1, $unicode,1,'');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	 {
		col_50: "none",
		col_operation: {
		id: ["value_total_grs_value","value_total_discount_value","value_total_bonous_value","value_total_claim_value","value_total_commission_value","value_total_net_invo_value","total_invoice_qty","total_carton_qty"],
	   col: [11,12,13,14,15,16,20,21],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
 
	
    function generate_report()
	{
		if($('#txt_req_no').val()!="" || $('#txt_wo_no').val()!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date*Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_location*cbo_item_category_id*txt_req_no*cbo_job_year*txt_wo_no*cbo_value_with*txt_date_from*txt_date_to*cbo_store*excel_dawnload_id","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/procurement_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[0]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body2",-1);
			setFilterGrid("table_body3",-1);
			
		}
	}
	
	
	function new_window()
	{
		//alert(document.getElementById('report_container2').innerHTML);
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="300px";
		document.getElementById('scroll_body3').style.overflowY="scroll";
		document.getElementById('scroll_body3').style.maxHeight="300px";
		
	}

	function generate_report_excel()
	{
		if($('#txt_req_no').val()!="" || $('#txt_wo_no').val()!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*excel_dawnload_id*txt_date_from*txt_date_to','Company Name*For Excel BTN*Date*Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate2"+get_submitted_data_string("cbo_company_name*cbo_location*cbo_item_category_id*txt_req_no*cbo_job_year*txt_wo_no*cbo_value_with*txt_date_from*txt_date_to*cbo_store*excel_dawnload_id","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/procurement_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_excel;
	}

	function fn_report_generated_reponse_excel()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[0]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_excel()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body2",-1);
			setFilterGrid("table_body3",-1);
			
		}
	}
	
	
	function new_window_excel()
	{
		//alert(document.getElementById('report_container2').innerHTML);
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		// document.getElementById('scroll_body2').style.overflow="auto";
		// document.getElementById('scroll_body2').style.maxHeight="none";
		// document.getElementById('scroll_body3').style.overflow="auto";
		// document.getElementById('scroll_body3').style.maxHeight="none";
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="300px";
		// document.getElementById('scroll_body2').style.overflowY="scroll";
		// document.getElementById('scroll_body2').style.maxHeight="300px";
		// document.getElementById('scroll_body3').style.overflowY="scroll";
		// document.getElementById('scroll_body3').style.maxHeight="300px";
		
	}
	
	function openmypage(po_id,k)
		{
			page_link='requires/procurement_progress_report_controller.php?action=po_id_details'+'&po_id='+po_id+'&k='+k;;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=480px,height=350px,center=1,resize=0,scrolling=0','../');
			emailwindow.onclose=function()
			{
				//alert("Jahid");
			}
		}

	function openmypage_popup(wo_id,prod_id,receive_basis,company,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_report_controller.php?wo_id='+wo_id+'&prod_id='+prod_id+'&receive_basis='+receive_basis+'&company='+company+'&action='+action, page_title, 'width=750px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_popupIndependent(prod_id,booking_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_report_controller.php?prod_id='+prod_id+'&booking_id='+booking_id+'&action='+action, page_title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	
function generate_trim_report(action,report_type,booking_no,company_name,approved_id){
	
	if (booking_no==''){
		return;
	}
	else{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
		if (r==true){
			show_comment="1";
		}
		else{
			show_comment="0";
		}
		report_title=$( "div.form_caption" ).html();
		var data="action="+action+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type+'&txt_booking_no='+booking_no+'&cbo_company_name='+company_name+'&id_approved_id='+approved_id;
		
		alert("Only For Trims Booking Multi Job");
		
		
		
		http.open("POST","../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse(){
	if(http.readyState == 4){
		var file_data=http.responseText.split("****");
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0]);
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}
	
	
  	function openmypage2(reqId,company_id,action)
	{
		var popupWidth = "width=1000px,height=350px,";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_report_controller.php?reqId='+reqId+'&company_id='+company_id+'&action='+action, 'Requisition Details', popupWidth+'center=1,resize=0,scrolling=0','../');
			
	}		

function generate_wo_print_report(report_data, report_action, report_path) 
{
	print_report( report_data, report_action, report_path);
}

function fn_ceckReqWo(str)
{
	
	if (str==1) {
		$('#txt_req_no').attr("disabled",false);
		if ($('#txt_req_no').val()=="") {
			//alert(str);
			$('#txt_wo_no').val("").attr("disabled",false);
		}
		else{
			
			$('#txt_wo_no').val("").attr("disabled",true);
		}
		
	}
	else{

		$('#txt_wo_no').attr("disabled",false);
		if ($('#txt_wo_no').val()=="") {
			//alert(str);
			$('#txt_req_no').val("").attr("disabled",false);
		}
		else{
			
			$('#txt_req_no').val("").attr("disabled",true);
		}
		
	}
}

/*
or
function fn_ceckReqWo(str)
{
	if (str==1) {			
		$("#txt_req_no").removeAttr('disabled');
		$("#txt_wo_no").attr('disabled', 'disabled');
		if ($("#txt_req_no").val()=="") {
			$("#txt_wo_no").removeAttr('disabled', 'disabled');
		}
	}
	else{
		$("#txt_req_no").attr('disabled', 'disabled');
		$("#txt_wo_no").removeAttr('disabled');
		if ($("#txt_wo_no").val()=="") {
			$("#txt_req_no").removeAttr('disabled', 'disabled');
		}
	}
}
*/
	function fnc_print_report(action_type,company_name,id,Purchase_Requisition,is_approved,location_id,remarks)
	{
		var report_title='';
		var approved_id='';
		var action='';
		var template_id='1';
		if(action_type==1 || action_type==2 || action_type==4) 
		{
			action="purchase_requisition_print";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+is_approved+'*'+''+'*'+template_id+'*'+location_id;
		}
		else if(action_type==3)
		{
			action="purchase_requisition_print_2";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==5)
		{
			action="purchase_requisition_print_3";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id+'*'+is_approved;
		}
		else if(action_type==6)
		{
			action="purchase_requisition_print_4";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==7)
		{
			action="purchase_requisition_print_5";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==8)
		{
			action="purchase_requisition_print_8";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==9)
		{
			action="purchase_requisition_print_9";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==10) 
		{
			
			var show_item="";
			var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			action="purchase_requisition_print_10";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;

		}
		else if(action_type==11)
		{
			action="purchase_requisition_print_11";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+''+'*'+template_id+'*'+location_id;
		}
		else if(action_type==12)
		{
			action="purchase_requisition_print_4_akh";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==13)
		{
			action="purchase_requisition_print_13";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==14)
		{
			action="purchase_requisition_print_14";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==15)
		{
			action="purchase_requisition_print_15";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==16)
		{
			action="purchase_requisition_print_16";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==17)
		{
			action="purchase_requisition_category_wise_print";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==18)
		{
			action="purchase_requisition_print_18";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==19)
		{
			action="purchase_requisition_print_19";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==20)
		{
			action="purchase_requisition_print_20";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==21)
		{
			action="purchase_requisition_print_21";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==22)
		{
			action="purchase_requisition_print_22";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==23)
		{
			action="purchase_requisition_print_23";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==24)
		{
			action="purchase_requisition_print_24";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==25)
		{
			action="purchase_requisition_print_25";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==26)
		{
			action="purchase_requisition_print_26";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else if(action_type==27)
		{
			action="purchase_requisition_print_27";
			var show_item="";
			var data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+template_id+'*'+location_id;
		}
		else
		{
			action="purchase_requisition_print";
		}

		freeze_window(5);

		http.open("POST","../../inventory/requires/purchase_requisition_controller.php",true);
			
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{

			if(http.readyState == 4) 
		    {
		    	//alert(action+"**"+action_type);
				window.open("../../inventory/requires/purchase_requisition_controller.php?action="+action+'&data='+data, "_blank");
				release_freezing();
		   }	
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_procureument_rpt" name="frm_procureument_rpt">
    <div style="width:1240px;">
    <h3 align="left" id="accordion_h1" style="width:1240px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:1240px;">
    <table width="1000" cellpadding="0" cellspacing="0" border="0" rules="all">
        <tr>
            <td align="center">
                <table class="rpt_table" cellspacing="0" cellpadding="0" width="1120" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="140">Location</th>
                    <th width="130">Item Category</th>                    
                    <th width="80">Year</th>
                    <th width="100">Reqsn No</th>
                    <th width="100">WO No</th>
                    <th width="110">Value</th>
                    <th width="90" class="must_entry_caption">Date From</th>
                    <th width="90" class="must_entry_caption">Date To</th>
                    <th width="100">Store</th>
                    <th width="100">For Excel BTN</th>
                    <th ><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td align="center">
						<?
                        	echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/procurement_progress_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/procurement_progress_report_controller', this.value+'_'+document.getElementById('cbo_location').value, 'load_drop_down_store', 'store_td' )","" );
                        ?>
                        </td>
                        <td id="location_td" align="center">
						<? 
                        	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location","id,location_name", 1, "-- Select Location --", $selected,"",0,"" );
                        ?>
                        </td>
                        <td  align="center">
						<? 
							$item_category = return_library_array("select category_id, short_name from  lib_item_category_list where category_id not in (5,7,6,23) and status_active=1 and is_deleted=0 order by short_name", "category_id", "short_name");
                        	echo create_drop_down( "cbo_item_category_id", 130,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25");
                        ?>
                 		</td>                        

                        <td align="center">
                        <?
							$year_current=date("Y");
							echo create_drop_down( "cbo_job_year", 70, $year,"", 1, "-Select-",$year_current);
						?>
                        </td>

                        <td align="center">
                        	<input type="text" id="txt_req_no" name="txt_req_no" style="width:90px;" class="text_boxes_numeric" onBlur="fn_ceckReqWo(1);" >
                        </td>
                        <td align="center">
                        	<input type="text" id="txt_wo_no" name="txt_wo_no" style="width:90px;" class="text_boxes" onBlur="fn_ceckReqWo(2);">
                        </td>
                        <td> 
                           <? $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0',2=>'Value Only 0');
                            echo create_drop_down( "cbo_value_with", 115, $valueWithArr, "", 0, "--  --", 0, "", "", ""); ?>
                        </td>
                        <td align="center">
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:80px">
                        </td>
                        <td align="center">
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                        </td>
                        <td id="store_td" align="center">
						<? 
                        	echo create_drop_down( "cbo_store", 140, $blank_array,"", 1, "-- Select Store --", $selected,"",0,"" );
                        ?>
                        </td>
						<td align="center">
						<? $excel_arr=array(1=>"Requisition Details",2=>"Requisition and Purchase Order Details",3=>"Based on Independent");
                        	echo create_drop_down( "excel_dawnload_id", 140, $excel_arr,"", 1, "-- Select Store --", $selected,"",0,"" );
                        ?>
                        </td>
                        <td align="center">
                        	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        	<input type="button" name="search" id="search" value="Show Excel V" onClick="generate_report_excel()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="16" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
        </tr>
    </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
    <div style="display:none" id="data_panel"></div>
    <div style="display: none;">
		<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 );
		?>
	</div>
    </form>
    </div>
</body>
<script>set_multiselect('cbo_item_category_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>