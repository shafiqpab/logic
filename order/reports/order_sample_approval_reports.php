<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	27-01-2013
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
//echo load_html_head_contents("Sample Info","../../", 1, 1, $unicode);
echo load_html_head_contents("Sample Info", "../../", 1, 1,$unicode,'','');
?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission = '<? echo $permission; ?>';
var rel_path = '../../';	
function fn_report_generated(shiping_status)
{
	if (form_validation('cbo_company_name','Plsease Select Comapny')==false)//*txt_date_from*txt_date_to*Please Select From Date*Please Select To Date
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to'));
		
		var data="action=report_generate&shipingStatus="+shiping_status+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*txt_style*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/order_sample_approval_reports_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split("****");
		var tot_rows=reponse[2];
		$('#report_container2').html(reponse[0]);
			document.getElementById('report_container1').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';/*<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>*/
 		
		 var tableFilters = 
		 {
			col_0: "none",			
			col_19: "none",
			col_14: "select",
			col_15: "select",
			display_all_text: " -- All --",
		}							
		setFilterGrid("tbl_details",-1,tableFilters);	
		
		show_msg('3');
		release_freezing();
 	}
}

function show_comment_info(job_no)
{
	if(job_no)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'po_comments.php?job_no='+job_no, 'Comment Details', 'width=500px,height=300px,center=1,resize=0,scrolling=0',' ../')	
	}	
}

	function new_window(type)
	{
		var report_div=''; var scroll_div='';
		if(type==1)
		{
			report_div="print_report_samp";
			//scroll_div='scroll_body';
		}
		else if(type==2)
		{ 
			report_div="print_report_pp";
			//scroll_div='scroll_body2';
		}
 		
 		//document.getElementById(scroll_div).style.overflow="auto";
		//document.getElementById(scroll_div).style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
		
		//document.getElementById(scroll_div).style.overflowY="scroll";
		//document.getElementById(scroll_div).style.maxHeight="380px";
	}	
	
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	 
</script>
</head>
 
<body onLoad="set_hotkey();">
<form id="sample_approval_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
         <fieldset style="width:1200px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="1200px" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <thead>                    
                    <th width="140">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="110">Team</th>
                    <th width="120">Team Member</th>
                    <th width="80">Job No</th>
                    <th width="60">Year</th>
                    <th width="80">Order No</th>
                    <th width="70">File No</th>
                    <th width="70">Ref. No</th>
                    <th width="80">Style No</th>
                    <th width="130" colspan="2">Shipment Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_sample_approval_reports_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td> 
                        <td><? echo create_drop_down( "cbo_team_name", 110, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_sample_approval_reports_controller', this.value, 'load_drop_down_team_member', 'team_td' )" ); ?></td>
                        <td id="team_td"><? echo create_drop_down( "cbo_team_member", 120, $blank_array,"", 1, "- Select Team Member- ", $selected, "" ); ?></td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px"  placeholder="Job prifix No" ></td>
                        <td><? echo create_drop_down( "cbo_year", 60, $year,"", 1, "- Select- ",  date("Y",time()), "" ); ?></td>
                        <td><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Write" ></td>
                        <td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px" placeholder="Write" ></td>
                        <td><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px" placeholder="Write" ></td>
                        <td><input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:70px" placeholder="Write"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                    <tr>
                        <td colspan="13" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container1" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
