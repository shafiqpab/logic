<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Buyer Claims Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	30-06-2019
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Claims Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	
	
	function fn_report_generated(operation)
	{
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var txt_exdate_from=document.getElementById('txt_exdate_from').value;
		var txt_exdate_to=document.getElementById('txt_exdate_to').value;
		
		if( (trim(txt_exdate_from)=="" || trim(txt_exdate_to)=="") && (trim(txt_date_from)=="" || trim(txt_date_to)=="") || cbo_company==0){
			var divData="cbo_company_id*txt_date_from*txt_date_to";	
			var msgData="Company Name*From Date*To Date";	
		}
		if((trim(txt_exdate_from)=="" || trim(txt_exdate_to)=="") && (trim(txt_date_from)=="" || trim(txt_date_to)=="") && cbo_company!=0){
			var divData="txt_date_from*txt_date_to";	
			var msgData="From Date*To Date";	
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";	
		}
		
		if(cbo_company==0 || (txt_date_from=="" || txt_date_to=="") && (txt_exdate_from=="" || txt_exdate_to==""))
		{
			if(form_validation(divData,msgData)==false){
				return;
			}
		}		
		else
		{	
			//alert(txt_date_from+'_'+txt_date_to+'_'+txt_exdate_from+'_'+txt_exdate_to+'_'+cbo_company); return;
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_date_from*txt_date_to*txt_exdate_from*txt_exdate_to',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/buyer_claims_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			if(tot_rows*1>1){
				var tableFilters = {
					/*col_operation: {
					   id: ["td_po_qty","td_po_value","td_exfactory_value","td_claim_value"],
					   col: [8,9,12,13],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}*/	
				 }
				 setFilterGrid("table_body",-1,tableFilters);
			}
			 
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
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
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:750px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:750px" > 		 
            <fieldset style="width:750px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="150">Buyer</th>
                    <th width="140" class="must_entry_caption" colspan="2">Claim Date Range</th>
                    <th width="140" colspan="2">Ex-Factory Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/buyer_claims_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        
                        <td><input name="txt_exdate_from" id="txt_exdate_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_exdate_to" id="txt_exdate_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        
                        
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="7"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
