<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create  BEP Analysis Report[EVANCE]
Functionality	:	
JS Functions	:
Created by		:	MD. REAZ UDDIN 
Creation date 	: 	06 DEC, 2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Income Statement", "../../", 1, 1, $unicode, 1, '');
?>
<script>
	function generate_report(type) {
		if (form_validation('txt_company*txt_date_from*txt_date_to', 'Company Name*From Date*To Date') == false) {
			return;
		}

		freeze_window(16);


		var data = $('#cbo_company').val() + "__" + $('#txt_date_from').val() + "__" + $('#txt_date_to').val() + "__" + $('#exchange_rate').val();

		//2,'report_container','data_panel'
		show_list_view(data, 'generate_report', 'report_container2', 'requires/bep_analysis_report_controller', '');


		release_freezing();

		accordion_menu('accordion_h1', 'content_search_panel', '');
		$('#exl_rpt_link').attr('href', document.getElementById('txt_excl_link').value);
		var image_location = document.getElementById("txt_imagelocation").value;
		if (image_location != "") {
			document.getElementById("company_image_container").innerHTML = '<img height="100" width="100" src="' + image_location + '" />';
		}
		return;
	}



	function select_multiple_company() {

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/bep_analysis_report_controller.php?action=multiple_company_search', 'Company Search', 'width=330px,height=420px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]

			var sel_id = this.contentDoc.getElementById("txt_selected_id");
			var sel_name = this.contentDoc.getElementById("txt_selected");

			$('#txt_company').val('');
			$('#cbo_company').val('');

			document.getElementById('txt_company').value = sel_name.value;
			document.getElementById('cbo_company').value = sel_id.value;
			//load_drop_down( 'requires/bep_analysis_report_controller', "'"+$('#cbo_company').val()+"'", 'load_drop_down_acc_period', 'td_ac_year' );

		}
	}


	function view_html_report() {
		$('#company_desctiption_tr').css('display', 'table-row');
		var response = document.getElementById('report_container2').innerHTML;
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write(response);
		d.close();
		$('#company_desctiption_tr').css('display', 'none');
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
		<?php
		echo load_freeze_divs("../../", $permission);
		$tableWidth = 600;
		?>

		<h3 style="width:<? echo $tableWidth; ?>px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>

		<fieldset style="width:<? echo $tableWidth; ?>px">
			<div id="content_search_panel">
				<table class="rpt_table" rules="all" width="<? echo $tableWidth; ?>">
					<thead>
						<th class="must_entry_caption">Company</th>

						<th>Exchange Rate</th>
						<th>Date Range</th>
						<th width="200" colspan="2"><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
					</thead>
					<tr class="general">
						<td width="200" align="center">
							<input type="text" name="txt_company" id="txt_company" value="" onDblClick="select_multiple_company()" placeholder="Dbl. Click" class="text_boxes" style="width:170px" />
							<input type="hidden" name="cbo_company" id="cbo_company" value="" class="text_boxes" style="" />
						</td>
						<td width="100" align="center">
							<input type="text" name="exchange_rate" id="exchange_rate" value="86" class="text_boxes_numeric" style="width:90px" />
						</td>
						<td width="250" align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" value="<?php echo date('01-m-Y'); ?>"  />
							&nbsp;<b> To </b> &nbsp;
							<input type="text" name="txt_date_to" id="txt_date_to" value="<?php echo date('t-m-Y'); ?>" class="datepicker" style="width:75px">

						</td>

						<td width="150" align="center" colspan="2"><input type="button" name="show2" id="show2" onClick="generate_report();" class="formbutton" style="width:80px" value="Show" /></td>

					</tr>

				</table>
				<table width="<? echo $tableWidth; ?>">
					<tr>
						<td height="10" colspan="8" id="data_panel" align="left">
							<div align="center" style="margin-top:10px; margin-bottom:10px">
								<input type="button" id="reprt_html" onClick="view_html_report()" class="formbutton" value="HTML Preview">&nbsp;&nbsp;
								<a id="exl_rpt_link"><input type="button" id="reprt_excl" class="formbutton" value="Download Excel"></a>
							</div>
						</td>
					</tr>
				</table>
			</div>

		</fieldset>
		<div id="report_container2"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>