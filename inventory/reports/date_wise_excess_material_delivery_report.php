<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Excess Material  Delivery Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	29-01-2023
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
echo load_html_head_contents("Date Wise Excess Material  Delivery Report","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
    // if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
	// {
	// 	return;
	// }

    if (form_validation('cbo_company_name', 'Comapny Name') == false)
    {
        return;
    }
    var txt_style     = $("#txt_style").val();
    var txt_job       = $("#txt_job").val();
    var txt_po        = $("#txt_po").val();
    var txt_date_from = $("#txt_date_from").val();
    var txt_date_to   = $("#txt_date_to").val();

    if(txt_style =="" || txt_job =="" || txt_po =="")
    {
        if(txt_date_from =="" && txt_date_to =="")
        {
            alert("Please select either date range Or Style Or Job No Or PO No...");
            return;
        }
    }
			
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_id*txt_style*txt_job_no*txt_po_no*cbo_category*txt_date_from*txt_date_to*txt_job_id',"../../")+'&report_title='+report_title + "&type=" + type;
	//alert (data);
	freeze_window(3);
	http.open("POST","requires/date_wise_excess_material_delivery_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

        //setFilterGrid("table_body",-1);
        //setFilterGrid("table_body1",-1);
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
    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company_id=document.getElementById('cbo_company_name').value;
    var buyer_id=document.getElementById('cbo_buyer_id').value;
    var data = company_id+'_'+buyer_id;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/date_wise_excess_material_delivery_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1055px,height=420px,center=1,resize=0','../')
    
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        // alert(no+','+id);
        
        var no=this.contentDoc.getElementById("txt_booking_no").value;
        var id=this.contentDoc.getElementById("txt_booking_id").value;
        $('#txt_booking_no').val(no);
        $('#txt_booking_id').val(id);
    }
}

function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_excess_material_delivery_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
		
	}
}

function openmypage_job_no()
{
	var companyID = $("#cbo_company_name").val();
	var jobYear = $("#cbo_year").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_excess_material_delivery_report_controller.php?action=job_no_popup&companyID='+companyID+'&jobYear='+jobYear, 'Style,Job and Po Info Details', 'width=570px,height=420px,center=1,resize=0,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var job_no_des=this.contentDoc.getElementById("hidden_job_no").value; //Access form field with id="emailfield"
		var job_no_id=this.contentDoc.getElementById("hidden_job_no_id").value;
		var style_no=this.contentDoc.getElementById("hidden_style_no").value;
		var po_no=this.contentDoc.getElementById("hidden_po_no").value;
       
		$("#txt_job_no").val(job_no_des);
		$("#txt_job_id").val(job_no_id);
		$("#txt_style").val(style_no);
		$("#txt_po_no").val(po_no);
	}
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="samplDdailyYarnIssueReport_1" id="samplDdailyYarnIssueReport_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1020px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="140">Buyer</th>
                            <th width="110">Job Year</th>
                            <th width="110">Style</th>
                            <th width="110">Job No</th>
                            <th width="110">PO No</th>
                            <th width="110">Category</th>
                            <th width="200" id="based_on_th_up" colspan="2">Ch. Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('samplDdailyYarnIssueReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td valign="middle">
                            <? 
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_excess_material_delivery_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td valign="middle">
                           <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 110, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td valign="middle">
                        <input type="text" id="txt_style" name="txt_style" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_job_no();" placeholder="Browse" readonly/>
                        </td>
                        <td valign="middle">
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_job_no();" placeholder="Browse" readonly/>
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td valign="middle">
                            <input type="text" id="txt_po_no" name="txt_po_no" class="text_boxes" style="width:110px" value="" onDblClick="openmypage_job_no();" placeholder="Browse" readonly/>
                        </td>
                        <td >
                            <?
                               $search_type_as_arr = array(0 => "All", 1 => "Trims", 2 => "Finish Fabric", 3 => "Woven Fabric");
                               echo create_drop_down("cbo_category", 110, $search_type_as_arr, "", 0, "-Select-", 0, "", 0, "", "", "", "", "");
                            ?>
                        </td>

                       <td valign="middle" align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:100px" placeholder="From Date"/>
                        </td>
                        <td valign="middle" align="center">
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:100px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)"  style="width:60px" class="formbutton" />
                        </td>

                    </tr>
                    <tr>
                        <td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            </fieldset> 
            
            <div id="report_container" align="center" style="padding: 10px;"></div>
            <div id="report_container2"></div>   
            
        </div>
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
