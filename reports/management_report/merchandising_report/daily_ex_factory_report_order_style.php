<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Daily Ex-Factory Report Order/Style wise
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	04-05-2021
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
echo load_html_head_contents("Cost Breakdown Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value","total_buyer_basic_qnty"],
	   col: [2,3,4,5,6,7,8,9],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	 var tableFilters2 = 
	 {
		col_34: "none",
		col_operation: {
		id: ["total_po_qty","value_total_po_valu","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","value_total_eecess_storage_val","value_cm_per_pcs_tot"],
	   col: [17,19,20,21,22,23,24,25,26,27,28,29,31],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	function fn_report_generated(type)
	{
		var txt_order 	= $("#txt_order").val();
		var txt_style 	= $("#txt_style").val();
		var txt_job 	= $("#txt_job").val();
		var txt_int_ref = $("#txt_int_ref").val();
		if(txt_style!="" || txt_order!="" || txt_job!="" || txt_int_ref!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
			{
				return;
			}
		}

		var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*txt_order*txt_style*txt_job*txt_int_ref*txt_date_from*txt_date_to*cbo_year',"../../../");
		freeze_window(3);
		http.open("POST","requires/daily_ex_factory_report_order_style_controller.php",true);
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
				setFilterGrid("table_body2",-1,tableFilters2);
			}
			if(reponse[2]==4)
			{
				setFilterGrid("table_body",-1);
				setFilterGrid("table_body2",-1);
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


	function fn_generate_print(button,sys_id,company,del_company,factory_date)
	{
		if(button==121){
			print_report( company +'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*2*../../*'+del_company, "ex_factory_print_new", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		}
		else if(button==122){
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*2*../../', "ex_factory_print_new2", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		}
		else if(button==123){
		 	print_report(company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*5*../../', "ExFactoryPrintSonia", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		} 
		else if(button==580)
		{		
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*6*../../', "ex_factory_print2", "../../../production/requires/garments_delivery_entry_controller" );
		 	return; 
		 }
		else if(button==169)
		{
		 	var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
		 	// alert(answer);
		 	var show_delv_info = (answer==true) ? 1 : 0;			
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*7*'+show_delv_info, "ex_factory_print_new3", "../../../production/requires/garments_delivery_entry_controller" );
		 	return; 
		 }
		 else if(button==758)
		{		
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*8*../../', "ex_factory_print_new7", "../../../production/requires/garments_delivery_entry_controller" );
		 	return; 
		 }
		else if(button==78)
		{
			 print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*1*../../', "ex_factory_print", "../../../production/requires/garments_delivery_entry_controller" ) ;
			 return;
		}
		else 
		{
			 print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*1*../../', "ex_factory_print", "../../../production/requires/garments_delivery_entry_controller" ) ;
			 return;
		}
	}

	function openmypage_ex_date(company_id,order_id,ex_factory_date,action)
	{
		//alert (order_id)
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_ex_factory_report_order_style_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		// alert(company);
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/daily_ex_factory_report_order_style_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{

			if(report_id[k]==250)
			{
				$('#button_data_panel')
					.append( '<input type="button" id="show_button" class="formbutton" style="width:60px" value="Detail" onClick="fn_report_generated(1)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==282)
			{
				$('#button_data_panel').append( '<input type="button" id="show_button" class="formbutton" style="width:60px" value="Detail2" onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
			}		
		
		}
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1100px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1080px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1080px;">
                <table class="rpt_table" width="1050" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="100" >Job No.</th>
                            <th width="100" >Job Year</th>
                            <th width="100" >Order No.</th>
                            <th width="100" >Style Ref.</th>
                            <th width="100" >Int. Ref.</th>
                            <th width="160" class="must_entry_caption">Ex-Factory Date</th>
                            <th width="140"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting(this.value);" );
                            ?>
                        </td>
                        <td> 
							<input type="text" name="txt_order" id="txt_job" placeholder="Job No" class="text_boxes" style="width:80px">
                        </td>
						<td>
							<?  
							echo create_drop_down( "cbo_year", 90, create_year_array(),'', 1, '-- All Year--', 0,'',0);
							?>
						</td>
                        <td> 
							<input type="text" name="txt_order" id="txt_order" placeholder="Order No" class="text_boxes" style="width:80px">
                        </td>
                        <td> 
							<input type="text" name="txt_style" id="txt_style" placeholder="Style Ref No" class="text_boxes" style="width:80px" >
                        </td>
                        <td> 
							<input type="text" name="txt_style" id="txt_int_ref" placeholder="Int. Ref." class="text_boxes" style="width:80px" >
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        
						<td id="button_data_panel" align="center"> </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Monthly" onClick="fn_report_generated(2)" />
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
