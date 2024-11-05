<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Quick Costing Statement report.
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin 
Creation date 	: 	03-07-2022
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
echo load_html_head_contents("Quick Costing Statement report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	
	
	function fn_report_generated(type)
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
			var data="action=report_generate"+get_submitted_data_string('cbo_buyer_id*cbo_season_id*cbo_subDept_id*txt_styleRef*txt_costSheetNo*cbo_type_id*txt_date_from*txt_date_to*cbo_costingstage_id',"../../")+'&report_title='+report_title+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/marketing_costing_report_woven_controller.php",true);
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
			var reportType=reponse[3];
			//alert(reportType)
			$('#report_container2').html(reponse[0]);
			if(tot_rows*1>1)
			{
				var tableFilters = {
					col_operation: {
					   id: ["value_td_fab","value_td_trim","value_td_print","value_td_wash","value_td_other","value_td_commercial","value_td_lc","value_td_commission","value_td_cm","value_td_fob"],
					   col: [18,19,20,21,22,23,24,25,26,27],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\'../../\' )" value="HTML Preview" name="Print" class="formbutton" style="width:100px"></a>&nbsp;&nbsp;';


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
	
	
	
	function generate_qc_report(qc_no,cost_sheet_no,action,entryForm)
	{
		var report_title="ESTIMATE COST SHEET";
		var hid_qc_no=qc_no;
		var txt_costSheetNo=cost_sheet_no;
		
		if(trim(entryForm)=="471")
		{
			generate_report_file( hid_qc_no+'*'+txt_costSheetNo+'*'+report_title, action,'../spot_costing/requires/short_quotation_v3_controller',entryForm);
		}
		else if(trim(entryForm)=="430")
		{
			//alert(trim(entryForm));
			generate_report_file( hid_qc_no+'*'+txt_costSheetNo+'*'+report_title, action,'../spot_costing/requires/quick_costing_woven_controller',entryForm);
		}
		else
		{
			generate_report_file( hid_qc_no+'*'+txt_costSheetNo+'*'+report_title, action,'../spot_costing/requires/quick_costing_controller',entryForm);
		}
	}
	
	function generate_report_file(data,action,page,entryForm)
	{
		if(trim(entryForm)=="471")
		{
			window.open("../spot_costing/requires/short_quotation_v3_controller.php?data=" + data+'&action='+action, true );
		}
		else if(trim(entryForm)=="430")
		{
				//alert(data);
			window.open("../spot_costing/requires/quick_costing_woven_controller.php?data=" + data+'&action='+action, true );
		}
		else
		{
			window.open("../spot_costing/requires/quick_costing_controller.php?data=" + data+'&action='+action, true );
		}
	}
	function fnc_costing_details(qc_no,buyer,costing_date,ex_rate,offer_qty,action)
    {
        //alert(buyer)
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/marketing_costing_report_woven_controller.php?qc_no='+qc_no+'&buyer='+buyer+'&costing_date='+costing_date+'&ex_rate='+ex_rate+'&offer_qty='+offer_qty+'&action='+action,'Costing Popup', 'width=958px,height=450px,center=1,resize=0','../');
        emailwindow.onclose=function()
        {
            
        }

    }

    function fnc_revise_details(cost_sheet_no,qc_no,company_id,revise_no,action)
	{
		var data=qc_no+'*'+cost_sheet_no+'*'+company_id+'*'+revise_no+'*Marketing Costing Sheet*0*'+'../../../';
		window.open("../spot_costing/requires/quick_costing_woven_controller.php?data=" + data+'&action='+action, true );
		
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="qcreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1020px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1020px" > 		 
            <fieldset style="width:1000px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Buyer</th>
                    <th width="100">Season</th>
                    <th width="100">Department</th>
                    <th width="100">Style Ref.</th>
                    <th width="80">Cost Sheet No</th>
                    <th width="80">Date Type</th>
                    <th width="130" class="must_entry_caption" colspan="2">Date Range</th>
                    <th width="100">Costing Stage</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('qcreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_buyer_id", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "load_drop_down( 'requires/marketing_costing_report_woven_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/marketing_costing_report_woven_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' );"); ?></td>
                        <td id="season_td"><?=create_drop_down( "cbo_season_id", 100, $blank_array,'', 1, "-Select Season-",$selected, "" ); ?></td>
                        <td id="sub_td"><?=create_drop_down( "cbo_subDept_id", 100, $blank_array,'', 1, "-Select Dept-",$selected, "" ); ?></td>
                        <td><input style="width:90px;" type="text" class="text_boxes" name="txt_styleRef" id="txt_styleRef" placeholder="Write"/></td>
                        <td><input style="width:70px;" type="text" class="text_boxes_numeric" name="txt_costSheetNo" id="txt_costSheetNo" placeholder="Write" /></td>
                        <td>
							<? $dateType_arr=array(1=>"Delivery Date",2=>"Costing Date");
                            echo create_drop_down( "cbo_type_id", 80, $dateType_arr,'', 0, "-Select Type-",2, "" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td>
							<? $costingStage=array(1=>"Confirm",2=>"Pending");
                            echo create_drop_down( "cbo_costingstage_id", 100, $costingStage,'', 1, "-All-",0, "" ); ?></td>
                        <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="10"><?=load_month_buttons(1); ?></td>
                        
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
