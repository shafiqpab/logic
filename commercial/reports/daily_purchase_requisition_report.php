<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Purchase Requisition Report.
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	30-03-2022
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
echo load_html_head_contents("Daily Purchase Requisition Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function generate_report(rep_type)
	{

		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_store_name*cbo_store_name*cbo_department_name*cbo_category_name*txt_req_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_purchase_requisition_report_controller.php",true);
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
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
 			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
	}

	function openmypage(req_id,category_id)
	{
		page_link='requires/daily_purchase_requisition_report_controller.php?action=req_details&req_id='+req_id+'&category_id='+category_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Requisition Details', 'width=1200px,height=350px,center=1,resize=0,scrolling=0','../');
	}

</script>
</head>

<body onLoad="set_hotkey();">
<form id="dailyPurchaseRequisition">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:880px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:880px;">
                <table class="rpt_table" width="870" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="120" class="must_entry_caption">Company</th>
                            <th width="110">Location</th>
                            <th width="110">Store</th>
                            <th width="100">Department</th>
                            <th width="100">Item Category</th>
                            <th width="100">Requisition No</th>
                            <th width="90" class="must_entry_caption">Date From</th>
                            <th width="90" class="must_entry_caption">Date To</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('dailyPurchaseRequisition','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_purchase_requisition_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/daily_purchase_requisition_report_controller', this.value, 'load_drop_down_department', 'department_td' );" );
                                ?>
                            </td>
                            <td align="center" id="location_td">
                            	<? echo create_drop_down( "cbo_location_name", 110, $blank_array,"", 1,"-- All --",0,"" ); ?>
                            </td>
                            <td id="store_td"> 
								<? echo create_drop_down( "cbo_store_name", 110, $blank_array,"", 1,"-- All --",0,"" ); ?>
							</td>
                            <td id="department_td"> 
								<? echo create_drop_down( "cbo_department_name", 100, $blank_array,"", 1,"-- All --",0,"" ); ?>
							</td>
                            <td align="center"> 
								<? echo create_drop_down( "cbo_category_name", 100,$item_category,"", 1, "-- All --", $selected, "",0,"$item_cate_credential_cond","","","1,2,3,12,13,14,24,25,35");?>
							</td>
							<td align="center">
                            	<input type="text" name="txt_req_no" id="txt_req_no" value="" class="text_boxes" style="width:100px" placeholder="Write" />
                            </td align="center">
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">
                            </td>
							<td align="center">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                            </td>
                            <td align="center">
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
						<tr>
							<td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
						</tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>