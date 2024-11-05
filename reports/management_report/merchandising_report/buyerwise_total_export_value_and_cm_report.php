<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Ex-Factory Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	05-06-2017
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
echo load_html_head_contents("Statement of Total Export Value & Report","../../../", 1, 1, $unicode,1,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_po_qty","value_total_ex_fac_qty","value_total_short_excess","value_total_short_excess_val","value_total_fob","value_total_ex_fac_qty_cm_cost_mergin","value_total_ex_fac_outbound_qty"],
	   col: [2,3,4,5,6,7,8],
	   operation: ["sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	
	 
	function fn_report_generated(type)
	{
	
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
			{
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_catgory*txt_date_from*txt_date_to*cbo_template_id',"../../../")+'&report_title='+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/buyerwise_total_export_value_and_cm_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	function openmypage_ex_date(company_id,order_id,ex_factory_date,action)
	{
		//alert (order_id)
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyerwise_total_export_value_and_cm_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="statement_of_total_export_value" name="statement_of_total_export_value">
    <div style="width:900px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150" class="must_entry_caption">Company Name</th>
                           
                            <th width="150">Buyer Name</th>
                            <th width="150">Product Category</th>
                            <th width="200" class="must_entry_caption">Shipment Date</th>
                            <th width="200"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/buyerwise_total_export_value_and_cm_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );;" );
                            ?>
                        </td>
                         
                        <td id="buyer_td">
							 <? 
	                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select --", $selected, "",1 );
	                         ?>	  
	                    </td>
                            <td>
                                <? 
	                           echo create_drop_down("cbo_item_catgory", 150, $product_category, "", 0, "", $selected, "");
                               ?>	
                            </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"  disabled>&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" disabled></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="View" onClick="fn_report_generated(1);" />
                            
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, ""); ?>
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
    <script>
        set_multiselect('cbo_item_catgory','0','0','','');
    </script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
