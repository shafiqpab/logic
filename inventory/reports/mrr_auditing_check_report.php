<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create MRR Auditing Checking Report
Functionality	:
JS Functions	:
Created by		:	Md. Jakir Hosen
Creation date 	: 	02-06-2022
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

$_SESSION['page_permission'] 	= $permission;
$menu_id 						= $_SESSION['menu_id'];

/*========== user credential  ========*/
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("select unit_id as COMPANY_ID, item_cate_id as ITEM_CATE_ID, company_location_id as COMPANY_LOCATION_ID, store_location_id as STORE_LOCATION_ID from user_passwd where id=$user_id");

$category_credential_id = $userCredential[0]['ITEM_CATE_ID'];

if ($category_credential_id !='') {
    $category_credential_cond = " and category_id in($category_credential_id)";
}
//---------------------------------------------------------------------------------------------
echo load_html_head_contents("MRR Auditing Report", "../../", 1, 1,'','','');

$permitted_item_category = return_field_value("item_cate_id","user_passwd","id='".$_SESSION['logic_erp']['user_id']."'");
$approval_setup			 = is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0");
?>

<script>

    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    var permission='<? echo $permission; ?>';


    function fn_report_generated()
    {

         
		var mrr_no=document.getElementById("txt_mrr_no").value;
		var challan_no=document.getElementById("txt_challan_no").value;
		var wo_id=document.getElementById("txt_wo_no").value;		
		var pi_no=document.getElementById("txt_pi_no").value;		 
		if (mrr_no == '' && challan_no == '' &&  wo_id == '' &&  pi_no =='') {
			 
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company Name*Date Range*Date Range') == false) {
				return;
			}
		}
		else{
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
		}

        var data="action=report_generate&"+get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_store_name*cbo_item_category_id*txt_challan_no*txt_mrr_no*txt_date_from*txt_date_to*cbo_audit_type*cbo_suppler_name*txt_wo_no*txt_pi_no*cbo_date_basis*cbo_year*txt_bill_no',"../../");
        freeze_window();
        http.open("POST","requires/mrr_auditing_check_report_controller.php",true);
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
            //document.getElementById('report_container').innerHTML = report_convert_button('../../');
            append_report_checkbox('table_body', 1);
            //var tableFilters = { col_0: "none",col_3: "select", display_all_text: " --- All Category ---" }
            document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            var tableFilters = { col_0: "none" }
            setFilterGrid("table_body",-1,tableFilters);
            show_msg('3');
            release_freezing();
        }
        release_freezing();
    }

    function new_window2()
    {
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none';
        $('#table_body tbody').find('tr:first').hide();
        var w = window.open('Surprise', '#');
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
        $('#table_body tbody').find('tr:first').show();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='330px';
    }

    // function generate_report_file(data,action)
	// {
	// 	window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + data+'&action='+action, true );
	// }

   

    function generate_report_grey_fabric_mrr(print_btn,company_id, update_id, rec_id,location_id,store_id) {
        var report_title="Knit Grey Fabric Roll Receive";

		if (print_btn == 86) {
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print&template_id=1', true);
		}
        else if(print_btn == 84){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print&template_id=1', true);
        }
        else if(print_btn == 85){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print3&template_id=1', true);
        }
        else if(print_btn == 89){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print4&template_id=1', true);
        }
        else if(print_btn == 129){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_print5&template_id=1', true);
        }
        else if(print_btn == 848){
			window.open("../grey_fabric/requires/grey_fabric_receive_roll_controller.php?data=" + company_id + '*' + rec_id  + '*' + update_id  + '*' + report_title+ '*' + location_id+ '*' + store_id + '&action=grey_fabric_receive_printmg&template_id=1', true);
        }
	}


    function show_mrr_dtls( data )
    {

        var d=data.split("__");
        // Entry Form Check
        if (d[7]==1)
        {
            // Yarn Receive
            //yarn 1
            // print button
            print_report( d[0]+'*'+d[2], "yarn_receive_print", "../requires/yarn_receive_controller" );
        }
        else if (d[7]==24)
        {
            var action = d[8].length > 0 ? d[8] : "trims_receive_entry_print_2";
            print_report( d[0]+'__'+d[1]+'__'+'Trims Receive Entry', action, "../trims_store/requires/trims_receive_entry_controller" );
        }
        else if (d[7]==20)
        {
            // General Item Receive
            //General Store 4,8,9,10,11,15,16,17,18,19,20,21,22
            // print button
            print_report( d[0]+'__'+d[1]+'__'+'General Item Receive'+'__'+d[4], "general_item_receive_print", "../general_store/requires/general_item_receive_controller" );
        }
        else if (d[7]==4)
        {
            // Dyes and Chemical Receive
            // Dyes and chemical  5,6,7,23
            // print button
            print_report( d[0]+'*'+d[1]+'*'+'Dyes And Chemical Receive'+'*'+d[5]+'*'+d[0]+'*'+d[6], "chemical_dyes_receive_print", "../chemical_dyes/requires/chemical_dyes_receive_controller");
        }
        else if (d[7]==37)
        {
            // knit finish fabric receive by garments
            // print 4 button
            print_report( d[0]+'*'+d[1]+'*'+'Knit Finish Fabric Receive By Garments'+'*'+d[2],'finish_fabric_receive_print_4','../finish_fabric/requires/knit_finish_fabric_receive_by_garments_controller');
        }
        else if (d[7]==7)
        {
            // Finish fabric Production Entry
            // print button
            print_report( d[0]+'*'+d[1]+'*'+d[2]+'*'+'Finish Fabric Production Entry','finish_fab_production_print','../../production/requires/finish_fabric_receive_controller');
        }
        else if (d[7]==2)
        {
            // Finish fabric Production Entry
            // print button
            print_report( d[0]+'*'+d[2]+'*'+d[1],'rejection_challan_print','../../production/requires/grey_production_entry_controller');
        }
        else
        {
            alert('Develop Later');
            return;
        }
    }
    function date_type_change(val = 1){
        if(val == 2){
            $('#cbo_date_basis').val(1);
        }else{
            $('#cbo_date_basis').val(2);
        }
    }

    function set_bill_td()
	{
		var item_category = $('#cbo_item_category_id').val();
        if(item_category ==2 || item_category ==13)
        {
            $('#txt_bill_no').removeAttr('disabled','disabled'); 
        }
        else
        {
			$('#txt_bill_no').attr('disabled','disabled');
        }
    }
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <form name="mrraudit_1" id="mrraudit_1">
        <h3 align="left" style="width:1360px;" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1190px;">
                <table class="rpt_table" width="1280" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="100">Supplier</th>
                    <th width="80">Location</th>
                    <th width="100">Store</th>
                    <th width="120">Item Category</th>
                    <th width="60">Challan No</th>
                    <th width="60">MRR No</th>
                    <th width="70">WO Number</th>
                    <th width="70">PI Number</th>
                    <th width="90">Date Basis</th>
                    <th width="90">Year</th>
                    <th width="120" colspan="2" class="must_entry_caption">Date Range</th>
                    <th width="70">Bill No</th>
                    <th width="90">Audit Status</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('mrraudit_1','report_container','','','')" class="formbutton" style="width:70px" /></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/mrr_auditing_check_report_controller',this.value, 'load_drop_down_location', 'com_location_td' ); load_drop_down( 'requires/mrr_auditing_check_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td'); load_drop_down( 'requires/mrr_auditing_check_report_controller', this.value, 'load_drop_down_store', 'com_store_td' );" );
                            ?>
                        </td>
                        <td id="supplier_td">
                            <?
                            echo create_drop_down( "cbo_suppler_name", 100, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                            ?>
                        </td>
                        <td id="com_location_td">
                            <?
                            echo create_drop_down( "cbo_location_id", 80, $blank_array,"", 1, "-- All  --", 0, "",0 );
                            ?>
                        </td>
                        <td id="com_store_td">
                            <?
                            echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select Store --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_item_category_id", 120, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond order by short_name", "category_id,short_name", 1, "-- Select Category --", 0, "set_bill_td();" );
                            ?>
                        </td>
                        <td><input name="txt_challan_no" id="txt_challan_no" style="width:60px" class="text_boxes" placeholder="Write"></td>
                        <td><input name="txt_mrr_no" id="txt_mrr_no" style="width:60px" class="text_boxes" placeholder="Write"></td>
                        <td><input name="txt_wo_no" id="txt_wo_no" style="width:70px" class="text_boxes" placeholder="Write"></td>
                        <td><input name="txt_pi_no" id="txt_pi_no" style="width:70px" class="text_boxes" placeholder="Write"></td>
                        <td>
                            <?
                            $databasis_type_arr = array(2=>"Audit Date", 1=>"MRR Date");
                            echo create_drop_down( "cbo_date_basis", 90, $databasis_type_arr,"", 0, "", $selected,"","", "");
                            ?>
                        </td>

                        <td>
							<?
                            	echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        	?>
						</td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"></td>
                        <td><input name="txt_bill_no" id="txt_bill_no" style="width:70px" class="text_boxes" disabled placeholder="Write"></td>
                        <td>
                            <?
                            $auditmrr_type_arr = array(1=>"Audited", 2=>"Un-Audited");
                            echo create_drop_down( "cbo_audit_type", 90, $auditmrr_type_arr,"", 0, "", "1","date_type_change(this.value)","", "");
                            ?>
                        </td>
                        <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated()"/></td>
                    </tr>
                    <tr>
                        <td colspan="13" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    $('#cbo_audit_type').val(0);
</script>
</html>