<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Import Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	20-10-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

//--------------------------------------------------------------------------------
echo load_html_head_contents('Import Report', '../../', 1, 1, $unicode, 1, '', '');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

 	function openmypage_supplier()
	{
	    var company = $("#cbo_company_id").val();
	    var category = $("#cbo_item_category_id").val();

	    if(form_validation('cbo_company_id*cbo_item_category_id','Company Name*Item Category')==false)
	    {
	        return;
	    }

	    var title = 'Supplier List';
	    var page_link = 'requires/import_report_controller.php?action=supplier_list_popup'+'&company='+company+'&category='+category;
	    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=320px,center=1,resize=1,scrolling=0','');

	    emailwindow.onclose=function()
	    {
	        var theform=this.contentDoc.forms[0];
	        var txt_selected_id=this.contentDoc.getElementById("hidden_supplier_id").value;
	        var txt_selected_name=this.contentDoc.getElementById("hidden_supplier_name").value;
	        $("#cbo_supplier_id").val(txt_selected_id);
	        $("#cbo_supplier_name").val(txt_selected_name);
	    }
	}


	function generate_report(type)
	{		
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		if (cbo_supplier_name=='') $("#cbo_supplier_id").val('');
		
		var cbo_based_on = $("#cbo_based_on").val();
		var txt_search_common = $("#txt_search_common").val();
		if (txt_search_common != '')
		{
			if(form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}

			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_issue_banking*cbo_item_category_id*cbo_lc_type_id*cbo_search_by*txt_search_common*cbo_supplier_id*cbo_based_on","../../")+'&report_title='+report_title;
		}	
		else if (cbo_based_on==1 || cbo_based_on==3 || cbo_based_on==5 || cbo_based_on==11)
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}

			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_issue_banking*cbo_item_category_id*cbo_lc_type_id*cbo_search_by*txt_search_common*cbo_supplier_id*cbo_based_on*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;	
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date','Company Name*Date')==false)
			{
				return;
			}

			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_issue_banking*cbo_item_category_id*cbo_lc_type_id*cbo_search_by*txt_search_common*cbo_supplier_id*cbo_based_on*txt_date","../../")+'&report_title='+report_title;
		}
		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/import_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
		document.getElementById('scroll_body').style.maxHeight='none';
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY='scroll';
		document.getElementById('scroll_body').style.maxHeight='300px';
	}

	function search_LcPiNumber_function(id) 
	{
		if (id==1) 
		{
			$('#txt_search_common').val('');
			$("#search_by_lcpi_td").html('PI Number');			
		}
		else 
		{
			$('#txt_search_common').val('');
			$("#search_by_lcpi_td").html('L/C Number');
		}
	}

	function search_date_function(id) 
	{
		if (id==1) {
			$("#search_by_date_td").html('PI Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">');
			datepicker_();
		} else if (id==2) {
			$("#search_by_date_td").html('PI Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date" id="txt_date" class="datepicker" style="width:110px" value="<?= date('d-m-Y'); ?>" placeholder="Date" readonly>');			
		} else if (id==3) {
			$("#search_by_date_td").html('LC Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">');
			datepicker_();
		} else if (id==4) {
			$("#search_by_date_td").html('LC Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date" id="txt_date" class="datepicker" style="width:110px" value="<?= date('d-m-Y'); ?>" placeholder="Date" readonly>');			
		} else if (id==5) {
			$("#search_by_date_td").html('Acceptance Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">');
			datepicker_();
		} else if (id==6) {
			$("#search_by_date_td").html('Acceptance Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date" id="txt_date" class="datepicker" style="width:110px" value="<?= date('d-m-Y'); ?>" placeholder="Date" readonly>');			
		} else if (id==11) {
			$("#search_by_date_td").html('Payment Date');
			$("#search_by_date_td").css("color", "Blue");
			$("#date_field").html('&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">');
			datepicker_();
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="importreport1" name="importreport1">
			<div style="width:1240px;">
				<h3 align="left" id="accordion_h1" style="width:1240px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:1240px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="1230" border="1" rules="all">
							<thead>
								<th width="130" class="must_entry_caption">Company Name</th>
								<th width="100">Issuing Bank</th>
								<th width="130">Item Category</th>
								<th width="100">L/C Type</th>
								<th width="90">Search By</th>
								<th width="100" id="search_by_lcpi_td"><? echo "L/C Number"; ?></th>
								<th width="80">Supplier</th>
								<th width="240">Based On</th>
								<th width="130" id="search_by_date_td" class="must_entry_caption"><? echo "PI Date"; ?></th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('importreport1','report_container*report_container2','','','')" /></th>
							</thead>
							<tbody>
								<tr>
									<td>
										<?
										echo create_drop_down('cbo_company_id', 130,"select id, company_name from lib_company where status_active=1 and is_deleted=0 and core_business in(1,3) order by company_name","id,company_name", 1, '-- Select Company --',$selected, '');
										?>
									</td>
									<td>
										<?
										echo create_drop_down('cbo_issue_banking', 100, "select id, (bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0 order by bank_name","id,bank_name", 1, '--Select Bank--', $selected, '');
										?>						
									</td>
									<td id="cat_td">
		                                <? 
		                                echo create_drop_down('cbo_item_category_id', 150, $item_category, '', 0, '', $selected, '', '', '', '', '', '');
		                                ?> 
		                            </td>
									<td>
										<?
										echo create_drop_down('cbo_lc_type_id', 100, $lc_type, '', 1, '--Select LC Type--', 0, '', 0);
										?>
									</td>
									<td>
										<?
										$serch_by_arr = array(1=>'PI Number', 2=>'L/C Number');
                            			echo create_drop_down("cbo_search_by", 90, $serch_by_arr, '', '', '', 2, 'search_LcPiNumber_function(this.value);', 0);
										?>  
									</td>
									<td align="center">
		                                <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write"/>
		                            </td>
									<td>										   
                        				<input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes" style="width:80px;" placeholder="Browse" onDblClick="openmypage_supplier()"/>  
                              			<input type="hidden" name="cbo_supplier_id" id="cbo_supplier_id"/>
									</td>
									<td>
										<?
										$based_on_arr = array(
											1=>'Import PI Report -Periodical', 
											2=>'Import PI Report -as on dated',
											3=>'Import LC Liability Report -Periodical',
											4=>'Import LC Liability Report -as on dated',
											5=>'Import Acceptance Liability Report -Periodical',
											6=>'Import Acceptance Liability Report -as on dated',
											/*7=>'EDF Liability Report -Periodical',
											8=>'EDF Liability Report -as on dated',
											9=>'UPAS Liability Report -Periodical',
											10=>'UPAS Liability Report -as on dated',*/
											11=>'Import Payment -Periodical'
										);
                            			echo create_drop_down("cbo_based_on", 240, $based_on_arr, '', '', '', 1, 'search_date_function(this.value);', 0);
										?> 
									</td>
									<td id="date_field">
										&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">
									</td>									
									<td align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton"/>
									</td>
								</tr>
								<tr>
									<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
								</tr>
							</tbody>
						</table>
						<br/>
					</fieldset>
				</div>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script>set_multiselect('cbo_item_category_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
