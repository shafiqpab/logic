<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Consolidated Order Summary Report.
Functionality	:	
JS Functions	:
Created by		:	Saidul Islam Reza
Creation date 	: 	31-03-2015
Updated by 		:  	Jahid	
Update date		: 	20-05-2014	   
QC Performed BY	:		
QC Date			:	
Comments		: Update According to sujon vai requirment issue id 3793
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Consolidated Order Summary Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value"],
	   col: [2,3,4,5,6,7,8],
	   operation: ["sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	function fn_report_generated(type)
	{
		if(form_validation('txt_date_from*txt_date_to','Shipmet Date From*Shipmet Date To')==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_leader*txt_date_from*txt_date_to*cbo_date_type',"../../../");
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/consolidated_order_summary_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		
	}
		
		
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		setFilterGrid("table_body",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();
	}
	
	
function calculate_date(str)
{		
	if(str==0){
		var thisDate=($('#txt_date_from').val()).split('-');
		var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
		
		//alert(last);return;
		var last_date = last.getDate();
		var month = last.getMonth()+1;
		var year = last.getFullYear();
		
		if(month<10)
		{
			var months='0'+month;
		}
		else
		{
			var months=month;
		}
		
		var last_full_date=last_date+'-'+months+'-'+year;
		var first_full_date='01'+'-'+months+'-'+year;
		
		$('#txt_date_from').val(first_full_date);
		$('#txt_date_to').val(last_full_date);
	}
	else
	{
		
		var thisDate=($('#txt_date_to').val()).split('-');
		var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
		
		//alert(last);return;
		var last_date = last.getDate();
		var month = last.getMonth()+1;
		var year = last.getFullYear();
		
		if(month<10)
		{
			var months='0'+month;
		}
		else
		{
			var months=month;
		}
		var last_full_date=last_date+'-'+months+'-'+year;
		$('#txt_date_to').val(last_full_date);
		
	}
	//$("#txt_applying_period_to_date").attr("disabled", "disabled");
	
}
	
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="100%" align="center">
<form id="consolid_order_1" name="consolid_order_1">
    <div style="width:1150px;">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1000px;">
                <table class="rpt_table" width="100%" cellpadding="1" cellspacing="2" align="center" rules="all">
                	<thead>
                    	<tr>                   
                            <th width="100">Company Name</th>
                            <th width="150">Buyer Name</th>
                            <th width="150">Team Leader</th>
                            <th width="150">Type</th>
                            <th colspan="2" class="must_entry_caption">Shipment Date</th>
                            <th width="80"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90%" onClick="reset_form('consolid_order_1','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td> 
                            <? 
                              echo create_drop_down( "cbo_company_name", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/consolidated_order_summary_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                         <td id="buyer_td" align="center">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 145, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td align="center">
                            <?  
                               echo create_drop_down( "cbo_team_leader", 145, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", 0, "",0 );  
                            ?>
                        </td>
                        
                        <td align="center">
                            <? 
								$report_po_date = array(1 => "Country Ship Date", 2 => "Public Ship Date", 3=>"Original Ship Date" );
                                echo create_drop_down( "cbo_date_type", 145, $report_po_date,"", 0, "-- Select Type --", 1, "",0,"" );
                            ?>
                        </td>
                        
                        
                         <td align="center">
                             <input id="txt_date_from" name="txt_date_from" class="datepicker" style="width:100px;" onChange="calculate_date(0)" placeholder="Select Date" readonly >
                         </td>
                         <td align="center">
                             <input id="txt_date_to" name="txt_date_to" class="datepicker" style="width:100px;" onChange="calculate_date(1)" placeholder="Select Date" readonly>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90%" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
