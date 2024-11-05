<?
/*-------------------------------------------- Comments
Purpose			: 	This Report will create TNA Progress Vs Actual Finish
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	25-04-2016
Updated by 		: 	REZA
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
echo load_html_head_contents("TNA Progress Vs Actual Finish","../../", 1, 1, $unicode,'','');

?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
		
var permission='<? echo $permission; ?>';



var tableFilters = 
 {
	col_operation: {
	id: ["total_po_qty"],
	col: [13],
	operation: ["sum"],
	write_method: ["innerHTML"]
	}
 }

function fnc_generate_report_main( operation,type)
{
	//alert(type);return;
	var task_name_ref=$('#txt_taks_name').val();
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	var style_no=$('#txt_style_ref_no').val();
	
	var action_type="";
	
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
	
	if(type==2){
		action_type="generate_report_tna_hit_rate";
	}
	else{
		action_type="generate_report";
	}
	
	
	
	
	var data="action="+action_type+"&"+get_submitted_data_string('cbo_company_name*txt_taks_name*tna_task_id*cbo_buyer_name*cbo_team_agent*cbo_team_leader*cbo_team_member*txt_job_no*txt_order_no*txt_style_ref_no*cbo_search_type*cbo_shipment_status*cbo_order_status*txt_date_from*txt_date_to*cbo_task_group*cbo_tna_status*cbo_issue_status',"../../"); 
	freeze_window(operation);
	http.open("POST","requires/tna_progress_vs_actual_plan_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_report_main_reponse;
}

function fnc_generate_report_main_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('****');
		//document.getElementById('report_container').innerHTML  = reponse[1];
		
		//alert(reponse[0]);
		//alert(reponse[1]);
		$("#report_container").html(reponse[0]);  
		document.getElementById('print_button').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		setFilterGrid("table_body",-1,tableFilters);//
		
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

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_progress_vs_actual_plan_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action=edit_update_tna'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
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
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_progress_vs_actual_plan_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	
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
	var page_link='requires/tna_progress_vs_actual_plan_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;  
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
			$('#from_date_html').html('Tna From Date');
			$('#to_date_html').html('');
			$('#to_date_html').html('Tna To Date');
			
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

function fn_change_caption(str)
{
	
	
	if(str==1)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('Ship From Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('Ship To Date');
		document.getElementById("cbo_tna_status").disabled = false;
	}
	else if(str==2)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('Cun.Ship From Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('Cun.Ship To Date');
		document.getElementById("cbo_tna_status").disabled = false;
	}
	else if(str==3)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('PO Insert From Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('PO Insert. To Date');
		document.getElementById("cbo_tna_status").disabled = false;
	}
	else if(str==4)
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('Plan Start From Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('Plan Start To Date');
		document.getElementById("cbo_tna_status").disabled = true;
	}
	else
	{
		$('#from_date_html').html('');
		$('#from_date_html').html('Plan Finish From Date');
		$('#to_date_html').html('');
		$('#to_date_html').html('Plan Finish To Date');
		document.getElementById("cbo_tna_status").disabled = true;

	}
	
}

function openmypage_image(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=350px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
	}
}


let set_tna_issue=(issue_status,po_id,job_id)=>{
	var title=(issue_status==1)?"TNA Task Issue Raised":"TNA Task Issue Closed";
	var company = $("#cbo_company_name").val();	
	
	var page_link='requires/tna_progress_vs_actual_plan_controller.php?action=task_issue_raised_close_popup&company_id='+company+'&po_id='+po_id+'&job_id='+job_id+'&issue_status='+issue_status+'&permission='+permission;  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=350px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
}




</script>
<body  onLoad="set_hotkey();">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1640px; text-align:left">
    	<table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="110" class="must_entry_caption" > Company Name</th>
                    <th width="110">Task Group</th>
                    <th width="100" >Task</th>
                    <th width="110" class="" id="buyer_caption" >Buyer Name</th>
                    <th width="110">Agent</th>
                    <th width="110" >Team Leader</th>
                    <th width="110" >Merchant</th>
                    <th width="80" >Job No</th>
                    <th width="80">Style No</th>
                    <th width="80" >Order No</th>
                    <th width="90" >Order Status</th>
                    <th width="90" >shipment Status</th>
					<th width="90">TNA Status</th>
                    <th width="90" >Date Category</th>
                    <th width="70"  id="from_date_html">From Date</th>
                    <th id="to_date_html">To Date</th>
                    <th>Issue Status</th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                <?
                	echo create_drop_down( "cbo_company_name", 110, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/tna_progress_vs_actual_plan_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/tna_progress_vs_actual_plan_controller', this.value, 'load_drop_down_agent', 'agent_td' ); " );
                ?> 
                </td>
                 <td>
                <?
                    if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
    
                   echo create_drop_down( "cbo_task_group", 130, "select task_group from lib_tna_task where is_deleted = 0 and status_active=1 $task_group_con group by task_group order by task_group","task_group,task_group", 1, "-- Select --", $selected, "" );
                    ?> 
                </td>
                <td align="center">
                <input style="width:90px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                <input type="hidden" name="tna_task_id" id="tna_task_id"/> <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>               
                </td>
                <td id="buyer_td" align="center">
                <? 
                	echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                ?>	
                </td>
                <td align="center" id="agent_td">
				<? 
                	echo create_drop_down( "cbo_team_agent", 110, $blank_array,"", 1, "-- Select Agent --", $selected, "" );
                ?>	
                </td>
                <td align="center">
				<? 
                	echo create_drop_down( "cbo_team_leader", 110, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/tna_progress_vs_actual_plan_controller', this.value, 'load_drop_down_marchant', 'marchant_td' );" );
                ?>	
                </td>
                <td id="marchant_td" align="center">
				<? 
                	echo create_drop_down( "cbo_team_member", 110, $blank_array,"", 1, "-- Select Merchant --", $selected, "" );
                ?>	
                </td>
                
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                <td align="center"><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                <td  align="center"><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Write" ></td>
                <td align="center">
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 90, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td align="center">
					<? 
                    	$shipment_status_tna = array(4=>"ALL (Pending+Partial)",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                        //echo create_drop_down( "cbo_shipment_status", 90, $shipment_status_tna,"", 4, "-- Select --", $selected, "",0,"4,3" );
					 	echo create_drop_down( "cbo_shipment_status", 90, $shipment_status_tna,"",1,"--All--",4,"", "","4,3" );
					 ?>	
                </td>
				<td>
					<? 
						$tna_status=array(1=>'On Start Date',2=>'On Finish Date');
						echo create_drop_down( "cbo_tna_status", 90, $tna_status, "",0, "-- Select --", $selected, "" );
					?>
                </td>
                <td align="center">
					<? 
						$search_type=array(1=>'Ship Date',2=>'Country Ship Date',3=>'PO Insert Date',4=>'Plan Start Date',5=>'Plan Finish Date');
						echo create_drop_down( "cbo_search_type", 90, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
                     ?>	
                </td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value="" placeholder="From Date" /></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value="" placeholder="To Date" /></td>

				<td>
					<? 
						$issue_status_arr=array(0=>'All',1=>'Raised',2=>'Closed');
						echo create_drop_down( "cbo_issue_status", 90, $issue_status_arr, "",0, "-- Select --", $selected, "" );
					?>
                </td>

                
                
           </tr>
           
           <tr>
            	<td colspan="17" align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
           <tr>
           		<td colspan="17" height="40" valign="middle" align="center">
                    <input type="button" class="formbutton" style="width:100px" value="Show" onClick="fnc_generate_report_main(3,1)" id="btn_report_generate"/>
                    
                    
                    <input type="button" class="formbutton" style="width:100px" value="TNA Hit Rate" onClick="fnc_generate_report_main(3,2)" id="btn_report_generate"/>
                    
                    
                	<input type="reset" id="btn_rest" value="Reset" onClick="reset_form('','report_container*print_button','','','','');" style="width:100px" class="formbutton" />
                    
                    
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
 
 
 

 
 

