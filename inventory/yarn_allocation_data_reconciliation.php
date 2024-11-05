<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Item Inquiry

Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	24-08-2015
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST); 
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn allocation data Reconciliation","../", 1, 1, $unicode);
?>
<style type="text/css">
table { width: 100%; margin: auto; font-family: arial; font-size: 12px; }
table tr td{ background-color: #E9F3FF; }
.global { background-color: rgba(52,168,83,.5) }
.new { background-color: rgba(52,168,83,.5); }
.border{border-top: 1px solid; background-color: #fff !important; font-weight: bold; font-size: 12px; }
thead th{  background-color: #ccc !important; }
.dyed{ background-color: firebrick; color: #fff; }
</style>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_value_to','Company Name*From Value*To Value') == false )
		{
			return;
		}


		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_product_id*txt_value_from*txt_value_to',"../");
		freeze_window(3);
		http.open("POST","requires/yarn_allocation_data_reconciliation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("**");
			$("#report_container2").html(response[0]);
			show_msg('3');
			release_freezing();
		}
	}

	function synchronize_allocation()
	{
		if(confirm("Are you sure?")){
			var company_id = $("#cbo_company_id").val();
			var product_id = $("#txt_product_id").val();
			var txt_value_from = $("#txt_value_from").val();
			var txt_value_to = $("#txt_value_to").val();
			var data="action=synchronize_allocation&product_id="+product_id+"&company_id="+company_id+"&txt_value_from="+txt_value_from+"&txt_value_to="+txt_value_to;
			freeze_window(3);
			http.open("POST","requires/yarn_allocation_data_reconciliation_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_synchronize_stock_reponse;
		}
	}

	function fn_synchronize_stock_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("**");
			show_msg(response[0]);
			release_freezing();
			generate_report();
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../",$permission);  ?>
		<form name="storeItemInquiry_1" id="storeItemInquiry_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:530px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:530px;">
						<table class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th class="must_entry_caption">Company</th>
									<th class="">Product Id</th>
									<th class="must_entry_caption">Value Range</th>
									<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
									?>
								</td>
								<td>
									<input type="text" id="txt_product_id" name="txt_product_id" class="text_boxes_numeric" style="width:80px" onDblClick="openmypage(5,this.id);" placeholder="Write" />
								</td>
								<td>
									<input type="text" id="txt_value_from" name="txt_value_from" class="text_boxes_numeric" style="width:30px" placeholder="From" />
									<input type="text" id="txt_value_to" name="txt_value_to" class="text_boxes_numeric" style="width:30px" placeholder="To" />
								</td>
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<br />
			<div id="report_container2" style="margin-left:5px"></div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
