<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
$user_id=$_SESSION['logic_erp']['user_id'];
//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id";

$location_id = $userCredential[0][csf('location_id')];
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$location_credential_cond=""; $userbrand_idCond="";

if ($location_id) {
    $location_credential_cond = " and id in($location_id)";
}

if ($_SESSION['logic_erp']["user_code"]!="SUPERADMIN" && $userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = " and id in ( $userbrand_id)";
}
//echo $userbrand_idCond;

if($action=="get_company_config"){
	$action($data);
}

function get_company_config($data)
{
	global $location_credential_cond;
	$loc="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name";
	// echo $loc;
	$result_loc=sql_select($loc);
	$index=$selected;
	if(count($result_loc)==1)
	{
		$index=$result_loc[0][csf('id')];
	}
	
	$cbo_location_name= create_drop_down( "cbo_location_name", 130, $loc,"id,location_name", 1, "-- Select --", $index, "" ); 
	
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "fnc_get_buyer_config(this.value);" ); 
	
	$cbo_agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	
	$cbo_client= create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );
	
	$sqlfile="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	
	$txt_file_year= create_drop_down( "txt_file_year", 70, $sqlfile,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,"");

	$act_po_data=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=93  and status_active=1 and is_deleted=0");
	
	echo "document.getElementById('location').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	
	echo "document.getElementById('party_type_td').innerHTML = '".$cbo_client."';\n";
	echo "document.getElementById('file_year_td').innerHTML = '".$txt_file_year."';\n";
	if($act_po_data !=""){
		echo "document.getElementById('act_po_id').value = '".$act_po_data."';\n";
	}
	else{
		echo "document.getElementById('act_po_id').value = '2';\n";
	}
	
	exit();
}

if($action=="get_buyer_config"){
	$action($data);
}

function get_buyer_config($data)
{
	global $userbrand_idCond;
	$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=70; else $width=150;
	//echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC";
	$cbo_season_id=create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	
	$cbo_brand_id=create_drop_down( "cbo_brand_id", 130, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	
	echo "document.getElementById('season_td').innerHTML = '".$cbo_season_id."';\n";
	echo "document.getElementById('brand_td').innerHTML = '".$cbo_brand_id."';\n";
}

// Master Form*************************************Master Form*************************
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_sew_location")
{
	$sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
	$result=sql_select($sql);
	$index=$selected;
	if(count($result)==1)
	{
		$index=$result[0][csf('id')];
	}
	echo create_drop_down( "cbo_working_location_id", 130, $sql,"id,location_name", 1, "-- Select --", $index, "" );	
	exit();		 
}

if ($action=="load_drop_down_file_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "txt_file_year", 70,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,"");
	exit();
}

if ($action=="load_drop_down_buyer")
{
	/*$leader_name_id=0;
	$team_buyer_arr=array();
	$sql_buyer=sql_select("select id, marketing_team_id from lib_buyer");
	foreach($sql_buyer as $row)
	{
		$team_buyer_arr[$row[csf('id')]]=$row[csf('marketing_team_id')];
	}*/

	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); check_tna_templete(this.value); load_drop_down( 'requires/order_entry_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/order_entry_controller', this.value, 'load_drop_down_brand', 'brand_td');" );//load_drop_down( 'requires/order_entry_controller', this.value, 'load_drop_down_team_leader', 'leader_td'); load_drop_down( 'requires/order_entry_controller', ".$leader_name_id.", 'cbo_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/order_entry_controller', ".$leader_name_id.", 'cbo_factory_merchant', 'div_marchant_factory');
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id, a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );
	exit();
}

/*if($action=="load_drop_down_team_leader")
{
	echo create_drop_down( "cbo_team_leader", 150, "select id, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/order_entry_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory'); ");
	exit();
}*/

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 130, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_projected_po")
{
	echo create_drop_down( "cbo_projected_po", 100, "select id, po_number from  wo_po_break_down where job_no_mst='$data' and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=70; else $width=130;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}


if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library, cm_cost_method, user_id from variable_order_tracking where company_name=$data and variable_list in (14,20,23,25,32,33,44,45,47,53,93,96) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$tna_integrated=0; $copy_quotation=0; $set_smv_id=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; $cost_control_source=0; $color_from_lib=0; $next_process_ship_date=0; $act_po_data=2; $po_update_user_id=0; $po_control_booking=2;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==14) $tna_integrated=$result[csf('tna_integrated')];
		else if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		else if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
		else if($result[csf('variable_list')]==25) $publish_shipment_date=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==32) 
		{
			$po_update_period=$result[csf('po_update_period')];
			$po_update_user_id=$result[csf('user_id')];
		}
		else if($result[csf('variable_list')]==33) $po_current_date=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==45) $excut_source=$result[csf('excut_source')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
		else if($result[csf('variable_list')]==93) $act_po_data=$result[csf('cm_cost_method')];
		else if($result[csf('variable_list')]==96) $po_control_booking=$result[csf('copy_quotation')];
	}
	echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source."_".$cost_control_source."_".$set_smv_id."_".$color_from_lib."_".$act_po_data."_".$po_update_user_id."_".$po_control_booking;
 	exit();
}
if($action=="po_wise_booking_approval"){
	$data=explode("_",$data);
	$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	$is_approved=0;
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row)
	{
		if($row[csf('is_approved')]==1)
		{
			$is_approved=$row[csf('is_approved')];
		}		
	}
	echo $is_approved;
	exit();
}

if($action=="check_tna_templete")
{
	$data=explode("_",$data);
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific  from tna_task_template_details where for_specific in ($data[0],0) and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp)
	{
		if($row_temp[csf('for_specific')]>0) $temp=1;
		else $temp=0;
	}
	//echo $temp;
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1] and variable_list=14 and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	$marketing_team_id=0;
    $cut_off_used=0;
	 $buyer_info=sql_select("SELECT marketing_team_id, cut_off_used from lib_buyer where id=$data[0] and status_active=1 and is_deleted=0");
    foreach ($buyer_info as $row) {
    	$marketing_team_id=$row[csf('marketing_team_id')];
    	if($row[csf('cut_off_used')]!=''){
    		$cut_off_used=$row[csf('cut_off_used')];	
    	}
    }
	echo $temp."_".$tna."_".$marketing_team_id."_".$cut_off_used;
	//echo $temp."_".$tna;
	die;
}

if ($action=="load_drop_gmts_item")
{
	echo create_drop_down( "cbo_gmtsItem_id", 120, $garments_item, 0, 1, "--Select Item--", $data,"fnc_calAmountQty_ex(0,1); fnc_calculateRate( document.getElementById( 'cbo_breakdown_type' ).value, 0); ",'',$data);
	exit();
}

if ($action=="load_dorp_down_code")
{
	echo create_drop_down( "cbo_code_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select Code--", "","");
	exit();
}

/*if ($action=="load_dorp_down_ultimate_country")
{
	echo create_drop_down( "cbo_country_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Ultimate Country--", "","");
	exit();
}
*/
if ($action=="load_dorp_down_countryCode")
{
	echo create_drop_down( "cbo_countryCode_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Country Code--", "","");
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_buyer_style_po_check")
{
	$ex_data=explode('_', $data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$style_ref=$ex_data[2];
	$po_no=$ex_data[3];
	$po_id=$ex_data[4];
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and b.id!='$po_id'";
	//$sql_check=sql_select("Select a.job_no from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' and a.buyer_name='$buyer_id' and a.style_ref_no='$style_ref' and b.po_number='$po_no' $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no order by a.job_no ASC");//Hide by issue id ISD-23-26984
	
	$sql_check=sql_select("Select a.job_no from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.po_number='$po_no' $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no order by a.job_no ASC");
	$job_no_all='';
	foreach ($sql_check as $row)
	{
		if($job_no_all=='') $job_no_all=$row[csf("job_no")]; else $job_no_all.=', '.$row[csf("job_no")];
	}
	//echo "50***Duplicate Buyer, Style and PO no.\n Merchandising Job: ".$job_no_all."\nPress \"OK\" Allow this PO No.\nPress \"Cancel\" Dont Allow this PO No.***".$job_no_all;//Hide by issue id ISD-23-26984
	
	echo "50***Same PO Found in Another Job.\n Merchandising Job: ".$job_no_all."\nPress \"OK\" Allow this PO No.\nPress \"Cancel\" Dont Allow this PO No.***".$job_no_all;
	exit();
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Quotation Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="70">Quotation ID</th>
                <th width="100">Style Ref.</th>
                <th width="180">Delv. Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_id">
                <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down('order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
            </td>
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+'<? echo $txt_job_no; ?>', 'create_quotation_id_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" height="40" colspan="6"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_quotation_id_list_view")
{
	//echo $data;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";// else { echo "Please Select Buyer First."; die; }
	//echo $data[1];
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}

	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($data[7]!='') $inset_date=return_field_value("insert_date", "wo_po_details_master", "job_no='$data[7]'");
	else $inset_date=$pc_date_time;

	$ex_insert_date=explode(" ",$inset_date);
	if($db_type==0) $date_change=change_date_format($ex_insert_date[0],"yyyy-mm-dd");
	else if($db_type==2) $date_change=change_date_format($ex_insert_date[0], "", "",1);
	
	$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
	$app_nessity=2; $validate_page=0; $allow_partial=2;
	foreach($sql as $row){
		$app_nessity=$row[csf('approval_need')];
		$validate_page=$row[csf('validate_page')];
		$allow_partial=$row[csf('allow_partial')];
	}
	
	$quotAppCond="";
	if($validate_page==1)
	{
		if($app_nessity==1)
		{
			 if($allow_partial==1) $quotAppCond=" and a.approved in (1,3)";
			 else $quotAppCond=" and a.approved=1";
		}
	}

	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select a.id, a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.pord_dept, a.offer_qnty, a.est_ship_date from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and b.confirm_date is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in (select quotation_id from wo_po_details_master where status_active=1 and is_deleted=0 and quotation_id!=0) $company $buyer_cond $style_cond $quotation_id_cond $quotAppCond $company order by a.id DESC";
	//echo $sql; die;

	echo create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "60,120,100,100,140,100,80","850","260",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_quotation")
{
	$ex_data=explode("_",$data);
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, b.price_with_commn_pcs,a.dealing_merchant from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id='$ex_data[0]'");
	foreach ($data_array as $row)
	{
		$team_id=return_field_value("team_id", "lib_mkt_team_member_info", "id='".$row[csf("dealing_merchant")]."'");
		
		echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td');\n";
		//echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "disable_enable_fields('cbo_company_name',1);\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "load_drop_down( 'requires/order_entry_controller', '".$team_id."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		echo "load_drop_down( 'requires/order_entry_controller', '".$team_id."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		echo "document.getElementById('cbo_team_leader').value = '".$team_id."';\n";
		
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_merchant")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("pord_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		//echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";

		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('txt_quotation_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "location_select();\n";
	}
	exit();
}

if ($action=="qc_id_popup")
{
  	echo load_html_head_contents("Quick Costing Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="90">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="70">Cost Sheet No</th>
                <th width="100">Style Ref.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
			<td>
			<? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in (3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $cbo_company_name, "",1); 
			?> 
			</td>
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_qc_id_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_qc_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";//else { echo "Please Select Buyer First."; die; }
	//echo $data[0];
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}

	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.cost_sheet_no like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	if($db_type==0) $quotNullCheck="IFNULL(quotation_id,0)";
	else if($db_type==2) $quotNullCheck="nvl(quotation_id,0)";
	
	$arr=array (0=>$company_arr,5=>$buyer_arr,8=>$pord_dept);

	$sql= "select a.id, a.company_id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, a.revise_no, a.option_id, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$data[0]'
	and a.qc_no not in (select quotation_id from wo_po_details_master where status_active=1 and is_deleted=0 and $quotNullCheck!=0) $buyer_cond $style_cond $quotation_id_cond order by a.id DESC";
	//echo $sql;
	echo create_list_view("list_view", "Company Name, QC ID, Cost Sheet No, Rev., Opt., Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "70,50,70,40,40,100,100,100,80,80","850","280",0, $sql , "js_set_value", "id", "", 1, "company_id,0,0,0,0,buyer_id,0,0,department_id,0,0", $arr , "company_id,id,cost_sheet_no,revise_no,option_id,buyer_id,confirm_style,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,0,0,0,2,3') ;
	
	exit();
}

if($action=="populate_data_from_search_popup_qc")
{
	$data_array=sql_select("select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.season_year, a.brand_id, a.style_ref, a.style_des, a.prod_dept, a.offer_qty, a.delivery_date, a.inquery_id, a.team_leader, a.dealing_marchant, b.confirm_style, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
	
	$inquery_id=$data_array[0][csf('inquery_id')]*1;
	if($inquery_id>0)
	{
		$sqlInq=sql_select("select TEAM_LEADER, DEALING_MARCHANT, FACTORY_MARCHANT, TEAM_LEADER from WO_QUOTATION_INQUERY where id='$inquery_id'");
	}
	//echo "select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'";
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("confirm_style")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("prod_dept")]."';\n";
		if($inquery_id>0)
		{
			echo "document.getElementById('cbo_team_leader').value = '".$sqlInq[0]['TEAM_LEADER']."';\n";
			
			echo "load_drop_down( 'requires/order_entry_controller', '".$sqlInq[0]['TEAM_LEADER']."', 'cbo_dealing_merchant', 'div_marchant');\n";
			echo "load_drop_down( 'requires/order_entry_controller', '".$sqlInq[0]['TEAM_LEADER']."', 'cbo_factory_merchant', 'div_marchant_factory');\n";
			
			echo "document.getElementById('cbo_dealing_merchant').value = '".$sqlInq[0]['DEALING_MARCHANT']."';\n";
			echo "document.getElementById('cbo_factory_merchant').value = '".$sqlInq[0]['FACTORY_MARCHANT']."';\n";
		}
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_id")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("qc_no")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("confirm_fob")]."';\n";
		echo "document.getElementById('txt_quotation_price').value = '".$row[csf("confirm_fob")]."';\n";
		exit();
	}
}

if ($action=="ws_id_popup")
{
  	echo load_html_head_contents("Work Study Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Buyer Name</th>
                <th width="70">System ID</th>
                <th width="100">Style Ref.</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_ws_id_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td');
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>;
		$('#cbo_buyer_id').attr('disabled',true);
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_ws_id_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$search_type=$data[2];
	$sysNo=$data[3];
	$styleRef=$data[4];
	
	if($search_type==1)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref='$styleRef'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '%$styleRef%' ";
	}
	else if($search_type==2)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '$styleRef%' ";
	}
	else if($search_type==3)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '%$styleRef' ";
	}

	if ($sysNo!='') $sys_con=" and a.system_no='$sysNo'"; else $sys_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'"; else $buyer_id_con="";
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}
	
	$variable_stylesmv_source=return_field_value("publish_shipment_date","variable_order_tracking","company_name='$company' and variable_list=47 and status_active=1 and is_deleted=0 ","publish_shipment_date");
	//echo $variable_stylesmv_source;
	$appCond="";
	if($variable_stylesmv_source==3)
	{
		$approval_necessity_setup=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id=b.mst_id and a.company_id='$company' and b.page_id=31 and a.status_active=1 and a.is_deleted=0 order by a.setup_date desc","approval_need");	
		if($approval_necessity_setup==1)
		{
			$appCond="and a.approved=1";
		}
	}
	
	$bulletin_type_cond="";
	if($variable_stylesmv_source==3){
		$bulletin_type_cond="and a.bulletin_type=3";
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.total_smv,a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $style_con $buyer_id_con $sys_con $appCond $bulletin_type_cond order by a.id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['total_smv']=$row[csf('total_smv')];
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['system_no'].=$row[csf('system_no')].',';
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_sewing_arr[8]);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="160">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $ext_no=>$dataarr)
		{
			foreach($dataarr as $style=>$arrdata)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
				$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
	
				$finish_smv=$cut_smv=$sewing_smv=0;
	
				$sys_id=rtrim($arrdata['id'],',');
				$ids=array_filter(array_unique(explode(",",$sys_id)));
				//print_r($ids);
				$id_str=""; $k=0;
				foreach($ids as $idstr)
				{
					if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
	
					foreach($lib_sewing_ids as $lsid)
					{
						$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
						$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
						$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
					}
					$k++;
				}
	
				$system_no=rtrim($arrdata['system_no'],',');
				$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));
	
				$finish_smv=$finish_smv/$k;
				$cut_smv=$cut_smv/$k;
				$sewing_smv=$arrdata['total_smv'];//$sewing_smv/$k;
	
				$datastr=$style;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $datastr; ?>')">
					<td width="30"><? echo $i;//.'='.$k ?></td>
					<td width="120" style="word-break:break-all"><? echo $system_no; ?></td>
					<td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
					<td width="160" style="word-break:break-all"><? echo $style; ?></td>
					<td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
					<td><p><? echo $arrdata['operation_count']; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="check_precost")
{
	$sql_data=sql_select("select count(a.id) as id, a.approved, c.order_uom from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b, wo_po_details_master c where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no='$data' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no, a.approved,c.order_uom");
	$id=0; $order_uom=0; $is_approved=0;
	foreach($sql_data as $row)
	{
		$id=$row[csf('id')];
		$order_uom=$row[csf('order_uom')];
		if($row[csf('approved')]==1) $is_approved=1;
	}
	echo trim($id)."_".trim($order_uom)."_".trim($is_approved);
	die;
}

if($action=="check_precost_approve")
{
	$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	$isapproved=$sql_data[0][csf("approved")];
	echo trim($isapproved);
	die;
}

if($action=="open_set_list_view")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $set_smv_id.'='.$txt_style_ref;
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	$txt_style_ref=trim(str_replace($str_replace_check,' ',$txt_style_ref));
	?>
	<script>
	function add_break_down_set_tr( i )
	{
		var unit_id= document.getElementById('unit_id').value;
		if(unit_id==1)
		{
			alert('Only One Item');
			return false;	
		}
		var row_num=$('#tbl_set_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		var setsmv='<? echo $set_smv_id ?>';
		//alert(setsmv);
		if(setsmv==3)
		{
			if(form_validation('smv_'+i,'Sew SMV')==false)
			{
				 $('#smv_'+i).focus(); 
				return;
			}
		}
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		else
		{
			i++;
			 $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});
			  }).end().appendTo("#tbl_set_details");
			  $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#cboitem_'+i).val(0);
			$('#cboitem_'+i).removeAttr('disabled','');
			  $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#smv_'+i).removeAttr("ondblclick").attr("ondblclick","check_smv_set_popup("+i+")");
			  $('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
			  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
			  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			  $('#cboitem_'+i).val(''); 
			  $('#smv_'+i).val(''); 
			  $('#cutsmv_'+i).val(''); 
			  $('#finsmv_'+i).val(''); 
			  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			  set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			  set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			  set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}
	
	function fn_delete_down_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
			}
			 
			 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			 set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			 set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			 set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}
	
	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		$('#smv_'+id).val('');
		$('#cutsmv_'+id).val('');
		$('#finsmv_'+id).val('');
			
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cboitem_'+k).value)
				{
					alert("Same Gmts Item Duplication Not Allowed.");
					document.getElementById(td).value="0";
					document.getElementById(td).focus();
				}
			}
		}
	}
	
	function check_smv_set(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		//alert(item_id);
		var txt_style_ref='<? echo $txt_style_ref ?>'
		var set_smv_id='<? echo $set_smv_id ?>'
		var item_id=$('#cboitem_'+id).val();
		//alert(td);
		//get_php_form_data(company_id,'set_smv_work_study','requires/order_entry_controller' );
		var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'order_entry_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if(set_smv_id==1)
			{
				$('#smv_'+id).val(response[1]);
				$('#tot_smv_qnty').val(response[1]);
			}
		}
	}
	
	function check_smv_set_popup(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
	
		var txt_style_ref='<? echo $txt_style_ref ?>';
		var cbo_company_name='<? echo $cbo_company_name ?>';
		var cbo_buyer_name='<? echo $cbo_buyer_name ?>';
		var bulletin_type=$('#bulletin_type').val();
		var item_id=$('#cboitem_'+id).val();
			//alert(cbo_company_name);
		var set_smv_id='<? echo $set_smv_id ?>';
		
		if(set_smv_id==3 || set_smv_id==4 || set_smv_id==6 || set_smv_id==8 || set_smv_id==9)
		{
			$('#smv_'+id).val('');
			$('#cutsmv_'+id).val('');
			$('#finsmv_'+id).val('');
			
			$('#tot_smv_qnty').val('');
			$('#tot_cutsmv_qnty').val('');
			$('#tot_finsmv_qnty').val('');
			
			$('#hidquotid_'+id).val('');
			
			var page_link="order_entry_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&bulletin_type="+bulletin_type;
		}
		else
		{
			return;
		}
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
			var smv_data=selected_smv_data.split("_");
			var row_id=smv_data[3];
			
			$("#smv_"+row_id).val(smv_data[0]);
			$("#smv_"+row_id).attr('readonly','readonly');
			$("#cutsmv_"+row_id).val(smv_data[1]);
			$("#cutsmv_"+row_id).attr('readonly','readonly');
			$("#finsmv_"+row_id).val(smv_data[2]);
			$("#finsmv_"+row_id).attr('readonly','readonly');
			$("#hidquotid_"+row_id).val(smv_data[4]);
			calculate_set_smv(row_id);
		}	
	}

	function calculate_set_smv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('smv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('smvset_'+i).value=set_smv;
		
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		
		calculate_set_cutsmv(i);
		calculate_set_finsmv(i);
	}
	
	function calculate_set_cutsmv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('cutsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('cutsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
	}
	
	function calculate_set_finsmv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('finsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('finsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
	}
	
	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		if(des_fil_id=="tot_set_qnty")
		{
			math_operation( des_fil_id, field_id, '+', rowCount );
		}
		if(des_fil_id=="tot_smv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_cutsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_finsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
	}
	
	function js_set_value_set()
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		var set_breck_down="";
		var item_id=""
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
			{
				return;
			}
			var smv =document.getElementById('smv_'+i).value;
			if(smv==0)
			{
				alert("Smv 0 not accepted");
				return;
			}
			if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0)
			
			if($('#cutsmv_'+i).val()=='') $('#cutsmv_'+i).val(0)
			if($('#cutsmvset_'+i).val()=='') $('#cutsmvset_'+i).val(0)
			if($('#finsmv_'+i).val()=='') $('#finsmv_'+i).val(0)
			if($('#finsmvset_'+i).val()=='') $('#finsmvset_'+i).val(0)
			if($('#printseq_'+i).val()=='') $('#printseq_'+i).val(1)
			if($('#embroseq_'+i).val()=='') $('#embroseq_'+i).val(2)
			if($('#washseq_'+i).val()=='') $('#washseq_'+i).val(3)
			if($('#spworksseq_'+i).val()=='') $('#spworksseq_'+i).val(4)
			if($('#gmtsdyingseq_'+i).val()=='') $('#gmtsdyingseq_'+i).val(5)
			if($('#aopseq_'+i).val()=='') $('#aopseq_'+i).val(6)
			if($('#brushseq_'+i).val()=='') $('#brushseq_'+i).val(7)
			if($('#peachseq_'+i).val()=='') $('#peachseq_'+i).val(8)
			if($('#ydseq_'+i).val()=='') $('#ydseq_'+i).val(9)
			
			
			if(set_breck_down=="")
			{
				set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val()+'_'+$('#aop_'+i).val()+'_'+$('#aopseq_'+i).val()+'_'+$('#brush_'+i).val()+'_'+$('#brushseq_'+i).val()+'_'+$('#peach_'+i).val()+'_'+$('#peachseq_'+i).val()+'_'+$('#yd_'+i).val()+'_'+$('#ydseq_'+i).val()+'_'+$('#printdiff_'+i).val()+'_'+$('#embrodiff_'+i).val()+'_'+$('#washdiff_'+i).val()+'_'+$('#spwdiff_'+i).val();
				item_id+=$('#cboitem_'+i).val();
			}
			else
			{
				set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val()+'_'+$('#aop_'+i).val()+'_'+$('#aopseq_'+i).val()+'_'+$('#brush_'+i).val()+'_'+$('#brushseq_'+i).val()+'_'+$('#peach_'+i).val()+'_'+$('#peachseq_'+i).val()+'_'+$('#yd_'+i).val()+'_'+$('#ydseq_'+i).val()+'_'+$('#printdiff_'+i).val()+'_'+$('#embrodiff_'+i).val()+'_'+$('#washdiff_'+i).val()+'_'+$('#spwdiff_'+i).val();
				item_id+=","+$('#cboitem_'+i).val();
			}
		}
		
		document.getElementById('set_breck_down').value=set_breck_down;
		document.getElementById('item_id').value=item_id;
		parent.emailwindow.hide();
	}
	
	function open_emblishment_pop_up(i)
	{ 
		var page_link="order_entry_controller.php?action=open_emblishment_list";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
		}		
	}
	</script>
	</head>
	<body>
       <div id="set_details"  align="center">            
    	<fieldset>
         <? 
		 $is_disabled="";
		if($set_smv_id==3 || $set_smv_id==4){
			$bulletin_type=3;
		}
		if($set_smv_id==6 || $set_smv_id==8 || $set_smv_id==9){
			$bulletin_type=2;
		}
		 $sql_smv="SELECT  gmts_item_id from ppl_gsd_entry_mst where status_active=1 and is_deleted=0 and style_ref='$txt_style_ref' and bulletin_type=$bulletin_type order by id DESC";
		 //and rownum = 1 
		 //echo $sql_smv; die;
		 $sql_result=sql_select($sql_smv);$set_smv_arr=array();

		 foreach($sql_result as $row)
		 {
			$ws_garments_item=$row[csf('gmts_item_id')];
			$gmstitem_arr[$row[csf('gmts_item_id')]]=$row[csf('gmts_item_id')];
		 }
		 $gmstitem_string=implode(",", $gmstitem_arr);
		 //echo "select current_approval_status from ";
		 $other_cost_approved=return_field_value("current_approval_status","co_com_pre_costing_approval","job_no='$txt_job_no' and entry_form=15 and cost_component_id=12");
		 // echo $other_cost_approved.'='.$txt_job_no.'='.$precostapproved;
		 $disabled=0;
		 if($precostapproved==0 )
		 {
			 if($other_cost_approved==1)
			 {
				 echo '<p style="color:#FF0000;">Pre Cost Others Cost Approved, Any Change not allowed.</P>';
				 $disab="disabled";
				 $disabled=1;
			 }
			 else if($precostfound >0 ){ 
				 echo "Pre Cost Found, only Sew. and Cut. SMV Change allowed";
				 $disab="";
				 $disabled=1;
			 }
			 else $disabled=0;
		 }
		 else if($precostapproved==1 ) 
		 {
			 echo '<p style="color:#FF0000;">Pre Cost Approved, Any Change not allowed.</P>';
			 $disab="disabled";
			 $disabled=1;
		 }
		 else $disab="";
		// echo $set_smv_id;
		 
		 if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==4 || $set_smv_id==6 || $set_smv_id==8 || $set_smv_id==9) $readonly="readonly"; else $readonly=""; //pq 2, ws 3, ws 8
		 
		 if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==4 || $set_smv_id==5 || $set_smv_id==6 || $set_smv_id==8 || $set_smv_id==9) 
		 {
			$disabled=1; $disab="disabled";
		 }else $disabled=0;
		 ?>
        <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id"  />  
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
            <input type="hidden" id="bulletin_type" value="<? echo $bulletin_type;  ?>" />        	
            <table width="1350" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                    	<th width="150">Item</th>
                    	<th width="40">Set Ratio</th>
                    	<th width="40">Sew SMV/ Pcs</th>
                    	<th width="40">Cut SMV/ Pcs</th>
                    	<th width="40">Fin SMV/ Pcs</th>
                    	<th width="80">Complexity</th>
                    	<th width="100">Print</th>
                    	<th width="100">Embro</th>
                    	<th width="100">Wash</th>
                    	<th width="100">SP. Works</th>
                    	<th width="100">Gmts Dyeing</th>
                    	<th width="100">AOP</th>
                    	<th width="100">Brushing</th>
                    	<th width="100">Peached Finish</th>
                    	<th width="100">Yarn Dyeing</th>
                    	<th width="70"></th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $smv_arr=array();
                    $job_no="'".$txt_job_no."'";

                    $sql_d=sql_select('Select gmts_item_id AS "gmts_item_id",set_item_ratio AS "set_item_ratio",smv_pcs AS "smv_pcs",smv_set AS "smv_set",complexity AS "complexity",embelishment AS "embelishment", cutsmv_pcs AS "cutsmv_pcs", cutsmv_set AS "cutsmv_set", finsmv_pcs AS "finsmv_pcs", finsmv_set AS "finsmv_set",printseq AS "printseq",embro AS "embro",embroseq AS "embroseq",wash AS "wash",washseq AS "washseq",spworks AS "spworks",spworksseq AS "spworksseq",gmtsdying AS "gmtsdying",gmtsdyingseq AS "gmtsdyingseq", ws_id as "ws_id" , aop as "aop", aopseq as "aopseq", bush as "bush", bushseq as "bushseq", peach as "peach", peachseq as "peachseq", yd as "yd", ydseq as "ydseq",  printdiff as "printdiff",embrodiff as "embrodiff",washdiff as "washdiff",spwdiff as "spwdiff" from wo_po_details_mas_set_details where job_no='.$job_no.' order by id');
                    //quot_id as "quot_id" 

                    foreach($sql_d as $sql_r){
                        if($sql_r['gmts_item_id']=="") $sql_r['gmts_item_id']=0;
                        if($sql_r['set_item_ratio']=="") $sql_r['set_item_ratio']=0;
                        if($sql_r['smv_pcs']=="")
                        {
                            $sql_r['smv_pcs']=0;
                            $sql_r['smv_set']=0;
                        }
                        if($sql_r['complexity']=="") $sql_r['complexity']=0;
                        if($sql_r['embelishment']=="") $sql_r['embelishment']=0;
                        if($sql_r['cutsmv_pcs']=="")
                        {
                            $sql_r['cutsmv_pcs']=0;
                            $sql_r['cutsmv_set']=0;
                        }
                        if($sql_r['finsmv_pcs']=="")
                        {
                            $sql_r['finsmv_pcs']=0;
                            $sql_r['finsmv_set']=0;
                        }
                        if($sql_r['printseq']=="") $sql_r['printseq']=0;
                        if($sql_r['embro']=="") $sql_r['embro']=0;
                        if($sql_r['embroseq']=="") $sql_r['embroseq']=0;
                        
                        if($sql_r['wash']=="")$sql_r['wash']=0;
                        if($sql_r['washseq']=="")$sql_r['washseq']=0;
                        
                        if($sql_r['spworks']=="") $sql_r['spworks']=0;
                        if($sql_r['spworksseq']=="") $sql_r['spworksseq']=0;
                        
                        if($sql_r['gmtsdying']=="") $sql_r['gmtsdying']=0;
                        if($sql_r['gmtsdyingseq']=="")$sql_r['gmtsdyingseq']=0;
                        //if($sql_r['quot_id']=="") $sql_r['quot_id']=0;
						if($sql_r['ws_id']=="") $sql_r['ws_id']=0;
						if($sql_r['aop']=="") $sql_r['aop']=0;
                        if($sql_r['aopseq']=="") $sql_r['aopseq']=0;
						 if($sql_r['bush']=="") $sql_r['bush']=0;
						 if($sql_r['bushseq']=="") $sql_r['bushseq']=0;
						 if($sql_r['peach']=="") $sql_r['peach']=0;
						 if($sql_r['peachseq']=="") $sql_r['peachseq']=0;
						 if($sql_r['yd']=="") $sql_r['yd']=0;
						 if($sql_r['ydseq']=="") $sql_r['ydseq']=0;

						 if($sql_r['printdiff']=="") $sql_r['printdiff']=0;
						 if($sql_r['embrodiff']=="") $sql_r['embrodiff']=0;
						 if($sql_r['washdiff']=="") $sql_r['washdiff']=0;
						 if($sql_r['spwdiff']=="") $sql_r['spwdiff']=0;
                        
                        $sql_r=removenumeric($sql_r);
                        $smv_arr[]=implode("_",$sql_r);
                    }

                    $smv_srt=rtrim(implode("__",$smv_arr),"__");
                    if(count($sql_d)){
                        $set_breck_down=$smv_srt;
                    }
                   // echo count($sql_d)."hhh".$set_breck_down;
                    $data_array=explode("__",$set_breck_down);
                    // echo '<pre>';
                    // print_r($data_array); die;
                    if($data_array[0]=="")
                    {
                        $data_array=array();
                    }
                    if ( count($data_array)>0)
                    {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                            $i++;
                            $data=explode('_',$row);
                            $tot_cutsmv_qnty+=$data[6];
                            $tot_finsmv_qnty+=$data[8];
							if ($set_smv_id==4 || $set_smv_id==6) { if($data[19]=="") $data[19]=$data[4]; }
							//echo "==".$data[4].'**';
                            ?>
                            <tr id="settr_<?=$i; ?>" align="center">
                                <td><?=create_drop_down( "cboitem_".$i, 150, get_garments_item_array(2), "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id );",$disabled,'' ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<?=$i; ?>" name="txtsetitemratio_<?=$i; ?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$i; ?>);" value="<?=$data[1]; ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /></td>
                                <td>
                                    <input type="text" id="smv_<?=$i;?>" name="smv_<?=$i;?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$i;?>);"  value="<?=$data[2]; ?>" <?=$disab; ?> onDblClick="check_smv_set_popup(<?=$i;?>);" /> 
                                    <input type="hidden" id="smvset_<?=$i;?>" name="smvset_<?=$i;?>" style="width:30px" value="<?=$data[3]; ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="cutsmv_<?=$i;?>" name="cutsmv_<?=$i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_cutsmv(<?=$i;?>);"  value="<?=$data[6]; ?>" <?=$disab; ?> /> 
                                    <input type="hidden" id="cutsmvset_<?=$i;?>" name="cutsmvset_<?=$i;?>" style="width:30px" value="<?=$data[7]; ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="finsmv_<?=$i;?>" name="finsmv_<?=$i;?>" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(<?=$i;?>);" value="<?=$data[8]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} echo $readonly; ?> /> 
                                    <input type="hidden" id="finsmvset_<?=$i;?>" name="finsmvset_<?=$i;?>" style="width:30px" value="<?=$data[9]; ?>" readonly/>
                                </td>
                                <td><?=create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); ?></td>
                                <td><?=create_drop_down( "emblish_".$i, 60, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); ?>
                                    <input type="text" id="printseq_<?=$i;?>" name="printseq_<?=$i;?>" style="width:20px"  class="text_boxes_numeric" value="<?=$data[10]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> />
                                    <?=create_drop_down( "printdiff_".$i, 80, $difficulty_arr, "",1," -- Select --",$data[28],"",$disabled,''); ?>                                
                                </td>
                                <td><?=create_drop_down( "embro_".$i, 60, $yes_no, "",1," -- Select--", $data[11], "",$disabled,'' ); ?>
                                    <input type="text" id="embroseq_<?=$i;?>" name="embroseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[12]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <?=create_drop_down( "embrodiff_".$i, 80, $difficulty_arr, "",1," -- Select --",$data[29],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "wash_".$i, 60, $yes_no, "",1," -- Select--", $data[13], "",$disabled,'' ); ?>
                                    <input type="text" id="washseq_<?=$i;?>" name="washseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[14]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <?=create_drop_down( "washdiff_".$i, 80, $difficulty_arr, "",1," -- Select --",$data[30],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "spworks_".$i, 60, $yes_no, "",1," -- Select--", $data[15], "",$disabled,'' ); ?>
                                    <input type="text" id="spworksseq_<?=$i;?>" name="spworksseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[16]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                    <?=create_drop_down( "spwdiff_".$i, 80, $difficulty_arr, "",1," -- Select --",$data[31],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "gmtsdying_".$i, 60, $yes_no, "",1," -- Select--", $data[17], "",$disabled,'' ); ?>
                                    <input type="text" id="gmtsdyingseq_<?=$i;?>" name="gmtsdyingseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[18]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><?=create_drop_down( "aop_".$i, 60, $yes_no, "",1," -- Select--", $data[20], "",$disabled,'' ); ?>
                                    <input type="text" id="aopseq_<?=$i;?>" name="aopseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[21]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                            	<td><?=create_drop_down( "brush_".$i, 60, $yes_no, "",1," -- Select--", $data[22], "",$disabled,'' ); ?>
                            	    <input type="text" id="brushseq_<?=$i;?>" name="brushseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[23]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                            	</td> 
                            	<td><?=create_drop_down( "peach_".$i, 60, $yes_no, "",1," -- Select--", $data[24], "",$disabled,'' ); ?>
                                    <input type="text" id="peachseq_<?=$i;?>" name="peachseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[25]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td> 
                                <td><?=create_drop_down( "yd_".$i, 60, $yes_no, "",1," -- Select--", $data[26], "",$disabled,'' ); ?>
                                    <input type="text" id="ydseq_<?=$i;?>" name="ydseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[27]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td>
                                    <input type="hidden" id="hidquotid_<?=$i;?>" name="hidquotid_<?=$i;?>" style="width:30px" class="text_boxes_numeric" value="<?=$data[19]; ?>" readonly/>
                                    <input type="button" id="increaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<?=$i; ?>);" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <input type="button" id="decreaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<?=$i; ?> ,'tbl_set_details' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                </td> 
                            </tr>
                            <?
                        }
                    }
                    else
                    {
                        ?>
                        <tr id="settr_1" align="center">
                            <td><? echo create_drop_down( "cboitem_1", 150, get_garments_item_array(2), "",1,"--Select--", $ws_garments_item, "check_duplicate(1,this.id );",$disabled,$gmstitem_string ); ?></td>
                            <td><input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);"  value="<? if ($unit_id==1){echo "1"; } else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /></td>
                            <td>
                                <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="0" <? echo $readonly ?> onDblClick="check_smv_set_popup(1);"  /> 
                                <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1)" value="0" /> 
                                <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1)" value="0" /> 
                                <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td><? echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", 0, "",'','' ); ?>
                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="" />

                                <? echo create_drop_down( "printdiff_1", 80, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="" />
                                <? echo create_drop_down( "embrodiff_1", 80, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                                <? echo create_drop_down( "washdiff_1", 80, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="" />
                                <? echo create_drop_down( "spwdiff_1", 90, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "brush_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "peach_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "yd_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td>
                                <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1);" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                            </td> 
                        </tr>
                    <? 
                    } 
                    ?>
                </tbody>
            </table>
            <table width="1350" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="150">Total</th>
                        <th width="40"><input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px" value="<? if($tot_set_qnty!=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly /></th>
                        <th width="40"><input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px" value="<? if($tot_smv_qnty!=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly /></th>
                        <th width="40"><input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px" value="<? if($tot_cutsmv_qnty!=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" readonly /></th>
                        <th width="40"><input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px" value="<? if($tot_finsmv_qnty!=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" readonly /></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table width="1350" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/></td> 
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
    </body>   
    <script>
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Job Ref Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val('');
				$('#termsconditionID_'+i).val("");
				$('#termscondition_'+i).removeAttr("onBlur").attr("onBlur","row_sequence("+i+")");

				$('#sltd_'+i).val(i);
				//$('#sltd_'+i).html(i);
			}
		}

		function fn_deletebreak_down_tr(rowNo)
		{
			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}
		}

		function fnc_order_entry_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('termscondition_'+i,'Internal Ref')==false)
				{
					return;
				}
				var internal_ref = $('#termscondition_'+i).val();

				data_all+=get_submitted_data_string('txt_job_no*job_insert_date*insert_date*termscondition_'+i+'*termsconditionID_'+i,"../../../",i);
			}  //alert(data_all);return;
			var data="action=save_update_delete_wo_order_entry_ref&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+data_all;
			freeze_window(operation);
			http.open("POST","order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_order_entry_terms_condition_reponse;
		}

		function fnc_order_entry_terms_condition_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				//alert(reponse);
				release_freezing();
				if(reponse[0]==11)
				{
					alert("Duplicate Internal Ref Not Allow");
					return;
				}
				if (reponse[0].length>2) reponse[0]=10;

				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
				set_button_status(1, permission, 'fnc_order_entry_terms_condition',1);
			}
		}
		//Row Sequence

		function row_sequence(row_id)
		{
			var row_num=$('#tbl_termcondi_details tbody tr').length-1;
			var txt_seq=$('#termscondition_'+row_id).val();
			if(txt_seq=="")
			{
				return;
			}
			for(var j=1; j<=row_num; j++)
			{
				if(j==row_id)
				{
					continue;
				}
				else
				{
					var txt_seq_check=$('#termscondition_'+j).val();
					//alert(txt_seq_check);
					if(txt_seq==txt_seq_check)
					{
						alert("Duplicate Seq No. "+txt_seq);
						$('#termscondition_'+row_id).val('');
						return;
					}
				}
			}
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <? echo load_freeze_divs ("../../../",$permission);  ?>
    <fieldset>
    <form id="termscondi_1" autocomplete="off">
    <?
		if($db_type==0)
		{
			$year_cond="  insert_date as insert_date";
			$year_cond2="  YEAR(insert_date) as insert_year";
		}
		else if($db_type==2)
		{
			$year_cond="insert_date as insert_date";
			$year_cond2=" to_char(insert_date,'YYYY') as insert_year";
		}
		$job_insert_date= return_field_value("$year_cond","wo_po_details_master","job_no=$txt_job_no","insert_date");
		$insert_date= return_field_value("$year_cond2","wo_po_details_master","job_no=$txt_job_no","insert_year");

		if($db_type==0) $insert_year_cond="  YEAR(job_insert_date)=$insert_date";
		else if($db_type==2) $insert_year_cond=" to_char(job_insert_date,'YYYY')=$insert_date";
    ?>
    <input type="text" id="txt_job_no" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$txt_job_no) ?>"/>
    <input type="hidden" id="job_insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$job_insert_date) ?>"/>
    <input type="hidden" id="insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$insert_date) ?>"/>

    <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
        <thead>
            <tr>
            	<th width="30">Sl</th><th width="150">Internal Ref</th><th width="80"></th>
            </tr>
        </thead>
        <tbody>
        <?
        $current_year=date("Y",time());
        $data_array=sql_select("select max(internal_ref) as internal_ref from wo_order_entry_internal_ref where  $insert_year_cond");// quotation_id='$data'
        $max_ref=$data_array[0][csf('internal_ref')]+1;
        $data_array=sql_select("select id as update_id, internal_ref from wo_order_entry_internal_ref where job_no=$txt_job_no order by id asc");// quotation_id='$data'

        if( count($data_array)>0)
        {
			$i=0;
			foreach( $data_array as $row )
			{
				$i++;
				?>
				<tr id="settr_1" align="center">
                    <td>
                    	<input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $i;?>"    />
                    </td>
                    <td>
                        <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:140px"   class="text_boxes_numeric" value="<? echo $row[csf('internal_ref')]; ?>" onBlur="row_sequence(<? echo $i; ?>); "   />
                        <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('update_id')]?>"  />
                    </td>
                    <td>
                    	<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />                    	<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                    </td>
				</tr>
				<?
			}
        }
        else
        {
			$k=1; ?>
                <tr id="settr_1" align="center">
                    <td>
                        <input type="text" id="sltd_<? echo $k;?>"   name="sltd_<? echo $k;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $k;?>"    />
                    </td>
                    <td>
                        <input type="text" id="termscondition_<? echo $k;?>"  onBlur="row_sequence(<? echo $k; ?>); "   name="termscondition_<? echo $k;?>" style="width:140px"   class="text_boxes_numeric" value="<? echo $max_ref;?>"    />
                        <input type="hidden" id="termsconditionID_<? echo $k;?>"   name="termsconditionID_<? echo $k;?>" style="width:50px" value=""  />
                    </td>
                    <td>
                        <input type="button" id="increase_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?> )" />                	<input type="button" id="decrease_<? echo $k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> );" /></td>
                </tr>
			<?
			$k++;
        }
        ?>
        </tbody>
    </table>
    <table width="350" cellspacing="0" class="" border="0">
        <tr>
        	<td align="center" height="15" width="100%"> </td>
        </tr>
        <tr>
            <td align="center" width="100%" class="button_container">
            <?
            if ( count($data_array)>0)
            {
            	echo load_submit_buttons( $permission, "fnc_order_entry_terms_condition", 1,0 ,"reset_form('termscondi_1','','','','')",1) ;
            }
            else
            {
           		echo load_submit_buttons( $permission, "fnc_order_entry_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
            }
            ?>
            </td>
        </tr>
    </table>
    </form>
    </fieldset>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="save_update_delete_wo_order_entry_ref")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$job=str_replace("'","",$txt_job_no);
		$insert_date=str_replace("'","",$insert_date);
		if($db_type==0) $insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
		else if($db_type==2) $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
		$id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		$field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$internal_ref="termscondition_".$i;
			$internal_cond="termscondition_".$i;
			
			if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$$internal_cond."  $insert_year_cond" )==1)
			{
				echo "11**0";
				disconnect($con);die;
			}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
			$id=$id+1;
		}
		$rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$job;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$job;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job;
			}
			else{
				oci_rollback($con);
				echo "10**".$job;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$job=str_replace("'","",$txt_job_no);
		$insert_date=str_replace("'","",$insert_date);
		if($db_type==0) $insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
		else if($db_type==2) $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
		$data_array2=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref");// quotation_id='$data'
		$max_ref=$data_array2[0][csf('internal_ref')];
		$id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		$field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		$field_array_up="job_no*internal_ref*job_insert_date*insert_date";
		$add_comma=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$internal_ref="termscondition_".$i;
			$internal_cond=str_replace("'","",$$internal_ref);
			$update_id="termsconditionID_".$i;
			$mst_update_id=str_replace("'","",$$update_id);
			if($mst_update_id!="")
			{
				
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$internal_cond." and id!=$mst_update_id $insert_year_cond " )==1)
				{
					echo "11**0";
					disconnect($con);die;
				}
				
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_array_up[str_replace("'",'',$$update_id)] =explode("*",("".$txt_job_no."*".$internal_cond."*".$job_insert_date."*'".$pc_date_time."' "));
			}
			else if($mst_update_id=="")
			{
				
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$internal_cond."  $insert_year_cond" )==1)
				{
					echo "11**0";
					disconnect($con);die;
				}
				
				if ($add_comma!=1) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
				$id=$id+1;
				$add_comma++;
			}
		}
		$rID=execute_query(bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr ));
		if($data_array!="")
		{
			$rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		}
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$job;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$job;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job;
			}
			else{
				oci_rollback($con);
				echo "10**".$job;
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}

		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Style Ref </th>
                    <th>Internal Ref</th>
                    <th>File No</th>
                    <th>Order No</th>
                    <th colspan="2">Ship Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
                </tr>
            </thead>
            <tr class="general">
                <td>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10">
                <?
                	//echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
                	echo load_month_buttons(1);  ?>
                </td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
 
if($action=="create_po_search_list_view")
{ 
	$data=explode('_',$data); 
	// echo $data[10];die;

	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }

	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";

	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	
	$order_cond=""; $job_cond=""; $style_cond="";$file_no_cond="";$internal_ref_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond="";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond="";
		if ($file_no!="")  $file_no_cond=" and b.file_no='".trim($file_no)."' ";
		if ($internal_ref!="") $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond="";
			if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."%' ";
		if ($internal_ref!="") $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond="";
			if ($file_no!="")  $file_no_cond=" and b.file_no like '".trim($file_no)."%' ";
		if ($internal_ref!="") $internal_ref_cond=" and b.grouping like '".trim($internal_ref)."%' ";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond="";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond="";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."' ";
		if ($internal_ref!="") $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."' ";
	}

 

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($db_type==0)
	{
		$yearCond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
		$dateDiff="DATEDIFF(pub_shipment_date,po_received_date)";
	}
	else if($db_type==2)
	{
		$yearCond="to_char(a.insert_date,'YYYY')";
		$dateDiff="(pub_shipment_date - po_received_date)";
	}
	if ($data[2]==0)
	{
		$arr=array(2=>$buyer_arr,9=>$item_category);
		$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $dateDiff as  date_diff, $yearCond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,Job Qty.,Repeat No,PO number,PO Qty.,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "50,40,100,100,70,90,50,70,65,65,70,70,50","950","300",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,order_repeat_no,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,1,1,0,1,3,0,0,0,0');
	}
	else
	{
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array (2=>$company_arr,3=>$buyer_arr,5=>$item_category);
		$sql= "select a.job_no_prefix_num, a.company_name, a.job_no, a.buyer_name, a.style_ref_no, a.garments_nature, $yearCond as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst is not null) and a.garments_nature=$data[5] and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";

		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","950","300",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$company_id=return_field_value("company_name","wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
	$update_period_id=$po_current_date_data=$cost_control_source=$set_smv_id=$copy_quotation=0;
	
	$sqlVariable=sql_select("select variable_list, po_update_period, po_current_date, cost_control_source, publish_shipment_date, copy_quotation from variable_order_tracking where company_name ='$company_id' and variable_list in (32,33,47,53,20) and is_deleted=0 and status_active=1");
	
	foreach($sqlVariable as $result)
	{
		if($result[csf('variable_list')]==32) $update_period_id=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date_data=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
		else if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
	}
	unset($sqlVariable);
	//echo $update_period_id.'='.$po_current_date_data.'='.$set_smv_id.'='.$cost_control_source;

	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	$data_array=sql_select("SELECT id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, repeat_job_no, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id,fit_id, is_repeat, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, season_year, brand_id, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant, qlty_label, style_owner, sustainability_standard, fab_material, quality_level, product_group, working_company_id, working_location_id, style_source from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down('requires/order_entry_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant');\n";
		echo "load_drop_down('requires/order_entry_controller', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory');\n";
		echo "sub_dept_load('".$row[csf("buyer_name")]."','".$row[csf("product_dept")]."');\n";

		//echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_style_from').value = '".$row[csf("style_source")]."';\n";
		echo "document.getElementById('cbo_sustainability_standard').value = '".$row[csf("sustainability_standard")]."';\n"; 
		echo "document.getElementById('cbo_fab_material').value = '".$row[csf("fab_material")]."';\n"; 
		echo "document.getElementById('cbo_order_nature').value = '".$row[csf("quality_level")]."';\n";
		echo "document.getElementById('cbo_product_group').value = '".$row[csf("product_group")]."';\n"; 
		if($set_smv_id==3 || $set_smv_id==8 || $set_smv_id==9)
		{
			echo "$('#cbo_buyer_name').attr('disabled',true);\n";
			echo "$('#txt_style_ref').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#cbo_buyer_name').attr('disabled',false);\n";
			echo "$('#txt_style_ref').attr('disabled',false);\n";
		}
		get_buyer_config($row[csf("buyer_name")]);
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n";
		echo "document.getElementById('po_update_period_maintain').value = '".$update_period_id."';\n";
		echo "document.getElementById('po_current_date_maintain').value = '".$po_current_date_data."';\n";
		
		$working_location_dropdown='';
		
		$working_location_dropdown = create_drop_down( "cbo_working_location_id", 130, "select id,location_name from lib_location where company_id='".$row[csf("working_company_id")]."' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "");
		
		echo "document.getElementById('cbo_working_factory').value = '".$row[csf("working_company_id")]."';\n";
		echo "document.getElementById('sew_location').innerHTML = '".$working_location_dropdown."';\n";
		echo "document.getElementById('cbo_working_location_id').value = '".$row[csf("working_location_id")]."';\n";
		
		$current_date=date('d-m-Y');
		if($po_current_date_data==1)
		{
			echo "document.getElementById('txt_po_received_date').value = '".$current_date."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";
		}
		else
		{
			echo "document.getElementById('txt_po_received_date').value = '';\n";
			echo "$('#txt_po_received_date').attr('disabled',false);\n";
		}

		if($row[csf("is_repeat")]==1)
		{
			echo "$('#chk_is_repeat').prop('checked', true);\n";
		}
		else
		{
			echo "$('#chk_is_repeat').prop('checked', false);\n";
		}
		echo "check_tna_templete('".$row[csf("buyer_name")]."');\n";
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";
		echo "document.getElementById('txt_repeat_job_no').value = '".$row[csf("repeat_job_no")]."';\n";
		
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('cbo_packing').value = '".$row[csf("packing")]."';\n";
		echo "document.getElementById('cbo_fit_id').value = '".$row[csf("fit_id")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_ship_mode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".number_format($row[csf("set_smv")],2)."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_job_qty').value = '".number_format($row[csf("job_quantity")])."';\n";
		echo "document.getElementById('txt_avgUnit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		echo "document.getElementById('txt_total_price').value = '".number_format($row[csf("total_price")],2)."';\n";
		//echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_season', 'season_td');\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		//$brand_dropdown = create_drop_down( "cbo_brand_id", 150, "select id, brand_name from lib_buyer_brand brand where buyer_id='".$row[csf("buyer_name")]."' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
		//echo "document.getElementById('brand_td').innerHTML = '".$brand_dropdown."';\n";
		//echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_brand', 'brand_td');\n";

		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";
		echo "document.getElementById('cbo_style_owner').value = '".$row[csf("style_owner")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("id")]."';\n";
		echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("gmts_item_id")]."', 'load_drop_gmts_item', 'itm_td') ;\n";
	}
	$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	$isapproved=$sql_data[0][csf("approved")];
	if ($isapproved==1)
	{
		echo "document.getElementById('budgetApp_td').innerHTML = 'Pre Cost Approved, Any Change will be not allowed.';\n";
	}
	else if ($isapproved==3)
	{
		echo "document.getElementById('budgetApp_td').innerHTML = 'Pre Cost Partially Approved, Any Change will be not allowed.';\n";
	}
	else
	{
		echo "document.getElementById('budgetApp_td').innerHTML = '';\n";
	}

	if($cost_control_source==2)
	{
		$quotation_id=$data_array[0][csf("quotation_id")];
		$sql_qRate=sql_select("select sum(price_with_commn_pcs) as rate from wo_price_quotation_costing_mst where quotation_id='$quotation_id' and status_active=1 and is_deleted = 0 group by quotation_id");

		$qutation_rate=$sql_qRate[0][csf("rate")];
		echo "document.getElementById('txt_quotation_price').value = '".$qutation_rate."';\n";
	}
	else if($cost_control_source==1 || $cost_control_source==5 || $cost_control_source==6 || $cost_control_source==8 )
	{
		$quotation_id=$data_array[0][csf("quotation_id")];
		$sql_qRate=sql_select("select sum(confirm_fob) as rate from qc_confirm_mst where cost_sheet_id='$quotation_id' and status_active=1 and is_deleted = 0 group by cost_sheet_id");

		$qutation_rate=$sql_qRate[0][csf("rate")];
		echo "document.getElementById('txt_quotation_price').value = '".$qutation_rate."';\n";
	}

	$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,

	 sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$data' ");
	foreach ($projected_data_array as $row_val)
	{
		$job_projected_price=0;
		$job_projected_price=$row_val[csf("job_projected_total")]/$row_val[csf("job_projected_qty")];
	    echo "document.getElementById('txt_proj_qty').value = '".number_format($row_val[csf("job_projected_qty")])."';\n";
		echo "document.getElementById('txt_proj_avgUnit_price').value = '".number_format($job_projected_price,4)."';\n";
		echo "document.getElementById('txt_proj_total_price').value = '".number_format($row_val[csf("job_projected_total")],2)."';\n";
		echo "document.getElementById('txt_orginProj_qty').value = '".number_format($row_val[csf("projected_qty")])."';\n";
		echo "document.getElementById('txt_orginProj_total_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_orginProj_total_amt').value = '".number_format($row_val[csf("projected_amount")],2)."';\n";
	}
	if($set_smv_id==2 && $copy_quotation==1){
		echo "$('#cbo_order_uom').attr('disabled',true);\n";
	}
	exit();
}

if ($action=="load_drop_down_tna_task")
{
	$data=explode("_",$data);

	$sql_task = "SELECT a.id, task_template_id, lead_time, material_source, total_task, tna_task_id, deadline, execution_days, notice_before, a.sequence_no, for_specific, b.task_catagory, b.task_name FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific, lead_time";
	$result = sql_select( $sql_task ) ;
	$tna_template = array();
	$i=0; $k=0; $j=0;
	foreach( $result as $row )
	{
		if (!in_array($row[csf("task_template_id")],$template))
		{
			$template[]=$row[csf("task_template_id")];
			if ( $row[csf("for_specific")]==0 )
			{
				$tna_template[$i]['lead']=$row[csf('lead_time')];
				$tna_template[$i]['id']=$row[csf('task_template_id')];
				$i++;
			}
			else
			{
				if(!in_array($row[csf('for_specific')],$tna_template_spc)) { $j=0; $tna_template_spc[]=$row[csf("for_specific")]; }
				$tna_template_buyer[$row[csf('for_specific')]][$j]['lead']=$row[csf('lead_time')];
				$tna_template_buyer[$row[csf('for_specific')]][$j]['id']=$row[csf('task_template_id')];
				$j++;
			}
			$k++;
		}
    }

	$remain_days=datediff( "d", $data[0], $data[1] );
	$template_id=get_tna_template($remain_days,$tna_template,$data[2]);
	//echo $template_id;
	if($db_type==0) $short_name_cond="concat(a.sequence_no,'-',b.task_short_name)";
	else if($db_type==2) $short_name_cond="a.sequence_no || '-' || b.task_short_name";
	//echo "select a.id, $short_name_cond as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no";
	echo create_drop_down( "cbo_tna_task", 80, "select a.id, $short_name_cond as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no","id,task_short_name", 1, "-- Select --", "", "" );
	exit();
}

if ($action=="show_po_active_listview")
{
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	//echo $data;
	$sql= "select country_id, item_number_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type, sum(order_quantity) as po_qty from wo_po_color_size_breakdown where po_break_down_id='$data' and is_deleted=0 and status_active in (1,2,3) group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type order by country_ship_date";
	?>
	 <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Delivery Country</th>
                <th width="80">Code</th>
                <th width="80">Country</th>
                <th width="80">Country Code</th>
                <th width="120">Item</th>
                <th width="80">Cut-Off</th>
                <th width="80">Country Ship Date</th>
                <th width="40">Pack Type</th>
                <th>Country Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:790px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($row[csf("pack_type")]==''){
                	$row[csf("pack_type")]=0;
                }
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("country_id")].'_'.$row[csf("item_number_id")].'_'.$row[csf("country_ship_date")].'_'.$data.'_'.$row[csf("code_id")].'_'.$row[csf("ultimate_country_id")].'_'.$row[csf("ul_country_code")].'_'.$row[csf("pack_type")]; ?>','populate_country_details_form_data','requires/order_entry_controller');">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="80"><? echo $country_arr[$row[csf("country_id")]]; ?></td>
                        <td width="80"><? echo $country_code_arr[$row[csf("code_id")]]; ?></td>
                        <td width="80"><? echo $country_arr[$row[csf("ultimate_country_id")]]; ?></td>
                        <td width="80"><? echo $country_code_arr[$row[csf("ul_country_code")]]; ?></td>
                        <td width="120"><? echo $garments_item[$row[csf("item_number_id")]]; ?></td>
						<td width="80"><? echo $cut_up_array[$row[csf("cutup")]];  ?></td>
                        <td width="80"><? echo change_date_format($row[csf("country_ship_date")]);  ?></td>
                        <td width="40" align="center"><? echo $row[csf("pack_type")]; ?></td>
						<td align="right"><? echo  number_format($row[csf("po_qty")]); ?></td>
					</tr>
				<?
				$i++;
            }
			?>
			</table>
		</div>
	<?
	exit();
}

if ($action=="show_act_po_active_listview")
{
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	$sql="SELECT a.acc_po_no, a.acc_ship_date, a.acc_po_status, sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.is_deleted=0 and b.is_deleted=0 and a.po_break_down_id=$data group by a.acc_po_no, a.acc_ship_date, a.acc_po_status";
	?>
	 <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100">Actual PO NO</th>
                <th width="60">Actual PO Qty</th>
                <th width="60">Ship Date</th>
                <th width="60">Status</th>
            </thead>
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100"><? echo $row[csf("acc_po_no")]; ?></td>
                        <td width="60"><? echo $row[csf("po_qty")]; ?></td>
                        <td width="60"><? echo change_date_format($row[csf("acc_ship_date")]);  ?></td>
                        <td width="60"><? echo $row_status[$row[csf("acc_po_status")]]; ?></td>
					</tr>
				<?
				$i++;
            }
			?>
			</table>
		</div>
	<?
	exit();
}

if($action=="populate_country_details_form_data")
{
	$ex_data=explode('_',$data);
	$ret_matrix_type=return_field_value("matrix_type"," wo_po_break_down","id='$ex_data[3]' and is_deleted=0 and status_active=1");
	//echo $ret_matrix_type; die;
	$prod_country_arr=array(); $prod_item_arr=array(); $prod_color_arr=array(); $prod_size_arr=array(); $is_production_found=1;

	if($is_production_found==1)
	{
		$sql_check=sql_select("select a.order_id, a.country_id, b.gmt_item_id, a.color_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and  a.order_id='$ex_data[3]' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
		//echo "select a.order_id, a.country_id, b.gmt_item_id, a.color_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and  a.order_id='$ex_data[3]' group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id";group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id
		if(count($sql_check)>0)
		{
			foreach($sql_check as $dts)
			{
				$prod_country_arr[$dts[csf("order_id")]][$dts[csf("country_id")]]=$dts[csf("country_id")];
				$prod_item_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]]=$dts[csf("gmt_item_id")];
				$prod_color_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$color_library[$dts[csf("color_id")]]]=$dts[csf("color_id")];
				$prod_size_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$color_library[$dts[csf("color_id")]]][$size_library[$dts[csf("size_id")]]]=$dts[csf("size_id")];
				$is_production_found=0;
			}
			unset($sql_check);
		}
	}

	if($is_production_found==1)
	{
		//$sql_check=sql_select("select a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id='$ex_data[3]'  and a.is_deleted=0 and a.status_active in (1,2,3) and b.status_active=1 and b.is_deleted=0 ");
		$sql_check=sql_select("select a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b,pro_garments_production_mst c where b.color_size_break_down_id=a.id  and c.id=b.mst_id and a.po_break_down_id=c.po_break_down_id  and a.po_break_down_id='$ex_data[3]' and  b.production_qnty>0  and a.is_deleted=0 and a.status_active in (1,2,3) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
		//echo "select a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id='$ex_data[3]' group by a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id";group by a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id
		if(count($sql_check)>0)
		{
			foreach($sql_check as $dts)
			{
				$prod_country_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]]=$dts[csf("country_id")];
				$prod_item_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]]=$dts[csf("item_number_id")];
				$prod_color_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]]=$dts[csf("color_number_id")];
				$prod_size_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]][$size_library[$dts[csf("size_number_id")]]]=$dts[csf("size_number_id")];
				$is_production_found=0;
			}
			unset($sql_check);
		}
	}

	if($is_production_found==1)
	{
		$sql_check=sql_select("select a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, wo_booking_dtls b where b.color_size_table_id=a.id and a.po_break_down_id='$ex_data[3]' and a.is_deleted=0 and a.status_active in (1,2,3) and b.status_active=1 and b.is_deleted=0 ");
		//echo "select a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id='$ex_data[3]' group by a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id";group by a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id
		if(count($sql_check)>0)
		{
			foreach($sql_check as $dts)
			{
				$prod_country_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]]=$dts[csf("country_id")];
				$prod_item_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]]=$dts[csf("item_number_id")];
				$prod_color_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]]=$dts[csf("color_number_id")];
				$prod_size_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]][$size_library[$dts[csf("size_number_id")]]]=$dts[csf("size_number_id")];
				$is_production_found=0;
				unset($sql_check);
			}
		}
		
	}

	if($is_production_found==1)
	{
		$sql_check=sql_select("select a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, wo_booking_dtls b where b.po_break_down_id=a.po_break_down_id and a.po_break_down_id='$ex_data[3]'  and a.is_deleted=0 and a.status_active in (1,2,3) and b.status_active=1 and b.is_deleted=0 ");
		//echo "select a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id='$ex_data[3]' group by a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id";group by a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id
	if(count($sql_check)>0)
		{
			foreach($sql_check as $dts)
			{
				$prod_country_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]]=$dts[csf("country_id")];
				$prod_item_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]]=$dts[csf("item_number_id")];
				$prod_color_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]]=$dts[csf("color_number_id")];
				$prod_size_arr[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$color_library[$dts[csf("color_number_id")]]][$size_library[$dts[csf("size_number_id")]]]=$dts[csf("size_number_id")];
				$is_production_found=0;
			}
			unset($sql_check);
		}
	}

	$set_arr=array(); $company_name=0;
	$po_sql_data_arr=sql_select( "select a.order_uom, a.company_name, b.unit_price, a.set_break_down, a.total_set_qnty, b.id from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$ex_data[3]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in (1,2,3)");
	foreach($po_sql_data_arr as $sRow)
	{
		$set_arr[$sRow[csf("id")]]['uom']=$sRow[csf("order_uom")];
		$set_arr[$sRow[csf("id")]]['rate']=$sRow[csf("unit_price")];
		$set_arr[$sRow[csf("id")]]['set_break_down']=$sRow[csf("set_break_down")];
		$set_arr[$sRow[csf("id")]]['set_qty']=$sRow[csf("total_set_qnty")];
		$company_name=$sRow[csf("company_name")];
	}
	unset($po_sql_data_arr);
	if( $ex_data[0]=="" || $ex_data[0]==0) $country_id_cond=""; else  $country_id_cond="and country_id='$ex_data[0]'";
	if( $ex_data[4]!=0 && $ex_data[4]!='') $code_cond="and code_id='$ex_data[4]'"; else $code_cond="";
	if( $ex_data[5]!=0 && $ex_data[5]!='') $ultimate_country_cond="and ultimate_country_id='$ex_data[5]'"; else $ultimate_country_cond="";
	if( $ex_data[6]!=0 && $ex_data[6]!='') $country_code_cond="and ul_country_code='$ex_data[6]'"; else $country_code_cond="";
	if( $ex_data[7]!="" && $ex_data[7]!=0) $pack_type_cond="and pack_type='$ex_data[7]'"; else $pack_type_cond="";
	 $sql= "select id, country_id, code_id, ultimate_country_id, ul_country_code, item_number_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, excess_cut_perc, article_number, order_rate, order_quantity, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, color_order, size_order from wo_po_color_size_breakdown where po_break_down_id='$ex_data[3]' and item_number_id='$ex_data[1]' and country_ship_date='$ex_data[2]' $country_id_cond $code_cond $ultimate_country_cond $country_code_cond $pack_type_cond and is_deleted=0 and status_active in (1,2,3) order by  size_order";
	 //, color_order Asc, size_order Asc
	//echo $sql; die;

	$result = sql_select($sql); $color_array=array(); $size_array=array(); $all_data_arr=array(); $prod_check_color_arr=array(); $prod_check_size_arr=array();
	$m=1; $all_data="";
	foreach($result as $row)
	{
		if($m==1)
		{
			//echo $prod_country_arr[$ex_data[3]][$row[csf("country_id")]];
			if($row[csf("country_id")]=='') $row[csf("country_id")]=0;
			if($row[csf("ultimate_country_id")]=='') $row[csf("ultimate_country_id")]=0;
			echo "$('#cbo_gmtsItem_id').val('".$row[csf("item_number_id")]."');\n";

			if($prod_item_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]]!="")
				echo "$('#cbo_gmtsItem_id').attr('disabled','disabled');\n";
			else
				echo "$('#cbo_gmtsItem_id').removeAttr('disabled','disabled')".";\n";

			echo "$('#cbo_deliveryCountry_id').val('".$row[csf("country_id")]."');\n";
			echo "$('#hid_prev_country').val('".$row[csf("country_id")]."');\n";

			if($prod_country_arr[$ex_data[3]][$row[csf("country_id")]]!="")
				echo "$('#cbo_deliveryCountry_id').attr('disabled','disabled');\n";
			else
				echo "$('#cbo_deliveryCountry_id').removeAttr('disabled','disabled')".";\n";

			echo "load_drop_down( 'requires/order_entry_controller', ".$row[csf("country_id")].", 'load_dorp_down_code', 'code_td' );\n";
			//echo "load_drop_down( 'requires/order_entry_controller', ".$row[csf("country_id")].", 'load_dorp_down_ultimate_country', 'ultimate_td' );\n";
			echo "load_drop_down( 'requires/order_entry_controller', ".$row[csf("ultimate_country_id")].", 'load_dorp_down_countryCode', 'countryCode_td' );\n";
			echo "$('#cbo_code_id').val('".$row[csf("code_id")]."');\n";
			echo "$('#cbo_country_id').val('".$row[csf("ultimate_country_id")]."');\n";
			echo "$('#cbo_countryCode_id').val('".$row[csf("ul_country_code")]."');\n";
			echo "$('#txt_cutup_date').val('".change_date_format($row[csf("cutup_date")])."');\n";
			echo "$('#cbo_cutOff_id').val('".$row[csf("cutup")]."');\n";
			echo "$('#txt_countryShip_date').val('".change_date_format($row[csf("country_ship_date")])."');\n";
			echo "$('#txt_breakdownGrouping').val('".$row[csf("pack_type")]."');\n";
			echo "$('#txt_pcsQty').val('".$row[csf("pcs_pack")]."');\n";
			echo "$('#txt_is_update').val(1);\n";
			$m++;
		}

		$color_name=""; $size_name="";
		$color_name=$color_library[$row[csf("color_number_id")]];
		$size_name=$size_library[$row[csf("size_number_id")]];

		$prod_color=""; $prod_size=""; $prod_color_val=""; $prod_size_val="";
		$prod_color=$prod_color_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]][$color_name];
		$prod_size=$prod_size_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]][$color_name][$size_name];
		//echo $row[csf("item_number_id")].'='.$row[csf("country_id")];
		if($prod_color=="" || $prod_color==0) $prod_color_val=""; else  { $prod_color_val=$prod_color; $prod_check_color_arr[trim($color_name)]=$prod_color_val; }
		if($prod_size=="" || $prod_size==0) $prod_size_val=""; else  { $prod_size_val=$prod_size; $prod_check_size_arr[trim($size_name)]=$prod_size_val;}

		//$prod_check_color_arr[$color_name]=$prod_color_val;
		//$prod_check_size_arr[trim($size_name)]=$prod_size_val; color_order, size_order

		$color_array[$row[csf("color_order")]][$color_name]=$row[csf("color_number_id")];
		$size_array[$row[csf("size_order")]][$size_name]=$row[csf("size_number_id")];

		$all_data_arr[$color_name][$size_name]['po_qty']=$row[csf("order_quantity")];
		$all_data_arr[$color_name][$size_name]['rate']=$row[csf("order_rate")];
		$all_data_arr[$color_name][$size_name]['ex_per']=$row[csf("excess_cut_perc")];
		$all_data_arr[$color_name][$size_name]['art_no']=$row[csf("article_number")];
		$all_data_arr[$color_name][$size_name]['id']=$row[csf("id")];


		$item_id=$row[csf("item_number_id")];
		$avg_rate=$set_arr[$ex_data[3]]['rate'];
		$set_qnty=$set_arr[$ex_data[3]]['set_qty'];
		$set_breck_down=explode('__',str_replace("'","",$set_arr[$ex_data[3]]['set_break_down']));
		$item_ratio_arr=array();
		foreach($set_breck_down as $set_data)
		{
			$ex_set_data=explode('_',$set_data);
			$ex_item_id=$ex_set_data[0];
			$ex_item_ratio=$ex_set_data[1];
			$item_ratio_arr[$ex_item_id]=$ex_item_ratio;
		}

		$assort_data=0;
		$assort_data=$row[csf("assort_qty")].'!!'.$row[csf("solid_qty")];

		$order_total_amt=0; $color_size_rate=0; $color_size_poQty=0;
		$color_size_poQty=$row[csf("order_quantity")]/$item_ratio_arr[$item_id];
		$color_size_planCut=$row[csf("plan_cut_qnty")]/$item_ratio_arr[$item_id];
		$color_size_rate=number_format($row[csf("order_rate")],8)*$item_ratio_arr[$item_id];
		if($ret_matrix_type==4)
		{
			if ($all_data=="") $all_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$row[csf("pack_qty")].'**'.$row[csf("pcs_per_pack")].'**'.$assort_data; else $all_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$row[csf("pack_qty")].'**'.$row[csf("pcs_per_pack")].'**'.$assort_data;
		}
		else
		{
			if ($all_data=="") $all_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$assort_data; else $all_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$assort_data;
		}
		$all_color_size_id[$row[csf("id")]]=$row[csf("id")];
		//echo "append_color_size_row(1);\n";
	}
	$color_size_id_str=implode(",",$all_color_size_id);
	//print_r($prod_check_color_arr);
	echo "$('#color_size_break_down_all_data').val('".$all_data."');\n";
	echo "$('#size_all_id').val('".$color_size_id_str."');\n";
	$color_from_lib=return_field_value("color_from_library", "variable_order_tracking", "company_name='$company_name' and variable_list=23 and status_active=1 and is_deleted=0");
	if($ret_matrix_type==2 || $ret_matrix_type==3)
	{
		$ratio_data="";
		$sql_ratio= "select id, color_id, size_id, ratio_qty, ratio_rate from wo_po_ratio_breakdown where country_id='$ex_data[0]' and gmts_item_id='$ex_data[1]' and country_ship_date='$ex_data[2]' and po_id='$ex_data[3]' $code_cond $ultimate_country_cond $country_code_cond and is_deleted=0 and status_active=1";
		//echo $sql_ratio;
		$sql_ratio_result = sql_select($sql_ratio);
		foreach($sql_ratio_result as $row)
		{
			$color_name=$color_library[$row[csf("color_id")]];
			$size_name=$size_library[$row[csf("size_id")]];
			if ($ratio_data=="") $ratio_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$row[csf("ratio_qty")].'**'.$row[csf("ratio_rate")];  else $ratio_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$row[csf("ratio_qty")].'**'.$row[csf("ratio_rate")];
		}

		echo "$('#color_size_ratio_data').val('".$ratio_data."');\n";
	}

	$x=1; $html=""; //ksort($size_array); (issue id:3303)
	foreach($size_array as $sizeseq=>$sizedata)
	{
		foreach($sizedata as $size=>$size_val)
		{
			$disable="";
			if($prod_check_size_arr[trim($size)]!="") $disable="disabled"; else $disable="";
			$html=$html.'<tr align="center" id="trSize_'.$x.'"><td title="'.$size.'"><input type="text" name="txtSizeName[]" id="txtSizeName_'.$x.'" value="'.$size.'" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(2);" '.$disable.' /><input type="hidden" name="txtSizeId[]" id="txtSizeId_'.$x.'" value="'.$size_val.'" class="text_boxes" style="width:50px"/></td></tr>';
			$x++;
		}
	}
	$html=$html.'<tr align="center" id="trSize_'.$x.'"><td><input type="text" name="txtSizeName[]" id="txtSizeName_'.$x.'" value="" class="text_boxes" style="width:80px"  onKeyUp="append_color_size_row(2);"/><input type="hidden" name="txtSizeId[]" id="txtSizeId_'.$x.'" value="" class="text_boxes" style="width:50px"/></td></tr>';
	echo "$('#td_size').html('".$html."')".";\n";


	//echo $color_from_lib; color_size_break_down_all_data
	$i=1; $table=''; ksort($color_array);
	foreach($color_array as $colorseq=>$colordata)
	{
		foreach($colordata as $color=>$color_val)
		{
			$disable=""; $color_from="";
			if($prod_check_color_arr[trim($color)]!="") $disable="disabled"; else $disable="";
			if($disable=="") { if($color_from_lib==1) $color_from=' onDblClick="color_select_popup('.$i.')" readonly placeholder="Browse"'; else $color_from=' placeholder="Write"'; } else $color_from="";//
			$table=$table.'<tr align="center" id="trColor_'.$i.'"><td>'.$i.'</td><td><input type="text" name="txtColorName[]" id="txtColorName_'.$i.'" value="'.$color.'" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(1);" title="'.$color.'" '.$disable.' '.$color_from.'/><input type="hidden" name="txtColorId[]" id="txtColorId_'.$i.'" value="'.$color_val.'" class="text_boxes" style="width:50px"/></td></tr>';
			$i++;
		}
	}
	if($color_from_lib==1) $color_from=' onDblClick="color_select_popup('.$i.')" readonly placeholder="Browse"'; else $color_from=' placeholder="Write"';

	$table=$table.'<tr align="center" id="trColor_'.$i.'"><td>'.$i.'</td><td><input type="text" name="txtColorName[]" id="txtColorName_'.$i.'" value="" title="" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(1);" '.$color_from.'/><input type="hidden" name="txtColorId[]" id="txtColorId_'.$i.'" value=""  class="text_boxes" style="width:50px"/></td></tr>';

	echo "$('#td_color').html('".$table."')".";\n";
	echo "$('#breakdown_div').html('')".";\n";
	echo "$('#breakdownratio_div').html('')".";\n";
	exit();
}

if($action=="check_country")
{
	$data=explode("_",$data);
	$sql="Select country_id from wo_po_color_size_breakdown where po_break_down_id=$data[0] and country_id=$data[1] and is_deleted =0 and status_active=1";
	$data_array=sql_select($sql);
	$country=count($data_array);
	$cut_off=0;
	$sql_cut_off="Select cut_off from lib_country where id=$data[1] and is_deleted =0 and status_active=1";
	$res_cut_off=sql_select($sql_cut_off);
	if(count($res_cut_off)>0){
		$cut_off=$res_cut_off[0][csf('cut_off')];
	}
	echo $country."_".$cut_off."_".$data[1];
	exit();
}

if($action=="set_ship_date")
{
	$data=explode("_",$data);
	$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1) echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	else if($data[1]==2) echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	else if($data[1]==3) echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	exit();
}

if($action=="booking_no_with_approved_status")
{
	$data=explode("_",$data);
	//echo $data[0];
	//echo $data[1];
	if($data[1]=="")
	{
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row)
	{
		if($row[csf('is_approved')]==1)
		{
		  $approved_booking.=$row[csf('booking_no')].", ";
		}
		else
		{
		  $un_approved_booking.=$row[csf('booking_no')].", ";
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sql=sql_select("select cost_control_source from variable_order_tracking where company_name=$cbo_company_name and variable_list=53 order by id");
	$cost_control_source=$sql[0][csf('cost_control_source')];
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	$txt_style_ref="'".trim(str_replace($str_replace_check,' ',$txt_style_ref))."'";

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id("id", "wo_po_details_master", 1);
		//echo "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR('Y',insert_date)=".date('Y',time())." order by job_no_prefix_num desc"; die;
		if(str_replace("'","",$chk_is_repeat)==2)
		{
			if(str_replace("'","",$txt_repeat_no)=="")
			{
				if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_buyer_wise=$cbo_season_id";
				$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=$cbo_company_name and buyer_name=$cbo_buyer_name and style_ref_no=$txt_style_ref $season_cond");

				if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
				else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
			}
			else $repeat_no=str_replace("'","",$txt_repeat_no);
		}
		else $repeat_no=str_replace("'","",$txt_repeat_no);
		
		$txt_style_description=str_replace("'",'',$txt_style_description);
		$style_description='';
		$style_description=trim(str_replace($str_replace_check,'',$txt_style_description));
		
		$txt_remarks=str_replace("'",'',$txt_remarks);
		$mst_remarks='';
		$mst_remarks=trim(str_replace($str_replace_check,'',$txt_remarks));

		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')"; // cbo_brand_id

		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and $date_cond=".date('Y',time())." order by id DESC", "job_no_prefix", "job_no_prefix_num" ));

		$field_array="id, garments_nature, quotation_id,fit_id, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, repeat_job_no, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, is_repeat, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, season_buyer_wise, season_year, brand_id, factory_marchant, qlty_label, style_owner, sustainability_standard, fab_material, quality_level, product_group, working_company_id, working_location_id, style_source, is_deleted, status_active, inserted_by, insert_date";

		if(str_replace("'","",$txt_quotation_id)=="") $quotation_id=0; else $quotation_id=str_replace("'","",$txt_quotation_id);

		$data_array="(".$id.",".$garments_nature.",'".$quotation_id."',".$cbo_fit_id.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_location_name.",".$txt_style_ref.",'".$style_description."',".$txt_repeat_job_no.",".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$chk_is_repeat.",'".$repeat_no."',".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_packing.",'".$mst_remarks."',".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_id.",".$cbo_season_year.",".$cbo_brand_id.",".$cbo_factory_merchant.",".$cbo_qltyLabel.",".$cbo_style_owner.",".$cbo_sustainability_standard.",".$cbo_fab_material.",".$cbo_order_nature.",".$cbo_product_group.",".$cbo_working_factory.",".$cbo_working_location_id.",".$cbo_style_from.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "INSERT INTO wo_po_details_master (".$field_array.") VALUES ".$data_array; die; cbo_agent

		$field_array1="id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		/* for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."')";
			$add_comma++;
			$id1=$id1+1;
		} */
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				 disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."','".$set_breck_down_arr[20]."','".$set_breck_down_arr[21]."','".$set_breck_down_arr[22]."','".$set_breck_down_arr[23]."','".$set_breck_down_arr[24]."','".$set_breck_down_arr[25]."','".$set_breck_down_arr[26]."','".$set_breck_down_arr[27]."','".$set_breck_down_arr[28]."','".$set_breck_down_arr[29]."','".$set_breck_down_arr[30]."','".$set_breck_down_arr[31]."')";
			$total_smv_set+=$set_breck_down_arr[3];
			$add_comma++;
			$id1=$id1+1;
		}
		$tot_smv_qnty=str_replace("'",'',$tot_smv_qnty);
		if($tot_smv_qnty != number_format($total_smv_set,2,'.',''))
		{
			echo "SMV**";
			disconnect($con);die;
		}
		$flag=1; //echo "10**";
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;

		if($cost_control_source==1)
		{
			if(str_replace("'",'',$quotation_id)!=0)
			{
				$query="UPDATE qc_confirm_mst SET job_id=$id WHERE cost_sheet_id=$quotation_id and status_active=1 and is_deleted=0";
				$rIDt=execute_query($query,1);
				if($rIDt==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**".$rID.'='.$rID1.'='.$rIDt.'='.$flag.'='; die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID."**".$repeat_no."**".$quotation_id."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID."**".$repeat_no."**".$quotation_id."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}// Insert Here End------------------------------------------------------
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}

		if(str_replace("'","",$chk_is_repeat)==2)
		{
			if(str_replace("'","",$txt_repeat_no)=="")
			{
				if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_buyer_wise=$cbo_season_id";
				$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=$cbo_company_name and buyer_name=$cbo_buyer_name and style_ref_no=$txt_style_ref $season_cond");
				if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
				else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
			}
			else $repeat_no=str_replace("'","",$txt_repeat_no);
		}
		else $repeat_no=str_replace("'","",$txt_repeat_no);
		
		$txt_style_description=str_replace("'",'',$txt_style_description);
		$style_description='';
		$style_description=trim(str_replace($str_replace_check,'',$txt_style_description));
		
		$txt_remarks=str_replace("'",'',$txt_remarks);
		$mst_remarks='';
		$mst_remarks=trim(str_replace($str_replace_check,'',$txt_remarks));
		
		$PrevData=sql_select("select style_ref_no,gmts_item_id from wo_po_details_master where job_no=$txt_job_no");
		$PrevStyleRefNo=$PrevData[0][csf('style_ref_no')];
		$PrevGmtsItemId=$PrevData[0][csf('gmts_item_id')];

		$field_array="quotation_id*fit_id*buyer_name*location_name*style_ref_no*style_description*repeat_job_no*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*is_repeat*order_repeat_no*region*product_category*team_leader*dealing_marchant*packing*remarks*ship_mode*order_uom*gmts_item_id*style_ref_no_prev*gmts_item_id_prev*set_break_down*total_set_qnty*set_smv*season_buyer_wise*season_year*brand_id*factory_marchant*qlty_label*style_owner*sustainability_standard*fab_material*quality_level*product_group*working_company_id*working_location_id*style_source*updated_by*update_date";

		if(str_replace("'","",$txt_quotation_id)=="") $quotation_id=0; else $quotation_id=str_replace("'","",$txt_quotation_id);

		$data_array="'".$quotation_id."'*".$cbo_fit_id."*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_style_ref."*'".$style_description."'*".$txt_repeat_job_no."*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$chk_is_repeat."*'".$repeat_no."'*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_packing."*'".$mst_remarks."'*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*'".$PrevStyleRefNo."'*'".$PrevGmtsItemId."'*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_id."*".$cbo_season_year."*".$cbo_brand_id."*".$cbo_factory_merchant."*".$cbo_qltyLabel."*".$cbo_style_owner."*".$cbo_sustainability_standard."*".$cbo_fab_material."*".$cbo_order_nature."*".$cbo_product_group."*".$cbo_working_factory."*".$cbo_working_location_id."*".$cbo_style_from."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array1="id, job_id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id";
		$add_comma=0; $sewSmv=0; $cutSmv=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$hidd_job_id.",".$txt_job_no.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."')";
			$add_comma++;
			$id1=$id1+1;

			$sewSmv+=$set_breck_down_arr[3];
			$cutSmv+=$set_breck_down_arr[7];
		}
		$flag=1;
		//2_1_8_8_0_0_0_0_0_0_1_0_2_0_3_0_4_0_5___20_1_8_8_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=execute_query("delete from wo_po_details_mas_set_details where job_no =".$txt_job_no."",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		$rID3=execute_query( "update wo_booking_mst set is_apply_last_update=2 where job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**";

		//echo $is_smv; die;$is_smv=

		$txt_job_no=str_replace("'","",$txt_job_no);
		$set_smv_id=str_replace("'","",$set_smv_id);
		if($set_smv_id==1 || $set_smv_id==7) fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$cbo_currercy,$sewSmv,$cutSmv,1);

		$job_id=str_replace("'",'',$hidd_job_id);
		//echo "10**";
		if($cost_control_source==1)
		{
			if($quotation_id!=0)
			{
				$query="UPDATE qc_confirm_mst SET job_id=$job_id WHERE cost_sheet_id=$quotation_id and status_active=1 and is_deleted=0";// die;
				$rIDt=execute_query($query,1);
				if($rIDt==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rIDt.'='.$flag; die;
		//echo $is_smv; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$txt_job_no."**".$rID."**".$repeat_no."**".$quotation_id."**".str_replace("'",'',$hidd_job_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$txt_job_no."**".$rID."**".$repeat_no."**".$quotation_id."**".str_replace("'",'',$hidd_job_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sql_pre_cost=sql_select("select job_no from wo_pre_cost_mst where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 group by job_no");
		if(count($sql_pre_cost)>0)
		{
			echo "14**".$txt_job_no;
			disconnect($con);die;
		}

		if(count($sql_pre_cost)>0)
		{
			$sql_booking_no=sql_select("select booking_no from wo_booking_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 group by booking_no");
			$booking_str="";
			foreach($sql_booking_no as $row)
			{
				if($booking_str=="") $booking_str=$row[csf('booking_no')]; else $booking_str.=', '.$row[csf('booking_no')];
			}

			if($booking_str!="")
			{
				echo "13**".$booking_str;
				disconnect($con);die;
			}
		}
		$flag=1;
		$txt_quotation_id=str_replace("'","",$txt_quotation_id);
		if($txt_quotation_id)
		{
			$quotation_update_del = "'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$quotation_update_field = "quotation_status* updated_by*update_date";
			$quot_rID = sql_update("wo_price_quotation",$quotation_update_field,$quotation_update_del,"id","".$txt_quotation_id."",0);
			if($quot_rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID=execute_query( "update wo_po_details_master set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', style_ref_no='', quotation_id=0  where job_no =$txt_job_no and status_active=1 and is_deleted=0",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=execute_query( "update wo_po_break_down set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1  where job_no_mst =$txt_job_no and status_active=1 and is_deleted=0",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=execute_query( "update wo_po_color_size_breakdown set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1  where job_no_mst =$txt_job_no and status_active=1 and is_deleted=0",1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$quot_rID.'-'.$rID.'-'.$rID1.'-'.$rID2.'-'.$flag; die;
		if($db_type==0)
		{
			if($flag)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag)
			{
				oci_commit($con);
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
}

// Master Form End ***************************************** Master Form End******************************************

if ($action=="quotation_image_copy_for_job") //Quotation Image save
{

	$data_ex=explode("_",$data);
	$job_no=$data_ex[0];
	$quotation_id=$data_ex[1];
	$company_id=$data_ex[2];
	//echo $company_id.'='.$job_no.'='.$quotation_id;
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library from variable_order_tracking where company_name=$company_id and variable_list in (20,47) and status_active=1 and is_deleted=0 order by variable_list ASC");
	 $copy_quotation=0; $set_smv_id=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
	}
	if($copy_quotation==1)
	{
		if($set_smv_id==2 || $set_smv_id==4 || $set_smv_id==5 || $set_smv_id==6)
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

				$sql_quot=sql_select("select master_tble_id,form_name,image_location,file_type,real_file_name from common_photo_library where master_tble_id='$quotation_id' and form_name='quotation_entry' and file_type=1");
				//echo "select master_tble_id,form_name,image_location,file_type,real_file_name from common_photo_library where master_tble_id='$quotation_id' and form_name='quotation_entry' and file_type=1";

				$field_array_img="id,master_tble_id,details_tble_id,form_name,image_location,file_type,real_file_name";
				 $id_img=return_next_id( "id", "common_photo_library", 1 ) ;
				 $img_file=1;$file_type=1;
				foreach($sql_quot as $row)
				{
					$master_tble_id=$job_no;
					$form='knit_order_entry';
					$image_location_data=explode(".",$row[csf('image_location')]);
					$extension=trim($image_location_data[1]);
					 $fname=$form."_".$master_tble_id."_".$id_img.".".$extension;
					$real_file_name=$row[csf('real_file_name')];
					$file_name="file_upload/"."$fname";

					$file_save="../../../file_upload/"."$fname";
					$file_old="../../../".$row[csf('image_location')];
					//echo $file_save.'='.$file_old;die;
					copy($file_old , $file_save);
					$dets_tble_id='';

					if($img_file!=1) $data_array_img .=",";
					$data_array_img.="('".$id_img."','".$master_tble_id."','".$dets_tble_id."','".$form."','".$file_name."','".$file_type."','".$real_file_name."')";
					$id_img=$id_img+1;
					$img_file=$img_file+1;

				}

				//echo "10**insert into common_photo_library (".$field_array_img.") Values ".$data_array_img."";die;
				if($data_array_img!="")
				{
					$rID=sql_insert("common_photo_library",$field_array_img,$data_array_img,1);
				}
				if($db_type==0)
				{
					if($rID ){
						mysql_query("COMMIT");
						echo "0**".$job_no."**"."Qoutation Image Save is Done";
					}
					else{
						mysql_query("ROLLBACK");
						echo "10**".$job_no."**"."Qoutation Image is not found";
					}
				}



				if($db_type==2 || $db_type==1 )
				{
				if($rID ){
						oci_commit($con);
						echo "0**".$job_no."**"."Qoutation Image Save is Done";
					}
					else{
						oci_rollback($con);
						echo "10**".$job_no."**"."Qoutation Image is not found";
					}
				}
					disconnect($con);

		}
	}
		disconnect($con);
}
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="order_listview")
{
	$data=explode("*",$data);
	
	$sql_job="select a.total_set_qnty, quotation_id from wo_po_details_master a where a.job_no='$data[0]' and a.status_active=1 and a.is_deleted=0";
	$sql_job=sql_select($sql_job);
	$total_set_qnty=$sql_job[0][csf('total_set_qnty')];
	$total_set_qnty=$sql_job[0][csf('total_set_qnty')];
	
	if($data[2]==1)
	{//and supplier_id='$data[4]' for 2497
		$sqlbh="select ID, PO_NUMBER, PO_RECEIVED_DATE, PUB_SHIPMENT_DATE, PO_QUANTITY, PLAN_CUT, STATUS_ACTIVE, PO_UNIT_PRICE as UNIT_PRICE from bh_wo_po_break_down where is_deleted=0 and job_id='$data[3]' and lc_company_id='$data[4]' order by id DESC"; //
		$datasqlbh_array=sql_select($sqlbh); $bhdataArr=array();
		foreach($datasqlbh_array as $bhrow)
		{
			$bhdataArr[$bhrow['ID']]['po']=$bhrow['PO_NUMBER'];
			$bhdataArr[$bhrow['ID']]['porecdate']=$bhrow['PO_RECEIVED_DATE'];
			$bhdataArr[$bhrow['ID']]['pubshipdate']=$bhrow['PUB_SHIPMENT_DATE'];
			$bhdataArr[$bhrow['ID']]['poqty']=$bhrow['PO_QUANTITY'];
			$bhdataArr[$bhrow['ID']]['planqty']=$bhrow['PLAN_CUT'];
			$bhdataArr[$bhrow['ID']]['status']=$bhrow['STATUS_ACTIVE'];
			$bhdataArr[$bhrow['ID']]['poprice']=$bhrow['UNIT_PRICE'];
		}
		unset($datasqlbh_array);
		//print_r($bhdataArr);
	}
	
	$sql= "select id, po_number, po_received_date, pub_shipment_date, po_quantity, plan_cut, status_active, doc_sheet_qty, unit_price, bhpo_id from wo_po_break_down where is_deleted=0 and job_no_mst='$data[0]' order by id DESC";
	$data_array=sql_select($sql);

	$sql_po="select b.order_quantity,b.po_break_down_id  as po_id from wo_po_color_size_breakdown b where b.job_no_mst='$data[0]' and b.status_active=1 and b.is_deleted=0";
	$sql_po_color=sql_select($sql_po);
	
	//$poColorQty=0;
	foreach ($sql_po_color as $row)
	{ 
		$poColorQtyArr[$row[csf('po_id')]]+=$row[csf('order_quantity')];
	}

	$act_po_sql="SELECT a.po_break_down_id, sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.is_deleted=0 and b.is_deleted=0 and a.job_no='$data[0]' group by a.po_break_down_id";
	$act_po_data=sql_select($act_po_sql);
	$act_po_arr=array();
	if(count($act_po_data)>0){
		foreach($act_po_data as $act_row){
			$act_po_arr[$act_row[csf('po_break_down_id')]]=$act_row[csf('po_qty')];
		}
	}
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520">
        <thead>
            <th width="20">SL</th>
            <th width="80">Po No.</th>
            <th width="70">Po Qty</th>
			<th width="70">Avg. Rate Pcs/Set</th>
            <th width="70">Plan Qty</th>
            <th width="70">Ship Date</th>
            <th width="60">Lead Time</th>
            <th>Status</th>
        </thead>
     </table>
     <div style="width:540px; max-height:340px; overflow-y:scroll">
         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520" id="tbl_po_list">
            <tbody>
            <?
            $i=1; $tmpbhpoidArr=array();
            foreach($data_array as $row)
            {
               // if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$act_po_qty=$act_po_arr[$row[csf('id')]];
				if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";                
                if($data[1]==$row[csf('id')]) $bgcolor="#33CC00"; else $bgcolor;
				
				$daysOnHand=0;
				if($db_type==2)
				{
					$today= change_date_format($row[csf('pub_shipment_date')],'','',1);
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')],'','',1),$today);
				}
				else
				{
					$today= change_date_format($row[csf('pub_shipment_date')]);
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')]),$today);
				}

				$poColorQty=$poColorQtyArr[$row[csf('id')]];
				$poQty=$row[csf('po_quantity')]*$total_set_qnty;
				$doc_sheet_qty=$row[csf('doc_sheet_qty')]*$total_set_qnty;
				//echo $poColorQty.'='.$total_set_qnty."=".$poQty."=".$doc_sheet_qty;
				if(($poColorQty!=$poQty) || ($doc_sheet_qty>0 && ($doc_sheet_qty!=$poColorQty)))
				{
					$bgcolor="#FFFF00"; 
				}
				else
				{
					$bgcolor=$bgcolor;	
				}
				if($act_po_qty>0) $bgcolor="#FF0000"; else $bgcolor;
				
				$potdcolor=""; $poqtytdcolor=""; $popricetdcolor=""; $poplantdcolor=""; $pubshipdatetdcolor=""; $statustdcolor=""; $bhpodatachange=0;
				if($data[2]==1)
				{
					$bhpo=$bhdataArr[$row[csf('bhpo_id')]]['po'];
					$bhporecdate=$bhdataArr[$row[csf('bhpo_id')]]['porecdate'];
					$bhpubshipdate=$bhdataArr[$row[csf('bhpo_id')]]['pubshipdate'];
					$bhpoqty=$bhdataArr[$row[csf('bhpo_id')]]['poqty'];
					$bhplanqty=$bhdataArr[$row[csf('bhpo_id')]]['planqty'];
					$bhstatus=$bhdataArr[$row[csf('bhpo_id')]]['status'];
					$bhpoprice=$bhdataArr[$row[csf('bhpo_id')]]['poprice'];
					
					if($bhpo!=$row[csf('po_number')]) $potdcolor="#F0352F";
					if($bhpoqty!=$row[csf('po_quantity')]) $poqtytdcolor="#F0352F";
					if($bhpoprice!=$row[csf('unit_price')]) $popricetdcolor="#F0352F";
					if($bhplanqty!=$row[csf('plan_cut')]) $poplantdcolor="#F0352F";
					if($bhpubshipdate!=$row[csf('pub_shipment_date')]) $pubshipdatetdcolor="#F0352F";
					if($bhstatus!=$row[csf('status_active')]) $statustdcolor="#F0352F";
					
					if($bhpo!=$row[csf('po_number')] || $bhpoqty!=$row[csf('po_quantity')] || $bhpoprice!=$row[csf('unit_price')] || $bhplanqty!=$row[csf('plan_cut')] || $bhpubshipdate!=$row[csf('pub_shipment_date')] || $bhstatus!=$row[csf('status_active')])
					{
						$bhpodatachange=1;
					}
				}
				//echo $bhpo.'+';
                ?>
                <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<?=$i; ?>','<?=$bgcolor; ?>'); get_php_form_data('<?=$row[csf('id')].'_'.$data[2].'_'.$row[csf('bhpo_id')].'_'.$bhpodatachange; ?>', 'populate_order_details_form_data', 'requires/order_entry_controller');">
                    <td width="20" align="center"><?=$i; ?></td>
                    <td width="80" style="word-break:break-all; background-color:<?=$potdcolor; ?>" title="<?=$bhpo; ?>"><?=$row[csf('po_number')]; ?></td>
                    <td width="70" style="background-color:<?=$poqtytdcolor; ?>"align="right" title="<?=$bhpoqty; ?>"><?=number_format($row[csf('po_quantity')]); ?></td>
					<td width="70" style="background-color:<?=$popricetdcolor; ?>"align="center" title="<?=$bhpoprice; ?>"><?=number_format($row[csf('unit_price')],4); ?></td>
                    <td width="70" style="background-color:<?=$poplantdcolor; ?>"align="right" title="<?=$bhplanqty; ?>"><?=number_format($row[csf('plan_cut')]); ?></td>
                    <td width="70" style="background-color:<?=$pubshipdatetdcolor; ?>" title="<?=$bhpubshipdate; ?>"><?=change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    <td width="60" align="center"><?=$daysOnHand; ?></td>
                    <td style="background-color:<?=$statustdcolor; ?>" title="<?=$bhstatus; ?>"><?=$row_status[$row[csf('status_active')]]; ?></td>
                </tr>
				<?
				$tmpbhpoidArr[$row[csf('bhpo_id')]]=$row[csf('bhpo_id')];
                $i++;
            }
			
			foreach($bhdataArr as $bhpoid=>$bhdata)
			{
				if($tmpbhpoidArr[$bhpoid]=="")
				{
					if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";                
					
					$daysOnHand=0;
					if($db_type==2)
					{
						$today= change_date_format($bhdata['pubshipdate'],'','',1);
						$daysOnHand = datediff("d",change_date_format($bhdata['porecdate'],'','',1),$today);
					}
					
					/*$poColorQty=$poColorQtyArr[$row[csf('id')]];
					$poQty=$row[csf('po_quantity')]*$total_set_qnty;
					$doc_sheet_qty=$row[csf('doc_sheet_qty')]*$total_set_qnty;
					//echo $poColorQty.'='.$total_set_qnty."=".$poQty."=".$doc_sheet_qty;
					if(($poColorQty!=$poQty) || ($doc_sheet_qty>0 && ($doc_sheet_qty!=$poColorQty)))
					{
						$bgcolor="#FFFF00"; 
					}
					else
					{
						$bgcolor=$bgcolor;	
					}
					if($act_po_qty>0) $bgcolor="#FF0000"; else $bgcolor;*/
					?>
					<tr id="tr_<?=$i; ?>" bgcolor="#00FF00" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<?=$i; ?>','<?=$bgcolor; ?>'); get_php_form_data('<?='0_'.$data[2].'_'.$bhpoid.'_0'; ?>', 'populate_order_details_form_data', 'requires/order_entry_controller');">
						<td width="20" align="center"><?=$i; ?></td>
						<td width="80" style="word-break:break-all"><?=$bhdata['po']; ?></td>
						<td width="70" align="right"><?=number_format($bhdata['poqty']); ?></td>
						<td width="70" align="center"><?=number_format($bhdata['poprice'],4); ?></td>
						<td width="70" align="right"><?=number_format($bhdata['planqty']); ?></td>
						<td width="70"><?=change_date_format($bhdata['pubshipdate']); ?></td>
						<td width="60" align="center"><?=$daysOnHand; ?></td>
						<td><?=$row_status[$bhdata['status']]; ?></td>
					</tr>
					<?
                    $i++;
				}
			}
            ?>
            </tbody>
        </table>
    </div>
	<?
    exit();
}

if($action=="populate_order_details_form_data")
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		$result= sql_select("select a.company_name from bh_wo_po_details_master a, bh_wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$exdata[2]' and a.is_deleted=0 and a.status_active=1");
	}
	else
	{
		$result= sql_select("select a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$exdata[0]' and a.is_deleted=0 and a.status_active=1");
	}
	$company_id=$result[0][csf('company_name')];
	
	$update_period_id=$po_current_date_data=$next_process_ship_date=0;
	
	$sqlVari="select VARIABLE_LIST, PO_UPDATE_PERIOD, PO_CURRENT_DATE, DUPLICATE_SHIP_DATE from variable_order_tracking where company_name ='$company_id' and variable_list in (25,32,33) and is_deleted=0 and status_active=1";
	$sqlVariRes= sql_select($sqlVari);
	foreach($sqlVariRes as $vrow)
	{
		if($vrow['VARIABLE_LIST']==25) $next_process_ship_date=$vrow['DUPLICATE_SHIP_DATE'];
		if($vrow['VARIABLE_LIST']==32) $po_current_date_data=$vrow['PO_CURRENT_DATE'];
		if($vrow['VARIABLE_LIST']==33) $update_period_id=$vrow['PO_UPDATE_PERIOD'];
	}
	
	//$chk_variable_setting=sql_select("select case variable_list when 32 THEN po_update_period when 33 then po_current_date from variable_order_tracking where company_name=1");
	/*$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	$next_process_ship_date=return_field_value("duplicate_ship_date","variable_order_tracking"," company_name ='$company_id' and variable_list=25 and is_deleted=0 and status_active=1");*/
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	//echo "select id, is_confirmed, po_number, po_received_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, insert_date from wo_po_break_down where id='$data'"; die;
	//wo_pre_cos_fab_co_avg_con_dtls
	if($next_process_ship_date==1)
	{
		$next_process_check_found=0;
		$next_process_check_found=return_field_value("po_break_down_id","wo_pre_cos_fab_co_avg_con_dtls","po_break_down_id='$exdata[0]' and is_deleted=0 and status_active=1");
		//echo $next_process_check_found.'D';
	}

	$act_po_sql="SELECT a.po_break_down_id, sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.is_deleted=0 and b.is_deleted=0 and a.po_break_down_id='$exdata[0]' group by a.po_break_down_id";
	$act_po_data=sql_select($act_po_sql);
	$act_po_qt=0;
	if(count($act_po_data)>0){
		foreach($act_po_data as $act_row){
			$act_po_qty=$act_row[csf('po_qty')];
		}
	}
	if($exdata[1]==1 && $exdata[0]==0)
	{
		$data_array=sql_select("select id as bhpo_id, is_confirmed, po_number, po_received_date, pub_shipment_date, pub_shipment_date as shipment_date, factory_received_date, po_quantity as doc_sheet_qty, pack_price, no_of_carton, actual_po_no, matrix_type, round_type, po_unit_price as unit_price, up_charge, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, sc_lc, insert_date, file_year from bh_wo_po_break_down where id='$exdata[2]'");
	}
	else
	{
		$data_array=sql_select("select id, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, pack_handover_date,factory_received_date, doc_sheet_qty, pack_price, no_of_carton, actual_po_no, matrix_type, round_type, unit_price, up_charge, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, sc_lc, insert_date, file_year, bhpo_id from wo_po_break_down where id='$exdata[0]'");
	}
	
	foreach ($data_array as $row)
	{
		$insert_date=explode(" ",$row[csf("insert_date")]);
		$current_date=date('d-m-Y h:i:s');
		$po_insert_date=change_date_format($insert_date[0],'dd-mm-yyyy','-').' '.$insert_date[1];
		$total_time=datediff('n',$po_insert_date,$current_date);
		$total_hour=floor($total_time/60);
		if($exdata[1]!=1 || $exdata[0]!=0)
		{
			echo "document.getElementById('cbo_breakdown_type').value = '".$row[csf("matrix_type")]."';\n";
			echo "fnc_noof_carton(".$row[csf("matrix_type")].");\n";
			echo "$('#cbo_breakdown_type').attr('disabled','true')".";\n";
			echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";
		}
		else
		{
			echo "document.getElementById('update_id_details').value = '';\n";
		}
		echo "document.getElementById('hidd_bhpo_id').value = '".$row[csf("bhpo_id")]."';\n";
		echo "document.getElementById('cbo_round_type').value = '".$row[csf("round_type")]."';\n";
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n";
		
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";// Dont Hide Pls see the issue id ISD-23-26230
		if($po_current_date_data==1 && $row[csf("is_confirmed")]==1) echo "$('#txt_po_received_date').attr('disabled',true);\n";
		else echo "$('#txt_po_received_date').attr('disabled',false);\n";

		echo "$('#copy_id').removeAttr('disabled','disabled');\n";
		//echo $next_process_ship_date.'dd';
		if($next_process_check_found>0)
		{
			echo "$('#txt_pub_shipment_date').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_pub_shipment_date').attr('disabled',false);\n";
		}
		if($row[csf("shipment_date")]=="") $row[csf("shipment_date")]=$row[csf("pub_shipment_date")];
		if($row[csf("factory_received_date")]=="") $row[csf("factory_received_date")]=$row[csf("po_received_date")];
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";
		echo "document.getElementById('txt_org_shipment_date').value = '".change_date_format($row[csf("shipment_date")], "dd-mm-yyyy", "-")."';\n";
		echo "document.getElementById('txt_phd').value = '".change_date_format($row[csf("pack_handover_date")], "dd-mm-yyyy", "-")."';\n";
		echo "document.getElementById('txt_factory_rec_date').value = '".change_date_format($row[csf("factory_received_date")], "dd-mm-yyyy", "-")."';\n";

		echo "document.getElementById('txt_po_datedif_hour').value = '".$total_hour."';\n";
		echo "document.getElementById('txt_po_remarks').value = '".$row[csf("details_remarks")]."';\n";
		
		if($row[csf("matrix_type")]==4)
		{
			if($row[csf("pack_price")]!=0)
			{
				echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("pack_price")],4,'.','')."';\n";
			}
			else
			{
				 echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
			}
			echo "document.getElementById('txt_docSheetQty').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
		}
		else
		{
			echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
			echo "document.getElementById('txt_docSheetQty').value = '".number_format($row[csf("doc_sheet_qty")],0,'.','')."';\n";
		}
		
		if($exdata[1]==1)
		{
			$bhpo_id=$data_array[0][csf("bhpo_id")];
			$sql_qRate=sql_select("select po_unit_price as rate from bh_wo_po_break_down where id='$bhpo_id' and status_active=1 and is_deleted = 0");
	
			$qutation_rate=$sql_qRate[0][csf("rate")];
			echo "document.getElementById('txt_quotation_price').value = '".$qutation_rate."';\n";
		}
		
		echo "$('#txt_avg_price').attr('disabled',true);\n";
		echo "document.getElementById('txt_upCharge').value = '".$row[csf("up_charge")]."';\n";
		//echo "document.getElementById('txt_docSheetQty').value = '".$row[csf("doc_sheet_qty")]."';\n";
		echo "document.getElementById('txt_noOf_carton').value = '".$row[csf("no_of_carton")]."';\n";

		
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n";
		//echo "set_tna_task();\n";//ISD-23-26230

		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n";
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n";
		echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n";
		echo "document.getElementById('txt_file_year').value = '".$row[csf("file_year")]."';\n";
		echo "document.getElementById('txt_sc_lc').value = '".trim($row[csf("sc_lc")])."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n";
		if($exdata[1]!=1 || $exdata[0]!=0)
		{
			if($act_po_qty>0){
				echo "show_list_view('".$row[csf("id")]."','show_act_po_active_listview','act_po_list_view','requires/order_entry_controller','');\n";
			}
			else{
				echo "$('#act_po_list_view').html('');\n";
			}
			echo "show_list_view('".$row[csf("id")]."','show_po_active_listview','country_po_list_view','requires/order_entry_controller','');\n";
		}
	}
	exit();
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0) $packing = str_replace("'","",$cbo_packing);
	else $packing = str_replace("'","",$cbo_packing_po_level);
	
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	
	$txt_po_no="'".trim(str_replace($str_replace_check,' ',$txt_po_no))."'";
	$txt_style_ref="'".trim(str_replace($str_replace_check,' ',$txt_style_ref))."'";
	
	$sql=sql_select("select variable_list, copy_quotation, cost_control_source,excut_source, publish_shipment_date from variable_order_tracking where company_name=$cbo_company_name and variable_list in (20,53,78,45,65,47) order by id");
	$cost_control_source=0; $is_copy_quatation=2; $poEntryControlWithBomApproval=2; $excess_variable=0; $excess_per_level=0; $style_and_smv_source=0;
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==20) $is_copy_quatation=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==53) $cost_control_source=$vrow[csf('cost_control_source')];
		if($vrow[csf('variable_list')]==78) $poEntryControlWithBomApproval=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==45) $excess_variable=$vrow[csf('excut_source')];
		if($vrow[csf('variable_list')]==65) $excess_per_level=$vrow[csf('excut_source')];
		if($vrow[csf('variable_list')]==47) $style_and_smv_source=$vrow[csf('publish_shipment_date')];
	}
	
	if($is_copy_quatation==1)
	{
		$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and b.page_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		$app_nessity=2; $validate_page=0; $allow_partial=2;
		foreach($sql as $row){
			$app_nessity=$row[csf('approval_need')];
			$validate_page=$row[csf('validate_page')];
			$allow_partial=$row[csf('allow_partial')];
		}
		if($cost_control_source==2)
		{
			$sql=sql_select("select approved from wo_price_quotation where id=$txt_quotation_id");
			$quatation_approved=2;
			foreach($sql as $row){
				$quatation_approved=$row[csf('approved')];
			}
		
			if($app_nessity==1  && $validate_page==1){
				if($quatation_approved==2 || $quatation_approved==0 || $quatation_approved==3){
					echo "quataNotApp**".str_replace("'","",$txt_job_no);
					disconnect($con);die;
				}
			}
		}
	}
	if($poEntryControlWithBomApproval==1)
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1 || $isapproved==3)
		{
			echo "16** Budget approved of this job, Please un-approve the budget and try again.";
			disconnect($con);die;
		}
	}
	if (str_replace("'","",$copy_id)==2)
	{
		//==============Exces Cut Slap============
			$item_id=str_replace("'","",$cbo_gmtsItem_id);
			$avg_rate=str_replace("'","",$txt_avg_price);
			$set_qnty=str_replace("'","",$tot_set_qnty);
			$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
			$item_ratio_arr=array();
			foreach($set_breck_down as $set_data)
			{
				$ex_set_data=explode('_',$set_data);
				$ex_item_id=$ex_set_data[0];
				$ex_item_ratio=$ex_set_data[1];
				$item_ratio_arr[$ex_item_id]=$ex_item_ratio;
			}
			//$add_comma=0; $data_array1="";  $ratio_comma=0; $data_array_ratio="";
			$size_id_arr=array();//echo "10**=";
			for($k=1; $k<=$size_table; $k++)
			{
				$txtSizeNameStr="txtSizeName_".$k;
				$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
				
				if(str_replace("'","",$txtSizeName)!="")
				{
					if (!in_array(str_replace("'","",$txtSizeName),$new_array_size,TRUE))
					{ 
						$size_id_val = return_id( str_replace("'","",$txtSizeName), $size_library, "lib_size", "id,size_name","401");
						$new_array_size[$size_id_val]=str_replace("'","",$txtSizeName);
					}
					else $size_id_val =  array_search(str_replace("'","",$txtSizeName), $new_array_size,TRUE);
				}
				else $size_id_val=0;

				$size_id_arr[$k]=$size_id_val;
			}
			
			//print_r($size_id_arr);
			//echo "B=";die;
		if($excess_variable==2 && $excess_per_level==1){
			$color_wise_ord_qty=array();
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorNamestr="txtColorName_".$i;
				$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
				$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
				if(str_replace("'","",$txtColorName)!="")
				{
					if(str_replace("'","",$txtColorName)!="")
					{
						if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
						{
							$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
							$new_array_color[$color_id]=str_replace("'","",$txtColorName);
						}
						else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
					}
					else $color_id=0;

					for($m=1; $m<=$size_table; $m++)
					{
						$txtSizeNameStr="txtSizeName_".$m;
						$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
						if(str_replace("'","",$txtSizeName)!="")
						{
							$size_id = $size_id_arr[$m];
							//$txt_colorSizeQty="";
							$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
							$color_size_poQty=0;
							$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];						
							if($color_size_poQty>0)
							{
								$color_wise_ord_qty[$gmtsitem][$color_id] +=$color_size_poQty*1;
							}
						}
						//echo $data_array1;
					}
				}
			}				
			$item_details=sql_select("SELECT gmts_item_id, complexity, embelishment as print, printdiff, embro, embrodiff, wash, washdiff, spworks, spwdiff from wo_po_details_mas_set_details where job_id=$hidd_job_id");
			foreach ($item_details as $item) {
				if($item[csf('print')]==1) $item_dtls_arr[$item[csf('gmts_item_id')]]['print_difficulty'] = $item[csf('printdiff')];
				if($item[csf('embro')]==1) $item_dtls_arr[$item[csf('gmts_item_id')]]['emb_difficulty'] = $item[csf('embrodiff')];
				if($item[csf('wash')]==1) $item_dtls_arr[$item[csf('gmts_item_id')]]['wash_difficulty'] = $item[csf('washdiff')];
				if($item[csf('spworks')]==1) $item_dtls_arr[$item[csf('gmts_item_id')]]['splwork_difficulty'] = $item[csf('spwdiff')];
				//if($item[csf('complexity')]!=0) 
				$item_dtls_arr[$item[csf('gmts_item_id')]]['complexity'] = $item[csf('complexity')];
			}
			$attr_arr=array('cutting', 'sewing', 'finishing', 'cutting_difficulty', 'sewing_difficulty', 'finishing_difficulty');
			$march_arr=array('print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty');
			//$complexityMapingArr=array(1=>"1",2=>"2",3=>"3",4=>"4");
			//echo "10**";
			$complexityMapingArr=array("cutting_difficulty","sewing_difficulty","finishing_difficulty");
			foreach ($color_wise_ord_qty as $item_id => $color_dat) {
				$complexityid=$item_dtls_arr[$item_id]['complexity'];
				
				 foreach ($color_dat as $cid => $data) {
					 $slab_data=sql_select("SELECT id, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, total, 'print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty' from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$cbo_company_name and buyer_id=$cbo_buyer_name and $data >= lower_limit_qty AND $data <= upper_limit_qty");
					//echo "10**SELECT id, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, total, 'print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty' from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$cbo_company_name and buyer_id=$cbo_buyer_name and $data >= lower_limit_qty AND $data <= upper_limit_qty"; //die;
					 foreach ($slab_data as $row) {
						 foreach ($complexityMapingArr as $cattr) {
							 //echo $complexityid.'-'.$row[csf($cattr)].'<br>';
							 if($complexityid==$row[csf($cattr)]){
								 $exfield=explode("_", $cattr);
								 $slab_data_arr[$item_id][$cid][$exfield[0]] = $row[csf($exfield[0])];
							 }
						 }
						 foreach ($march_arr as $diff_attr) {
							 if($item_dtls_arr[$item_id][$diff_attr]==$row[csf($diff_attr)]){
								 $field_arr=explode("_", $diff_attr);
								 $slab_data_arr[$item_id][$cid][$diff_attr] =$row[csf($diff_attr)];
								 $slab_data_arr[$item_id][$cid][$field_arr[0]] =$row[csf($field_arr[0])];
							 }
						 }
					 }		 				 		
				 }
			}
			
			//echo "10**"; print_r($slab_data_arr); die;
			foreach ($slab_data_arr as $gmt_id=>$item_data) {
				 foreach ($item_data as $c_id => $sdata) {
					 foreach ($march_arr as $diff_attr) {
						 if($item_dtls_arr[$gmt_id][$diff_attr]==$slab_data_arr[$gmt_id][$c_id][$diff_attr]){
							 $field_arr=explode("_", $diff_attr);
							 $slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id][$field_arr[0]];
							// echo $slab_data_arr[$gmt_id][$c_id][$field_arr[0]].'=';
						 }
					 }
					 $slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['cutting'];		 		
					 $slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['sewing'];		 		
					 $slab_plan_cut[$gmt_id][$c_id] +=$slab_data_arr[$gmt_id][$c_id]['finishing'];	
					 //echo $slab_data_arr[$gmt_id][$c_id]['cutting'].'='.$slab_data_arr[$gmt_id][$c_id]['sewing'].'='.$slab_data_arr[$gmt_id][$c_id]['finishing'];
				 }
			}
			//echo "10**"; print_r($slab_data_arr); die;
		} //  End==============Exces Cut Slap============End====
	 }
	
	
	//==============================Lead Time Validation ==============================
	$min_lead_time_control=2;
	$sql_min_lead_time_control=sql_select("select min_lead_time_control from variable_order_tracking where company_name=$cbo_company_name and variable_list=51");
	foreach($sql_min_lead_time_control as $row_min_lead_time_control){
		$min_lead_time_control=$row_min_lead_time_control[csf('min_lead_time_control')];
	}
	if($min_lead_time_control==1)
	{
		$received_date=date('Y-m-d',strtotime(str_replace("'","",$txt_po_received_date)));
		$pub_shipment_date=date('Y-m-d',strtotime(str_replace("'","",$txt_pub_shipment_date)));
		$datediff=datediff( 'd', $received_date, $pub_shipment_date, $using_timestamps = false );
		$year=date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)));
		$month= (int) date("m",strtotime(str_replace("'","",$txt_org_shipment_date)));
	
		$pub_year=date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)));
		$pub_month= (int) date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)));
		
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month' and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		
		if(trim($datediff*1)< $min_leadtime_allocation){
			echo "LeadTime**0**".$min_leadtime_allocation;
			 disconnect($con);die;
		}
		//echo "10**".$datediff.'--'.$min_leadtime_allocation.'--'.$min_lead_time_control; disconnect($con);die;
	}
	
	//=============================================================

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if (str_replace("'","",$copy_id)==2)
		{
			$data_shipDate_vari=""; $flag=1;
			$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
			if($sql_shipDate_vari[0][csf("duplicate_ship_date")]==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";
			else $txt_pub_shipment_date_cond="";

			$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
			$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='knit_order_entry' and file_type=1");
			if($image_mdt==1 && $image=="")
			{
				echo "24**0"; 
				disconnect($con);
				die;
			}
			
			if (str_replace("'","",$update_id_details)=="")
			{
				if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
				{
					echo "11**0";
					disconnect($con);die;
				}
			}
			else
			{
				if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" )== 1)
				{
					echo "11**0";
					disconnect($con);die;
				}
			}
			
			$txt_details_remark=str_replace("'",'',$txt_po_remarks);
			$dtls_remark='';
			$dtls_remark=trim(str_replace($str_replace_check,'',$txt_details_remark));

			$id=return_next_id("id", "wo_po_break_down", 1);
			$breakdown_type=str_replace("'","",$cbo_breakdown_type);
			if($breakdown_type==4)
			{
				$docSheet_col="pack_price";
				$docSheet_field=$txt_avg_price;
				$avg_rate_pack=$txt_docSheetQty;
			}
			else
			{
				$docSheet_col="doc_sheet_qty";
				$docSheet_field=$txt_docSheetQty;
				$avg_rate_pack=$txt_avg_price;
			}
			if (str_replace("'","",$update_id_details)=="")
			{
				// txt_grouping
				if(str_replace("'","",$cbo_order_status)==2)
				{
					$field_array="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,pack_handover_date, factory_received_date, unit_price, up_charge, original_avg_price, $docSheet_col, no_of_carton, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, file_year, sc_lc, bhpo_id, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$hidd_job_id.",".$update_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$txt_org_shipment_date.",".$txt_phd.",".$txt_factory_rec_date.",".$avg_rate_pack.",".$txt_upCharge.",".$txt_avg_price.",".$docSheet_field.",".$txt_noOf_carton.",'".$dtls_remark."',".$cbo_delay_for.",'".$packing."',".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."',".$txt_file_no.",".$txt_file_year.",".$txt_sc_lc.",".$hidd_bhpo_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
				}  
				else
				{
					$field_array="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,pack_handover_date, factory_received_date, unit_price, up_charge, $docSheet_col, no_of_carton, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, file_year, sc_lc, bhpo_id, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$hidd_job_id.",".$update_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$txt_org_shipment_date.",".$txt_phd.",".$txt_factory_rec_date.",".$avg_rate_pack.",".$txt_upCharge.",".$docSheet_field.",".$txt_noOf_carton.",'".$dtls_remark."',".$cbo_delay_for.",'".$packing."',".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."',".$txt_file_no.",".$txt_file_year.",".$txt_sc_lc.",".$hidd_bhpo_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";

				}
				$po_id="'".$id."'";
			}
			else 
			{
				$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*pack_handover_date*factory_received_date*unit_price*up_charge*".$docSheet_col."*no_of_carton*details_remarks*delay_for*packing*grouping*projected_po_id*matrix_type*round_type*tna_task_from_upto*t_year*t_month*file_no*file_year*sc_lc*updated_by*update_date*status_active";

				$data_array="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$txt_org_shipment_date."*".$txt_phd."*".$txt_factory_rec_date."*".$avg_rate_pack."*".$txt_upCharge."*".$docSheet_field."*".$txt_noOf_carton."*'".$dtls_remark."'*".$cbo_delay_for."*'".$packing."'*".$txt_grouping."*".$cbo_projected_po."*".$cbo_breakdown_type."*".$cbo_round_type."*".$cbo_tna_task."*'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*'".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*".$txt_file_no."*".$txt_file_year."*".$txt_sc_lc."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
				$po_id=$update_id_details;
			}
			//echo "INSERT INTO wo_po_break_down (".$field_array.") VALUES ".$data_array; die;

			//====================================================================================
			if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";

			if (is_duplicate_field( "country_id", "wo_po_color_size_breakdown", "country_id=$cbo_deliveryCountry_id and po_break_down_id=$po_id and item_number_id=$cbo_gmtsItem_id and is_deleted=0 and country_ship_date=$txt_countryShip_date $pack_type_cond" )== 1)
			{
				echo "11**0";
				disconnect($con);die;
			}
			$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;
			$breakdown_type=str_replace("'","",$cbo_breakdown_type);

			if($breakdown_type==4)
			{
				$field_array1="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			}
			else
			{
				$field_array1="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			}

			//echo $cbo_gmtsItem_id.'kausar';die;$excess_cut_perc=($data[1]*$row[csf('order_quantity')]/100)+$row[csf('order_quantity')];
			
			$color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 and color_mst_id!=0", "color_number_id", "color_mst_id");

			$size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id");
	
			$item_mst=return_library_array( "select item_mst_id, item_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id");
			
			$add_comma=0; $data_array1="";  $ratio_comma=0; $data_array_ratio="";
			$size_id_arr=array();
			for($k=1; $k<=$size_table; $k++)
			{
				$txtSizeNameStr="txtSizeName_".$k;
				$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
				//$size_id_val = return_id( $$txtSizeName, $size_library, "lib_size", "id,size_name");
				if(str_replace("'","",$txtSizeName)!="")
				{
					if (!in_array(str_replace("'","",$txtSizeName),$new_array_size,TRUE))
					{
						$size_id_val = return_id( str_replace("'","",$txtSizeName), $size_library, "lib_size", "id,size_name","401");
						$new_array_size[$size_id_val]=str_replace("'","",$txtSizeName);
					}
					else $size_id_val =  array_search(str_replace("'","",$txtSizeName), $new_array_size,TRUE);
				}
				else $size_id_val=0;

				$size_id_arr[$k]=$size_id_val;
			}				
			if($breakdown_type==1)
			{
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorNamestr="txtColorName_".$i;
					$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
					if(str_replace("'","",$txtColorName)!="")
					{
						//$color_id = return_id( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name");
						if(str_replace("'","",$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
							{
								$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
						}
						else $color_id=0;

						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeNameStr="txtSizeName_".$m;
							$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
							if(str_replace("'","",$txtSizeName)!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

								if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);
								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];

								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								// echo "10**=".$$txt_colorSizeQty.'='.$item_id.'='.$color_id.',';die;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeRate);
								//$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';
								if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
								{
									$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
								}
								else
								{
									$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
									$item_mst_id=$id1;
								}
								
								if(array_key_exists($color_id,$color_mst))
								{
									$color_mst_id=$color_mst[$color_id];
								}
								else
								{
									$color_mst[$color_id]=$id1;
									$color_mst_id=$id1;
								}
								if(array_key_exists($size_id,$size_mst))
								{
									$size_mst_id=$size_mst[$size_id];
								}
								else
								{
									$size_mst[$size_id]=$id1;
									$size_mst_id=$id1;
								}
								if($excess_variable==2 && $excess_per_level==1)
								{
									$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
									$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
									//echo "10**=".$orderexcesscut.'='.$gmtsitem.'='.$color_id.',';die;
									if($orderexcesscut==''){
										$orderexcesscut=0;
									}
									if($orderexcesscut>0)
									{
										//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
										$plancut=$color_size_poQty*($orderexcesscut/100);
										$color_size_planCutQty=$plancut+$color_size_poQty*$item_ratio_arr[$item_id];
									}
									else{
										$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
									}
								}
								else{
									$orderexcesscut=$$txt_colorSizeExCut;
									$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								}
								// echo "10**=".$color_size_poQty.'='.$orderexcesscut.'='.$gmtsitem.'='.$color_id.',';die;
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$hidd_job_id.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."','".$order_total_amt."','".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";

									$id1=$id1+1;
									$add_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}
			else if($breakdown_type==4)
			{
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorNamestr="txtColorName_".$i;
					$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
					if(str_replace("'","",$txtColorName)!="")
					{
						if(str_replace("'","",$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
							{
								$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
						}
						else $color_id=0;
						
						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeNameStr="txtSizeName_".$m;
							$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
							if(str_replace("'","",$txtSizeName)!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizePackQty="txt_colorSizePackQty_".$i.'_'.$m;
								$txt_colorSizePcsQty="txt_colorSizePcsQty_".$i.'_'.$m;
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

								if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);

								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];

								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeRate);
								//$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';

								if($excess_variable==2 && $excess_per_level==1)
								{
									$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
									$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
									if($orderexcesscut==''){
										$orderexcesscut=0;
									}
									if($orderexcesscut>0)
									{
										//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
										$plancut=($color_size_poQty*$orderexcesscut)/100;
										$color_size_planCutQty=$plancut+$color_size_poQty;
									}
									else{
										$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
									}
									
								}
								else{
									$orderexcesscut=$$txt_colorSizeExCut;
									$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								}
								
								if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
								{
									$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
								}
								else
								{
									$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
									$item_mst_id=$id1;
								}
								
								if(array_key_exists($color_id,$color_mst))
								{
									$color_mst_id=$color_mst[$color_id];
								}
								else
								{
									$color_mst[$color_id]=$id1;
									$color_mst_id=$id1;
								}
								if(array_key_exists($size_id,$size_mst))
								{
									$size_mst_id=$size_mst[$size_id];
								}
								else
								{
									$size_mst[$size_id]=$id1;
									$size_mst_id=$id1;
								}
								
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$hidd_job_id.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."','".$order_total_amt."','".$color_size_planCutQty."',".$$txt_colorSizePackQty.",".$$txt_colorSizePcsQty.",".$txt_breakdownGrouping.",".$txt_pcsQty.",'".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";

									$id1=$id1+1;
									$add_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}
			else
			{
				$idratio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
				$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, inserted_by, insert_date";
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorNamestr="txtColorName_".$i;
					$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
					if(str_replace("'","",$txtColorName)!="")
					{
						if(str_replace("'","",$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
							{
								$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
						}
						else $color_id=0;
						
						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeNameStr="txtSizeName_".$m;
							$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
							if(str_replace("'","",$txtSizeName)!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

								if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);

								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];

								$txt_colorSizeRatioQty="txt_colorSizeRatioQty_".$i.'_'.$m;
								$txt_colorSizeRatioRate="txt_colorSizeRatioRate_".$i.'_'.$m;
								$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;

								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*str_replace("'","",$$txt_colorSizeRate);
								//$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';

								if($excess_variable==2 && $excess_per_level==1)
								{
									$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
									$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
									if($orderexcesscut==''){
										$orderexcesscut=0;
									}
									if($orderexcesscut>0)
									{
										//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
										$plancut=($color_size_poQty*$orderexcesscut)/100;
										$color_size_planCutQty=$plancut+$color_size_poQty;
									}
									else{
										$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
									}
								}
								else{
									$orderexcesscut=$$txt_colorSizeExCut;
									$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								}
								
								if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
								{
									$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
								}
								else
								{
									$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
									$item_mst_id=$id1;
								}
								
								if(array_key_exists($color_id,$color_mst))
								{
									$color_mst_id=$color_mst[$color_id];
								}
								else
								{
									$color_mst[$color_id]=$id1;
									$color_mst_id=$id1;
								}
								if(array_key_exists($size_id,$size_mst))
								{
									$size_mst_id=$size_mst[$size_id];
								}
								else
								{
									$size_mst[$size_id]=$id1;
									$size_mst_id=$id1;
								}
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$hidd_job_id.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."','".$order_total_amt."','".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";

									$id1=$id1+1;
									$add_comma++;

									if ($ratio_comma!=0) $data_array_ratio .=",";
										$data_array_ratio .="(".$idratio.",".$hidd_job_id.",".$po_id.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_countryShip_date.",'".$color_id."','".$size_id."',".$$txt_colorSizeRatioQty.",".$$txt_colorSizeRatioRate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									$idratio=$idratio+1;
									$ratio_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}

			$flag=1;
			// echo "10**INSERT INTO wo_po_color_size_breakdown (".$field_array1.") VALUES ".$data_array1; die;
			if (str_replace("'","",$update_id_details)=="")
			{
				$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;
			}
			else
			{
				$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",0);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;
			}

			$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;

			if($breakdown_type==2 || $breakdown_type==3)
			{
				$rIDratio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
				if($rIDratio==1 && $flag==1) $flag=1; else $flag=0;
			}
			//============================================================================================
			if($db_type==0) $sequNullCheck="IFNULL(sequ,0)";
		 	else if($db_type==2) $sequNullCheck="nvl(sequ,0)";
			$sam=1;
			$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
			//$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
			$sample_tag=sql_select("select tag_sample, sequ from lib_buyer_tag_sample where $sequNullCheck!=0 and buyer_id=$cbo_buyer_name order by sequ");
			//$sample_tag=sql_select("select b.tag_sample, b.sequ from lib_sample a, lib_buyer_tag_sample b where $sequNullCheck!=0 and a.id=b.tag_sample and b.buyer_id=$cbo_buyer_name and a.is_deleted=0 and a.status_active=1 and a.business_nature=3 order by b.sequ");
			$field_array_sm="id, job_no_mst, po_break_down_id, color_number_id, sample_type_id, status_active, is_deleted";
			//echo "select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id";	 die;
			$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
			//print_r($data_array_sample);
			foreach($sample_tag as $sample_tag_row)
			{
				foreach ( $data_array_sample as $row_sam1 )
				{
					$dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0");
					list($idsm)=$dup_data;
					if( $idsm[csf('id')] =='')
					{
						if ($sam!=1) $data_array_sm .=",";
						$data_array_sm .="(".$id_sm.",".$update_id.",".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
						$id_sm=$id_sm+1;
						$sam=$sam+1;
					}
				}
			}

			if($data_array_sm !='')
			{
				$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
			//============================================================================================
			$lap=1;
			$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
			// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
			$field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted";
			$data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and  b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
			foreach ( $data_array_lapdip as $row_lap1 )
			{
				$dup_lap=sql_select("select id from wo_po_lapdip_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_lap1[csf('po_id')]." and color_name_id=".$row_lap1[csf('color_number_id')]."  and status_active=1 and is_deleted=0");
				list($idlap)=$dup_lap;
				if( $idlap[csf('id')] =='')
				{
					if ($lap!=1) $data_array_lap .=",";
					$data_array_lap .="(".$id_lap.",".$update_id.",".$row_lap1[csf('po_id')].",".$row_lap1[csf('color_number_id')].",1,0)";
					$id_lap=$id_lap+1;
					$lap=$lap+1;
				}
			}
			if($data_array_lap !='')
			{
				$rID4=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;
			}

			// WO_PO_DOCUMENT_APPROVAL_INFO.........................
			$id_dai = return_next_id( "id", "WO_PO_DOCUMENT_APPROVAL_INFO", 1 ) ;
			$data_array_dai_res = sql_select("select a.JOB_NO_MST,a.id as PO_ID from  wo_po_break_down a  where a.job_no_mst=$update_id and a.id = $po_id and a.is_deleted=0 and a.status_active=1");
			
			foreach ( $data_array_dai_res as $row_dai )
			{
				$document_app_data_arr = return_library_array( "select PO_BREAK_DOWN_ID from WO_PO_DOCUMENT_APPROVAL_INFO where job_no_mst = $update_id and PO_BREAK_DOWN_ID = $po_id  and status_active=1 and is_deleted=0", "PO_BREAK_DOWN_ID", "PO_BREAK_DOWN_ID");
				if( $document_app_data_arr[$row_dai['PO_ID']] == '')
				{
					if ($data_array_dai != '') $data_array_dai .=",";
					$data_array_dai .="(".$id_dai.",'".$row_dai['JOB_NO_MST']."',".$row_dai['PO_ID'].",1,0)";
					$id_dai++;
				}
			}

			if($data_array_dai != '')
			{
				$field_array_dai = "ID,JOB_NO_MST,PO_BREAK_DOWN_ID,STATUS_ACTIVE,IS_DELETED";
				$rID5 = sql_insert("WO_PO_DOCUMENT_APPROVAL_INFO",$field_array_dai,$data_array_dai,1);
				if($rID5 == 1){$flag = 1;}else{ $flag = 0;}
			}
			//..................................................... end;


			if($flag==1)
			{
				execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
				execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_mst set isorder_change='1' where job_no =".$txt_job_no." and status_active=1 and is_deleted=0 and isorder_change=0",1);
				//die;
				update_color_size_sequence($update_id,1);
				update_cost_sheet($update_id);
				$return_data=job_order_qty_update($update_id,$po_id,$set_breck_down,$breakdown_type,$cbo_order_status);
			}
			//$return_data=update_job_mast($update_id);//define in common_functions.php
			//update_cost_sheet($update_id);
			//=============================================================================================
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'",'',$po_id)."**".$return_data;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'",'',$po_id)."**".$return_data;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".str_replace("'",'',$po_id)."**".$return_data;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'",'',$po_id)."**".$return_data;
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		else if (str_replace("'","",$copy_id)==1)
		{
			$txt_copypo_no="'".trim(str_replace($str_replace_check,' ',$txt_copypo_no))."'";
			
			if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_copypo_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
			{
				echo "11**0";
				disconnect($con);die;
			}
			
			$po_sql=sql_select("select id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,pack_handover_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, actual_po_no, matrix_type, round_type, tna_task_from_upto, t_year, t_month, original_po_qty, file_no,sc_lc,file_year from wo_po_break_down where id=$update_id_details and job_no_mst=$update_id ");// status_active=1

			$cbo_order_status="'".$po_sql[0][csf('is_confirmed')]."'";
			$txt_po_received_date="'".$po_sql[0][csf('po_received_date')]."'";
			$txt_pub_shipment_date="'".$po_sql[0][csf('pub_shipment_date')]."'";
			$txt_shipment_date="'".$po_sql[0][csf('shipment_date')]."'";
			$pack_handover_date="'".$po_sql[0][csf('pack_handover_date')]."'";
			$txt_fac_received_date="'".$po_sql[0][csf('factory_received_date')]."'";
			$txt_docSheetQty="'".$po_sql[0][csf('doc_sheet_qty')]."'";
			$txt_noOf_carton="'".$po_sql[0][csf('no_of_carton')]."'";
			$txt_poQty="'".$po_sql[0][csf('po_quantity')]."'";
			$txt_avg_price=(float)trim($po_sql[0][csf('unit_price')]);
			$txt_upCharge=(float)trim($po_sql[0][csf('up_charge')]);
			$txt_poAmt="'".$po_sql[0][csf('po_total_price')]."'";
			$txt_excessCut="'".$po_sql[0][csf('excess_cut')]."'";
			$txt_planCut="'".$po_sql[0][csf('plan_cut')]."'";
			$txt_po_remarks="'".$po_sql[0][csf('details_remarks')]."'";
			$cbo_delay_for="'".$po_sql[0][csf('delay_for')]."'";
			$packing="'".$po_sql[0][csf('packing')]."'";
			$txt_grouping="'".$po_sql[0][csf('grouping')]."'";
			$cbo_projected_po="'".$po_sql[0][csf('projected_po_id')]."'";
			//$txt_actual_po="'".$po_sql[0][csf('actual_po_no')]."'";
			$cbo_breakdown_type="'".$po_sql[0][csf('matrix_type')]."'";
			$breakdown_type=$po_sql[0][csf('matrix_type')];
			$cbo_round_type="'".$po_sql[0][csf('round_type')]."'";
			$cbo_tna_task="'".$po_sql[0][csf('tna_task_from_upto')]."'";
			$t_year="'".$po_sql[0][csf('t_year')]."'";
			$t_month="'".$po_sql[0][csf('t_month')]."'";
			$txt_file_no="'".$po_sql[0][csf('file_no')]."'";
			$txt_file_year="'".$po_sql[0][csf('file_year')]."'";
			$txt_sc_lc="'".$po_sql[0][csf('sc_lc')]."'";
			$txt_orgi_avg_price="'".$po_sql[0][csf('original_avg_price')]."'";
			$txt_orgi_po_qty="'".$po_sql[0][csf('original_po_qty')]."'";
			
			
			if (str_replace("'","",$update_id_details)!="")
			{
				$id=return_next_id("id", "wo_po_break_down", 1);
				if (str_replace("'","",$cbo_order_status)==1)
				{
					$field_array="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,pack_handover_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no,file_year,sc_lc, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$hidd_job_id.",".$update_id.", ".$cbo_order_status.", ".$txt_copypo_no.", ".$txt_po_received_date.", ".$txt_pub_shipment_date.", ".$txt_shipment_date.",".$pack_handover_date.",".$txt_fac_received_date.",".$txt_docSheetQty.", ".$txt_noOf_carton.", ".$txt_poQty.", '".$txt_avg_price."', '".$txt_upCharge."', ".$txt_poAmt.", ".$txt_excessCut.", ".$txt_planCut.", ".$txt_po_remarks.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",".$t_year.",".$t_month.",".$txt_file_no.",".$txt_file_year.",".$txt_sc_lc.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				}
				else
				{
					$field_array="id, job_id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, pack_handover_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, original_po_qty, file_no,file_year,sc_lc, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$hidd_job_id.",".$update_id.", ".$cbo_order_status.", ".$txt_copypo_no.", ".$txt_po_received_date.", ".$txt_pub_shipment_date.", ".$txt_shipment_date.",".$pack_handover_date.",".$txt_fac_received_date.",".$txt_docSheetQty.",".$txt_noOf_carton.", ".$txt_poQty.", ".$txt_avg_price.", ".$txt_upCharge.", ".$txt_orgi_avg_price.", ".$txt_poAmt.", ".$txt_excessCut.", ".$txt_planCut.", ".$txt_po_remarks.", ".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",".$t_year.",".$t_month.",".$txt_orgi_po_qty.",".$txt_file_no.",".$txt_file_year.",".$txt_sc_lc.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				}
				$po_id=$id;
				$color_size_breakdown_sql=sql_select("select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, color_order, size_order, assort_qty, solid_qty from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and job_no_mst=$update_id and status_active=1 and is_deleted=0 order by id ASC");

				$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;
				$field_array1="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, color_order, size_order, assort_qty, solid_qty,copy_po_id, inserted_by, insert_date, is_deleted, status_active";

				//echo $cbo_gmtsItem_id.'kausar';die;
				$add_comma=0; $data_array1="";
				foreach($color_size_breakdown_sql as $row)
				{
					$color_mst_id=0; $size_mst_id=0; $item_mst_id=0; $cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $txt_cutup_date=""; $cbo_cutOff_id=0; $txt_countryShip_date=""; $color_id=0; $size_id=0; $txt_colorSizeQty=0; $txt_colorSizeRate=0; $txt_colorSizeExCut=0; $txt_colorSizeArticleNo=0; $order_total_amt=0; $plancut_Qty=0; $assort_qty=0; $solid_qty=0;

					$cbo_gmtsItem_id = "'".$row[csf('item_number_id')]."'";
					$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
					$cbo_code_id = "'".$row[csf('code_id')]."'";
					$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
					$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";

					$txt_cutup_date = "'".$row[csf('cutup_date')]."'";
					$cbo_cutOff_id = "'".$row[csf('cutup')]."'";
					$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
					$color_id = "'".$row[csf('color_number_id')]."'";
					$size_id = "'".$row[csf('size_number_id')]."'";
					//	$copy_po_id = "'".$row[csf('copy_po_id')]."'";

					$txt_colorSizePackQty="'".$row[csf('pack_qty')]."'";
					$txt_colorSizePcsQty=(float)trim($row[csf('pcs_per_pack')]);
					$txt_breakdownGrouping="'".$row[csf('pack_type')]."'";

					$txt_colorOrder="'".$row[csf('color_order')]."'";
					$txt_sizeOrder="'".$row[csf('size_order')]."'";

					$txt_colorSizeQty="'".$row[csf('order_quantity')]."'";
					$txt_colorSizeRate=(float)trim($row[csf('order_rate')]);
					$txt_colorSizeExCut="'".$row[csf('excess_cut_perc')]."'";
					$txt_colorSizeArticleNo="'".$row[csf('article_number')]."'";

					$order_total_amt="'".$row[csf('order_total')]."'";
					$plancut_Qty="'".$row[csf('plan_cut_qnty')]."'";
					$assort_qty="'".$row[csf('assort_qty')]."'";
					$solid_qty="'".$row[csf('solid_qty')]."'";
					//print_r($$txt_colorSizeQty).'</br>';
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$hidd_job_id.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date .",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",".$txt_colorSizeQty.",'".$txt_colorSizeRate."',".$txt_colorSizeExCut.",".$txt_colorSizeArticleNo.",".$order_total_amt.",".$plancut_Qty.",".$txt_colorSizePackQty.",".$txt_colorSizePcsQty.",".$txt_breakdownGrouping.",".$txt_colorOrder.",".$txt_sizeOrder.",".$assort_qty.",".$solid_qty.",".$update_id_details.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

					$id1=$id1+1;
					$add_comma++;
					//echo $data_array1;
				}
				//echo "INSERT INTO wo_po_color_size_breakdown (".$field_array1.") VALUES ".$data_array1; die;
				//echo $data_array1;
				if(str_replace("'","",$cbo_breakdown_type)==2 || str_replace("'","",$cbo_breakdown_type)==3)
				{
					//echo "select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code from  wo_po_ratio_breakdown where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC";
					$sql_ratio=sql_select("select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code from  wo_po_ratio_breakdown where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC");
					$idRatio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
					$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, country_ship_date,color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code, inserted_by, insert_date";

					$add_comma=0; $data_array_ratio="";
					foreach($sql_ratio as $row)
					{
						$cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $cbo_code_id=0; $cbo_country_id=""; $cbo_countryCode_id=0; $txt_countryShip_date=0; $color_id=0; $size_id=0; $txt_colorSizeRatioQty=0; $txt_colorSizeRatioRate=0;

						$cbo_gmtsItem_id = "'".$row[csf('gmts_item_id')]."'";
						$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
						$cbo_code_id = "'".$row[csf('code_id')]."'";
						$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
						$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";

						$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
						$color_id = "'".$row[csf('color_id')]."'";
						$size_id = "'".$row[csf('size_id')]."'";

						$txt_colorSizeRatioQty="'".$row[csf('ratio_qty')]."'";
						$txt_colorSizeRatioRate=(float)trim($row[csf('ratio_rate')]);
						//print_r($$txt_colorSizeQty).'</br>';
						if ($add_comma!=0) $data_array_ratio .=",";
						$data_array_ratio .="(".$idRatio.",".$hidd_job_id.",".$po_id.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",".$txt_colorSizeRatioQty.",'".$txt_colorSizeRatioRate."',".$cbo_country_id.",".$cbo_code_id.",".$cbo_countryCode_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$idRatio=$idRatio+1;
						$add_comma++;
					}
					//echo "INSERT INTO wo_po_ratio_breakdown (".$field_array_ratio.") VALUES ".$data_array_ratio; die;
					$sql_destination=sql_select("select id, po_id, item_id, country_id, country_ship_date, ultimate_country_id,  color_id, destination_id, destination_qty, ul_country_code, code_id from wo_po_destination_info where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC");

					$idDest=return_next_id( "id", "wo_po_destination_info", 1) ;
					$field_array_des="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";

					$add_comma=0; $data_array_des="";
					foreach($sql_destination as $row)
					{
						$cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $cbo_code_id=0; $cbo_country_id=""; $cbo_countryCode_id=0; $txt_countryShip_date=0; $color_id=0; $cboDestination=0; $txt_qty=0;

						$cbo_gmtsItem_id = "'".$row[csf('item_id')]."'";
						$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
						$cbo_code_id = "'".$row[csf('code_id')]."'";
						$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
						$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";

						$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
						$color_id = "'".$row[csf('color_id')]."'";

						$cboDestination="'".$row[csf('destination_id')]."'";
						$txt_qty="'".$row[csf('destination_qty')]."'";
						//print_r($$txt_colorSizeQty).'</br>';
						if ($add_comma!=0) $data_array_ratio .=",";
						$data_array_des .="(".$idDest.",".$po_id.",".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$txt_countryShip_date.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$color_id.",".$cboDestination.",".$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$idDest=$idDest+1;
						$add_comma++;
					}
				}
				$flag=1;
				$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
				$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;
				$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
				$return_data=job_order_qty_update($update_id,$po_id,$set_breck_down,$breakdown_type,$cbo_order_status);
				//echo $data_array_ratio.'<br>'.$data_array_des;
				//update_color_size_sequence($update_id,1);
				execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
				execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				if(str_replace("'","",$cbo_breakdown_type)==2 || str_replace("'","",$cbo_breakdown_type)==3)
				{
					if($data_array_ratio!="" )
					{
						$rIDRatio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
						if($rIDRatio==1 && $flag==1) $flag=1; else $flag=0;
					}

					if($data_array_des!="" )
					{
						$rIDDes=sql_insert("wo_po_destination_info",$field_array_des,$data_array_des,0);
						if($rIDDes==1 && $flag==1) $flag=1; else $flag=0;
					}
				}


				// WO_PO_DOCUMENT_APPROVAL_INFO.........................
				$id_dai = return_next_id( "id", "WO_PO_DOCUMENT_APPROVAL_INFO", 1 ) ;
				$data_array_dai_res = sql_select("select a.JOB_NO_MST,a.id as PO_ID from  wo_po_break_down a  where a.job_no_mst=$update_id and a.id = $po_id and a.is_deleted=0 and a.status_active=1");

				foreach ( $data_array_dai_res as $row_dai )
				{
					$document_app_data_arr = return_library_array( "select PO_BREAK_DOWN_ID from WO_PO_DOCUMENT_APPROVAL_INFO where job_no_mst = $update_id and PO_BREAK_DOWN_ID = $po_id  and status_active=1 and is_deleted=0", "PO_BREAK_DOWN_ID", "PO_BREAK_DOWN_ID");
					if( $document_app_data_arr[$row_dai['PO_ID']] == '')
					{
						if ($data_array_dai != '') $data_array_dai .=",";
						$data_array_dai .="(".$id_dai.",'".$row_dai['JOB_NO_MST']."',".$row_dai['PO_ID'].",1,0)";
						$id_dai++;
					}
				}

				if($data_array_dai != '')
				{
					$field_array_dai = "ID,JOB_NO_MST,PO_BREAK_DOWN_ID,STATUS_ACTIVE,IS_DELETED";
					$rID5 = sql_insert("WO_PO_DOCUMENT_APPROVAL_INFO",$field_array_dai,$data_array_dai,1);
					if($rID5 == 1){$flag = 1;}else{ $flag = 0;}
				}
				//..................................................... end;

				//echo $flag.'='.$rID1;
				if($db_type==0)
				{
					if($flag==1)
					{
						mysql_query("COMMIT");
						echo "0**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no)."**".str_replace("'",'',$return_data);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no)."**".str_replace("'",'',$return_data);
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
						oci_commit($con);
						echo "0**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no)."**".str_replace("'",'',$return_data);
					}
					else
					{
						oci_rollback($con);
						echo "10**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no)."**".str_replace("'",'',$return_data);
					} 
				}
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
			else
			{
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		$sql_po_chk=sql_select("select shiping_status from wo_po_break_down where id=$update_id_details and is_deleted=0 and status_active=1");
		
		foreach($sql_po_chk as $row)
		{
			$shiping_status_id=$row[csf("shiping_status")];
			if($shiping_status_id==3)
			{
				echo "14**Fullship Found,Update not allowed.";
				disconnect($con);die;
			}
		}
		
		$sql_attach="select a.com_sales_contract_id as lc_sc_id, b.contract_value as lc_sc_value, sum(a.attached_qnty) as order_attached_qnty,sum(a.attached_value) as order_attach_value , 1 as type from com_sales_contract_order_info a , com_sales_contract b where  a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.wo_po_break_down_id=$update_id_details group by com_sales_contract_id,b.contract_value
		union all 
		select a.com_export_lc_id as lc_sc_id,b.lc_value as lc_sc_value ,sum(a.attached_qnty) as order_attached_qnty,sum(a.attached_value) as order_attach_value, 2 as type from com_export_lc_order_info a ,com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.wo_po_break_down_id=$update_id_details group by com_export_lc_id,b.lc_value";
		$sql_attach_result=sql_select($sql_attach);
		$tot_attach_value=0; $tot_sc_attached_qnty_arr= array();  $tot_lc_attached_qnty_arr= array();
		foreach($sql_attach_result as $row)
		{
			if($row[csf("order_attach_value")]>0)
			{
				$tot_attach_value+=$row[csf("order_attach_value")];
				$tot_attached_qnty+=$row[csf("order_attached_qnty")];
				if($row[csf("type")]==1)
				{
					$tot_sc_attached_qnty_arr[$row[csf("lc_sc_id")]]['qty'] +=$row[csf("order_attached_qnty")];
					$tot_sc_attached_qnty_arr[$row[csf("lc_sc_id")]]['val'] =$row[csf("lc_sc_value")];
					//$tot_sc_val_arr[$row[csf("lc_sc_id")]]['val'] =$row[csf("lc_sc_value")];

				}else{
					$tot_lc_attached_qnty_arr[$row[csf("lc_sc_id")]]['qty'] +=$row[csf("order_attached_qnty")];
					$tot_lc_attached_qnty_arr[$row[csf("lc_sc_id")]]['val'] =$row[csf("lc_sc_value")];
				}
			}
		}
		
		$cu_order_qty=str_replace("'","",$txt_docSheetQty);
		
		/* if($cu_order_qty<$tot_attached_qnty)
		{
			echo "17**Order Qty Not Allowed Less Then Attach Qty."; disconnect($con);die;
		}
		
		$cu_order_val=str_replace("'","",$txt_amount);
		if($cu_order_val<$tot_attach_value)
		{
			echo "16**Order Value Not Allowed Less Then Attach Value."; disconnect($con);die;
		} */
		
		$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
		$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='knit_order_entry' and file_type=1");
		if($image_mdt==1 && $image=="")
		{
			echo "24**0"; 
			disconnect($con);
			die;
		}
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		$data_shipDate_vari=$sql_shipDate_vari[0][csf("duplicate_ship_date")];

		if($data_shipDate_vari==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";
		else $txt_pub_shipment_date_cond="";

		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id  $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0";
			disconnect($con);die;
		}
		// if($isCuttingAllowedOrderEntry!=1){
			$is_produced=0;
			$sql_check=sql_select("select a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id,sum(a.marker_qty) as marker_qnty from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id=$update_id_details and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id");
			foreach($sql_check as $dts)
			{
				$prod_array[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$dts[csf("color_id")]][$dts[csf("size_id")]]=$dts[csf("marker_qnty")];
				$is_produced=1;
			}

			if($is_produced==0)
			{
				$sql_check=sql_select("select a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id, b.production_type, sum(b.production_qnty) as marker_qnty from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id=$update_id_details and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id, a.country_id, a.item_number_id, a.color_number_id, a.size_number_id, b.production_type order by b.production_type DESC");
				//echo "select a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id='$ex_data[3]' group by a.po_break_down_id, a.country_id, b.item_number_id, a.color_number_id, a.size_number_id";
				foreach($sql_check as $dts)
				{
					$prod_array[$dts[csf("po_break_down_id")]][$dts[csf("country_id")]][$dts[csf("item_number_id")]][$dts[csf("color_number_id")]][$dts[csf("size_number_id")]]=$dts[csf("marker_qnty")];
					$is_produced=1;
				}
				unset($sql_check);
			}

			if($is_produced==0)
			{
				if (is_duplicate_field( "po_break_down_id", "pro_garments_production_mst", "po_break_down_id=$update_id_details and production_quantity>0 and is_deleted=0" ) == 1)
				{
					$is_produced=1;
					echo "14**0";
					disconnect($con);die;
				}
			}
		// }
		

		$breakdown_type=str_replace("'","",$cbo_breakdown_type);
		if($breakdown_type==4)
		{
			$docSheet_col="pack_price";
			$docSheet_field=$txt_avg_price;
			$avg_rate_pack=$txt_docSheetQty;
		}
		else
		{
			$docSheet_col="doc_sheet_qty";
			$docSheet_field=$txt_docSheetQty;
			$avg_rate_pack=$txt_avg_price;
		}
		$prev_data=sql_select("SELECT is_confirmed ,po_number, po_received_date, pub_shipment_date, shipment_date,pack_handover_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no,sc_lc, is_deleted, status_active, updated_by, update_date, po_number_prev, pub_shipment_date_prev FROM wo_po_break_down WHERE id=$update_id_details");
		foreach($prev_data as $rows)
		{
			$prev_po_no=$rows[csf('po_number')];
			$prev_matrix_type=$rows[csf('matrix_type')];
			$prev_round_type=$rows[csf('round_type')];
			$prev_doc_sheet_qty=$rows[csf('doc_sheet_qty')];
			$prev_no_of_carton=$rows[csf('no_of_carton')];

			$prev_order_status=$rows[csf('is_confirmed')];
			$prev_po_received_date=$rows[csf('po_received_date')];
			$prev_po_qty=$rows[csf('po_quantity')];
			$prev_pub_shipment_date=$rows[csf('pub_shipment_date')];
			$prev_status=$rows[csf('status_active')];
			$prev_org_shipment_date=$rows[csf('shipment_date')];
			$prev_phd_date=$rows[csf('pack_handover_date')];
			$prev_factory_rec_date=$rows[csf('factory_received_date')];
			$prev_projected_po=$rows[csf('projected_po_id')];
			$prev_packing=$rows[csf('packing')];
			$prev_details_remark=$rows[csf('details_remarks')];
			$prev_file_no=$rows[csf('file_no')];
			$prev_sc_lc=$rows[csf('sc_lc')];
			$prev_avg_price=$rows[csf('unit_price')];
			$prev_excess_cut=$rows[csf('excess_cut')];
			$prev_plan_cut=$rows[csf('plan_cut')];
			$prev_status=$rows[csf('status_active')];
			$prev_updated_by=$rows[csf('updated_by')];
			$prev_update_date=$rows[csf('update_date')];

			$prev_pono=$rows[csf('po_number_prev')];
			$prev_pubship_date=$rows[csf('pub_shipment_date_prev')];
		}
		
		$txt_details_remark=str_replace("'",'',$txt_po_remarks);
		$dtls_remark='';
		$dtls_remark=trim(str_replace($str_replace_check,'',$txt_details_remark));

		if($prev_po_no==str_replace("'","",$txt_po_no))
		{
			$pre_po_no=$prev_pono;
		}
		else $pre_po_no=$prev_po_no;

		if(change_date_format($prev_pub_shipment_date)==change_date_format(str_replace("'","",$txt_pub_shipment_date)))
		{
			$pre_pubship_date=$prev_pubship_date;
		}
		else $pre_pubship_date=$prev_pub_shipment_date;
		//echo $tot_poQty.'='.$tot_poQty.'='.$tot_poAmt.'='.$txt_avg_price.'='.$set_qnty;
		$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*pack_handover_date*factory_received_date*unit_price*up_charge*".$docSheet_col."*no_of_carton*details_remarks*delay_for*packing*grouping*projected_po_id*matrix_type*round_type*tna_task_from_upto*t_year*t_month*file_no*file_year*sc_lc*po_number_prev*pub_shipment_date_prev*updated_by*update_date*status_active";

		$data_array="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$txt_org_shipment_date."*".$txt_phd."*".$txt_factory_rec_date."*".$avg_rate_pack."*".$txt_upCharge."*".$docSheet_field."*".$txt_noOf_carton."*'".$dtls_remark."'*".$cbo_delay_for."*'".$packing."'*".$txt_grouping."*".$cbo_projected_po."*".$cbo_breakdown_type."*".$cbo_round_type."*".$cbo_tna_task."*'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*'".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*".$txt_file_no."*".$txt_file_year."*".$txt_sc_lc."*'".$pre_po_no."'*'".$pre_pubship_date."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";

		//echo '10**'.$field_array.'<br>Kausar'.$data_array; die;

		//Check any change  to wo_po_break_down table;
		//--------------------------------


		//----------------------------------
		$sql_con="matrix_type=$cbo_breakdown_type and round_type=$cbo_round_type and is_confirmed=$cbo_order_status and po_number =$txt_po_no and job_no_mst=$update_id and po_received_date=$txt_po_received_date and pub_shipment_date=$txt_pub_shipment_date and shipment_date=$txt_org_shipment_date and pack_handover_date=$txt_phd and factory_received_date=$txt_factory_rec_date and doc_sheet_qty=$txt_docSheetQty and no_of_carton=$txt_noOf_carton and po_quantity=$txt_poQty and unit_price=$txt_avg_price and po_total_price=$txt_poAmt and excess_cut='1' and plan_cut='1' and details_remarks=$txt_po_remarks and delay_for=$cbo_delay_for and packing=$packing and grouping=$txt_grouping and projected_po_id=$cbo_projected_po and tna_task_from_upto=$cbo_tna_task and t_year=".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))." and t_month=".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))." and file_no=$txt_file_no and id=$update_id_details and is_deleted=0";
		$sql_con=str_replace("=''"," IS NULL ",$sql_con);
		$is_duplicate=is_duplicate_field( "po_number", "wo_po_break_down", $sql_con );

		$log_id_mst=return_next_id( "id", "wo_po_update_log", 1);

		if($db_type==0) $current_date = $pc_date_time;
		else $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);

		$previous_po_qty=return_field_value("po_quantity","wo_po_break_down","job_no_mst=".$update_id." and id=".$update_id_details."");

		$log_update_date=return_field_value("update_date","wo_po_update_log","job_no=".$update_id." and po_id=".$update_id_details." order by id DESC");

		$log_update=date("Y-m-d", strtotime($log_update_date));
		$curr_date=date("Y-m-d", strtotime($current_date));

		if(($log_update=="" && $is_duplicate!=1) || ($log_update!=$curr_date && $is_duplicate!=1))
		{
			$field_array_history="id, entry_form, matrix_type, round_type, job_no, po_no, po_id, order_status, po_received_date, previous_po_qty, shipment_date, org_ship_date,phd_date, po_status, t_year, t_month, fac_receive_date, projected_po, packing, remarks, file_no,sc_lc, doc_sheet_qty, avg_price, no_of_carton, excess_cut_parcent, plan_cut, status, update_date, update_by";

			$data_array_history="(".$log_id_mst.",1,'".$prev_matrix_type."','".$prev_round_type."',".$update_id.",'".$prev_po_no."',".$update_id_details.",'".$prev_order_status."','".$prev_po_received_date."','".$prev_po_qty."','".$prev_pub_shipment_date."','".$prev_org_shipment_date."','".$prev_phd_date."','".$prev_status."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".$prev_factory_rec_date."','".$prev_projected_po."','".$prev_packing."','".$prev_details_remark."','".$prev_file_no."','".$prev_sc_lc."','".$prev_doc_sheet_qty."','".$prev_avg_price."','".$prev_no_of_carton."','".$prev_excess_cut."','".$prev_plan_cut."','".$prev_status."','".$prev_update_date."',".$prev_updated_by.")";

			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);
		}
		else if( $log_update==$curr_date)
		{
			$field_array_history="po_no*po_id*matrix_type*round_type*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*phd_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*sc_lc*avg_price*doc_sheet_qty*no_of_carton*excess_cut_parcent*plan_cut*status*update_date*update_by";

			$data_array_history="'".$prev_po_no."'*".$update_id_details."*'".$prev_matrix_type."'*'".$round_type."'*'".$prev_order_status."'*'".$prev_po_received_date."'*'".$prev_po_qty."'*'".$prev_pub_shipment_date."'*'".$prev_org_shipment_date."'*'".$prev_phd_date."'*'".$prev_status."'*'".$prev_factory_rec_date."'*'".$prev_projected_po."'*'".$prev_packing."'*'".$prev_details_remark."'*'".$prev_file_no."'*'".$prev_sc_lc."'*'".$prev_avg_price."'*'".$prev_doc_sheet_qty."'*'".$prev_no_of_carton."'*'".$prev_excess_cut."'*'".$prev_plan_cut."'*'".$prev_order_status."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1);
		}
		//Log History end.-------------------------...REZA
		$breakdown_type=str_replace("'","",$cbo_breakdown_type);
		$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;

		if($breakdown_type==4)
		{
			$field_array1="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";

			$field_array_up="color_mst_id*size_mst_id*item_mst_id*item_number_id*country_id*code_id*ultimate_country_id*ul_country_code*cutup_date*cutup*country_ship_date*color_number_id*size_number_id*order_quantity*order_rate*excess_cut_perc*article_number*order_total*plan_cut_qnty*color_number_id_prev*country_ship_date_prev*pack_qty*pcs_per_pack*pack_type*pcs_pack*assort_qty*solid_qty*updated_by*update_date*status_active";
		}
		else
		{
			$field_array1="id, job_id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";

			$field_array_up="color_mst_id*size_mst_id*item_mst_id*item_number_id*country_id*code_id*ultimate_country_id*ul_country_code*cutup_date*cutup*country_ship_date*color_number_id*size_number_id*order_quantity*order_rate*excess_cut_perc*article_number*order_total*plan_cut_qnty*color_number_id_prev*country_ship_date_prev*assort_qty*solid_qty*updated_by*update_date*status_active";
		}
		$field_arr_status="status_active";
		
		$color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and status_active=1 and is_deleted=0 and color_mst_id!=0", "color_number_id", "color_mst_id");

		$size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id");

		$item_mst=return_library_array( "select item_mst_id, item_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id");
		
		/*echo "10**".$color_mst.'-'.$item_mst;
		echo "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and status_active=1 and is_deleted=0 and color_mst_id!=0";
		 die;*/

		$pre_color_date_arr=array();
		$PrevData=sql_select("select id, color_number_id, size_number_id, country_ship_date, order_quantity, country_ship_date_prev, color_number_id_prev, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id=".$update_id_details);
		foreach($PrevData as $row)
		{
			$pre_color_date_arr[$row[csf('id')]]['color_id']=$row[csf('color_number_id')];
			$pre_color_date_arr[$row[csf('id')]]['size_id']=$row[csf('size_number_id')];
			$pre_color_date_arr[$row[csf('id')]]['country_id']=$row[csf('country_id')];

			$pre_color_date_arr[$row[csf('id')]]['ship_date']=$row[csf('country_ship_date')];
			$pre_color_date_arr[$row[csf('id')]]['qty']=$row[csf('order_quantity')];
			$pre_color_date_arr[$row[csf('id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];

			$pre_color_date_arr[$row[csf('id')]]['precolor_id']=$row[csf('color_number_id_prev')];
			$pre_color_date_arr[$row[csf('id')]]['preship_date']=$row[csf('country_ship_date_prev')];
		}
		unset($PrevData);
		/*echo "10**<pre>";
		print_r($pre_color_date_arr);
		 die;*/
		$set_breck_data="'".$item_id.'___'.$item_ratio_arr[$item_id]."'";
		$add_comma=0; $data_array1=""; $ratio_comma=0; $data_array_ratio=""; $new_array_size=array();
		$size_id_arr=array(); //echo "10**";
		for($k=1; $k<=$size_table; $k++)
		{
			$txtSizeNameStr="txtSizeName_".$k;
			$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
			if(str_replace("'","",$txtSizeName)!="")
			{
				if (!in_array(str_replace("'","",$txtSizeName),$new_array_size,TRUE))
				{
					$size_id_val = return_id( str_replace("'","",$txtSizeName), $size_library, "lib_size", "id,size_name","401");
					$new_array_size[$size_id_val]=str_replace("'","",$txtSizeName);
				}
				else $size_id_val =  array_search(str_replace("'","",$txtSizeName), $new_array_size,TRUE);
			}
			else $size_id_val=0;
			
			$size_id_arr[$k]=$size_id_val;
		}
		//print_r($size_id_arr); die;
		//echo "10**";
		$tmpIds=array(); $all_update_size_id=array();
		if($breakdown_type==1)
		{
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorNamestr="txtColorName_".$i;
				$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
				if(str_replace("'","",$txtColorName)!="")
				{
					if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
					{
						$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$txtColorName);
					}
					else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
				}
				else $color_id=0;
				
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeNameStr="txtSizeName_".$m;
					$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

					//Zero Qty Accept Here
					if($pre_color_date_arr[str_replace("'","",$$txt_colorSizeId)]['qty']>0 && (str_replace("'","",$$txt_colorSizeQty)=='' || str_replace("'","",$$txt_colorSizeQty)==0)){
						//$txtcolorSizeQty=1; // Issue id: 23-2503
						$txtcolorSizeQty=0; //issue id:23-13092
					}
					else{
						$txtcolorSizeQty=str_replace("'","",$$txt_colorSizeQty);
					}

					if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);

					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];

					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$txtcolorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;

					if($excess_variable==2 && $excess_per_level==1)
					{
						$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
						$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
						//echo "10**A=".$orderexcesscut.'='.$gmtsitem.'='.$color_id;die;
						if($orderexcesscut==''){
							$orderexcesscut=0;
						}
						if($orderexcesscut>0)
						{
							//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
							$plancut=($color_size_poQty*$orderexcesscut)/100;
							$color_size_planCutQty=$plancut+$color_size_poQty;
						}
						else{
							$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
						}
						
					}
					else{
						$orderexcesscut=$$txt_colorSizeExCut;
						$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					}
					//$orderexcesscut=$$txt_colorSizeExCut;
					
					if($color_size_poQty>0)
					{
						if(str_replace("'",'',$$txt_colorSizeId)=="")
						{
							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
								$item_mst_id=$id1;
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=$id1;
								$color_mst_id=$id1;
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=$id1;
								$size_mst_id=$id1;
							}
							
							if($color_size_poQty>0)
							if ($add_comma!=0) $data_array1 .=",";
							$data_array1 .="(".$id1.",".$hidd_job_id.",".$update_id_details.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",'".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."',".$order_total_amt.",'".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
							$id1=$id1+1;
							$add_comma++;
						}
						else if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							$check_prod=0;
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <=$color_size_poQty )
							{
								$color_size_Qty=$color_size_poQty;
								$color_size_planQty=$color_size_planCutQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$color_size_planQty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['plan_cut_qnty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
								$check_prod=1;
								$order_total_amt=$color_size_Qty*$color_size_rate;
								if(($orderexcesscut*1)>0) $plancut=($color_size_Qty*$orderexcesscut)/100; else $plancut=$color_size_Qty;
								$color_size_planCutQty=$color_size_Qty*$item_ratio_arr[$item_id];
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);

							$pre_color_id=0; $pre_country_ship_date=''; $order_total_amt=0;
							$order_total_amt=$color_size_Qty*$color_size_rate;
							$PrevColorNumberId=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$PrevCountryShipDate=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];

							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['precolor_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['preship_date'];							

							if($PrevColorNumberId==$color_id)
							{
								$pre_colorid=$pre_color_id;
							}
							else 
							{
								$pre_colorid=$PrevColorNumberId;
								$iscolorchanged=1;
							}
							
							//echo $pre_colorid.'--'.$color_id.'--'.str_replace("'",'',$$txt_colorSizeId).'<br>';

							if(change_date_format($PrevCountryShipDate)==change_date_format(str_replace("'","",$txt_countryShip_date)))
							{
								$pre_countryship_date=$pre_country_ship_date;
							}
							else $pre_countryship_date=$PrevCountryShipDate;
							
							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=str_replace("'",'',$$txt_colorSizeId);
								$item_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=str_replace("'",'',$$txt_colorSizeId);
								$color_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=str_replace("'",'',$$txt_colorSizeId);
								$size_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}

							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*".$cbo_gmtsItem_id."*".$cbo_deliveryCountry_id."*".$cbo_code_id."*".$cbo_country_id."*".$cbo_countryCode_id."*".$txt_cutup_date."*".$cbo_cutOff_id."*".$txt_countryShip_date."*".$color_id."*".$size_id."*'".$color_size_Qty."'*'".$color_size_rate."'*".$orderexcesscut."*'".$txt_colorSizeArticleNo."'*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_colorid."'*'".$pre_countryship_date."'*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$cbo_status.""));
						}
					}
				}
			}//echo $data_array1;
			//print_r($data_array_up); die;
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val;
					$all_update_size_id[$val]=$val;
				}
			}
		}
		else if($breakdown_type==4)
		{
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorNamestr="txtColorName_".$i;
				$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
				if(str_replace("'","",$txtColorName)!="")
				{
					if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
					{
						$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$txtColorName);
					}
					else $color_id =  array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
				}
				else
				{
					$color_id=0;
				}
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeNameStr="txtSizeName_".$m;
					$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizePackQty="txt_colorSizePackQty_".$i.'_'.$m;
					$txt_colorSizePcsQty="txt_colorSizePcsQty_".$i.'_'.$m;

					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

					//Zero Qty Accept Here
					if($pre_color_date_arr[str_replace("'","",$$txt_colorSizeId)]['qty']>0 && (str_replace("'","",$$txt_colorSizeQty)=='' || str_replace("'","",$$txt_colorSizeQty)==0)){
						//$txtcolorSizeQty=1; // Issue id: 23-2503
						$txtcolorSizeQty=0; //issue id:23-13092
					}
					else{
						$txtcolorSizeQty=str_replace("'","",$$txt_colorSizeQty);
					}

					if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);

					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];

					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$txtcolorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;
					if($excess_variable==2 && $excess_per_level==1)
					{
						$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
						$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
						if($orderexcesscut==''){
							$orderexcesscut=0;
						}
						if($orderexcesscut>0)
						{
							//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
							$plancut=($color_size_poQty*$orderexcesscut)/100;
							$color_size_planCutQty=$plancut+$color_size_poQty;
						}
						else{
							$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
						}
					}
					else{
						$orderexcesscut=$$txt_colorSizeExCut;
						$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					}
					
					if($color_size_poQty>0)
					{
						if(str_replace("'",'',$$txt_colorSizeId)=="")
						{
							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
								$item_mst_id=$id1;
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=$id1;
								$color_mst_id=$id1;
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=$id1;
								$size_mst_id=$id1;
							}

							if ($add_comma!=0) $data_array1 .=",";
							$data_array1 .="(".$id1.",".$hidd_job_id.",".$update_id_details.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",'".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."',".$order_total_amt.",'".$color_size_planCutQty."',".$$txt_colorSizePackQty.",".$$txt_colorSizePcsQty.",".$txt_breakdownGrouping.",".$txt_pcsQty.",'".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
							$id1=$id1+1;
							$add_comma++;
						}
						else if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							$check_prod=0;
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <1 )
							{
								$color_size_Qty=$color_size_poQty;
								$color_size_planQty=$color_size_planCutQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$color_size_planQty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['plan_cut_qnty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
								$order_total_amt=$color_size_Qty*$color_size_rate;
								$check_prod=1;
							}
							if($check_prod==0)
							{
								if($production_quantity_arr[str_replace("'",'',$$txt_colorSizeId)]!="")
								{
									$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
									$check_prod=1;
								}
							}

							if($check_prod==0)
							{
								if($production_qty_arr[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$cbo_deliveryCountry_id)]!=0)
								{
									$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
									$check_prod=1;
								}
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
							$pre_color_id=0; $pre_country_ship_date=''; $order_total_amt=0;
							$order_total_amt=$color_size_Qty*$color_size_rate;
							$PrevColorNumberId=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$PrevCountryShipDate=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];

							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['precolor_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['preship_date'];							

							if($PrevColorNumberId==$color_id)
							{
								$pre_colorid=$pre_color_id;
							}
							else 
							{
								$pre_colorid=$PrevColorNumberId;
								$iscolorchanged=1;
							}
							
							//echo $pre_colorid.'--'.$color_id.'--'.str_replace("'",'',$$txt_colorSizeId).'<br>';

							if(change_date_format($PrevCountryShipDate)==change_date_format(str_replace("'","",$txt_countryShip_date)))
							{
								$pre_countryship_date=$pre_country_ship_date;
							}
							else $pre_countryship_date=$PrevCountryShipDate;
							
							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=str_replace("'",'',$$txt_colorSizeId);
								$item_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=str_replace("'",'',$$txt_colorSizeId);
								$color_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=str_replace("'",'',$$txt_colorSizeId);
								$size_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}

							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*".$cbo_gmtsItem_id."*".$cbo_deliveryCountry_id."*".$cbo_code_id."*".$cbo_country_id."*".$cbo_countryCode_id."*".$txt_cutup_date."*".$cbo_cutOff_id."*".$txt_countryShip_date."*".$color_id."*".$size_id."*'".$color_size_Qty."'*'".$color_size_rate."'*".$orderexcesscut."*'".$txt_colorSizeArticleNo."'*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_colorid."'*'".$pre_countryship_date."'*".$$txt_colorSizePackQty."*".$$txt_colorSizePcsQty."*".$txt_breakdownGrouping."*".$txt_pcsQty."*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$cbo_status.""));
						}
					}
				}
			}
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val;
					$all_update_size_id[$val]=$val;
				}
			}
		}
		else
		{
			$idratio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
			$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, inserted_by, insert_date";
			$field_array_ratioUp="country_id*gmts_item_id*code_id*ultimate_country_id*ul_country_code*country_ship_date*color_id*size_id*ratio_qty*ratio_rate*updated_by*update_date";
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorNamestr="txtColorName_".$i;
				$txtColorName="'".trim(str_replace($str_replace_check,' ',$$txtColorNamestr))."'";
				if(str_replace("'","",$txtColorName)!="")
				{
					if (!in_array(str_replace("'","",$txtColorName),$new_array_color,TRUE))
					{
						$color_id = return_id( str_replace("'","",$txtColorName), $color_library, "lib_color", "id,color_name","401");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$txtColorName);
					}
					else $color_id = array_search(str_replace("'","",$txtColorName), $new_array_color,TRUE);
				}
				else
				{
					$color_id=0;
				}
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeNameStr="txtSizeName_".$m;
					$txtSizeName="'".trim(str_replace($str_replace_check,' ',$$txtSizeNameStr))."'";
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;

					if(str_replace("'","",$$txt_colorSizeArticleNo)=="") $txt_colorSizeArticleNo="no article"; else $txt_colorSizeArticleNo=str_replace("'","",$$txt_colorSizeArticleNo);

					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];

					$txt_colorSizeRatioQty="txt_colorSizeRatioQty_".$i.'_'.$m;
					$txt_colorSizeRatioRate="txt_colorSizeRatioRate_".$i.'_'.$m;
					$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;

					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*str_replace("'","",$$txt_colorSizeRate);
					//$plancut_Qty=$color_size_poQty+((str_replace("'","",$$txt_colorSizeExCut)*$color_size_poQty)/100);
					//$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					if($excess_variable==2 && $excess_per_level==1)
					{
						$gmtsitem=str_replace("'","",$cbo_gmtsItem_id);
						$orderexcesscut=$slab_plan_cut[$gmtsitem][$color_id];
						if($orderexcesscut==''){
							$orderexcesscut=0;
						}
						if($orderexcesscut>0)
						{
							//$plancut=(str_replace("'","",$$txtorderquantity)*$orderexcesscut)/100;
							$plancut=($color_size_poQty*$orderexcesscut)/100;
							$color_size_planCutQty=$plancut+$color_size_poQty;
						}
						else{
							$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
						}
						
					}
					else{
						$orderexcesscut=$$txt_colorSizeExCut;
						$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					}
					
					//print_r($$txt_colorSizeQty).'</br>';
					if($color_size_poQty>0)
					{
						if(str_replace("'",'',$$txt_colorSizeId)=="")
						{

							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=$id1;
								$item_mst_id=$id1;
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=$id1;
								$color_mst_id=$id1;
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=$id1;
								$size_mst_id=$id1;
							}

							if ($add_comma!=0) $data_array1 .=",";
							$data_array1 .="(".$id1.",".$hidd_job_id.",".$update_id_details.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",'".$color_size_poQty."','".$color_size_rate."',".$orderexcesscut.",'".$txt_colorSizeArticleNo."',".$order_total_amt.",'".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
							$id1=$id1+1;
							$add_comma++;
						}
						else if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							$check_prod=0;
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <=$color_size_poQty )
							{
								$color_size_Qty=$color_size_poQty;
								$color_size_planQty=$color_size_planCutQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$color_size_planQty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['plan_cut_qnty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
								$order_total_amt=$color_size_Qty*$color_size_rate;
								$check_prod=1;
							}
							if($check_prod==0)
							{
								if($production_quantity_arr[str_replace("'",'',$$txt_colorSizeId)]!="")
								{
									$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
									$check_prod=1;
								}
							}

							if($check_prod==0)
							{
								if($production_qty_arr[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$cbo_deliveryCountry_id)]!=0)
								{
									$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
									$check_prod=1;
								}
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);

							$pre_color_id=0; $pre_country_ship_date=''; $order_total_amt=0;
							$order_total_amt=$color_size_Qty*$color_size_rate;
							$PrevColorNumberId=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$PrevCountryShipDate=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];

							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['precolor_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['preship_date'];							

							if($PrevColorNumberId==$color_id)
							{
								$pre_colorid=$pre_color_id;
							}
							else 
							{
								$pre_colorid=$PrevColorNumberId;
								$iscolorchanged=1;
							}
							
							//echo $pre_colorid.'--'.$color_id.'--'.str_replace("'",'',$$txt_colorSizeId).'<br>';

							if(change_date_format($PrevCountryShipDate)==change_date_format(str_replace("'","",$txt_countryShip_date)))
							{
								$pre_countryship_date=$pre_country_ship_date;
							}
							else $pre_countryship_date=$PrevCountryShipDate;
							
							if (array_key_exists(str_replace("'","",$cbo_gmtsItem_id),$item_mst))
							{
								$item_mst_id=$item_mst[str_replace("'","",$cbo_gmtsItem_id)];
							}
							else
							{
								$item_mst[str_replace("'","",$cbo_gmtsItem_id)]=str_replace("'",'',$$txt_colorSizeId);
								$item_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							
							if(array_key_exists($color_id,$color_mst))
							{
								$color_mst_id=$color_mst[$color_id];
							}
							else
							{
								$color_mst[$color_id]=str_replace("'",'',$$txt_colorSizeId);
								$color_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}
							if(array_key_exists($size_id,$size_mst))
							{
								$size_mst_id=$size_mst[$size_id];
							}
							else
							{
								$size_mst[$size_id]=str_replace("'",'',$$txt_colorSizeId);
								$size_mst_id=str_replace("'",'',$$txt_colorSizeId);
							}

							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*".$cbo_gmtsItem_id."*".$cbo_deliveryCountry_id."*".$cbo_code_id."*".$cbo_country_id."*".$cbo_countryCode_id."*".$txt_cutup_date."*".$cbo_cutOff_id."*".$txt_countryShip_date."*".$color_id."*".$size_id."*'".$color_size_Qty."'*'".$color_size_rate."'*".$orderexcesscut."*'".$txt_colorSizeArticleNo."'*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_colorid."'*'".$pre_countryship_date."'*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$cbo_status.""));
						}

						if(str_replace("'",'',$$txt_colorSizeRatioId)=="")
						{
							if ($ratio_comma!=0) $data_array_ratio .=",";
								$data_array_ratio .="(".$idratio.",".$hidd_job_id.",".$update_id_details.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_countryShip_date.",'".$color_id."','".$size_id."',".$$txt_colorSizeRatioQty.",".$$txt_colorSizeRatioRate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$idratio=$idratio+1;
							$ratio_comma++;
						}
						else
						{
							$idRatio_arr[]=str_replace("'",'',$$txt_colorSizeRatioId);
							$data_array_ratio_up[str_replace("'",'',$$txt_colorSizeRatioId)] =explode("*",("".$cbo_deliveryCountry_id."*".$cbo_gmtsItem_id."*".$cbo_code_id."*".$cbo_country_id."*".$cbo_countryCode_id."*".$txt_countryShip_date."*".$color_id."*".$size_id."*".$$txt_colorSizeRatioQty."*".$$txt_colorSizeRatioRate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}
				}
			}//echo $data_array1;
			//print_r($data_array_up); die;
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val;
					$all_update_size_id[$val]=$val;
				}
			}
			$all_ratio_id="";
			foreach($idRatio_arr as $val)
			{
				if($val!="")
				{
					if($all_ratio_id=="") $all_ratio_id=$val; else $all_ratio_id.=','.$val;
				}
			}
		}
		
		//oci_rollback($con); disconnect($con); die;

		//print_r($all_id);
		$flag=1;
		$nodeleted_ids=array_merge($tmpIds,$id_arr); $colSizeUpdate_id_array=array();
		if(implode(',',$nodeleted_ids)!='') $col_id_cond="and id not in (".implode(',',$nodeleted_ids).")"; else $col_id_cond="";
		if($flag==1)
		{
			if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";
			$colSize_sql_dtls="Select id, color_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and country_id=$cbo_deliveryCountry_id and item_number_id=$cbo_gmtsItem_id and status_active!=0 and is_deleted=0 $col_id_cond $pack_type_cond";//

			//echo "10**".$colSize_sql_dtls; die;
			$nameArray=sql_select( $colSize_sql_dtls );
			foreach($nameArray as $row)
			{
				if( ($prod_array[str_replace("'","",$update_id_details)][str_replace("'","",$cbo_deliveryCountry_id)][str_replace("'","",$cbo_gmtsItem_id)][$row[csf("color_number_id")]][$row[csf("size_number_id")]]*1)==0 )
					$colSizeUpdate_id_array[]=$row[csf('id')];
			}
		}

		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;

		
		if($data_array1!="")
		{
			//echo "insert into wo_po_color_size_breakdown (".$field_array1.") values ".$data_array1;die;
			$rID4=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}

		/*foreach($id_arr as $id_up)
		{
			$rID1=sql_update("wo_po_color_size_breakdown",$field_array_up,$data_array_up,"id","".$id_up."",1);
			if($rID1) $flag=1; else $flag=0;
		}*/
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}


		//$distance_delete_id=array_diff($colSizeUpdate_id_array,$nodeleted_ids);

		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		foreach($colSizeUpdate_id_array as $id_val)
		{
			$rID5=sql_update("wo_po_color_size_breakdown",$field_array_del,$data_array_del,"id","".$id_val."",1);
			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		}
		//zakaria joy 
		$size_all_id=str_replace("'","",$size_all_id);
		$size_all_id_arr=explode(",",$size_all_id);
		$deleted_size_id=array_diff($size_all_id_arr,$all_update_size_id);
		if(count($deleted_size_id)>0){
			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'47'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			foreach($deleted_size_id as $key=>$id_val)
			{
				if($id_val>0){
					$rID90=sql_update("wo_po_color_size_breakdown",$field_array_del,$data_array_del,"id","".$id_val."",1);
					if($rID90==1 && $flag==1) $flag=1; else $flag=0;
				}				
			}
		}
		//End Zakaria joy
		if($flag==1)
		{
			$sam=1;
			if($db_type==0) $sequNullCheck="IFNULL(sequ,0)";
		 	else if($db_type==2) $sequNullCheck="nvl(sequ,0)";
			$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
			$sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where $sequNullCheck!=0 and buyer_id=$cbo_buyer_name order by sequ");
			$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted";
			$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$update_id_details and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
			//print_r($data_array_sample);
			foreach($sample_tag as $sample_tag_row)
			{
				foreach ( $data_array_sample as $row_sam1 )
				{
					$dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0");
					list($idsm)=$dup_data;
					if( $idsm[csf('id')] =='')
					{
						if ($sam!=1) $data_array_sm .=",";
						$data_array_sm .="(".$id_sm.",".$update_id.",".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
						$id_sm=$id_sm+1;
						$sam=$sam+1;
					}
				}
			}

			if($data_array_sm !='')
			{
				$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}

			//============================================================================================

			$dup_lap=sql_select("select id, color_name_id from wo_po_lapdip_approval_info where job_no_mst='$update_id' and po_break_down_id=$update_id_details and status_active=1 and is_deleted=0");
			$labdip_arr=array();
			foreach($dup_lap as $row)
			{
				$labdip_arr[$row[csf('color_name_id')]]['id']=$row[csf('id')];
				$labdip_arr[$row[csf('color_name_id')]]['color']=$row[csf('color_name_id')];
			}
			unset($dup_lap);
			$lap=1;
			$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
			// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
			$field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted";
			$data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and  b.po_break_down_id=$update_id_details and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
			$lab_id_arr=array(); $save_id=array();
			foreach ( $data_array_lapdip as $row_lap1 )
			{
				//$dup_lap=sql_select("select id from wo_po_lapdip_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_lap1[csf('po_id')]." and color_name_id=".$row_lap1[csf('color_number_id')]."  and status_active=1 and is_deleted=0");
				//list($idlap)=$dup_lap;
				if( $labdip_arr[$row_lap1[csf('color_number_id')]]['id'] =='')
				{
					if ($lap!=1) $data_array_lap .=",";
					$data_array_lap .="(".$id_lap.",".$update_id.",".$row_lap1[csf('po_id')].",".$row_lap1[csf('color_number_id')].",1,0)";
					$lab_id_arr[]=$id_lap;
					$id_lap=$id_lap+1;
					$lap=$lap+1;
				}
				else
				{
					$save_id[]=$labdip_arr[$row_lap1[csf('color_number_id')]]['id'];
				}
			}

			$nodel_ids=array_merge($save_id,$lab_id_arr);

			if($data_array_lap !='')
			{
				$rID4=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;
			}
			if(implode(",",$nodel_ids)!='') $riD5=execute_query( "update wo_po_lapdip_approval_info set status_active='0', is_deleted='1' where po_break_down_id=$update_id_details and id not in (".implode(",",$nodel_ids).") and lapdip_target_approval_date is null and send_to_factory_date is null and submitted_to_buyer is null and approval_status=0 and approval_status_date is null",1);
		}

		/*if($data_arr_status!="")
		{
			$rIDs=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_arr_status,$data_arr_status,$id_arr ));
			if($rIDs) $flag=1; else $flag=0;
		}*/
		
		execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
		execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);

		if($breakdown_type==2 || $breakdown_type==3)
		{
			$ratio_sql="Select id from wo_po_ratio_breakdown where po_id=$update_id_details and country_id=$cbo_deliveryCountry_id and gmts_item_id=$cbo_gmtsItem_id and status_active=1 and is_deleted=0";
			$ratio_sql_res=sql_select( $ratio_sql );
			foreach($ratio_sql_res as $row)
			{
				$ratioUpdate_id_array[]=$row[csf('id')];
			}
			if(implode(',',$idRatio_arr)!="")
			{
				$distance_ratio_delete_id=array_diff($ratioUpdate_id_array,$idRatio_arr);
			}
			else
			{
				$distance_ratio_delete_id=$ratioUpdate_id_array;
			}
			//print_r ($distance_ratio_delete_id);
			$field_array_ratio_del="status_active*is_deleted*updated_by*update_date";
			$data_array_ratio_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//print_r ($distance_ratio_delete_id);
			if(implode(',',$distance_ratio_delete_id)!="")
			{
				foreach($distance_ratio_delete_id as $id_val)
				{
					$rIDdelratio=sql_update("wo_po_ratio_breakdown",$field_array_ratio_del,$data_array_ratio_del,"id","".$id_val."",1);
					if($rIDdelratio==1 && $flag==1) $flag=1; else $flag=0;
				}
			}

			if($data_array_ratio!="")
			{
				//echo "insert into wo_po_ratio_breakdown (".$field_array_ratio.") values ".$data_array_ratio;die;
				$rIDratio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
				if($rIDratio==1 && $flag==1) $flag=1; else $flag=0;
			}
			$rIDratioUp=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_array_ratioUp,$data_array_ratio_up,$idRatio_arr ));
			if($rIDratioUp==1 && $flag==1) $flag=1; else $flag=0;
		}
		$po_id=str_replace("'",'',$update_id_details);

		//echo $update_id; die;
		update_color_size_sequence($update_id,2);
		//job_order_qty_update($update_id,$po_id,$tot_set_qnty,$cbo_order_status);
		$return_data=job_order_qty_update($update_id,$update_id_details,$set_breck_down,$breakdown_type,'0');

		update_cost_sheet($update_id);
		//echo $return_data; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "select item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and is_deleted=0 and status_active=1 group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code order by country_ship_date";

		if (is_duplicate_field( "po_break_down_id", "pro_garments_production_mst", "po_break_down_id=$update_id_details and is_deleted=0 and production_quantity>0" ) == 1)
		{
			echo "14**Production Found";
			die;
		}
		else if (is_duplicate_field( "order_id", "ppl_cut_lay_size", "order_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**Cut and Lay Found";
			disconnect($con);die;
		}
		else if (is_duplicate_field( "po_breakdown_id", "pro_roll_details", "po_breakdown_id=$update_id_details and is_deleted=0 and status_active=1 and is_sales<>1 and booking_without_order=0" ) == 1)
		{
			echo "14**Roll Data Found";
			disconnect($con);die;
		}
		else if (is_duplicate_field( "po_break_down_id", "pro_bundle_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**Bundle Data Found";
			disconnect($con);die;
		}

		$sql_booking_no=sql_select("select booking_no from wo_booking_dtls where po_break_down_id=".$update_id_details." and status_active=1 and is_deleted=0 group by booking_no");
		$booking_str="";
		foreach($sql_booking_no as $row)
		{
			if($booking_str=="") $booking_str=$row[csf('booking_no')]; else $booking_str.=', '.$row[csf('booking_no')];
		}

		if($booking_str!="")
		{
			echo "13**".$booking_str;
			disconnect($con);die;
		}

		if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";
		$sql= sql_select("select item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and is_deleted=0 and status_active!=0 group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type order by country_ship_date");
		//echo count($sql); die;
		$flag=1;

		if(str_replace("'",'',$delete_country)==1)
		{
			if(count($sql)==1)
			{
				$field_array_po="status_active*is_deleted*updated_by*update_date";
				$data_array_po="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID1=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$update_id_details."",1);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
				
				$field_array="status_active*is_deleted*updated_by*update_date";
				$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$rID=sql_update("wo_po_color_size_breakdown",$field_array,$data_array,"po_break_down_id","".$update_id_details."",1);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;
				
			}
			else
			{
				for($i=1; $i<=$color_table; $i++)
				{
					for($m=1; $m<=$size_table; $m++)
					{
						$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
						$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;
						if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
						}
	
						if (str_replace("'",'',$$txt_colorSizeRatioId)!="")
						{
							$id_ratio_arr[]=str_replace("'",'',$$txt_colorSizeRatioId);
						}
					}
				}
	
				$field_array="status_active*is_deleted*updated_by*update_date";
				$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				foreach($id_arr as $val)
				{
					$rID=sql_update("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$val."",1);
					if($rID==1 && $flag==1) $flag=1; else $flag=0;
				}
				$field_array_ratio="status_active*is_deleted*updated_by*update_date";
				$data_array_ratio="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				foreach($id_ratio_arr as $val)
				{
					$rIDratio=sql_update("wo_po_color_size_breakdown",$field_array_ratio,$data_array_ratio,"id","".$val."",1);
					if($rIDratio==1 && $flag==1) $flag=1; else $flag=0;
				}
			}
		}
		else
		{
			 $flag=0;
		}
		//$return_data=update_job_mast($update_id);
		$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
		//echo $update_id.'=='.$update_id_details.'=='.$set_breck_down; die;
		update_color_size_sequence($update_id,2);
		//job_order_qty_update($update_id,$po_id,$tot_set_qnty,$cbo_order_status);
		//$return_data=job_order_qty_update($update_id,$update_id_details,$set_breck_down,$breakdown_type,'0');
		$return_data=job_order_qty_update($update_id,$update_id_details,$set_breck_down,$breakdown_type,'0');
		update_cost_sheet($update_id);
		//die;
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$delete_po)."**".$return_data;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		//echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3];
		disconnect($con);
		die;
	}
}

if($action=="check_tna_leadtime")
{
	$data=explode("_",$data);
	$txt_po_received_date=date('Y-m-d',strtotime($data[2]));
    $txt_pub_shipment_date=date('Y-m-d',strtotime($data[3]));
    $dDiff=datediff( 'd', $txt_po_received_date, $txt_pub_shipment_date, $using_timestamps = false );
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific from tna_task_template_details where for_specific=$data[0] and lead_time<= '$dDiff'  and status_active=1 and is_deleted=0");
	//echo "select count(for_specific) as for_specific from tna_task_template_details where for_specific=$data[0] and lead_time<= '$dDiff'  and status_active=1 and is_deleted=0";
	foreach($sql_temp as $row_temp){
		if($row_temp[csf('for_specific')]>0) $temp=1; else $temp=0;
	}
	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1] and variable_list=14 and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	
	$tna_process_type=return_field_value("tna_process_type", "variable_order_tracking", "company_name=$data[1] and variable_list=31 and status_active=1 and is_deleted=0");
    if($tna_process_type==1) $tna_process=1; else $tna_process=0;
	
	echo $temp."_".$tna."_".$dDiff."_".$tna_process;
	die;
}
// function================

function get_tna_template( $remain_days, $tna_template, $buyer )
{
	global $tna_template_buyer;
	if(count($tna_template_buyer[$buyer])>0)
	{
		$n=count($tna_template_buyer[$buyer]);
		for($i=0;$i<$n;$i++)
		{
			if($remain_days<=$tna_template_buyer[$buyer][$i]['lead'])
			{
				if ($i!=0)
				{
					$up_day=$tna_template_buyer[$buyer][$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template_buyer[$buyer][$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template_buyer[$buyer][$i-1]['id'];
					else
						return $tna_template_buyer[$buyer][$i]['id'];
				}
				else
				{
					return $tna_template_buyer[$buyer][$i]['id'];
				}
			}
		}
	}
	else
	{
		$n=count($tna_template);
		for($i=0;$i<$n;$i++)
		{
			if($remain_days<=$tna_template[$i]['lead'])
			{
				if ($i!=0)
				{
					$up_day=$tna_template[$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template[$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template[$i-1]['id'];
					else

						return $tna_template[$i]['id'];
				}
				else
				{
					return $tna_template[$i]['id'];
				}
			}
		}
	}
}

if($action=="get_unit_price"){
	$data_arr=explode("*",$data);
	//po_id+'*'+country+'*'+item+'*'+color+'*'+size
	$sql_data=sql_select("SELECT order_rate from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id=$data_arr[0] and item_number_id=$data_arr[2] and color_number_id=$data_arr[3] and size_number_id=$data_arr[4]");
	$unit_price=0;
	foreach($sql_data as $row)
	{
		$unit_price=$row[csf('order_rate')];
	}
	echo $unit_price;
	disconnect($con);die;
}

if ($action=="actual_po_info_popup") //ISD-22-08883
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][365] );
	

	$act_po_variable_sql=sql_select("select id, exeed_budge_qty from variable_order_tracking where company_name='$company_id' and variable_list=52 order by id");
	foreach($act_po_variable_sql as $row){
		$act_po_variable=$row[csf('exeed_budge_qty')];
	}
	$current_acc_po_dtls=sql_select("SELECT sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.acc_po_status=1 and a.po_break_down_id=$po_id and a.job_id=$job_id");
	foreach($current_acc_po_dtls as $row){
		$currrent_po_qty=$row[csf('po_qty')];
	}

	$balance_po_qty=$po_quantity-$currrent_po_qty;

	$gmts_item_arr=sql_select("SELECT item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id='$po_id'");
	foreach($gmts_item_arr as $row){
		$gmts_item_data[$row[csf('item_number_id')]]=$row[csf('item_number_id')];
	}
	$gmts_item=implode(",",$gmts_item_data);
	
	?> 
	<script>

 		var field_level_data=<?=$data_arr;?>;

		var permission='<?=$permission; ?>';
			
		function add_break_down_tr(i) 
		{
			var row_num=$('#actual_po_details tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				var country_id = $('#cboCountryId_'+i).val();
				var gmtsItem_id = $('#cboGmtsItemId_'+i).val();
				var gmtscolor = $('#cbogmtscolor_'+i).val();
				var gmtssize = $('#cbogmtssize_'+i).val();
				i++;
				$("#actual_po_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
					});  
				}).end().appendTo("#actual_po_details");
				$('#actual_po_details tr:last td:eq(3)').attr('id','gmtssize_'+i);
				$('#poQnty_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
				$('#pounitprice_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
				$('#cboGmtsItemId_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",2);");
				$('#cbogmtscolor_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",3);");
				$('#cbogmtssize_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",4);");

				$('#cbogmtscolor_'+i).removeAttr("onchange").attr("onchange","get_gmts_size(this.value,"+i+");");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
				$('#rowid_'+i).val("");	
				$('#cboCountryId_'+i).val(country_id);
				$('#cboGmtsItemId_'+i).val(gmtsItem_id);
				$('#cbogmtscolor_'+i).val(gmtscolor);
				$('#cbogmtssize_'+i).val(gmtssize);				
				fnc_poqty_cal();
				set_all_onclick();
				navigate_arrow_key();
			}
		}
		function pack_add_break_down_tr(i) 
		{
			var row_num=$('#packing_finishing tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				var gmtsItem_id = $('#cbopackGmtsItemId_'+i).val();
				var gmtscolor = $('#cbopackgmtscolor_'+i).val();
				var gmtssize = $('#cbopackgmtssize_'+i).val();
				i++;
				$("#packing_finishing tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
					});  
				}).end().appendTo("#packing_finishing");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","pack_add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","pack_fn_deletebreak_down_tr("+i+");");
				$('#packrowid_'+i).val("");
				$('#cbopackGmtsItemId_'+i).val(gmtsItem_id);
				$('#cbopackgmtscolor_'+i).val(gmtscolor);
				$('#cbopackgmtssize_'+i).val(gmtssize);
				set_all_onclick();
				navigate_arrow_key();
			}
		}		
		function fn_deletebreak_down_tr_bk(rowNo) 
		{			
			var numRow = $('#actual_po_details tr').length; 
			var po_qty=$("#txt_po_qty").val()*1;
			var balance_poqty=0;
			var dtls_data="";
			console.log(numRow);
			if(rowNo==numRow && rowNo!=1)
			{
				var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
				if(r==false)
				{
					return;
				}
				var permission_array=permission.split("_");
				var rowid=$('#rowid_'+rowNo).val();
				var updateid=$('#update_id').val();
				if(rowid !="" && permission_array[2]==1)
				{
					var dtls_data=return_global_ajax_value(rowid+'_'+updateid, 'delete_row', '', 'order_entry_controller');
				}
				var index=rowNo-1
				$('#actual_po_details tr:eq('+index+')').remove();
				dtls_data_arr= dtls_data.split("**");
				balance_poqty=po_qty-dtls_data_arr[1];
				$("#txt_po_balance_qty").val(balance_poqty);
				$("#fixed_balance_qty").val(balance_poqty);
				$("#balance_po").html(balance_poqty);
				set_sum_value_set( 'txtTotPoQnty', 'poQnty_' );
				set_sum_value_set( 'txtTotPoValue', 'povalue_' );
				navigate_arrow_key();
			}
		}
		function fn_deletebreak_down_tr(rowNo) 
		{			
			var numRow = $('#actual_po_details tr').length; 
			var po_qty=$("#txt_po_qty").val()*1;
			var balance_poqty=0;
			var dtls_data="";
			if(numRow!=1)
			{
				var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
				if(r==false)
				{
					return;
				}
				var permission_array=permission.split("_");
				var rowid=$('#rowid_'+rowNo).val();
				var updateid=$('#update_id').val();
				if(rowid !="" && permission_array[2]==1)
				{
					var dtls_data=return_global_ajax_value(rowid+'_'+updateid, 'delete_row', '', 'order_entry_controller');
				}
				var index=rowNo-1
				$('#actual_po_details tr:eq('+index+')').remove();
				var numRow = $('#actual_po_details tr').length; 

				for(var i = rowNo;i <= numRow;i++)
				{
					$("#tbl_list_search tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'value': function(_, value) { return value }
						});
						$('#actual_po_details tr:last td:eq(3)').attr('id','gmtssize_'+i);
						$('#poQnty_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
						$('#pounitprice_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
						$('#cboGmtsItemId_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",2);");
						$('#cbogmtscolor_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",3);");
						$('#cbogmtssize_'+i).removeAttr("onchange").attr("onchange","get_unit_price(this.value,"+i+",4);");

						$('#cbogmtscolor_'+i).removeAttr("onchange").attr("onchange","get_gmts_size(this.value,"+i+");");
						$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
						$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
					})
				}
				dtls_data_arr= dtls_data.split("**");
				balance_poqty=po_qty-dtls_data_arr[1];
				$("#txt_po_balance_qty").val(balance_poqty);
				$("#fixed_balance_qty").val(balance_poqty);
				$("#balance_po").html(balance_poqty);
				set_sum_value_set( 'txtTotPoQnty', 'poQnty_' );
				set_sum_value_set( 'txtTotPoValue', 'povalue_' );
				navigate_arrow_key();
			}
		}
		function pack_fn_deletebreak_down_tr(rowNo) 
		{			
			var numRow = $('#packing_finishing tr').length;
			var balance_poqty=0;
			if(rowNo==numRow && rowNo!=1)
			{
				var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
				if(r==false)
				{
					return;
				}
				var permission_array=permission.split("_");
				var rowid=$('#packrowid_'+rowNo).val();
				if(rowid !="" && permission_array[2]==1)
				{
					var dtls_data=return_global_ajax_value(rowid, 'pack_delete_row', '', 'order_entry_controller');
				}
				var index=rowNo-1
				$('#packing_finishing tr:eq('+index+')').remove();
				navigate_arrow_key();
			}
		}			
		function fnc_acc_po_info( operation )
		{
			freeze_window(operation);
			if (form_validation('actpoNo*txt_po_rcv_date*txt_po_shipment_date','Actual PO No*PO Rcv Date*PO Ship Date')==false)
			{
				release_freezing();
				return; 
			}
			var job_no= $('#txt_job_no').val();
			var row_num = $('#actual_po_details tr').length; 
			var z=1;  
			var po_item_chk_arr=new Array();
			var z=1; var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cboCountryId_'+i+'*cboGmtsItemId_'+i+'*cbogmtscolor_'+i+'*cbogmtssize_'+i+'*poQnty_'+i+'*pounitprice_'+i,'Country*Gmts Item*Gmts Color*Gmts Size*PO Qty*Unit Price')==false)
				{
					release_freezing();
					return; 
				}
				var CountryId= $('#cboCountryId_'+i).val();
				var GmtsItemId= $('#cboGmtsItemId_'+i).val();
				var gmtscolor= $('#cbogmtscolor_'+i).val();
				var gmtssize= $('#cbogmtssize_'+i).val();
				po_item_chk_arr.push(CountryId+'#'+GmtsItemId+'#'+gmtscolor+'#'+gmtssize);
				//console.log(CountryId+'#'+GmtsItemId+'#'+gmtscolor+'#'+gmtssize);
				if(hasDuplicates(po_item_chk_arr)) {
					alert('Error: you have duplicates values !');
					release_freezing();
					return;
				}
				data_all+="&cboCountryId_" + z + "='" + $('#cboCountryId_'+i).val()+"'"+"&cboGmtsItemId_" + z + "='" + $('#cboGmtsItemId_'+i).val()+"'"+"&cbogmtscolor_" + z + "='" + $('#cbogmtscolor_'+i).val()+"'"+"&cbogmtssize_" + z + "='" + $('#cbogmtssize_'+i).val()+"'"+"&poQnty_" + z + "='" + $('#poQnty_'+i).val()+"'"+"&pounitprice_" + z + "='" + $('#pounitprice_'+i).val()+"'"+"&povalue_" + z + "='" + $('#povalue_'+i).val()+"'"+"&rowid_" + z + "='" + $('#rowid_'+i).val()+"'";
				z++;
			}
			
			var data="action=save_update_delete_accpoinfo&operation="+operation+"&total_row="+row_num+get_submitted_data_string('hid_po_id*txt_job_no*hid_job_id*actpoNo*txt_rcv_ship_date*txt_po_shipment_date*txt_po_rcv_date*cbo_ship_mode*actpostatus*txtTotPoQnty*txtTotPoValue*update_id*txt_po_remarks',"../../../")+data_all;
			
			http.open("POST","order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_acc_po_info_reponse;
		}
		function fnc_acc_po_info_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				//if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==10)
				{
					show_msg(trim(reponse[0]));
					release_freezing();
					return;
				}
				if(reponse[0]==11)
				{
					alert("Duplicate Actual PO Data Found.");
					release_freezing();
					return;
				}
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					var poqty=$("#txt_po_qty").val()*1;
					var poid=$("#hid_po_id").val()*1;
					get_php_form_data(poid+'_'+poqty, 'acc_po_balance_qty', 'order_entry_controller');
					var datalist=document.getElementById('hid_po_id').value+'__'+document.getElementById('txt_job_no').value;
					show_list_view( datalist,'accpo_list_view','save_up_list_view','order_entry_controller','');//setFilterGrid(\'tbl_upListView\',-1)
					var tableFilters_po = 
					{
						//col_0: "none",col_1:"none",display_all_text: " -- All --",
						col_operation: { 
							id: ["total_po_qty"],
							col: [6],
							operation: ["sum"],
							write_method: ["innerHTML"]
						}
					}
					setFilterGrid("tbl_upListView",-1,tableFilters_po);
						
					
					$('#tbl_list_search tbody tr:not(:first)').remove();
					reset_form('accpoinfo_1','','','','','hid_po_id*hid_job_id*txt_job_no*txt_po_qty*txt_po_balance_qty*fixed_balance_qty');
					$('#rowid_1').val("");
					$('#cboCountryId_1').val(0);
					$('#cboGmtsItemId_1').val(0);
					$('#cbogmtscolor_1').val(0);
					$('#cbogmtssize_1').val(0);
					$('#poQnty_1').val("");
					$('#pounitprice_1').val("");
					$('#povalue_1').val("");
					$('#txtTotPoQnty').val("");
					$('#txtTotPoValue').val("");
					set_button_status(0, permission, 'fnc_acc_po_info',1);

					$('#packing_finishing_info tbody tr:not(:first)').remove();
					reset_form('pack_finish_2','','','','');
					$('#cbopackGmtsItemId_1').val(0);
					$('#cbopackgmtscolor_1').val(0);
					$('#cbopackgmtssize_1').val(0);
					$('#cartonQnty_1').val("");
					$('#cbm_1').val("");
					set_button_status(0, permission, 'fnc_pack_finish_info',2);

					release_freezing();
					navigate_arrow_key();
				}
			}
		}
		function fnc_pack_finish_info(operation){
			var act_po_id=$('#update_id').val()*1;
			if(act_po_id==''){
				alert("Please Save Actual PO Info");
				release_freezing();
				return;
			}
			freeze_window(operation);
			var row_num = $('#packing_finishing tr').length;   
			var pack_item_chk_arr=new Array();
			var z=1; var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('cbopackGmtsItemId_'+i+'*cartonQnty_'+i+'*cbm_'+i,'Gmts Item*No. Of Carton*CBM')==false)
				{
					release_freezing();
					return; 
				}
				var packgmtsItemId= $('#cbopackGmtsItemId_'+i).val();
				var packgmtscolor= $('#cbopackgmtscolor_'+i).val();
				var packgmtssize= $('#cbopackgmtssize_'+i).val();
				var carton= $('#cartonQnty_'+i).val();
				var cbm= $('#cbm_'+i).val();
				/* console.log(packgmtsItemId+'--'+packgmtscolor+'--'+packgmtssize+'--'+carton+'--'+cbm+'--');
				return; */
				pack_item_chk_arr.push(packgmtsItemId+'#'+packgmtscolor+'#'+packgmtssize);
				if(hasDuplicates(pack_item_chk_arr)) {
					alert('Error: you have duplicates values !');
					release_freezing();
					return;
				}
				data_all+="&cboGmtsItemId_" + z + "='" + $('#cbopackGmtsItemId_'+i).val()+"'"+"&cbogmtscolor_" + z + "='" + $('#cbopackgmtscolor_'+i).val()+"'"+"&cbogmtssize_" + z + "='" + $('#cbopackgmtssize_'+i).val()+"'"+"&cartonQnty_" + z + "='" + $('#cartonQnty_'+i).val()+"'"+"&cbm_" + z + "='" + $('#cbm_'+i).val()+"'"+"&packrowid_" + z + "='" + $('#packrowid_'+i).val()+"'";
				z++;
			}
			
			var data="action=save_update_delete_actpack&operation="+operation+"&total_row="+row_num+get_submitted_data_string('hid_po_id*txt_job_no*hid_job_id*update_id',"../../../")+data_all;
			
			http.open("POST","order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_pack_finish_info_reponse;
		}
		function fnc_pack_finish_info_reponse(){
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				if(reponse[0]==10)
				{
					show_msg(trim(reponse[0]));
					release_freezing();
					return;
				}
				if(reponse[0]==0 || reponse[0]==1)
				{
					set_button_status(1, permission, 'fnc_pack_finish_info',2);
				}
				else{
					set_button_status(0, permission, 'fnc_pack_finish_info',2);
				}
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					
					var act_po_id=$("#update_id").val();
					var po_id=$("#hid_po_id").val();
					show_list_view(act_po_id+'_'+po_id,'populate_act_pack_details','packing_finishing','order_entry_controller','');
					
					release_freezing();
				}
				navigate_arrow_key();
			}
		}
		function hasDuplicates(arr) {
			var counts = [];
			
			for (var i = 0; i <= arr.length; i++) {
				if (counts[arr[i]] === undefined) 
				{
					counts[arr[i]] = 1;
				} 
				else
				{
				return true;
				}
			}
			return false;
		}		
		function fnc_poqty_cal()
		{
			var row_num = $('#actual_po_details tr').length;
			var totQty=totvalue=povalue=0;
			var poqty=$("#txt_po_balance_qty").val()*1;
			var act_qty_variable=$("#act_qty_variable").val()*1;
			var fixed_bal_qty=$("#fixed_balance_qty").val()*1;
			for (var i=1; i<=row_num; i++)
			{
				if( ($("#poQnty_"+i).val()*1)>0)
				{
					if(act_qty_variable==1){
						totQty+=$("#poQnty_"+i).val()*1;
						if(totQty>fixed_bal_qty)
						{
							alert("Actual PO Qty Over from PO Quantity");
							$("#poQnty_"+i).val('');
							$("#povalue_"+i).val('');
							return;
						}
					}					
					if(($("#pounitprice_"+i).val()*1)>0){
						povalue=$("#poQnty_"+i).val()*$("#pounitprice_"+i).val()*1;
						$("#povalue_"+i).val(povalue);
					}
					
				}
			}		
			set_sum_value_set( 'txtTotPoQnty', 'poQnty_' );
			set_sum_value_set( 'txtTotPoValue', 'povalue_' );
			navigate_arrow_key();
			
		}
		function set_sum_value_set(des_fil_id,field_id)
		{
			var rowCount = $('#actual_po_details tr').length;
			if(des_fil_id=="txtTotPoQnty" || des_fil_id=="txtTotPoValue")
			{
				math_operation( des_fil_id, field_id, '+', rowCount );
			}
		}		
		function get_temp_data(rowid)
		{
			$('#actual_po_details tr:last').remove();
			$('#packing_finishing tr:last').remove();
			var poqty=$("#txt_po_qty").val()*1;
			get_php_form_data(rowid+'_'+poqty, 'populate_acc_details_data', 'order_entry_controller');
			show_list_view(rowid+'_'+document.getElementById('hid_po_id').value+'_'+document.getElementById('hid_job_id').value,'show_acc_po_dtls','actual_po_details','order_entry_controller','');
			show_list_view(rowid+'_'+document.getElementById('hid_po_id').value+'_'+document.getElementById('hid_job_id').value,'show_act_pack_dtls','packing_finishing','order_entry_controller','');
			navigate_arrow_key();
		}
		function ship_date_validation(type)
		{
			var po_rcv_date=$('#hid_po_rcv_date').val();
			var poshipdate=document.getElementById('txt_po_shipment_date').value;
			var rcvshipdate=document.getElementById('txt_po_rcv_date').value;
			//alert(po_rcv_date+'=='+poshipdate+'--'+rcvshipdate);
			if(type==1){
				var datediff = date_compare(po_rcv_date,rcvshipdate);
				if(datediff==false)
				{
					alert("Actual PO Recv Date Is Less Than PO Received Date.");
					$('#txt_po_rcv_date').val("");
					return;
				}
			}
			if(type==2){
				var datediff = date_compare(po_rcv_date,poshipdate);
				if(datediff==false)
				{
					alert("Actual PO Ship Date Is Less Than PO Received Date.");
					$('#txt_po_shipment_date').val("");
					return;
				}
			}
		}
		function get_unit_price(value, row, type){
			var country= $("#cboCountryId_"+row).val();
			var item= $("#cboGmtsItemId_"+row).val();
			var color= $("#cbogmtscolor_"+row).val();
			var size= $("#cbogmtssize_"+row).val();
			var po_id= $("#hid_po_id").val();
			if(type==1){
				country=value;
			}
			else if(type==2){
				item=value;
			}
			else if(type==3){
				color=value;
			}
			else{
				size=value;
			}
			var unit_price = return_ajax_request_value(po_id+'*'+country+'*'+item+'*'+color+'*'+size, 'get_unit_price', 'order_entry_controller');
			$('#pounitprice_'+row).val(unit_price);
			fnc_poqty_cal();
		}
		function get_gmts_size(value,row,type){	
			var item= $("#cboGmtsItemId_"+row).val();
			var color= $("#cbogmtscolor_"+row).val();
			var po_id= $("#hid_po_id").val();
			if(type==1){
				item=value;
			}
			else{
				color=value;
			}	
			load_drop_down( 'order_entry_controller', item+'_'+row+'_'+color+'_'+po_id, 'load_drop_down_gmtssize', 'gmtssize_'+row );
		}
		function navigate_arrow_key()
		{
			$('input').keyup(function(e){
				if( e.which==39 )
				{
						//if( $(this).getCursorPosition() == $(this).val().length )
						$(this).closest('td').next().find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
				}
				else if( e.which==37 )
				{
					//if( $(this).getCursorPosition() == 0 )
						$(this).closest('td').prev().find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
				}
				else if( e.which==40 )
				{
					$(this).closest('tr').next().find('td:eq('+$(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
				}
				else if( e.which==38 )
				{
					$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric,.combo_boxes').select();
				}
			});
		}

    </script>
</head>
<body>
<div align="center">
	<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
	<div style="font-size:16px; color:#36F">Actual Po Entry Master</div>
	<fieldset style="width:850px">
    <form id="accpoinfo_1" autocomplete="off">
		<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="actual_po_master">
			<thead>
				<tr style="font-weight:bold; text-align:center;">
					<td colspan="2">Master PO Qty: <?= $po_quantity; ?></td>
					<td colspan="2"></td>
					<td colspan="2">Balance Qty: <span id="balance_po"><?= $balance_po_qty; ?></span></td>
				</tr>
				<tr>
					<th width="180" class="must_entry_caption">Actual PO NO.</th>
					<th width="80" class="must_entry_caption">PO Recv Date</th>
					<th width="80" class="must_entry_caption">Ship Date</th>
					<th width="80">Rev Ship Date</th>
					<th width="140">Ship Mode</th>
					<th width="120">Status</th>
				</tr>
			</thead>
			<tbody>
					<tr class="general">
					<td align="center">
						<input type="text" id="actpoNo" name="actpoNo" class="text_boxes" style="width:180px" value="" />
						<input type="hidden" id="update_id" name="update_id" value="" />
						<input type="hidden" id="company_id" name="company_id" value="<?= $company_id ?>" />
						<input type="hidden" id="act_qty_variable" name="act_qty_variable" value="<?= $act_po_variable ?>" />
					</td>
					<td align="center"><input type="text" id="txt_po_rcv_date" name="porcvdate" class="datepicker" style="width:80px" onChange="ship_date_validation(1)" readonly/></td>
					<td align="center"><input type="text" id="txt_po_shipment_date" name="poshipdate" class="datepicker" style="width:80px" onChange="ship_date_validation(2)" readonly/></td>
					<td align="center"><input type="text" id="txt_rcv_ship_date" name="rcvshipdate" class="datepicker" style="width:80px" readonly/></td>
					<td align="center"><? echo create_drop_down( "cbo_ship_mode", 140,$shipment_mode, 1, "", $selected, "" ); ?></td>
					<td align="center"><?= create_drop_down( "actpostatus", 100, $row_status, 1, "", $selected,""); ?></td>
				</tr>
				<tr>
					<td colspan="6"><input type="text" id="txt_po_remarks" name="poremarks" class="text_boxes" placeholder="Remarks" style="width:700px" /></td>
				</tr>
			</tbody>            
		</table>
		<div style="font-size:16px; color:#36F">Actual Po Entry Details</div>
		<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" style="margin-top: 10px;">
			<thead>
				<th width="120" class="must_entry_caption">Country</th>
				<th width="150" class="must_entry_caption">Gmts. Item</th>
				<th width="150" class="must_entry_caption">Gmts. Color</th>
				<th width="80" class="must_entry_caption">Gmts. Size</th>
				<th width="80" class="must_entry_caption">PO Qty.</th>
				<th width="80" class="must_entry_caption">Unit Price</th>
				<th width="80">Amount</th>
				<th>&nbsp;</th>
			</thead>
			<tbody id="actual_po_details">
					<tr class="general" id="tr_1">
					<td>
						<?=create_drop_down( "cboCountryId_1", 120,"select a.id,a.country_name from lib_country a where a.status_active=1 and a.is_deleted=0 group by a.id, a.country_name order by a.country_name ASC", "id,country_name", 1, "-Country-", "","" ); ?>
					</td>
					<td align="center">
						<?=create_drop_down( "cboGmtsItemId_1", 150, $garments_item, 0, 1, "--Select Item--", $selected,"get_unit_price(this.value,1,2);get_gmts_size(this.value,1,1)",0,$gmts_item); ?>
					</td>
					<td align="center">
						<?=create_drop_down( "cbogmtscolor_1", 150, "select a.id, a.color_name, b.color_order from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.color_name, b.color_order order by b.color_order ASC", "id,color_name", 1, "-Select Color-", $selected,"get_unit_price(this.value,1,3); get_gmts_size(this.value,1,2)",0,""); ?>
					</td>
					<td align="center" id="gmtssize_1">
						<?=create_drop_down( "cbogmtssize_1", 80, "select a.id, a.size_name, b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.size_name, b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $selected,"get_unit_price(this.value,1,4);",0,""); ?>
					</td>
					<td align="center">
						<input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:80px" value="" onBlur='fnc_poqty_cal();' />
						<input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" value="" />
					</td>
					<td align="center">
						<input type="text" id="pounitprice_1" name="pounitprice_1" class="text_boxes_numeric" style="width:80px" value="" onBlur='fnc_poqty_cal();' />
					</td>
					<td align="center">
						<input type="text" id="povalue_1" name="povalue_1" class="text_boxes_numeric" style="width:80px" value="" readonly/>
					</td>
					<td>
						<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
						<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th><input type="text" id="txtTotPoQnty" name="txtTotPoQnty" class="text_boxes_numeric" style="width:80px" value="<?=$totpoqty; ?>" readonly/></th>
				<th>&nbsp;</th>
				<th><input type="text" id="txtTotPoValue" name="txtTotPoValue" class="text_boxes_numeric" style="width:80px" value="<?=$totpovalue; ?>" readonly/></th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
        <div align="center" style="margin-top:10px">
           <?
		   if(count($data_array)>0)
			{
				echo load_submit_buttons( $permission, "fnc_acc_po_info", 1,0 ,"reset_form('accpoinfo_1','','','','','hid_po_id*hid_job_id*txt_job_no*txt_po_qty*txt_po_balance_qty*fixed_balance_qty')",1) ; 
			}
			else
			{
				echo load_submit_buttons( $permission, "fnc_acc_po_info", 0,0 ,"reset_form('accpoinfo_1','','','','','hid_po_id*hid_job_id*txt_job_no*txt_po_qty*txt_po_balance_qty*fixed_balance_qty')",1) ; 
			}
		   ?>
            <input type="hidden" id="hid_po_id" value="<?=$po_id; ?>" />
            <input type="hidden" id="hid_po_rcv_date" value="<?=$rcv_date; ?>" />
            <input type="hidden" id="hid_job_id" value="<?=$job_id; ?>" />
            <input type="hidden" id="txt_job_no" value="<?=$txt_job_no; ?>" />
            <input type="hidden" id="txt_po_qty" value="<?=$po_quantity; ?>" />
            <input type="hidden" id="txt_po_balance_qty" value="<?=$balance_po_qty; ?>" />
            <input type="hidden" id="fixed_balance_qty" value="<?=$balance_po_qty; ?>" />
        </div>
    </form>
	</fieldset>
	<fieldset>
	<form id="pack_finish_2" autocomplete="off">
		<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="packing_finishing_info" style="margin-bottom:20px">
			<thead>
				<tr><th colspan="6">Packing & Finishing Info</th></tr>
				<tr>
					<th width="150" class="must_entry_caption">Gmts. Item</th>
					<th width="150" class="must_entry_caption">Gmts. Color</th>
					<th width="80" class="must_entry_caption">Gmts. Size</th>
					<th width="80" class="must_entry_caption">No. Of Carton</th>
					<th width="80" class="must_entry_caption">CBM</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody id="packing_finishing">
				<tr class="general" id="tr_1">
					<td align="center">
						<?=create_drop_down( "cbopackGmtsItemId_1", 150, $garments_item, 0, 1, "--Select Item--", $selected,"",0,$gmts_item); ?>
					</td>
					<td align="center">
						<?=create_drop_down( "cbopackgmtscolor_1", 150, "select a.id, a.color_name, b.color_order from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.color_name,b.color_order order by b.color_order ASC", "id,color_name", 1, "All Color", $selected,"",0,""); ?>
					</td>
					<td align="center">
						<?=create_drop_down( "cbopackgmtssize_1", 80, "select a.id, a.size_name, b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.size_name, b.size_order order by b.size_order ASC", "id,size_name", 1, "All Size", $selected,"",0,""); ?>
					</td>
					<td align="center">
						<input type="text" id="cartonQnty_1" name="cartonQnty_1" class="text_boxes_numeric" style="width:80px" value="" />
						<input type="hidden" id="packrowid_1" name="packrowid_1" class="text_boxes" value="" />
					</td>
					<td align="center">
						<input type="text" id="cbm_1" name="cbm_1" class="text_boxes_numeric" style="width:80px" value=""/>
					</td>
					<td>
						<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="pack_add_break_down_tr(1);" />
						<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="pack_fn_deletebreak_down_tr(1);" />
					</td>
				</tr>
			</tbody>
		</table>
		<div align="center" style="margin-top:10px">
           <?
			echo load_submit_buttons( $permission, "fnc_pack_finish_info", 0,0 ,"reset_form('pack_finish_2','','','','')",2) ;
		   ?>
        </div>
	</form>
	</fieldset>
    <div id="save_up_list_view"></div>
    </div>
    </body>
   
    <script>
	show_list_view( '<?=$po_id.'__'.$txt_job_no; ?>','accpo_list_view','save_up_list_view','order_entry_controller','');
	
	</script>       
     <script>
		var tableFilters_po = 
		{
			col_operation: { 
				id: ["total_po_qty"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		setFilterGrid("tbl_upListView",-1,tableFilters_po);		
		</script>  
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		navigate_arrow_key();
		setFieldLevelAccess(<?=$cbo_company_name;?>);

	</script>
    </html>
    <?
    exit();
}

if($action=="load_drop_down_gmtssize"){
	$data_arr = explode("_", $data);
	echo create_drop_down( "cbogmtssize_$data_arr[1]", 80, "select a.id, a.size_name, b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data_arr[3]' and b.item_number_id='$data_arr[0]' and b.color_number_id='$data_arr[2]' group by a.id, a.size_name, b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $selected,"get_unit_price(this.value,$data_arr[1],4)");
	exit();
}

if($action=="populate_act_pack_details"){
	$data=explode("_",$data);
	$i=1;
	$pack_finish_data=sql_select("SELECT id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm from wo_po_act_pack_finish_info where status_active=1 and is_deleted=0 and act_po_id=$data[0]");
	
	if(count($pack_finish_data)>0){
		foreach($pack_finish_data as $row){
	?>
	<tr class="general" id="tr_<?=$i?>">
		<td align="center">
			<?=create_drop_down( "cbopackGmtsItemId_".$i, 90, $garments_item, 0, 1, "--Select Item--", $row[csf('gmts_item')],"",0,$gmts_item); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtscolor_".$i, 90, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]'  group by a.id, a.color_name order by a.color_name ASC", "id,color_name", 1, "All Color", $row[csf('gmts_color_id')],"",0,""); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtssize_".$i, 80, "select a.id, a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' group by a.id, a.size_name order by a.size_name ASC", "id,size_name", 1, "All Size", $row[csf('gmts_size_id')],"",0,""); ?>
		</td>
		<td align="center">
			<input type="text" id="cartonQnty_<?=$i?>" name="cartonQnty_<?=$i?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('carton_qty')] ?>" />
			<input type="hidden" id="packrowid_<?=$i?>" name="packrowid_<?=$i?>" class="text_boxes" value="<?= $row[csf('id')] ?>" />
		</td>
		<td align="center">
			<input type="text" id="cbm_<?=$i?>" name="cbm_<?=$i?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('cbm')] ?>"/>
		</td>
		<td>
			<input type="button" id="increase_<?=$i?>" name="increase_<?=$i?>" style="width:30px" class="formbutton" value="+" onClick="pack_add_break_down_tr(<?=$i?>);" />
			<input type="button" id="decrease_<?=$i?>" name="decrease_<?=$i?>" style="width:30px" class="formbutton" value="-" onClick="pack_fn_deletebreak_down_tr(<?=$i?>);" />
		</td>
	</tr>
	<? 
	$i++;
	} 
	} else { ?>
	<tr class="general" id="tr_1">
		<td align="center">
			<?=create_drop_down( "cbopackGmtsItemId_1", 90, $garments_item, 0, 1, "--Select Item--", $selected,"",0,$gmts_item); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtscolor_1", 90, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.color_name order by a.color_name ASC", "id,color_name", 1, "All Color", $selected,"",0,""); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtssize_1", 80, "select a.id, a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.size_name order by a.size_name ASC", "id,size_name", 1, "All Size", $selected,"",0,""); ?>
		</td>
		<td align="center">
			<input type="text" id="cartonQnty_1" name="cartonQnty_1" class="text_boxes_numeric" style="width:80px" value="" />
			<input type="hidden" id="packrowid_1" name="packrowid_1" class="text_boxes" value="" />
		</td>
		<td align="center">
			<input type="text" id="cbm_1" name="cbm_1" class="text_boxes_numeric" style="width:80px" value=""/>
		</td>
		<td>
			<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="pack_add_break_down_tr(1);" />
			<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="pack_fn_deletebreak_down_tr(1);" />
		</td>
	</tr>
	<? }
	exit();
}

if($action=="show_acc_po_dtls"){
	$data=explode("_",$data);
	$acc_po_dtls=sql_select("SELECT b.id as dtls_id, b.country_id, b.gmts_item, b.gmts_color_id, b.gmts_size_id, b.po_qty, b.unit_price, b.unit_value from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.is_deleted=0 and b.is_deleted=0 and a.id=$data[0]");
	$gmts_item_arr=sql_select("SELECT item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id='$data[1]'");
	foreach($gmts_item_arr as $row){
		$gmts_item_data[$row[csf('item_number_id')]]=$row[csf('item_number_id')];
	}
	$gmts_item_id=implode(",",$gmts_item_data);
	$i=1;
	foreach($acc_po_dtls as $row){
		$gmts_color=$row[csf('gmts_color_id')];
		$gmts_item=$row[csf('gmts_item')];
	?>
		<tr class="general" id="tr_<?= $i?>">
			<td>
				<?=create_drop_down( "cboCountryId_".$i, 120,"select a.id, a.country_name from lib_country a where a.status_active=1 and a.is_deleted=0 group by a.id, a.country_name order by a.country_name ASC", "id,country_name", 1, "-Country-", $row[csf('country_id')],"" ); ?>
			</td>
			<td align="center">
				<?=create_drop_down( "cboGmtsItemId_".$i, 150, $garments_item, 0, 1, "--Select Item--", $row[csf('gmts_item')],"get_unit_price(this.value,$i,2);get_gmts_size(this.value,$i,1)",0,$gmts_item_id); ?>
			</td>
			<td align="center">
				<?=create_drop_down( "cbogmtscolor_".$i, 150, "select a.id, a.color_name, b.color_order from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' and b.job_id='$data[2]' group by a.id, a.color_name, b.color_order order by b.color_order ASC", "id,color_name", 1, "-Select Color-", $row[csf('gmts_color_id')],"get_unit_price(this.value,$i,3);get_gmts_size(this.value,$i,2)",0,""); ?>
			</td>
			<td align="center" id="gmtssize_<?= $i?>">
				<?=create_drop_down( "cbogmtssize_".$i, 80, "select a.id, a.size_name,b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' and b.job_id='$data[2]' and b.color_number_id=$gmts_color and b.item_number_id=$gmts_item group by a.id, a.size_name, b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $row[csf('gmts_size_id')],"get_unit_price(this.value,$i,4)",0,""); ?>
			</td>
			<td align="center">
				<input type="text" id="poQnty_<?= $i ?>" name="poQnty_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('po_qty')] ?>" onBlur="fnc_poqty_cal();" />
				<input type="hidden" id="rowid_<?= $i ?>" name="rowid_<?= $i ?>" class="text_boxes" value="<?= $row[csf('dtls_id')] ?>" />
			</td>
			<td align="center">
				<input type="text" id="pounitprice_<?= $i ?>" name="pounitprice_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('unit_price')] ?>" onBlur="fnc_poqty_cal();"/>
			</td>
			<td align="center">
				<input type="text" id="povalue_<?= $i ?>" name="povalue_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('unit_value')] ?>" readonly/>
			</td>
			<td>
				<input type="button" id="increase_<?= $i ?>" name="increase_<?= $i ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>);" />
				<input type="button" id="decrease_<?= $i ?>" name="decrease_<?= $i ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<?= $i ?>);" />
			</td>
		</tr>
	<?
	$i++;
	}
	exit();
}

if($action=="show_act_pack_dtls"){
	$data=explode("_",$data);
	$i=1;
	$pack_finish_data=sql_select("SELECT id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm from wo_po_act_pack_finish_info where is_deleted=0 and act_po_id=$data[0]");
	$gmts_item_arr=sql_select("SELECT item_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id='$data[1]'");
	foreach($gmts_item_arr as $row){
		$gmts_item_data[$row[csf('item_number_id')]]=$row[csf('item_number_id')];
	}
	$gmts_item=implode(",",$gmts_item_data);
	if(count($pack_finish_data)>0){
		foreach($pack_finish_data as $row){
	?>
	<tr class="general" id="tr_<?=$i?>">
		<td align="center">
			<?=create_drop_down( "cbopackGmtsItemId_".$i, 150, $garments_item, 0, 1, "--Select Item--", $row[csf('gmts_item')],"",0,$gmts_item); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtscolor_".$i, 150, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]'  group by a.id, a.color_name order by a.color_name ASC", "id,color_name", 1, "All Color", $row[csf('gmts_color_id')],"",0,""); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtssize_".$i, 80, "select a.id, a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' group by a.id, a.size_name order by a.size_name ASC", "id,size_name", 1, "All Size", $row[csf('gmts_size_id')],"",0,""); ?>
		</td>
		<td align="center">
			<input type="text" id="cartonQnty_<?=$i?>" name="cartonQnty_<?=$i?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('carton_qty')] ?>" />
			<input type="hidden" id="packrowid_<?=$i?>" name="packrowid_<?=$i?>" class="text_boxes" value="<?= $row[csf('id')] ?>" />
		</td>
		<td align="center">
			<input type="text" id="cbm_<?=$i?>" name="cbm_<?=$i?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('cbm')] ?>"/>
		</td>
		<td>
			<input type="button" id="increase_<?=$i?>" name="increase_<?=$i?>" style="width:30px" class="formbutton" value="+" onClick="pack_add_break_down_tr(<?=$i?>);" />
			<input type="button" id="decrease_<?=$i?>" name="decrease_<?=$i?>" style="width:30px" class="formbutton" value="-" onClick="pack_fn_deletebreak_down_tr(<?=$i?>);" />
		</td>
	</tr>
	<? 
	$i++;
	} 
	} else { ?>
	<tr class="general" id="tr_1">
		<td align="center">
			<?=create_drop_down( "cbopackGmtsItemId_1", 150, $garments_item, 0, 1, "--Select Item--", $selected,"",0,$gmts_item); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtscolor_1", 150, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' and b.job_id='$data[2]' group by a.id, a.color_name order by a.color_name ASC", "id,color_name", 1, "All Color", $selected,"",0,""); ?>
		</td>
		<td align="center">
			<?=create_drop_down( "cbopackgmtssize_1", 80, "select a.id, a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data[1]' and b.job_id='$data[2]' group by a.id, a.size_name order by a.size_name ASC", "id,size_name", 1, "All Size", $selected,"",0,""); ?>
		</td>
		<td align="center">
			<input type="text" id="cartonQnty_1" name="cartonQnty_1" class="text_boxes_numeric" style="width:80px" value="" />
			<input type="hidden" id="packrowid_1" name="packrowid_1" class="text_boxes" value="" />
		</td>
		<td align="center">
			<input type="text" id="cbm_1" name="cbm_1" class="text_boxes_numeric" style="width:80px" value=""/>
		</td>
		<td>
			<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="pack_add_break_down_tr(1);" />
			<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="pack_fn_deletebreak_down_tr(1);" />
		</td>
	</tr>
	<? }
	exit();
}

if($action=="save_update_delete_accpoinfo")// zakaria joy 28-05-22(8883)
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	if ($operation==0)  // Insert Here
	{
		$con = connect();		
		//if(check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}	
		if (is_duplicate_field( "acc_po_no", "wo_po_acc_po_info", "acc_po_no=$actpoNo and acc_ship_date=$txt_po_shipment_date and po_break_down_id=$hid_po_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";			
		$mst_id=return_next_id( "id", "wo_po_acc_po_info", 1);
		$id=return_next_id( "id", "wo_po_acc_po_info_dtls", 1);
		//echo "10**".$mst_id."==".$id; die;
		$field_array="id, job_no, job_id, po_break_down_id, acc_po_no, acc_rcv_date, acc_ship_date, acc_revise_ship_date, acc_ship_mode, acc_po_qty, acc_po_value, acc_po_status, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$mst_id.",".$txt_job_no.",".$hid_job_id.",".$hid_po_id.",".$actpoNo.",".$txt_po_rcv_date.",".$txt_po_shipment_date.",".$txt_rcv_ship_date.",".$cbo_ship_mode.",".$txtTotPoQnty.",".$txtTotPoValue.",".$actpostatus.",".$txt_po_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$actpostatus.",'0')";

		$field_array_dtls="id, mst_id, po_break_down_id, country_id, gmts_item, gmts_color_id, gmts_size_id, po_qty, unit_price, unit_value, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1; $i<=$total_row; $i++)
		{
			$cboCountryId="cboCountryId_".$i;
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$poQnty="poQnty_".$i;
			$unitprice="pounitprice_".$i;
			$povalue="povalue_".$i;
			$rowid="rowid_".$i;
		
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls .="(".$id.",".$mst_id.",".$hid_po_id.",".$$cboCountryId.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$poQnty.",".$$unitprice.",".$$povalue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$actpostatus.",0)";
			$id=$id+1;
		}
		$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,0);
		$rID1=sql_insert("wo_po_acc_po_info_dtls",$field_array_dtls,$data_array_dtls,0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if (is_duplicate_field( "acc_po_no", "wo_po_acc_po_info", "acc_po_no=$actpoNo and acc_ship_date=$txt_po_shipment_date and po_break_down_id=$hid_po_id and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$add_comma=0;
		$id=return_next_id( "id", "wo_po_acc_po_info_dtls", 1 ) ;
		$field_array_mst="acc_po_no*acc_rcv_date*acc_ship_date*acc_revise_ship_date*acc_ship_mode*acc_po_qty*acc_po_value*acc_po_status*remarks* updated_by*update_date*status_active";

		$data_array_mst="".$actpoNo."*".$txt_po_rcv_date."*".$txt_po_shipment_date."*".$txt_rcv_ship_date."*".$cbo_ship_mode."*".$txtTotPoQnty."*".$txtTotPoValue."*".$actpostatus."*".$txt_po_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$actpostatus."";

		$rID2=sql_update("wo_po_acc_po_info",$field_array_mst,$data_array_mst,"id",$update_id,1);
		//echo "10**".$rID2; die;

		$field_array_dtls="id, mst_id, po_break_down_id, country_id, gmts_item, gmts_color_id, gmts_size_id, po_qty, unit_price, unit_value, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="country_id*gmts_item*gmts_color_id*gmts_size_id*po_qty*unit_price*unit_value*updated_by*update_date*status_active";
		//echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboCountryId="cboCountryId_".$i;
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$poQnty="poQnty_".$i;
			$unitprice="pounitprice_".$i;
			$povalue="povalue_".$i;
			$rowid="rowid_".$i;
			
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$cboCountryId."*".$$cboGmtsItemId."*".$$cbogmtscolor."*".$$cbogmtssize."*".$$poQnty."*".$$unitprice."*".$$povalue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$actpostatus.""));
			}
			if(str_replace("'",'',$$rowid)=="")
			{
				if($add_comma!=0) $data_array_dtls .=",";
				$data_array_dtls.="(".$id.",".$update_id.",".$hid_po_id.",".$$cboCountryId.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$poQnty.",".$$unitprice.",".$$povalue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$actpostatus.",0)";
				$add_comma++;
				$id=$id+1;
			}
		}
		$flag=1;
		//echo "10**".bulk_update_sql_statement( "wo_po_acc_po_info_dtls", "id", $field_array_up, $data_array_up, $id_arr ); die;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		$rID1=execute_query("UPDATE wo_po_act_pack_finish_info set status_active=$actpostatus where is_deleted=0 and act_po_id=$update_id");
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls!="")
		{
			$rID1=sql_insert("wo_po_acc_po_info_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;	
		//echo "10**".$rID."**".$rID1."**".$rID2; die;
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)  //Delete Here
	{
		$con = connect();
		$rID=execute_query("update wo_po_acc_po_info set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$update_id");
		$rID1=execute_query("update wo_po_acc_po_info_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where mst_id=$update_id");
		if($db_type==2 || $db_type==1 )
		{			
			if($rID && $rID1 ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_actpack")// zakaria joy 28-05-22(8883)
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	if ($operation==0)  // Insert Here
	{
		$con = connect();		
		//if(check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_po_act_pack_finish_info", 1);
		$field_array_dtls="id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm, act_po_id, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1; $i<=$total_row; $i++)
		{
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$cartonQnty="cartonQnty_".$i;
			$cbm="cbm_".$i;
			$rowid="packrowid_".$i;
		
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls .="(".$id.",".$hid_po_id.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$cartonQnty.",".$$cbm.",".$update_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		//echo "10**insert into wo_po_act_pack_finish_info ($field_array_dtls) values $data_array_dtls"; die;
		$rID=sql_insert("wo_po_act_pack_finish_info",$field_array_dtls,$data_array_dtls,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();		
		//if(check_table_status( $_SESSION['menu_id'], 1 )==0) { echo "15**0"; disconnect($con); die;}

		$add_comma=0;
		$id=return_next_id( "id", "wo_po_act_pack_finish_info", 1);
		$field_array_dtls="id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm, act_po_id, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="gmts_item*gmts_color_id*gmts_size_id*carton_qty*cbm*act_po_id*updated_by*update_date";

		for ($i=1;$i<=$total_row;$i++)
		{
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$cartonQnty="cartonQnty_".$i;
			$cbm="cbm_".$i;
			$rowid="packrowid_".$i;
			
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$cboGmtsItemId."*".$$cbogmtscolor."*".$$cbogmtssize."*".$$cartonQnty."*".$$cbm."*".$update_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			if(str_replace("'",'',$$rowid)=="")
			{
				if($add_comma!=0) $data_array_dtls .=",";
				$data_array_dtls.="(".$id.",".$hid_po_id.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$cartonQnty.",".$$cbm.",".$update_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$add_comma++;
				$id=$id+1;
			}
		}
		$flag=1;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_act_pack_finish_info", "id", $field_array_up, $data_array_up, $id_arr ));
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls!="")
		{
			$rID1=sql_insert("wo_po_act_pack_finish_info",$field_array_dtls,$data_array_dtls,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)  //Delete Here
	{
		$con = connect();
		$rID=execute_query("update wo_po_act_pack_finish_info set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where po_break_down_id=$hid_po_id");
		if($db_type==2 || $db_type==1 )
		{			
			if($rID){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="accpo_list_view")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$exdata=explode("__",$data);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	?>
     <fieldset>
    <div style="width:700px;" align="center">
    <legend>Actual PO Info List View</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="160">PO NO</th>
                <th width="80">PO Recv Date</th>
                <th width="80">Ship Date</th>
                <th width="80">Rev Ship Date</th>
                <th width="50">Ship Mode</th>
                <th width="60">PO Qty</th>
                <th width="60">PO Value</th>
                <th>Status</th>
            </thead>
     	</table>
        <div style="width:718px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_upListView" >
            
            <?
				$sql="select id, acc_po_no, acc_rcv_date, acc_ship_date, acc_revise_ship_date, acc_ship_mode, acc_po_status, acc_po_qty, acc_po_value from wo_po_acc_po_info where po_break_down_id='$exdata[0]' and job_no='$exdata[1]' and is_deleted=0";
				$sql_res=sql_select($sql);
				
				//print_r($mst_temp_arr);
				$i=1; $tot_qty=0;
				foreach($sql_res as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<?=$row[csf('id')]; ?>');">
                    	<td width="30" align="center"><?=$i; ?></td>
                        <td width="160"><?=$row[csf('acc_po_no')]; ?></td>
                        <td width="80"><?= change_date_format($row[csf('acc_rcv_date')]); ?></td>
                        <td width="80"><?= change_date_format($row[csf('acc_ship_date')]); ?></td>
                        <td width="80"><?= change_date_format($row[csf('acc_revise_ship_date')]); ?></td>
                        <td width="50"><?= $shipment_mode[$row[csf('acc_ship_mode')]]; ?></td>
                        <td width="60" align="right"><?=$row[csf('acc_po_qty')]; ?></td>
                        <td width="60" align="right"><?=$row[csf('acc_po_value')]; ?></td>
                        <td ><?= $row_status[$row[csf('acc_po_status')]]; ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qty+=$row[csf('acc_po_qty')];
					$tot_value+=$row[csf('acc_po_value')];
				}
			?>
           
            </table>
        </div>
        <table width="700" class="tbl_bottom"  border="1" rules="all">
			 
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="160">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="50">&nbsp;</td>
          			<td width="60" id="total_po_qty" align="right"><strong><? echo number_format($tot_qty,0);?> </strong></td>
          			<td width="60" id="total_po_value" align="right"><strong><? echo number_format($tot_value,0);?> </strong></td>
            		<td align="">&nbsp; </td>
					</tr>
				 
			</table>
            
       
        
     </div>
     </fieldset>
    <?
	exit();
}

if($action=="acc_po_balance_qty"){
	$data=explode("_",$data);
	$current_acc_po_dtls=sql_select("SELECT sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.acc_po_status=1 and a.po_break_down_id=$data[0]");
	
	foreach($current_acc_po_dtls as $row){
		$currrent_po_qty=$row[csf('po_qty')];
	}
	$balance_po_qty=$data[1]-$currrent_po_qty;
	echo "$('#fixed_balance_qty').val('".$balance_po_qty."');\n";
	echo "$('#txt_po_balance_qty').val('".$balance_po_qty."');\n";
	echo "$('#balance_po').html('".$balance_po_qty."');\n";
	exit();
}

if($action=="populate_acc_details_data")
{
	$data=explode("_",$data);

	$data_array=sql_select("select id,job_id, po_break_down_id, acc_po_no, acc_rcv_date, acc_ship_date, acc_revise_ship_date, acc_ship_mode, acc_po_status, acc_po_qty, acc_po_value,remarks from wo_po_acc_po_info where id='$data[0]' and is_deleted=0");
	foreach($data_array as $row)
	{
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "$('#actpoNo').val('".$row[csf("acc_po_no")]."');\n";
		echo "$('#txt_po_rcv_date').val('".change_date_format($row[csf("acc_rcv_date")])."');\n";
		echo "$('#txt_po_shipment_date').val('".change_date_format($row[csf("acc_ship_date")])."');\n";
		echo "$('#txt_rcv_ship_date').val('".change_date_format($row[csf("acc_revise_ship_date")])."');\n";
		echo "$('#cbo_ship_mode').val('".$row[csf("acc_ship_mode")]."');\n";
		echo "$('#actpostatus').val('".$row[csf("acc_po_status")]."');\n";
		echo "$('#txtTotPoQnty').val('".$row[csf("acc_po_qty")]."');\n";
		echo "$('#txtTotPoValue').val('".$row[csf("acc_po_value")]."');\n";
		echo "$('#txt_po_remarks').val('".$row[csf("remarks")]."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_acc_po_info',1);\n";
		$po_break_down_id=$row[csf('po_break_down_id')];
		$job_id=$row[csf('job_id')];
	}
	$current_acc_po_dtls2=sql_select("SELECT sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.acc_po_status=1 and a.po_break_down_id=$po_break_down_id and a.job_id=$job_id and b.mst_id!='$data[0]'");
	foreach($current_acc_po_dtls2 as $row){
		$without_currrent_po_qty=$row[csf('po_qty')];
	}
	$curr_balance_po_qty=$data[1]-$without_currrent_po_qty;
	echo "$('#fixed_balance_qty').val('".$curr_balance_po_qty."');\n";

	$current_acc_po_dtls=sql_select("SELECT sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.is_deleted=0 and b.is_deleted=0 and a.po_break_down_id=$po_break_down_id and a.job_id=$job_id");
	
	foreach($current_acc_po_dtls as $row){
		$currrent_po_qty=$row[csf('po_qty')];
	}
	$balance_po_qty=$data[1]-$currrent_po_qty;
	echo "$('#txt_po_balance_qty').val('".$balance_po_qty."');\n";
	echo "$('#balance_po').html('".$balance_po_qty."');\n";

	$pack_finish_data=sql_select("SELECT id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm from wo_po_act_pack_finish_info where is_deleted=0 and act_po_id=$data[0]");
	if(count($pack_finish_data)>0){
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pack_finish_info',2);\n";
	}
	exit();
}

if($action=="delete_row")
{
	$con = connect();
	$data=explode("_",$data);
	$rID=execute_query("update wo_po_acc_po_info_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data[0]");

	$current_acc_po_dtls=sql_select("SELECT b.po_qty, b.unit_price from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.acc_po_status=1 and a.id=$data[1]");
	foreach($current_acc_po_dtls as $row){
		$currrent_po_qty+=$row[csf('po_qty')];
		$currrent_po_value+=$row[csf('unit_price')]*$row[csf('po_qty')];
	}
	$rID1=execute_query("update wo_po_acc_po_info set acc_po_qty=$currrent_po_qty, acc_po_value=$currrent_po_value, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data[1]");
	if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID1){
		oci_commit($con);
		echo "2**".$currrent_po_qty;
		}
		else{
		oci_rollback($con);
		echo "10**".$currrent_po_qty;
		}
	}
	disconnect($con);
	die;
}

if($action=="pack_delete_row")
{
	$con = connect();
	$rID=execute_query("update wo_po_act_pack_finish_info set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data");
	if($db_type==2 || $db_type==1 )
	{
		if($rID){
		oci_commit($con);
		echo "2**".$currrent_po_qty;
		}
		else{
		oci_rollback($con);
		echo "10**".$currrent_po_qty;
		}
	}
	disconnect($con);
	die;
}

if($action=="reorder_size_color")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';

	function fnc_size_color_reorder(operation)
	{
		var row_num_color=$('#color_order tbody tr').length;
		var data_all_color="";
		for (var i=1; i<=row_num_color; i++)
		{
			if (form_validation('colorordering_'+i+'*change_color_'+i,'Color Ordering*Change Color')==false)
			{
				return;
			}
			data_all_color=data_all_color+get_submitted_data_string('txt_job_no*colorid_'+i+'*colorordering_'+i+'*change_color_'+i,"../../../",i);
		}

		var row_num_size=$('#size_order tbody tr').length;
		var data_all_size="";
		for (var i=1; i<=row_num_size; i++)
		{
			if (form_validation('sizeordering_'+i+'*change_size_'+i,'Size Ordering*Change Size')==false)
			{
				return;
			}

			data_all_size=data_all_size+get_submitted_data_string('txt_job_no*sizeid_'+i+'*sizeordering_'+i+'*change_size_'+i,"../../../",i);
		}

		var data="action=save_update_color_size_ordering&operation="+operation+'&total_row_color='+row_num_color+data_all_color+'&total_row_size='+row_num_size+data_all_size;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_color_reorder_reponse;
	}

	function fnc_size_color_reorder_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			release_freezing();
			if(reponse[0]==121)
			{
				alert(reponse[1]);
			}
		}
	}
	</script>
	</head>
	<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
        <input type="hidden" id="garments_nature" value="2">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
        <fieldset style="width:650px;">
            <form id="colorsizeorder_1">
            <input type="hidden" class="text_boxes_numeric" id="txt_job_no" value="<? echo $txt_job_no; ?>" style="widows:60px"/>
                <table width="650">
                    <tr>
                        <td valign="top">
                        	<table width="400" id="color_order" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="150">Color</th>
                                        <th width="150">Change Color</th>
                                        <th>Color Ordering</th>
                                    </tr>
                            	</thead>
                    			<tbody>
								<?
								
								$sqlpo=sql_select("select ID, PO_NUMBER from WO_PO_BREAK_DOWN where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0");
								$poidArr=array();
								foreach($sqlpo as $row)
								{
									$poidArr[$row["ID"]]=$row["ID"];
								}
								unset($sqlpo);
								
								$is_produced=0; $prodColorArr=array(); $prodSizeArr=array();
								$sql_check=sql_select("select A.COLOR_ID, A.SIZE_ID, sum(A.MARKER_QTY) AS MARKER_QNTY from ppl_cut_lay_size a, ppl_cut_lay_dtls b
						where b.id=a.dtls_id and a.order_id in (".implode(",",$poidArr).") and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.color_id,a.size_id");
								foreach($sql_check as $dts)
								{
									$prodColorArr[$dts["COLOR_ID"]]=$dts["MARKER_QNTY"];
									$prodSizeArr[$dts["SIZE_ID"]]=$dts["MARKER_QNTY"];
									$is_produced=1;
								}
								unset($sql_check);
						
								if($is_produced==0)
								{
									$sql_check=sql_select("select A.COLOR_NUMBER_ID, A.SIZE_NUMBER_ID, B.PRODUCTION_TYPE, sum(B.PRODUCTION_QNTY) AS MARKER_QNTY from wo_po_color_size_breakdown a, pro_garments_production_dtls b where b.color_size_break_down_id=a.id and a.po_break_down_id in (".implode(",",$poidArr).") and b.status_active=1 and b.is_deleted=0 group by a.color_number_id, a.size_number_id, b.production_type order by b.production_type DESC");
									foreach($sql_check as $dts)
									{
										$prodColorArr[$dts["COLOR_NUMBER_ID"]]=$dts["MARKER_QNTY"];
										$prodSizeArr[$dts["SIZE_NUMBER_ID"]]=$dts["MARKER_QNTY"];
										$is_produced=1;
									}
									unset($sql_check);
								}
								
								$disabled="";
								if(!empty($pre_cost))
								{
									$disabled="disabled";
								}
								$sql_data=sql_select("select min(ID) AS ID, COLOR_NUMBER_ID, min(COLOR_ORDER) AS COLOR_ORDER from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by color_number_id order by color_order");
								
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$colorChangeField="";
									if($prodColorArr[$sql_row["COLOR_NUMBER_ID"]]=="" || $prodColorArr[$sql_row["COLOR_NUMBER_ID"]]==0) $colorChangeField=""; else $colorChangeField="disabled readonly";
									?>
									<tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                        <td align="center"><?=$i; ?></td>
                                        <td style="word-break:break-all"><?=$color_library[$sql_row["COLOR_NUMBER_ID"]]; ?><input type="hidden" class="text_boxes_numeric" id="colorid_<?=$i; ?>" value="<?=$sql_row["COLOR_NUMBER_ID"]; ?>" /></td>
                                         <td><input type="text" class="text_boxes" id="change_color_<?=$i; ?>" style="width:135px" value="<?=$color_library[$sql_row["COLOR_NUMBER_ID"]]; ?>" <?=$disabled; ?> <?=$colorChangeField; ?> /> </td>
                                        <td><input type="text" class="text_boxes_numeric" id="colorordering_<?=$i; ?>" style="width:60px" value="<?=$sql_row["COLOR_ORDER"];  ?>"/></td>
									</tr>
									<?
									$i++;
                                }
                                ?>
                                </tbody>
                			</table>
                		</td>
               			<td valign="top">
                			<table width="250" id="size_order" class="rpt_table" border="1" rules="all">
                				<thead>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="80">Size</th>
                                        <th width="90">Change Size</th>
                                        <th>Size Ordering</th>
                                    </tr>
                                </thead>
                                <tbody>
								<? $sql_data=sql_select("select min(ID) AS ID, SIZE_NUMBER_ID, min(SIZE_ORDER) AS SIZE_ORDER from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by size_number_id order by size_order ");
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$sizeChangeField="";
									if($prodSizeArr[$sql_row["SIZE_NUMBER_ID"]]=="" || $prodSizeArr[$sql_row["SIZE_NUMBER_ID"]]==0) $sizeChangeField=""; else $sizeChangeField="disabled readonly";
									?>
									<tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer">
                                        <td align="center"><?=$i; ?></td>
                                      	<td style="word-break:break-all"><?=$size_library[$sql_row["SIZE_NUMBER_ID"]]; ?><input type="hidden" class="text_boxes_numeric" id="sizeid_<?=$i; ?>" value="<?=$sql_row["SIZE_NUMBER_ID"]; ?>" style="width: :60px" /></td>
                                        <td><input type="text" class="text_boxes" id="change_size_<?=$i; ?>" style="width:80px" value="<?=$size_library[$sql_row["SIZE_NUMBER_ID"]]; ?>" <?=$disabled; ?> <?=$sizeChangeField; ?> /></td>
                                        <td><input type="text" class="text_boxes_numeric" id="sizeordering_<?=$i; ?>" style="width:60px" value="<?=$sql_row["SIZE_ORDER"];  ?>"/></td>
									</tr>
									<?
									$i++;
                                }
                                ?>
                                </tbody>
               				</table>
                		</td>
                	</tr>
                	<tr>
                        <td align="center" colspan="8" class="button_container">
                        	<?=load_submit_buttons( $permission, "fnc_size_color_reorder", 1,0 ,"",1); ?>
                        </td>
                	</tr>
                </table>
            </form>
        </fieldset>
    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_color_size_ordering")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
		
	$new_array_color_ordering=array();
	$new_array_size_ordering=array();
	$total_row_color=str_replace("'", "", $total_row_color);
	$total_row_size=str_replace("'", "", $total_row_size);
	$pre_cost=return_field_value("id","wo_pre_cost_mst","status_active=1 and is_deleted=0 and job_no=$txt_job_no and approved in (1,3)","id");
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$flag=1;
		 for ($i=1;$i<=$total_row_color;$i++)
		 {
			 $colorid="colorid_".$i;
			 $colorordering="colorordering_".$i;
			 $change_color="change_color_".$i;

			 if(str_replace("'","",$$change_color)!="")
			 {
				if (!in_array(str_replace("'","",$$change_color),$new_array_color_ordering))
				{
					$color_id = return_id( str_replace("'","",$$change_color), $color_library, "lib_color", "id,color_name","401");
					$new_array_color_ordering[$color_id]=str_replace("'","",$$change_color);
				}
				else $color_id =  array_search(str_replace("'","",$$change_color), $new_array_color_ordering);
			}
			else $color_id=0;
			
			
			$rID=execute_query( "update wo_po_color_size_breakdown set color_order=".$$colorordering." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
			if(!$rID) {
				$flag=0;
				break;
			}
			$rID1=true;
			if($color_id!=str_replace("'", "", $$colorid) && !empty($color_id))
			{
				if(!empty($pre_cost))
				{
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$colorid);
						die;
					}
					else if($db_type==2 || $db_type==1 )
					{
						oci_rollback($con);
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$colorid);
						die;
					}
	
				}
				$rID1=execute_query( "update wo_po_color_size_breakdown set COLOR_NUMBER_ID=".$color_id.", COLOR_NUMBER_ID_PREV=".$$colorid." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
				if(!$rID1) {
					$flag=0;
					break;
				}
			}
		 }

		 for ($i=1;$i<=$total_row_size;$i++)
		 {
			 $sizeid="sizeid_".$i;
			 $sizeordering="sizeordering_".$i;
			 $change_size="change_size_".$i;
			 $rID2=execute_query( "update wo_po_color_size_breakdown set  size_order=".$$sizeordering."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
			 if(!$rID2) {
					$flag=0;
					break;
			 }

			if(str_replace("'","",$$change_size)!="")
			{
				if (!in_array(str_replace("'","",$$change_size),$new_array_size_ordering, TRUE))
				{
					$size_id_val = return_id( str_replace("'","",$$change_size), $size_library, "lib_size", "id,size_name","401");
					$new_array_size_ordering[$size_id_val]=str_replace("'","",$$change_size);
				}
				else $size_id_val =  array_search(str_replace("'","",$$change_size), $new_array_size_ordering);
			}
			else $size_id_val=0;
			$rID3=true;
			if($size_id_val!=str_replace("'", "", $$sizeid) && !empty($size_id_val))
			{
				if(!empty($pre_cost))
				{
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$sizeid);
						die;
					}
					else if($db_type==2 || $db_type==1 )
					{
						oci_rollback($con);
						disconnect($con);
						echo "121**Costing found against the job so color and size change not allowed.**$color_id**".str_replace("'", "", $$sizeid);
						die;
					}
				}
				$rID3=execute_query( "update wo_po_color_size_breakdown set  size_number_id=".$size_id_val.",size_number_id_prev=".$$sizeid."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
				if(!$rID3) {
					$flag=0;
					break;
			 	}
			}
		 }
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $flag)
			{
				mysql_query("COMMIT");
				echo "0**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3 && $flag)
			{
				oci_commit($con);
				echo "0**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$size_id_val."**".$color_id."**".$rID ."**". $rID1 ."**". $rID2."**". $rID3 ."**". $flag;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="reorder_size_color_backup")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';

	function fnc_size_color_reorder(operation)
	{
		var row_num_color=$('#color_order tbody tr').length;
		var data_all_color="";
		for (var i=1; i<=row_num_color; i++)
		{
			if (form_validation('colorordering_'+i,'Color Ordering')==false)
			{
				return;
			}
			data_all_color=data_all_color+get_submitted_data_string('txt_job_no*colorid_'+i+'*colorordering_'+i,"../../../",i);
		}

		var row_num_size=$('#size_order tbody tr').length;
		var data_all_size="";
		for (var i=1; i<=row_num_size; i++)
		{
			if (form_validation('sizeordering_'+i,'Size Ordering')==false)
			{
				return;
			}
			data_all_size=data_all_size+get_submitted_data_string('txt_job_no*sizeid_'+i+'*sizeordering_'+i,"../../../",i);
		}

		var data="action=save_update_color_size_ordering&operation="+operation+'&total_row_color='+row_num_color+data_all_color+'&total_row_size='+row_num_size+data_all_size;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_color_reorder_reponse;
	}

	function fnc_size_color_reorder_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			release_freezing();
		}
	}
	</script>
	</head>
	<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
        <input type="hidden" id="garments_nature" value="2">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
        <fieldset style="width:500px;">
            <form id="colorsizeorder_1">
            <input type="hidden" class="text_boxes_numeric" id="txt_job_no" value="<? echo $txt_job_no; ?>" style="widows:60px"/>
                <table>
                    <tr>
                        <td valign="top">
                        	<table id="color_order" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="150">Color</th>
                                        <th width="">Color Ordering</th>
                                    </tr>
                            	</thead>
                    			<tbody>
								<? $sql_data=sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by color_number_id order by color_order");
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									?>
									<tr>
                                        <td><? echo $i; ?></td>
                                        <td><? echo $color_library[$sql_row[csf('color_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="colorid_<? echo $i; ?>" value="<? echo $sql_row[csf('color_number_id')]; ?>" style="widows:60px"/></td>
                                        <td><input type="text" class="text_boxes_numeric" id="colorordering_<? echo $i; ?>" style="widows:60px" value="<? echo $sql_row[csf('color_order')];  ?>"/></td>
									</tr>
									<?
									$i++;
                                }
                                ?>
                                </tbody>
                			</table>
                		</td>
               			<td valign="top">
                			<table id="size_order" class="rpt_table" border="1" rules="all">
                				<thead>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="150">Size</th>
                                        <th width="">Size Ordering</th>
                                    </tr>
                                </thead>
                                <tbody>
								<? $sql_data=sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active!=0 and is_deleted=0 group by size_number_id order by size_order ");
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									?>
									<tr>
                                        <td><? echo $i; ?></td>
                                        <td><? echo $size_library[$sql_row[csf('size_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="sizeid_<? echo $i; ?>" value="<? echo $sql_row[csf('size_number_id')]; ?>" style="widows:60px"/></td>
                                        <td><input type="text" class="text_boxes_numeric" id="sizeordering_<? echo $i; ?>" style="widows:60px" value="<? echo $sql_row[csf('size_order')];  ?>"/></td>
									</tr>
									<?
									$i++;
                                }
                                ?>
                                </tbody>
               				</table>
                		</td>
                	</tr>
                	<tr>
                        <td align="center" colspan="7"  class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_size_color_reorder", 1,0 ,"",1); ?>
                        </td>
                	</tr>
                </table>
            </form>
        </fieldset>
    </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_color_size_orderingbackup")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 for ($i=1;$i<=$total_row_color;$i++)
		 {
			 $colorid="colorid_".$i;
			 $colorordering="colorordering_".$i;
			 $rID=execute_query( "update wo_po_color_size_breakdown set color_order=".$$colorordering." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
		 }

		 for ($i=1;$i<=$total_row_size;$i++)
		 {
			 $sizeid="sizeid_".$i;
			 $sizeordering="sizeordering_".$i;
			 $rID=execute_query( "update wo_po_color_size_breakdown set  size_order=".$$sizeordering."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
		 }
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

function update_color_size_sequence2($txt_job_no,$btn_mood)
{
	$colororder_by="";
	$sizeorder_by="";
	if($btn_mood==1)
	{
		$colororder_by="order by color_order, id ";
		$sizeorder_by="order by size_order, id";
	}
	else if($btn_mood==2)
	{
		$colororder_by="order by color_order";
		$sizeorder_by="order by size_order";
	}
	
	//$sqlData="select id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by";
	
	$sql_data=sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by");
	$color_order=1;
	foreach ($sql_data as $row)
	{
		$rID=execute_query("update wo_po_color_size_breakdown set color_order=".$color_order." where color_number_id=".$row[csf('color_number_id')]." and job_no_mst=$txt_job_no",0);
		$color_order++;
	}
	unset($sql_data);

	$sql_size=sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id $sizeorder_by");
	$size_order=1;
	foreach ($sql_size as $rows)
	{
		$rID=execute_query( "update wo_po_color_size_breakdown set size_order=".$size_order." where size_number_id=".$rows[csf('size_number_id')]." and job_no_mst=$txt_job_no",0);
		$size_order++;
	}
}

function job_order_qty_update($job_no,$po_id,$set_data,$breakdown_type,$order_status)
{
	$po_data_arr=array(); $job_data_arr=array(); $item_set_arr=array(); $item_ratio=0;
	//print_r($set_data);
	foreach($set_data as $exSet)
	{
		$exItemRatio=explode('_',$exSet);
		//$item_ratio_arr[$exItemRatio[0]]=$exItemRatio[1];
		$item_ratio+=$exItemRatio[1];
	}
	//$data_array_se=sql_select("select po_break_down_id, status_active, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active!=0 group by po_break_down_id, status_active");
	$data_array_se=sql_select("SELECT b.po_break_down_id, b.status_active, sum(b.order_quantity) as po_tot, sum(b.order_total) as po_tot_price, sum(b.plan_cut_qnty) as plan_cut from wo_po_break_down a join wo_po_color_size_breakdown b on a.id=b.po_break_down_id where b.job_no_mst=$job_no and b.is_deleted=0 and b.status_active!=0 and a.is_deleted=0 and a.status_active=1 group by b.po_break_down_id, b.status_active");
	foreach($data_array_se as $row)
	{
		//$item_ratio=0;
		$item_qty=0; $item_amt=0; $item_planCut=0;
		//$item_ratio=$item_ratio_arr[$row[csf('item_number_id')]];
		$item_qty=$row[csf('po_tot')]/$item_ratio;
		$item_amt=$row[csf('po_tot_price')];//*$item_ratio;
		$item_planCut=$row[csf('plan_cut')]/$item_ratio;
		$po_data_arr[$row[csf('po_break_down_id')]]['qty']+=$item_qty;
		$po_data_arr[$row[csf('po_break_down_id')]]['amt']+=$item_amt;
		$po_data_arr[$row[csf('po_break_down_id')]]['plan']+=$item_planCut;
		$job_item_qty=0; $job_item_amt=0;
		if($row[csf('status_active')]==1)
		{
			$job_item_qty=$row[csf('po_tot')]/$item_ratio;
			$job_item_amt=$row[csf('po_tot_price')];
		}
		$job_data_arr['qty']+=$job_item_qty;
		$job_data_arr['amt']+=$job_item_amt;
	}
	//echo $item_ratio; die;
	//list($po_data)=$data_array_se;
	$set_qnty=str_replace("'","",$set_qnty);
	$job_qty=0; $job_amt=0; $poavgprice=0;
	$job_qty=$job_data_arr['qty'];
	$job_amt=$job_data_arr['amt'];
	if($job_amt>0)
	{
 	$poavgprice=number_format($job_amt/$job_qty,4,'.','');
	}
	else $poavgprice=0;
	
	if($job_qty==0) $job_qty=0;
	if($job_amt==0) $job_amt=0;
	if($poavgprice==0) $poavgprice=0;
	//echo $job_qty_set.'='.$job_amt_set.'='.$job_price; die;
	$field_array_job="job_quantity*avg_unit_price*total_price";
	$data_array_job="".$job_qty."*".$poavgprice."*".$job_amt."";
	//echo $field_array_job."****".$data_array_job;
	$po_qty=$po_data_arr[str_replace("'","",$po_id)]['qty'];
	if($po_qty>0)
	{
	$po_unit_price=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4,'.','');
	$poavgprice_po=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4,'.','');
	$po_ex_per=number_format((($po_data_arr[str_replace("'","",$po_id)]['plan']-$po_qty)/$po_qty)*100,2,'.','');
	}
	else {
		$po_unit_price=0;$poavgprice_po=0;$po_ex_per=0;
		}
	

	
	if($breakdown_type==4)
	{
		if(str_replace("'","",$order_status)==2)
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut*original_po_qty";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*'".$po_qty."'";
		}
		else
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";
		}
	}
	else
	{
		if(str_replace("'","",$order_status)==2)
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut*original_po_qty";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*'".$po_qty."'";
		}
		else
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";
		}

	}
	//echo "10**".$field_array_po."*".$data_array_po; die;
	$rID2=sql_update("wo_po_details_master",$field_array_job,$data_array_job,"job_no","".$job_no."",1);
	$rID3=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$po_id."",1);

	$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,
	sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst=$job_no ");

	$jobQtyProjected=0; $jobPriceProjected=0; $jobAmtProjected=0; $jobQtyOriginal=0; $jobPriceOriginal=0; $jobAmtOriginal=0;
	$job_projected_price=0;
	if($projected_data_array[0][csf('job_projected_total')]>0)
	{
	$job_projected_price=$projected_data_array[0][csf('job_projected_total')]/$projected_data_array[0][csf('job_projected_qty')];
	}
	else $job_projected_price=0;

	$jobQtyProjected= number_format($projected_data_array[0][csf('job_projected_qty')],0,'.','');
	$jobPriceProjected= number_format($job_projected_price,4,'.','');
	$jobAmtProjected= number_format($projected_data_array[0][csf('job_projected_total')],2,'.','');

	$jobQtyOriginal= number_format($projected_data_array[0][csf('projected_qty')],0,'.','');
	$jobPriceOriginal= number_format($projected_data_array[0][csf('projected_rate')],4,'.','');
	$jobAmtOriginal= number_format($projected_data_array[0][csf('projected_amount')],2,'.','');
	
	if($poavgprice=='nan') $poavgprice=0;
	if($po_unit_price=='nan') $po_unit_price=0;
	if($jobQtyOriginal=='nan') $jobQtyOriginal=0;
	if($jobPriceOriginal=='nan') $jobPriceOriginal=0;

	$value= $job_qty."**".$poavgprice."**".$job_amt."**".$jobQtyProjected."**".$jobPriceProjected."**".$jobAmtProjected."**".$jobQtyOriginal."**".$jobPriceOriginal."**".$jobAmtOriginal."**".$po_unit_price;
	//array(0=>$rID,1=>$po_data[csf('po_tot')],2=>$poavgprice,3=>$po_data[csf('po_tot_price')]);
	return $value;
	//exit();
}

if($action=="ultimate_dtls_popup")
{
	echo load_html_head_contents("Ultimate Country Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$ex_data=explode('_',$data);
	$po_no=$ex_data[0];
	$po_id=$ex_data[1];
	$qty=$ex_data[2];
	$color=$ex_data[3];
	$item_id=$ex_data[5];
	$country_id=$ex_data[6];
	$code_id=$ex_data[7];
	$ultimate_country_id=$ex_data[8];
	$countryCode_id=$ex_data[9];
	$countryShip_date=$ex_data[10];
	//echo $data; die;

	?>
	<script>
	var permission='<? echo $permission; ?>';

	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_list_search tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			var colorQty=$('#txt_colorQty').val()*1;
			var inQty=0;
			for( var k = 1; k <= row_num; k++)
			{
				inQty=inQty+($('#txtQty_'+k).val()*1);
			}
			var desQty=0;
			desQty=colorQty-inQty;

			i++;
			$("#tbl_list_search tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_list_search");
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#cboDestination_'+i).val("");
			$('#txtQty_'+i).attr('placeholder',desQty);
			//$('#txt_qty_'+i).removeAttr("placeholder").attr("placeholder","Type an ID");
			$('#txtQty_'+i).val("");
			//$('#txt_qty_'+i).removeAttr('placeholder').placeholder();
		}
	}

	function fn_deletebreak_down_tr(rowNo)
	{
		var numRow = $('table#tbl_list_search tbody tr').length;
		if(rowNo!=1)
		{
			var permission_array=permission.split("_");
			var rowid=$('#rowid_'+rowNo).val();
			if(rowid !="" && permission_array[2]==1)
			{
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'order_entry_controller');
			}
			var index=rowNo-1
			$('#tbl_list_search tbody tr:eq('+index+')').remove();
			var numRow = $('table#tbl_list_search tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
					'value': function(_, value) { return value }
					});
				})
			}
		}
	}

	function fnc_destination_info( operation )
	{
		var row_num = $('table#tbl_list_search tbody tr').length;
		var data_all="";
		var colorQty=$('#txt_colorQty').val()*1;
		var inQty=0;
		for (var i=1; i<=row_num; i++)
		{
			if(form_validation('cboDestination_'+i+'*txtQty_'+i,'Destination*Qty')==false)
			{
				return;
			}
			inQty=inQty+($('#txtQty_'+i).val()*1);
			data_all+=get_submitted_data_string('cboDestination_'+i+'*txtQty_'+i,"../../../",i);
		}
		var desQty=0;
		desQty=colorQty-inQty;
		if (colorQty<inQty)
		{
			alert("Qty Excceded From Color Qty.");
			return;
		}
		var data_main="action=save_update_delete_destination_info&operation="+operation+"&total_row="+row_num+get_submitted_data_string('txt_poId*cbo_country*cbo_code_id*cbo_ultimate*cbo_countrycode_id*txt_country_date*txt_color*cbo_item',"../../../");
		//alert(data_main);
		var data=data_main+data_all;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_destination_info_reponse;
	}

	function fnc_destination_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
		}
	}
	</script>
	</head>
	<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
        <fieldset style="width:720px">
        <form id="ultimateinfo_1" autocomplete="off">
            <table width="720" cellspacing="2" cellpadding="2">
                <tr>
                	<td width="100"><strong>Po No</strong></td>
                    <td width="100"><input type="text" id="txt_poNo" name="txt_poNo" class="text_boxes" style="width:90px" value="<? echo $po_no; ?>" disabled /><input type="hidden" id="txt_poId" name="txt_poId" class="text_boxes" style="width:70px" value="<? echo $po_id; ?>" disabled /></td>
                    <td width="100"><strong>Delivery Country</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_country", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Select Country--", $country_id,"",1 ); ?></td>
                    <td width="100"><strong>Code</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_code_id", 100,"select id, ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select--", $code_id,"",1 ); ?></td>
                </tr>
                <tr>
                	<td width="100"><strong>Country</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_ultimate", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Select--", $ultimate_country_id,"",1 ); ?></td>
                	<td width="100"><strong>Country Code</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_countrycode_id", 100,"select id, ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select--", $countryCode_id,"",1 ); ?></td>
                	<td width="100"><strong>Country Ship date</strong></td>
                    <td width="100"><input type="text" id="txt_country_date" name="txt_country_date" class="datepicker" style="width:90px" value="<? echo $countryShip_date; ?>" disabled /></td>
                </tr>
                <tr>
                	<td width="100"><strong>Gmts Item</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_item", 100,$garments_item, "", 1, "--Select Item--", $item_id,"",1 ); ?></td>
                	<td width="100"><strong>Color</strong></td>
                    <td width="100"><input type="text" id="txt_color" name="txt_color" class="text_boxes" style="width:90px" value="<? echo $color; ?>" disabled /></td>
                	<td width="100"><strong>Color Qty</strong></td>
                    <td width="100"><input type="text" id="txt_colorQty" name="txt_colorQty" class="text_boxes_numeric" style="width:90px" value="<? echo $qty; ?>" disabled /></td>
                </tr>
            </table>
            <table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                    <th>Destination</th>
                    <th>Qty.</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>
					<?
					$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
					
					
					//$color_id = return_id( str_replace("'","",$color), $color_arr, "lib_color", "id,color_name");
					
					if(str_replace("'","",$color)!="")
					{ 
						if (!in_array(str_replace("'","",$color),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$color), $color_arr, "lib_color", "id,color_name","401");  
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_color[$color_id]=str_replace("'","",$color);
						}
						else $color_id =  array_search(str_replace("'","",$color), $new_array_color); 
					}
					else $color_id=0;
					
					
                    $data_array=sql_select("select id, destination_id, destination_qty from wo_po_destination_info where po_id =".$po_id." and item_id=".$item_id." and country_id=".$country_id." and code_id=".$code_id." and ultimate_country_id=".$ultimate_country_id." and ul_country_code=".$countryCode_id." and color_id='".$color_id."'");

					//echo "select id, destination_id, destination_qty from wo_po_destination_info where po_id =".$po_id." and item_id=".$item_id." and country_id=".$country_id." and code_id=".$code_id." and ultimate_country_id=".$ultimate_country_id." and ul_country_code=".$countryCode_id." and color_id='".$color_id."'";
                    if(count($data_array)>0)
                    {
						$i=1;
						foreach($data_array as $row)
						{
							?>
							<tr class="general" id="tr_<? echo $i;?>">
                                <td><? echo create_drop_down( "cboDestination_$i", 200,"select id, depo_code from lib_country_depo_mapping where country_id='$country_id' and status_active=1 and is_deleted=0 order by depo_code", "id,depo_code", 1, "--Select--", $row[csf("destination_id")],"",'' ); ?></td>
                                <td><input type="text" id="txtQty_<? echo $i;?>" name="txtQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("destination_qty")]; ?>" /></td>
                                <td width="100">
                                    <input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
                                    <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
                                </td>
							</tr>
							<?
							$i++;
						}
                    }
                    else
                    {
						?>
						<tr class="general" id="tr_1">
                            <td><? echo create_drop_down( "cboDestination_1", 200,"select id, depo_code from lib_country_depo_mapping where country_id='$country_id' and  status_active=1 and is_deleted=0 order by depo_code", "id,depo_code", 1, "--Select--", '',"",'' ); ?></td>
                            <td><input type="text" id="txtQty_1" name="txtQty_1" class="text_boxes_numeric" style="width:80px" placeholder="<? echo $qty; ?>" /></td>
                            <td width="100">
                                <input type="button" id="increase_1" name="increase_1" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
                            </td>
						</tr>
						<?
                    }
                    ?>
                </tbody>
            </table>
            <div align="center" style="margin-top:10px">
            <?
            if(count($data_array)>0)
            {
            	echo load_submit_buttons( $permission, "fnc_destination_info", 1,0 ,"reset_form('ultimateinfo_1','','','','')",1) ;
            }
            else
            {
            	echo load_submit_buttons( $permission, "fnc_destination_info", 0,0 ,"reset_form('ultimateinfo_1','','','','')",1) ;
            }
            ?>
            </div>
        </form>
        </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_delete_destination_info")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_po_destination_info", 1) ;
		$field_array="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";
		//$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_size", "id,lib_color","401");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else $color_id=0;
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboDestination="cboDestination_".$i;
			$txt_qty="txtQty_".$i;

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_poId.",".$cbo_item.",".$cbo_country.",".$txt_country_date.",".$cbo_code_id.",".$cbo_ultimate.",".$cbo_countrycode_id.",'".$color_id."',".$$cboDestination.",".$$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}
		$rID=sql_insert("wo_po_destination_info",$field_array,$data_array,1);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_size", "id,lib_color","401");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else $color_id=0;
		
		$add_comma=0;
		execute_query("delete from wo_po_destination_info where po_id =".$txt_poId." and item_id=".$cbo_item." and country_id=".$cbo_country." and code_id=".$cbo_code_id." and ultimate_country_id=".$cbo_ultimate." and ul_country_code=".$cbo_countrycode_id." and color_id='".$color_id."'",0);
		$id=return_next_id( "id", "wo_po_destination_info", 1) ;
		$field_array="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";

		for ($i=1;$i<=$total_row;$i++)
		{
			$cboDestination="cboDestination_".$i;
			$txt_qty="txtQty_".$i;

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_poId.",".$cbo_item.",".$cbo_country.",".$txt_country_date.",".$cbo_code_id.",".$cbo_ultimate.",".$cbo_countrycode_id.",'".$color_id."',".$$cboDestination.",".$$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}

		$rID=sql_insert("wo_po_destination_info",$field_array,$data_array,1);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$field_array_up="status_active*is_deleted*updated_by*update_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$poNo="poNo_".$i;
			$poQnty="poQnty_".$i;
			$rowid="rowid_".$i;
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2";
			}
			else{
				mysql_query("ROLLBACK");
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;*/
	}
}

if($action=='full_qty_check_for_validation')
{
	$ex_data=explode('***',$data);
	if($ex_data[3]==0)
	{
		$sql_result = sql_select("select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[1]' and item_number_id='$ex_data[2]' and status_active!=0 and is_deleted=0");
	}
	else if($ex_data[3]==1)
	{
		$sql_result = sql_select("select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[4]' and item_number_id='$ex_data[2]' and status_active!=0 and is_deleted=0");
	}
	//echo "select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[1]' and item_number_id='$ex_data[2]' and status_active=1 and is_deleted=0";

	echo $sql_result[0][csf('po_qty')];disconnect($con);
 	exit();
}

if($action=="set_ship_date")
{
	$data=explode("_",$data);
	$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1)
	{
		echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	}
	else if($data[1]==2)
	{
		echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	}
	else if($data[1]==3)
	{
		echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	}
	exit();
}

if($action=="load_cutOff_id_from_lib")
{
	$sql_country=sql_select("select cut_off from lib_country where id='$data'");
	echo "document.getElementById('cbo_cutOff_id').value = '".$sql_country[0][csf('cut_off')]."';\n";
 	disconnect($con);exit();
}

if($action=="assortment_pop_up")
{
	echo load_html_head_contents("Assortment Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode("_",$data);
	$ex_ass_data=explode('!!',$ex_data[3]);
	?>
    <script>
		function fnc_total_qty_check(index_i)
		{
			var assort_qty=$('#txt_assort').val()*1;
			var solid_qty=$('#txt_solid').val()*1;
			var tot_qty=$('#txt_tot_asst').val()*1;
			var tot_ast_sold=assort_qty+solid_qty;
			if(assort_qty>tot_qty)
			{
				alert('Assort Qty is Over.')
				$('#txt_assort').val('');
				return;
			}
			else if(solid_qty>tot_qty)
			{
				alert('Solid Qty is Over.')
				$('#txt_solid').val('');
				return;
			}
			var balance_qty=0;
			if(index_i==1)
			{
				balance_qty=tot_qty-assort_qty;
				$('#txt_solid').val( balance_qty );
			}
			else if(index_i==2)
			{
				balance_qty=tot_qty-solid_qty;
				$('#txt_assort').val( balance_qty );
			}
		}

		function js_set_value()
		{
			document.getElementById('txt_assort').value;
			document.getElementById('txt_solid').value;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
    <div id="rate_details"  align="center">
        <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
            <table width="360" cellspacing="0" border="1" class="rpt_table" id="tbl_rateDetails" rules="all">
            	<thead>
                	<th width="80">Color Name</th>
                    <th width="60">Size</th>
                	<th width="70">Assort</th>
                    <th width="70">Solid</th>
                    <th>Total</th>
                </thead>
                <tr>
                    <td bgcolor="#CCFF66" align="center"><? echo $ex_data[0]; ?></td>
                    <td bgcolor="#FFFFCC" align="center"><? echo $ex_data[1]; ?></td>
                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_assort" id="txt_assort" value="<? echo $ex_ass_data[0]; ?>" onBlur="fnc_total_qty_check(1);" /></td>
                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_solid" id="txt_solid" value="<? echo $ex_ass_data[1]; ?>" onBlur="fnc_total_qty_check(2);" /></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="txt_tot_asst" id="txt_tot_asst" value="<? echo $ex_data[2]; ?>" disabled /></td>
                </tr>
            </table>
            <table width="360" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value()" /></td>
                </tr>
            </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="sc_lc_status")
{
	$data=explode("_",$data);
	/*if($data[1]=="")
	{
		$sql="select booking_no, is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}*/

	$sql_sc="select a.contact_system_id, a.contract_no from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.wo_po_break_down_id='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.contact_system_id, a.contract_no";

	$sql_lc="select a.export_lc_system_id, a.export_lc_no from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and b.wo_po_break_down_id='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.export_lc_system_id, a.export_lc_no";
	$sql_sc_res=sql_select($sql_sc);
	$sql_lc_res=sql_select($sql_lc);
	$sc_data=""; $lc_data="";

	foreach($sql_sc_res as $rowsc)
	{
		if($rowsc[csf('contact_system_id')]=="")
		{
			$sc_data='';
		}
		else
		{
			if($sc_data=='') $sc_data=$rowsc[csf('contact_system_id')]." : ".$rowsc[csf('contract_no')]; else $sc_data.=', '.$rowsc[csf('contact_system_id')]." : ".$rowsc[csf('contract_no')];
		}
	}

	foreach($sql_lc_res as $rowlc)
	{
		if($rowlc[csf('export_lc_system_id')]=="")
		{
			$lc_data='';
		}
		else
		{
			if($lc_data=='') $lc_data=$rowlc[csf('export_lc_system_id')]." : ".$rowlc[csf('export_lc_no')]; else $lc_data.=', '.$rowlc[csf('export_lc_system_id')]." : ".$rowlc[csf('export_lc_no')];
		}
	}
	unset($sql_sc_res); unset($sql_lc_res);
	/*if($sc_data=='') $sc_data=0;
	if($lc_data=='') $lc_data=0; */
	echo rtrim($sc_data)."_".rtrim($lc_data);
	exit();
}

if($action=="get_cutting_qty_country")
{
	$data=explode("_",$data);
	$production_quantity=0;
	$sql_data= sql_select("SELECT a.po_break_down_id, sum(b.production_qnty) as production_quantity from  pro_garments_production_mst a join pro_garments_production_dtls b on  a.id=b.mst_id where a.po_break_down_id='$data[0]' and  a.country_id='$data[1]' and a.production_type=1 and b.color_size_break_down_id='$data[2]' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id"); 
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_quantity')]>0)
		{
			$production_quantity=$row_data[csf('production_quantity')];
		}
	}
	echo trim($production_quantity);
	exit();
}

if($action=="set_smv_work_study")
{
	$data=explode("**",$data);
	$item_id=$data[1];
	$style_id=$data[0];

	$sql_smv="select upper(style_ref) as style_ref, gmts_item_id, total_smv from ppl_gsd_entry_mst where gmts_item_id=$item_id and status_active=1 and is_deleted=0";
	$sql_result=sql_select($sql_smv);$set_smv_arr=array();
	foreach($sql_result as $row)
	{
		$set_smv_arr[$row[csf('style_ref')]][$row[csf('gmts_item_id')]]['smv']+=$row[csf('total_smv')];
	}
	// print_r($set_smv_arr);
	if(count($sql_result)>0) echo "1_".$set_smv_arr[$style_id][$item_id]['smv'];
	else echo "0_";

	exit();
}

/*if($action=="set_smv_checking")
{
	$smv_id=return_field_value("publish_shipment_date","variable_order_tracking","company_name =$data and variable_list=47 and is_deleted=0 and status_active=1");
	if($smv_id=="" || $smv_id==0) $smv_id=0; else $smv_id=$smv_id;
	echo "document.getElementById('set_smv_id').value 	= '".$smv_id."';\n";
	exit();
}*/

if($action=="open_smv_list")
{
	echo load_html_head_contents("WS SMV Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$item_id=$item_id;
	$style_id=$txt_style_ref;
	$set_smv_id=$set_smv_id;
	$row_id=$id;
	$set_smv_id=$set_smv_id;
	$cbo_buyer_name=$cbo_buyer_name;
	$cbo_company_name=$cbo_company_name;
	//echo $cbo_company_name;
	?>
	<script type="text/javascript">
      function js_set_value(id)
      { 	//alert(id);
		  document.getElementById('selected_smv').value=id;
		  parent.emailwindow.hide();
      }
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                    <input type="hidden" id="row_id" value="<?  echo $row_id;?>">
                    <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                    <input type="hidden" id="bulletin_type" value="<?  echo $bulletin_type;?>">
                &nbsp;</th>
            </thead>
            <tr>
                <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value+'_'+document.getElementById('bulletin_type').value, 'create_item_smv_search_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
        </table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if($action=="create_item_smv_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];
	$bulletin_type=$data[5];

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}
	
	
	$variable_stylesmv_source=return_field_value("publish_shipment_date","variable_order_tracking","company_name='$company' and variable_list=47 and status_active=1 and is_deleted=0 ","publish_shipment_date");
	//echo $variable_stylesmv_source;
	$appCond="";
	if($variable_stylesmv_source==3)
	{
		$approval_necessity_setup=return_field_value("approval_need","approval_setup_mst a, approval_setup_dtls b","a.id=b.mst_id and a.company_id='$company' and b.page_id=31 and a.status_active=1 and a.is_deleted=0 order by a.setup_date desc","approval_need");	
		if($approval_necessity_setup==1)
		{
			$appCond="and a.approved=1";
		}
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code, a.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and bulletin_type=$bulletin_type $gmts_item_con $style_con $buyer_id_con $appCond and rownum = 1 order by a.id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$smv_dtls_arr[$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr[$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('extention_no')]]['total_smv']=$row[csf('total_smv')];
		$smv_dtls_arr[$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('extention_no')]]['system_no'].=$row[csf('system_no')].',';
		$smv_dtls_arr[$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
	$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		//echo $row[csf('operator_smv')].'<br>'.$row[csf('helper_smv')].'<br>';

		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_sewing_arr[8]);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="80">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="50">Bulletin Type</th>
                <th width="120">Style</th>
                <th width="70">Sewing SMV</th>
                <th width="70">Avg. Cuting SMV</th>
                <th width="70">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $ext_no=>$arrdata)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
			$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

			$finish_smv=$cut_smv=$sewing_smv=0;

			$sys_id=rtrim($arrdata['id'],',');
			$ids=array_filter(array_unique(explode(",",$sys_id)));
			//print_r($ids);
			$id_str=""; $k=0;
			foreach($ids as $idstr)
			{
				if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;

				foreach($lib_sewing_ids as $lsid)
				{
					$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
					$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
					$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
				}
				$k++;
			}

			$system_no=rtrim($arrdata['system_no'],',');
			$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));

			$finish_smv=$finish_smv/$k;
			$cut_smv=$cut_smv/$k;
			//$sewing_smv=$sewing_smv/$k;
			$sewing_smv=$arrdata['total_smv'];

			$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i; ?></td>
                <td width="80" style="word-break:break-all"><? echo $system_no; ?></td>
                <td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
                <td width="50" style="word-break:break-all"><? echo $bulletin_type_arr[$bulletin_type]; ?></td>
                <td width="120" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="70" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="70" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="70" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
			</tr>
			<?
			$i++;
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="color_popup")//ISD-23-24355
{
	echo load_html_head_contents("Color Select Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>

	$(document).ready(function (e) {
		setFilterGrid('tbl_list_search', -1);
	});
	
	var selected_id = new Array();
	var selected_name = new Array();

	function check_all_data() {
		var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

		tbl_row_count = tbl_row_count - 1;
		for (var i = 1; i <= tbl_row_count; i++) {
			js_set_value(i);
		}
	}

	function toggle(x, origColor) {
		var newColor = 'yellow';
		if (x.style) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
		}
	}

	function js_set_value(str) {
        toggle(document.getElementById('search' + str), '#FFFFCC');

        if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
        }
        else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
        }

        var id = '';  var name = '';
        for (var i = 0; i < selected_id.length; i++) {
			if(id=="") id=selected_id[i]; else id += ","+selected_id[i];
         	//id += selected_id[i] + ',';
         	//name += selected_name[i] + '__';
			if(name=="") name=selected_name[i]; else name+= "__"+selected_name[i];
        }

        id = id.substr(0, id.length - 0);
        name = name.substr(0, name.length - 0);

        $('#hiddcolor_id').val(id);
        $('#hiddcolor_name').val(name);
    }

	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="hiddcolor_name" name="hiddcolor_name" />
            <input type="hidden" id="hiddcolor_id" name="hiddcolor_id" />
            
            <table width="210" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th>Color Name</th>
                    </tr>
                </thead>
            </table>
            <div style="width:210px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="190" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				if($buyer_name=="" || $buyer_name==0)
				{
					$sql="select ID, COLOR_NAME FROM lib_color WHERE status_active=1 and is_deleted=0";
				}
				else
				{
					$sql="select A.ID, A.COLOR_NAME FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and a.status_active=1 and a.is_deleted=0";
				}
				//echo $sql;
				$colArray=sql_select($sql);
				foreach ($colArray as $crow) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>)">
						<td width="30" align="center"><?=$i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$crow['ID']; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$crow['COLOR_NAME']; ?>"/>
						</td>
                        <td style="word-break:break-all"><?=$crow['COLOR_NAME']; ?></td>
                    </tr>
                    <?
                    $i++;
                }
				?>
			</table>
		</div>
		<table width="210" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
                    <div style="float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/>
                        Check / Uncheck All
                    </div>
				</td>
			</tr>
            <tr>
				<td align="center" height="30" valign="bottom">
                    <div>
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                    </div>
				</td>
			</tr>
		</table>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}


if($action=="color_popup1")//ISD-23-24355
{
	echo load_html_head_contents("Color Popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
        	<table cellspacing="0" width="210" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th align="center"><?=create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                </thead>
            </table>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            if($buyer_name=="" || $buyer_name==0)
            {
            	$sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0";
            }
            else
            {
            	$sql="select a.id, a.color_name FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and status_active=1 and is_deleted=0";
            }
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

function fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$currercy,$sewSmv,$cutSmv,$page)
{
	if($page==1)
	{
		$is_pre_cost="";

		$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$cm_cost=0;

		$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
		$txt_sew_smv=$sewSmv*1;//$pre_cost_data[0][csf("sew_smv")];
		$txt_cut_smv=$cutSmv*1;//$pre_cost_data[0][csf("cut_smv")];
		$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
		$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
		//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);

		$cbo_currercy=str_replace("'","",$currercy);
		$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
		$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
		$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
		$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
		$costing_date= $pre_cost_data[0][csf("costing_date")];
		//var txt_job_no= document.getElementById('txt_job_no').value;

		$cbo_costing_per_value=0;
		if($cbo_costing_per==1) $cbo_costing_per_value=12;
		else if($cbo_costing_per==2) $cbo_costing_per_value=1;
		else if($cbo_costing_per==3) $cbo_costing_per_value=24;
		else if($cbo_costing_per==4) $cbo_costing_per_value=36;
		else if($cbo_costing_per==5) $cbo_costing_per_value=48;

		$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
		if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;

		if($cm_cost_method_based_on==0 || $cm_cost_method_based_on==1)
		{
			if($costing_date=="" || $costing_date==0)
			{
				if($db_type==0) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
			}
			else
			{
				if($db_type==0) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				else if($db_type==2) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
			}
		}
		else if($cm_cost_method_based_on==2)
		{
			$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$min_shipment_date="";
			foreach($min_shipment_sql as $row){ $min_shipment_date=$row[csf('min_shipment_date')]; }
			if($db_type==0) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
			else if($db_type==2) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==3)
		{
			$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$max_shipment_date="";
			foreach($max_shipment_sql as $row){ $max_shipment_date=$row[csf('max_shipment_date')]; }

			if($db_type==0) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
			else if($db_type==2) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==4)
		{
			$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$min_pub_shipment_date="";
			foreach($max_shipment_sql as $row){ $min_pub_shipment_date=$row[csf('min_pub_shipment_date')]; }

			if($db_type==0) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
			else if($db_type==2) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==4)
		{
			$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$max_pub_shipment_date="";
			foreach($max_shipment_sql as $row){ $max_pub_shipment_date=$row[csf('max_pub_shipment_date')]; }

			if($db_type==0) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
			else if($db_type==2) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		}

		$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0;
		$limit="";
		if($db_type==0) $limit="LIMIT 1"; else if($db_type==2) $limit="";
		$sqlstnd_cm="select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$cbo_company_name and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit";
		$sqlstnd_cm_arr=sql_select($sqlstnd_cm);
		foreach ($sqlstnd_cm_arr as $row)
		{
			if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
			if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
			if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
			if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
			if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
			if($row[csf("operating_expn")] !="")$operating_expn=$row[csf("operating_expn")];
		}
		//$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;

		$sql_pre_cost_dtls="select max(cm_cost) as cm_cost, sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
		$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
		$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0; $prev_cm_cost=0;

		$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
		$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
		$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
		$prev_cm_cost=$sql_pre_cost_dtls_arr[0][csf("cm_cost")]*1;

		if (count($pre_cost_data)>0)
		{
			execute_query( "update wo_pre_cost_mst set sew_smv='$txt_sew_smv', cut_smv='$txt_cut_smv' where job_no ='".$txt_job_no."'",1);
			if($cm_cost_predefined_method_id==1)
			{
				$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
				//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
				$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
				//alert(txt_exchange_rate)
				$cm_cost=$cm_cost/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==2)
			{
				$cu=0; $su=0;
				$cut_per=$txt_cut_efficiency_per/100;
				$sew_per=$txt_sew_efficiency_per/100;
				$cu=($txt_cut_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($cut_per*1);
				if($cu=="") $cu=0;

				$su=($txt_sew_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($sew_per*1);
				if($su=='') $su=0;
				$cm_cost=($cu+$su)/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==3)
			{
				//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
				$per_day_cost=$monthly_cm_expense/26;
				$per_machine_cost=$per_day_cost/$no_factory_machine;
				$per_line_cost=$per_machine_cost*$txt_machine_line;
				$total_production_per_line=$txt_prod_line_hr*$working_hour;
				$per_product_cost=$per_line_cost/$total_production_per_line;

				$cm_cost=($per_product_cost*$cbo_costing_per_value)/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==4)
			{
				$sew_per=$txt_sew_efficiency_per/100;
				$su=((trim(($cost_per_minute*1))/$sew_per)*($txt_sew_smv*$cbo_costing_per_value));
				$cm_cost=$su/$txt_exchange_rate;
			}
			else
			{
				$cm_cost=$prev_cm_cost;
			}

			$dec_type=0;
			if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;

			$cm_cost=number_format($cm_cost,6,'.','');
			$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');

			$tot_cost=number_format(($prev_tot_cost+$cm_cost),6,'.','');
			$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');

			$margin_dzn=number_format(($price_dzn-$tot_cost),6,'.','');
			$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');

			$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),6,'.','');
			$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');

			$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),6,'.','');
			$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');


			$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*margin_pcs_set*margin_pcs_set_percent";
			$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";

			$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
		}
		else
		{
			return;
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost;
	}
}

if($action=='print_job_details')
{
	//echo $data;
	$data=explode("**", $data);
	$txt_job_no=$data[0];
	$cbo_company_name=$data[1];
	$cbo_buyer_name=$data[2];
	$path=$data[3];


	
	if(!empty($txt_job_no)) 
	{
		$jobcond=" and a.job_no='".$txt_job_no."'";
	}
	else $jobcond="";
		
		 
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
		$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
		$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code"  );
		$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
		$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
		$sub_dept_array = return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
		$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
			ob_start();
			$width=1880;
			?>
	        <div style="width:<? echo $width;?>px">
		        <fieldset style="width:100%;">
		          	<table width="<? echo $width;?>">
		              	<tr class="form_caption">
		                    <td colspan="20" align="center">Color size details Report</td>
		                </tr>
		                <tr class="form_caption">
		                    <td colspan="20" align="center"><? echo $company_library[$cbo_company_name]; ?></td>
		                </tr>
		            </table>
		            <table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
		                <thead>
			                	 <tr>
				                    <th width="40">SL</th>
				                    <th width="120">LC Company</th>
				                    <th width="100">Buyer</th>
				                    <th width="100">Style Ref.</th>
				                    <th width="100">Style Desc.</th>
				                    <th width="70">Season</th>
				                    <th width="70">Product Dept.</th>
				                    <th width="70">Sub. Dept.</th>
				                    <th width="100">Item </th>
				                    <th width="100">PO No</th>
				                    <th width="70">Order Receive Date</th>
				                    <th width="70">Shipment Date</th>
				                    
				                    <th width="70">Delivery Country</th>
				                    <th width="70">UCUST Code</th>
				                    
				                    <th width="70">Country</th>
				                    <th width="70">Cust Code</th>
				                    
				                    <th width="70">Order UOM</th>
				                    <th width="70">Ship Mode</th>
				                    <th width="100">Color</th>
				                    <th width="70">Size</th>
				                    <th width="70">Order Qty</th>
									<th width="70">Ex-Cut %</th>
				                    <th width="70">Need To Cut</th>
				                    <th width="">Price</th>
				                </tr>
		                     
		                </thead>
		          
			                <?
			                $i=1; $total_order_qnty=0; $total_order_qnty_in_pcs=0;  
							 
							if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
							else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
							else $year_field="";//defined Later
			               $sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name,a.style_owner,a.order_uom,a.ship_mode,a.buyer_name,a.season_year,a.season_buyer_wise, a.team_leader, a.dealing_marchant, a.style_ref_no, a.style_description,a.product_code,a.product_dept,a.pro_sub_dep,a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.grouping,b.file_no,b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut,c.item_number_id,c.color_number_id,c.size_number_id,c.country_id,c.order_quantity,c.size_order,c.code_id,c.ul_country_code,c.ultimate_country_id,c.plan_cut_qnty,c.country_id,c.order_rate,c.order_total,c.excess_cut_perc from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.style_owner='$cbo_company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $jobcond  order by  b.id,c.color_order,c.size_order";// b.id, b.pub_shipment_date
						//echo $sql;
			                $nameArray=sql_select($sql);
			                $tot_rows=count($nameArray);
							$total_plan_cut_anty=$total_order_qnty=0;
			                foreach($nameArray as $row )
			                {
			                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$seasonyear=substr($row[csf('season_year')], -2);
								$season_year="";
								if($seasonyear)
								{
									$season_year="-".$seasonyear;
								}
			               		 ?>
			                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
			                        <td width="40"><? echo $i; ?></td>
			                        <td width="120" align="center"><? echo $company_library[$row[csf('style_owner')]]; ?></td>
			                        <td width="100" align="center"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
			                        <td width="100"><p style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
			                        <td width="100" align="center"><p style="word-break:break-all"><? echo $row[csf('style_description')]; ?></p></td>
			                        <td width="70" align="center"><? echo $season_library[$row[csf('season_buyer_wise')]].$season_year; ?></td>
			                        <td width="70"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
			                        <td width="70"><p><? echo $sub_dept_array[$row[csf('pro_sub_dep')]]; ?></p></td>
			                        <td width="100"><p style="word-break:break-all"> <? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
			                        <td width="100"><p style="word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
			                        <td width="70" align="center" >
			                            <? 
			                                echo change_date_format($row[csf('po_received_date')]); //
			                            ?>
			                        </td>
			                        <td width="70" align="center"><? echo  change_date_format($row[csf('pub_shipment_date')]); ?></td>
			                         <td width="70" align="center"><? echo  $country_arr[$row[csf('country_id')]]; ?></td>
			                         <td width="70" align="center"><? echo $country_code_arr[$row[csf('code_id')]]; ?></td>
			                          
			                        <td width="70" align="center"><p style="word-break:break-all">
			                        <? 
										echo  $country_arr[$row[csf('ultimate_country_id')]];//ultimate_country_id
			                        ?> </p>
			                        </td>
			                        <td width="70" align="center"><? echo $country_code_arr[$row[csf('ul_country_code')]]; ?></td>
			                        
			                        <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
			                        
			                        <td width="70" align="center"><? echo $shipment_mode[$row[csf('ship_mode')]];; ?></td>
			                        <td width="100" align="center"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
			                        <td width="70" align="center"><? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
			                        <td width="70" align="right"><? echo number_format($row[csf('order_quantity')],0,'.',''); ?></td>
									<td width="70" align="right"><? echo number_format($row[csf('excess_cut_perc')],0,'.',''); ?></td>
			                        <td width="70" align="right"><? echo number_format($row[csf('order_quantity')]+($row[csf('order_quantity')]*$row[csf('excess_cut_perc')])/100,0,'.',''); ?></td>
			                        <td width="" align="right"><? echo number_format($row[csf('order_rate')],2,'.',''); ?></td>
			                     
			                    </tr>
				                <?
								$total_order_qnty+=$row[csf('order_quantity')];
								$total_plan_cut_anty+=$row[csf('order_quantity')]+($row[csf('order_quantity')]*$row[csf('excess_cut_perc')])/100;
				                $i++;
			                }
			                ?>
		                  </tbody>
		                    <tfoot>
		                        <th width="40"></th>
		                        <th width="120"></th>
		                        <th width="100"></th>
		                        <th width="100"></th>
		                        <th width="100"></th>
		                        <th width="70"></th>
		                        <th width="70"></th>
		                        <th width="70"></th>
		                        <th width="100"></th>
		                        <th width="100"></th>
		                        <th width="70" align="right"></th>
		                        <th width="70" align="right" id=""></th>
		                        <th width="70"></th>
		                        <th width="70"></th>
		                        <th width="70"></th>
		                        <th width="70">&nbsp;</th>
		                         <th width="70"></th>
		                        <th width="70"></th>
		                        <th width="100"></th>
		                        <th width="70">Total</th>
		                        <th width="70"  align="right" id="total_order_qnty_in_pcs"><? echo $total_order_qnty;?></th>
								<th width="70"></th>
		                         <th width="70"  align="right" id="total_order_qnty_needtocut"><? echo $total_plan_cut_anty;?></th>
		                        <th width=""></th>
		                        
		                    </tfoot>
		                </table>
		           
		        </fieldset>
		    </div>
	<?
	exit();	 
}

if($action=='print_B2')
{
	//echo $data;
	$data=explode("**", $data);
	$txt_job_no=$data[0];
	$cbo_company_name=$data[1];
	$cbo_buyer_name=$data[2];
	$path=$data[3];
	
	if(!empty($txt_job_no)) 
	{
		$jobcond=" and a.job_no='".$txt_job_no."'";
	}
	else $jobcond="";
		
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	$sub_dept_array = return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
	$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$dealing_marchand_lib=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$set_ratio=return_library_array( "select job_no, set_item_ratio from wo_po_details_mas_set_details", "job_no", "set_item_ratio"  );
	ob_start();
	

	$data_arr=sql_select("select buyer_name,company_name,job_no,season_buyer_wise,gmts_item_id,style_ref_no,style_description,brand_id,dealing_marchant,remarks from wo_po_details_master where job_no='$txt_job_no'");

	$total_order_qnty=0; $total_order_qnty_in_pcs=0;  
		 
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.style_owner, a.order_uom, a.ship_mode, a.buyer_name, a.season_year, a.season_buyer_wise, a.team_leader, a.dealing_marchant, a.style_ref_no, a.style_description, a.product_code, a.product_dept, a.pro_sub_dep, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, c.item_number_id, c.color_number_id, c.size_number_id, c.country_id, c.order_quantity, c.size_order, c.plan_cut_qnty, c.country_id, c.order_rate, c.order_total, b.no_of_carton, b.details_remarks,matrix_type  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.company_name='$cbo_company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $jobcond order by b.id, c.color_order, c.size_order";// b.id, b.pub_shipment_date
						
	// echo $sql;
  
	$nameArray=sql_select($sql);
	$po_color_size_arr=array();
	foreach($nameArray as $row )
	{
		$matrix_type=$row[csf('matrix_type')];
		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];	

		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['country']=$country_arr[$row[csf('country_id')]];
		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];

		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['no_of_carton']=$row[csf('no_of_carton')];	

		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];	
		$order_wise_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['remarks']=$row[csf('details_remarks')];		

		$size_arr[$row[csf('size_order')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$color_size_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_color_size_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qnty']+=$row[csf('plan_cut_qnty')];
		$po_color_size_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];

		$po_color_size_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['ratio']+=$row[csf('plan_cut_qnty')];

		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
	}
	ksort($size_arr);
	
	//========================================================================================
	if($matrix_type!=1){
		$ratio_data=sql_select("select id, color_id, size_id, ratio_qty, ratio_rate, po_id, gmts_item_id from wo_po_ratio_breakdown where is_deleted=0 ".where_con_using_array($po_id_arr,1,'po_id')." and status_active=1");

		foreach($ratio_data as $row){
			$po_color_size_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['ratio']=$row[csf('ratio_qty')];
		}
	}
	
	//============================================================================================
	// echo "<pre>";
	// print_r($po_color_size_arr);
	$seqsize=0;
	foreach($size_arr as $sizseq=>$sizeval) { 
		foreach($sizeval as $val) { 
			$seqsize++;
		}
	 }
	
	$width=910+($seqsize*60);
	?>
	<div style="width:<?=$width;?>px">
		<fieldset style="width:100%;">
			<table width="<? echo $width;?>">
				<tr class="form_caption">
					<td  align="left">Job No </td>
					<td  align="left">:<?=$data_arr[0][csf('job_no')];?></td>
					<td  align="left">Style Ref </td>
					<td  align="left">:<?=$data_arr[0][csf('style_ref_no')];?></td>
					<td  align="left">Style Description </td>
					<td  align="left">:<?=$data_arr[0][csf('style_description')];?></td>
				</tr>
				<tr class="form_caption">
					<td  align="left">Company </td>
					<td  align="left">:<?=$company_library[$cbo_company_name];?></td>
					<td  align="left">Buyer  </td>
					<td  align="left">:<?=$buyer_library[$data_arr[0][csf('buyer_name')]];?></td>
					<td  align="left"> Brand </td>
					<td  align="left">:<?=$brand_library[$data_arr[0][csf('brand_id')]];?></td>
				</tr>
				<tr class="form_caption">
					<td  align="left">Season </td>
					<td  align="left">:<?=$season_library[$data_arr[0][csf('season_buyer_wise')]];?></td>
					<td  align="left">  </td>
					<td  align="left"> </td>
					<td  align="left">Merchant </td>
					<td  align="left">:<?=$dealing_marchand_lib[$data_arr[0][csf('dealing_marchant')]];?></td>
				</tr>
			</table>
            <table id="table_header_1" class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                     <tr>
                        <th width="120">Order Number</th>
                        <th width="100">Item</th>
                        <th width="100"></th>
                        <th width="100">Color</th>
                        <th width="100">Size</th>
                        <?
                        foreach($size_arr as $sizseq=>$sizeval) { 
							foreach($sizeval as $val) { 
							//echo $sizseq.'-'.$val.'<br>'; ?>
							<th width="60"><?=$size_library[$val];?></th>
                        <? }} ?>
                        <th width="60"> Ratio Total</th>
                        <th width="70">Order Qty</th>
                        <th width="80">Order Qty PCS </th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
				<?
                $i=1; 
                $tot_rows=count($nameArray); $total_plan_cut_anty=$total_order_qnty=0;
                $po_ratio_tot=array();  $item_size_qty_tot=array();
                foreach($order_wise_arr as $po_id =>$item_data )
                {
					foreach($item_data as $item_id =>$color_data )
					{
						$r=1; $c=1; $re=1; $cq=1;
						foreach($color_data as $color_id => $row )
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$seasonyear=substr($row[csf('season_year')], -2);
							$season_year="";
							if($seasonyear) $season_year="-".$seasonyear;
							?>
							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
								<?
                                if($r==1){?>
                                    <td width="120" align="left" style="word-break:break-all" rowspan="<?=count($color_data)*2;?>"><?=$row['po_number']."<br>".$row['pub_shipment_date']."<br>".$row['country']; ?></td>
                                    <td width="100" align="left" style="word-break:break-all" rowspan="<?=count($color_data)*2;?>"><?=$garments_item[$item_id];?></td>
                                <? $r++; }
                                if($c==1){
									?>			
									<td width="100"><? echo "Carton Qty"; ?></td>	
                                <? $c++; } else { ?>				
                                	<td width="100"><? echo " "; ?></td>
                                <? } ?>
                                <td width="100" style="word-break:break-all"><? echo " "; ?></td>
                                <td width="100" align="center" style="word-break:break-all">Ratio</td>
                                <?
                                foreach($size_arr as $sizseq=>$sizeval) {
									foreach($sizeval as $sizeid) {
										?>
										<td width="60" align="center" style="word-break:break-all"><?=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['ratio']; ?></td>
										<?
										$po_color_ratio_tot[$po_id][$item_id][$color_id]+=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['ratio'];
										$po_ratio_tot[$po_id]+=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['ratio'];
										$item_ratio_tot[$po_id][$item_id]+=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['ratio'];
									}
                                }
								?>
                                <td width="60" align="center" style="word-break:break-all"><?=$po_color_ratio_tot[$po_id][$item_id][$color_id]; ?></td>
                                <td width="70"><? echo ""; ?></td>
                                <td width="80" style="word-break:break-all"><? echo ""; ?></td>
                                <?
                                if($re==1){?>
                                	<td style="word-break:break-all" rowspan="<?=count($color_data)*2;?>"><?=$row['remarks']; ?></td>
                                <? $re++; }?>				
                            </tr>
                            <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
                                <? if($cq==1){ ?>			
                                <td width="100" align="center" style="word-break:break-all"><?=$row['no_of_carton']; ?></td>	
                                <? $cq++;} else { ?>				
                                <td width="100"><? echo " "; ?></td>
                                <? } ?>
                                <td width="100" style="word-break:break-all"><? echo $color_arr[$color_id]; ?></td>
                                <td width="100" align="center"><? echo ""; ?></td>
                                <?
                                $po_qty_tot=array();
                                foreach($size_arr as $sizseq=>$sizeval){
									foreach($sizeval as $sizeid) {
										$ratio=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['ratio'];
										$orderQty=$po_color_size_arr[$po_id][$item_id][$color_id][$sizeid]['order_quantity'];
										?>
										<? if($matrix_type!=1){?>
										<td width="60" align="right" style="word-break:break-all" title="<?=$row['no_of_carton'].'*'.$ratio;?>">
									
										<?=$row['no_of_carton']*$ratio; ?>
										</td>
										<?
										$item_size_qty_tot[$po_id][$item_id][$sizeid]+=$row['no_of_carton']*$ratio;
										$po_qty_tot[$po_id][$item_id][$color_id]+=$row['no_of_carton']*$ratio;
										$po_size_qty_tot[$po_id][$sizeid]+=$row['no_of_carton']*$ratio;
										}else{?>
										<td width="60" align="right" style="word-break:break-all" title="<?=$orderQty;?>">
										<?=$orderQty; ?>
										</td>
										<?
										$item_size_qty_tot[$po_id][$item_id][$sizeid]+=$orderQty;
										$po_qty_tot[$po_id][$item_id][$color_id]+=$orderQty;
										$po_qty_total[$po_id][$item_id][$color_id]+=$ratio;
										$po_size_qty_tot[$po_id][$sizeid]+=$orderQty;
										}
									}
                                }
								?>
                                <td width="60"><? echo ""; ?></td>
                                <td width="70" align="right"><?=$po_qty_tot[$po_id][$item_id][$color_id]; ?></td>
                                <td  style="word-break:break-all" align="right">
								<? if($matrix_type!=1){?>
								<?=$po_qty_tot[$po_id][$item_id][$color_id]*$set_ratio[$row['job_no']];
								}else{?>
								<?=$po_qty_total[$po_id][$item_id][$color_id];
								}
								?></td>
							</tr>
							<?
							$total_order_qnty+=$row[csf('order_quantity')];//$total_plan_cut_anty+=$row[csf('plan_cut_anty')];
							$i++;
							
							$po_order_qty_tot[$po_id]+=$po_qty_tot[$po_id][$item_id][$color_id]; 
							$po_order_qty_pcs_tot[$po_id]+=$po_qty_tot[$po_id][$item_id][$color_id]*$set_ratio[$row['job_no']]; 
							$po_order_qty_pcs_total[$po_id]+=$po_qty_total[$po_id][$item_id][$color_id]; 
							$item_order_qty_tot[$po_id][$item_id]+=$po_qty_tot[$po_id][$item_id][$color_id]; 
							$item_order_qty_pcs_tot[$po_id][$item_id]+=$po_qty_tot[$po_id][$item_id][$color_id]*$set_ratio[$row['job_no']];
							$item_order_qty_pcs_total[$po_id][$item_id]+=$po_qty_total[$po_id][$item_id][$color_id];  
						}?>
						<tr>
                            <td colspan="5" align="right"><b> Item Wise Total</b></td>
                            <?
                            foreach($size_arr as $sizseq=>$sizeval) {
								foreach($sizeval as $sizeid) {
									?>
									<td align="right" style="word-break:break-all"><?=$item_size_qty_tot[$po_id][$item_id][$sizeid]; ?></td>
                            <? } } ?>
                            <td align="center"><b><?=$item_ratio_tot[$po_id][$item_id];?></b></td>
                            <td align="right"><b><?=$item_order_qty_tot[$po_id][$item_id];?></b></td>
                            <td align="right"><b>
							<? if($matrix_type!=1){?>
								<?=$item_order_qty_pcs_tot[$po_id][$item_id];
								}else{?>
								<?=$item_order_qty_pcs_total[$po_id][$item_id];
								}
								?></b></td>
                            <td>&nbsp;</td>
						</tr>
						<?
					}
					?>
					<tr>
                        <td colspan="5" align="right"><b>Po Wise Total</b></td>
                        <?
                        foreach($size_arr as $sizseq=>$sizeval){
							foreach($sizeval as $sizeid) {
								?>
								<td align="right"><b style="word-break:break-all"><?=$po_size_qty_tot[$po_id][$sizeid]; ?></b></td>
                        <? } } ?>
                        <td align="center"><b><?=$po_ratio_tot[$po_id];?></b></td>
                        <td align="right"><b><?=$po_order_qty_tot[$po_id];?></b></td>
                        <td align="right"><b>
								<? if($matrix_type!=1){?>
								<?=$po_order_qty_pcs_tot[$po_id];
								}else{?>
								<?=$po_order_qty_pcs_total[$po_id];
								}
								?></b></td>
                        <td>&nbsp;</td>                      
					</tr>
					<?
                }
                ?>
                </tbody>
            </table>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="generate_excel_sheet")
{
	$process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
	
	//echo $cbo_company_name; die;
	ob_start();
	$html="";
	
	$colorArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryArr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	
	$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.style_ref_no as STYLE_REF_NO, a.style_description as STYLE_DESCRIPTION, b.id AS POID, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.po_received_date as PO_RECEIVED_DATE, c.country_id AS COUNTRY_ID, c.country_ship_date AS COUNTRY_SHIP_DATE, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.item_number_id AS ITEM_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.order_rate as ORDER_RATE, c.excess_cut_perc as EXCESS_CUT_PERC, c.plan_cut_qnty AS PLAN_CUT_QNTY from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$cbo_company_name and a.id=$hidd_job_id ";
	$sqlpoRes = sql_select($sqlpo);
	$poArr=array();
	foreach($sqlpoRes as $row)
	{
		$strData=$row['STYLE_DESCRIPTION'].'__'.change_date_format($row['PUB_SHIPMENT_DATE']).'__'.change_date_format($row['SHIPMENT_DATE']).'__'.$countryArr[$row['COUNTRY_ID']].'__'.change_date_format($row['COUNTRY_SHIP_DATE']).'__'.$row['ORDER_RATE'].'__'.$row['EXCESS_CUT_PERC'].'__'.change_date_format($row['PO_RECEIVED_DATE']);
		
		$poArr[$row['STYLE_REF_NO']][$row['PO_NUMBER']][$colorArr[$row['COLOR_NUMBER_ID']]][$sizeArr[$row['SIZE_NUMBER_ID']]][$strData]+=$row['ORDER_QUANTITY'];
	}
	unset($sqlpoRes);
	?>
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="100">Style Ref.</th>
                    <th width="100">Style Desc.</th>
                    <th width="100">Po No</th>
                    <th width="70">Pub. Shipment Date</th>
                    <th width="70">Shipment Date</th>
                    <th width="120">Delivery Country</th>
                    <th width="70">Country Ship Date</th> 
                    <th width="100">Gmt Color</th>
                    <th width="80">Gmt Size</th>
                    <th width="80">Qty.</th>
                    <th width="70">Ex. Cut %</th>
                    <th width="70">Unit Price</th>
                    <th>PO Receive Date</th>
                </tr>
            </thead>
        </table>
    <div style="width:1150; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="1130" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <? $i=0;
		foreach($poArr as $styleRef=>$styleData)
		{
			foreach($styleData as $pono=>$poData)
			{
				foreach($poData as $colorstr=>$colorData)
				{
					foreach($colorData as $sizestr=>$sizeData)
					{
						foreach($sizeData as $exval=>$sizeqty)
						{
							$style_des=$pubshipdate=$shipdate=$countryName=$po_avg_rate=0; $country_qty=0; $country_amt=0; $countryShipDate=""; $planCutQty=0;
							
							$exdata=explode('__',$exval);
							
							$style_des=$exdata[0];
							$pubshipdate=$exdata[1];
							$shipdate=$exdata[2];
							$countryName=$exdata[3];
							$countryShipDate=$exdata[4];
							$po_avg_rate=number_format($exdata[5],2); 
							$exPer=$exdata[6];
							
							$poRecDate=$exdata[7];//date("d-m-Y");
							
							$country_qty=$sizeqty;
							?>
                            <tr>
                                <td width="100" style="word-break:break-all"><?=$styleRef; ?></td>
                                <td width="100" style="word-break:break-all"><?=$style_des; ?></td>
                                <td width="100" style="word-break:break-all"><?=$pono; ?></td>
                                <td width="70"><?=$pubshipdate; ?></td>
                                <td width="70"><?=$shipdate; ?></td>
                                <td width="120" style="word-break:break-all"><?=$countryName; ?></td>
                                <td width="70"><?=$countryShipDate; ?></td> 
                                <td width="100" style="word-break:break-all"><?=$colorstr; ?></td>
                                <td width="80" style="word-break:break-all"><?=$sizestr; ?></td>
                                <td width="80" align="right"><?=$sizeqty; ?></td>
                                <td width="70" align="right"><?=$exPer; ?></td>
                                <td width="70" align="right"><?=$po_avg_rate; ?></td>
                                <td><?=$poRecDate; ?></td>
                            </tr>
                            <?
							$i++;
						}
					}
				}
			}
		}
		unset($poArr);
		?>
        </table>
    </div>
    </fieldset>
    <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) 
    {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$i"; 
    exit();
}

if ($action=="actual_po_info_popup_v1")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	?> 
	<script>
	var permission='<?=$permission; ?>';
		
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_list_search tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_list_search");
			  
			$('#poQnty_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
			$('#shipdate_'+i).removeAttr("class").attr("class","datepicker");
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
			$('#rowid_'+i).val("");
			fnc_poqty_cal();
			set_all_onclick();
		}
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{   
		var numRow = $('table#tbl_list_search tbody tr').length; 
		if(rowNo!=1)
		{
			var permission_array=permission.split("_");
			var rowid=$('#rowid_'+rowNo).val();
			if(rowid !="" && permission_array[2]==1)
			{
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'order_entry_controller');
			}
			var index=rowNo-1
			$('#tbl_list_search tbody tr:eq('+index+')').remove();
			var numRow = $('table#tbl_list_search tbody tr').length; 
			for(i = rowNo;i <= numRow;i++)
			{
				$("#size_color_break_down_list tbody tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
						'value': function(_, value) { return value }             
					}); 
				})
			}
			set_all_onclick();
		}
	}
		
	function fnc_acc_po_info( operation )
	{
		freeze_window(operation);
		var job_no= $('#txt_job_no').val();
		var row_num = $('table#tbl_list_search tbody tr').length; 
		var z=1;  
		var po_item_chk_arr=new Array();
		
		for (var i=1; i<=row_num; i++)
		{
			var po_no= $('#poNo_'+i).val();
			var shipdate= $('#shipdate_'+i).val();
			var poQnty= $('#poQnty_'+i).val();
			po_item_chk_arr.push(po_no+'#'+shipdate);
		}
		//alert(po_item_chk_arr);
		function hasDuplicates(arr) {
		var counts = [];
		
		for (var i = 0; i <= arr.length; i++) {
			if (counts[arr[i]] === undefined) 
			{
				counts[arr[i]] = 1;
			} 
			else
			 {
			  return true;
			}
		}
		return false;
		}
		if(hasDuplicates(po_item_chk_arr)) {
		alert('Error: you have duplicates values !');
		release_freezing();
		return;
		}

		var z=1; var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('poNo_'+i+'*poQnty_'+i,'PO No*PO Qty')==false)
			{
				release_freezing();
				return; 
			}			
			data_all+="&poNo_" + z + "='" + $('#poNo_'+i).val()+"'"+"&poQnty_" + z + "='" + $('#poQnty_'+i).val()+"'"+"&shipdate_" + z + "='" + $('#shipdate_'+i).val()+"'"+"&rowid_" + z + "='" + $('#rowid_'+i).val()+"'";
			z++;
		}
		
		var data="action=save_update_delete_accpoinfo_v1&operation="+operation+"&total_row="+row_num+get_submitted_data_string('hid_po_id*txt_job_no*hid_job_id',"../../../")+data_all;
	
		http.open("POST","order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_acc_po_info_reponse;
	}

	function fnc_acc_po_info_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				var datalist=document.getElementById('hid_po_id').value+'__'+document.getElementById('txt_job_no').value;
				show_list_view( datalist,'accpo_list_view_v1','save_up_list_view','order_entry_controller','');//setFilterGrid(\'tbl_upListView\',-1)
				 var tableFilters_po = 
				{
					col_operation: { 
						id: ["total_po_qty"],
						col: [2],
						operation: ["sum"],
						write_method: ["innerHTML"]
					}
				}
				setFilterGrid("tbl_upListView",-1,tableFilters_po);
					  
				
				$('#tbl_list_search tbody tr:not(:first)').remove();
				$('#poNo_1').val("");
				$('#rowid_1').val("");
				$('#poQnty_1').val("");
				$('#shipdate_1').val("");
				$('#txtTotPoQnty').val("");
				set_button_status(0, permission, 'fnc_acc_po_info',1);
				release_freezing();
			}
		}
	}
	
	function fnc_poqty_cal()
	{
		var row_num = $('table#tbl_list_search tbody tr').length;
		var totQty=0;
		var poqty=$("#txt_po_qty").val()*1;
		for (var i=1; i<=row_num; i++)
		{
			if( ($("#poQnty_"+i).val()*1)>0)
			{
				totQty+=$("#poQnty_"+i).val()*1;
				console.log(totQty+'--'+poqty);
				if(totQty>poqty)
				{
					alert("Actual PO Qty Over from PO Quantity");
					$("#poQnty_"+i).val('');
					return;
				}
			}
		}		
		$("#txtTotPoQnty").val(totQty);
		
	}
	
	function get_temp_data(rowid)
	{
		get_php_form_data(rowid, 'populate_acc_details_data_v1', 'order_entry_controller');
	}
    </script>
	</head>
	<body>
	<div align="center">
	<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
	<div style="font-size:16px; color:#36F">Actual Po Entry Info</div>
	<fieldset style="width:450px">
    <form id="accpoinfo_1" autocomplete="off">
        <table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
            <thead>
                <th width="150" class="must_entry_caption">Act. PO Number</th>
                <th width="80" class="must_entry_caption">PO Qty.</th>
                <th width="70">Ship Date</th>
                <th>&nbsp;</th>
            </thead>
            <tbody>
                <tr class="general" id="tr_1">
                    <td align="center">
                        <input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" style="width:60px" value="" />
                        <input type="text" id="poNo_1" name="poNo_1" class="text_boxes" style="width:140px" value="" />
                    </td>                    
                    <td align="center"><input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:70px" value="" onBlur="fnc_poqty_cal();" /></td>
                    <td align="center"><input type="text" id="shipdate_1" name="shipdate_1" class="datepicker" style="width:60px" value=""/></td>
                    <td>
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th><input type="text" id="txtTotPoQnty" name="txtTotPoQnty" class="text_boxes_numeric" style="width:70px" value="<?=$totpoqty; ?>" disabled readonly/></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <div align="center" style="margin-top:10px">
           <?
		   if(count($data_array)>0)
			{
				echo load_submit_buttons( $permission, "fnc_acc_po_info", 1,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
			}
			else
			{
				echo load_submit_buttons( $permission, "fnc_acc_po_info", 0,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
			}
		   ?>
            <input type="hidden" id="hid_po_id" value="<?=$po_id; ?>" />
            <input type="hidden" id="txt_job_no" value="<?=$txt_job_no; ?>" />
            <input type="hidden" id="txt_po_qty" value="<?=$po_quantity; ?>" />
            <input type="hidden" id="hid_job_id" value="<?=$job_id; ?>" />
        </div>
        </form>
	</fieldset>
    <div id="save_up_list_view"></div>
    </div>
    </body>
   
    <script>
	show_list_view( '<?=$po_id.'__'.$txt_job_no; ?>','accpo_list_view_v1','save_up_list_view','order_entry_controller','');
	
	</script>       
     <script>
	 var tableFilters_po = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_po_qty"],
			col: [2],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>
     <script>
			setFilterGrid("tbl_upListView",-1,tableFilters_po);
		</script>  
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="save_update_delete_accpoinfo_v1")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$row_cond="";
	if($operation==1){
		$row_id="rowid_1";
		$accupdateid=str_replace("'","",$$row_id);
		$row_cond="and id <> $accupdateid";
	}
	$sql_po_chk=sql_select("select id, job_no po_break_down_id, acc_po_no, acc_ship_date, acc_po_qty from wo_po_acc_po_info where job_no=$txt_job_no and status_active=1 $row_cond");
	$accPoDataArr=array();
	foreach($sql_po_chk as $row)
	{
		if ($operation==0)
		{
			$accPoDataArr[$row[csf('acc_po_no')]][strtotime($row[csf('acc_ship_date')])]=$row[csf('acc_po_qty')];
		}
		else if ($operation==1)
		{
			$accPoDataArr[$row[csf('id')]][$row[csf('acc_po_no')]][strtotime($row[csf('acc_ship_date')])]=$row[csf('acc_po_qty')];
		}
	}
	unset($sql_po_chk);
	$hid_job_id=str_replace("'","",$hid_job_id);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		$id=return_next_id( "id", "wo_po_acc_po_info", 1);
		$field_array="id, job_no, job_id, po_break_down_id, acc_po_no, acc_po_qty, acc_ship_date,act_version, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1; $i<=$total_row; $i++)
		{
			$poNo="poNo_".$i;
			$poQnty="poQnty_".$i;
			$shipdate="shipdate_".$i;
			$rowid="rowid_".$i;
			$acc_poNo=str_replace("'","",$$poNo);
			$acc_shipdate=str_replace("'","",$$shipdate);

			//$ship_date =  date('M/d/Y/YYYY',strtotime($acc_shipdate));

			if(str_replace("'",'',$$shipdate)!="") $ship_dateCon=date("d-M-Y",strtotime(str_replace("'",'',$$shipdate))); else $ship_dateCon="";
			$acc_po_no_chk=$accPoDataArr[$acc_poNo][strtotime($ship_dateCon)];	
			if(($acc_po_no_chk*1)>0)
			{
				$msg="Error: You have duplicates values !";
				echo "11**".$msg;	
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$hid_job_id.",".$hid_po_id.",".$$poNo.",".$$poQnty.",'".$ship_dateCon."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		//echo "11**insert into wo_po_acc_po_info (".$field_array.") values ".$data_array; die;
		//check_table_status( $_SESSION['menu_id'],0); die;
		$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0) { echo "15**0"; disconnect($con); die;}

		$add_comma=0;
		$id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		$field_array="id, job_no, job_id, po_break_down_id, acc_po_no, acc_po_qty, acc_ship_date,act_version, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="acc_po_no*acc_po_qty*acc_ship_date*updated_by*update_date";
		//echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$poNo="poNo_".$i;			
			$poQnty="poQnty_".$i;
			$shipdate="shipdate_".$i;
			$rowid="rowid_".$i;
			
			$acc_poNo=str_replace("'","",$$poNo);			
			$acc_poQnty=str_replace("'","",$$poQnty);
			
			$acc_updateid=str_replace("'","",$$rowid);
			
			if(str_replace("'",'',$$shipdate)!="") $ship_dateCon=date("d-M-Y",strtotime(str_replace("'",'',$$shipdate))); else $ship_dateCon="";
			
			$acc_shipdate=str_replace("'","",$$shipdate);
			
			$acc_po_no_chk=$accPoDataArr[$acc_updateid][$acc_poNo][strtotime($acc_shipdate)];	
			if(($acc_po_no_chk*1)>0)
			{
				$msg="Error: You have duplicates values !.";
				echo "11**".$msg;	
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
			
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$poQnty."*'".$ship_dateCon."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			if(str_replace("'",'',$$rowid)=="")
			{
				if($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$hid_job_id.",".$hid_po_id.",".$$poNo.",".$$poQnty.",'".$ship_dateCon."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$add_comma++;
				$id=$id+1;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0); die;
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$flag=1;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array!="")
		{
			$rID1=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)  //Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(check_table_status( $_SESSION['menu_id'], 1 )==0) { echo "15**0"; disconnect($con); die;}
		$field_array_up="status_active*is_deleted*updated_by*update_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$poNo="poNo_".$i;
			$poQnty="poQnty_".$i;
			$rowid="rowid_".$i;
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}
if($action=="accpo_list_view_v1")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$exdata=explode("__",$data);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	?>
     <fieldset>
    <div style="width:300px;" align="center">
    <legend>Actual PO Info List View</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">Po No</th>
                <th width="60">PO Qty</th>
                <th>Ship Date</th>
            </thead>
     	</table>
        <div style="width:300px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="280" class="rpt_table" id="tbl_upListView" >
            
            <?
				$sql="select id, acc_po_no, acc_po_qty, acc_ship_date from wo_po_acc_po_info where po_break_down_id='$exdata[0]' and job_no='$exdata[1]' and status_active=1 and is_deleted=0";
				$sql_res=sql_select($sql);
				
				//print_r($mst_temp_arr);
				$i=1; $tot_qty=0;
				foreach($sql_res as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<?=$row[csf('id')]; ?>');">
                    	<td width="30" align="center"><?=$i; ?></td>
                        <td width="110" style="word-break:break-all"><?=$row[csf('acc_po_no')]; ?></td>
                        <td width="60" align="right"><?=$row[csf('acc_po_qty')]; ?></td>
                        <td style="word-break:break-all"><?=change_date_format($row[csf('acc_ship_date')]); ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qty+=$row[csf('acc_po_qty')];
				}
			?>
           
            </table>
        </div>
        <table width="300" class="tbl_bottom"  border="1" rules="all">
			 
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="110">&nbsp;</td>
          			<td width="60" id="total_po_qty" align="right"><strong><? echo number_format($tot_qty,0);?> </strong></td>
            		<td align="">&nbsp; </td>
					</tr>
				 
			</table>
            
       
        
     </div>
     </fieldset>
    <?
	exit();
}
if($action=="populate_acc_details_data_v1")
{
	$data_array=sql_select("select id, acc_po_no, acc_po_qty, acc_ship_date from wo_po_acc_po_info where id='$data' and status_active=1 and is_deleted=0");
	foreach($data_array as $row)
	{
		echo "$('#rowid_1').val('".$row[csf("id")]."');\n";
		echo "$('#poNo_1').val('".$row[csf("acc_po_no")]."');\n";
		echo "$('#poQnty_1').val('".$row[csf("acc_po_qty")]."');\n";
		echo "$('#txtTotPoQnty').val('".$row[csf("acc_po_qty")]."');\n";
		echo "$('#shipdate_1').val('".change_date_format($row[csf("acc_ship_date")])."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_acc_po_info',1);\n";
	}
	exit();
}

if ($action=="bh_style_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_id').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table cellspacing="0" width="1170" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>                	 
                <th width="150" class="must_entry_caption">Company Name</th>
				<th width="150" class="must_entry_caption">Working Factory</th>
                <th width="130">Buyer Name</th>
                <th width="80">BH Job No</th>
                <th width="90">Style Ref </th>
                <th width="90">Internal Ref</th>
                <th width="90">File No</th>
                <th width="90">Order No</th>
                <th width="130" colspan="2">Ship Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">BH Job Without PO</th> 
            </tr>          
        </thead>
        <tr class="general">
            <td> 
            <input type="hidden" id="selected_id">
            <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                <? 
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_by_buying_house_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
                ?>
            </td>
			<td>
				<? 
                    echo create_drop_down( "cbo_working_factory", 150, "select a.supplier_id,b.company_name from bh_wo_po_break_down a,lib_company b where a.supplier_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 group by a.supplier_id, b.company_name order by a.supplier_id", "supplier_id,company_name",1, "-- Select Company --","","",0 );
            	?>
			</td>
            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, "-- Select Buyer --" ); ?>	</td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
            <td align="center">
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_working_factory').value, 'create_bh_style_search_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
    	</tr>
        <tr class="general">
            <td align="center" valign="middle" colspan="11">
             <?=load_month_buttons(1);  ?>
            </td>
        </tr>
    </table>    
    <div id="search_div" align="center"></div>
    </form>
   </div>
</body>   
<script>
	<? if ($cbo_buyer_name!=0) { ?>
		load_drop_down('order_entry_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer', 'buyer_td' );
		document.getElementById('cbo_buyer_name').value=<?=$cbo_buyer_name; ?>;
		//document.getElementById('cbo_buyer_name').change();
	<? } ?>
    </script>        
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_bh_style_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[2]==0)
	{
		if ($data[0]!=0) $company=" and b.lc_company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	}else {if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }}
	if ($data[1]!=0){
		$buyer=" and a.buyer_name='$data[1]'"; 
	}
	else{
		$buyer=""; $bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_name in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond=""; $job_cond=""; $style_cond="";
	$style_data = strtolower($data[10]);

	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  ";  
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no)='$style_data'"; 
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data%'  ";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '$style_data%'  ";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data'  ";
	}
			
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	$working_factory = str_replace("'","",$data[13]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	if ($working_factory==0) $working_factory_cond=""; else $working_factory_cond=" and b.supplier_id='$working_factory' ";
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if($db_type==0)
	{
		$date_diff_cond="DATEDIFF(pub_shipment_date,po_received_date)";
		$year_select_cond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
	}
	else if($db_type==2)
	{
		$date_diff_cond="(pub_shipment_date - po_received_date)";
		$year_select_cond="to_char(a.insert_date,'YYYY')";
	}
	//$userbrand_idCond = " and id in ( $userbrand_id)";
	if($userbrand_idCond!="") $bhUserBrandCond=str_replace("and id","a.brand_id", $userbrand_idCond);
	//if($data_level_secured)
	//echo $data_level_secured.'d';
	if($data_level_secured==1)//Limit Access user // ===Issue Id=135 (2022 yr)======
	{
		$sqlTeam=sql_select("select b.id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and a.user_tag_id='$user_id' and a.status_active =1 and a.is_deleted=0");
		//$mktTeamId="";
		foreach($sqlTeam as $row){
			$mktTeamIdArr[$row[csf('id')]]=$row[csf('id')];
		}
		$mktTeamId=implode(",",$mktTeamIdArr);
		$mktTeamAccess="";
		if(count($mktTeamIdArr)>0) $mktTeamAccess=" and a.team_leader in($mktTeamId)";//Dont hide Issue id ISD-20-31821
	}
	else //All Acces user 
	{
		$mktTeamAccess="";	
	}
	 
	//echo $data[2].'D';
	if ($data[2]==0)
	{
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, b.lc_company_id,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity,b.supplier_id, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from bh_wo_po_details_master a, bh_wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $working_factory_cond $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $mktTeamAccess $bhUserBrandCond order by a.id DESC";
	}
	else
	{
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, $year_select_cond as year from bh_wo_po_details_master a where a.job_no not in( select distinct job_no_mst from bh_wo_po_break_down where status_active=1 and is_deleted=0 ) and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and a.is_deleted=0 $company $buyer $job_cond $style_cond $mktTeamAccess $bhUserBrandCond order by a.id DESC";
	}
	//echo $sql;
	$result=sql_select($sql);
	?>
	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" align="left" class="rpt_table" >
 			<thead>
 				<th width="30">SL</th>
                <th width="80">Company</th>
 				<th width="50">Year</th>
 				<th width="50">BH Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">BH Style Ref. No</th>
                <th width="80">BH Job Qty.</th>
                <th width="90">BH PO No</th> 
                <th width="80">BH PO Qty.</th>
 				<th width="65">Shipment Date</th>
 				<th width="70">Internal Ref</th>
 				<th width="70">File No</th>  
                <th width="85">Gmts Nature</th>             
 				<th>Lead time</th>               
 			</thead>
 		</table>
    	<div style="width:1020px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="list_view">  
 				<?
 				$i=1;
 				foreach ($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
 					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')]; ?>');"> 
                        <td width="30" align="center"><? echo $i; ?>  </td> 
                        <td width="80" style="word-break:break-all"><? echo $comp[$row[csf('company_name')]]; ?></p></td> 
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('year')]; ?></p></td>
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('job_quantity')]; ?></p></td>
                        <td width="90" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('po_quantity')]; ?></p></td>
                        <td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('file_no')]; ?></p></td>
                        <td width="85" style="word-break:break-all"><? echo $item_category[$row[csf('garments_nature')]]; ?></p></td>
                        <td style="word-break:break-all" align="center"><? echo $row[csf('date_diff')]; ?></p></td>
                    </tr> 
                    <? 
                    $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>
    <?php
	exit();
} 

if ($action=="populate_data_from_bh_style_popup")
{
	$update_period_id=$po_current_date_data=$cost_control_source=$set_smv_id=0;
	$company_id=return_field_value("company_name","bh_wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
	$sqlVariable=sql_select("select variable_list, po_update_period, po_current_date, cost_control_source, publish_shipment_date from variable_order_tracking where company_name ='$company_id' and variable_list in (32,33,47,53) and is_deleted=0 and status_active=1");
	
	foreach($sqlVariable as $result)
	{
		if($result[csf('variable_list')]==32) $update_period_id=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date_data=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
	}
	unset($sqlVariable);
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	
	$is_precost_found=return_field_value("job_no","wo_pre_cost_mst"," job_no ='$data' and is_deleted=0 and status_active=1");
	
	$sqlJob="select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, copy_from, company_name, buyer_name, location_name, style_ref_no, repeat_job_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, season_year, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant, style_owner, design_source_id, qlty_label, working_location_id, brand_id, sustainability_standard, fab_material, quality_level from bh_wo_po_details_master where job_no='$data'";
	//echo $sqlJob;
	$data_array=sql_select($sqlJob);
 
 	$company_id=$data_array[0][csf('company_name')];
	$team_leader=$data_array[0][csf('team_leader')];
	$dealing_marchant=$data_array[0][csf('dealing_marchant')];
	$factory_marchant=$data_array[0][csf('factory_marchant')];
	$quotation_id=$data_array[0][csf("quotation_id")];
	
	//echo $is_precost_found.'ddd';
	$color_qty=sql_select("select sum(order_quantity) as poQty from  bh_wo_po_color_size_breakdown where job_no_mst='$data' and status_active =1 and is_deleted=0");
	$colorQty=$color_qty[0][csf('poQty')];
	//echo $colorQty.'DSDS';;
	
	$team_arr=array(); $team_deal_arr=array(); $team_fact_arr=array();
	$tsql=sql_select("(select id, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 and id!=$team_leader) union all (select id, team_leader_name from lib_marketing_team where is_deleted=0 and id=$team_leader) order by team_leader_name ASC");
	foreach ($tsql as $row)
	{
		$team_arr[$row[csf("id")]]=$row[csf("team_leader_name")];
	}
	unset($tsql);
	$tmdsql=sql_select("(select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and status_active =1 and is_deleted=0 and id!=$dealing_marchant) union all (select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and is_deleted=0 and id=$dealing_marchant) order by team_member_name ASC");
 	foreach ($tmdsql as $row)
	{
		$team_deal_arr[$row[csf("id")]]=$row[csf("team_member_name")];
	}
	unset($tmdsql);
	
	$tmfsql=sql_select("(select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and status_active =1 and is_deleted=0 and id!=$factory_marchant) union all (select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and is_deleted=0 and id=$factory_marchant) order by team_member_name ASC");
 	foreach ($tmfsql as $row)
	{
		$team_fact_arr[$row[csf("id")]]=$row[csf("team_member_name")];
	}
	unset($tmfsql);
	
	foreach ($data_array as $row)
	{
		//echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		//echo "load_drop_down( 'requires/order_entry_controller', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		echo "sub_dept_load('".$row[csf("buyer_name")]."','".$row[csf("product_dept")]."');\n";

		//echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		//echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		//echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_sustainability_standard').value = '".$row[csf("sustainability_standard")]."';\n"; 
		echo "document.getElementById('cbo_fab_material').value = '".$row[csf("fab_material")]."';\n"; 
		echo "document.getElementById('cbo_order_nature').value = '".$row[csf("quality_level")]."';\n";
		echo "document.getElementById('cbo_product_group').value = '".$row[csf("product_group")]."';\n"; 
		
		echo "$('#cbo_buyer_name').attr('disabled',true);\n";
		//echo "$('#txt_style_ref').attr('disabled',true);\n";
		
		get_buyer_config($row[csf("buyer_name")]);
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n";
		echo "document.getElementById('po_update_period_maintain').value = '".$update_period_id."';\n";
		echo "document.getElementById('po_current_date_maintain').value = '".$po_current_date_data."';\n";
		
		$working_location_dropdown='';
		
		$working_location_dropdown = create_drop_down( "cbo_working_location_id", 130, "select id,location_name from lib_location where company_id='".$row[csf("working_company_id")]."' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "");
		
		echo "document.getElementById('cbo_working_factory').value = '".$row[csf("working_company_id")]."';\n";
		echo "document.getElementById('sew_location').innerHTML = '".$working_location_dropdown."';\n";
		echo "document.getElementById('cbo_working_location_id').value = '".$row[csf("working_location_id")]."';\n";
		
		$current_date=date('d-m-Y');
		if($po_current_date_data==1)
		{
			echo "document.getElementById('txt_po_received_date').value = '".$current_date."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";
		}
		else
		{
			echo "document.getElementById('txt_po_received_date').value = '';\n";
			echo "$('#txt_po_received_date').attr('disabled',false);\n";
		}

		if($row[csf("is_repeat")]==1)
		{
			echo "$('#chk_is_repeat').prop('checked', true);\n";
		}
		else
		{
			echo "$('#chk_is_repeat').prop('checked', false);\n";
		}
		echo "check_tna_templete('".$row[csf("buyer_name")]."');\n";
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";
		//echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		//echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('cbo_packing').value = '".$row[csf("packing")]."';\n";
		echo "document.getElementById('cbo_fit_id').value = '".$row[csf("fit_id")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_ship_mode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".number_format($row[csf("set_smv")],2)."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_job_qty').value = '".number_format($row[csf("job_quantity")])."';\n";
		echo "document.getElementById('txt_avgUnit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		echo "document.getElementById('txt_total_price').value = '".number_format($row[csf("total_price")],2)."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		
		//echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";
		//echo "document.getElementById('cbo_style_owner').value = '".$row[csf("style_owner")]."';\n";
		//echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "load_drop_down('requires/order_entry_controller', '".$row[csf("gmts_item_id")]."', 'load_drop_gmts_item', 'itm_td');\n";
	}
	$working_factory_qry=sql_select("select supplier_id from bh_wo_po_break_down where job_no_mst='$data' ");//issue_id 2497 
	foreach ($working_factory_qry as $row_data)
	{
		echo "document.getElementById('cbo_style_owner').value = '". $row_data[csf("supplier_id")]."';\n";
	}
	
	$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,

	sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from bh_wo_po_break_down where job_no_mst='$data' ");
	foreach ($projected_data_array as $row_val)
	{
		$job_projected_price=0;
		$job_projected_price=($row_val[csf("job_projected_total")]/$row_val[csf("job_projected_qty")])*1;
	    echo "document.getElementById('txt_proj_qty').value = '".number_format($row_val[csf("job_projected_qty")])."';\n";
		echo "document.getElementById('txt_proj_avgUnit_price').value = '".number_format($job_projected_price,4)."';\n";
		echo "document.getElementById('txt_proj_total_price').value = '".number_format($row_val[csf("job_projected_total")],2)."';\n";
		echo "document.getElementById('txt_orginProj_qty').value = '".number_format($row_val[csf("projected_qty")])."';\n";
		echo "document.getElementById('txt_orginProj_total_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_orginProj_total_amt').value = '".number_format($row_val[csf("projected_amount")],2)."';\n";
	}
	exit();
}

if ($action=="repeat_job_popup") //Repeat Job
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
?>
	<script>
	function js_set_value( job_no )
	{
		//alert(job_no);
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	
	function fnc_search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_by_td_up').html('Job No');
			else if(val==2) $('#search_by_td_up').html('Style Ref.');
			else if(val==3) $('#search_by_td_up').html('File No');
			else if(val==4) $('#search_by_td_up').html('Internal Ref');
			else if(val==5) $('#search_by_td_up').html('PO');
		}
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Search By</th>
                <th width="100">Style Ref </th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td> 
                <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1 ); ?>
            </td>
            <td id="buyer_td">
             <? echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?>	</td>
             <td>
				<?
                    $search_by_arr=array(1=>"Job",2=>"Style Ref.",3=>"File No",4=>"Int. Ref",5=>"PO",);
                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", 2,'fnc_search_by(this.value,2)',0 );
                ?>
            </td>
           	<td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" value="<? echo $txt_style_ref;?>" /></td>
            <td>
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_type').value, 'create_job_repeat_search_list_view', 'search_div', 'order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td align="center" valign="middle"><? //echo load_month_buttons(1);  ?></td>
        </tr>
     </table>
     <div id="search_div"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_repeat_search_list_view") //lib_file_creation
{
	//echo $data;die;
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$search_string=$data[2];
	$search_by=$data[3];
	
	if ($company!=0) $company_name=" and a.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0){
		$buyer=" and a.buyer_name='$buyer_id'"; 
	}
	else{
		$buyer="";
		$bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_name in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	//echo $buyer;
	$search_byCond="";
	if(trim($search_string)!="")
	{
		if($search_by==1) $search_byCond=" and a.job_no_prefix_num='$search_string'";
		else if($search_by==2) $search_byCond=" and a.style_ref_no='$search_string'";
		else if($search_by==3) $search_byCond=" and b.file_no='$search_string'";
		else if($search_by==4) $search_byCond=" and b.grouping='$search_string'";
		else if($search_by==5) $search_byCond=" and b.po_number='$search_string'";
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	
	$year_field="to_char(a.insert_date,'YYYY')";
					
	$sql= "select a.id, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, $year_field  as year, b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.status_active=1 and b.status_active=1 and b.status_active=1 $company_name $search_byCond group by a.id, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.insert_date, b.grouping order by a.id desc";
		//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Style Ref.,Int. Ref.", "130,60,130,120","500","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0,", $arr , "job_no,year,style_ref_no,grouping", "",'','0,0,0,0,0');
	exit();
}

?>