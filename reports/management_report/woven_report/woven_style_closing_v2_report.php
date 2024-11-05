<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Post Costing Report.
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	18-07-2021
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
echo load_html_head_contents("Woven Style Closing v2 Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	function fn_report_generated(report)
	{
		freeze_window(3);
		$("#report_type").val(report);
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
		var txt_style_ref=$("#txt_style_ref").val();
		 
		 
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			if( (txt_date_from.length==0 && txt_date_to.length==0 ) && ( txt_style_ref.length==0 ) )
			{
				
				   if(report==1)
					{
						alert("Select Date or Style ");
					}
					 
				
				release_freezing();
				return;
			}
			var report_title=$("div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*hide_job_id*txt_date_from*txt_date_to',"../../../")+"&report_type="+report+"&report_title="+report_title;
			
			http.open("POST","requires/woven_style_closing_v2_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			//$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
		$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
			
			/*if(response[2]==1)
			{
				var tableFilters = {
					col_operation: {
						id: ["td_jobQty","value_jobVal","td_styleQty","value_styleVal","td_exQty","value_fabbom","value_accbom","value_embbom","value_washbom","value_cmbom","value_opbom","value_totbom","value_fabact","value_accact","value_embact","value_washact","value_cmact","value_opeact","value_totact","value_fob","value_bprofitloss","value_aprofitloss","value_variance","value_marginval","value_marginper"],
						col: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}*/
		
			setFilterGrid("table_body",-1);
	 		show_msg('3');
			release_freezing();
		}
	}

	function openmypage_order(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		//
		var data = $("#cbo_company_name").val()+'_'+type+'_'+$("#cbo_buyer_name").val();
		var page_link='requires/woven_style_closing_v2_report_controller.php?action=order_no_search_popup&data='+data;
		var title='Style Ref. Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			if(type==1)
			{
				$('#txt_style_ref').val(order_no);
				$('#hide_job_id').val(order_id);
			}
			else if(type==2)
			{
				$('#txt_order_no').val(order_no);
				$('#hide_order_id').val(order_id);
			}
		}
	}

	function new_window()
	{
		const el = document.querySelector('#scroll_body');
		if (el) 
		{
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if (el) 
		{
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$("#scroll_body tr:first").show();
		}
	}

	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
 

	function openmypage_actual(po_id,type,tittle,popup_width)
	{
		var txt_exchange_rate=$("#txt_exchange_rate").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_v2_report_controller.php?po_id='+po_id+'&action='+type+'&ex_rate='+txt_exchange_rate, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	 
	
	function generate_ex_factory_popup(action,job,id,start_date,end_date,width)
	{
		var cbo_date_type=$('#cbo_date_type').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_v2_report_controller.php?action='+action+'&job='+job+'&id='+id+'&start_date='+start_date+'&end_date='+end_date+'&cbo_date_type='+cbo_date_type, 'Ex-Factory Details', 'width='+width+',height=250px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_budget_op_cost_popup(action,commercial,lab_test,freight,inspection,currier_pre_cost,design_cost,width)
	{
		//  alert(commercial+"*"+lab_test+"*"+freight+"*"+inspection+"*"+currier_pre_cost+"*"+design_cost);
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_v2_report_controller.php?action='+action+'&commercial='+commercial+'&lab_test='+lab_test+'&freight='+freight+'&inspection='+inspection+'&currier_pre_cost='+currier_pre_cost+'&design_cost='+design_cost, 'Budget Oparational Cost Details', 'width='+width+',height=250px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_actual_op_cost_popup(action,commercial,lab_test,freight,inspection,currier_pre_cost,design_cost,width)
	{
		// alert(commercial+"*"+lab_test+"*"+freight+"*"+inspection+"*"+currier_pre_cost+"*"+design_cost);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_v2_report_controller.php?action='+action+'&commercial='+commercial+'&lab_test='+lab_test+'&freight='+freight+'&inspection='+inspection+'&currier_pre_cost='+currier_pre_cost+'&design_cost='+design_cost, 'Budget Oparational Cost Details', 'width='+width+',height=250px,center=1,resize=0,scrolling=0','../../');
	}



	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='Pub.Ship Date';
		}
		else if(str==2){
			document.getElementById('search_date_td').innerHTML='Ex-factory Date';
		}
		
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:880px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:880px;">
                <table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="150">Buyer Name</th>
                            <th width="100">Style Ref</th>
                          
                            <th width="140" colspan="2" id="search_date_td">Ex-factory Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_style_closing_v2_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                        <td>
                            <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_order(1);" readonly onChange="$('#hide_job_id').val('');" autocomplete="off"/>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                       
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                        	<input type="hidden" name="report_type" id="report_type">
                           
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Style" onClick="fn_report_generated(1);" />
                            <input type="button" id="show_button2" class="formbutton" style="width:50px" value="Style 2" onClick="fn_report_generated(2);" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><?=load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
