<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Receive WIP Report
Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	22-02-2021
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
echo load_html_head_contents("Batch Receive WIP Report", "../../../", 1, 1,'','','');
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
	var tableFilters_2 =
	{
		col_operation: {
		id: ["value_sbatch_qnty","value_stotal_trims_weight","value_sbatch_weight","value_s_roll_total"],
		col: [15,16,17,18],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

function fn_report_generated(operation)
{
	var fso_number=document.getElementById('fso_number_show').value;
	if(fso_number != "")
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if(form_validation('cbo_company_name*cbo_based_on*txt_date_from*txt_date_to','Company*Based On*From date Fill*To date Fill')==false)
		{
			return;
		}
	}
	freeze_window(5);
 	var data="action=generated_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*fso_number_show*fso_number*txt_date_from*txt_date_to*cbo_based_on',    "../../../");

	http.open("POST","requires/batch_receive_wip_report_controller.php",true);
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
			if(document.getElementById('table_body2'))
			{
				setFilterGrid("table_body2",-1,tableFilters_2);
			}


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

	/*function fsoNumber()
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/batch_receive_wip_report_controller.php?cbo_company_id=' + cbo_company_id + '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

 			emailwindow.onclose = function ()
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				alert (fso_id);
				var fso_id2=fso_id.split("**");
				alert (fso_id2);
				var fso_id3=fso_id2.split("#");
				alert (fso_id3);
				$('#fso_number_show').val(fso_no);
				$('#fso_number').val(fso_id);
 			}
 		}
 	}*/

 	function fsoNumber()
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/batch_receive_wip_report_controller.php?cbo_company_id=' + cbo_company_id + '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

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

 	function issue_roll_popup(issue_id, fso_id, dtls_id, action)
    {
        var popup_width = '';
        if (action == "roll_issue_popup")
        {
            popup_width = '400px';
        }
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_receive_wip_report_controller.php?issue_id=' + issue_id + '&action=' + action + '&fso_id=' + fso_id + '&dtls_id=' + dtls_id, 'Detail Veiw', 'width=' + popup_width + ', height=300px,center=1,resize=0,scrolling=0', '../');
    }

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:735px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:735px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer</th>
                            <th>Year</th>
                            <th>FSO No</th>
                            <th>Based On</th>
                            <th class="must_entry_caption">Grey Issue Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/batch_receive_wip_report_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
										echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
                                </td>
                                <td>
                                	<?
                                       echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", "", "",0,"" );
									?>
                                </td>
                                <td>
                                    <input type="text"  name="fso_number_show" id="fso_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Browse" onDblClick="fsoNumber();" readonly>
                                    <input type="hidden" name="fso_number" id="fso_number">
                                </td>
                                <td>
                                	<?
                                		$based_on = array('1' => 'Transaction Date', '2' => 'Insert Date' );
                                    	echo create_drop_down( "cbo_based_on", 65, $based_on,"", 1,"-- Select --", 1, "",0,"" );
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
                                <td align="center" colspan="12">
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