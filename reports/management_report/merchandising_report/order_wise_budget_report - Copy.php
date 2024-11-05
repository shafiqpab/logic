<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Budget Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	7-06-2014
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
//echo load_html_head_contents("Cost Breakdown Report","../../", 1, 1, $unicode,1,1);
echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	 var tableFilters = 
			 {
				 col_47: "none",
				col_operation: {
					id: ["total_order_qnty","total_order_amount","total_yarn_cost","total_purchase_cost","total_knitting_cost","total_yarn_dyeing_cost","total_fabric_dyeing_cost","total_heat_setting_cost"],
			   col: [9,10,12,13,14,15,16,17],
			   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
			   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}	
			}
	function fn_report_generated()
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
	
		if(job_no!="" || order_no!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status',"../../../");
			freeze_window(3);
			http.open("POST","requires/order_wise_budget_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1);
			//alert(document.getElementById('graph_data').value);
			show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
			
		}
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_budget_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_budget_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_fab_purchase_detail(po_id,job_no,company_id,buyer_id,fabric_source,action)
	{  
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}	
	function generate_pre_cost_knit_popup(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_precost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_precost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_precost_washing_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	
	function generate_precost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_precost_trim_cost_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}				
	function new_window(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="yarn_summary";
			//scroll_div='scroll_body';
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
		
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1100px;" id="content_search_panel">
        	
            <table class="rpt_table" width="1100" cellpadding="1" cellspacing="2" align="center">
               <thead>                    
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No.</th>
                    <th>Order Status</th>
                     <th>Order</th>
                    <th class="must_entry_caption">Shipment Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_budget_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                    <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                    </td>
                     <td><? 
					 $order_status=array(0=>"All",1=>"Confirmed",2=>"Projected"); 
					 
					 echo create_drop_down( "cbo_order_status", 100, $order_status,"", 0, "", 1, "" ); 
					 
					 ?></td>
                    <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px" onDblClick="openmypage_order();" placeholder="Write/Browse Order"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                    </td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
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
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
