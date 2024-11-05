<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report.
Functionality	:
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	26-04-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Other Purchase Work Order App Status Report", "../../", 1, 1, '', 1, 1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_supplier_id*cbo_type*cbo_item_cat*cbo_year*txt_req_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/purchase_requision_approval_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}


	function fn_report_generated_reponse()
	{
	 	if(http.readyState == 4)
		{
	  		var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1);
			show_msg('3');
			release_freezing();
	 	}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#tbl_list_search tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";

		$("#tbl_list_search tr:first").show();
	}


</script>
</head>
<body onLoad="set_hotkey();">
<form id="priceQuotationApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1050px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1040px;">
             <table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Supplier</th>
                    <th>Item Category</th>
					<th>Requsition Year</th>
					<th>Requistion No</th>
                    <th>Type</th>
                    <th>Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('priceQuotationApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?= create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/other_purchase_work_order_approval_report_controller',this.value, 'load_drop_supplier', 'supplier_td' );" );?>
                        </td>
                        <td id="supplier_td">
                            <?= create_drop_down("cbo_supplier_id", 150, $blank_array,"", 1, "-- All --", $selected, "", 0,"");?>
                        </td>
                        <td>
                        	<?= create_drop_down("cbo_item_cat", 150, $item_category, "", 1, "-- All --", "", "", 0);?>
                        </td>
                        <td>
                        	<?= create_drop_down("cbo_year", 100, $year, "", 1, "-- All --", "", "", 0);?>
                        </td>
                         <td>
                            <input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px">
                        </td>
						<td>
                        	<?
							$search_by_arr=array(0=>"All", 2=>"Pending", 1=>"Partial Approved", 3=>"Full Approved");
							echo create_drop_down("cbo_type", 100, $search_by_arr, "", 0, "", "", "", 0);
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:70px;" readonly />
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><?= load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>