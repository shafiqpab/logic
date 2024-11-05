<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Ex-Facorty Details Report
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq	
Creation date 	: 	03-03-2019
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
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();

		
		if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
		{
			return;
		}				
		else
		{
			
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_shipping_status*cbo_location_name*txt_date_from*txt_date_to*cbo_buyer_name',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/ex_factory_details_report_controller.php",true);
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

	function fn_monthly_report_generated(type)
	{
		var company=$('#cbo_company_name').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();

		
		if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
		{
			return;
		}				
		else
		{
			
			var data="action=report_generate_monthly&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_shipping_status*cbo_location_name*txt_date_from*txt_date_to*cbo_buyer_name',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/ex_factory_details_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_monthly_report_generated_reponse;
		}
		
	}
		
	function fn_monthly_report_generated_reponse()
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

	function openPopup(param)
	{		
		var popup_width='850px';
		var action = "ex_factory_popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/ex_factory_details_report_controller.php?data='+param+'&action='+action, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_source(source,company)
	{
		load_drop_down( 'requires/ex_factory_details_report_controller', source+'**'+company, 'load_drop_delivery_company', 'dev_company_td' );
		set_multiselect('cbo_delivery_company_name','0','0','0','0');
	}
	 
</script>
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<form id="monthly_ex_factory" name="monthly_ex_factory">
		    <div style="width:1100px;" align="center">
		        <? echo load_freeze_divs ("../../../"); ?>
		        <h3 align="left" id="accordion_h1" style="width:1710" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		        <div id="content_search_panel"> 
		            <fieldset style="width:1100px;">
		                <table class="rpt_table" width="1090" cellpadding="1" cellspacing="2" align="center">
		                	<thead>
		                    	<tr>                   
		                            <th width="150" class="must_entry_caption">LC Company</th>
		                            <th width="100">Buyer</th>
		                            <th width="150">Delivery Company</th>
		                            <th width="120">Delivery Location</th>
		                            <th width="105">Shipment Status</th>
		                            <th width="300" class="must_entry_caption">Ex-Factory Date</th>
		                            <th width="130"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
		                        </tr>
		                    </thead>
		                    <tbody>
			                    <tr class="general">
			                        <td> 
			                            <?
			                            echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected,"load_drop_down( 'requires/ex_factory_details_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
			                            ?>
			                        </td>
			                        <td id="buyer_td">
										<? 
				                        echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select --", $selected, "" );
				                        ?>	  
				                    </td>
			                         <td id="dev_company_td"> 
			                            <?
			                            echo create_drop_down( "cbo_delivery_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Delivery Company --", $selected,"load_drop_down( 'requires/ex_factory_details_report_controller', this.value, 'load_drop_down_location', 'location' );" );
			                            ?>
			                        </td> 
			                        <td id="location">
										<? 
				                        echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 0, "-- Select --", $selected, "" );
				                        ?>	  
				                    </td>
			                      <td id="shipment_status_td">
			                      	 	<?
			                           	echo create_drop_down( "cbo_shipping_status", 105, $shipment_status,"", 0, "-- Select --", 0, "",0,'0,2,3','','','','' );	
			                            ?>   
			                      </td>
			                        <td>
			                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From Date" >&nbsp; To
			                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:110px"  placeholder="To Date" >
			                        </td>
			                        <td>
			                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" />
										<input type="button" id="show_button" class="formbutton" style="width:50px" value="Monthly" onClick="fn_monthly_report_generated(1);" />
			                           
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
	set_multiselect('cbo_company_name','0','0','0','0');	
	$("#multi_select_cbo_company_name a").click(function(){load_buyer();});

	function load_buyer()
	{  
		var company=$("#cbo_company_name").val(); 		 
		load_drop_down( 'requires/ex_factory_details_report_controller', company, 'load_drop_down_buyer', 'buyer_td' );
	}
	//=======================================================================
	set_multiselect('cbo_delivery_company_name','0','0','0','0');
	$("#multi_select_cbo_delivery_company_name a").click(function(){load_buyer_location();});

	function load_buyer_location()
	{  
		var company=$("#cbo_delivery_company_name").val(); 		 
		load_drop_down( 'requires/ex_factory_details_report_controller', company, 'load_drop_down_location', 'location' );
		set_multiselect('cbo_location_name','0','0','0','0');
	}
	//========================================================================
	set_multiselect('cbo_location_name','0','0','0','0');
	set_multiselect('cbo_shipping_status','0','0','0','0');
</script>
</html>
