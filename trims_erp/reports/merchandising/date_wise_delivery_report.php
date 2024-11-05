<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Md.Mahbubur Rahman
Creation date 	: 	22-07-2019
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
echo load_html_head_contents("Date Wise Production Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters2 = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_delevery_qty","value_tot_delevery_valu_usd","value_tot_delevery_valu_usd2","value_tot_bill_amount_usd","value_tot_bill_balance"],
		col: [21,22,24,26,27],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters3 = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_order_del_qty","value_tot_delevery_qty","value_tot_delevery_qty_value"],
		col: [20,21,23],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	}

	function load_sub_section(rowNo)
	{
		var section=$('#cbo_section_id').val();
		load_drop_down( 'requires/date_wise_delivery_report_controller',section, 'load_drop_down_subsection', 'subSectionTd');
	}
	
	function load_internall(id)
	{
		if(id==1)
		{
			$('#txt_internal_no').attr('disabled',false);
		}
		else if (id==2 || id==0)
		{
			$('#txt_internal_no').attr('disabled',true);
			$('#txt_internal_no').val("");
		}
		
	}
	
	//cbo_company_id*cbo_section_id*cbo_sub_section_id*cbo_customer_source*cbo_customer_name*txt_order_no*txt_date_from*txt_date_to*hid_order_id
	function openmypage_order()
	{
		var company = $('#cbo_company_id').val();
		var party_name = $('#cbo_customer_name').val();
		var customer_source = $('#cbo_customer_source').val();
		if ( form_validation('cbo_company_id*cbo_customer_source*cbo_customer_name','Company*Within Group*Party')==false )
		{
			return;
		}
		else
		{
			var title = 'Order No. Pop-up';
			var page_link = 'requires/date_wise_delivery_report_controller.php?company='+company+'&party_name='+party_name+'&customer_source='+customer_source+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=420px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					$('#txt_order_no').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					release_freezing();
				}
			}
		}
	}
	
	function generate_report(report_type)
	{
		//cbo_company_id*cbo_section_id*cbo_sub_section_id*cbo_customer_source*cbo_customer_name*txt_order_no*txt_date_from*txt_date_to*hid_order_id
		
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('txt_order_no').value=='' && document.getElementById('txt_internal_no').value=='')
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		}
		
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_section_id*cbo_sub_section_id*cbo_customer_source*cbo_customer_name*txt_order_no*txt_date_from*txt_date_to*hid_order_id*txt_internal_no',"../../../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/date_wise_delivery_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
			setFilterGrid("table_body_id",-1,tableFilters2);
			setFilterGrid("table_body_ids",-1,tableFilters3);
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();
		$('.delivery_challan').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body_id tr:first').show();
		$('.delivery_challan').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function print_delv_challan(company_id, mst_id, trims_del, within_group, cust_order_no){
		var report_title=$( "div.form_caption" ).html();
			/* print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_dalivery_no').val()+'*'+$('#cbo_template_id').val()+'*'+$('#no_copy').val(), "print_delv_challan", "requires/date_wise_delivery_report_controller")  */
			print_report( company_id+'*'+mst_id+'*'+within_group+'*'+report_title+'*'+cust_order_no+'*'+trims_del+'*'+'1'+'*'+'1', "print_delv_challan", "requires/date_wise_delivery_report_controller") 
			//return;
			show_msg("3");
	}

	function print_button_setting(company)
	{    

		get_php_form_data(company,'print_button_variable_setting','requires/date_wise_delivery_report_controller' );
	}
	
	
</script>
</head>
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
     <h3 style="width:1020px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1020px">  
             <form name="date_wise_production_1" id="date_wise_production_1" autocomplete="off" >    
                    <fieldset> 
                        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="120" class="must_entry_caption">Company</th>
                                <th width="120">Location</th>
                                <th width="100">Section</th>
                                <th width="100">Sub-Section</th>
                                <th width="100">Customer Source</th>
                                <th width="100">Internal Ref</th>
                                <th width="100">Party</th>
                                <th width="140">Dev. Challan</th>
                                <th colspan="2" id="th_date_caption">Date</th> 
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('date_wise_production_1','','','','')" /></th>
                            </thead>
                            <tbody>
                            	<tr>
                                    <td>
										 <? 
                                       		 echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/date_wise_delivery_report_controller', this.value, 'load_drop_down_location', 'location_td');print_button_setting(this.value);","","","","","",2);  ?>                            
                                    </td>
                                    <td id="location_td">
		                                <?php 
		                                    echo create_drop_down( 'cbo_location_name', 100, $blank_array, '', 1, '-- Select Location --', $selected, '', '', '', '', '', '', 2);
		                                ?>
		                            </td>
                                    <td>
										<? 
											 echo create_drop_down( "cbo_section_id", 100, $trims_section,"", 1, "-- All --","","load_sub_section(1)",0,'','','','','','',"");
                                        ?>
                                    </td>
                                    <td id="subSectionTd">
										<? 
											echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"", 1, "-- All --","",'',0,'','','','','','',""); 
           
                                        ?>
                                    </td>
                                    <td>
										<? 
                                       		 echo create_drop_down( "cbo_customer_source",100,$yes_no,"", 1, "--All--",0, "load_drop_down( 'requires/date_wise_delivery_report_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' );load_internall(this.value)","","","","","",2);
                                        ?>
                                    </td>
                                    <td >
                                <input type="text" name="txt_internal_no" id="txt_internal_no" value="" class="text_boxes" style="width:100px;" disabled/>                    							
                            		</td>
                                    <td id="buyer_td">
										<? 
                                        	echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                        ?>
                                    </td>
                                    <td >
                                        <input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:100px" placeholder="Write/Browse" onDblClick="openmypage_order();"/><input type="hidden" name="hid_order_id" id="hid_order_id"></td>
                                     <td width="90">
                                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    				</td>
                                    <td width="90">
                                    	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        		    </td>
                                    <td id="button_data_panel">
                                    
									</td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="center"><? echo load_month_buttons(1);  ?></td>
                                </tr>
                        	<tbody>
                        </table> 
                    </fieldset>
              </form> 
          </div>
          <div id="report_container" align="center"></div>
          <div id="report_container2"></div> 
   </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>