<?
/*-------------------------------------------- Comments
Purpose			: 	This report will create Daily Production Monitoring Report Based on Ship Date Wise Report
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	15-09-2020
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
echo load_html_head_contents("Daily Production Monitoring Report Based on Ship Date Wise  Report","../../", 1, 1, $unicode,1,''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	/*var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_sub_total_booking_quantity","value_sub_total_rcv","value_sub_total_rcv_balance","value_sub_total_issue","value_sub_total_stock_qnty","value_total_transfe_in_yds"],
		col: [10,11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}*/
	
	function generate_report(rpt_type)
	{
		
		if( form_validation('cbo_company_id*txt_ship_date_from*txt_ship_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
	
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_workingcompany_id*txt_ship_date_from*txt_ship_date_to*txt_po_date_from*txt_po_date_to*cbo_order_status*cbo_shipment_status*cbo_date_category*cbo_report_type',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/daily_production_monitoring_report_based_on_shipdate_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(reponse[2]);
			if(reponse[2]==1)
			{
				//setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				//setFilterGrid("table_body",-1,tableFilters3);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').show();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}


	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="shipdatewise_1" id="shipdatewise_1" autocomplete="off" > 
    <h3 style="width:1230px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1230px;">
                <table class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                        	<th width="140">Buyer</th>                           
                        	<th width="140">Working Company</th>                           
                        	<th width="140" class="must_entry_caption">Ship Date Range</th>                           
                        	<th width="140">PO Rcvd Date Range</th>                           
                            <th width="100">Order Status</th>
                            <th width="100">Shipment Status</th>
                            <th width="100">Date Category</th>
                            <th width="100">Report Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('shipdatewise_1','report_container*report_container2','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_production_monitoring_report_based_on_shipdate_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td id="td_workingcompany">
                            <? 
                               echo create_drop_down( "cbo_workingcompany_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Working Company--", 0, "",0 );
                            ?>                            
                        </td>
                        <td>
                            <input type="text" name="txt_ship_date_from" id="txt_ship_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly/> To                             
                            <input type="text" name="txt_ship_date_to" id="txt_ship_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;"  readonly/>				
                        </td>
                         <td>
                            <input type="text" name="txt_po_date_from" id="txt_po_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly/> To                             
                            <input type="text" name="txt_po_date_to" id="txt_po_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;"  readonly/>				
                        </td>
                        <td> 
                            <?
								$order_status = array(0 => "All",1 => "Confirmed", 2 => "Projected");
                                echo create_drop_down( "cbo_order_status", 90, $order_status,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_shipment_status", 90, $shipment_status,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$report_date_catagory = array(1 => "Country Ship Date Wise", 2 => "Ship Date Wise", 3 => "Org. Ship Date Wise", 4 => "PO Insert Date Wise", 5=> "Extended Ship Date");
                                echo create_drop_down( "cbo_date_category", 90, $report_date_catagory,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$report_type_arr = array(0 => "Order Wise Details");
                                echo create_drop_down( "cbo_report_type", 90, $report_type_arr,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                    
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                        </td>
                    </tr>
                    <!-- <tr>
                    	<td colspan="13" align="center"><? //echo load_month_buttons(1);  ?></td>
                    </tr> -->
                </table> 
            </fieldset> 
        </div>
             
    </form>    
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>      
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
