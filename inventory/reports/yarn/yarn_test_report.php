<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Test Report
				
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

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Test Report","../../../", 1, 1, $unicode,1,1); 

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
    var txt_lot_no = $("#txt_lot_no").val();

    var txt_date_from = $("#txt_date_from").val();
    var txt_date_to = $("#txt_date_to").val();

    if(txt_lot_no =="" )
    {
        if(txt_date_from =="" && txt_date_to =="")
        {
            alert("Please select either date range or Lot No...");
            return;
        }
    }
			
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_supplier_id*txt_composition*txt_composition_id*cbo_yarn_type*cbo_yarn_count*txt_lot_no*cbo_appoval_status*cbo_qc_status*search_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title + "&type=" + type;
	//alert (data);
	freeze_window(3);
	http.open("POST","requires/yarn_test_report_controller.php",true);
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

function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company_id=document.getElementById('cbo_company_name').value;
    var buyer_id=document.getElementById('cbo_buyer_id').value;
    var data = company_id+'_'+buyer_id;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_test_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1055px,height=420px,center=1,resize=0','../../')
    
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_test_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
		
	}
}


function search_as_populate(str)
{
    if (str == 2 || str == 3)
    {
        $("#cbo_qc_status").val(0);
        $("#cbo_qc_status").attr('disabled',true);
    } 
    else
    {
        $("#cbo_qc_status").attr('disabled',false);
    }
}

function search_populate(str)
{
    if (str == 1)
    {
        document.getElementById('based_on_th_up').innerHTML = "QC Date";
    } 
    else if (str == 2)
    {
        document.getElementById('based_on_th_up').innerHTML = "Yarn Receive Date";
    }

}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="samplDdailyYarnIssueReport_1" id="samplDdailyYarnIssueReport_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1360px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1360" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="132" class="must_entry_caption">Company</th>
                            <th width="112">Supplier</th>
                            <th width="112">Yarn Type</th>
                            <th width="102">Count</th>
                            <th width="112">Composition</th>
                            <th width="82">Lot</th>
                            <th width="112">Approval Status</th>
                            <th width="100">QC Status</th>
                            <th width="150">Date Type</th>
                            <th width="200" id="based_on_th_up" colspan="2">QC Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('samplDdailyYarnIssueReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td valign="middle">
                            <? 
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/yarn_test_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                            ?>                            
                        </td>

                        <td valign="middle" id="supplier_td">
                            <? echo create_drop_down( "cbo_supplier_id", 112, $blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                        </td>
                        <td valign="middle">
                            <? echo create_drop_down( "cbo_yarn_type", 112, $yarn_type,"", 0, "--Select--", 0, "",0 ); ?>
                        </td>
                        <td valign="middle">
                            <?
                                echo create_drop_down("cbo_yarn_count",102,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td valign="middle">
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:112px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td valign="middle">
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:82px" value="" placeholder="Write"/>
                        </td>
                       
                        <td >
                            <?
                               $search_type_as_arr = array(1 => "All", 2 => "QC Pass", 3 => "Reject");
                               $fnc_name = "search_as_populate(this.value)";
                               echo create_drop_down("cbo_appoval_status", 100, $search_type_as_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                            ?>
                        </td>
                        <td valign="middle">
                            <?
                                echo create_drop_down( "cbo_qc_status", 100, $comments_acceptance_arr,"", 1, "All", $selected, "", "","");
                            ?>
                        </td>
                        <td >
                            <?
                               $search_type_arr = array(1 => "QC Date", 2 => "Yarn Receive Date");
                               $fnc_name = "search_populate(this.value)";
                               echo create_drop_down("search_type", 150, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
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
                        <td colspan="12" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            </fieldset> 
            
            <div id="report_container" align="center" style="padding: 10px;"></div>
            <div id="report_container2"></div>   
            
        </div>
    </form>    
</div>    
</body>  
<script>
	set_multiselect('cbo_yarn_type*cbo_yarn_count','0*0','0*0','','0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
