<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Wise Item Stock Ledger

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	15-10-2019
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
echo load_html_head_contents("Store Wise Item Stock Report","../../../", 1, 1, $unicode);
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters1 = 
	{
		//col_0: "none",col_1: "select",display_all_text: " -- All --",col_3: "select",display_all_text: " -- All --",col_6: "select",display_all_text: " -- All --",
		col_50: "none",
		col_operation: { 
			id: ["value_grp_total_qnty","value_grp_total_value"],
			//fixed_headers: true,  
			col: [7,8],
			//decimal_precision: [2,2],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"],
		}
	}
	
	function generate_report(type)
	{
		if( form_validation('txt_date_from','Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category = $("#cbo_item_category").val();
		var cbo_item_group = $("#cbo_item_group").val();
		var txt_description_id 	= $("#txt_description_id").val();
		var cbo_store 	= $("#cbo_store").val();
		var txt_date_from 	= $("#txt_date_from").val();
		var value_with 	= $("#cbo_value_with").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_category="+cbo_item_category+"&cbo_item_group="+cbo_item_group+"&txt_description_id="+txt_description_id+"&cbo_store="+cbo_store+"&txt_date_from="+txt_date_from+"&value_with="+value_with+"&type="+type+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
		//alert(dataString);return;

		freeze_window(3);
		http.open("POST","requires/store_wise_item_stock_report_controller.php",true);
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
			}
			//*/
			//$("#store_total_1,#store_total_2,#store_total_3,#store_total_4,#grp_total_qnty,#grp_total_value").css("font-weight","bold")
			setFilterGrid("table_body",-1,tableFilters1);
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

	function openmypage_description()
	{
		var txt_description_id = $("#txt_description_id").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category = $("#cbo_item_category").val();
		var cbo_item_group = $("#cbo_item_group").val();
		if( form_validation('txt_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=description_popup&cbo_company_name='+cbo_company_name+'&cbo_item_category='+cbo_item_category+'&cbo_item_group='+cbo_item_group+'&txt_description_id='+txt_description_id, 'Description Details', 'width=510px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_description").val(selected_name);
			$("#txt_description_id").val(selected_id);
		}
	}
	
	function openmypage_company()
	{
		var txt_company_name = $("#txt_company_name").val()
		var cbo_company_name = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=company_popup&cbo_company_name='+cbo_company_name, 'Company Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_company_name").val(selected_name);
			$("#cbo_company_name").val(selected_id);
		}
	}
	
	function openmypage_category()
	{
		var txt_item_category = $("#txt_item_category").val()
		var cbo_item_category = $("#cbo_item_category").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=category_popup&cbo_item_category='+cbo_item_category, 'Category Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_item_category").val(selected_name);
			$("#cbo_item_category").val(selected_id);
		}
	}
	
	function openmypage_group()
	{
		var txt_item_group = $("#txt_item_group").val()
		var cbo_item_group = $("#cbo_item_group").val();
		var cbo_item_category = $("#cbo_item_category").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=item_group_popup&cbo_item_category='+cbo_item_category+'&cbo_item_group='+cbo_item_group, 'Group Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_item_group").val(selected_name);
			$("#cbo_item_group").val(selected_id);
		}
	}
	
	function openmypage_store()
	{
		var txt_store = $("#txt_store").val()
		var cbo_store = $("#cbo_store").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category = $("#cbo_item_category").val();
		if( form_validation('txt_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=store_popup&cbo_company_name='+cbo_company_name+'&cbo_item_category='+cbo_item_category+'&cbo_store='+cbo_store, 'Store Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_store").val(selected_name);
			$("#cbo_store").val(selected_id);
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
									<th width="160">Company</th>
									<th width="160">Item Category</th>
									<th width="160">Item Group</th>
									<th width="160">Item Name</th>
									<th width="160">Store</th>
									<th>Value</th>
									<th class="must_entry_caption">Date</th>
									<th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td>
                                    <input type="text" id="txt_company_name" name="txt_company_name" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_company();" placeholder="Browse" readonly />
									<input type="hidden" id="cbo_company_name" name="cbo_company_name" />
								</td>
								<td>
                                    <input type="text" id="txt_item_category" name="txt_item_category" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_category();" placeholder="Browse" readonly />
									<input type="hidden" id="cbo_item_category" name="cbo_item_category" />
								</td>
								<td>
                                     <input type="text" id="txt_item_group" name="txt_item_group" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_group();" placeholder="Browse" readonly />
									<input type="hidden" id="cbo_item_group" name="cbo_item_group" />
								</td>
								<td>
									<input type="text" id="txt_description" name="txt_description" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_description();" placeholder="Browse" readonly />
									<input type="hidden" id="txt_description_id" name="txt_description_id" />
								</td>
								<td>
									 <input type="text" id="txt_store" name="txt_store" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_store();" placeholder="Browse" readonly />
									<input type="hidden" id="cbo_store" name="cbo_store" />
								</td>
								<td>
									<?
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
									?>
								</td>
								<td>
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
								</td>
								<td>
									<input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
								</td>
							</tr>
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
<script>
	//$("#cbo_value_with").val(1);
</script>
</html>
