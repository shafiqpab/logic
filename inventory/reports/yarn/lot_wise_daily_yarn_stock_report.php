<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Lot Wise Daily Yarn Stock Report

Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	28-05-2023
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
echo load_html_head_contents("Lot Wise Daily Yarn Stock Report","../../../", 1, 1, $unicode,1,1);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{

	if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
	{
		return;
	}

	var cbo_company_name = $("#cbo_company_name").val();
	var txt_count 	= $("#txt_yarn_count_id").val();
	var txt_lot_no 	= $("#txt_lot_no").val();
	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();
	var txt_composition = $("#txt_composition").val();
	var txt_composition_id = $("#txt_composition_id").val();
	var txt_yarn_brand = $("#txt_yarn_brand").val();
	var txt_yarn_brand_id = $("#txt_yarn_brand_id").val();

	var lot_search_type = 0

	if ($('#lot_search_type').is(":checked"))
	{
	   lot_search_type = 1;
	}

	var dataString = "&cbo_company_name="+cbo_company_name+"&txt_count="+txt_count+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&to_date="+to_date+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&lot_search_type="+lot_search_type+"&txt_yarn_brand="+txt_yarn_brand+"&txt_yarn_brand_id="+txt_yarn_brand_id+"&type="+type;
 	var data="action=generate_report"+dataString;
	freeze_window(3);
	http.open("POST","requires/lot_wise_daily_yarn_stock_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}

function generate_report_reponse()
{
	if(http.readyState == 4)
	{
 		var reponse=trim(http.responseText).split("**");
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

		// var tableFilters = {
		// 	col_0: "none",
		// 	col_operation: {
		// 		id: ["value_total_opening_balance"],
		// 		col: [10],
		// 		operation: ["sum"],
		// 		write_method: ["innerHTML"]
		// 	}
		// }
		setFilterGrid("table_body",-1);

		show_msg('3');
		release_freezing();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$("#table_body tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="350px";
	$("#table_body tr:first").show();
}


function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/lot_wise_daily_yarn_stock_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);

	}
}

function openmypage_yarn_count()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/lot_wise_daily_yarn_stock_report_controller.php?action=yarn_count_popup&companyID='+companyID, 'Yarn Count Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_count_des=this.contentDoc.getElementById("hidden_yarn_count").value; //Access form field with id="emailfield"
		var yarn_count_id=this.contentDoc.getElementById("hidden_yarn_count_id").value;
		$("#txt_yarn_count").val(yarn_count_des);
		$("#txt_yarn_count_id").val(yarn_count_id);

	}
}

function openmypage_yarn_brand()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/lot_wise_daily_yarn_stock_report_controller.php?action=yarn_brand_popup&companyID='+companyID, 'Yarn Brand Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_brand_des=this.contentDoc.getElementById("hidden_yarn_brand").value; //Access form field with id="emailfield"
		var yarn_brand_id=this.contentDoc.getElementById("hidden_yarn_brand_id").value;
		$("#txt_yarn_brand").val(yarn_brand_des);
		$("#txt_yarn_brand_id").val(yarn_brand_id);

	}
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <div style="width:100%;" align="center">
        <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:850px;">
                <table class="rpt_table" width="835" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
                            <th>Brand</th>
                            <th class="must_entry_caption" colspan="2">Transacion Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<?
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/lot_wise_daily_yarn_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/lot_wise_daily_yarn_stock_report_controller' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/lot_wise_daily_yarn_stock_report_controller' );" );
                            ?>
                        </td>
                    
                        <td>
							<input type="text" id="txt_yarn_count" name="txt_yarn_count" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_yarn_count();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_yarn_count_id" name="txt_yarn_count_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td>
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
						
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
                        </td>

                        <td>
							<input type="text" id="txt_yarn_brand" name="txt_yarn_brand" class="text_boxes" style="width:80px" value="" onDblClick="openmypage_yarn_brand();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_yarn_brand_id" name="txt_yarn_brand_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                       
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">

                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                       
                        <td colspan="2">
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="20">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
<script>
	//set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script>
</html>
