<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Item Group Wise Production Status Report
Functionality	:	
JS Functions	:
Created by		:	KAMRUL SHEIKH
Creation date 	: 	20-9-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Code is poetry, I try to do that!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Group Wise Production Status Report", "../../", 1, 1,$unicode,1,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["gr_order_qty","gr_cut_qty","gr_cut_bal","gr_input_qty","gr_input_bal","gr_order_input_bal"],
			col: [9,10,11,12,13,14],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	

	function fn_generate_report(type)
	{
		var garments_item = document.getElementById('cbo_garments_item').value;
		
		var buyer_name = document.getElementById('cbo_buyer_name').value;
		// alert(buyer_name);
		 
		if(garments_item=="" && buyer_name=="")
        {        
        	if (form_validation('cbo_company_name*cbo_year*txt_date_from*txt_date_to','Company Name*Year*From Date*To Date')==false)
			{
				alert('Please Select Company and Year');
				return;
			}
        }
		else
		{
			if (form_validation('cbo_company_name*cbo_year','Company Name*Year')==false)
			{
				return;
			}
		}	
		 
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_garments_item*cbo_order_status*cbo_shipment_status*cbo_year*txt_date_from*txt_date_to',"../../")+'&type='+type;
		 
		freeze_window(3);
		http.open("POST","requires/item_group_wise_production_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	show_msg('3');
			 release_freezing(); 
			 var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="All" class="formbutton" style="width:120px"/>'; 
			
			
		}
	} 

	function new_window()
	{
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 

			//$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="425px";
		
	} 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}	 
	 
	
	function exportToExcel()
	{
		// $(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    var data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		var ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
	    document.getElementById("dlink").click();
		// $(".fltrow").show();
		// alert('ok');
	}
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1100px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionWipReport_1">    
      <fieldset style="width:1080px;">
            <table class="rpt_table" width="1080px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                   <tr>
                        <th class="must_entry_caption" width="130" >Company</th>
                        <th width="130">Buyer</th>
						<th width="100">Item Group</th>
						<th width="80">Order Status</th>                      
						<th width="120">Shipment Type</th>
						<th width="80" class="must_entry_caption">Year</th>
						<th class="must_entry_caption"  width="200">Shipment Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('dateWiseProductionWipReport_1','report_container','','','')" /></th>
                    </tr>   
              </thead>
                <tbody>
					<tr class="general">
						<td align="center"> 
							<?
								echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/item_group_wise_production_status_report_controller', this.value, 'load_drop_down_buyer', 'td_buyer' );" );
							?>
						</td>
						<td align="center" id="td_buyer"> 
							<?
								echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-Select Buyer-", $selected, "",1,"" );
							?>
					
						</td>
						<td align="center" id="garments_item"> 
							<?
							echo create_drop_down( "cbo_garments_item", 110, $garments_item,"", 1, "-Select item-", $selected, "",0,"" );
							?>
						</td>					
						<td align="center">
										<?
							$order_status = array(0 => "ALL Status", 1 => "Projected", 2 => "Confirmed",3 => "Inactive",4=> "Cancel");
							echo create_drop_down("cbo_order_status", 80, $order_status, "", 0, "", 0, "", "");
							?>
						</td>					
						<td align="center">
							<?
							
								echo create_drop_down("cbo_shipment_status", 120, $shipment_status, "", 1,"All-Type", 0, "", "");
							?>
						</td>
						<td align="center" id="garments_item"> 
							<?
								echo create_drop_down( "cbo_year", 80, $year,"",1, "-- All Year --", $selected ,date("Y",time()), "0" );
							?>
						</td> 
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date">
						</td>    
						<td>
							<input type="button" id="item_group" class="formbutton" style="width:100px" value="Item Group Wise" onClick="fn_generate_report(1)" />      
							
							<input type="button" id="month_wise" class="formbutton" style="width:90px" value="Month Wise" onClick="fn_generate_report(2)" />       
							
						</td>
						
						
					</tr>
					<tr>
						<td colspan="7" align="center">
							<? echo load_month_buttons(1); ?>
						</td> 
					</tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
