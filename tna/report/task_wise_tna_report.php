<?
/*-------------------------------------------- Comments

Purpose			: 	This File will create Task Wise TNA Report

Functionality	:	
				
JS Functions	:

Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	29-01-2018
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
echo load_html_head_contents("Task Wise TNA Report","../../", 1, 1, $unicode,1,'');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';



var tableFilters = 
 {
	col_33: "none",
	col_operation: {
	id: ["total_po_qty"],
	col: [4],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
 }

function fn_generate_report( operation,rpt_type)
{
	var task_name_ref=$('#txt_taks_name').val();
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	var style_no=$('#txt_style_ref_no').val();
	
	
	var action_type="";
	if(rpt_type==1)
	{
		if(job_no!="" || order_no!="" || style_no!="")
		{
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{
				return;
			}
		}
		action_type="generate_tna_report";
	} 
	
	var data="action="+action_type+"&"+get_submitted_data_string('cbo_company_name*txt_taks_name*tna_task_id*cbo_buyer_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_order_no*txt_style_ref_no*cbo_shipment_status*cbo_order_status',"../../"); 
	freeze_window(operation);
	http.open("POST","requires/task_wise_tna_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_generate_report_reponse;
}

function fn_generate_report_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('****');
		$("#report_container").html(reponse[0]);  
		document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body",-1,tableFilters);
		release_freezing();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide(); 
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
	
	$('#table_body tr:first').show();
	
}
function update_tna_process(type,id,po_id)
{ 

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/task_wise_tna_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action=edit_update_tna'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			//get_submitted_data_string('',"../../"); 
			release_freezing();
		}
	}
}

function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/task_wise_tna_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			freeze_window(5);
			
			release_freezing();
		}
	}
}


function openmypage_task()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var tna_task = $("#txt_taks_name").val();
	var tna_task_id = $("#tna_task_id").val();
	var tna_task_id_no = $("#tna_task_id_no").val();
	var page_link='requires/task_wise_tna_report_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;  
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		//alert(style_des_no);
		$("#txt_taks_name").val(style_des);
		$("#tna_task_id").val(style_id); 
		$("#tna_task_id_no").val(style_des_no);
		if(style_des!="")
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('TNA From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('TNA To Date');
			
		}
		else
		{
			$('#from_date_html').html('');
			$('#from_date_html').html('Ship From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('Ship To Date');
		}
	}
}



</script>
<body  onLoad="set_hotkey()">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1000px; text-align:left">
    	<table width="1000" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="120" class="must_entry_caption" > Company Name</th>
                    <th width="110">Task</th>
                    <th width="120" class="" id="buyer_caption" >Buyer Name</th>
                    <th width="120">Merchant</th>
                    <th width="80">Search type</th>
                    <th width="80">Order Status</th>
                    <th width="70" id="from_date_html">From Date</th>
                    <th width="70" id="to_date_html">To Date</th>
                    <th width="90" >Job No</th>
                    <th width="90" >Order No</th>
                    <th>Style Ref. No</th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                	<?
					   echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/task_wise_tna_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
						?> 
                </td>
                <td align="center">
                        <input style="width:100px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                        <input type="hidden" name="tna_task_id" id="tna_task_id"/> <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>               
                 </td>
                <td id="buyer_td">
						 <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                         ?>	
                 </td>
                <td id="team_td">
                                    
					<? 
						echo create_drop_down( "cbo_team_member", 120, "select id,team_member_name from  lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
                        //echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                     ?>	
                                    
                </td>
                <td align="center">
					<? 
                    	$shipment_status_tna = array(1=>"ALL (Pending+Partial)",2=>"Full Pending",3=>"Partial Shipment",4=>"Full Shipment/Closed");
                        echo create_drop_down( "cbo_shipment_status", 80, $shipment_status_tna,"", 1, "-- Select --", $selected, "",0,"1,4" );
						
					
					 ?>	
                </td>
                <td align="center">
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 80, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value=""/></td>
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
                <td align="center"><input type="text" name="txt_order_no" id="txt_order_no" autocomplete="off" class="text_boxes" style="width:80px" ></td>
                <td  align="center"><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" autocomplete="off" class="text_boxes" style="width:80px" /></td>
           </tr>
           
           <tr>
            	<td colspan="13" align="center" height="20" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
           <tr>
           		<td colspan="13" height="40" valign="middle" align="center">
                    <input type="button" class="formbutton" style="width:120px;" value="Generate Report" onClick="fn_generate_report(3,1)" id="btn_report_generate"/>
                </td>
           </tr>
           
        </table>
        <div id="print_button"></div>
        <div style="margin-top:5px" id="report_container"></div>
    </fieldset>
	 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
 
 
 

 
 

