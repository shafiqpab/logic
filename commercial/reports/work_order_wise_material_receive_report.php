<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Order Wise Material Receive Report
Functionality	:	
JS Functions	:
Created by		:	Zayed
Creation date 	: 	05-03-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Work Order Wise Material Receive Report", "../../", 1, 1, '', '', '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function generate_report(rep_type)
	{
		if (form_validation('cbo_company_name*cbo_wo_type*txt_wo_no', 'Company Name*WO Type* Wo number') == false){
			return;
		}

		var report_title = $("div.form_caption").html();
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_wo_type*txt_wo_no*cbo_job_year', "../../") + '&report_title=' + report_title;
		// alert(data);return;
		freeze_window(3);
		http.open("POST", "requires/work_order_wise_material_receive_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if (http.readyState == 4) {
			var response = trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);

			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
            setFilterGrid("table_body",-1);
            setFilterGrid("table_body2",-1);
            setFilterGrid("table_body3",-1);
            setFilterGrid("table_body4",-1);
		}
	}

	function new_window()
	{
        document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:nth-child(1)').css('display','none');
        document.getElementById('scroll_body2').style.overflowY="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		$('#table_body2 tr:nth-child(1)').css('display','none');
        document.getElementById('scroll_body3').style.overflowY="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
		$('#table_body3 tr:nth-child(1)').css('display','none');
        document.getElementById('scroll_body4').style.overflowY="auto";
		document.getElementById('scroll_body4').style.maxHeight="none";
		$('#table_body4 tr:nth-child(1)').css('display','none');

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>'); // media="print"
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:nth-child(1)').css('display','');
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="250px";
		$('#table_body2 tr:nth-child(1)').css('display','');
		document.getElementById('scroll_body3').style.overflowY="scroll";
		document.getElementById('scroll_body3').style.maxHeight="250px";
		$('#table_body3 tr:nth-child(1)').css('display','');
		document.getElementById('scroll_body4').style.overflowY="scroll";
		document.getElementById('scroll_body4').style.maxHeight="250px";
		$('#table_body4 tr:nth-child(1)').css('display','');
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<form id="category_wise_monthly_budget_vs_requ_rpt">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", ''); ?>
			<h3 align="left" id="accordion_h1" style="width:620px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:620px;">
					<table class="rpt_table" width="620" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
						<thead>
							<tr>
								<th width="160" class="must_entry_caption">Company Name</th>
								<th width="140" class="must_entry_caption">WO Type</th>
								<th width="140"class="must_entry_caption">Work Order No</th>
								<th width="70">Year</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('category_wise_monthly_budget_vs_requ_rpt','report_container*report_container2','','','')" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_company_name", 160, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --");
									?>
								</td>
								<td align="center">
									<?
                                    $wo_type_arr = array(1 => 'Others Purchase Order', 2 => 'Stationary Purchase Order');
                                    echo create_drop_down("cbo_wo_type", 140, $wo_type_arr, "id,name", 1, "-- Select WO Type --"); 
                                    ?>
								</td>
                                <td>
                                    <input type="text" name="txt_wo_no" class="text_boxes" id="txt_wo_no" placeholder="Write" style="width:140px" />
                                </td>
                                <td align="center">
                                    <?
                                    $year_current = date("Y");
                                    echo create_drop_down("cbo_job_year", 70, $year, "", 1, "-Select-", $year_current);
                                    ?>
								</td>
								<td align="center">
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div style="margin-top:10px" id="report_container" align="center"></div>
		<div id="report_container2" align="center"></div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>