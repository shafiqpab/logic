<?php
/*--- ----------------------------------------- Comments
Purpose         :   Printing Bill Report
Functionality   :   
JS Functions    :
Created by      :   Sapayth Hossain
Creation date   :   03-09-2020
Updated by      :   
Update date     :
Oracle Convert  :       
Convert date    :      
QC Performed BY :       
QC Date         :   
Comments        :
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Printing Bill Report', '../../', 1, 1, $unicode, 1, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    /*var tableFilters = {
		col_operation: {
		   id: ["tot_wo_qty","tot_avg_rate","tot_mat_rcv","tot_mat_issue","tot_prod_qty","tot_qc_qty","tot_del_qty","tot_rej_qty","tot_bill_qty","tot_bill_amt"],
		   col: [6,7,9,10,11,12,13,14,15,16],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}*/

    function showReport(reportType) {
    	if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*Date From*Date To')==false)
		{
			return;
		}
		if(reportType == 1) {
			var reportTitle = '';
			
		} else {
			var reportTitle = '';
		}

		freeze_window(5);

		var data='action=report_generate_'+reportType+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_party_id*txt_wo_no*txt_job_no*cbo_buyer_id*txt_buyer_po*txt_buyer_style*cbo_based_on*txt_date_from*txt_date_to', '../../')+'&report_title='+reportTitle;

		http.open('POST', 'requires/printing_bill_report_controller.php', true);
		http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		http.send(data);
		http.onreadystatechange = function() {reportGeneratedResponse(reportType)};
    }

    function reportGeneratedResponse(reportType) {
    	if(http.readyState == 4) {
    		// console.log(reportType);
	  		var response=trim(http.responseText);
	  		if (reportType==2) {
	  			/*response = response.split('**');
	  			$('#report2-header').html(response[0]);
	  			console.log(response[1]);
	  			$('tbody#report-rows').html('kljdsflkjds');*/
	  			var reponse=trim(http.responseText).split("**"); 

	  			$('#report-area').html(reponse[0]);
	  			document.getElementById('report-area1').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

	  			setFilterGrid("table_body", -1, tableFilters);
	  			
	  		} else {
	  			$('#report-area').html(response);

	  			$('#report-area1').html('');
	  		}
			
			/*document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid('tbl_list_search',-1);*/
			show_msg('3');
			release_freezing();
	 	}
    }

    function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report-area').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}

</script>
</head>
<body>
    <div style="width:100%;" align="center">
        <?php echo load_freeze_divs ('../../', $permission); ?>
        <fieldset style="width:60%;">
            <legend>Search Panel</legend>
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
		        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
		            <thead>
		                <tr>
		                    <th style="width:15%;">Company Name</th>
		                    <th style="width:10%;">Within Group</th>
		                    <th style="width:10%;">Party</th>
		                    <th style="width:10%;">WO No</th>
		                    <th style="width:10%;">Job No</th>
		                    <th style="width:10%;">Buyer</th>
		                    <th style="width:10%;">Buyer PO</th>
                            <th style="width:10%;">Buyer Style No</th>
		                    <th style="width:10%;">Based On</th>
		                    <th colspan="2" style="width:5%;">Date Range</th>
		                    <th colspan="2" style="width:10%;">
		                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
		                    </th>
		                </tr>
		            </thead>
		            <tbody>
		                <tr class="general">
		                    <td>
		                        <?php echo create_drop_down('cbo_company_id', 140, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', 0, "load_drop_down( 'requires/printing_bill_report_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );load_drop_down( 'requires/printing_bill_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
		                    </td>
		                    <td>
		                        <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 0, '', 0, "load_drop_down( 'requires/printing_bill_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party', 'party_td' );"); ?>
		                    </td>
		                    <td id="party_td">
		                        <?php echo create_drop_down('cbo_party_id', 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $selected, ''); ?>
		                    </td>
		                    <td>
		                        <input class="text_boxes" type="text" name="txt_wo_no" id="txt_wo_no" />
		                    </td>
		                    <td>
		                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
		                    </td>
		                    <td id="buyer_td">
		                    	<?php echo create_drop_down('cbo_buyer_id', 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $selected, ''); ?>
		                    </td>
		                    <td>
		                    	<input class="text_boxes" type="text" name="txt_buyer_po" id="txt_buyer_po" />
		                    </td>
                            <td>
		                    	<input class="text_boxes" type="text" name="txt_buyer_style" id="txt_buyer_style" />
		                    </td>
		                    <td>
		                    	<?php
		                    		$based_on_arr = array(1 => 'Delivery Date Wise', 2 => 'Bill Date Wise');
		                    		echo create_drop_down('cbo_based_on', 140, $based_on_arr, '', 0, '', 1, '');
		                    	?>
		                    </td>
		                    <td>
	                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
	                        </td>
	                        <td>
	                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date">
	                        </td>
		                    <td>
		                    	<input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="showReport(1)" />
		                    </td>
		                    <td>
		                    	<input type="button" name="btnSearchJob" class="formbutton" value="Show-2" onClick="showReport(2)" />
		                    </td>
		                </tr>
		                <tr>
		                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
		                </tr>
		            </tbody>
		            <tfoot>
	                    <tr>
	                        <td colspan="12" align="center">
								<?php echo load_month_buttons(1); ?>
	                        </td>
	                    </tr>
	                </tfoot>
		        </table>
		    </form>
        </fieldset>
    </div>
    <div id="report-area1" style="margin: 20px auto; width: 100%" align="center"></div>
    <div id="report-area" style="margin: 20px auto; width: 100%"></div>

    <div id="report-area2" style="margin: 20px auto; width: 100%">
    	<div id="report2-header"></div>
    	<table>
    		<tbody id="report-rows"></tbody>
    	</table>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>