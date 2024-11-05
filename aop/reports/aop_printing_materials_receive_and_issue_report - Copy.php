<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create AOP Production Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	01-01-2020
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
echo load_html_head_contents("AOP Printing Materials Receive And Issue Report", "../../", 1, 1,$unicode,1,1);
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	
	var tableFilters = 
	{
		col_35: "none",
		col_operation: {
		id: ["value_tot_total_order_qty","value_tot_total_order_val","value_tot_total_rec_qty","value_tot_total_issue_qty","value_tot_total_material_blce","value_tot_total_prod_qty","value_tot_total_qc_qty","value_total_reject_quantity","value_tot_delv_today_quantity","value_tot_delv_balance"],
		col: [8,9,14,17,19,21,24,25,29,31],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function fn_report_generated(type)
	{
		
		
		
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*date*date')==false)
		{
			return;
		}
		else
		{
			
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var datediff = date_diff( 'd', from_date, to_date )+1;
			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_name*cbo_within_group*txt_buyer_po*txt_buyer_style*txt_job_no*txt_order_no*txt_date_from*txt_date_to*txt_order_id*txt_reference_no*txt_buyer_buyer_no*cbo_report_type',"../../")+'&type='+type+'&datediff='+datediff;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/aop_printing_materials_receive_and_issue_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	function new_window()
	{
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
	}
	
	
	
	function fnc_load_party(type)
	{
		if ( form_validation('cbo_company_id','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_id').val();
		var cbo_within_group = $('#cbo_within_group').val();
		load_drop_down( 'requires/aop_printing_materials_receive_and_issue_report_controller', company+'_'+cbo_within_group, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/aop_printing_materials_receive_and_issue_report_controller', company+'_'+cbo_within_group, 'load_drop_down_buyer_buyer', 'buyer_buye_td' );
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_party_name').value;
		var page_link="requires/aop_printing_materials_receive_and_issue_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}	
	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_party_name').value;
		var job_no=document.getElementById('txt_job_no').value;
		var page_link="requires/aop_printing_materials_receive_and_issue_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&job_no="+job_no;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<form id="aopworkProgressReport_1">
   	 	<div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1250px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1250px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="80">Within Group</th>
                    <th width="125">Party </th>
                    <th width="80">AOP Job No</th>
                    <th width="80">AOP Order No</th> 
                    <th width="80">Aop Reference</th>
                    <th width="80">Buyer Name</th>
                    <th width="80">Buyer PO</th>
                    <th width="80">Buyer Style</th> 
                    <th width="80">Report Type</th>
                    <th width="60" class="must_entry_caption">Date From</th>
                    <th width="60" class="must_entry_caption">Date To</th>
                    <th width="100">
                    	<input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td  align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 135, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1);" );
                            ?>
                        </td>
                        <td >
                            <? 
                                echo create_drop_down( "cbo_within_group",80, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1);" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
							
                                echo create_drop_down( "cbo_party_name", 125, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"" );
								
                            ?>
                        </td>
                        
                        <td>
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" placeholder="Wr/Br Job" onDblClick="openmypage_job();" >
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Wr/Br Order" onDblClick="openmypage_order();" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_reference_no" id="txt_reference_no" class="text_boxes" style="width:75px"  placeholder="Write">
                        </td>
                         <td id="buyer_buye_td">
                         <? 
                          echo create_drop_down( "txt_buyer_buyer_no", 125, $blank_array,"", 1, "-- Select buyer --", $selected, "",1,"" ); ?>
                        </td>
                        <td>
                            <input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:75px" placeholder="Write">
                        </td>
                        <td>
                            <input name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:75px" placeholder="Write">
                        </td>
                        <td>
                       		<? 
							$report_type_array= array(1 => "All", 2 => "Bulk", 3=> "Sample", 4=> "Subcontract");
							echo create_drop_down( "cbo_report_type", 100, $report_type_array,"", 1, "-- Select status --", 0, "",0 ); ?>
                        </td>
                         <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                          
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="16" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 	</form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>