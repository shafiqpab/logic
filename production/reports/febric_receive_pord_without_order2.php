<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status  Without Order Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	01-07-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Receive Status Without Order Report", "../../", 1, 1,'',1,1);

?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

var tableFilters =
{
	col_operation: {
	id: ["value_dtls_tot_sample_qty","value_dtls_tot_gery_req","value_dtls_tot_yarn_issue","value_dtls_tot_yarn_balance","value_dtls_tot_gery_knit_product","value_dtls_tot_gray_bal","value_dtls_tot_gery_delivery","value_dtls_tot_gery_in_knit_product","value_dtls_tot_grey_knit_receive_prod","value_dtls_tot_net_transfer","value_dtls_tot_gray_available_all","value_dtls_tot_gray_balance","value_dtls_tot_gray_issue","value_dtls_tot_batch_qty","value_dtls_tot_dying_qty","value_dtls_tot_dying_balance","value_dtls_tot_fin_req_qty","value_dtls_tot_fin_prod_qnty","value_tot_fin_balance","value_dtls_tot_fin_delivery_qty","value_dtls_tot_fabric_in_prod_floor","value_dtls_tot_finish_prod_rece_store","value_finish_parchase_rece_store","value_dtls_tot_fabric_store_available","value_dtls_tot_fin_balance","value_dtls_tot_cutting_qty","value_dtls_tot_yet_to_issue","value_dtls_tot_left_over"],
	// col: [18,19,20,21,22,23,24,25,27,28,29,30,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46],
	col: [4,19,20,21,22,23,24,25,26,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47],
	operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}

 
function fn_report_generated()
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	if($('#txt_wo_no').val()=="" && $('#txt_internal_ref').val()=="")
	{
		if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}
	/*else
	{*/
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_wo_year*txt_wo_no*txt_internal_ref*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/febric_receive_pord_without_order2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	//}
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body",-1,tableFilters);
		//append_report_checkbox('table_header_1',1);
		// $("input:checkbox").hide();
		show_msg('3');
		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tbody').find('tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tbody').find('tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="300px";
}

function generate_order_report(booking_no,company_id,is_approved,action,entry_form,style_id="")
{
	if (entry_form==140) 
	{
		var action="sample_requisition_print1";
		var cbo_template_id=1;
		print_report( company_id+'*'+style_id+'*'+trim(booking_no)+'*'+cbo_template_id, "sample_requisition_print1", "../../order/woven_order/requires/sample_requisition_with_booking_controller" )
		return;
	}
	else
	{
		var data="action="+action+'&txt_booking_no='+"'"+trim(booking_no)+"'"+'&cbo_company_name='+"'"+company_id+"'"+'&id_approved_id='+"'"+is_approved+"'";
				
		http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
	
	
}
	
function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

function openmypage(boking_id,type,booking_no)
{
	page_link='requires/febric_receive_pord_without_order2_controller.php?boking_id='+boking_id+'&action='+type+'&booking_no='+booking_no;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width=1000px, height=450px, center=1, resize=0, scrolling=0','../');
}

function open_febric_receive_status_color_wise_popup(boking_id,type,color_id)
{
	var widths="";
	if(type=='issue_to_cut')
	{
		widths='800px';
	}
	else
	{
		widths='900px';
	}
	page_link='requires/febric_receive_pord_without_order2_controller.php?boking_id='+boking_id+'&action='+type+'&color_id='+color_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width='+widths+', height=450px, center=1, resize=0, scrolling=0','../');
}


function opengreyNetTransfer(order_id,company_id,type)
{
	page_link='requires/febric_receive_pord_without_order2_controller.php?order_id='+order_id+'&company_id='+company_id+'&action='+type;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width=800px, height=300px, center=1, resize=0, scrolling=0','../');
}

</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:800px;">
             <table class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="60">WO Year</th>
                    <th width="100">WO Number</th>
                    <th width="80">Internal Ref.</th>
                    <th width="130" colspan="2">WO Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:90px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/febric_receive_pord_without_order2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_wo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td><input name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated();" /></td>
                    </tr>
                    <tr>
                    	<td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
