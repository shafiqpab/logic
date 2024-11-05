<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Procurement Report

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	05-05-2019
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
echo load_html_head_contents("Yarn Procurement Report","../../../", 1, 1, $unicode,1,'');
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters1 = 
	{
		col_0: "none",col_1: "select",display_all_text: " -- All --",col_3: "select",display_all_text: " -- All --",col_6: "select",display_all_text: " -- All --",
		col_operation: { 
			id: ["store_total_1","store_total_2","store_total_3","store_total_4","grp_total_qnty","grp_total_value"],
			fixed_headers: true,  
			col: [7,8,9,10,11,12],
			decimal_precision: [2,2,2,2,2,2],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"],
		}
	}
	var tableFilters2 = 
	{
		col_0: "none",col_1: "select",display_all_text: " -- All --",col_3: "select",display_all_text: " -- All --",col_6: "select",display_all_text: " -- All --",
		col_operation: { 
			id: ["store_total_1","store_total_2","store_total_3","store_total_4","store_total_5","store_total_6","store_total_7","store_total_8","store_total_9","store_total_10","store_total_11","store_total_12","store_total_13","store_total_114","store_total_15","store_total_16","grp_total_qnty","grp_total_value"],
			col: [7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"],
		}
	}		

	function generate_report(type)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date From*Date To')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_yarn_count*cbo_composition*cbo_yarn_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_procurement_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			//var reponse=trim(http.responseText).split("**");
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);

			document.getElementById('report_container').innerHTML="<a style='text-decoration:none' id='dlink'><input type='button' value='Convert To Excel' name='excel' id='excel' onclick='exportToExcel();'  class='formbutton' style='width:155px'/></a>"+"&nbsp;&nbsp;&nbsp;<input type='button' onclick='new_window()' value='HTML Preview' name='Print' class='formbutton' style='width:100px'/>";

			//var cbo_company_name = $("#cbo_company_name").val();
			/*if(cbo_company_name==0){
				var tableName=tableFilters2;
			}else{
				var tableName=tableFilters1;
			}*/
			//setFilterGrid("table_body",-1,tableName);//,tableFilters2
			//$("#store_total_1,#store_total_2,#store_total_3,#store_total_4,#grp_total_qnty,#grp_total_value").css("font-weight","bold")
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}

	$(document).ready(function()
	{
		$('#txt_composition').bind('copy paste cut',function(e) {
			e.preventDefault();
		});
	});

	function openmypage_composition()
	{
		var pre_composition_id = $("#txt_composition_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_procurement_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var composition_des=this.contentDoc.getElementById("hidden_composition").value;
			var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
			$("#txt_composition").val(composition_des);
			$("#txt_composition_id").val(composition_id);
		}
	}
	function tableToExcels()
	{
		document.getElementById("report_container2").innerHTML = tableToExcel();
	}

	function exportToExcel() {
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

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:1000px;">
						<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th width="160" class="must_entry_caption">Company</th>
									<th width="160">Count</th>
									<th width="200">Composition</th>
                                    <th width="160">Yarn Type</th>
									<th class="must_entry_caption">Date Range</th>
									<th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td>
									<?
									echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "" );
									?>
								</td>
                                <td>
									<?
									echo create_drop_down("cbo_yarn_count",150,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "", $selected, "");
									?>
								</td>
                                <td>
                                	<?
									echo create_drop_down("cbo_composition",180,$composition,"",0, "", $selected, "");
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_yarn_type",150,$yarn_type,"",0, "", $selected, "");
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:60px" disabled readonly />&nbsp;&nbsp;To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:60px" readonly/>
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
set_multiselect('cbo_yarn_count*cbo_composition*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
