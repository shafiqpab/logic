<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Category And Line Wise Total NPT Report
				
Functionality	:	
JS Functions	:
Created by		:	Sapayth 
Creation date 	: 	23-02-2021
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
echo load_html_head_contents('Category And Line Wise Total NPT Report', '../../', 1, 1, $unicode, '', '');
?>	
<script>
	var permission='<?php echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	function generate_report(type) {
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to', 'Comapny Name*Date Form*Date To') == false) {
			return;
		}

		/*var cbo_company_id = $('#cbo_company_id').val();
		var cbo_location_id = $('#cbo_location_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		var cbo_line_id = $('#cbo_line_id').val();
		var txt_date_from = $('#txt_date_from').val();*/

        freeze_window(3);
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*txt_date_from*txt_date_to', "../../");
		
		http.open("POST", "requires/category_and_line_wise_total_npt_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generated_reponse;
	}
    
	function generate_report2(type) {
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to', 'Comapny Name*Date Form*Date To') == false) {
			return;
		}

		/*var cbo_company_id = $('#cbo_company_id').val();
		var cbo_location_id = $('#cbo_location_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		var cbo_line_id = $('#cbo_line_id').val();
		var txt_date_from = $('#txt_date_from').val();*/

        freeze_window(3);
		var data = "action=report_generate2" + get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*txt_date_from*txt_date_to', "../../");
		
		http.open("POST", "requires/category_and_line_wise_total_npt_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generated_reponse;
	}
    function generate_report3(type) {
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to', 'Comapny Name*Date Form*Date To') == false) {
			return;
		}

		/*var cbo_company_id = $('#cbo_company_id').val();
		var cbo_location_id = $('#cbo_location_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		var cbo_line_id = $('#cbo_line_id').val();
		var txt_date_from = $('#txt_date_from').val();*/

        freeze_window(3);
		var data = "action=report_generate3" + get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*txt_date_from*txt_date_to', "../../");
		
		http.open("POST", "requires/category_and_line_wise_total_npt_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generated_reponse;
	}

	function report_generated_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split("####");
			$('#report-container').html(response[0]);
            document.getElementById('report_container2').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
    function new_window() {
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report-container').innerHTML+'</body</html>'); 
        d.close();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='300px';
    }	
</script>
</head>
<body>
    <div style="width:100%;"> 
	<?php echo load_freeze_divs('../../', $permission); ?>
	<h3 style="width:950px; margin: 0 auto;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:950px; margin: 0 auto;">  
    
    <form name="search_form_1" id="search_form_1" autocomplete="off" >    
            <fieldset>  
                
                <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="160" class="must_entry_caption">Company</th>
                        <th width="160">Location</th>
                        <th width="100">Floor</th>
                        <th width="100">Line</th>
                        <th class="must_entry_caption"th width="200">Date</th>
                        <th>
                        	<input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('search_form_1','','','','')" />
                        </th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?php 
									echo create_drop_down( 'cbo_company_id', 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', $selected, "load_drop_down( 'requires/category_and_line_wise_total_npt_report_controller', this.value, 'load_drop_down_location', 'location_td');get_php_form_data(this.value, 'print_button_variable_setting', 'requires/category_and_line_wise_total_npt_report_controller');", '', '', '', '', '', 2);
                                ?>                            
                            </td>
                            <td id="location_td">
                                <?php 
									echo create_drop_down( 'cbo_location_id', 160, $blank_array, '', 1, '--All--', $selected, '', '', '', '', '', '', 2);
                                ?>
                            </td>
                            <td id="floor_td">
                                <?php
                                    echo create_drop_down( 'cbo_floor_id', 100, $blank_array, '', 1, '--All--', '', '' );
                                ?>
                            </td>
                            <td id="line_td">
                                <?php
                                    echo create_drop_down( 'cbo_line_id', 100, $blank_array, '', 1, '--All--', '', '' );
                                ?>
                            </td>
                           	<td width="200">
                               <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" readonly />&nbsp; To
                               <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" readonly />
                    
                            </td>
                        <td>
                            <input type="button" name="search" id="show111111" value="Show" onClick="generate_report(1)" style="width:100px; display:none;" class="formbutton" />
                            <input type="button" name="search" id="show222222" value="Show2" onClick="generate_report2(2)" style="width:100px; display:none;" class="formbutton" />
                            <input type="button" name="search" id="show333333" value="Show3" onClick="generate_report3(3)" style="width:100px; display:none;" class="formbutton" />
                        </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </form> 
       
    </div>
    <!-- <div style="display:none" id="data_panel"></div>    -->
    <div id="report_container2" align="center" style="padding: 20px 10px;"></div>
    <div id="report-container"></div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>