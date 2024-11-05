<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Item Ledger

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	15-10-2014
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

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Store Item Ledger","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_item()
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_cat = $("#cbo_item_cat").val();

		/*var txt_produc_no = $("#txt_product_no").val();
		var txt_produc_id = $("#txt_product_id").val();
		var txt_product = $("#txt_product").val();
		var page_link='requires/general_store_item_ledger_controller.php?action=item_description_search&company='+company+'&txt_produc_no='+txt_produc_no+'&txt_produc_id='+txt_produc_id+'&txt_product='+txt_product; */
		var page_link='requires/general_store_item_ledger_controller.php?action=item_description_search&company='+company+'&cbo_item_cat='+cbo_item_cat;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo);
		}
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_item_cat*txt_product','Company Name*Item Category*Item Description')==false )
		{
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_cat = $("#cbo_item_cat").val();
		var txt_product_id = $("#txt_product_id").val();
		var cbo_method = $("#cbo_method").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var report_title=$( "div.form_caption" ).html();
        var cbo_store_name = $("#cbo_store_name").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_cat="+cbo_item_cat+"&txt_product_id="+txt_product_id+"&cbo_method="+cbo_method+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_store_name="+cbo_store_name;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/general_store_item_ledger_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Excel Preview" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
function fnExportToExcel()
{
    // $(".fltrow").hide();
    let tableData = document.getElementById("report_container2").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    },
    format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
    }

    let ctx = {
        worksheet: 'Worksheet',
        table: tableData
    }

    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
}

	function new_window()
	{

			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 style="width:900px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:900px;">
			<table class="rpt_table" width="900" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="130" class="must_entry_caption">Item Category</th>
                        <th width="130" class="must_entry_caption">Item Description</th>
                        <th width="110">Method</th>
                        <th width="100">Store Name</th>
                        <th width="180">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/general_store_item_ledger_controller', this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                    </td>
                    <td align="center">
                            <?
								echo create_drop_down( "cbo_item_cat", 120, $general_item_category,"", 1, "-- Select Item --", $selected, "",0,"" );
							?>
                    </td>
                    <td align="center">
                        <input style="width:130px;"  name="txt_product" id="txt_product"  ondblclick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />
                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>   <input type="hidden" name="txt_product_no" id="txt_product_no"/>
                    </td>
                    <td align="center">
						<?
                            echo create_drop_down( "cbo_method", 110, $store_method,"", 1, "Weighted Average", $selected, "", "","");
                        ?>
                    </td>
                    <td id="store_td">
                        <?
                            echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select Store --", 0, "", 0 );
                        ?>
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px"/>
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px"/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>
        </fieldset>
    </div>
    <br />

        <!-- Result Contain Start-->

        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>

        <!-- Result Contain END-->


    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
