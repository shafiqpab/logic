<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Procurement Report

Functionality	:
JS Functions	:
Created by		:	Zaman
Creation date 	: 	20-03-2021
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
echo load_html_head_contents("Lot Wise Yarn Transaction","../../../", 1, 1, $unicode,1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../logout.php";

    //func_onDblClick_itemDescription
	function func_onDblClick_itemDescription()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('txt_item_description').value+"_"+document.getElementById('txt_product_id').value;
		//alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/lot_wise_yarn_transaction_v2_controller.php?action=actn_onDblClick_itemDescription&data='+data,'Item Description Popup', 'width=600px,height=400px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var hdn_product_id=this.contentDoc.getElementById("hdn_product_id").value; // product ID
			var hdn_item_description=this.contentDoc.getElementById("hdn_item_description").value; // product Description
			$("#txt_product_id").val(hdn_product_id);
			$("#txt_item_description").val(hdn_item_description);
		}
	}
	
	//func_generate_report
	function func_generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_item_description','Company Name*Item Description')==false )
		{
			return;
		}
		
		/*
		| if item description is not select
		| then date rage mandatory
		*/
		/*if($('#txt_product_id').val() == '')
		{
			if( form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
			{
				return;
			}
		}*/

		var report_title=$( "div.form_caption" ).html();
		//var data="action=generate_report"+get_submitted_data_string('cbo_company_id*txt_item_description*txt_product_id*cbo_method*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		var data="action=generate_report"+get_submitted_data_string('cbo_company_id*txt_item_description*txt_product_id',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/lot_wise_yarn_transaction_v2_controller.php",true);
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
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
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
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:400px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:400px;">
						<table class="rpt_table" width="400" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th width="150" class="must_entry_caption">Company</th>
									<th width="150" class="must_entry_caption">Item Description</th>
									<!--<th width="120" class="must_entry_caption">Method</th>
									<th class="must_entry_caption">Date Range</th>-->
									<th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td id="tdCompany">
									<?
									echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", "", "", '1', "" );
									?>
								</td>
                                <td>
                                    <input style="width:150px;" name="txt_item_description" id="txt_item_description" class="text_boxes" onDblClick="func_onDblClick_itemDescription()" placeholder="Browse" readonly />
                                    <input type="hidden" name="txt_product_id" id="txt_product_id" style="width:90px;"/>
                                </td>
                                <!--<td>
									<?
                                    echo create_drop_down("cbo_method", 120, $store_method, "", 1, "Weighted Average", $selected, "", "", "");
                                    ?>
                                </td>								
                                <td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly />&nbsp;&nbsp;To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
								</td>-->
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="func_generate_report(1)" style="width:60px;" class="formbutton" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>