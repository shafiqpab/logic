<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Wise Grey Stock Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	07-11-2018
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
echo load_html_head_contents("Embellishment Work Progress Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	 var tableFilters = 
	 {
		col_operation: {
		   id: ["gt_order_qty_id","gt_plan_qty_id","gt_rec_prev_qty_id","gt_rec_today_qty_id","gt_rec_total_qty_id","gt_rec_bal_qty_id","gt_issue_prev_qty_id","gt_issue_today_qty_id","gt_issue_total_qty_id","gt_issue_bal_qty_id","gt_print_prev_qty_id","gt_print_today_qty_id","gt_print_total_qty_id","gt_print_bal_qty_id","gt_print_wip_qty_id","gt_qc_prev_qty_id","gt_qc_today_qty_id","gt_qc_total_qty_id","gt_qc_bal_qty_id","gt_delivery_prev_qty_id","gt_delivery_today_qty_id","gt_delivery_total_qty_id","gt_delivery_bal_qty_id","gt_delivery_loq_qty_id","gt_bill_total_qty_id","gt_bill_total_amount_id","gt_bill_bal_qty_id"],
		   col: [8,9,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	

	 }
	
	
	
	function fn_report_generated(type)
	{
		if ((type==1) || (type==2))
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*from Date*to date')==false)
			{
				return;
			}
		}
		if (type==3)
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
			{
				return;
			}
			//alert(txt_date_from);
		}
		if (type==4)
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to*cbo_buyer_id','Comapny Name*From Date*To Date*Buyer')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		if(type==1){
			var data="action=bill_report_statement_bk"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		else if(type==2){
			var data="action=bill_statement_report_generate_buyer"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		else if(type==3){
			var data="action=bill_report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		
		else if(type==4){
			var data="action=bill_report_statement"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		else if(type==5){
			var data="action=bill_report_excess"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
			
		freeze_window(3);
		http.open("POST","requires/embellishment_bill_statement_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fn_report_generated_reponse(){
			
			if(http.readyState == 4) 
			{   
				show_msg('3');
				var reponse=trim(http.responseText).split("**"); 
				$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(type==1){setFilterGrid("table_body",-1,tableFilters);}
				if(type==2){setFilterGrid("table_body",-1);}
				if(type==3){setFilterGrid("table_body",-1);}
				if(type==4){setFilterGrid("table_body",-1);}
				if(type==5){setFilterGrid("table_body",-1);}
				release_freezing();
			}
			
			
		}
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_id*cbo_party_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/embellishment_bill_statement_report_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/emb_order_details_report_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}
	
	function order_search_popup()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('txt_job_no').value+"_"+document.getElementById('cbo_year').value;
		
		var page_link="requires/embellishment_bill_statement_report_controller.php?action=order_no_popup&data="+data;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_buyer_po_id').value=job[0];
			document.getElementById('txt_buyer_po').value=job[1];
			release_freezing();
		}
	}
	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('txt_job_no').value+"_"+document.getElementById('cbo_year').value;
		
		
		
		var page_link="requires/embellishment_bill_statement_report_controller.php?action=style_no_popup&data="+data;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}
	
	
	

</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1390px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1380px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="100" class="must_entry_caption">Company</th>
                    <th width="60">Location</th>
                    <th width="65">Within Group </th>
                    <th width="120">Party </th>
                    <th width="80">Party Location</th>
                    <th width="65">Year</th>                     
                    <th width="70">Job No</th>
                    <th width="70">Work Order</th>
                    <th width="70">Buyer</th>
                    <th width="90">Buyer PO</th>
                    <th width="80">Style Ref.</th>
					<th width="140">Date Range</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
					<th width=""><input type="button" id="Bill_button" class="formbutton" style="width:80px" value="Buyer" onClick="fn_report_generated(2)" /> </th>
                    <th width=""> <input type="button" id="Bill_button" class="formbutton" style="width:80px" value="Excess Bill" onClick="fn_report_generated(5)" /></th>      
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<? echo create_drop_down( "cbo_company_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_bill_statement_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/embellishment_bill_statement_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    	</td>
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location--", $selected, "",1,"" );
                            ?>
                        </td>
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 65, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/embellishment_bill_statement_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party', 'party_td' );$('#cbo_party_location_id').val(0);$('#cbo_party_location_id').prop('disabled','disabled');if(this.value==2){ $('#txt_buyer_po').prop('disabled','disabled');$('#txt_buyer_po').val('');$('#txt_style_ref').prop('disabled','disabled');$('#txt_style_ref').val('');}else{ $('#txt_buyer_po').prop('disabled','');$('#txt_style_ref').prop('disabled','');}" ); ?>
						</td>
                        <td id="party_td">
                        	<? 
                        		echo create_drop_down( "cbo_party_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_bill_statement_report_controller', this.value, 'load_drop_down_party_location', 'party_location_td' );"); 
							?>
                    	</td>
                        <td id="party_location_td">
							<? 
								echo create_drop_down( "cbo_party_location_id", 100, $blank_array,"", 1, "-- Select Location--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--All--","", "",0 );
                            ?>
                        </td>
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:55px;" readonly/>
                    	</td>
                        <td>
                            <input name="txt_wo_order_no" id="txt_wo_order_no" class="text_boxes" style="width:65px"  placeholder="Write">
                        </td>
                        <td id="buyer_td">
                        	<? 
                        		echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                        <td>
                            <input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:65px"  placeholder="Wr/Br Order" onDblClick="order_search_popup();" >
                            <input type="hidden" name="txt_buyer_po_id" id="txt_buyer_po_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Wr/Br Style" onDblClick="openmypage_style();" >
                        </td>               
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:40px" placeholder="From Date" >&nbsp; To 
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:40px"  placeholder="To Date"  >
                        </td>
						<td align="center">
                          <input type="button" id="Bill_button" class="formbutton" style="width:60px" value="Bill" onClick="fn_report_generated(3)" />
                        </td>
						<td align="center">
                          <input type="button" id="Bill_button" class="formbutton" style="width:80px" value="Statement All" onClick="fn_report_generated(1)" />
                        </td>
						<td align="center">
                           <input type="button" id="Bill_button" class="formbutton" style="width:80px" value="Statement" onClick="fn_report_generated(4)" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="14" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
