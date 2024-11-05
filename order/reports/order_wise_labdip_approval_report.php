<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Labdip Approval Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	12-03-2013
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
echo load_html_head_contents("Labdip Approval Report", "../../", 1, 1,$unicode,'1','');

?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_job_no*txt_order_no*cbo_year_selection*txt_style*txt_date_from*txt_date_to*txt_file_no*txt_int_ref_no',"../../");
			freeze_window(3);
			http.open("POST","requires/order_wise_labdip_approval_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+',1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if(tot_rows*1>1)
			{
				 var tableFilters = 
				 {
					 /* col_0: "none",
					  col_12: "none",
					  col_20: "none",
					display_all_text: " ---Show All---",*/
					col_operation: {
					id: ["total_order_qnty","total_order_qnty_in_pcs"],
				    col: [10,12],
				    operation: ["sum","sum"],
				    write_method: ["innerHTML","innerHTML"]
					}	
				}
				
				setFilterGrid("table_body",-1,tableFilters);
			}
			show_msg('3');
			release_freezing();
		}
	}
	 

	function new_window(html_filter_print,type)
	{
		if(type==1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			$("#data_panel2").hide();
			
			if(html_filter_print*1>1) $("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="400px";
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
			
			$("#data_panel2").show();
			
			if(html_filter_print*1>1) $("#table_body tr:first").show();
		}
		else if(type==2)
		{
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('lapdib_approval_div').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
		}
	}	
	
</script>

</head>

<body onLoad="set_hotkey();">
<form id="order_wise_labdip_approval_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1300px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:1300px;">
                <table class="rpt_table" width="1300" cellpadding="1" cellspacing="2" border="1" rules="all">
                   <thead>                    
                        <th width="130">Company Name</th>
                        <th width="130">Buyer Name</th>
                        <th width="130">Team</th>
                        <th width="130">Team Member</th>
                        <th width="90">JOb No</th>
                        <th width="100">Order No</th>
                        <th width="90">File No</th>
                        <th width="90">Int. Ref. No</th>
                        <th width="100">Style No</th>
                        <th width="160">Shipment Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
                            <?
                            	echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_labdip_approval_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                            </td>
                            <td id="buyer_td" align="center">
                            <? 
                            	echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                            </td>
                            <td align="center">
                            <?
                            	echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_wise_labdip_approval_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                            ?>
                            </td>
                            <td id="team_td" align="center">
                            <? 
                            	echo create_drop_down( "cbo_team_member", 130, $blank_array,"", 1, "- Team Member- ", $selected, "" );
                            ?>	
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px"  placeholder="Job prifix No" >
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px"  placeholder="Order No" >
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"  placeholder="File No" >
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" style="width:80px"  placeholder="Ref. No" >
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:100px"   >
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                            	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" >
                            </td>
                            <td align="center">
                            	<input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellpadding="1" cellspacing="2">
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>     
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
