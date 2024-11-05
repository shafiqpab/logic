<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Production Summary
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	11-05-2019
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

echo load_html_head_contents("Style Wise Production Summary","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
			
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_wo_company_name*cbo_buyer_name*txt_style_ref*txt_order*txt_order_id*cbo_year*txt_job_no*txt_job_no_id*cbo_ship_status*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		
		http.open("POST","requires/style_production_summary_report_controller.php",true);
		
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			var tableFilters = {
				/*col_operation: {
				id: ["td_knitting_com","td_knitting_bal","td_knitting_wip","td_inspection_qnty","td_inspe_bal","td_ins_wip","td_makeup_comp","td_makeup_bal","td_makeup_wip","td_triming_comp","td_triming_bal","td_triming_wip","td_mending_comp","td_mending_bal","td_mending_wip","td_wash_comp","td_wash_bal","td_wash_wip","td_attach_comm","td_attach_bal","td_attach_wip","td_sewing_comm","td_sewing_bal","td_sewing_wip","td_pqc_comm","td_pqc_bal","td_pqc_wip","td_iron_com","td_iron_bal","td_re_iron","td_packing_comm","td_packing_bal","td_shipment_com","td_shipment_acc_bal"],
				col: [9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31, 34,35,36,37,38,39,40,41,42,43,44],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}*/
			}
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body_sub",-1,tableFilters);
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	
	function openmypage_style(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var txt_job_no_id = $("#txt_job_no_id").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/style_production_summary_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no+'&txt_job_no_id='+txt_job_no_id+'&type='+type;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);
			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_no_id').val(job_id);	
			}
			else if(type==2) 
			{
				$('#txt_style_ref').val(job_no);
				$('#txt_style_ref_id').val(job_id);	
			}
			else if(type==3) 
			{
				$('#txt_order').val(job_no);
				$('#txt_order_id').val(job_id);	
			}
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}

	function print_report_button_setting(report_ids) 
    {
     
        $('#show_button_1').hide();
        $('#show_button_2').hide();
        $('#show_button_3').hide();

        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button_1').show();}
            if(items==259){$('#show_button_2').show();}
            if(items==125){$('#show_button_3').show();}
            });
    }
	
</script>
 
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1200px;" id="content_search_panel">
            <table class="rpt_table" width="1200" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company</th>
                    <th class="">Working Company</th>
                    <th>Buyer</th>
                    <th>Year</th>
                    <th>Job No</th>
                    <th>Style Ref</th>
                    <th>Order</th>
                    <th>Ship Status</th>
                    <th colspan="2">Pub. Shipment Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td width="140"><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_production_summary_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/style_production_summary_report_controller' );" ); ?></td>

                        <td width="140">
                        	<? echo create_drop_down( "cbo_wo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?>
                        </td>
                        <td id="buyer_td" width="120"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" ); ?></td>
                        <td width="60"><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td width="80">
                            <input style="width:70px;" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_style(1)" class="text_boxes" placeholder="Br/Wr"/>
                            <input type="hidden" name="txt_job_no_id" id="txt_job_no_id"/> 
                        </td>
                        <td width="100">
                            <input style="width:90px;" name="txt_style_ref" id="txt_style_ref" class="text_boxes" placeholder="Write"/>
                        </td>
                        <td width="100">
                            <input style="width:90px;" name="txt_order" id="txt_order" onDblClick="openmypage_style(3)" class="text_boxes" placeholder="Br/Wr" />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>      
                        </td>
                        <td width="100"><? 
						
							$shipStatus=array(1 => "Partial or Pending", 2 => "Full Shipped");
							echo create_drop_down( "cbo_ship_status", 100, $shipStatus,"", 1, "--Select--", 1, "",0,"" ); ?></td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                        <td>
                        	<input type="button" id="show_button_1" class="formbutton" style="width:50px; display:none;" value="Show" onClick="fn_report_generated(1)" />
                        	<input type="button" id="show_button_2" class="formbutton" style="width:50px; display:none;" value="Show 2" onClick="fn_report_generated(2)" />
							<input type="button" id="show_button_3" class="formbutton" style="width:70px; display:none;" value="Color Wise" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
					<tr>
						<td colspan="11" align="center">
							<? echo load_month_buttons(1); ?>
						</td>
					</tr>
                 </tbody>
               </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>