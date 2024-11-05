<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Plan Wise Yarn Issue Monitoring Report.
Functionality   :	
JS Functions    :
Created by		: 
Creation date   : 	18-07-2017
Updated by 		: 	Shafiq	
Update date		: 	29-08-2020	   
QC Performed BY :		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Party Wise\Booking Wise Process Loss Report", "../../", 1, 1,$unicode,1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	var job_no = $("#txt_job_no").val();
	var booking_no = $("#txt_booking_no").val();
	var booking_no = $("#txt_booking_no").val();
	var internalRef_no = $("#txt_internal_ref_no").val();

	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	if (job_no =="" && booking_no =="" && internalRef_no =="") {	
		/*if (form_validation('txt_date_from*txt_date_to','From Date*End Date')==false)
		{
			return;
		}*/
		alert('Please Fill up ,Job/Booking/Ref No');
		return;
	}
	
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_job_no*hide_job_id*txt_booking_no*hide_booking_id*txt_internal_ref_no',"../../")+'&report_type='+type;

	freeze_window(3);
	http.open("POST","requires/party_wise_booking_wise_process_loss_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;'; 
		show_msg('3');
		release_freezing();
 	}
	
}


function openmypage_booking()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var job_IDs = $("#hide_job_id").val();
	var txt_job_no = $("#txt_job_no").val();
      //var cbo_year = $("#cbo_year").val();
        
	var page_link='requires/party_wise_booking_wise_process_loss_report_controller.php?action=booking_no_search_popup&companyID='+companyID+'&txt_job_no='+txt_job_no;
	var title='Booking No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=420px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		
		$('#txt_booking_no').val(job_no);
		$('#hide_booking_id').val(job_id);	 
	}
}

function openmypage_job()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
    var cbo_year = $("#cbo_year").val();
        
	var page_link='requires/party_wise_booking_wise_process_loss_report_controller.php?action=job_no_search_popup&companyID='+companyID;
	var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		
		$('#txt_job_no').val(job_no);
		$('#hide_job_id').val(job_id);	 
	}
}
	

function generate_report(company_id,program_id)
{
	 print_report( company_id+'*'+program_id + '*' + '../../', "print", "../requires/yarn_requisition_entry_controller" ) ;
}



function new_window()
{
	const scroll_body = document.querySelector('#scroll_body');
	const scroll_body_dtls = document.querySelector('#scroll_body_dtls');

	if (scroll_body) {
	    document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

	}
	if (scroll_body_dtls) {
	    document.getElementById('scroll_body_dtls').style.overflow="auto";
		document.getElementById('scroll_body_dtls').style.maxHeight="none";

	}
	
	
	
	//$("#tbl_list_search").find('input([name="check"])').hide();	
	$('input[type="checkbox"]').hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('input[type="checkbox"]').show();
	
	

	if (scroll_body) {
	    document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";

	}
	if (scroll_body_dtls) {
	    document.getElementById('scroll_body_dtls').style.overflowY="scroll";
		document.getElementById('scroll_body_dtls').style.maxHeight="330px";

	}
}

function openmypage_allocation(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_booking_wise_process_loss_report_controller.php?data='+data+'&action='+action, 'Allocation Details', 'width=905px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_program(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_booking_wise_process_loss_report_controller.php?data='+data+'&action='+action, 'Programs Details', 'width=1245px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_requisition(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_booking_wise_process_loss_report_controller.php?data='+data+'&action='+action, 'Requisition Details', 'width=720px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_issue(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_booking_wise_process_loss_report_controller.php?data='+data+'&action='+action, 'Issue Details', 'width=1085px, height=400px,center=1,resize=0,scrolling=0','../');
}

function openmypage_production(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_booking_wise_process_loss_report_controller.php?data='+data+'&action='+action, 'Production Details', 'width=1245px, height=400px,center=1,resize=0,scrolling=0','../');
}


</script>


</head>
 
<body onLoad="set_hotkey();">

<form id="planWiseYarnIssueMonitor_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:850px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th>LC Company</th>
                    <th>Job No</th>
                    <th>Booking No</th>
                    <th>Internal Ref No</th>
                     <th> <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('planWiseYarnIssueMonitor_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                   <!-- <th>Order No</th>
                   <th>Party Wise</th>
                   
                    <th colspan="2" class="must_entry_caption">Booking Date Range</th>-->
                </thead>
                <tbody>
                    <tr class="general" id="td_company">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "" );
                            ?>
                        </td>
                       
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:170px" placeholder="Browse" onDblClick="openmypage_job(1);" autocomplete="off" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>                            
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:170px" placeholder="Browse" onDblClick="openmypage_booking();" onChange="$('#hide_booking_id').val('');" autocomplete="off" readonly>
                            <input type="hidden" name="hide_booking_no" id="hide_booking_id" readonly>
                        </td>
                         <td>
                            <input type="text" name="txt_internal_ref_no" id="txt_internal_ref_no" class="text_boxes" style="width:100px" placeholder="Write" autocomplete="off" >
                        </td>
                       <!--  <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_job(2);" autocomplete="off" readonly>
                            <input type="hidden" name="hide_po_id" id="hide_po_id" readonly>
                        </td>-->
                         <!--<td> 
                            <?
                              //echo  create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
                            ?>
                        </td> -->

                      <!--  <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                        </td>-->
                         <td colspan="2" align="center">
                        
                        	<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
         
                           
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="4" align="center"><? //echo load_month_buttons(1); ?></td>
                       
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding:10px 0;"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
