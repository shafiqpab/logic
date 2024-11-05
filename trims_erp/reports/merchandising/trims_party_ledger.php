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

	function load_sub_section(rowNo)
	{
		var section=$('#cbo_section_id').val();
		load_drop_down( 'requires/trims_party_ledger_controller',section, 'load_drop_down_subsection', 'subSectionTd');
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
			var page_link = 'requires/trims_party_ledger_controller.php?company='+company+'&party_name='+party_name+'&customer_source='+customer_source+'&action=order_popup';
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
		
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('txt_search_common').value==''){
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		}
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_customer_source*cbo_customer_name*cbo_search_by*txt_search_common*txt_date_from*txt_date_to',"../../../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/trims_party_ledger_controller.php",true);
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
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
	
	function fnc_search_by(val)
	{
		if(val==1 || val==0)
		{
			$('#txt_search_common').val('');
			$('#search_td').html('WO NO.');
		}
		else if(val==2)
		{
			$('#txt_search_common').val('');
			$('#search_td').html('Bill NO.');
		}
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
                                <th width="100">Within Group</th>
                                <th width="100">Party</th>
                                <th width="100">Search By</th>
                                <th width="140" id="search_td">WO No</th>
                                <th colspan="2" id="th_date_caption">Bill Date</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('date_wise_production_1','','','','')" /></th>
                            </thead>
                            <tbody>
                            	<tr>
                                    <td>
										 <? 
                                       		echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);  ?>                            
                                    </td>
                                    <td>
										<? 
                                       		echo create_drop_down( "cbo_customer_source",100,$yes_no,"", 1, "--All--",0, "load_drop_down( 'requires/trims_party_ledger_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' )","","","","","",2);
                                        ?>
                                    </td>
                                    <td id="buyer_td">
										<? 
                                        	echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                        ?>
                                    </td>
                                    <td>
										<? 
											$search_by = array(1 => "WO No", 2 => "Bill No");
                                       		echo create_drop_down( "cbo_search_by",100,$search_by,"", 0, "--Select--",0, "fnc_search_by(this.value)","","","","","",2);
                                        ?>
                                    </td>
                                    <td >
                                        <input name="txt_search_common" id="txt_search_common" type="text"  class="text_boxes" style="width:127px" placeholder="Write"/><input type="hidden" name="hid_order_id" id="hid_order_id"></td>
                                     <td width="90">
                                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    				</td>
                                    <td width="90">
                                    	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        		    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" /> 					</td>
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