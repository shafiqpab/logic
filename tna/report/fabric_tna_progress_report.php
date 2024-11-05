<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA Process","../../", 1, 1, $unicode,1,'');

?>
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
		
var permission='<? echo $permission; ?>';



var tableFilters = 
 {
	col_operation: {
		id: ["total_po_qty"],
		col: [4],
		operation: ["sum"],
		write_method: ["innerHTML"]
	}
 }

function fnc_generate_report_main( operation,rpt_type)
{
	var task_name_ref=$('#txt_taks_name').val();
	var job_no=$('#txt_job_no').val();
	var order_no=$('#txt_order_no').val();
	var style_no=$('#txt_style_ref_no').val();
	
	if(task_name_ref!="")
	{
		if(rpt_type==1)
		{
			alert("Please Click On Task Wise Button");
			return;
		}
	}
	
	var action_type="";
	
	if(rpt_type==3)
	{
			if ( form_validation('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to','Company Name*Buyer Name*Date From*Date To')==false )
			{
				return;
			}
			action_type="generate_buyer_task_wise_report";
	}
	else if(rpt_type==1)
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
	else if(rpt_type==2)
	{
		if ( form_validation('cbo_company_name*txt_taks_name','Company Name*Task')==false )
		{
			return;
		}
		action_type="generate_task_wise_report";
	}
	
	else if(rpt_type==4)
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
		action_type="generate_overdew_task_wise_report";
	}
	else
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
		action_type="generate_penalty_report";
	}
	
	var data="action="+action_type+"&"+get_submitted_data_string('cbo_company_name*txt_taks_name*tna_task_id*cbo_buyer_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_order_no*txt_style_ref_no*cbo_search_type*cbo_shipment_status*cbo_order_status*cbo_task_group',"../../"); 
	freeze_window(operation);
	http.open("POST","requires/fabric_tna_progress_report_controller.php",true);
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
		
		//alert(reponse[1]);
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

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_tna_progress_report_controller.php?type='+type+'&mid='+id+'&po_id='+po_id+'&action=edit_update_tna'+'&permission='+permission, "TNA Update", 'width=640px,height=240px,center=1,resize=1,scrolling=0','../')
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
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_tna_progress_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1040px,height=460px,center=1,resize=1,scrolling=0','../')
	
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
	var cbo_task_group = $("#cbo_task_group").val();
	
	
	var page_link='requires/fabric_tna_progress_report_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no+'&cbo_task_group='+cbo_task_group;  
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
	
	var tna_task_id = $("#tna_task_id").val();
	if(tna_task_id){alert('Task ID Selected.');return;}
	
	if(str==1)
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Ship From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Ship To Date');
	}
	else if(str==3)
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Cun.Ship From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Cun.Ship To Date');
	}
	else
	{
	$('#from_date_html').html('');
	$('#from_date_html').html('Recv. From Date');
	$('#to_date_html').html('');
	$('#to_date_html').html('Recv. To Date');
	}
	
}


function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==54)
			{
				$("#btn_buyer_task_report_generate").show();	 
			}
			if(report_id[k]==55)
			{
				$("#btn_task_report_generate").show();	 
			}
			if(report_id[k]==56)
			{
				$("#btn_report_generate").show();	 
			}
			if(report_id[k]==57)
			{
				$("#btn_due_task").show();	 
			}
			if(report_id[k]==58)
			{
				$("#btn_penalty").show();	 
			}
		}
}


</script>
<body  onLoad="set_hotkey()">
<div align="center"> 
    <? echo load_freeze_divs ("../../");  ?>
    <h3 align="left" id="accordion_h1" style="width:1360px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
    <fieldset style="width:1260px; text-align:left">
        <table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <thead>
            <tr>
                <th width="120" class="must_entry_caption" align="center"> Company Name</th>
                <th width="130" align="center">Task Group</th>
                <th width="130" align="center">Task</th>
                <th width="120" class="" id="buyer_caption" align="center">Buyer Name</th>
                 <th width="120" align="center">Merchant</th>
                <th width="100" align="center">Search type</th>
                <th width="100" align="center">shipment Status</th>
                <th width="80" align="center">Order Status</th>
                <th width="70" align="center" id="from_date_html">Ship From Date</th>
                <th width="70" align="center" id="to_date_html">Ship To Date</th>
                <th width="120" align="center">Job No</th>
                <th width="120" align="center">Order No</th>
                <th width="120" align="center">Style Ref. No</th>
                <!--<td><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes" onDblClick="openmypage('Order Info',1); return false"  style="width:100px" readonly value=""/></td>-->
                
                 
           </tr>
           </thead>
           <tr>
                <td>
                	<?
					   echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/fabric_tna_progress_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value, 'set_print_button', 'requires/fabric_tna_progress_report_controller' ); " );
						?> 
                </td>
                
                <td>
                	<?
					   	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}

					   echo create_drop_down( "cbo_task_group", 130, "select task_group from lib_tna_task where is_deleted = 0 and status_active=1 $task_group_con group by task_group order by task_group","task_group,task_group", 1, "-- Select --", $selected, "" );
						?> 
                </td>
                
                <td align="center">
                        <input style="width:120px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>   
                        <input type="hidden" name="tna_task_id" id="tna_task_id"/> <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>               
                 </td>
                <td id="buyer_td">
						 <? 
                            echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                         ?>	
                 </td>
                <td id="team_td">
                                    
					<? 
						echo create_drop_down( "cbo_team_member", 130, "select id,team_member_name from  lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
                        //echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                     ?>	
                                    
                </td>
                <td>
					<? 
						$search_type=array(1=>'Ship Date',2=>'PO Recv. Date',3=>'Country Ship Date',4=>'PO Insert Date');
						echo create_drop_down( "cbo_search_type", 80, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
                     ?>	
                </td>
                <td>
					<? 
                    	$shipment_status_tna = array(0=>"ALL (Pending+Partial)",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                        echo create_drop_down( "cbo_shipment_status", 80, $shipment_status_tna,"", 1, "-- Select --", $selected, "",0,"0,3" );
						
					
					 ?>	
                </td>
                <td>
					<?
					$order_status=array(0=>"ALL",1=>"Confirmed",2=>"Projected"); 
                    echo create_drop_down( "cbo_order_status", 80, $order_status,"",0,"",1,"", "" ); 
                    ?>
                </td>
                <td><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:60px"  value=""/></td>
                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:60px"  value=""/></td>
                <td><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes" style="width:90px" /></td>
                <td><input type="text" name="txt_order_no" id="txt_order_no" autocomplete="off" class="text_boxes" style="width:90px" ></td>
                <td><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" autocomplete="off" class="text_boxes" style="width:90px" /></td>
                <!--<td><input type="text" name="txt_job_no" id="txt_job_no" autocomplete="off" class="text_boxes" onDblClick="openmypage('Order Info',1); return false"  style="width:100px" readonly value=""/></td>-->
                
                 
           </tr>
           
           <tr>
            	<td colspan="13" align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
           <tr>
           		<td colspan="13" height="40" valign="middle" align="center">
                	<input type="button" class="formbutton" style="width:150px;display:none;" value="Generate Buyer Wise" onClick="fnc_generate_report_main(3,3)" id="btn_buyer_task_report_generate" />
                    <input type="button" class="formbutton" style="width:150px;display:none;" value="Generate Task Wise" onClick="fnc_generate_report_main(3,2)" id="btn_task_report_generate" />
                    <input type="button" class="formbutton" style="width:120px;display:none;" value="Generate Report" onClick="fnc_generate_report_main(3,1)" id="btn_report_generate"/>
                    <input type="button" class="formbutton" style="width:120px;display:none;" value="Overdue Task" onClick="fnc_generate_report_main(3,4)" id="btn_due_task" />
                    <input type="button" class="formbutton" style="width:100px;display:none;" value="Penalty" onClick="fnc_generate_report_main(3,5)" id="btn_penalty" />
                     <div id="print_button" style="text-align:center;"></div>
                </td>
           </tr>
           
        </table>
       
    	</fieldset>
         </div>
        <div style="margin-top:5px; text-align:left" id="report_container"></div>
		
    
	 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
 
 
 

 
 

