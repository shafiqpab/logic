<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Procurement Report

Functionality	:
JS Functions	:
Created by		:	Zaman
Creation date 	: 	18-10-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
	header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Procurement Report","../../../", 1, 1, $unicode,1,'');
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_name*cbo_receive_basis*cbo_receive_purpose*cbo_issue_purpose*cbo_store_name*txt_date_from*txt_date_to','Company Name*Receive Basis*Receive Purpose*Issue Purpose*store Name*Date From*Date To')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_receive_basis*cbo_receive_purpose*cbo_issue_purpose*cbo_store_name*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/monthly_yarn_store_statement_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML="<a style='text-decoration:none' id='dlink'><input type='button' value='Convert To Excel' name='excel' id='excel' onclick='exportToExcel();' class='formbutton' style='width:155px'/></a>"+"&nbsp;&nbsp;&nbsp;<input type='button' onclick='new_window()' value='HTML Preview' name='Print' class='formbutton' style='width:100px'/>";
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}

	function tableToExcels()
	{
		document.getElementById("report_container2").innerHTML = tableToExcel();
	}

	function exportToExcel()
	{
        var tableData = document.getElementById("report_container2").innerHTML;        
        var data_type = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }

        var ctx = {
            worksheet: 'Worksheet',
            table: tableData
        }
        document.getElementById("dlink").href = data_type + base64(format(template, ctx));
        document.getElementById("dlink").traget = "_blank";
        document.getElementById("dlink").click();
    }
	
	function func_onchange_company()
	{
		//alert('su..re');
		var company_id = $('#cbo_company_name').val();
		//for issue purpose
		load_drop_down( 'requires/monthly_yarn_store_statement_controller', company_id, 'load_drop_down_issue_purpose', 'tdIssuePurpose' );
		set_multiselect('cbo_issue_purpose','0','0','','0');
		
		//for store
		load_drop_down( 'requires/monthly_yarn_store_statement_controller', company_id, 'load_drop_down_store', 'tdStore' );
		set_multiselect('cbo_store_name','0','0','','0');

	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:1020px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:1020px;">
						<table class="rpt_table" width="1020" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th width="150" class="must_entry_caption">Company</th>
									<th width="150" class="must_entry_caption">Receive Basis</th>
									<th width="150" class="must_entry_caption">Receive Purpose</th>
									<th width="150" class="must_entry_caption">Issue Purpose</th>
                                    <th width="150" class="must_entry_caption">Store</th>
									<th class="must_entry_caption">Date Range</th>
									<th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td id="tdCompany">
									<?
									echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", "", "", $selected, "" );
									?>
								</td>
                                <td>
									<?
                                    echo create_drop_down( "cbo_receive_basis", 150, $receive_basis_arr,"", "0", "", $selected, "","","1,2,4");
									?>
								</td>
                                <td>
                                	<?
                                    echo create_drop_down( "cbo_receive_purpose", 150, $yarn_issue_purpose,"", "0", "", "", "", "","2,5,6,7,12,15,16,38,43,46,50,51");
									?>
								</td>
								<td id="tdIssuePurpose">
									<?
									echo create_drop_down("cbo_issue_purpose", 150, $blank, "", "0", "", $selected, "", "", "","","","");
									?>
								</td>
								<td id="tdStore">
									<?
									echo create_drop_down("cbo_store_name", 150, $blank, "", "0", "", $selected, "", "", "","","","");
									//echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 0, "", 0, "" );
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly />&nbsp;&nbsp;To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
								</td>
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
								</td>
							</tr>
							<!--<tr>
								<td colspan="6" align="center"><?// echo load_month_buttons(1); ?></td>
							</tr>-->
						</table>
					</fieldset>
				</div>
			</div>
			<br />
			<!-- Result Contain Start-->
			<div id="report_container" align="center"></div>
			<div id="report_container2" style="margin-left:5px"></div>
			<!-- Result Contain END-->
		</form>
	</div>
</body>
<script>
set_multiselect('cbo_company_name*cbo_receive_basis*cbo_receive_purpose*cbo_issue_purpose*cbo_store_name','0*0*0*0*0','0*0*0*0*0','','0*0*0*0*0');
setTimeout[($("#tdCompany").attr("onclick","func_onchange_company();"),3000)];
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>