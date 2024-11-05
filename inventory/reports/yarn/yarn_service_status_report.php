<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Service Status Report
				
Functionality	:	
JS Functions	:
Created by		:	Md Abu Sayed
Creation date 	: 	31-07-2021
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
echo load_html_head_contents("Yarn Service Status Report","../../../", 1, 1, $unicode,1,1); 
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
    else
    {
        var txt_job_no       = $('#txt_job_no').val();
        var txt_wo_no        = $('#txt_wo_no').val();
        var txt_internal_ref = $('#txt_internal_ref').val();
        var cbo_wo_type      = $('#cbo_wo_type').val();

        if(cbo_wo_type == 1)
        {            
            if (txt_job_no=="" && txt_wo_no=="" && txt_internal_ref=="")
            {
                if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To')==false)
                {
                    return;
                }
            }
        }
        else
        {
            if ( txt_wo_no=="")
            {
                if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To')==false)
                {
                    return;
                }
            }
           
        }	

        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_supplier_name*txt_job_no*hide_job_id*txt_date_from*txt_date_to*txt_wo_no*hide_wo_id*cbo_wo_type*txt_internal_ref',"../../../")+'&report_title='+report_title+'&type='+type;
        //alert (data);
        freeze_window(3);
        http.open("POST","requires/yarn_service_status_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;  
    }
}

function company_onchange() 
{
    $("#cbo_wo_type").val(1);
    $('#txt_job_no').prop("disabled", false);
    $('#txt_internal_ref').prop("disabled", false);
	
}
function wo_type_onchange() 
{
	if($("#cbo_wo_type").val() != 1)
	{
		$('#cbo_buyer_name').prop("disabled", true);
		$('#txt_job_no').prop("disabled", true);
		$('#txt_internal_ref').prop("disabled", true);
        $('#txt_job_no').val("");
		$('#cbo_buyer_name').val("");
        $('#txt_internal_ref').val("");
        $('#txt_wo_no').val("");
	}
    else
    {
        $('#cbo_buyer_name').prop("disabled", false);
        $('#txt_job_no').prop("disabled", false);
        $('#txt_internal_ref').prop("disabled", false);
        $('#txt_wo_no').val("");
    }
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		show_msg('3');
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
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

function openmypage_job()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
   
    var companyID = $("#cbo_company_name").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year = $("#cbo_year").val();
    var page_link='requires/yarn_service_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
    var title='Job No Search';
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var job_no=this.contentDoc.getElementById("hide_job_no").value;
        var job_id=this.contentDoc.getElementById("hide_job_id").value;
        //alert (job_no);
        $('#txt_job_no').val(job_no);
        $('#hide_job_id').val(job_id);	 
    }
}

function openmypage(job_no,booking_id,color,lot,action,trans_type,issue_ids,issue_rtn_ids)
{ 
	var companyID = $("#cbo_company_name").val();
	var popup_width='600px';
	var data_ref='requires/yarn_service_status_report_controller.php?companyID='+companyID+'&job_no='+job_no+'&booking_id='+booking_id+'&color='+color+'&lot='+lot+'&action='+action+'&trans_type='+trans_type+'&issue_ids='+issue_ids+'&issue_rtn_ids='+issue_rtn_ids;
	//alert(data_ref);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', data_ref, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

function wo_popup()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_name").val();
    var cbo_buyer_name = $("#cbo_buyer_name").val();
    
    var page_link='requires/yarn_service_status_report_controller.php?action=work_no_popup&companyID='+companyID+'&cbo_buyer_name='+cbo_buyer_name;
    var title='Work No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=470px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var wo_no=this.contentDoc.getElementById("hide_wo_no").value;
        var wo_id=this.contentDoc.getElementById("hide_wo_id").value;
        //var booing_type=this.contentDoc.getElementById("hide_booing_type").value;
        //alert(wo_no);
        //alert(hide_recv_id);
        $('#txt_wo_no').val(wo_no);
        $('#hide_wo_id').val(wo_id);
        //$('#hide_booking_type').val(booing_type);
        
    }
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>	
    <form name="ServiceYarnReport_1" id="ServiceYarnReport_1" autocomplete="off" > 
    <h3 align="left" id="accordion_h1" style="width:1040px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
            <fieldset style="width:1040px;">
            <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company Name</th>
                            <th>Order Type</th>
                            <th>Party Name</th>
                            <th>Buyer Name</th>
                            <th>Job No</th>
                            <th id="td_search">Work Order No</th>
                            <th>Internal Ref.</th>
                            <th colspan="2" id="td_date">Work Order Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('ServiceYarnReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_service_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/yarn_service_status_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );company_onchange();" );
                            ?>                            
                        </td>
                        <td id="work_order_type_td">
							<? 
								$wo_type_arr = array(1=>'With Order',2=>'Non Order');
                                echo create_drop_down( "cbo_wo_type", 100, $wo_type_arr,"", 0, "-- Select Type --", $selected, "wo_type_onchange()",0,"" );
                            ?>
                        </td>
                        <td id="supplier_td">
							<? 
                                echo create_drop_down( "cbo_supplier_name", 130, $blank_array,"", 1, "--Select Supplier--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_job()" >
                             <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td align="center" title="Wo Prefix-Comma Seperate">
                             <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes"  onDblClick="wo_popup(1)" style="width:100px"  placeholder="Write/Browse" > 
                             <input type="hidden" name="hide_wo_id" id="hide_wo_id" class="text_boxes"  style="width:20px">
                        </td>
                       
                        <td align="center">
                             <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:50px" placeholder="Write">
                        </td>
                        <td>
                             <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                        </td>
                        <td>
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tfoot>
                        <tr align="center">
                            <td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
            </table>
            </fieldset>
    </div>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2"></div>
<div style="display:none" id="data_panel"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>