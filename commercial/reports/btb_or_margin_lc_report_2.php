<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create BTB or Margin LC Report 2.
Functionality	:
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	07-10-2023
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
echo load_html_head_contents('BTB or Margin LC Report 2', '../../', 1, 1, $unicode, 1, '', '');
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
	    var page_link = 'requires/btb_or_margin_lc_report_2_controller.php?action=supplier_list_popup'+'&company='+company+'&category='+category;
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
		if(form_validation('cbo_company_id*cbo_item_category_id','Company Name*Catagory')==false)
		{
			return;
		}
		var txt_btb_lc = $("#txt_btb_lc").val();
		if(txt_btb_lc==""){			
			if(form_validation('cbo_company_id*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Catagory*From Date*To Date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_item_category_id*cbo_lc_type_id*cbo_supplier_id*txt_btb_lc*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/btb_or_margin_lc_report_2_controller.php",true);
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
		// document.getElementById('scroll_body').style.overflow='auto';
		// document.getElementById('scroll_body').style.maxHeight='none';
		//$('#table_body tbody').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		//$('#table_body tbody').find('tr:first').show();
		// document.getElementById('scroll_body').style.overflowY='scroll';
		// document.getElementById('scroll_body').style.maxHeight='300px';
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


</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="importreport1" name="importreport1">
			<div style="width:940px;">
				<h3 align="left" id="accordion_h1" style="width:940px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:940px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="930" border="1" rules="all">
							<thead>
								<th width="130" class="must_entry_caption">Company Name</th>		
								<th width="130">Item Category</th>
								<th width="100">L/C Type</th>
								<th width="80">Supplier</th>
								<th width="100">BTB LC NO</th>		
								<th width="130" class="must_entry_caption"><? echo "LC Date Range"; ?></th>

								<th width="130"> <input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('importreport1','report_container*report_container2','','','')" /></th>
							</thead>
							<tbody>
								<tr>
									<td>
										<?
										echo create_drop_down('cbo_company_id', 130,"select id, company_name from lib_company where status_active=1 and is_deleted=0 and core_business in(1,3) order by company_name","id,company_name", 1, '-- Select Company --',$selected, '');
										?>
									</td>
							
									<td id="cat_td">		                             
                                        <? echo create_drop_down( "cbo_item_category_id", 150, "select category_id, short_name from  lib_item_category_list where status_active=1 and category_id in(1,2,5,6,23,4) and is_deleted=0 order by short_name","category_id,short_name", 1, "-- Select Debit Note --","","","","", "",1); 
                                        ?>
		                            </td>
									<td>
										<?
										echo create_drop_down('cbo_lc_type_id', 100, $lc_type, '', 1, '--Select LC Type--', 0, '', 0);
										?>
									</td>
						
								
									<td>										   
                        				<input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes" style="width:80px;" placeholder="Browse" onDblClick="openmypage_supplier()"/>  
                              			<input type="hidden" name="cbo_supplier_id" id="cbo_supplier_id"/>
									</td>

                                    <td align="center">
		                                <input type="text" style="width:100px" class="text_boxes" name="txt_btb_lc" id="txt_btb_lc" placeholder="Write"/>
		                            </td>
								
									<td id="date_field">
										&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date">
									</td>									
									<td  align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton"/>
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
<script>set_multiselect('cbo_company_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
