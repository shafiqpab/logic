<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly capacity vs booked report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	02-05-2019
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
echo load_html_head_contents("Monthly capacity vs booked report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(report_type)
	{
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('txt_order_no').value==''){
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		}
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_customer_source*cbo_customer_name*txt_order_no*cbo_section_id*cbo_sub_section_id*txt_item_description*cbo_date_category*txt_date_from*txt_date_to*cbo_delivery_status',"../../");
		freeze_window(3);
		http.open("POST","requires/order_wise_production_and_delivery_report_controller.php",true);
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
			setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body_id tr:first').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
    <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         
         <div id="content_search_panel" style="width:1300px">  
         <form name="monthly_capacity_booked_1" id="monthly_capacity_booked_1" autocomplete="off" >    
            <fieldset>  
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="100">Cust.Source</th>
                        <th width="100">Customer Name</th>
                        <th width="100">Work Order No</th>
                        <th width="100">Section</th>
                        <th width="100">Sub Section</th>
                        <th width="140">Item Description</th>
                        <th width="100">Delivery Status</th>
                        <th width="100">Date Category</th>
                        <th colspan="2" id="th_date_caption">Order Receive Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('monthly_capacity_booked_1','','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
									echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);
                                ?>                            
                            </td>
                            <td>
                                <? 
									echo create_drop_down( "cbo_customer_source",100,$yes_no,"", 1, "--All--",0, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' )","","","","","",2);
                                ?>
                            </td>
                            <td id="buyer_td">
                                <? 
									echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                ?>
                            </td>
                            <td >
                                <input type="text" name="txt_order_no" id="txt_order_no" value="" class="text_boxes" style="width:100px;"/>                    							
                            </td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_section_id", 100, $trims_section,"", 1, "--All--", "", "" );
                                ?>
                            </td>
                           <td>
                                <? 
                                    echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"", 1, "--All--", "", "" );
                                ?>
                           </td>
                            <td>
                                <input type="text" name="txt_item_description" id="txt_item_description" value="" class="text_boxes" style="width:130px;"/>                    							
                            </td>
                           <td> 
							   <?   
									echo create_drop_down( "cbo_delivery_status", 100, $delivery_status, "", 1, "--All--","", " $('#th_date_caption').html($('#cbo_date_category option:selected').text());", "", "");
                                ?>
                        	</td>
                           <td> 
							   <?   
                                   $date_cat=array(1=>"Order Receive Date",2=>"Target Delv Date");
									echo create_drop_down( "cbo_date_category", 100, $date_cat, "", 0, "--All--","", " $('#th_date_caption').html($('#cbo_date_category option:selected').text());", "", "");
                                ?>
                        	</td>
                            <td width="90">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    							
                            </td>
                             <td width="90">
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        
                        	</td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                        </tr>
                        <tr>
                        	<td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
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
