<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Consolidated Ex-Factory Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	23-07-2016
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
echo load_html_head_contents("Consolidated Ex-Factory Report","../../../", 1, 1, $unicode,0,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	/*var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value","total_buyer_basic_qnty"],
	   col: [2,3,4,5,6,7,8,9],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } */
	 
	function fn_report_generated(type)
	{
		if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
		else
		{
			
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_date_category*txt_date_from*txt_date_to',"../../../");
			freeze_window(3);
			http.open("POST","requires/consolidate_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		
		//$('#table_body tr:first').hide();
		//$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//$('#table_body tr:first').show();
		//$('#table_body2 tr:first').show();
		
		
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	function openmypage_ex_date(company_id,order_id,ex_factory_date,action)
	{
		//alert (order_id)
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/consolidate_ex_factory_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1000px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="790" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="120">Company Name</th>
                            <th width="120">Buyer Name</th>
							<th width="120">Date Category</th>
                            <th width="200" class="must_entry_caption">Shipment Date</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/consolidate_ex_factory_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected );
                            ?>
                        </td>
						<td>
                        	<? 
								$report_po_date = array(1 => "Country Ship Date Wise", 2 => "Public Ship Date Wise", 3 => "Original Ship Date");
                                echo create_drop_down( "cbo_date_category", 120, $report_po_date,"", 1, "--Select--", 3,"" );
                            ?>	
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Quantity Wise" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Value Wise" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                    </tbody>
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
