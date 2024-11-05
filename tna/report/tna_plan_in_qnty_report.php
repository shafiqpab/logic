<?
/*-------------------------------------------- Comments
Purpose			: 	This Report will create TNA Plan In Quantity
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	17-05-2016
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
echo load_html_head_contents("TNA Plan In Quantity","../../", 1, 1, $unicode,'','');
?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';



function fnc_generate_report( operation)
{
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	
	/*if(job_no!="" || order_no!="")
	{
		if ( form_validation('cbo_company_name*cbo_task_name','Company Name*Task Name')==false )
		{
			return;
		}
	}
	else
	{
		if ( form_validation('cbo_company_name*cbo_task_name*txt_date_from*txt_date_to','Company Name*Task Name*Date From*Date To')==false )
		{
			return;
		}
	}*/
	
	if ( form_validation('cbo_company_name*cbo_task_name*txt_date_from*txt_date_to','Company Name*Task Name*Date From*Date To')==false )
	{
		return;
	}
	
	var data="action=generate_report&"+get_submitted_data_string('cbo_company_name*cbo_task_name*cbo_buyer_name*cbo_job_year*txt_job_no*txt_order_no*cbo_shipment_status*cbo_order_status*txt_date_from*txt_date_to',"../../");
	freeze_window(operation);
	http.open("POST","requires/tna_plan_in_qnty_report_controller.php",true);
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
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="360px";
	
}
function update_tna_process(type,id,po_id)
{ 

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_plan_in_qnty_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action=edit_update_tna'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
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
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_plan_in_qnty_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	
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

function openmypage_image(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
	}
}

</script>
<body  onLoad="set_hotkey();">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
	<fieldset style="width:1160px; text-align:left">
    	<table width="1150" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<thead>
            	<tr>
                    <th width="130" class="must_entry_caption"> Company Name</th>
                    <th width="130" class="must_entry_caption">Task</th>
                    <th width="130" class="" id="buyer_caption" >Buyer Name</th>
                    <th width="60" >Job Year</th>
                    <th width="100" >Job No</th>
                    <th width="100" >Order No</th>
                    <th width="110" >shipment Status</th>
                    <th width="110" >Order Status</th>
                    <th class="must_entry_caption" width="70">From Date</th>
                    <th class="must_entry_caption" width="70">To Date</th>
                    <th><input type="reset" id="btn_rest" value="Reset" onClick="reset_form('','report_container*print_button','cbo_company_name*cbo_task_name*cbo_buyer_name*cbo_job_year*txt_job_no*txt_order_no*cbo_shipment_status*cbo_order_status*txt_date_from*txt_date_to','','','');" style="width:100px" class="formbutton" /></th>
               </tr>
            </thead>
           <tr class="general">
                <td align="center">
                <?
                	echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/tna_plan_in_qnty_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); " );
                ?> 
                </td>
                <td align="center">
                <? 
                	echo create_drop_down( "cbo_task_name", 130, "select task_name,task_short_name from lib_tna_task where status_active =1 and is_deleted=0 and row_status=1 and task_type=1 and  task_name in(48,50,60,61,64,73,84,86,87,88,101,110) order by TASK_SEQUENCE_NO","task_name,task_short_name", 1, "-- Select Task --", $selected, "" );
                ?>              
                </td>
                <td id="buyer_td" align="center">
                <? 
                	echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                ?>	
                </td>
                <td align="center" id="agent_td">
				<?
					$year_current=date("Y");
					echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",0);
				?>	
                </td>
                <td align="center"><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                <td  align="center"><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" placeholder="Write" ></td>
                <td align="center">
					<? 
                    	$shipment_status_tna = array(0=>"ALL (Pending+Partial)",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                        echo create_drop_down( "cbo_shipment_status", 110, $shipment_status_tna,"", 1, "-- Select --", $selected, "",0,"0,3" );
					 ?>	
                </td>
                <td align="center">
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 110, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px" readonly placeholder="From Date" /></td>
                <td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px" readonly placeholder="To Date" /></td>
                <td align="center"><input type="button" class="formbutton" style="width:100px" value="Show" onClick="fnc_generate_report(3)" id="btn_report_generate"/></td>
           </tr>
           <tr>
            	<td colspan="11" align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1); ?>
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
 
 
 

 
 

