<?php
/*--- -----------------------------------------
Purpose			:	This page will display knitting plan and position as per style
Functionality	:
JS Functions	:
Created by		:	Sapayth
Creation date 	:	13-04-2020
Updated by 		:
Update date		:
Oracle Convert 	:
Convert date	:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Knitting Plan and Position as per Style', '../../', 1, 1, $unicode, 0, '');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    /**
     * load buyer dropdown based on the company selected
     * @param  {[int]} companyId [description]
     * @return {[void]}           [description]
     */
    function get_company_config(companyId) {
        load_drop_down('requires/knitting_plan_and_position_style_controller', companyId, 'load_drop_down_buyer', 'buyer_td' );
    }

    /**
     * a handler to reset TNA Date after the year is changed
     * @return {[void]} [description]
     */
    function resetDates() {
        document.getElementById('txt_date_from').value = '';
        document.getElementById('txt_date_to').value = '';

        for(var i = 1; i <= 12; i++) {
            var btn = document.getElementById('btn_'+i);
            if(btn.classList.contains('month_button_selected')) {
                btn.classList.remove('month_button_selected');
                btn.classList.add('month_button');
            } 
        }
    }

    /**
     * change title based on the dropdown selected
     * @param  {[int]} selectedId [description]
     * @param  {[string]} changeBy   [description]
     * @param  {[int]} tdId       [description]
     * @return {[void]}            [description]
     */
    function caption_change(selectedId, changeBy, tdId) {
        if(changeBy == 'type') {
            if(selectedId==1) {
                document.getElementById(tdId).innerHTML = 'TNA Date';
            } else {
                document.getElementById(tdId).innerHTML = 'Shipment date';
            }
        } else {
            if(selectedId==1) {
                document.getElementById(tdId).innerHTML = 'File No';
            } else if(selectedId==2) {
                document.getElementById(tdId).innerHTML = 'Ref. No';
            } else {
                document.getElementById(tdId).innerHTML = 'Style Ref';
            }
        }        
    }

    /**
     * call the controller for the report
     * @param  {[int]} id [description]
     * @return {[void]}    [description]
     */
    function fn_report_generated(id) {        
        if(id == 1) {
            if(form_validation('cbo_company_name', 'Company')==false) {
                return;
            }
            freeze_window(operation);
            var txt_date_from = document.getElementById('txt_date_from').value;
            var txt_date_to = document.getElementById('txt_date_to').value;
            var searchType = document.getElementById('cbo_search_type').value;
            var data="action=generate_report&search_type="+searchType+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year_selection*txt_ref_no*txt_file_no*txt_order_no', "../../");
            http.open("POST", "requires/knitting_plan_and_position_style_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_generate_report_main_reponse;
        }
    }

    /**
     * a handler to attach when fn_report_generated ajax request is finished. displays the report in selected container
     * @return {[void]} [description]
     */
    function fnc_generate_report_main_reponse() {
        if(http.readyState == 4) {
            var response = trim(http.responseText).split("####");;
            // console.log(response);
            document.getElementById('report-area').innerHTML = response[0];
            document.getElementById('report-area').style.display = 'block';

            if(response[1]) {
                document.getElementById('btnExcel').href = response[1];
            }
            
            // document.getElementById('')
            release_freezing();
        }
        
    }

    function printPreview() {
        var w = window.open('', '_blank');
        var d = w.document.open();
        var styles = '<style> .heading-area {background:#E9F3FF; padding: 10px 0; border: 1px solid #E9F3EE; }';
        styles += ' #rpt_table thead { background: #8AABD7; } ';
        styles += ' tbody#report-container tr td { text-align: center; padding: 3px 1px; } ';
        styles += '</style>';
        d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" media="print" /><title>Print Preview</title>'+styles+'</head><body>'+document.getElementById('rptTableArea').innerHTML+'</body</html>');
        d.close();
    }

    function searchPopup(searchType) {
        if(form_validation('cbo_company_name', 'Company Name')==false) {
            return;
        }
        
        var companyID = document.getElementById('cbo_company_name').value;
        var page_link='requires/knitting_plan_and_position_style_controller.php?action=search_popup&searchType='
                        +searchType+'&companyID='+companyID;
        var title='Search Knitting';
        
        searchWindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
        searchWindow.onclose=function() {
            var theform=this.contentDoc.forms[0];
            var jobNo=this.contentDoc.getElementById('hdnJobNo').value
            var orderNo=this.contentDoc.getElementById('hdnOrderNo').value;
            var year=this.contentDoc.getElementById('hdnYear').value;
            
            document.getElementById('txt_job_no').value = jobNo;
            document.getElementById('txt_order_no').value = orderNo;
            document.getElementById('cbo_year_selection').value = year;

            fn_report_generated(1);
        }
    }

</script>

<style>
    tbody#report-container tr td {
        text-align: center;
        padding: 3px 1px;
    }
</style>

</head>

<body>
    <?php echo load_freeze_divs('../', $permission); ?>
	<form id="knittingPlanForm_1">
		<div class="search-panel">
			<fieldset style="width:80%; margin: 0 auto;">
	            <legend>Knitting Plan and Position as per Style</legend>
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Ref No</th>
                        <th>Enter File No</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th>Type</th>
                        <th colspan="2" id="date_td">TNA Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingPlanForm_1', '', '', '', '');resetDates();" class="formbutton" style="width:70px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?php
                                    echo create_drop_down( 'cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, 'get_company_config(this.value);' );
                                ?>
                            </td>
                            <td id="buyer_td">
                                <?php echo create_drop_down('cbo_buyer_name', 120, $blank_array, '', 1, '-- Select Buyer --', $selected, ''); ?>
                            </td>
                             <td align="center">	
                            	<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write" />
                       	    </td>  
                              <td align="center">
                                <input type="text" class="text_boxes" name="txt_file_no" id="txt_file_no" style="width:80px" placeholder="Write" />
                           	 </td>
                            <td>
                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" readonly ondblclick="searchPopup(1)" placeholder="Browse" />
                            </td>
                            <td>
                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" readonly ondblclick="searchPopup(2);" placeholder="Browse" />
                            </td>
                            <td>
                                <?php
                                    $search_by_arr=array(1=>"TNA date wise",2=>"Shipment date wise");
                                    echo create_drop_down( 'cbo_search_type', 140, $search_by_arr, '', 0, '', '', "caption_change(this.value, 'type', 'date_td');", 0 );
                                ?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                            </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /></td>
                        </tr>
                        <tr>
                            <td colspan="11" align="center">
                                <?php echo load_month_buttons(1); ?>
                             </td>
                        </tr>
                    </tbody>        
                </table>				
	        </fieldset>
		</div>
	</form>
    <div width="100%" style="margin-top: 30px; margin-bottom: 60px; display: none;" id="report-area"></div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    document.getElementById('cbo_year_selection').onchange = resetDates;
</script>
</html>