<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sub-Con WO vs Finish Trims Delivery
				
Functionality	:	
JS Functions	:
Created by		:	Md. Nazim Uddin
Creation date 	: 	20-11-2020
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

	function load_sub_section(rowNo)
	{
		var section=$('#cbo_section_id').val();
		load_drop_down( 'requires/trims_wo_vs_finish_delivery_controller',section, 'load_drop_down_subsection', 'subSectionTd');
	}
	
	//cbo_company_id*cbo_section_id*cbo_sub_section_id*cbo_customer_source*cbo_customer_name*txt_search_common*txt_date_from*txt_date_to*hid_order_id
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
			var page_link = 'requires/trims_wo_vs_finish_delivery_controller.php?company='+company+'&party_name='+party_name+'&customer_source='+customer_source+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=420px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					$('#txt_search_common').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					release_freezing();
				}
			}
		}
	}
	
	function generate_report(report_type)
	{
		//cbo_company_id*cbo_section_id*cbo_sub_section_id*cbo_customer_source*cbo_customer_name*txt_search_common*txt_date_from*txt_date_to*hid_order_id
		
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('txt_wo_no').value==''){
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		}
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_supplier_id*txt_wo_no*cbo_section*txt_date_from*txt_date_to',"../../../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/trims_wo_vs_finish_delivery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();*/
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		/*$('#table_body_id tr:first').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";*/
	}
	
	function fnc_remarks(ids, action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/trims_wo_vs_finish_delivery_controller.php?ids='+ids+'&action='+action,'Remarks Details', 'width=500px,height=320px,center=1,resize=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
     <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:900px">  
             <form name="date_wise_production_1" id="date_wise_production_1" autocomplete="off" >    
                    <fieldset> 
                       <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="120" class="must_entry_caption">Company</th>
                                <th width="150">Supplier</th>
                                <th width="140" id="search_td">WO No</th>
                                <th width="120" >Section</th>
                                <th colspan="2" class="must_entry_caption" id="th_date_caption">WO Date Range</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('date_wise_production_1','','','','')" /></th>
                            </thead>
                            <tbody>
                            	<tr>
                                    <td>
										 <? 
                                       		echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/trims_wo_vs_finish_delivery_controller',this.value, 'load_supplier_dropdown', 'supplier_td_id' );get_php_form_data( this.value, 'eval_multi_select', 'requires/trims_wo_vs_finish_delivery_controller' );","","","","","",2);  ?>                            
                                    </td>

                        			<td id="supplier_td_id"><? echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" ); ?></td>
                                    <td>
                                        <input name="txt_wo_no" id="txt_wo_no" type="text"  class="text_boxes" style="width:127px" placeholder="Write" onDblClick="openmypage_order();"/>
                                    </td>
                                    <td>
										<? 
											echo create_drop_down( "cbo_section", 120, $trims_section,"", 1, "-- Select Section --","","",0,'','','','','','',"cboSection[]");
                                        ?>
                                    </td>
                                    
                                     <td width="90">
                                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    				</td>
                                    <td width="90">
                                    	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        		    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" /> 					</td>
                                </tr>
                                <tr>
                                    <td colspan="7" align="center"><? echo load_month_buttons(1);  ?></td>
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
<script>
	set_multiselect('cbo_supplier_id','0','0','0','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript">
</script> 

</html>