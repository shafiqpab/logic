<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Procurement Progress Report V2.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	16-05-2022
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
echo load_html_head_contents("Procurement Progress Report V2", "../../",  1, 1, $unicode,1,'');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission = '<? echo $permission; ?>';
		
	function generate_report()
	{
		if($('#txt_req_no').val()!="" || $('#txt_wo_no').val()!="" || $('#txt_pi_num').val()!="" || $('#txt_btb_num').val()!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*cbo_date_type*txt_date_from*txt_date_to','Company Name*Date Type*Date*Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_item_category_id*txt_req_no*txt_wo_no*txt_pi_num*txt_btb_num*cbo_date_type*txt_date_from*txt_date_to*cbo_year_selection","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/procurement_progress_report_v2_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			// setFilterGrid("table_body",-1);			
		}
	}	
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 	
	}
	
	function openmypage_popup(wopi_id,prod_id,rcv_basis,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_report_v2_controller.php?wopi_id='+wopi_id+'&prod_id='+prod_id+'&rcv_basis='+rcv_basis+'&action='+action, page_title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../');
	}	


	function fn_chkField(str)
	{
		if(str==1) 
		{
			if ($('#txt_req_no').val()!="") 
			{
				$('#txt_wo_no').val("").attr("disabled",true);
				$('#txt_pi_num').val("").attr("disabled",true);
				$('#txt_btb_num').val("").attr("disabled",true);
			}
		}
		else if(str==2) 
		{
			if ($('#txt_wo_no').val()!="") 
			{
				$('#txt_req_no').val("").attr("disabled",true);
				$('#txt_pi_num').val("").attr("disabled",true);
				$('#txt_btb_num').val("").attr("disabled",true);
			}
		}
		else if(str==3) 
		{
			if ($('#txt_pi_num').val()!="") 
			{
				$('#txt_wo_no').val("").attr("disabled",true);
				$('#txt_req_no').val("").attr("disabled",true);
				$('#txt_btb_num').val("").attr("disabled",true);
			}
		}
		else if(str==4) 
		{
			if ($('#txt_btb_num').val()!="") 
			{
				$('#txt_wo_no').val("").attr("disabled",true);
				$('#txt_req_no').val("").attr("disabled",true);
				$('#txt_pi_num').val("").attr("disabled",true);
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_procureument_rpt" name="frm_procureument_rpt">
		<div style="width:1140px;">
			<h3 align="left" id="accordion_h1" style="width:1140px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel"> 
				<fieldset style="width:1140px;">
					<table class="rpt_table" cellspacing="0" cellpadding="0" width="1120" rules="all">
						<thead>
							<tr>
								<th width="140" class="must_entry_caption">Company</th>
								<th width="130">Item Category</th>                    
								<th width="110">Requisition No</th>
								<th width="110">WO No</th>
								<th width="110">PI Number</th>
								<th width="110">BTB LC Number</th>
								<th width="110" class="must_entry_caption">Date Type</th>
								<th width="90" class="must_entry_caption">Date From</th>
								<th width="90" class="must_entry_caption">Date To</th>
								<th ><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_procureument_rpt','report_container*report_container2','','','')" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center">
								<?
									echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "","" );
								?>
								</td>
								<td  align="center">
								<? 
									echo create_drop_down( "cbo_item_category_id", 120,$general_item_category,"", 1, "-- Select --", $selected, "","","","","","");
								?>
								</td>                        
								<td align="center">
									<input type="text" id="txt_req_no" name="txt_req_no" style="width:100px;" class="text_boxes_numeric" onBlur="fn_chkField(1);" >
								</td>
								<td align="center">
									<input type="text" id="txt_wo_no" name="txt_wo_no" style="width:100px;" class="text_boxes_numeric" onBlur="fn_chkField(2);">
								</td>
								<td align="center">
									<input type="text" id="txt_pi_num" name="txt_pi_num" style="width:100px;" class="text_boxes" onBlur="fn_chkField(3);">
								</td>
								<td align="center">
									<input type="text" id="txt_btb_num" name="txt_btb_num" style="width:100px;" class="text_boxes" onBlur="fn_chkField(4);">
								</td>
								<td> 
									<? $dateArr=array(1=>'Requisition Date', 2=>'Work Order Date', 3=>'PI Date', 4=>'BTB LC Date');
									echo create_drop_down( "cbo_date_type", 110, $dateArr, "", 1, "-- Select --", 0, "", "", ""); ?>
								</td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:80px">
								</td>
								<td align="center">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
								</td>
								<td align="center">
									<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div id="report_container" align="center"></div>
		<div id="report_container2"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>