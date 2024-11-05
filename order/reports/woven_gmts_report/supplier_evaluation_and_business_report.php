<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Woven Supplier Evaluation And Business Report.
Functionality	:
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	21-11-2019
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

$fabric_booking_type = array( 0 =>'-- Select --',1 => "Main Fabric Booking",2=>'Partial Fabric Booking', 3 => "Short Fabric Booking", 4 => "Sample Fabric Booking - With Order", 5 => 'Sample Fabric Booking - Without Order');
echo load_html_head_contents("Supplier Evaluation And Business", "../../../", 1, 1,$unicode,1,1);
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission = '<? echo $permission; ?>';
	function load_drop_down_supplier(item_cat_id)
	{
		var company_id = $('#cbo_company_id').val();
		var party_type = '';
		if(company_id == 0){
			alert("Company can not be empty");
			$('#cbo_category_id').val(0)
			return;
		}
		//load_drop_down( 'requires/pre_cost_entry_controller_v2', trim_group_id+"_"+i+"_"+trim_rate_variable+"_"+buyer, 'load_drop_down_supplier', 'tdsupplier_'+i );
		if(item_cat_id == 4){
			party_type = 5;
		}
		else{
			party_type = 9;
		}
		load_drop_down('requires/supplier_evaluation_and_business_report_controller', party_type+'*'+company_id, 'load_drop_down_supplier', 'supplier_td' );
	}

	function fn_report_generated(operation)
	{
		if(form_validation("cbo_company_id*cbo_category_id*cbo_year*cbo_month_from*cbo_month_to","Company Name*Category*Year*Month From*Month To ")==false){
				return;
			}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_name*cbo_category_id*cbo_supplier_id*cbo_year*cbo_month_from*cbo_month_to',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/supplier_evaluation_and_business_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			/*if(tot_rows*1>1){
				var tableFilters = {
					col_operation: {
					   id: ["total_booking_qty_kg","total_booking_qty_yds","total_booking_qty_mtr","total_booking_amount"],
					   col: [14,15,16,17],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
				 setFilterGrid("table_body",-1,tableFilters);
				 //setFilterGrid("table_body",-1,'');
			}*/

			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			//$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
    <h3 style="width:900px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:900px" >
            <fieldset style="width:900px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="150">Buyer</th>
                    <th width="150" class="must_entry_caption">Item Category</th>
                    <th width="150">Supplier Name</th>
                    <th width="150" class="must_entry_caption" colspan="3">Month Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/supplier_evaluation_and_business_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><? echo create_drop_down( "cbo_category_id", 150, $item_category,"", 1, "--Select Category--", $selected, "load_drop_down_supplier(this.value)",0,"3,4","" ); ?></td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_id", 150, $blank_array,"",1,"--Select Supplier--", $selected, ""); ?></td>
                        <td width="70"><? $selected_year=date("Y"); echo create_drop_down( "cbo_year", 70, $year,"", 1, "--Year--", $selected_year, "",0 ); ?>
                        </td>
                        <td width="90"><? $selected_month=date("m"); echo create_drop_down( "cbo_month_from", 90, $months,"", 1, "From Month", 0, "",0 ); ?>
                        </td>
                        <td width="90"><? $selected_month=date("m"); echo create_drop_down( "cbo_month_to", 90, $months,"", 1, "To Month", 0, "",0 );
                            ?>
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>