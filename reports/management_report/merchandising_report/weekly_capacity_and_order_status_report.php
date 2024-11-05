<?
/*-------------------------------------------- Comments
Purpose			: 	This report will create Weekly Capacity and Order Status		
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA
Creation date 	: 	02-03-2020
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

echo load_html_head_contents("Weekly Capacity and Order Status","../../../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	 var tableFilters = {
			 	 //col_10:'none',
				 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["total_po_minute","total_po_qty","total_po_value"],
					col: [17,18,20],
					operation: ["sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				}
				 var tableFilters2 = {
			 	 //col_10:'none',
				 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["total_po_minute","total_po_qty","total_po_value"],
					col: [19,20,22],
					operation: ["sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				}
	 
	function fn_report_generated(type)
	{
		if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			if(type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_lc_company_id*cbo_lc_location_id*cbo_date_cat_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
			}

			freeze_window(3);
			http.open("POST","requires/weekly_capacity_and_order_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
			var date_cat_id=document.getElementById('cbo_date_cat_id').value;
			if(date_cat_id==1 || date_cat_id==3)
			{
				setFilterGrid("table-body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table-body",-1,tableFilters2);
			}
	 		show_msg('3');
			release_freezing();
		}
	}
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		 
		$("#table-body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table-body tr:first").show();
	}
	
</script>


</head>
<body onLoad="set_hotkey()">
   <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form id="monthlyCapacityBuyerWiseBooked_1" name="monthlyCapacityBuyerWiseBooked_1">
            <h3 align="left" id="accordion_h1" class="accordion_h" style="width:950px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:950px" align="center" >
                <fieldset>  
                    <table cellpadding="0" cellspacing="2" width="930" class="rpt_table" border="1" rules="all">
                        <thead>  
                            <tr>
                                <th width="150">Working Company</th>
                                <th width="150">WC Location</th>
                                <th width="150">LC Company</th>
                                <th width="150">LC Com. Location</th>
								<th width="100">Date Category</th>
                                <th class="must_entry_caption"  width="80">From Date</th>
                                <th class="must_entry_caption" width="100">To Date</th>
                                <th colspan="3">
                                <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyCapacityBuyerWiseBooked_1','report_container*report_container2','','','');" />
                                </th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "--Select Company--", $selected,"" ); ?></td>
                                <td id="wc_location_td">
									<? echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",0,"" ); ?>
                                </td>
                                <td><? echo create_drop_down( "cbo_lc_company_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "--Select Company--", $selected," " ); ?></td>
                                <td id="lc_location_td">
									<? echo create_drop_down( "cbo_lc_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",0,"" ); ?>
                                </td>
								 <td>
									<? 
									$date_category_arr=array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date'); //
									echo create_drop_down( "cbo_date_cat_id", 100, $date_category_arr,"", 1, "-- Select --", 1, "",0,"" ); ?>
                                </td>
								
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>

                                <td><input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1)" style="width:80px" class="formbutton" /></td>
                            </tr>
                            <tr>
                                <td colspan="8" align="center">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                         </tbody>
                    </table>
                   
                </fieldset>
            </div>
			 <div id="report_container" align="center"></div>
    		<div id="report_container2"></div>
        </form>    
    </div>
   
</body>
<script>
	set_multiselect('cbo_company_id','0','0','','0');
	set_multiselect('cbo_lc_company_id','0','0','','0');
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_lc_location_id','0','0','','0');
		
	$("#multiselect_dropdown_table_headercbo_company_id").click(function(){
		var wcCompany=$("#cbo_company_id").val();
		load_drop_down( 'requires/weekly_capacity_and_order_status_report_controller',wcCompany, 'load_drop_down_location', 'wc_location_td' );
		set_multiselect('cbo_location_id','0','0','','0');
	});
	
	$("#multiselect_dropdown_table_headercbo_lc_company_id").click(function(){
		var lcCompany=$("#cbo_lc_company_id").val();
		load_drop_down( 'requires/weekly_capacity_and_order_status_report_controller', lcCompany, 'lc_load_drop_down_location', 'lc_location_td' );
		set_multiselect('cbo_lc_location_id','0','0','','0');
	});

</script>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>