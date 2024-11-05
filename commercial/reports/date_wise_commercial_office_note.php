<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date wise Commercial Office Note Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	28-08-2021
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
echo load_html_head_contents('Date wise Commercial Office Note', '../../', 1, 1, $unicode, 1, '', '');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';


	function generate_report(type)
	{		

		var txt_search_common = $("#txt_search_common").val();
		if (txt_search_common != '')
		{
			if(form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}
		}	
			
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_item_category_id*cbo_search_by*txt_search_common*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
	
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/date_wise_commercial_office_note_controller.php",true);
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
			setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}

	function search_NoteLcPiNumber_function(id)
	{
		$('#txt_search_common').val('');
		if (id==1) $("#search_by_notelcpi_td").html('Office Note No');
		else if (id==2) $("#search_by_notelcpi_td").html('PI Number');
		else $("#search_by_notelcpi_td").html('LC Number');
	}


</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="officenotereport1" name="officenotereport1">
			<div style="width:850px;">
				<h3 align="left" id="accordion_h1" style="width:850px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:850px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="850" border="1" rules="all">
							<thead>
								<th width="180" class="must_entry_caption">Company Name</th>
								<th width="150">Item Category</th>
								<th width="120">Search By</th>
								<th width="120" id="search_by_notelcpi_td"><? echo "Office Note No"; ?></th>
								<th width="180" class="must_entry_caption">Office Note Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('officenotereport1','report_container*report_container2','','','')" /></th>
							</thead>
							<tbody>
								<tr>
									<td>
										<?
										echo create_drop_down('cbo_company_id', 180,"select id, company_name from lib_company where status_active=1 and is_deleted=0 and core_business in(1,3) order by company_name","id,company_name", 0, '',$selected, '');
										?>
									</td>

									<td>
		                                <? 
		                                echo create_drop_down('cbo_item_category_id', 150, $item_category, '', 0, '', $selected, '', '', '', '', '', '');
		                                ?> 
		                            </td>
									<td>
										<?
										$serch_by_arr = array(1=>'Office Note No', 2=>'PI Number', 3=>'LC Number');
                            			echo create_drop_down("cbo_search_by", 120, $serch_by_arr, '', '', '', 1, 'search_NoteLcPiNumber_function(this.value);', 0);
										?>  
									</td>
									<td align="center">
		                                <input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write"/>
		                            </td>
									<td id="date_field">
										&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
									</td>									
									<td align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton"/>
									</td>
								</tr>
								<tr>
									<td colspan="6" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
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
<script>set_multiselect('cbo_company_id*cbo_item_category_id','0*0','0*0','','0*0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
