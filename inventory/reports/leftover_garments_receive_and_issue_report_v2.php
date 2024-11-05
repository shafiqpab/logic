<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Leftover Garments Receive and Issue Report
Functionality	:	
JS Functions	:
Created by		:	Sapayth Hossain
Creation date 	: 	09-12-2020
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
//die;
echo load_html_head_contents('Production Status Summary Report', '../../', 1, 1, $unicode, 1, 1);
?>	
<script>
	var permission='<?php echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

	function openStylePopup() {
		if( !form_validation('cbo_company_name', 'Company') ) { return; }

		var company_name = document.getElementById('cbo_company_name').value;
		var buyer_name = document.getElementById('cbo_buyer_name').value;
		var job_year = document.getElementById('cbo_year').value;
		/* var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();*/

		var page_link='requires/leftover_garments_receive_and_issue_report_v2_controller.php?action=style_search_popup&company='+company_name+'&buyer='+buyer_name+'&job_year='+job_year;
		var title="Style Reference";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var data=style_no.split("_");
			$('#hdn_style_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);
			// $('#hdn_job_no').val(data[1]); 
	  		$('#txt_style_ref').val(data[2]);
	  		$('#txt_job_no').attr('disabled','true');
		}
	}

	function openOrderPopup() {
		if( !form_validation('cbo_company_name', 'Company') ) { return; }
		
		var companyID = $("#cbo_company_name").val();
		var job_no = $("#txt_job_no").val();
		var cbo_job_year = document.getElementById('cbo_year').value;
		var page_link='requires/leftover_garments_receive_and_issue_report_v2_controller.php?action=orderno_search_popup&companyID='+companyID+'&job_no='+job_no+'&job_year='+cbo_job_year;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hdnOrderNo").value;
			var order_id=this.contentDoc.getElementById("hdnOrderId").value;

			$('#hdn_order_no').val(order_no);
			$('#hdn_order_id').val(order_id);
			// var orderNoStr = order_no.replace(/\*/g, ',');
			$('#txt_order_no').val(order_no);
		}		
	}

	function fn_report_generated() {
		if( !form_validation('cbo_company_name','Company')) {
			return;
		}
		var styleRef = document.getElementById('txt_style_ref').value;
		var jobNo = document.getElementById('txt_job_no').value;
		var orderNo = document.getElementById('txt_order_no').value;
		var txt_int_ref = document.getElementById('txt_int_ref').value;
		if (styleRef == '' && jobNo == '' && orderNo == '' && txt_int_ref == '') {
			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To')) {
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		freeze_window(3);
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*hdn_style_ref_id*hdn_order_id*hdn_order_no*txt_style_ref*txt_job_no*txt_order_no*txt_int_ref*cbo_location_name*cbo_store_name*cbo_goods_type', '../../')+'&report_title='+report_title;
		//alert(data);return;
		
		http.open("POST","requires/leftover_garments_receive_and_issue_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse() {
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);
			$('#hidden_report_container').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			var tableFilters = {
				col_operation: {
				id: ["value_total_rcv_qty","value_total_issue_qty","value_total_stock_qty"],
				col: [8,9,10],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body", -1, tableFilters);
			
			release_freezing();
			show_msg('3');
		}
	}

	function fnc_qty_details(poBreakDownIds, companyId, goodsType, action, popupTitle) {
		var dateFrom = document.getElementById('txt_date_from').value;
		var dateTo = document.getElementById('txt_date_to').value;
	    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/leftover_garments_receive_and_issue_report_v2_controller.php?poBreakDownIds='+poBreakDownIds+'&companyId='+companyId+'&goodsType='+goodsType+'&dateFrom='+dateFrom+'&dateTo='+dateTo+'&action='+action, popupTitle, 'width=600px,height=320px,center=1,resize=0', '../../');
	    // emailwindow.onclose=function() {}
	}

	function new_window() {
		$("#scroll_body").css({"overflow-y":"auto","max-height":"none"});
		$("#img_id").css("display","block");
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('hidden_report_container').innerHTML+'</body</html>');
		d.close(); 
		
		$("#scroll_body").css({"overflow-y":"scroll","max-height":"400px"});
		$("#img_id").css("display","none");
		
		$("#table_body tr:first").show();
	}

</script>
</head>
<body>
<form id="leftoverReport_1">
    <div style="width:100%;" align="center">
        <?php echo load_freeze_divs ('../../', $permission); ?>
        <h3 align="left" id="accordion_h1" style="width: 99%;" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <fieldset style="width: 99%;" id="content_search_panel">
            <table class="rpt_table" cellpadding="1" cellspacing="2" align="center" border="1" rules="all" style="width: 100%;">
                <thead> 
                	<tr>
                    <th class="must_entry_caption">Company</th>
					<th>Location</th>
					<th>Year</th>
                    <th>Buyer</th>
					<th>Goods Type</th>
					<th>Store Name</th>
                    <th>Style Ref.</th>
				    <th>Job No</th>
                    <th>Order No</th>
                    <th>Internal Ref.</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px;float:right" value="Reset" onClick="reset_form('leftoverReport_1','report_container*report_container2','','','')" />  </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/leftover_garments_receive_and_issue_report_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/leftover_garments_receive_and_issue_report_v2_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/leftover_garments_receive_and_issue_report_v2_controller',this.value, 'load_drop_down_store_name', 'store_name_td');" );
							?>
                        </td>
						<td id="location_td" align="center">
							<? 
								echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "--Select Location--", $selected, "",0,0  );
							?>
                        </td>
                        <td>
                        	<?php echo create_drop_down( 'cbo_year', 60, create_year_array(), '', 1, '-- All --', date('Y',time()), '', 0, '' ); ?>
                        </td>
                        <td id="buyer_td">
                        <?php 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                        ?>
                        </td>
						<td> 
						<?
						$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
						echo create_drop_down( "cbo_goods_type", 120, $goods_type_arr, "", 1, "-- Select Goods Type --", $selected, "", "", "", '', '');
						?>
						</td>
						<td id="store_name_td" >
							<? 
							echo create_drop_down( "cbo_store_name", 120, "SELECT id,store_name from lib_store_location  where id='$data'", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
							?>
						</td>
                        <td align="center">	
                        	<input type="text" name="txt_style_ref" id="txt_style_ref" onDblClick="openStylePopup();" class="text_boxes" style="width:100px" placeholder="Browse" readonly="readonly" >
                        </td>
                        <td align="center">	
                        	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write" >
                        </td>
                        <td align="center">	
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse or Write" onDblClick="openOrderPopup();" />
                        </td>
                        <td align="center">	
                        	<input type="text" name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:100px" placeholder="Write" >
                        </td>
                        <td>
                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                        </td>
                        <td>
                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" >
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:60px; float:right" value="Show" onClick="fn_report_generated()" />
                        	<input type="hidden" id="hdn_style_ref_id" name="hdn_style_ref_id" />
                        	<input type="hidden" id="hdn_order_id" name="hdn_order_id" />
				            <input type="hidden" id="hdn_order_no" name="hdn_order_no" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                 	<tr align="center"  class="general">
                        <td colspan="13">
                        	<?php echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
            </table> 
          </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
    <div id="hidden_report_container" style="display: none;"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//set_multiselect('cbo_buyer_name','0','0','','');
//set_multiselect('cbo_location_id','0','0','','');
</script>
</html>