<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Daily Yarn Issue Return Status Report.
Functionality	:	
JS Functions	:
Created by		:	Didarul Alam
Creation date 	: 	03/04/2019
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
echo load_html_head_contents(" Daily Yarn Issue Return Status Report", "../../../", 1, 1,'',1,1);
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;	
		var txt_ord_no=document.getElementById('txt_ord_no').value;
		var txt_lot_no=document.getElementById('txt_lot_no').value;
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        if(txt_job_no == "" && txt_style_ref == "" && txt_ord_no == "" && txt_lot_no == "")
        {
            if(form_validation('txt_date_from*txt_date_to','From date*To date')==false)
            {
                return;
            }
        }
        var action = "";
        if(type == 1){
            action = "report_generate";
        }else if(type == 2){
            action = "report_generate_party_wise";
        }
        var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*txt_ord_no*txt_lot_no*txt_date_from*txt_date_to',"../../../");


    freeze_window(3);
		http.open("POST","requires/daily_yarn_issue_return_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("****");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+response[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
        if(response[2] == 1) {
            var tableFilters =
                {
                    col_operation: {
                        id: ["value_total_alocation_qty", "value_total_issue_qty", "value_total_return_qty", "value_total_reject_qty"],
                        col: [16, 17, 18, 19],
                        operation: ["sum", "sum", "sum", "sum"],
                        write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                    }
                }
            setFilterGrid("table_body", -1, tableFilters);
        }
		show_msg('3');
		release_freezing();
 	}
	
}

function new_window(type = 1)
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
    if(type == 1){
        $("#table_body tr:first").hide();
    }
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
    if(type == 1){
        $('#scroll_body tr:first').show();
    }
}
</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">

<form id="yarnPurchaseReqReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../../",'');  ?>
         
         <h3 style="width:875px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:840px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Style Ref</th>
                    <th>Order No</th>
                    <th>Lot</th>
                    <th colspan="2" class="must_entry_caption">Date Range</th>
                             
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('yarnPurchaseReqReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_yarn_issue_return_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                       
                        <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td>
                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td>
                        <input type="text" name="txt_ord_no" id="txt_ord_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        <td>
                          <input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px" placeholder="Write" />
                        </td>
                        
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                        <td>
                            <input type="button" id="show_button_party_wise" class="formbutton" style="width:80px" value="Party Wise" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
