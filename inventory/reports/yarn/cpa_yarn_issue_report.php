<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create CPA Yarn Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	27-05-2015
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
echo load_html_head_contents("CPA Yarn Issue Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
	if(type == 3) 
	{
		/*if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}*/
	}
	else
	{
		// if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		// {
		// 	return;
		// }
		if (form_validation('cbo_company_name', 'Comapny Name') == false)
		{
            return;
        }
		var txt_cpa_booking_no = $("#txt_cpa_booking_no").val();

        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();

        if(txt_cpa_booking_no =="")
        {
            if(txt_date_from =="" && txt_date_to =="")
            {
                alert("Please select either date range or CPA No.");
                return;
            }
        }
	}
	
	if(type == 3) 
	{
		var action ='generate_report_only_excel';
    }
    else
    {
		var action ='report_generate';
	}

	var report_title=$( "div.form_caption" ).html();
	var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_cpa_booking_no*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;//*txt_job_no +"&zero_val="+zero_val
	//alert (data);return;
	freeze_window(3);
	http.open("POST","requires/cpa_yarn_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");

 		if(reponse[2]==3)
		{
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('excel').click();
				
			show_msg('3');
			release_freezing();				
			return;	
		}
		else
		{
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid('tbl_body',-1);
			setFilterGrid('tbl_body2',-1);		
			show_msg('3');
			release_freezing();
		}
	}
} 

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	document.getElementById('scroll_body2').style.overflow="auto";
	document.getElementById('scroll_body2').style.maxHeight="none";
	
	$('#tbl_body tr:first').hide();
	$('#tbl_body2 tr:first').hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="280px";
	document.getElementById('scroll_body2').style.overflowY="scroll";
	document.getElementById('scroll_body2').style.maxHeight="280px";
	
	$('#tbl_body tr:first').show();
	$('#tbl_body2 tr:first').show();
}

function openmypage(order_id,type,trans_id,prod_id)
{
	var popup_width='890px';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cpa_yarn_issue_report_controller.php?order_id='+order_id+'&action='+type+'&trans_id='+trans_id+'&prod_id='+prod_id, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="dailyYarnIssueReport_1" id="dailyYarnIssueReport_1" autocomplete="off" > 
    <h3 style="width:1020px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:1020px;" id="content_search_panel">
            <fieldset style="width:1020px;">
                <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="200" class="must_entry_caption">Company</th>                                
                            <th width="200">Buyer</th>
                            <th width="150">CPA No</th>
                            <th class="must_entry_caption" width="200">Transaction Date</th>
                            <th width="250"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('dailyYarnIssueReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cpa_yarn_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 180, $blank_array,"", 1, "--Select--", 0, "",0 ); ?></td>
                        <td width="150"><input type="text" id="txt_cpa_booking_no" name="txt_cpa_booking_no" class="text_boxes" style="width:120px" value="" /></td>
                        <td width="200">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date" />
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date" />
                        </td>
                        <td width="250">
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(0)" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" ><? echo load_month_buttons(1); ?></td>
                        <td colspan="" align="center">
                            <input type="button" name="search" id="search1" value="Excel Generate" onClick="generate_report(3)" style="width:100px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                </table> 
            </fieldset> 
        </div>
        <div id="report_container" align="center" style="padding: 10px 0"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
