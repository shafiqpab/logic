<?
/*-------------------------------------------- Comments -----------------------
Purpose			:	This Form Will Create Quick Costing report.
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	11-12-2019
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
echo load_html_head_contents("Quick Costing report.", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_operation: {
			id: ["value_total_wo_qnty"],
		    col: [7],
		    operation: ["sum"],
		    write_method: ["innerHTML"]
		}
	} 
	
	function fn_report_generated(operation)
	{
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		var divData=msgData="";
		if(txt_date_from=="" && txt_date_to=="" && cbo_buyer_id==0)
		{
			var divData="cbo_buyer_id";	
			var msgData="Buyer Name";	
		}
		
		if(txt_date_from=="" && txt_date_to=="" && cbo_buyer_id==0)
		{
			if(form_validation(divData,msgData)==false){
				return;
			}
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_buyer_id*txt_date_from*txt_date_to*cbo_type_id',"../../")+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/qc_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//var tot_rows=reponse[2];
			$('#report_container4').html(reponse[0]);
			document.getElementById('report_container3').innerHTML=report_convert_button('../../');
			document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px;"/>';
			
	 		show_msg('3');
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}
	
	//function new_window(html_filter_print)
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		$('#table_body tbody tr:first').show();
	}
	
	function generate_qc_report(qc_no,cost_sheet_no,action,entry_form)
	{
		var report_title="ESTIMATE COST SHEET";
		var hid_qc_no=qc_no;
		var txt_costSheetNo=cost_sheet_no;
		//alert(entry_form);
		if(entry_form=='430')
		{
			generate_report_file( hid_qc_no+'*'+txt_costSheetNo+'*'+report_title, action,'../spot_costing/requires/quick_costing_woven_controller',entry_form);
		}
		else
		{
			generate_report_file( hid_qc_no+'*'+txt_costSheetNo+'*'+report_title, action,'../spot_costing/requires/quick_costing_controller',entry_form);
		}
	}
	
	function generate_report_file(data,action,page,entry_form)
	{
		if(entry_form=='430')
		{
			window.open("../spot_costing/requires/quick_costing_woven_controller.php?data=" + data+'&action='+action, true );
		}
		else
		{
			window.open("../spot_costing/requires/quick_costing_controller.php?data=" + data+'&action='+action, true );
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="qcReport_1" id="qcReport_1" autocomplete="off" > 
    <h3 style="width:750px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:750px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Buyer</th>
                        <th width="150" class="must_entry_caption" colspan="2">Costing Date Range</th>
                        <th width="150">Costing Stage</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form('qcReport_1', 'report_container3*report_container4', '','','');" /> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"></td>
                        <td>
							<? $costingStage=array(1=>"Confirm",2=>"Pending");
                            echo create_drop_down( "cbo_type_id", 150, $costingStage,'', 1, "-All-",0, "" ); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" /></td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="center"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
