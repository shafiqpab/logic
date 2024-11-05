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
	
	
	if(type==1)
	{
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_po_qty","value_total_ex_fac_qty","value_total_ex_fac_val","value_total_ex_fac_cm_cost","value_total_ex_fac_qty_mergin","value_total_ex_fac_qty_cm_cost_mergin"],
	   col: [7,11,12,13,14,15],
	   operation: ["sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	}
	else if (type==2)
	{
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_ex_fac_qty","value_total_ex_fac_val","value_total_ex_fac_cm_cost","value_total_ex_fac_qty_mergin","value_total_ex_fac_qty_cm_cost_mergin"],
	   col: [1,2,3,4,5],
	   operation: ["sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	}
	 
	function fn_report_generated(type)
	{
		var pre_cost_class=document.getElementById('cbo_pre_cost_class').value;
			
			
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
			{
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			freeze_window(3);
			
			if(type==3 || type==4){
				var data="action=report_generate_3&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_catgory*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
				if(pre_cost_class==1)
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller_v1.php",true);
				}
				else
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller.php",true);
				}
			}
			else if(type==5){
				var data="action=report_generate_5&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_catgory*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
				if(pre_cost_class==1)
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller_v1.php",true);
				}
				else
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller.php",true);
				}
			}
			else if(type==6){
				var data="action=report_generate_6&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_catgory*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
				if(pre_cost_class==1)
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller_v1.php",true);
				}
				else
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller.php",true);
				}
			}
			else{
				var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_catgory*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
				
				if(pre_cost_class==1)
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller_v1.php",true);
				}
				else
				{
					http.open("POST","requires/statement_of_total_export_value_and_cm_report_controller.php",true);
				}
			}
			
			
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
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}
	
	function new_window(type)
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		if(type!=6){
			$('#table_body tr:first').hide();
			$('#table_body2 tr:first').hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type!=6){
			$('#table_body tr:first').show();
			$('#table_body2 tr:first').show();
		}
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	function openmypage_ex_date(company_id,order_id,ex_factory_date,action)
	{
		//alert (order_id)
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/statement_of_total_export_value_and_cm_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	
	function open_exfactory_qty_dtls(job_no)
	{
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/statement_of_total_export_value_and_cm_report_controller.php?action=ex_factory_qty_pcs_dtls&job_no='+job_no, "Ex-Factory Qty Details", 'width=600px, height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	
	
	function open_actual_cost_dtls(job_no)
	{
		var budget_version = $("#cbo_pre_cost_class").val();
		var cbo_company_name = $("#cbo_company_name").val();
		
			if(budget_version==1)
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/statement_of_total_export_value_and_cm_report_controller.php?action=actual_cost_dtls&job_no='+job_no+'&cbo_company_name='+cbo_company_name, "Actual Cost Details", 'width=1600px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
			else
			{
			    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/statement_of_total_export_value_and_cm_report_controller.php?action=actual_cost_dtls&job_no='+job_no+'&cbo_company_name='+cbo_company_name, "Actual Cost Details", 'width=1600px, height=400px, center=1, resize=0, scrolling=0', '../../');
			}
		
		
		
		
	}
	
	
	
	function generate_link(type,txt_job_no)
	{

			freeze_window(3);
				var rate_amt=2; var zero_val='';
				if(type!='mo_sheet')
				{
					var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
				}
				var excess_per_val="";
				if(type=='mo_sheet')
				{
					excess_per_val = prompt("Please enter your Excess %", "0");
					if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
					
				}

				if (r==true) zero_val="1"; else zero_val="0";
				
				//var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*cbo_costing_per*print_option_id',"../../");
				
				
				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+txt_job_no+"'&cbo_company_name="+$("#cbo_company_name").val();


				freeze_window(3);
				http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_link_reponse;
		
		
	}
	
	
	function generate_link_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
		}
	}

	
	
</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
	<form id="statement_of_total_export_value" name="statement_of_total_export_value">
    <div style="width:1000px;" align="center">
        	<? echo load_freeze_divs ("../../../"); ?>
         	<h3 align="left" id="accordion_h1" style="width:100%;" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:100%;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Product Category</th>
                            <th colspan="3" class="must_entry_caption">Ex-Factory Date</th>
                            <th>Pre Cost By</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/statement_of_total_export_value_and_cm_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/statement_of_total_export_value_and_cm_report_controller');" );
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
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" ></td>
                        <td>To</td>
                        <td>
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px"  placeholder="To Date" ></td>
                        
                        <td>
                            <? 
								$pre_cost_class_arr = array(1=>'Pre Cost V1',2=>'Pre Cost V2');
								echo create_drop_down( "cbo_pre_cost_class", 100, $pre_cost_class_arr,"",0, "--Select--", 2,"",0 );
                           ?>	
                        </td>
                        
                        <td>
                            <input type="button" id="show_button_1" class="formbutton" style="width:80px; display:none;" value="Show" onClick="fn_report_generated(1);" />
                            
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center">
                            <? echo load_month_buttons(1); ?>
                            <input type="button" id="show_button_3" class="formbutton" style="width:80px; display:none;" value="Show 2" onClick="fn_report_generated(3);" />
                            <input type="button" id="show_button_2" class="formbutton" style="width:80px; display:none;" value="Summary" onClick="fn_report_generated(2);" />
                            <input type="button" id="show_button_4" class="formbutton" style="width:80px; display:none;" value="Summary 2" onClick="fn_report_generated(4);" />
                            <input type="button" id="show_button_5" class="formbutton" style="width:80px; " value="Cost Anlys" onClick="fn_report_generated(5);" />
                            <input type="button" id="show_button_5" class="formbutton" style="width:80px; " value="Cost Anlys 2" onClick="fn_report_generated(6);" />
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
    <script>
        set_multiselect('cbo_item_catgory','0','0','','');
    </script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
