<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Grey Roll Issue To Batch Receive WIP Report
Functionality	:
JS Functions	:
Created by		:	
Creation date 	: 	23-10-2022
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
echo load_html_head_contents("Grey Roll Issue To Batch Receive WIP Report", "../../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
	{
		col_operation: {
		id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
		col: [26,27,28,29],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(operation)
	{
		var cbo_company_name =$('#cbo_company_name').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var txt_job_no = $('#txt_job_no').val();
		var txt_internal_ref = $('#txt_internal_ref').val();
		var txt_style_no = $('#txt_style_no').val();
		var txt_booking_no = $('#txt_booking_no').val();
		var cbo_based_on =$('#cbo_based_on').val();
		var txt_date_from =$('#txt_date_from').val();
		var txt_date_to =$('#txt_date_to').val();

		/*if($('#cbo_company_name').val()==0)
		{
			var data='cbo_knitting_company*cbo_based_on*txt_date_from*txt_date_to';
			var filed='Working Company Name*Based On*From Date*To Date';
		}
		else
		{
			var data='cbo_company_name*cbo_based_on*txt_date_from*txt_date_to';
			var filed='Company Name*Based On*From Date*To Date';
		}
		if( form_validation(data,filed)==false )
		{
			return;
		}*/

		if(txt_job_no!="" || txt_style_no!="" || txt_booking_no!="" || txt_internal_ref!="" || (cbo_based_on!=0 && txt_date_from!="" && txt_date_to!=""))
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*cbo_based_on*txt_date_from*txt_date_to','Company*Based On*From Date*To Date')==false)
			{
				return;
			}
		}

		freeze_window(5);
	 	var data="action=generated_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_knitting_source*cbo_knitting_company*cbo_floor_id*cbo_buyer_name*cbo_year*txt_job_no*txt_internal_ref*txt_style_no*txt_booking_no*txt_date_from*txt_date_to*cbo_based_on',    "../../../");
	 	// alert(data);release_freezing();return;

		http.open("POST","requires/batch_transaction_report_for_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
	}

	function fnc_show_batch_report()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1,tableFilters);

			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#scroll_body tr:first').hide();
		}

		if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="none";
			$('#scroll_body_subcon tr:first').hide();
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }

	    if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="397px";
	        $('#scroll_body_subcon tr:first').show();
		}
	}

	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}

 	function jobNumber()
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/batch_transaction_report_for_sales_controller.php?cbo_company_id=' + cbo_company_id + '&action=job_no_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../../');

 			emailwindow.onclose = function ()
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				//alert (job_no);
				$('#fso_number_show').val(fso_no);
				$('#fso_number').val(fso_id);
 			}
 		}
 	}

 	function delivery_roll_popup(delivery_id, fso_id, dtls_id, action)
    {
        var popup_width = '';
        if (action == "roll_delivery_popup")
        {
            popup_width = '400px';
        }
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_transaction_report_for_sales_controller.php?delivery_id=' + delivery_id + '&action=' + action + '&fso_id=' + fso_id + '&dtls_id=' + dtls_id, 'Detail Veiw', 'width=' + popup_width + ', height=300px,center=1,resize=0,scrolling=0', '../../');
    }

    function generate_issue_print_report(print_btn,company_id,issue_number,issue_id)
	{
		var report_title="Grey Fabric Roll Issue Challan";
		//   alert(print_btn);
		if(print_btn==68) // Print Barcode
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=roll_issue_challan_print1', true );
		}
		if(print_btn==69)//Fabric Details
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=issue_challan_print', true );
		}
		if(print_btn==70)//GIN3-MC
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=mc_wise_print', true );
		}
		if(print_btn==71)//GIN4
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=fabric_details_print_bpkw', true );
		}
		if(print_btn==181)//GIN5
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=fabric_details_print_bpkw_gin5', true );
		}
		if(print_btn==388)//GIN6
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=fabric_details_print_bpkw_gin6', true );
		}
		if(print_btn==236)//Print With Collar Cuff
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=roll_issue_challan_print2', true );
		}
		if(print_btn==325)//GIN2
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=roll_issue_challan_print', true );
		}
		if(print_btn==326)//Sales Wise Issue
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=sales_roll_issue_challan_print', true );
		}
		if(print_btn==327)//GIN1
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=roll_issue_challan_print1', true );
		}
		if(print_btn==84)//Print 2
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=sales_roll_issue_challan_print2', true );
		}
		if(print_btn==415)//GIN7
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=fabric_details_print_bpkw_gin7', true );
		}
		if(print_btn==451)//Print With Collar Cuff-outside
		{
			window.open("../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=" + company_id+'*'+issue_number+'*'+issue_id+'*'+report_title+'&action=roll_issue_challan_print3', true );
		}
	}

	function generate_rcvBatch_print_report(print_btn,company_id,rcv_number,sys_id)
	{
		var report_title="Grey Roll Receive By Batch";
		//   alert(print_btn);
		if(print_btn==0) // Print
		{
			window.open("../../grey_fabric/requires/grey_feb_receive_batch_entry_controller.php?data=" + company_id+'*'+rcv_number+'*'+sys_id+'*'+report_title+'&action=grey_delivery_print', true );
		}
	}

	function openmypage_batch(batch_ids,color_id,body_part_id,fso_id,popup_width,action)
	{
		var companyID = $("#cbo_company_name").val();
		var popup_width=popup_width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_transaction_report_for_sales_controller.php?companyID='+companyID+'&batch_ids='+batch_ids+'&color_id='+color_id+'&body_part_id='+body_part_id+'&fso_id='+fso_id+'&action='+action+'&popup_width='+popup_width, 'Details Veiw', 'width='+popup_width+', height=300px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:1430px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1430px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Source</th>
                            <th>Knitting Company</th>
                            <th>Floor</th>
                            <th>FSO Year</th>
                            <th>Buyer</th>
                            <th>FSO No</th>
                            <th>Internal Ref.</th>
                            <th>Style No</th>
                            <th>Fabric Booking No.</th>
                            <th>Based On</th>
                            <th class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/batch_transaction_report_for_sales_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>
                                <td>
                                	<?
                                	echo create_drop_down("cbo_knitting_source",65,$knitting_source,"",1,"-- Select --", 0,"load_drop_down( 'requires/batch_transaction_report_for_sales_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com')",0,"1,3");
									?>
                                </td>
                                <td id="knitting_com">
                                    <?
                                        echo create_drop_down( "cbo_knitting_company", 130, "","", 1, "-- Select Knitting Company --", 0, "" );
                                    ?>
                                </td>
                                <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor_id", 65, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                </td>
                                <td>
                                	<?
                                       echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
									?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
										echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
                                </td>
                                <td>
                                    <input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" placeholder="Write">
                                </td>   
                                <td align="center">
                                    <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" placeholder="Write" style="width:100px" />
                                </td>                         
                                <td>
                                    <input type="text"  name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px;" placeholder="Write">
                                </td>
                                <td>
                                    <input type="text"  name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;" placeholder="Write">
                                </td>
                                <td>
                                	<?
                                		$based_on = array('1'=>'Grey Roll Issue','2'=>'Batch Receive ');
                                    	echo create_drop_down( "cbo_based_on", 100, $based_on,"", 1,"-- Select --", 1, "",0,"" );
									?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                    &nbsp;To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="13">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>