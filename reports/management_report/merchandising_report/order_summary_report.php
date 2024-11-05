<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Order Summary Report
Functionality	:
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	23-11-2020
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
echo load_html_head_contents("Order Summary Report","../../../", 1, 1, $unicode,1,1);
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
		var show_value = 0;
		if(confirm("Press Ok for with value or Press Cancel for without value."))
		{
			show_value = 1;
		}
		
		if(type==1){
			if(form_validation('cbo_company_name*cbo_date_type*cbo_from_year*cbo_from_month*cbo_to_year*cbo_to_month*cbo_report_type','Company Name*Date Type*From Year*From Month*To Year*To Month*Report Type')==false)
			{
				return;
			}
		}
		else{
		if(form_validation('cbo_date_type*cbo_from_year*cbo_from_month*cbo_to_year*cbo_to_month','Date Type*From Year*From Month*To Year*To Month')==false)
			{
				return;
			}
		}
		var action="";
		if(type==3){
			action="report_order_details";
		}	
		else{
			action="report_generate_powise";
		}
		
		var data="action="+action+"&reportType="+type+"&show_value="+show_value+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_buyer_ref*cbo_sub_department*cbo_shipping_status*cbo_order_status*cbo_date_type*cbo_from_year*cbo_from_month*cbo_to_year*cbo_to_month*cbo_report_type',"../../../");
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/order_summary_report_controller.php",true);
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
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:120px"/>&nbsp;&nbsp;<input type="button" onclick="new_window2('+reponse[2]+',1)" value="1st Print Preview" name="Print" class="formbutton" style="width:120px"/>&nbsp;&nbsp;<input type="button" onclick="new_window2('+reponse[2]+',2)" value="2nd Print Preview" name="Print" class="formbutton" style="width:120px"/>&nbsp;&nbsp;<input type="button" onclick="new_window2('+reponse[2]+',3)" value="3rd Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	function new_window(type)
	{
		if (type==2)
		{
			document.getElementById('scroll_body_1').style.overflow="auto";
			document.getElementById('scroll_body_1').style.maxHeight="none";
		}

		document.getElementById('scroll_body_2').style.overflow="auto";
		document.getElementById('scroll_body_2').style.maxHeight="none";

		document.getElementById('scroll_body_3').style.overflow="auto";
		document.getElementById('scroll_body_3').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body_1').style.overflowY="scroll";
		document.getElementById('scroll_body_1').style.maxHeight="350px";

		document.getElementById('scroll_body_2').style.overflowY="scroll";
		document.getElementById('scroll_body_2').style.maxHeight="350px";

		document.getElementById('scroll_body_3').style.overflowY="scroll";
		document.getElementById('scroll_body_3').style.maxHeight="350px";
	}

	function new_window2(rptType,rptFormat)
	{
		var report_container = "";
		if (rptType==2 && rptFormat==1)
		{
			document.getElementById('scroll_body_1').style.overflow="auto";
			document.getElementById('scroll_body_1').style.maxHeight="none";
			report_container = "1st_part";
		}
		else if(rptFormat==1)
		{
			report_container = "1st_part";
		}

		if (rptFormat==2)
		{
			document.getElementById('scroll_body_2').style.overflow="auto";
			document.getElementById('scroll_body_2').style.maxHeight="none";
			report_container = "2nd_part";
		}
		if (rptFormat==3)
		{
			document.getElementById('scroll_body_3').style.overflow="auto";
			document.getElementById('scroll_body_3').style.maxHeight="none";
			report_container = "3rd_part";
		}

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById(report_container).innerHTML+'</body</html>');
		d.close();

		if (rptType==2 && rptFormat==1)
		{
			document.getElementById('scroll_body_1').style.overflowY="scroll";
			document.getElementById('scroll_body_1').style.maxHeight="350px";
		}
		if (rptFormat==2)
		{
			document.getElementById('scroll_body_2').style.overflowY="scroll";
			document.getElementById('scroll_body_2').style.maxHeight="350px";
		}
		if (rptFormat==3)
		{
			document.getElementById('scroll_body_3').style.overflowY="scroll";
			document.getElementById('scroll_body_3').style.maxHeight="350px";
		}

		/*document.getElementById('scroll_body_1').style.overflowY="scroll";
		document.getElementById('scroll_body_1').style.maxHeight="350px";

		document.getElementById('scroll_body_2').style.overflowY="scroll";
		document.getElementById('scroll_body_2').style.maxHeight="350px";

		document.getElementById('scroll_body_3').style.overflowY="scroll";
		document.getElementById('scroll_body_3').style.maxHeight="350px";*/
	}

	function openPopup(param)
	{
		var popup_width='850px';
		var action = "ex_factory_popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_summary_report_controller.php?data='+param+'&action='+action, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_source(source,company)
	{
		load_drop_down( 'requires/order_summary_report_controller', source+'**'+company, 'load_drop_delivery_company', 'dev_company_td' );
		set_multiselect('cbo_delivery_company_name','0','0','0','0');
	}
	
	function fn_load_buyer()
	{
		load_drop_down( 'requires/order_summary_report_controller', $('#cbo_company_name').val(), 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_name','0','0','','0','');
		// set_multiselect('cbo_buyer_name','0','0','','0','fn_load_sub_dep();fn_load_buyer_ref();');
	}	
	
	function fn_load_sub_dep()
	{
		load_drop_down( 'requires/order_summary_report_controller', $('#cbo_buyer_name').val(), 'load_drop_down_sub_dep', 'sub_department_td' );
		set_multiselect('cbo_sub_department','0','0','','0','');
	}		
	
	function fn_load_buyer_ref()
	{
		load_drop_down( 'requires/order_summary_report_controller', $('#cbo_buyer_name').val(), 'load_drop_down_buyer_ref', 'buyer_ref_td' );
		set_multiselect('cbo_buyer_ref','0','0','','0','');
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<form id="monthly_ex_factory" name="monthly_ex_factory">
		    <div style="width:1560px;" align="center">
		        <? echo load_freeze_divs ("../../../"); ?>
		        <h3 align="left" id="accordion_h1" style="width:1560px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		        <div id="content_search_panel">
		            <fieldset style="width:1560px;">
		                <table class="rpt_table" width="1560" cellpadding="1" cellspacing="2" align="center">
		                	<thead>
		                    	<tr>
		                            <th width="120" class="must_entry_caption">Company</th>
		                            <th width="120">Buyer</th>
		                            <th width="120">Buyer Ref.</th>
		                            <th width="120">Sub Department</th>
		                            <th width="120">Shipment Status</th>
									<th width="70">Order Status</th>
		                            <th width="120" class="must_entry_caption">Date Type</th>
		                            <th width="100" class="must_entry_caption">From Year</th>
		                            <th width="100" class="must_entry_caption">From Month</th>
		                            <th width="100" class="must_entry_caption">To Year</th>
		                            <th width="100" class="must_entry_caption">To Month</th>
		                            <th width="120" class="must_entry_caption">Report Type</th>
		                            <th width="100" colspan="2"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
		                        </tr>
		                    </thead>
		                    <tbody>
			                    <tr class="general">
			                        <td>
			                            <?
			                            echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"fn_load_buyer();" );
			                            ?>
			                        </td>
			                        <td id="buyer_td">
										<?
				                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 0, "-- Select --", $selected, "" );
				                        ?>
				                    </td>
			                         <td id="buyer_ref_td">
			                            <?
			                            $sql = "select distinct EXPORTERS_REFERENCE from lib_buyer  where status_active =1 and is_deleted=0 $company_cond and exporters_reference is not null order by exporters_reference";
			                            $res = sql_select($sql);
			                            $ref_array = array();
			                            foreach ($res as $val) 
			                            {
			                            	$ref_array[$val['EXPORTERS_REFERENCE']] = $val['EXPORTERS_REFERENCE'];
			                            }

			                            echo create_drop_down( "cbo_buyer_ref", 120, $ref_array,"", 0, "-- Select --", $selected,"" );
			                            ?>
			                        </td>
			                        <td id="sub_department_td">
										<?
				                        echo create_drop_down( "cbo_sub_department", 120, "select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name","", 0, "-- Select --", $selected, "" );
				                        ?>
				                    </td>
			                      <td id="shipment_status_td">
			                      	 	<?
										$search = 'Partial Delivery';
										$replace = 'Running';
										array_walk($shipment_status, function (&$v) use ($search, $replace)
										{
											$v = str_replace($search, $replace, $v);    
										}                                                                     
										);  
										// print_r($array);
			                           	echo create_drop_down( "cbo_shipping_status", 120, $shipment_status,"", 0, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
								  <td>
									  <?=create_drop_down( "cbo_order_status", 70, $order_status,"", 1,"-- All --", $selected, "",0,"" ); ?>
								  </td>
			                      <td id="date_type_td">
			                      	 	<?
			                      	 	$date_type = array(1 =>"Shipment Date" ,2=>"ETD/LDD",3=>"Original Ship Date" , 4=>"Country Ship Date", 5=>"PO Insert Date");
			                           	echo create_drop_down( "cbo_date_type", 120, $date_type,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
			                      <td id="from_year_td">
			                      	 	<?
			                           	echo create_drop_down( "cbo_from_year", 100, $year,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
			                      <td id="from_month_td">
			                      	 	<?
			                           	echo create_drop_down( "cbo_from_month", 100, $months,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
			                      <td id="to_year_td">
			                      	 	<?
			                           	echo create_drop_down( "cbo_to_year", 120, $year,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>

			                      <td id="to_month_td">
			                      	 	<?
			                           	echo create_drop_down( "cbo_to_month", 120, $months,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
			                      <td id="report_type_td">
			                      	 	<?
			                      	 	$report_type = array(1 =>"Sub Dept wise" ,2=>"Buyer wise", 3=>"Prod Dept");
			                           	echo create_drop_down( "cbo_report_type", 120, $report_type,"", 1, "-- Select --", 0, "",0,'','','','','' );
			                            ?>
			                      </td>
			                        <td>
			                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" />
										<input type="button" id="show_button" class="formbutton" style="width:100px" value="All Company" onClick="fn_report_generated(2);" />
										<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(3);" />

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
	    <div id="report_container" align="center" style="padding: 10px 0;"></div>
	    <div id="report_container2"></div>
	    <div style="display:none" id="data_panel"></div>
 	</div>
</body>

<script type="text/javascript">
	set_multiselect('cbo_buyer_name','0','0','0','0');
	set_multiselect('cbo_buyer_ref','0','0','0','0');
	set_multiselect('cbo_sub_department','0','0','0','0');
	set_multiselect('cbo_shipping_status','0','0','0','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
