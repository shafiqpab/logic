<?
/*-------------------------------------------- Comments
Purpose			: 	This report will decscribe supplier wise mrr information

Functionality	:
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman
Creation date 	: 	20-12-2018
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

$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Supplier Wise MRR Info Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report()
	{
		if( form_validation('cbo_company_name*cbo_supplier_name','Company Name*Supplier')==false )
		{
			alert("Select Mandatory Fields First");
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_mrr_number = $("#txt_mrr_number").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_year = $("#cbo_year_selection").val();
		var report_title=$( "div.form_caption" ).html();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_supplier_name="+cbo_supplier_name+"&cbo_store_name="+cbo_store_name+"&txt_mrr_number="+txt_mrr_number+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_year="+cbo_year+"&report_title="+report_title;
		var data="action=generate_report"+dataString;

		freeze_window(operation);
		http.open("POST","requires/supplier_wise_mrr_info_rpt_controller.php",true);
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
			show_msg('3');
			setFilterGrid('mrr_details_tbl_body',-1);
			release_freezing();
		}
	}

	function new_window()
	{
			$(".flt").hide();
			// document.getElementById('scroll_body').style.overflow="auto";
			// document.getElementById('scroll_body').style.maxHeight="none";
			document.getElementById('democlass').removeAttribute("style");
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			$(".flt").show();
			release_freezing();	
			// document.getElementById('scroll_body').style.overflow="auto";
			// document.getElementById('scroll_body').style.maxHeight="250px";
			document.getElementById('democlass').removeAttribute("style");
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
    <form name="item_inquiry_report" id="item_inquiry_report" autocomplete="off" >
    <h3 style="width:950px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:950px;">
			<table class="rpt_table" width="950" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="150" class="must_entry_caption">Supplier</th>
						<th width="130">Store</th> 
                        <th width="130">MRR Number</th>
                        <th width="100">From Date</th>
                        <th width="100">To Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('item_inquiry_report','report_container*report_container2','','','')" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/supplier_wise_mrr_info_rpt_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/supplier_wise_mrr_info_rpt_controller', this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                    </td>
                    <td align="center" id="supplier_td">
                        <?
                            echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"", 1, "--Select Supplier--", "", "" );
                        ?>
                    </td>
					<td id="store_td" align="center">
						<?  echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "",1 ); ?>
					</td>
                    <td align="center">
                        <input style="width:120px;"  name="txt_mrr_number" id="txt_mrr_number" class="text_boxes" placeholder="Write MRR Number"/>
                    </td>
                     <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date"  readonly />

                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" readonly />

                    </td>


                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>
        </fieldset>
    </div>
    <br />

        <!-- Result Contain Start-------------------------------------------------------------------->

        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>

        <!-- Result Contain END-------------------------------------------------------------------->


    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
