<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Wise Yarn Stock Ledger

Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	27-12-2018
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
echo load_html_head_contents("Store Wise Yarn Stock Report","../../../", 1, 1, $unicode,1,1);
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
		if( form_validation('txt_date_from','Date')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_dyed_type = $("#cbo_dyed_type").val();
		var cbo_yarn_type = $("#cbo_yarn_type").val();
		var txt_count 	= $("#cbo_yarn_count").val();
		var txt_lot_no 	= $("#txt_lot_no").val();
		var from_date 	= $("#txt_date_from").val();
		var value_with 	= $("#cbo_value_with").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var txt_composition = $("#txt_composition").val();
		var txt_composition_id = $("#txt_composition_id").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();

		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");	
			$("#txt_qnty").focus();
			return;
		}

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&txt_count="+txt_count+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&value_with="+value_with+"&cbo_supplier="+cbo_supplier+"&type="+type+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty;
		var data="action=generate_report"+dataString;

		freeze_window(3);
		http.open("POST","requires/store_wise_yarn_stock_report_controller.php",true);
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

			document.getElementById('report_container').innerHTML="<a style='text-decoration:none' id='dlink'><input type='button' value='Convert To Excel' name='excel' id='excel' onclick='exportToExcel();'  class='formbutton' style='width:155px'/></a>"<!--+"&nbsp;&nbsp;&nbsp;<input type='button' onclick='new_window()' value='HTML Preview' name='Print' class='formbutton' style='width:100px'/>"-->;

			var cbo_company_name = $("#cbo_company_name").val();
			if(cbo_company_name==0){
				var tableName=tableFilters2;
			}else{
				var tableName=tableFilters1;
			}
			setFilterGrid("table_body",-1);//,tableFilters2
			$("#store_total_1,#store_total_2,#store_total_3,#store_total_4,#grp_total_qnty,#grp_total_value").css("font-weight","bold")
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".fltrow").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$(".fltrow").show();
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_yarn_stock_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

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

	function exportToExcel()
	{
		$(".fltrow").hide();
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
		$(".fltrow").show();
    }

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
			<div style="width:100%;" align="center">
				<h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:1200px;">
						<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th>Company</th>
									<th>Supplier</th>
									<th>Dyed Type</th>
									<th>Yarn Type</th>
									<th>Count</th>
									<th>Composition</th>
									<th>Lot</th>
									<th>Value</th>
									<th>Get Upto</th>
									<th>Qty.</th>
									<th class="must_entry_caption">Date</th>
									<th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr class="general">
								<td>
									<?
									echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/store_wise_yarn_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data( this.value, 'eval_multi_select', 'requires/store_wise_yarn_stock_report_controller' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/store_wise_yarn_stock_report_controller' );" );
									?>
								</td>
								<td id="supplier">
									<?

									echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
									?>
								</td>
								<td align="center">
									<?
									$dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
									echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_yarn_type",100,$yarn_type,"",0, "-- Select --", $selected, "");
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_yarn_count",90,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
									?>
								</td>
								<td>
									<input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />
									<input type="hidden" id="txt_composition_id" name="txt_composition_id" />
								</td>
								<td>
									<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
								</td>
								<td>
									<?
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
									?>
								</td>
								<td> 
									<?
										echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
									?>
								</td>
								<td>
									<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
								</td>
								<td colspan="2">
									<input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;" class="formbutton" />
                                    <input type="button" name="search" id="search1" value="Report2" onClick="generate_report(2)" style="width:60px; display:none;" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td colspan="13" align="center"><? echo load_month_buttons(1); ?></td>
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
<script>
	set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script>
</html>
