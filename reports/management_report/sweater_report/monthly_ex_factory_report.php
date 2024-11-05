<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Ex-Factory Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	02-01-2014
Updated by 		: 	Md. Shafiqul Islam Shafiq	
Update date		: 	15-02-2019	   
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
echo load_html_head_contents("Ex-Factory Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_org_po_quantity","total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value","total_buyer_basic_qnty"],
	   col: [2,3,4,5,6,7,8,9,11],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 var tableFilters2 =   
	 {
		col_33: "none",
		col_operation: {
		//id: ["total_po_qty","value_total_po_valu","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","value_total_eecess_storage_val"],
		id: ["total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","total_ex_per","value_cm_per_pcs_tot"],
	   //col: [19,21,22,23,24,25,26,27,28,29,30,31],
	   //col: [20,22,23,24,25,26,27,28,29,30,31,32],value_total_eecess_storage_val
	   col: [25,26,27,28,29,30,31,32,34,35,36],  
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 

	 var tableFilters5 =   
	 {
 	 	col_33: "none",
	 	col_operation: {		
	 		id: ["gr_order_fob_id","gr_ex_qnty_id","gr_ex_fob_id"],	    
	 		col: [12,13,14],  
	 		operation: ["sum","sum","sum","sum"],
	 		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML",]
	 	}
	 } //10, "gr_order_qnty_id",
	 
	/* var tableFilters2 = 
	 {
		col_33: "none",
		col_operation: {
		id: ["total_po_qty","value_total_po_valu","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","value_total_eecess_storage_val"],
	   col: [16,18,19,20,21,22,23,24,25,26,27],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } */
	 
	function chng_val(vall)
	{
		if(vall=1001)
		{
			if(form_validation('txt_date_from','Date From')==false)
				{
					if(form_validation('txt_date_to','Date From')==false)
					{
						return;
					}
				}
				
		}
		if(vall=1002)
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
				{
					return;
				}
		}
	}
	function fn_report_generated(type)
	{
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_delivery_company_name').val();
		var internal_ref=$('#txt_internal_ref').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();

		/*if(company==0 && del_company==0)
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
			{
				return;
			}
		}*/
		if(internal_ref !="")
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		else if(txt_date_from=="" || txt_date_to=="")
		{
			if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
			{
				return;
			}
		}
		else
		{
			
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
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
			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
			}
			if(reponse[2]==4)
			{				
				setFilterGrid("table_body",-1,tableFilters5);
 				$("#gr_order_qnty_id").css("text-align","center");
				$("#gr_order_fob_id").css("text-align","center");
				$("#gr_ex_qnty_id").css("text-align","center");
				$("#gr_ex_fob_id").css("text-align","center");

			}
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}
	function openmypage_ex_date(company_id,order_id,item_number_id,ex_factory_date,action,challan_id,type)
	{
		//alert (type);
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_ex_factory_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action+'&challan_id='+challan_id+'&item_number_id='+item_number_id+'&type='+type, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}
	function fnc_source(source,company)
	{
		load_drop_down( 'requires/monthly_ex_factory_report_controller', source+'**'+company, 'load_drop_delivery_company', 'dev_company_td' );
		set_multiselect('cbo_delivery_company_name','0','0','0','0');
	}
</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1710px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1710" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1710px;">
                <table class="rpt_table" width="1700" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150">Company Name</th>
                            <th width="100">Buyer</th>
                            <th width="100">Source</th>
                            <th width="150">Delivery Company</th>
                            <th width="120">Delivery Location</th>
                            <th width="120">Floor</th>
                            <th width="120">Internal Ref.</th>
                            <th width="105">Shipment Status</th>
                            <th width="300" class="must_entry_caption">Ex-Factory Date</th>
                            <th width="400"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							 <? 
	                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select --", $selected, "" );
	                         ?>	  
	                    </td>
	                    <td width="">
                    		<?
                    			echo create_drop_down( "cbo_source", 100, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_source(this.value,$('#cbo_company_name').val());", 0, '1,3' );
                    		?>
                    	</td>

                         <td id="dev_company_td"> 
                            <?
                                echo create_drop_down( "cbo_delivery_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Delivery Company --", $selected,"load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_location', 'location' );" );
                            ?>
                        </td>

                        


                        <td id="location">
							 <? 
	                            echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "-- Select --", $selected, "" );
	                         ?>	  
	                    </td>
                        
                        
                        <td id="del_floor_td">
                        <? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?>
                      </td>
                      <td>
                      	<input type="text" name="txt_internal_ref" id="txt_internal_ref" placeholder="Internal Ref." class="text_boxes">
                      </td>
                      <td id="shipment_status_td">
                      	 <?
                                  echo create_drop_down( "cbo_shipping_status", 105, $shipment_status,"", 1, "-- Select --", 0, "",0,'0,2,3','','','','' );	
                                         ?>   
                      </td>
                        
                        
                        
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:110px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Detail" onClick="fn_report_generated(1);" />
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Monthly" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Challan Wise" onClick="fn_report_generated(3)" />
                            <input type="button" id="show_button4" class="formbutton" style="width:90px" value="Country Wise" onClick="fn_report_generated(4)" />
                            <input type="button" id="show_button4" class="formbutton" style="width:90px" value="Country Wise 2" onClick="fn_report_generated(5)" />
                            <input type="button" id="show_button16" class="formbutton" style="width:90px" value="Detail MFG" onClick="fn_report_generated(16)" />
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
<script type="text/javascript">
	set_multiselect('cbo_delivery_company_name','0','0','0','0');
	$("#multi_select_cbo_delivery_company_name a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{  
		var company=$("#cbo_delivery_company_name").val(); 		 
		load_drop_down( 'requires/monthly_ex_factory_report_controller', company, 'load_drop_down_location', 'location' );
	}
</script>
</html>
