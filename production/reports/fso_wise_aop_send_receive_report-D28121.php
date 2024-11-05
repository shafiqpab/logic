<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	FSO Wise AOP Send and Receive Report.
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	16-01-2021
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
echo load_html_head_contents("Knitting Production QC Report","../../", 1, 1, $unicode,0,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect_count","td_total_slub_hole_defect_count","td_total_needle_mark_defect_count","td_total_totalDefect_point","td_total_reject_qty"],
	   //col: [24,26,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,51],
	   //col: [27,29,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,54],
	   col: [28,30,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,59],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	var tableFilters4 = 
	{
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect","td_total_slub_hole_defect","td_total_needle_mark_defect","td_total_totalDefect_point","td_total_reject_qty"],
	   	col: [33,35,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,64],
	   	operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function chng_val(vall)
	{
		if(vall=1001)
		{
			if(form_validation('txt_date_to','Date From')==false)
				{
					if(form_validation('txt_date_from','Date From')==false)
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
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_within_group=$('#cbo_within_group').val();
		var cbo_po_company=$('#cbo_po_company').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_year_selection=$('#cbo_year_selection').val();
		var txt_sales_order_no=$('#txt_sales_order_no').val();
		var cbo_based_on=$('#cbo_based_on').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		//cbo_company_name*cbo_within_group*cbo_within_group*cbo_po_company*cbo_buyer_name*cbo_year_selection*txt_sales_order_no*cbo_based_on*txt_date_from*txt_date_to
		if (txt_sales_order_no=='')
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
			{
				release_freezing();
				return;
			}
		}
		else{
			if(form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		
	
		var data="action=report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_po_company*cbo_buyer_name*cbo_year_selection*txt_sales_order_no*cbo_based_on*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/fso_wise_aop_send_receive_report_controller.php",true);
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
			//setFilterGrid("table_body",-1,tableFilters);
			//setFilterGrid("table_body_show4",-1,tableFilters4);
			/*if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
			}*/
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
		document.getElementById('scroll_body4').style.overflow="auto";
		document.getElementById('scroll_body4').style.maxHeight="none";
		document.getElementById('scroll_body5').style.overflow="auto";
		document.getElementById('scroll_body5').style.maxHeight="none";
		document.getElementById('scroll_body6').style.overflow="auto";
		document.getElementById('scroll_body6').style.maxHeight="none";
		document.getElementById('scroll_body7').style.overflow="auto";
		document.getElementById('scroll_body7').style.maxHeight="none";
		document.getElementById('scroll_body8').style.overflow="auto";
		document.getElementById('scroll_body8').style.maxHeight="none";
		
		$('#table_body tr:first').hide();
		$('#table_body_show4 tr:first').hide();*/
		//$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		/*$('#table_body tr:first').show();
		$('#table_body_show4 tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="300px";
		document.getElementById('scroll_body3').style.overflowY="scroll";
		document.getElementById('scroll_body3').style.maxHeight="300px";
		document.getElementById('scroll_body4').style.overflowY="scroll";
		document.getElementById('scroll_body4').style.maxHeight="300px";
		document.getElementById('scroll_body5').style.overflowY="scroll";
		document.getElementById('scroll_body5').style.maxHeight="300px";
		document.getElementById('scroll_body6').style.overflowY="scroll";
		document.getElementById('scroll_body6').style.maxHeight="300px";
		document.getElementById('scroll_body7').style.overflowY="scroll";
		document.getElementById('scroll_body7').style.maxHeight="300px";
		document.getElementById('scroll_body8').style.overflowY="scroll";
		document.getElementById('scroll_body8').style.maxHeight="300px";*/
	}
	
	function openmypage_booking_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		
		page_link='requires/fso_wise_aop_send_receive_report_controller.php?action=booking_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Job Info", 'width=1000px,height=370px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
				document.getElementById('txt_booking_no').value=booking_no;
			}
		}
	}	

	function openmypage_jobNo() {
 	var cbo_company_id = $('#cbo_company_name').val();
 	var cbo_within_group = $('#cbo_within_group').val();
 	var color_from_library = $('#color_from_library').val();

 	if (form_validation('cbo_company_name', 'Company') == false) {
 		return;
 	}
 	else {
 		var title = 'Job Selection Form';
 		var page_link = 'requires/fso_wise_aop_send_receive_report_controller.php?cbo_company_id=' + cbo_company_id+'&cbo_within_group='+cbo_within_group + '&action=jobNo_popup';

 		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

 		emailwindow.onclose = function () {
 			var theform = this.contentDoc.forms[0];
 			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
 			var booking_data = hidden_booking_data.split("**");
 			var job_id = booking_data[9];

 			$('#txt_sales_order_no').val(booking_data[0]);
 			$('#txt_sales_order_id').val(booking_data[0]);
        }
    }
}

function fnc_purchase_details(prod_id,action)
{
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fso_wise_aop_send_receive_report_controller.php?prod_id='+prod_id+'&action='+action,'Details Popup', 'width=1100px,height=320px,center=1,resize=0','../../');
	emailwindow.onclose=function()
	{
		
	}
}
</script>

</head>
 
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../"); ?>
		<h3 style="width:1030px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
		<div id="content_search_panel">
		<fieldset style="width:960px;">
            <table class="rpt_table" width="1030" cellpadding="1" cellspacing="2" align="center">
            	<thead>
                	<tr> 
                        <th width="120">Company Name</th>
                        <th width="80">Within Group</th>
                        <th width="120">PO Company</th>
                        <th width="120">PO Buyer Name</th>
                        <th width="80">Booking Year</th>
                        <th width="100">Sales Order No</th>
                        <th width="100">Based on</th>
                        <th width="180" class="must_entry_caption">Date Range</th>
                        <th width="100">
                        <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                        </th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
						<?
                        echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "");
                        ?>
                    </td>
                    <td><?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 0, "--  --",str_replace("'","",$within_group), "","0" ); ?></td>
                    <td><? echo create_drop_down( "cbo_po_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/fso_wise_aop_send_receive_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );",0 ); ?> </td>

                    <td id="buyer_td">
                    <? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$buyer_id), "","1" ); ?>
                    </td>
                    
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 80, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td>
                    	<input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" onClick="openmypage_jobNo()" placeholder="Browse" style="width:87px" readonly="readonly" />
                    	<input type="hidden" name="txt_sales_order_id" id="txt_sales_order_id" class="text_boxes" style="width:87px" />
                    </td>
                    <td>
                    	<?
                    	$search_by_arr=array(1=>"AOP Issue Date",2=>"AOP Receive Date");
                        echo create_drop_down( "cbo_based_on",100, $search_by_arr,"",0, "",1,'',0 );
                    	?>
                    </td>
                    <td>
                  		<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                  		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                   	<td>
                   	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" />
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
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
