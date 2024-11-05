<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

$company_location_cond=set_user_lavel_filtering(' and id','company_location_id');

$user_id=$_SESSION['logic_erp']['user_id'];
$data_level_secured=$_SESSION['logic_erp']["data_level_secured"];

	//========== user credential start ========
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");

	$location_id = $userCredential[0][csf('location_id')];
	$location_credential_cond="";

	if ($location_id !='') {
	    $location_credential_cond = " and id in($location_id)";
	}

//************************************ Start*************************************************
// Master Form*************************************Master Form*************************

function fnc_file_no_action($data){
	$file_no_vari=return_field_value("internal_file_source", "variable_settings_commercial", "company_name=$data  and variable_list=20  and status_active=1 and is_deleted=0");
	if($file_no_vari !="") return trim($file_no_vari); else return 0;
}
//$file_no_vari=return_field_value("internal_file_source", "variable_settings_commercial", "company_name=$data  and variable_list=20  and status_active=1 and is_deleted=0");
	
function publish_shipment_date($data){
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");
	if($publish_shipment_date !="") return trim($publish_shipment_date); else return 1;
}
function act_po_data($data){
	$act_po_data=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=93  and status_active=1 and is_deleted=0");
	if($act_po_data !="") return trim($act_po_data); else return 2;
}

function update_period_maintained_data($data){
	$po_update_period=0;
	$po_update_period_user_id="";
	$sql=sql_select("select po_update_period,user_id from variable_order_tracking where company_name ='$data' and variable_list=32 and is_deleted=0 and status_active=1");
	foreach($sql as $row){
		if($row[csf('po_update_period')]){
			$po_update_period=$row[csf('po_update_period')];
			$po_update_period_user_id=$row[csf('user_id')];
		}
	}
	return  array ("po_update_period"=>$po_update_period,"user_id"=>$po_update_period_user_id);
}

function po_received_date_maintained_data($data){
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$data' and variable_list=33 and is_deleted=0 and status_active=1");
	if($po_current_date_data==""){
		$po_current_date_data=0;
	}else{
		$po_current_date_data=$po_current_date_data;
	}
	return $po_current_date_data;
}

function copy_quotation($data){
	$copy_quotsql=sql_select("select variable_list, copy_quotation, cost_control_source, publish_shipment_date from variable_order_tracking where company_name=$data and variable_list in (20,47,53)  and status_active=1 and is_deleted=0");
	foreach($copy_quotsql as $row)
	{
		if($row[csf('variable_list')]==20) $copy_quotation=$row[csf('copy_quotation')];
		else if($row[csf('variable_list')]==47) $set_smv_id=$row[csf('publish_shipment_date')];
		else if($row[csf('variable_list')]==53) $cost_control_source=$row[csf('cost_control_source')];
	}
	return $copy_quotation."_".$cost_control_source."_".$set_smv_id;
}

function season_mandatory($data){
	//echo "select season_mandatory from variable_order_tracking where company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0";
	$season_mandatory=return_field_value("season_mandatory", "variable_order_tracking", "company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0");
	if($season_mandatory !="") return trim($season_mandatory); else return 2;
}
if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=130; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if($action=="load_drop_down_gmtssize"){
	$data_arr = explode("_", $data);
	echo create_drop_down( "cbogmtssize_$data_arr[1]", 80, "select a.id, a.size_name, b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$data_arr[3]' and b.item_number_id='$data_arr[0]' and b.color_number_id='$data_arr[2]' group by a.id, a.size_name, b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $selected,"get_unit_price(this.value,$data_arr[1],4)");
	exit();
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
	 
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value);" ); 
	
	$cbo_agent= create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	$cbo_client= create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );

	$publish_shipment_date=publish_shipment_date($data);
	
	$update_period_maintained_data=update_period_maintained_data($data);
	$po_received_date_maintained_data=po_received_date_maintained_data($data);
	$copy_quotation_data=copy_quotation($data);
	$act_po_data=act_po_data($data);
	
	$excopy_quotation=explode("_",$copy_quotation_data);
	$copy_quotation=$excopy_quotation[0];
	$cost_control_source=$excopy_quotation[1];
	$style_smv_source=$excopy_quotation[2];
	$sew_company_location=$excopy_quotation[3];
	
	$season_mandatory=season_mandatory($data);
	
	
	echo "document.getElementById('location').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	echo "document.getElementById('party_type_td').innerHTML = '".$cbo_client."';\n";
	echo "publish_shipment_date(".$publish_shipment_date.");\n";
	
	echo "budget_exceeds_quot('".$copy_quotation.'_'.$cost_control_source.'_'.$style_smv_source."');\n";
	echo "document.getElementById('po_update_period_maintain').value = '".$update_period_maintained_data['po_update_period']."';\n";
	echo "document.getElementById('txt_user_id').value = '".$update_period_maintained_data['user_id']."';\n";
	echo "document.getElementById('po_current_date_maintain').value = '".$po_received_date_maintained_data."';\n";
	
	if($sew_company_location=="" || $sew_company_location==0) $sew_company_location=0; else $sew_company_location=$sew_company_location;
	echo "document.getElementById('sewing_company_validate_id').value 	= '".$sew_company_location."';\n";
	
	if($style_smv_source=="" || $style_smv_source==0) $style_smv_source=0; else $style_smv_source=$style_smv_source;
	echo "document.getElementById('set_smv_id').value 		= '".$style_smv_source."';\n"; 
	echo "document.getElementById('hid_cost_source').value 		= '".$cost_control_source."';\n";
	echo "document.getElementById('is_season_must').value = '".$season_mandatory."';\n";
	echo "document.getElementById('act_po_id').value = '".$act_po_data."';\n";
	$fnc_file_no=fnc_file_no_action($data);
	echo "fnc_file_no_check(".$fnc_file_no.");\n";
}

if($action=="get_company_config"){
	$action($data);
}

if ($action=="load_drop_down_location")
{
	$sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0  order by location_name";
	$result=sql_select($sql);
	$index=$selected;
	if(count($result)==1)
	{
		$index=$result[0][csf('id')];
	}
	echo create_drop_down( "cbo_location_name", 130, $sql,"id,location_name", 1, "-- Select --", $index, "" );
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
	//echo $sql."**".$index;
	echo create_drop_down( "cbo_working_location_id", 130, $sql,"id,location_name", 1, "-- Select --", $index, "" );	
	exit();		 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value);check_tna_templete(this.value)" ); 
	exit();	  	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	exit();	
} 

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" ); 
	exit();	 
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 130, "select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 130, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
}

if ($action=="load_drop_down_projected_po")
{
	echo create_drop_down( "cbo_projected_po", 100, "select id,po_number from  wo_po_break_down where job_no_mst='$data'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
}

if($action=="publish_shipment_date")
{
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");

	if($publish_shipment_date !="") echo trim($publish_shipment_date); else echo 1;
	die;
}
if($action=="act_po_data")
{
	$act_po_data=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=93  and status_active=1 and is_deleted=0");

	if($act_po_data !="") echo trim($act_po_data); else echo 2;
	die;
}
if($action=="file_no_action")
{
	$file_no_vari=return_field_value("internal_file_source", "variable_settings_commercial", "company_name=$data  and variable_list=20  and status_active=1 and is_deleted=0");

	if($file_no_vari !="") echo trim($file_no_vari); else echo 0;
	die;
}

if($action=="is_of_day")
{
	$data=explode("_",$data);
	if($db_type==0)
	{
		$txt_pub_shipment_date=change_date_format($data[1],'yyyy-mm-dd','-');
		$txt_org_shipment_date=change_date_format($data[2],'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$txt_pub_shipment_date=change_date_format($data[1],'','-',1);
		$txt_org_shipment_date=change_date_format($data[2],'','-',1);
    }
	$txt_pub_shipment=1;
	$txt_org_shipment=1;
	//echo "select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_pub_shipment_date' and a.status_active=1 and a.is_deleted=0";
	$is_of_day=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data[0]  and variable_list=46  and status_active=1 and is_deleted=0");
	if($is_of_day==2){
		//$txt_pub_shipment=return_field_value("day_status", "lib_capacity_calc_dtls", "date_calc='$txt_pub_shipment_date'");
		//$txt_org_shipment=return_field_value("day_status", "lib_capacity_calc_dtls", "date_calc=$txt_org_shipment_date");
		$txt_pub_shipment_sql=sql_select("select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_pub_shipment_date' and a.status_active=1 and a.is_deleted=0");
		$txt_pub_shipment=$txt_pub_shipment_sql[0][csf('day_status')];
		$txt_org_shipment_sql=sql_select("select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_org_shipment_date' and a.status_active=1 and a.is_deleted=0");
		$txt_org_shipment=$txt_org_shipment_sql[0][csf('day_status')];
		
	}
	echo $txt_pub_shipment."_".$txt_org_shipment;
	die;
}

if($action=="update_period_maintained_data")
{
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$data' and variable_list=32 and is_deleted=0 and status_active=1");
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	echo "document.getElementById('po_update_period_maintain').value 				= '".$update_period_id."';\n";
	exit();	
}
if($action=="po_received_date_maintained_data")
{
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$data' and variable_list=33 and is_deleted=0 and status_active=1");
	if($po_current_date_data=="") $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	echo "document.getElementById('po_current_date_maintain').value 				= '".$po_current_date_data."';\n";
	exit();	
}

if($action=="copy_quotation")
{
	$copy_quotsql=sql_select("select variable_list, copy_quotation, cost_control_source, publish_shipment_date, season_mandatory from variable_order_tracking where company_name=$data and variable_list in (20,47,53,64)  and status_active=1 and is_deleted=0");
	$copy_quotation=$set_smv_id=$cost_control_source=$sew_company_location=0;
	foreach($copy_quotsql as $row)
	{
		if($row[csf('variable_list')]==20) $copy_quotation=$row[csf('copy_quotation')];
		else if($row[csf('variable_list')]==47) $set_smv_id=$row[csf('publish_shipment_date')];
		else if($row[csf('variable_list')]==53) $cost_control_source=$row[csf('cost_control_source')];
		else if($row[csf('variable_list')]==64) $sew_company_location=$row[csf('season_mandatory')];
	}
	echo $copy_quotation."_".$cost_control_source."_".$set_smv_id."_".$sew_company_location;
	
	exit();
}

if ($action=="order_popup")
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
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table cellspacing="0" width="1020" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>                	 
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="130">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="90">Style Ref </th>
                <th width="90">Internal Ref</th>
                <th width="90">File No</th>
                <th width="90">Order No</th>
                <th width="130" colspan="2">Ship Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th> 
            </tr>          
        </thead>
        <tr class="general">
            <td> 
            <input type="hidden" id="selected_job">
            <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                <? 
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
    	</tr>
        <tr class="general">
            <td align="center" valign="middle" colspan="10">
             <?=load_month_buttons(1);  ?>
            </td>
        </tr>
    </table>    
    <div id="search_div" align="center"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0){
		$buyer=" and a.buyer_name='$data[1]'"; 
	}
	else{
		$buyer="";
		$bu_arr=array();
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
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
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
	//if($data_level_secured)
	//echo $data_level_secured.'d';
	if($data_level_secured==1)//Limit Access user // ===Issue Id=135 (2022 yr)======
	{
	echo $sqlTeam=sql_select("select b.id from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and a.data_level_security=1 and a.user_tag_id='$user_id' and a.status_active =1 and a.is_deleted=0");
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
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $mktTeamAccess order by a.id DESC";
	}
	else
	{
		$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, $year_select_cond as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond $mktTeamAccess order by a.id DESC";
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
 				<th width="50">Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref. No</th>
                <th width="80">Job Qty.</th>
                <th width="90">PO number</th> 
                <th width="80">PO Qty.</th>
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
                    <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')];?>')"> 
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

if ($action=="populate_data_from_search_popup")
{
	$update_period_id=$po_current_date_data=$cost_control_source=$set_smv_id=0;
	$company_id=return_field_value("company_name","wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
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
	
	$data_array=sql_select("select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, copy_from, company_name, buyer_name, location_name, style_ref_no, repeat_job_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, season_year, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant, style_owner, design_source_id, qlty_label, working_location_id, brand_id, sustainability_standard, fab_material, quality_level, po_tna_lead_time, requisition_no from wo_po_details_master where job_no='$data'");
 
 	$company_id=$data_array[0][csf('company_name')];
	$team_leader=$data_array[0][csf('team_leader')];
	$dealing_marchant=$data_array[0][csf('dealing_marchant')];
	$factory_marchant=$data_array[0][csf('factory_marchant')];
	$quotation_id=$data_array[0][csf("quotation_id")];
	
	//echo $is_precost_found.'ddd';
	$color_qty=sql_select("select sum(order_quantity) as poQty from  wo_po_color_size_breakdown where job_no_mst='$data'  and status_active =1 and is_deleted=0");
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
		$cbo_team_leader= create_drop_down( "cbo_team_leader", 130, $team_arr,"", 1, "-- Select Team --", $selected, "load_drop_down( \'requires/woven_order_entry_controller\', this.value, \'cbo_dealing_merchant\', \'div_marchant\' ); " );//load_drop_down( \'requires/woven_order_entry_controller\', this.value, \'cbo_factory_merchant\', \'div_marchant_factory\' )
		$cbo_dealing_merchant= create_drop_down( "cbo_dealing_merchant", 130, $team_deal_arr,"", 1, "-- Select Team Member --", $selected, "" );
		//$cbo_factory_merchant= create_drop_down( "cbo_factory_merchant", 140, $team_fact_arr,"", 1, "-- Select Team Member --", $selected, "" );
		
		$cbo_projected_po= create_drop_down( "cbo_projected_po", 100, "select id,po_number from  wo_po_break_down where job_no_mst='".$row[csf("job_no")]."'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
		
		//$active_po_list=show_po_active_listview($row[csf("job_no")]);
		echo "document.getElementById('div_teamleader').innerHTML = '".$cbo_team_leader."';\n";
		echo "document.getElementById('div_marchant').innerHTML = '".$cbo_dealing_merchant."';\n";
		//echo "document.getElementById('div_marchant_factory').innerHTML = '".$cbo_factory_merchant."';\n";
		echo "document.getElementById('projected_po_td').innerHTML = '".$cbo_projected_po."';\n";
		$tot_poQty=$row[csf("job_quantity")];
		//echo $tot_poQty.'='.$colorQty.'d';
		if($row[csf("order_uom")]==58){
			$tot_poQty=$row[csf("job_quantity")]*$row[csf('total_set_qnty')];
		}
		if($tot_poQty!=$colorQty)
		{
		 echo "fnc_poQty_chk(1);\n";
		}
		echo "document.getElementById('hid_colorQty').value = '".$colorQty."';\n";
		//echo "document.getElementById('po_list_view').innerHTML = '".$active_po_list."';\n";
		
		get_company_config($row[csf("company_name")]);
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";

		echo "document.getElementById('hidd_job_id').value = '".$row[csf("id")]."';\n"; 


		echo "document.getElementById('cbo_order_nature').value = '".$row[csf("quality_level")]."';\n"; 
		echo "document.getElementById('cbo_po_tna_lead_time').value = '".$row[csf("po_tna_lead_time")]."';\n";
		echo "document.getElementById('sustainability_standard').value = '".$row[csf("sustainability_standard")]."';\n"; 
		echo "document.getElementById('cbo_fab_material').value = '".$row[csf("fab_material")]."';\n"; 
		echo "document.getElementById('txt_requision_no').value = '".$row[csf("requisition_no")]."';\n"; 

		//echo "document.getElementById('txt_style_ref').setAttribute ('value', '".$row[csf("style_ref_no")]."');\n";
		echo "document.getElementById('txt_copy_form').value = '".$row[csf("copy_from")]."';\n";
		 echo "document.getElementById('cbo_design_source_id').value = '".$row[csf("design_source_id")]."';\n";  
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";  
		 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n"; 
		echo "document.getElementById('txt_repeat_job_no').value = '".$row[csf("repeat_job_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('buyer_name')]."_".$row[csf('product_dept')]."', 'load_drop_down_sub_dep', 'sub_td' );\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_bhmerchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n"; 
		echo "document.getElementById('po_update_period_maintain').value = '".$update_period_id."';\n"; 
		echo "document.getElementById('po_current_date_maintain').value = '".$po_current_date_data."';\n"; 
		echo "document.getElementById('cbo_working_company_id').value = '".$row[csf("style_owner")]."';\n";  
		echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('style_owner')]."', 'load_drop_down_sew_location', 'sew_location' );\n";
		echo "document.getElementById('cbo_working_location_id').value = '".$row[csf("working_location_id")]."';\n";  
		
		//echo "$('#cbo_company_name').attr('disabled',true);\n"; 
		//echo "get_company_config(".$row[csf("company_name")].");set_smv_check(".$row[csf("company_name")].");\n";
		$current_date=date('d-m-Y');
		if($po_current_date_data==1){
			echo "document.getElementById('txt_po_received_date').value = '".$current_date."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		}
		else{
			echo "document.getElementById('txt_po_received_date').value = '';\n"; 
			echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		}
		if($is_precost_found)
		{
			echo "$('#cbo_design_source_id').attr('disabled',true);\n";   
		}
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n"; 
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n"; 
		echo "document.getElementById('cbo_packing').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";  
		echo "document.getElementById('cbo_ship_mode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_total_job_quantity').value = '".$row[csf("job_quantity")]."';\n";
		//echo "document.getElementById('set_pcs').value = '".$unit_of_measurement[$row[csf("order_uom")]]."';\n";
		echo "document.getElementById('set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('pojected_set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('currpojected_set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_avg_unit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		//echo "document.getElementById('set_unit').value = '".$currency[$row[csf("currency_id")]]."';\n";
		echo "document.getElementById('set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('projected_set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('currprojected_set_unit').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('txt_job_total_price').value = '".$row[csf("total_price")]."';\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller','".$row[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "load_drop_down( 'requires/woven_order_entry_controller','".$row[csf("buyer_name")]."*1"."', 'load_drop_down_brand', 'brand_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";  
		//echo "set_smv_check($row[csf("company_name")])";
	}
	
	if($cost_control_source==2)
	{
		$sql_qRate=sql_select("select sum(price_with_commn_pcs) as rate from wo_price_quotation_costing_mst where quotation_id='$quotation_id' group by quotation_id");

		$qutation_rate=$sql_qRate[0][csf("rate")];
		//echo "document.getElementById('txt_quotation_price').value = '".$qutation_rate."';\n";
	}
	
	$projected_data_array=sql_select("select sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$data'");
	foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_projected_job_quantity').value = '".$row_val[csf("projected_qty")]."';\n";
		echo "document.getElementById('txt_projected_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_project_total_price').value = '".$row_val[csf("projected_amount")]."';\n";
	}
	
	$projected_data_array=sql_select("select sum(po_quantity) as po_qty, sum(po_quantity*unit_price) as po_amount, (sum(po_quantity*unit_price)/sum(po_quantity)) as po_rate from wo_po_break_down where job_no_mst='$data' and is_confirmed=2");
	foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_currprojected_job_qnty').value = '".$row_val[csf("po_qty")]."';\n";
		echo "document.getElementById('txt_currprojected_price').value = '".number_format($row_val[csf("po_rate")],4)."';\n";
		echo "document.getElementById('txt_currproject_total_price').value = '".$row_val[csf("po_amount")]."';\n";
	}
	
	$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no='$data' ", "internal_ref"  ), 0, -1);
	echo "internal( '".$internal."' ) ;\n"; 
}

if ($action=="file_no_popup") //File No from File Creation Page
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
?>
	<script>
	function js_set_value( file_no )
	{
		//alert(job_no);
		document.getElementById('selected_file_no').value=file_no;
		parent.emailwindow.hide();
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
                <th width="110"> File Type </th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td> 
                <input type="hidden" id="selected_file_no">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",0 ); ?>
            </td>
            <td id="buyer_td">
             <? echo create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
             <td width="110">
         <?
                        $file_type = array(1 => "Yarn Procurement", 2=>"Projection Order", 3=>"Confirm Order");
                        echo create_drop_down( "cbo_file_type", 162, $file_type,"", 1, "-- Select Type --", 0, "" );
           ?>
              </td>
             <td>
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_file_type').value, 'create_file_no_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? //echo load_month_buttons(1);  ?></td>
        </tr>
     </table>
     <div id="search_div"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script> $("#cbo_buyer_name").val(<? echo $cbo_buyer_name;?>); </script>
</html>
<?
exit();
}

if($action=="create_file_no_search_list_view") //
{
	//echo $data;die;
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	//echo $buyer_id.'=DD';
	
	if ($company!=0) $company_name=" and a.company_id='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0){
		$buyerCond=" and a.buyer_id='$buyer_id'"; 
	}
	else{
		$buyerCond="";
		$bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_id in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	//echo $buyer;
	//$style_cond="";
	//if (trim($style)!="") $style_cond=" and a.style_ref_no='$style'  "; //else  $style_cond=""; 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$buyer_arr,9=>$item_category);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					
	 $sql= "select a.file_no,a.buyer_id,a.file_year from lib_file_creation a  where   a.status_active=1 and a.status_active=1 $company_name $buyerCond   order by a.file_no desc";
		//echo $sql;
	echo  create_list_view("list_view", "File,File Year,Buyer.", "150,70,110","500","320",0, $sql , "js_set_value", "file_no", "", 1, "0,0,buyer_id", $arr , "file_no,file_year,buyer_id", "",'','0,0,0,0');
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
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td> 
                <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1 ); ?>
            </td>
            <td id="buyer_td">
             <? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?>	</td>
           
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>"></td>
             <td>
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value, 'create_job_repeat_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? //echo load_month_buttons(1);  ?></td>
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
	$style=$data[2];
	
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
	$style_cond="";
	if (trim($style)!="") $style_cond=" and a.style_ref_no='$style'  "; //else  $style_cond=""; 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					
	$sql= "select a.job_no,a.buyer_name,a.style_ref_no,a.job_quantity,$year_field from wo_po_details_master a  where   a.status_active=1 and a.status_active=1 $company_name   $style_cond  order by a.job_no desc";
		//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Style Ref.", "130,70,130","500","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0", $arr , "job_no,year,style_ref_no", "",'','0,0,0,0');
}

if ($action=="order_popup_for_copy")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( po_id )
	{
		document.getElementById('po_id').value=po_id;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 <input type="hidden" id="po_id">
 <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <tr>
            <td align="center" valign="top" id="search_div"> 
            <?
			$arr=array (0=>$order_status,11=>$row_status);
			if($db_type==0)
			{
				 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no'"; 
			}
			
			if($db_type==2)
			{
				 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(pub_shipment_date-po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no'"; 
			}
			 
			echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50","1050","220",0, $sql , "js_set_value", "id", "", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,2,2,2,2,1') ;
			?>
            </td>
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

function show_po_active_listview($data)
{
	global $db_type;
	$arr=array (0=>$order_status,12=>$row_status);
	
	if($db_type==0)
	{
 	 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC ";
	 
	}
	
	if($db_type==2)
	{
 	    $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(pub_shipment_date-po_received_date) as  date_diff,(pub_shipment_date-po_received_date) as fac_date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC"; 
	
	}
	return  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "60,110,70,70,70,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,fac_date_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,2,1') ;
}

if ($action=="load_drop_down_tna_task")
{
	$sql_task = "SELECT a.id,task_template_id, lead_time, material_source, total_task, tna_task_id, deadline, execution_days, notice_before, a.sequence_no, for_specific, b.task_catagory, b.task_name,b.task_sequence_no FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific,lead_time";
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
	$data=explode("_",$data);
	$remain_days=datediff( "d", $data[0], $data[1] );
	$template_id=get_tna_template($remain_days,$tna_template,$data[2]);
	   //echo $template_id; 
	//echo $data[0].'='.$data[1].'='.$remain_days;
	
	echo create_drop_down( "cbo_tna_task", 80, "select b.task_name,b.task_short_name from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.task_name and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.task_type=1  order by b.task_sequence_no","task_name,task_short_name", 1, "-- Select --", "", "" );
	exit();
}

if($action=="check_tna_leadtime")
{
	$data=explode("_",$data);
	$txt_po_received_date=date('Y-m-d',strtotime($data[2]));
    $txt_pub_shipment_date=date('Y-m-d',strtotime($data[3]));
    $dDiff=datediff( 'd', $txt_po_received_date, $txt_pub_shipment_date, $using_timestamps = false );
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific  from tna_task_template_details where for_specific=$data[0] and lead_time<= '$dDiff'  and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp){
		if($row_temp[csf('for_specific')]>0) $temp=1; else $temp=0;
	}
	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1]  and variable_list=14  and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	
	$tna_process_type=return_field_value("tna_process_type", "variable_order_tracking", "company_name=$data[1]  and variable_list=31  and status_active=1 and is_deleted=0");
    if($tna_process_type==1) $tna_process=1; else $tna_process=0;
	
	echo $temp."_".$tna."_".$dDiff."_".$tna_process;
	die;
}

if($action=="check_tna_templete")
{
	$data=explode("_",$data);
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific from tna_task_template_details where for_specific=$data[0] and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp){
		if($row_temp[csf('for_specific')]>0) $temp=1; else $temp=0;
	}
	//echo $temp;	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1]  and variable_list=14  and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	
	$tna_process_type=return_field_value("tna_process_type", "variable_order_tracking", "company_name=$data[1]  and variable_list=31  and status_active=1 and is_deleted=0");
    if($tna_process_type==1) $tna_process=1; else $tna_process=0;
	
	echo $temp."_".$tna."_".$tna_process;
	die;
}

if ($action=="show_po_active_listview")
{
	$shipmentStatusArr = array(0 => "Full Pending", 1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");
	$arr=array (0=>$order_status,12=>$row_status,13=>$shipmentStatusArr);
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(pub_shipment_date,po_received_date) as  date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (pub_shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	//echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qty.,Avg. Rate,Amount, Excess Cut %,Plan Cut Qty.,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	//total_set_qnty
	$sql_po="select b.order_quantity,b.po_break_down_id  as po_id from wo_po_color_size_breakdown b where   b.job_no_mst='$data' and b.status_active=1 and b.is_deleted=0";
	$sql_po_color=sql_select($sql_po);
	
	//$poColorQty=0;
	foreach ($sql_po_color as $row)
	{ 
	$poColorQtyArr[$row[csf('po_id')]]+=$row[csf('order_quantity')];
	}
	$sql_job="select a.total_set_qnty from wo_po_details_master a where a.job_no='$data' and a.status_active=1 and a.is_deleted=0";
	$sql_job=sql_select($sql_job);
	$total_set_qnty=$sql_job[0][csf('total_set_qnty')];
	//$poColorQty=0;
	 
	
	?>
    <div align="left" style=" margin-left:5px;"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" align="left" class="rpt_table" >
 			<thead>
 				<th width="20">SL</th>
                <th width="70">Order Status</th>
 				<th width="100">PO No</th>
 				<th width="65">PO Recv. Date</th>               
 				<th width="65">Ship Date</th>
                <th width="65">Orgn. Ship Date</th>
                <th width="70">PO Qty.</th>
                <th width="60">Avg. Rate</th> 
                <th width="70">Amount</th>
 				<th width="50">Excess Cut %</th>
 				<th width="70">Plan Cut Qty</th>
 				<th width="40">Lead Time</th>  
                <th width="40">Lead time on Fac Rcv Date</th>
                <th width="70">Status</th>
                <th>Ship Status</th>             
 				              
 			</thead>
 		</table>
    	<div style="width:950px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="list_view">  
 				<?
				// get_details_form_data(theemail.value,'populate_order_details_form_data','requires/woven_order_entry_controller')
 				$i=1;
				$sql_result=sql_select($sql);
 				foreach ($sql_result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					$poColorQty=$poColorQtyArr[$row[csf('id')]];
					$poQty=$row[csf('po_quantity')]*$total_set_qnty;
				
					if($poColorQty!=$poQty)
					{
						//fnc_poQty_chk(type)
						$bgcolor="#FFFF00";
					}
					else
					{
						$bgcolor=$bgcolor;	
					}
 					?>
                    <tr id="" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="get_details_form_data('<? echo $row[csf('id')];?>','populate_order_details_form_data','requires/woven_order_entry_controller')"> 
                        <td width="20" align="center"><? echo $i; ?>  </td> 
                        <td width="70" style="word-break:break-all"><? echo $order_status[$row[csf('is_confirmed')]]; ?></p></td> 
                        <td width="100" style="word-break:break-all" align="center"><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="65" style="word-break:break-all" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></p></td>
                        <td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
                        <td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
                        <td width="70" style="word-break:break-all" title="<? echo $poColorQty; ?>" align="right"><? echo $row[csf('po_quantity')]; ?></p></td>
                        <td width="60" style="word-break:break-all"  align="right"><? echo $row[csf('unit_price')]; ?></p></td>
                        <td width="70" style="word-break:break-all" align="right"><? echo number_format($row[csf('po_total_price')],4); ?></p></td>
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('excess_cut')]; ?></p></td>
                        <td width="70" style="word-break:break-all"  align="right"><? echo $row[csf('plan_cut')]; ?></p></td>
                        <td width="40" style="word-break:break-all" align="center"><? echo $row[csf('date_diff')]; ?></p></td>
                        <td width="40" style="word-break:break-all" align="center"><? echo $row[csf('facdate_diff')]; ?></p></td>
                        <td width="70" style="word-break:break-all"  align="center"><? echo $row_status[$row[csf('status_active')]]; ?></p></td>
                        <td style="word-break:break-all" align="center"><? echo $shipmentStatusArr[$row[csf('shiping_status')]]; ?></p></td>
                    </tr> 
                    <? 
                    $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>
    <?
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (shipment_date-po_received_date) as  date_diff,(pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	//echo $sql;
	$sqldata=sql_select($sql);
	if(count($sqldata)>0){
		echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	}
	exit();
}

if ($action=="show_po_active_listview_not_used")
{
	$shipmentStatusArr = array(0 => "Full Pending", 1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");
	$arr=array (0=>$order_status,12=>$row_status,13=>$shipmentStatusArr);
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(pub_shipment_date,po_received_date) as  date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (pub_shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qty.,Avg. Rate,Amount, Excess Cut %,Plan Cut Qty.,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (shipment_date-po_received_date) as  date_diff,(pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	//echo $sql;
	$sqldata=sql_select($sql);
	if(count($sqldata)>0){
		echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	}
	exit();
}

if ($action=="show_deleted_po_active_listview"){
	$arr=array (0=>$order_status,12=>$row_status);
	if($db_type==0){
	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	}
	if($db_type==2){
	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "70,130,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Woven Order Entry","../../../", 1, 1, $unicode);
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
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down('woven_order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                    </td>
                    <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
                    <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+'<? echo $txt_job_no; ?>', 'create_quotation_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" height="40" colspan="6"><?=load_month_buttons(1); ?></td>
                </tr>
            </table>
        </form>
    </div>
    <div id="search_div"></div>
    </body> 
    <script>
		load_drop_down('woven_order_entry_controller', <?=$cbo_company_name; ?>, 'load_drop_down_buyer', 'buyer_pop_td' );
		document.getElementById('cbo_buyer_name').value=<?=$cbo_buyer_name; ?>;
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";// else { echo "Please Select Buyer First."; die; }
	//echo $buyer."mmmm";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	
	$style_cond=""; $quotation_id_cond="";
	if($data[4]==1)
	{
	   if (trim($data[5])!="") $quotation_id_cond=" and id='$data[5]'";
	   if (trim($data[6])!="") $style_cond=" and style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]%' ";
	  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '$data[5]%' "; 
	  if (trim($data[6])!="") $style_cond=" and style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]' ";
	  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]' "; 
	}
	
	$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
	$app_nessity=2; $validate_page=0; $allow_partial=2;
	foreach($sql as $row){
		$app_nessity=$row[csf('approval_need')];
		$validate_page=$row[csf('validate_page')];
		$allow_partial=$row[csf('allow_partial')];
	}
	
	$quotAppCond="";
	if($validate_page==1 && $app_nessity==1)
	{
		 if($allow_partial==1) $quotAppCond=" and approved in (1,3)";
		 else $quotAppCond=" and approved=1";
	}
		
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	 $sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from wo_price_quotation a where status_active=1  and is_deleted=0 $company $buyer $style_cond $quotation_id_cond $quotAppCond order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="requisition_pop_up")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('requisition_no').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
		<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
	        <table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	            <thead>
	                <th colspan="9"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
	            </thead>
	            <thead>
	                <th class="must_entry_caption" width="140">Company Name</th>
	                <th width="157">Buyer Name</th>
	                <th width="70">Requisition No</th>
	                <th width="70">Style ID</th>
	                <th width="80">Style Name</th>
	                <th width="90" class="must_entry_caption">Sample Stage</th>
	                <th width="130" colspan="2">Est. Ship Date</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	            </thead>
	            <tr class="general">
	                <td>
	                    <input type="hidden" id="requisition_no">
	                    <?  echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" ); ?>
	                </td>
	                <td id="buyer_td_req"><? echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
	                <td><input type="text" style="width:60px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num" /></td>
	                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_style_id" id="txt_style_id" /></td>
	                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  /></td>
	                <td><? echo create_drop_down( "cbo_sample_stage", 90, $sample_stage, "", 1, "-Select Stage-", $selected, "", "", "" ); ?></td>

	                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
	                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To date"></td>
	                <td>
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('cbo_sample_stage').value, 'create_requisition_id_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
	                </td>
	            </tr>
	            <tr>
	                <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
	            </tr>
	        </table>
	        <div id="search_div"></div>
	    </form>
		</div>
		<script type="text/javascript"></script>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and a.company_id='$data[2]'"; else { echo "<b style='color:crimson;'> Please Select Company First.</b>"; die; }
	if ($data[3]!=0) $buyer=" and a.buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.id='$data[1]'"; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no='$data[6]'"; else $style_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==2)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.id like '$data[1]%' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==3)
	{
		if (trim($data[1])!="") $style_id_cond=" and a.id like '%$data[1]' "; else $style_id_cond="";
		if ($data[6]!="") $style_cond=" and a.style_ref_no like '%$data[6]' "; else $style_cond="";
	}

	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and a.estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[7]!="") $requisition_num=" and a.requisition_number_prefix_num like '%$data[7]' "; else $requisition_num="";

	if ($data[8]!=0) $stage_id=" and a.sample_stage_id= '$data[8]' "; else  $stage_id="";
	if (!$data[8]) {echo "<b style='color:crimson;'> Please Select Sample Stage </b>";die;}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	$arr=array (2=>$buyer_arr,4=>$product_dept,5=>$dealing_marchant,6=>$sample_stage);
	$sql="";
	if($db_type==0) $yearCond="SUBSTRING_INDEX(a.insert_date, '-', 1)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
	
	$sql= "select a.id, a.requisition_number_prefix_num, $yearCond as year, a.company_id, a.buyer_name, a.style_ref_no, a.product_dept, a.dealing_marchant, a.sample_stage_id,a.requisition_number from sample_development_mst a,wo_po_sample_approval_info b where a.id=b.requisition_id and a.entry_form_id in (117,203) and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num $stage_id order by a.id DESC";
	echo $sql;
	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Product Department,Dealing Merchandiser,Sample Stage", "60,140,140,100,90,90,100","870","250",0, $sql , "js_set_value", "requisition_number", "", 1, "0,0,buyer_name,0,product_dept,dealing_marchant,sample_stage_id", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,product_dept,dealing_marchant,sample_stage_id", "",'','0,0,0,0,0,0') ;

	exit();
}

if ($action=="populate_data_from_search_popup_quotation")
{
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.price_with_commn_pcs,i.season_buyer_wise as in_season_buyer_wise from wo_price_quotation_costing_mst b,  wo_price_quotation a left join wo_quotation_inquery i on a.inquery_id=i.id where a.id=b.quotation_id and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		
		//echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
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
		$season_buyer_wise=$row[csf("season_buyer_wise")];
		if(!$season_buyer_wise){
			$season_buyer_wise=$row[csf("in_season_buyer_wise")];
		}
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('txt_quotation_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$season_buyer_wise."';\n";
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
    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Buyer Name</th>
                <th width="70">Cost Sheet No</th>
                <th width="100">Style Ref.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_qc_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" colspan="5"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('woven_order_entry_controller', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_name').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_qc_id_list_view")
{
	$data=explode('_',$data);
	//echo $data[1];
	//if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
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

	$arr=array (2=>$buyer_arr,5=>$pord_dept);
	
	$sql_approved="select b.approval_need, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and b.status_active=1 and page_id=28 and a.company_id='$data[0]'  order by b.id desc";
	$result_nasscity = sql_select($sql_approved); $approved_need=2; $allow_partial=2;
	foreach($result_nasscity as $row)
	{
		$approved_need=$row[csf("approval_need")];
		$allow_partial=$row[csf("allow_partial")];
	}
	unset($result_nasscity);
	if($approved_need==1 && $allow_partial==1) $approved_need_cond="and a.approved in (1,3)"; else if($approved_need==1 && $allow_partial==2) $approved_need_cond="and a.approved in (1)"; else $approved_need_cond="";
	
	$sql= "select a.id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $style_cond $quotation_id_cond $approved_need_cond order by a.id DESC";
	//echo $sql;
	echo create_list_view("list_view", "QC ID, Cost Sheet No, Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "50,70,100,100,150,100,100","800","280",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_id,0,0,department_id,0,0", $arr , "id,cost_sheet_no,buyer_id,confirm_style,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_qc")
{
	$qcFob_arr=return_library_array( "select mst_id, tot_fob_cost from qc_tot_cost_summary where status_active=1 and is_deleted=0",'mst_id','tot_fob_cost');
	$data_array=sql_select("select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("confirm_style")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_id")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("qc_no")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$qcFob_arr[$row[csf("qc_no")]]."';\n";
		echo "$('#txt_avg_price').attr('quot_cost','".$qcFob_arr[$row[csf("qc_no")]]."');\n";
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
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+<?= $set_smv_id ?>, 'create_ws_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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

if($action=="create_ws_id_list_view_bk")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$search_type=$data[2];
	$sysNo=$data[3];
	$styleRef=$data[4];
	$set_smv_id=$data[5];
	
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
	if($set_smv_id==3){
		$bulletin_type_cond="and a.bulletin_type=3";
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $style_con $buyer_id_con $sys_con $appCond $bulletin_type_cond order by a.id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]][$row[csf('style_ref')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]][$row[csf('style_ref')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]][$row[csf('style_ref')]]['system_no'].=$row[csf('system_no')].',';
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]][$row[csf('style_ref')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
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
		foreach($smv_dtls_arr as $bullid=>$bulldataarr)
		{
		foreach($bulldataarr as $ext_no=>$dataarr)
		{
			foreach($dataarr as $style=>$arrdata)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
				$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
	
				$finish_smv=$cut_smv=$sewing_smv=0;
	
				$sys_id=$bullid;//rtrim($arrdata['id'],',');
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
				$sewing_smv=$sewing_smv/$k;
	
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

if($action=="create_ws_id_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$search_type=$data[2];
	$sysNo=$data[3];
	$styleRef=$data[4];
	$set_smv_id=$data[5];
	
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

	$bulletin_type_cond="";
	if($set_smv_id==3){
		$bulletin_type_cond="and a.bulletin_type=3";
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

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $style_con $buyer_id_con $sys_con $appCond $bulletin_type_cond order by a.id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
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
				$sewing_smv=$sewing_smv/$k;
	
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

if($action=="open_set_list_view")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $set_smv_id.'='.$txt_style_ref;
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
			  $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#smv_'+i).removeAttr("ondblclick").attr("ondblclick","check_smv_set_popup("+i+",8)");
			  $('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
			  $('#cutsmv_'+i).removeAttr("ondblclick").attr("ondblclick","check_smv_set_popup("+i+",7)");
			  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
			  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","check_smv_set_popup("+i+",4)");
			  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			  $('#cboitem_'+i).val(''); 
			  $('#smv_'+i).val(''); 
			  $('#cutsmv_'+i).val(''); 
			  $('#finsmv_'+i).val('');

			  $('#cboitem_'+i).removeAttr('disabled')

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
		//get_php_form_data(company_id,'set_smv_work_study','requires/woven_order_entry_controller' );
		var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'woven_order_entry_controller');
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
	
	function check_smv_set_popup(id,processtype)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
	
		var txt_style_ref='<?=$txt_style_ref ?>';
		var cbo_company_name='<?=$cbo_company_name ?>';
		var cbo_buyer_name='<?=$cbo_buyer_name ?>';
		var item_id=$('#cboitem_'+id).val();
		var bulletin_type=$('#bulletin_type').val();
			//alert(cbo_company_name);
		var set_smv_id='<?=$set_smv_id ?>';
		
		if(set_smv_id==3 || set_smv_id==4 || set_smv_id==6 || set_smv_id==8 || set_smv_id==9)
		{
			if(processtype==8)//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			{
				$('#smv_'+id).val('');
				$('#tot_smv_qnty').val('');
			}
			else if(processtype==7)//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			{
				$('#cutsmv_'+id).val('');
				$('#tot_cutsmv_qnty').val('');
			}
			else if(processtype==4)//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			{
				$('#finsmv_'+id).val('');
				$('#tot_finsmv_qnty').val('');
			}
			
			$('#hidquotid_'+id).val('');//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			
			var page_link="woven_order_entry_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&bulletin_type="+bulletin_type+"&processtype="+processtype;
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
			
			var row_id=smv_data[1];
			
			if(processtype==8)
			{
				$("#smv_"+row_id).val(smv_data[0]);
				$("#smv_"+row_id).attr('readonly','readonly');
			}
			else if(processtype==7)
			{
				$("#cutsmv_"+row_id).val(smv_data[0]);
				$("#cutsmv_"+row_id).attr('readonly','readonly');
			}
			else if(processtype==4)
			{
				$("#finsmv_"+row_id).val(smv_data[0]);
				$("#finsmv_"+row_id).attr('readonly','readonly');
			}
			$("#hidquotid_"+row_id).val(smv_data[2]);
			
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
		var unit_id= document.getElementById('unit_id').value;
		//alert(unit_id);
		
		if(unit_id!=1)
		{
			if( ($('#tot_set_qnty').val()*1)<2)
			{
				alert("Ratio Break Down Does Not Match with Order UOM Set.");
				return;
			}
		}
		
		var rowCount = $('#tbl_set_details tr').length-1;
		var set_breck_down=""; var item_id=""
		
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
		var page_link="woven_order_entry_controller.php?action=open_emblishment_list";
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
		 if($set_smv_id==3 || $set_smv_id==4){
			$bulletin_type=3;
		}
		if($set_smv_id==6 || $set_smv_id==8 || $set_smv_id==9){
			$bulletin_type=2;
		}
		 $sql_smv="SELECT  gmts_item_id from ppl_gsd_entry_mst where status_active=1 and is_deleted=0 and style_ref='$txt_style_ref' and bulletin_type=$bulletin_type order by id DESC";
		 $sql_result=sql_select($sql_smv);$set_smv_arr=array();

		 foreach($sql_result as $row)
		 {
			$ws_garments_item=$row[csf('gmts_item_id')];
			$gmstitem_arr[$row[csf('gmts_item_id')]]=$row[csf('gmts_item_id')];
		 }
		 $gmstitem_string=implode(",", $gmstitem_arr);
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
		 
		 if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==8) $readonly="readonly"; else $readonly=""; //pq 2, ws 3, ws 8
		 
		 if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==4 || $set_smv_id==5 || $set_smv_id==6 || $set_smv_id==8 || $set_smv_id==9) //ISD-22-22097
		 {
			 $disabled=1;  $disab="disabled";
		 }else $disabled=0;
		 ?>
        <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id"  />  
            <input type="hidden" id="unit_id" value="<?=$unit_id;  ?>" />
			<input type="hidden" id="bulletin_type" value="<?=$bulletin_type;  ?>" />          	
            <table width="1300" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                    	<th width="150">Item</th>
                    	<th width="40">Set Ratio</th>
                    	<th width="40">Sew SMV/ Pcs</th>
                    	<th width="40">Cut SMV/ Pcs</th>
                    	<th width="40">Fin SMV/ Pcs</th>
                    	<th width="70">Complexity</th>
                    	<th width="90">Print</th>
                    	<th width="90">Embro</th>
                    	<th width="90">Wash</th>
                    	<th width="90">SP. Works</th>
                    	<th width="90">Gmts Dyeing</th>
                    	<th width="90">AOP</th>
                    	<th width="90">Brushing</th>
                    	<th width="90">Peached Finish</th>
                    	<th width="90">Yarn Dyeing</th>
                    	<th>&nbsp;</th>
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
                            ?>
                            <tr id="settr_<?=$i;?>" align="center">
                                <td><?=create_drop_down( "cboitem_".$i, 150, get_garments_item_array(2), "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id );",$disabled,$gmstitem_string ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<?=$i;?>" name="txtsetitemratio_<?=$i;?>" style="width:27px"  class="text_boxes_numeric" onChange="calculate_set_smv(<?=$i;?>);" value="<?=$data[1]; ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /></td>
                                <td>
                                    <input type="text" id="smv_<?=$i;?>" name="smv_<?=$i;?>" style="width:27px" class="text_boxes_numeric" onChange="calculate_set_smv(<?=$i;?>);"  value="<?=$data[2]; ?>" <?=$disab; ?> onDblClick="check_smv_set_popup(<?=$i;?>,8);" /> 
                                    <input type="hidden" id="smvset_<?=$i;?>" name="smvset_<?=$i;?>" style="width:20px" value="<?=$data[3]; ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="cutsmv_<?=$i;?>" name="cutsmv_<?=$i;?>" style="width:27px"  class="text_boxes_numeric" onChange="calculate_set_cutsmv(<?=$i;?>);" value="<?=$data[6]; ?>" <?=$disab; ?> onDblClick="check_smv_set_popup(<?=$i;?>,7);" /> 
                                    <input type="hidden" id="cutsmvset_<?=$i;?>" name="cutsmvset_<?=$i;?>" style="width:20px" value="<?=$data[7]; ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="finsmv_<?=$i;?>" name="finsmv_<?=$i;?>" style="width:27px" class="text_boxes_numeric" onChange="calculate_set_finsmv(<?=$i;?>);"  value="<?=$data[8]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} echo $readonly; ?> onDblClick="check_smv_set_popup(<?=$i;?>,4);"/> 
                                    <input type="hidden" id="finsmvset_<?=$i;?>" name="finsmvset_<?=$i;?>" style="width:20px" value="<?=$data[9]; ?>" readonly/>
                                </td>
                                <td><?=create_drop_down( "complexity_".$i, 70, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); ?></td>
                                <td><?=create_drop_down( "emblish_".$i, 50, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); ?>
                                    <input type="text" id="printseq_<?=$i;?>" name="printseq_<?=$i;?>" style="width:20px"  class="text_boxes_numeric" value="<?=$data[10]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> />
                                    <?=create_drop_down( "printdiff_".$i, 70, $difficulty_arr, "",1," -- Select --",$data[28],"",$disabled,''); ?>                                
                                </td>
                                <td><?=create_drop_down( "embro_".$i, 50, $yes_no, "",1," -- Select--", $data[11], "",$disabled,'' ); ?>
                                    <input type="text" id="embroseq_<?=$i;?>" name="embroseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[12]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <?=create_drop_down( "embrodiff_".$i, 70, $difficulty_arr, "",1," -- Select --",$data[29],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "wash_".$i, 50, $yes_no, "",1," -- Select--", $data[13], "",$disabled,'' ); ?>
                                    <input type="text" id="washseq_<?=$i;?>" name="washseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[14]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <?=create_drop_down( "washdiff_".$i, 70, $difficulty_arr, "",1," -- Select --",$data[30],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "spworks_".$i, 50, $yes_no, "",1," -- Select--", $data[15], "",$disabled,'' ); ?>
                                    <input type="text" id="spworksseq_<?=$i;?>" name="spworksseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[16]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                    <?=create_drop_down( "spwdiff_".$i, 70, $difficulty_arr, "",1," -- Select --",$data[31],"",$disabled,''); ?>  
                                </td>
                                <td><?=create_drop_down( "gmtsdying_".$i, 50, $yes_no, "",1," -- Select--", $data[17], "",$disabled,'' ); ?>
                                    <input type="text" id="gmtsdyingseq_<?=$i;?>" name="gmtsdyingseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[18]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><?=create_drop_down( "aop_".$i, 50, $yes_no, "",1," -- Select--", $data[20], "",$disabled,'' ); ?>
                                    <input type="text" id="aopseq_<?=$i;?>" name="aopseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[21]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                            	<td><?=create_drop_down( "brush_".$i, 50, $yes_no, "",1," -- Select--", $data[22], "",$disabled,'' ); ?>
                            	    <input type="text" id="brushseq_<?=$i;?>" name="brushseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[23]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                            	</td> 
                            	<td><?=create_drop_down( "peach_".$i, 50, $yes_no, "",1," -- Select--", $data[24], "",$disabled,'' ); ?>
                                    <input type="text" id="peachseq_<?=$i;?>" name="peachseq_<?=$i;?>" style="width:20px" class="text_boxes_numeric" value="<?=$data[25]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td> 
                                <td><?=create_drop_down( "yd_".$i, 50, $yes_no, "",1," -- Select--", $data[26], "",$disabled,'' ); ?>
                                    <input type="text" id="ydseq_<?=$i;?>" name="ydseq_<?=$i;?>" style="width:20px"  class="text_boxes_numeric" value="<?=$data[27]; ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td>
                                    <input type="hidden" id="hidquotid_<?=$i;?>" name="hidquotid_<?=$i;?>" style="width:30px" class="text_boxes_numeric" value="<?=$data[19]; ?>" readonly/>
                                    <input type="button" id="increaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<?=$i; ?>);" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <input type="button" id="decreaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<?=$i; ?> ,'tbl_set_details');" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                </td> 
                            </tr>
                            <?
                        }
                    }
                    else
                    {
                        ?>
                        <tr id="settr_1" align="center">
                            <td><?=create_drop_down( "cboitem_1", 150, get_garments_item_array(2), "",1,"--Select--", $ws_garments_item, "check_duplicate(1,this.id );",$disabled,$gmstitem_string ); ?></td>
                            <td><input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<? if ($unit_id==1){echo "1";} else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /></td>
                            <td>
                                <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1);" onDblClick="check_smv_set_popup(1,8);" value="0" <?=$readonly; ?> /> 
                                <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1);" onDblClick="check_smv_set_popup(1,7);" value="0" <?=$readonly; ?> /> 
                                <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1);" onDblClick="check_smv_set_popup(1,4);" value="0" <?=$readonly; ?> /> 
                                <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td><?=create_drop_down( "complexity_1", 70, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td><?=create_drop_down( "emblish_1", 50, $yes_no, "",1," -- Select --", 0, "",'','' ); ?>
                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="" />
                                <?=create_drop_down( "printdiff_1", 70, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><?=create_drop_down( "embro_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="embroseq_1" name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="" />
                                <?=create_drop_down( "embrodiff_1", 70, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><?=create_drop_down( "wash_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="washseq_1" name="washseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                                <?=create_drop_down( "washdiff_1", 70, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><?=create_drop_down( "spworks_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="" />
                                <?=create_drop_down( "spwdiff_1", 70, $difficulty_arr, "",1," -- Select --",0,"",'',''); ?> 
                            </td>
                            <td><?=create_drop_down( "gmtsdying_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="gmtsdyingseq_1" name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><?=create_drop_down( "aop_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="aopseq_1" name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><?=create_drop_down( "brush_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="brushseq_1"   name="brushseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><?=create_drop_down( "peach_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="peachseq_1"   name="peachseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><?=create_drop_down( "yd_1", 50, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="ydseq_1"   name="ydseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td>
                                <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                            </td> 
                        </tr>
                    <? 
                    } 
                    ?>
                </tbody>
            </table>
            <table width="1300" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="150">Total</th>
                        <th width="40">
                            <input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:27px" value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                        </th>
                         <th width="40">
                            <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:27px" value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                         <th width="40">
                            <input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:27px" value="<? if($tot_cutsmv_qnty !=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th width="40">
                            <input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:27px" value="<? if($tot_finsmv_qnty !=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table width="1250" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value_set();"/></td> 
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

if($action=="booking_no_with_approved_status")
{
	$data=explode("_",$data);
	if($data[1]==""){
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else{
		 $sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row){
		if($row[csf('is_approved')]==1){
		  $approved_booking.=$row[csf('booking_no')].", ";	
		}
		else{
		  $un_approved_booking.=$row[csf('booking_no')].", ";	
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
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
	 disconnect($con);die;
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sql=sql_select("select variable_list, copy_quotation, cost_control_source from variable_order_tracking where company_name=$cbo_company_name and variable_list in (20,53) order by id");
	$cost_control_source=0; $is_copy_quatation=2;
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==20) $is_copy_quatation=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==53) $cost_control_source=$vrow[csf('cost_control_source')];
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
	
	if(str_replace("'","",$txt_quotation_id)=="") $quotation_id=0; else $quotation_id=str_replace("'","",$txt_quotation_id);
	// Insert Here----------------------------------------------------------
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	
	if ($operation==0) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "wo_po_details_master", 1);
		//echo "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR('Y',insert_date)=".date('Y',time())." order by job_no_prefix_num desc"; die; 
		$txt_style_ref=str_replace("'",'',$txt_style_ref);
		$style_ref='';
		$style_ref=trim(str_replace($str_replace_check,'',$txt_style_ref));
		
		$txt_style_description=str_replace("'",'',$txt_style_description);
		$style_description='';
		$style_description=trim(str_replace($str_replace_check,'',$txt_style_description));
		
		$txt_remarks=str_replace("'",'',$txt_remarks);
		$mst_remarks='';
		$mst_remarks=trim(str_replace($str_replace_check,'',$txt_remarks));
			 
		if($db_type==0)$yearCond="and YEAR(insert_date)"; else if($db_type==2) $yearCond="and to_char(insert_date,'YYYY')";
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name $yearCond=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		
		$field_array="id, garments_nature, quotation_id, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, brand_id, location_name, style_ref_no, repeat_job_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, season_buyer_wise, season_year, factory_marchant, style_owner, working_location_id, design_source_id, qlty_label, sustainability_standard, fab_material, quality_level, po_tna_lead_time, requisition_no, is_deleted, status_active, inserted_by, insert_date";
		//txt_repeat_job_no cbo_design_source_id
		$data_array="(".$id.",".$garments_nature.",'".$quotation_id."','".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_brand_id.",".$cbo_location_name.",'".$style_ref."',".$txt_repeat_job_no.",'".$style_description."',".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$txt_repeat_no.",".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_bhmerchant.",".$cbo_packing.",'".$mst_remarks."',".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_name.",".$cbo_season_year.",".$cbo_factory_merchant.",".$cbo_working_company_id.",".$cbo_working_location_id.",".$cbo_design_source_id.",".$cbo_qltyLabel.",".$sustainability_standard.",".$cbo_fab_material.",".$cbo_order_nature.",".$cbo_po_tna_lead_time.",'".str_replace("'", "", $txt_requision_no)."',0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val();
		
		$field_array1="id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff";
		$add_comma=0;
		$total_smv_set=0;
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
		$flag=1;
		//echo "10**INSERT INTO wo_po_details_mas_set_details (".$field_array1.") VALUES ".$data_array1.""; die;
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$job_id=$id;
		$rIDt=1;
		if($cost_control_source==1)
		{
			if($quotation_id!=0)
			{
				$query="UPDATE qc_confirm_mst SET job_id=$job_id WHERE cost_sheet_id=$quotation_id and status_active=1 and is_deleted=0";
				$rIDt=execute_query($query,1);
				if($rIDt==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID." &&". $rID1; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rIDt."** INSERT INTO wo_po_details_master (".$field_array.") VALUES ".$data_array."**".str_replace("'",'',$rID1);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rIDt."** INSERT INTO wo_po_details_master (".$field_array.") VALUES ".$data_array."**".str_replace("'",'',$rID1);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$txt_job_no and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
			 disconnect($con);die;
		}
		$txt_style_ref=str_replace("'",'',$txt_style_ref);
		$style_ref='';
		$style_ref=trim(str_replace($str_replace_check,'',$txt_style_ref));
		
		$txt_style_description=str_replace("'",'',$txt_style_description);
		$style_description='';
		$style_description=trim(str_replace($str_replace_check,'',$txt_style_description));
		
		$txt_remarks=str_replace("'",'',$txt_remarks);
		$mst_remarks='';
		$mst_remarks=trim(str_replace($str_replace_check,'',$txt_remarks));
		
		$PrevData=sql_select("select style_ref_no,gmts_item_id from wo_po_details_master where job_no=$txt_job_no");
		$PrevStyleRefNo=$PrevData[0][csf('style_ref_no')];
		$PrevGmtsItemId=$PrevData[0][csf('gmts_item_id')];
		$field_array="quotation_id*buyer_name*brand_id*location_name*style_ref_no*repeat_job_no*style_description*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*order_repeat_no*region*product_category*team_leader*dealing_marchant*bh_merchant*packing*remarks*ship_mode*order_uom*gmts_item_id*set_break_down*total_set_qnty*set_smv*season_buyer_wise*season_year*factory_marchant*style_owner*design_source_id*qlty_label*working_location_id*style_ref_no_prev*gmts_item_id_prev*sustainability_standard*fab_material*quality_level*po_tna_lead_time*requisition_no*updated_by*update_date";
		$data_array="'".$quotation_id."'*".$cbo_buyer_name."*".$cbo_brand_id."*".$cbo_location_name."*'".$style_ref."'*".$txt_repeat_job_no."*'".$style_description."'*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$txt_repeat_no."*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_bhmerchant."*".$cbo_packing."*'".$mst_remarks."'*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_name."*".$cbo_season_year."*".$cbo_factory_merchant."*".$cbo_working_company_id."*".$cbo_design_source_id."*".$cbo_qltyLabel."*".$cbo_working_location_id."*'".$PrevStyleRefNo."'*'".$PrevGmtsItemId."'*".$sustainability_standard."*".$cbo_fab_material."*".$cbo_order_nature."*".$cbo_po_tna_lead_time."*'".str_replace("'", "", $txt_requision_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id, job_no, job_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff";
		$add_comma=0;
		$total_smv_set=0;
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
			$data_array1 .="(".$id1.",".$txt_job_no.",".$hidd_job_id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."','".$set_breck_down_arr[20]."','".$set_breck_down_arr[21]."','".$set_breck_down_arr[22]."','".$set_breck_down_arr[23]."','".$set_breck_down_arr[24]."','".$set_breck_down_arr[25]."','".$set_breck_down_arr[26]."','".$set_breck_down_arr[27]."','".$set_breck_down_arr[28]."','".$set_breck_down_arr[29]."','".$set_breck_down_arr[30]."','".$set_breck_down_arr[31]."')";
			$total_smv_set+=$set_breck_down_arr[3];
			$add_comma++;
			$id1=$id1+1;
			$sewSmv+=$set_breck_down_arr[3];
			$cutSmv+=$set_breck_down_arr[7];
		}
		$tot_smv_qnty=str_replace("'",'',$tot_smv_qnty);
		if($tot_smv_qnty != number_format($total_smv_set,2,'.',''))
		{
			echo "SMV**";
			 disconnect($con);die;
		}
		$flag=1;
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID; die;
		//ALTER SESSION SET NLS_TIMESTAMP_FORMAT='DD-MON-RR HH.MI.SSXFF AM';
		$rID1=execute_query("delete from wo_po_details_mas_set_details where  job_no =".$txt_job_no."",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		$rID3=execute_query("update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		$rID4=execute_query("update  wo_pre_cost_mst set sew_smv=$tot_smv_qnty  where  job_no =".$txt_job_no."",1);
		if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		$data_int=$cbo_currercy.'****'.$set_breck_down;
		$set_smv_id=str_replace("'","",$set_smv_id);
		if($set_smv_id==1 || $set_smv_id==7) fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$data_int,$sewSmv,$cutSmv,1);
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
		
		$jobidArr[str_replace("'","",$hidd_job_id)]=str_replace("'","",$hidd_job_id);
		
		if(trim($PrevStyleRefNo)!=trim($style_ref))
		{
			fnc_isdyeingplan("WO_PO_DETAILS_MASTER", $jobidArr);
		}
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rIDt.'='.$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_job_no."**".$rID."**".str_replace("'",'',$hidd_job_id);
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
				echo "1**".$txt_job_no."**".$rID."**".str_replace("'",'',$hidd_job_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here----------------------------------------------------------
	{
		$con = connect();
		
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$txt_job_no and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
			 disconnect($con);die;
		}
		
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
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",1);
		$rID1=sql_delete("wo_po_break_down",$field_array,$data_array,"job_no_mst","".$txt_job_no."",1);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1)
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
			if($rID && $rID1)
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
	}
}

// Master Form End ***************************************** Master Form End******************************************
 
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="get_excess_cut_percent")
{
	$data=explode("_",$data);
	$hidd_job_id=$data[3];
	$cbo_company_name=$data[1];
	$cbo_buyer_name=$data[2];
	$excess_variable=return_field_value("excut_source","variable_order_tracking"," company_name ='$data[1]' and variable_list=45 and is_deleted=0 and status_active=1");	
	$excess_per_level=return_field_value("excut_source","variable_order_tracking"," company_name ='$data[1]' and variable_list=65 and is_deleted=0 and status_active=1");
	$editable_id=return_field_value("editable","variable_order_tracking"," company_name ='$data[1]' and variable_list=45 and excut_source=2 and is_deleted=0 and status_active=1");
	if($editable_id==0 || $editable_id=='') $editable_id=0;else $editable_id=$editable_id;
	$percentage=0;
	if($excess_variable==2 && $excess_per_level==2){
		$item_details=sql_select("SELECT gmts_item_id, printdiff,embrodiff,washdiff,spwdiff from wo_po_details_mas_set_details where job_id=$hidd_job_id order by id ASC FETCH FIRST 1 ROWS ONLY");
			foreach ($item_details as $item) {
			 	$item_dtls_arr['print_difficulty'] = $item[csf('printdiff')];
			 	$item_dtls_arr['emb_difficulty'] = $item[csf('embrodiff')];
			 	$item_dtls_arr['wash_difficulty'] = $item[csf('washdiff')];
			 	$item_dtls_arr['splwork_difficulty'] = $item[csf('spwdiff')];
			}
			$attr_arr=array('cutting', 'sewing', 'finishing');
			$march_arr=array('print_difficulty','emb_difficulty','wash_difficulty','splwork_difficulty');
			$slab_data=sql_select("SELECT id, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, total from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$cbo_company_name and buyer_id=$cbo_buyer_name and $data[0] >= lower_limit_qty AND $data[0] <= upper_limit_qty");
	 		foreach ($slab_data as $row) {
	 			foreach ($attr_arr as $attr) {
		 			$slab_data_arr[$attr] = $row[csf($attr)];
		 		}
	 			foreach ($march_arr as $diff_attr) {
		 			if($item_dtls_arr[$diff_attr]==$row[csf($diff_attr)]){
		 				$field_arr=explode("_", $diff_attr);
		 				$slab_data_arr[$field_arr[0]] =$row[csf($field_arr[0])];
		 			}
		 		}
	 		}
	 		$percentage=array_sum($slab_data_arr);
	}
	echo $excess_variable."_".$percentage."_".$editable_id;
}

if ($action=="populate_order_details_form_data")
{
	$user=$_SESSION['logic_erp']['user_id'];
	$user_id=return_field_value("is_data_level_secured","user_passwd","id=$user AND valid=1");
	$company_id=return_field_value("a.company_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id='$data' and a.is_deleted=0 and a.status_active=1");
	
	$result_color= sql_select("select c.order_quantity from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id='$data'   and a.is_deleted=0 and a.status_active=1  and c.is_deleted=0 and c.status_active=1");
//	echo "select c.order_quantity from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id='$data'   and a.is_deleted=0 and a.status_active=1  and c.is_deleted=0 and c.status_active=1";
	 
	$tot_color_size_qty=0;
	foreach ($result_color as $row)
	{
		$tot_color_size_qty+=$row[csf("order_quantity")];
	}
	
	$result= sql_select("select a.company_name, a.job_no, a.total_set_qnty from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$data' and a.is_deleted=0 and a.status_active=1");
	$company_id=$result[0][csf('company_name')];
	$total_set_qnty=$result[0][csf('total_set_qnty')];
	$txt_job_no=$result[0][csf('job_no')];
	//$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	//$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	
	$sql=sql_select("select variable_list, copy_quotation, po_update_period, po_current_date from variable_order_tracking where company_name='$company_id' and variable_list in (32,33,78) order by id");
	$poEntryControlWithBomApproval=2; $update_period_id=0; $po_current_date_data=0; 
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==32) $update_period_id=$vrow[csf('po_update_period')];
		if($vrow[csf('variable_list')]==33) $po_current_date_data=$vrow[csf('po_current_date')];
		
		if($vrow[csf('variable_list')]==78) $poEntryControlWithBomApproval=$vrow[csf('copy_quotation')];
	}
	
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	$bomApproved=0;
	if($poEntryControlWithBomApproval==3) 
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1 || $isapproved==3)
		{
			$bomApproved=1;
		}
	}

	$data_array=sql_select("select id, is_confirmed, po_number, po_received_date, pub_shipment_date, extended_ship_date, shipment_date, factory_received_date, po_quantity,unit_price, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, insert_date, sc_lc, with_qty, sewing_company_id, sewing_location_id, txt_etd_ldd from wo_po_break_down where id='$data'");
	foreach ($data_array as $row)
	{ 
		$insert_date=explode(" ",$row[csf("insert_date")]);
		$current_date=date('d-m-Y h:i:s');
		$po_insert_date=change_date_format($insert_date[0],'dd-mm-yyyy','-').' '.$insert_date[1];
		$total_time=datediff(n,$po_insert_date,$current_date);
		$total_hour=floor($total_time/60);
		//.":".$total_time%60
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n"; 
		echo "document.getElementById('txt_order_status').value = '".$row[csf("is_confirmed")]."';\n"; 
		echo "document.getElementById('txt_hidden_color_qty').value = '".$tot_color_size_qty."';\n";
		$poQty=$row[csf("po_quantity")]*$total_set_qnty; 
		if($poQty!=$tot_color_size_qty)
		{
			echo "fnc_poQty_chk(1);\n";
		}
		else
		{
			echo "fnc_poQty_chk(2);\n";
		}
		$current_date=date('d-m-Y');
		if($po_current_date_data==1 && $row[csf("is_confirmed")]==1)
		{
			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		}
		else if($po_current_date_data==1 && $row[csf("is_confirmed")]==2)
		{
			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";
		}
		else
		{
			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n"; 
			echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		}
		
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_org_shipment_date').value = '".change_date_format($row[csf("shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_factory_rec_date').value = '".change_date_format($row[csf("factory_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('chk_extended_ship_date').value = '".change_date_format($row[csf("extended_ship_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_etd_ldd').value = '".change_date_format($row[csf("txt_etd_ldd")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('hidden_po_qty').value = '".$row[csf("po_quantity")]."';\n";  
		echo "$('#txt_po_quantity').attr('saved_po_quantity','".$row[csf("po_quantity")]."')".";\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("unit_price")]."';\n";  
		echo "document.getElementById('txt_amount').value = '".number_format($row[csf("po_total_price")],4,'.','')."';\n";  
		echo "document.getElementById('txt_excess_cut').value = '".$row[csf("excess_cut")]."';\n";  
		echo "document.getElementById('txt_plan_cut').value = '".$row[csf("plan_cut")]."';\n";  
		echo "document.getElementById('txt_po_datedif_hour').value = '".$total_hour."';\n";  
		echo "document.getElementById('txt_details_remark').value = '".$row[csf("details_remarks")]."';\n";  
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";  
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n"; 
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n"; 
		echo "set_tna_task();\n"; 
		
		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n"; 
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";  
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n"; 
		echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n"; 
		echo "document.getElementById('txt_sc_lc').value = '".$row[csf("sc_lc")]."';\n"; 
		
		if($row[csf("with_qty")]==0){
			echo "document.getElementById('with_qty').checked = true;\n"; 
		}else{
			echo "document.getElementById('with_qty').checked = false;\n"; 
		}
		echo "document.getElementById('with_qty').value = '".$row[csf("with_qty")]."';\n"; 
		echo "document.getElementById('with_qty_pop').value = '".$row[csf("with_qty")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n";  
	}
	$qry_result=sql_select( "select id from  wo_po_color_size_breakdown where po_break_down_id='$data' and  status_active=1 and is_deleted=0");
	$row=count($qry_result);
	if($row>0)
	{
		//echo "$('#txt_avg_price').attr('disabled','true')".";\n";
		//echo "$('#txt_avg_price').attr('title','Change It From Color Size Break Down')".";\n";
	}
	else
	{
		//echo "$('#txt_avg_price').removeAttr('disabled')".";\n";
		//echo "$('#txt_avg_price').removeAttr('title')".";\n";
		echo "alert('Color Size Breakdown not found for this Order')".";\n";
	}
	$sql_data=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from  pro_garments_production_mst where po_break_down_id=".$data." and production_type=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_quantity')]>0)
		{
			echo "$('#txt_excess_cut').attr('disabled','true')".";\n";
			echo "$('#txt_excess_cut').attr('title','Cutting Qty Found')".";\n";
		}
		else
		{
			echo "$('#txt_excess_cut').removeAttr('disabled')".";\n";
			echo "$('#txt_avg_price').removeAttr('title')".";\n";
		}
	}
	
	if($bomApproved==1)
	{
		echo "$('#cbo_order_status').attr('disabled','true')".";\n";
		echo "$('#txt_po_received_date').attr('disabled','true')".";\n";
		echo "$('#txt_po_no').attr('disabled','true')".";\n";
		echo "$('#txt_pub_shipment_date').attr('disabled','true')".";\n";
		echo "$('#txt_org_shipment_date').attr('disabled','true')".";\n";
		echo "$('#txt_factory_rec_date').attr('disabled','true')".";\n";
		echo "$('#chk_extended_ship_date').attr('disabled','true')".";\n";
		echo "$('#txt_etd_ldd').attr('disabled','true')".";\n";
		echo "$('#txt_avg_price').attr('disabled','true')".";\n";
		echo "$('#txt_excess_cut').attr('disabled','true')".";\n";
		echo "$('#txt_details_remark').attr('disabled','true')".";\n";
		echo "$('#cbo_status').attr('disabled','true')".";\n";
		echo "$('#cbo_packing_po_level').attr('disabled','true')".";\n";
		echo "$('#show_textcbo_delay_for').attr('disabled','true')".";\n";
		echo "$('#txt_grouping').attr('disabled','true')".";\n";
		echo "$('#cbo_projected_po').attr('disabled','true')".";\n";
		echo "$('#cbo_tna_task').attr('disabled','true')".";\n";
		echo "$('#txt_file_no').attr('disabled','true')".";\n";
		echo "$('#txt_sc_lc').attr('disabled','true')".";\n";
		echo "$('#with_qty').attr('disabled','true')".";\n";
		echo "$('#fileupload').attr('disabled','true')".";\n";
		//echo "$('#actualpo').attr('disabled','true')".";\n";
		echo "$('#txt_amount').attr('disabled','true')".";\n";
		echo "$('#txt_plan_cut').attr('disabled','true')".";\n";
	}
}

if ($action=="get_cutting_qty")
{
	$production_quantity=0;
	if($data!="")
	{
		$sql_data=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from  pro_garments_production_mst where po_break_down_id='$data' and production_type=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($sql_data as $row_data)
		{
			if($row_data[csf('production_quantity')]>0)
			{
			$production_quantity=$row_data[csf('production_quantity')];
			}
		}
	}
	echo trim($production_quantity);
}

if($action=="get_cutting_qty_order")
{
	$msg=array(0=>"Delete not allowed bcz following Subsequence Entry Found:");
	$sales_contact_array=array();
	$sales_contact_sql=sql_select("select a.contact_system_id,a.contract_no from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.wo_po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sales_contact_sql as $sales_contact_row){
		$sales_contact_array[$sales_contact_row[csf('contact_system_id')]]=$sales_contact_row[csf('contract_no')];
	}
	
	if(count($sales_contact_array)>0){
		$msg[]=count($msg).'. Attached with Sales contact No :'.implode(",",$sales_contact_array);
	}
	
	$lc_array=array();
	$lc_sql=sql_select("select a.export_lc_system_id, a.export_lc_no from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and b.wo_po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($lc_sql as $lc_row){
		$lc_array[$lc_row[csf('export_lc_system_id')]]=$lc_row[csf('export_lc_no')];
	}
	
	if(count($lc_array)>0){
		$msg[]=count($msg).'. Attached with Export LC No  :'.implode(",",$lc_array);
	}
	
	
	$fb_array=array();//1
	$tb_array=array();//2
	$sb_array=array();//3
	$fs_array=array();//4
	$ts_array=array();//5
	$eb_array=array();//6
	$booking_sql=sql_select("select a.booking_no, a.booking_type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($booking_sql as $booking_row){
		if($booking_row[csf('booking_type')]==1){
		$fb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==2){
		$tb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==3){
		$sb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==4){
		$fs_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==5){
		$ts_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==6){
		$eb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
	}
	
	if(count($fb_array)>0){
		$msg[]=count($msg).'. Fabric Booking Found BK No  :'.implode(",",$fb_array);
	}
	if(count($tb_array)>0){
		$msg[]=count($msg).'. Trims Booking Found BK No  :'.implode(",",$tb_array);
	}
	if(count($sb_array)>0){
		$msg[]=count($msg).'. Service Booking Found BK No  :'.implode(",",$sb_array);
	}
	if(count($fs_array)>0){
		$msg[]=count($msg).'. Sample Fabric Booking Found BK No  :'.implode(",",$fs_array);
	}
	if(count($ts_array)>0){
		$msg[]=count($msg).'. Sample Trims Booking Found BK No  :'.implode(",",$ts_array);
	}
	if(count($eb_array)>0){
		$msg[]=count($msg).'. Emblishment Booking Found BK No  :'.implode(",",$eb_array);
	}
		
	$cutting_array=array();//1
	$printing_array=array();//2
	$printreceived_array=array();//3
	$sewingin_array=array();//4
	$sewingout_array=array();//5
	$iron_array=array();//7
	$finish_array=array();//8
	
	$sql_production=sql_select( "select production_quantity,production_type from  pro_garments_production_mst where po_break_down_id='$data'  and  status_active=1 and is_deleted=0");
	foreach($sql_production as $row_production)
	{
		if($row_production[csf('production_type')]==1){
		$cutting_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==2){
		$printing_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==3){
		$printreceived_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==4){
		$sewingin_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==5){
		$sewingout_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==7){
		$iron_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==8){
		$finish_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
	}
	
	if(count($cutting_array)>0){
		$msg[]=count($msg).'. Cutting Qty Found :'.$cutting_array[1];
	}
	if(count($printing_array)>0){
		$msg[]=count($msg).'. Printing Qty Found :'.$printing_array[2];
	}
	if(count($printreceived_array)>0){
		$msg[]=count($msg).'. Print received Qty Found :'.$printreceived_array[3];
	}
	if(count($sewingin_array)>0){
		$msg[]=count($msg).'.	Sewing Input  Found :'.$sewingin_array[4];
	}
	if(count($sewingout_array)>0){
		$msg[]=count($msg).'. Sewing Output Found :'.$sewingout_array[5];
	}
	if(count($iron_array)>0){
		$msg[]=count($msg).'. Iron Qty Found :'.$iron_array[7];
	}
	if(count($finish_array)>0){
		$msg[]=count($msg).'. Finish Qty Found :'.$finish_array[8];
	}
	$dataArrayYarnIssue=array();
	$sql_yarn_iss="select a.po_breakdown_id, 
			sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
			sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
			from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 and a.po_breakdown_id='$data' group by a.po_breakdown_id";
	$dataArrayIssue=sql_select($sql_yarn_iss);
	foreach($dataArrayIssue as $row_yarn_iss)
	{
		//$issuue=$row_yarn_iss[csf('issue_qnty')]-$row_yarn_iss[csf('return_qnty')]
		$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]]+=$row_yarn_iss[csf('issue_qnty')];
	}
	
	if(count($dataArrayYarnIssue)>0){
		$msg[]=count($msg).'. Yarn Issue Qty Found :'.$dataArrayYarnIssue[$data];
	}
	
	$yarnAllocationArr=array(); //$yarnAllocationJobArr=array();
	$sql_yarn_allocation="select a.po_break_down_id 
			sum(a.qnty) AS allocation_qty
			from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id='$data' group by a.po_break_down_id";
	$dataArrayAllocation=sql_select($sql_yarn_allocation);
	foreach($dataArrayAllocation as $allocationRow)
	{
		$yarnAllocationArr[$allocationRow[csf('po_break_down_id')]]+=$allocationRow[csf('allocation_qty')];
	}
	
	if(count($yarnAllocationArr)>0){
		$msg[]=count($msg).'. Yarn Allocation Qty Found  :'.$yarnAllocationArr[$data];
	}
	
	//$trans_qnty_arr=array(); 
	$grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array(); $grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
							
	$dataArrayTrans=sql_select("select po_breakdown_id, 
							sum(CASE WHEN entry_form in (2,58) THEN quantity ELSE 0 END) AS grey_receive,
							sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

							sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
							sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_rollwise,
							sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
							
							sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
							sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
							sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
							sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
							sum(CASE WHEN entry_form ='80' and trans_type=6 THEN quantity ELSE 0 END) AS trans_out_sample_knit,
							sum(CASE WHEN entry_form ='81' and trans_type=5 THEN quantity ELSE 0 END) AS trans_in_sample_knit
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,58,61,80,81) and po_breakdown_id= '$data' group by po_breakdown_id");
	foreach($dataArrayTrans as $row)
	{
		$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
	}
		
	if(count($grey_receive_qnty_arr)>0){
		$msg[]=count($msg).'. Grey Production Qty Found :'.$grey_receive_qnty_arr[$data];
	}
		
	$greyPurchaseQntyArray=array(); $greyProductionQntyArray=array();
	$sql_grey_purchase="select c.po_breakdown_id, 
	sum(CASE WHEN a.receive_basis<>9 THEN c.quantity ELSE 0 END) AS grey_purchase_qnty,
	sum(CASE WHEN a.receive_basis=9 THEN c.quantity ELSE 0 END) AS grey_production_qnty
	from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22) and c.entry_form in (22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$data' group by c.po_breakdown_id";//and a.receive_basis<>9 sum(c.quantity) as grey_purchase_qnty
	$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
	foreach($dataArrayGreyPurchase as $greyRow)
	{
		$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		$greyProductionQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_production_qnty')];
	}
	
	if(count($greyPurchaseQntyArray)>0){
		$msg[]=count($msg).'. Grey Recv. Purchase Qty Found :'.$greyPurchaseQntyArray[$data];
	}
	
	if(count($greyProductionQntyArray)>0 && $greyProductionQntyArray[$data]>0){
		$msg[]=count($msg).'. Grey Recv. Production Qty Found :'.$greyProductionQntyArray[$data];
	}
	
	$greyDeliveryArray=array();
	$sql_grey_delivery="select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 and order_id='$data' by order_id";
	$data_grey_delivery=sql_select($sql_grey_delivery);
	foreach($data_grey_delivery as $greyDel)
	{
		$greyDeliveryArray[$greyDel[csf('order_id')]]=$greyDel[csf('grey_delivery_qty')];
	}
	
	if(count($greyDeliveryArray)>0){
		$msg[]=count($msg).'. Grey Delivery Qty Found :'.$greyDeliveryArray[$data];
	}
	//var_dump($greyDeliveryArray);
	$finDeliveryArray=array();
	$sql_fin_delivery="select a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.order_id='$data' group by a.order_id, b.color";
	
	$data_fin_delivery=sql_select($sql_fin_delivery);
	foreach($data_fin_delivery as $finDel)
	{
		$finDeliveryArray[$finDel[csf('order_id')]]=$finDel[csf('fin_delivery_qty')];
	}

	if(count($data_fin_delivery)>0 && $data_fin_delivery[$data]>0){
		$msg[]=count($msg).'. Finish Delivery Qty Found :'.$data_fin_delivery[$data];
	}
	
	$trim_array=array();
	$trim_receive_qty_data=sql_select("select b.po_breakdown_id,b.quantity*a.rate as amount   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id='$data ");
	foreach($trim_receive_qty_data as $row)
	{
		$trim_array[$row[csf('po_breakdown_id')]]+=$row[csf('amount')];
	}
	
	if(count($trim_array)>0){
		$msg[]=count($msg).'. Trims Receive Value Found :'.$trim_array[$data];
	}
	
	if(count($msg)>1){
		echo "1**".implode("\n",$msg);
	 disconnect($con);	die;
	}
		
	
	/*$production_quantity=0;
	$sql_data=sql_select( "select count(id) as id from  pro_garments_production_mst where po_break_down_id='$data'  and  status_active=1 and is_deleted=0");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('id')]>0)
		{
		$production_quantity=$row_data[csf('id')];
		}
	}
	echo trim($production_quantity);*/
	 disconnect($con);die;
}

if ($action=="save_update_delete_dtls")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0){
		$packing = $cbo_packing;
	}
	else{
		$packing = $cbo_packing_po_level;
	}
	if (file_exists('dateretriction.php')){
		require('dateretriction.php');
	}
	
	$sql=sql_select("select variable_list, copy_quotation, cost_control_source from variable_order_tracking where company_name=$cbo_company_name and variable_list in (20,53,78) order by id");
	$cost_control_source=0; $is_copy_quatation=2; $poEntryControlWithBomApproval=2;
	foreach($sql as $vrow)
	{
		if($vrow[csf('variable_list')]==20) $is_copy_quatation=$vrow[csf('copy_quotation')];
		if($vrow[csf('variable_list')]==53) $cost_control_source=$vrow[csf('cost_control_source')];
		if($vrow[csf('variable_list')]==78) $poEntryControlWithBomApproval=$vrow[csf('copy_quotation')];
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
	$bomApproved=0;
	if($poEntryControlWithBomApproval==1 || $poEntryControlWithBomApproval==3)
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1 || $isapproved==3)
		{
			$bomApproved=1;
			if($poEntryControlWithBomApproval==1)
			{
				echo "16** Budget approved of this job, Please un-approve the budget and try again.";
				disconnect($con);die;
			}
		}
	}
	 
	// added on temporary basis. CTO
	/*$off_day_ship=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=46");
	if( $off_day_ship==2 )
	{
		$days_status=return_field_value("day_status", "lib_capacity_calc_mst a, lib_capacity_calc_dtls b ", " b.date_calc=$txt_pub_shipment_date and comapny_id=$cbo_company_name and a.id=b.mst_id");
		if($days_status==2)
		{
		    echo "46**1"; 
			die	;
		}
		$days_statusss=return_field_value("day_status", "lib_capacity_calc_mst a, lib_capacity_calc_dtls b ", " b.date_calc=$txt_org_shipment_date and comapny_id=$cbo_company_name and a.id=b.mst_id");
		if($days_statusss==2)
		{
		    echo "46**2"; 
			die	;
		}
	}*/
	// added on temporary basis. CTO
	
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	
	if ($operation==0) //Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($poEntryControlWithBomApproval==3 && $bomApproved==1)//issue id ISD-21-19965
		{
			echo "bomApp**0"; 
			disconnect($con);
			die;
		}
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
			$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date"; else $txt_pub_shipment_date_cond="";	
		
		$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
		$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='knit_order_entry' and file_type=1");
		if($image_mdt==1 && $image=="")
		{
		    echo "24**0"; 
			disconnect($con);
			die;
		}
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="") $org_shipment_date=$txt_pub_shipment_date;
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="") $txt_pub_shipment_date=$txt_org_shipment_date;
		
		//==============================Lead Time Validation ==============================
		$min_lead_time_control=2;
		$sql_min_lead_time_control=sql_select("select min_lead_time_control from variable_order_tracking where company_name=$cbo_company_name and variable_list=51");
		foreach($sql_min_lead_time_control as $row_min_lead_time_control){
			$min_lead_time_control=$row_min_lead_time_control[csf('min_lead_time_control')];
		}
		
		$received_date=date('Y-m-d',strtotime(str_replace("'","",$txt_po_received_date)));
        $pub_shipment_date=date('Y-m-d',strtotime(str_replace("'","",$txt_pub_shipment_date)));
        $dDiff=datediff( 'd', $received_date, $pub_shipment_date, $using_timestamps = false );
		$year=date("Y",strtotime(str_replace("'","",$org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$org_shipment_date)));

		$pub_year=date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)));
	    $pub_month= (int) date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)));
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		if($dDiff < $min_leadtime_allocation && $min_lead_time_control==1){
			echo "LeadTime**0**".$min_leadtime_allocation;
			 disconnect($con);die;
		}
		//====================================================================================
		//==============================capacity Validation ==============================
		$buyer_allocation_maintain=2;
		$capacity_exceed_level=0;
		$sql_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_company_name and variable_list=52");
		foreach($sql_capa_vari as $row_capa_vari){
			$buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
			$capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
		}
		//echo "10**".$capacity_exceed_level.'--'.$buyer_allocation_maintain; die;
		
		$capaBuyerCond=""; $poBuyerCond="";
		if($buyer_allocation_maintain==1){
			$capaBuyerCond="and a.buyer_id=$cbo_buyer_name";
			$poBuyerCond="and b.buyer_name=$cbo_buyer_name";
		}else{
			$capaBuyerCond=""; $poBuyerCond="";
		}
		//==============================capacity Validation For Working Company==============================
		$lc_company_id=str_replace("'","",$cbo_company_name);
		$w_company_id=str_replace("'","",$cbo_working_company_id);
		$cbo_working_location_id=str_replace("'","",$cbo_working_location_id);
		
		$w_buyer_allocation_maintain=2;
		$w_capacity_exceed_level=0;
		$w_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_working_company_id and variable_list=52");
		foreach($w_capa_vari as $row_capa_vari){
			$w_buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
			$w_capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
		}
		$w_capaBuyerCond="";
		$w_poBuyerCond="";
		if($w_buyer_allocation_maintain==1){
			if($lc_company_id==$w_company_id)
			{
				//$w_capaBuyerCond="and b.buyer_id=$cbo_buyer_name";
				//$w_poBuyerCond="and b.buyer_name=$cbo_buyer_name";
			}
			else $w_capaBuyerCond="";
		}else{
			$w_capaBuyerCond=""; $w_poBuyerCond="";
		}
		//End
		$year_month_name=$month.",".$year;
		$sales_target_qty=0; $sales_target_value=0; $sales_target_mint=0;
		$sql_sales_target=sql_select("select sum(sales_target_qty) as sales_target_qty,  sum(sales_target_value) as sales_target_value,   sum(sales_target_mint) as sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.company_id=$cbo_company_name  $capaBuyerCond and b.year_month_name='$year_month_name' and a.status_active=1 and a.is_deleted=0  order by a.id");//and a.team_leader=$cbo_team_leader and  a.starting_year='$year'
		foreach($sql_sales_target as $row_sales_target){
			$sales_target_qty+=$row_sales_target[csf('sales_target_qty')];	;
			$sales_target_value+=$row_sales_target[csf('sales_target_value')];	
			$sales_target_mint+=$row_sales_target[csf('sales_target_mint')];
		}
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		
		if($w_buyer_allocation_maintain==1){
			$sql_allowcat="select a.company_id,b.buyer_id, b.allocation_percentage FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b where a.id=b.mst_id AND a.company_id=$cbo_working_company_id   AND a.month_id=$month AND a.year_id=$year   and b.status_active=1 and b.is_deleted=0 $w_capaBuyerCond";
			$sql_allowcat_result=sql_select($sql_allowcat);
			$tot_allocation_percentage=0;
			foreach($sql_allowcat_result as $row)
			{
				if($row[csf('allocation_percentage')]>0)
				{
					$tot_allocation_percentage+=$row[csf('allocation_percentage')];
					$com_buyer_allocate_arr[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
					$buyer_allocate_percent_arr[$row[csf('buyer_id')]]=$row[csf('allocation_percentage')];
					$allocat_buyer_id.=$row[csf('buyer_id')].',';
				}
				if($row[csf('allocation_percentage')]>0) $allocat_buyer_id2.=$row[csf('buyer_id')].',';
				else $unallocat_buyer_id.=$row[csf('buyer_id')].',';
			}
			$buyer_remain_allocate_percent=100-$tot_allocation_percentage;
			//echo  $tot_allocation_percentage.'A'.$buyer_remain_allocate_percent;
			
			$allocat_buyer_ids=rtrim($allocat_buyer_id,',');
			$unallocat_buyer_ids=rtrim($unallocat_buyer_id,',');
			if($lc_company_id!=$w_company_id)
			{
				if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
			}
			
			$w_poBuyerCond='';
			if($lc_company_id==$w_company_id)
			{
				$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
				
				if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
				{
					if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
				}
				else
				{
					if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name in($buyer_id) ";else $w_poBuyerCond='';
				}
			}
		}
			
  
		$sql_con_capa="SELECT sum(capacity_pcs) as sales_target_qty, sum(d.capacity_min) as capacity_min FROM  lib_capacity_calc_mst c,  lib_capacity_calc_dtls d WHERE c.id=d.mst_id AND c.comapny_id=$cbo_working_company_id AND c.year=$pub_year and d.month_id=$pub_month and  d.capacity_min>0 and c.status_active=1 and c.is_deleted=0";
		//echo "10**".$sql_con_capa;die;
		$con_capa_result=sql_select($sql_con_capa);
		foreach($con_capa_result as $row)
		{
			$tot_company_capacity_min=$row[csf('capacity_min')];
			$working_sales_target_qty+=$row[csf('sales_target_qty')];	;
			$working_sales_target_value+=$row[csf('sales_target_value')];	
		}
		if($buyer_allocation_maintain==1){
			if($capacity_exceed_level==1){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==2){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price + str_replace("'","",$txt_amount);
				if($totPrice > $sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==3){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv>$sales_target_mint){
					echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==4){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");// and b.team_leader=$cbo_team_leader 
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==5){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader 
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price + str_replace("'","",$txt_amount);
				if($totPrice > $sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					 disconnect($con);die;
				}
			}
			if($capacity_exceed_level==6){
				
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv>$sales_target_mint){
					//echo "CapaCityMin";
					echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
					disconnect($con);die;
				}
			}
			if($capacity_exceed_level==7){ //Working Company
				$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
				if($db_type==2)
				{
				$date_from=change_date_format($pub_shipment_date,'','',1);
				$date_to=change_date_format($pub_shipment_date,'','',1);
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$second_month_ldate=date("Y-M-t",strtotime($date_to));
				$ship_last_day=change_date_format($second_month_ldate,'','',1);
				$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
				}
				$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.order_uom, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond  $allocat_buyer_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_confirmed=1 $pub_date_upto";
				//echo "10**".$sql_po; die;	
				$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
				$w_sql_po=sql_select($sql_po);
				foreach($w_sql_po as $row_po){
					$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
					$pcs_qty=$row_po[csf('total_set_qnty')];
					if($w_buyer_allocation_maintain==1)//Yes
					{
						if($lc_company_id==$w_company_id)
						{
							$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
							
							if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
							}
							else
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
							}
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
						}
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
					}
				}
				//echo "10**".$working_sales_target_qty.'-'.$w_buyer_allocation_maintain; die;
	
				if($w_buyer_allocation_maintain==1)//Yes
				{
					//$tot_buyer_capacity_min=$tot_company_capacity_min;
					//$tot_buyer_capacity_min=$tot_company_capacity_min;
					$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];	
					//echo "10**".$buyer_allocate_percent; die;			
					if($lc_company_id==$w_company_id)
					{
						if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
						{					
							$buyer_allocate_percent=$buyer_remain_allocate_percent;
							$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
						else
						{
							$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
							$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}					
					}
					else
					{
						$buyer_allocate_percent=100-$tot_allocation_percentage;
						$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
				}
				else
				{
					$total_company_capacity_min=$working_sales_target_qty;
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				//echo "10**".$total_company_capacity_min; die;
				if($w_tot_po_qty>$total_company_capacity_min){
					echo "CapaCityQty**".$w_tot_po_qty."**".$total_company_capacity_min;
					disconnect($con);die;
				}
			}
			if($capacity_exceed_level==12){ //Working Company
				$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
				if($db_type==2)
				{
				$date_from=change_date_format($pub_shipment_date,'','',1);
				$date_to=change_date_format($pub_shipment_date,'','',1);
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$second_month_ldate=date("Y-M-t",strtotime($date_to));
				$ship_last_day=change_date_format($second_month_ldate,'','',1);
				$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
				}
				else
				{
					$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$second_month_ldate=date("Y-m-t",strtotime($date_to));
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
					$pub_date_upto=" and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day' ";
				}
				$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond  $allocat_buyer_cond and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.working_location_id = $cbo_working_location_id $pub_date_upto";
				//echo "16**".$sql_po; die;
			
				$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
				$w_sql_po=sql_select($sql_po);
				//echo "16**".$w_buyer_allocation_maintain; die;
				foreach($w_sql_po as $row_po){
					$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
					//$allo_buyer_percn=$buyer_allocate_percent_arr[$buyer_id];
					//if($allo_buyer_percn=='' || $allo_buyer_percn==0) $allo_buyer_percn=0;else $allo_buyer_percn=$allo_buyer_percn;
					//$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					if($w_buyer_allocation_maintain==1)//Yes
					{
						if($lc_company_id==$w_company_id)
						{
							$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
							
							if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
							}
							else
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
							}
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
				}
				if($w_buyer_allocation_maintain==1)//Yes
				{
					$tot_buyer_capacity_min=$tot_company_capacity_min;
					$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
					
					if($lc_company_id==$w_company_id)
					{
						if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
						{
						
							$buyer_allocate_percent=$buyer_remain_allocate_percent;
							$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
						else
						{
							$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
							$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
	
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
							//echo "16**".$w_tot_po_qty; die;
							//echo  $w_tot_po_qty.'b';
						}
					}
					else
					{
						$buyer_allocate_percent=100-$tot_allocation_percentage;
						$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
				}
				else
				{
					$total_company_capacity_min=$tot_company_capacity_min;
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				if($w_tot_po_qty>$total_company_capacity_min){
					echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
					disconnect($con);die;
				}
			}
		}
		if($buyer_allocation_maintain==3){
			if($capacity_exceed_level==1){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==2){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price + str_replace("'","",$txt_amount);
				if($totPrice > $sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==3){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv>$sales_target_mint){
					echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==4){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");// and b.team_leader=$cbo_team_leader 
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					 disconnect($con);die;
				}
			}		
			if($capacity_exceed_level==5){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader 
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price + str_replace("'","",$txt_amount);
				if($totPrice > $sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					 disconnect($con);die;
				}
			}
			if($capacity_exceed_level==6){
				
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and a.t_month=$month and  a.t_year=$year    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv>$sales_target_mint){
					//echo "CapaCityMin";
					echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
					disconnect($con);die;
				}
			}
			if($capacity_exceed_level==7){ //Working Company
				$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
				if($db_type==2)
				{
					$date_from=change_date_format($pub_shipment_date,'','',1);
					$date_to=change_date_format($pub_shipment_date,'','',1);
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$second_month_ldate=date("Y-M-t",strtotime($date_to));
					$ship_last_day=change_date_format($second_month_ldate,'','',1);
					$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
				}
				$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.order_uom, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_confirmed=1 $pub_date_upto";
				//echo "10**".$sql_po; die;	
				$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
				$w_sql_po=sql_select($sql_po);
				foreach($w_sql_po as $row_po){
					$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
					$pcs_qty=$row_po[csf('total_set_qnty')];
					$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
				}
				//echo "10**".$working_sales_target_qty.'-'.$w_buyer_allocation_maintain; die;
	
				$total_company_capacity_min=$working_sales_target_qty;
				$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
				$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				if($w_tot_po_qty>$total_company_capacity_min){
					echo "CapaCityQty**".$w_tot_po_qty."**".$total_company_capacity_min;
					disconnect($con);die;
				}
			}
			if($capacity_exceed_level==10){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id and a.t_month=$month and  a.t_year=$year   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
				//echo "10**SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id and a.t_month=$month and  a.t_year=$year   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0"; die;
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')]*$row_po[csf('total_set_qnty')];
				}
				$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
				$totPoqty=$po_quantity+$w_curr_po_qty;
				//echo "10**".$totPoqty.'--'.$working_sales_target_qty; die;
				if($totPoqty>$working_sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$working_sales_target_qty;
					 disconnect($con);die;
				}
			}
			if($capacity_exceed_level==12){ //Working Company
				$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
				if($db_type==2)
				{
					$date_from=change_date_format($pub_shipment_date,'','',1);
					$date_to=change_date_format($pub_shipment_date,'','',1);
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$second_month_ldate=date("Y-M-t",strtotime($date_to));
					$ship_last_day=change_date_format($second_month_ldate,'','',1);
					$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
				}
				else
				{
					$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$second_month_ldate=date("Y-m-t",strtotime($date_to));
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
					$pub_date_upto=" and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day' ";
				}
				$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.working_location_id = $cbo_working_location_id $pub_date_upto";
				//echo "16**".$sql_po; die;
			
				$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
				$w_sql_po=sql_select($sql_po);
				//echo "16**".$w_buyer_allocation_maintain; die;
				foreach($w_sql_po as $row_po){
					$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$total_company_capacity_min=$tot_company_capacity_min;
				$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				if($w_tot_po_qty>$total_company_capacity_min){
					echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
					disconnect($con);die;
				}
			}
		}
	
		
		//==============================capacity Validation ==============================
		
		$txt_po_no=str_replace("'",'',$txt_po_no);
		$pono='';
		$pono=trim(str_replace($str_replace_check,'',$txt_po_no));
		
		$txt_details_remark=str_replace("'",'',$txt_details_remark);
		$dtls_remark='';
		$dtls_remark=trim(str_replace($str_replace_check,'',$txt_details_remark));
		
		$id=return_next_id("id", "wo_po_break_down", 1) ;
		//confirm and projection check for issue id 6952 
		if (str_replace("'","",$cbo_order_status)==1)
		{
			$field_array="id, job_no_mst, job_id, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,txt_etd_ldd, factory_received_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, file_no, is_deleted, status_active, inserted_by, insert_date, sc_lc, with_qty";
			$data_array="(".$id.",".$update_id.",".$hidd_job_id.",".$cbo_order_status.",'".$pono."',".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_etd_ldd.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",'".$dtls_remark."',".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_file_no.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_sc_lc.",".$with_qty.")";
		}
		else
		{
			$field_array="id, job_no_mst,job_id, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date,txt_etd_ldd, factory_received_date, po_quantity, unit_price, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, original_po_qty, file_no, is_deleted, status_active, inserted_by, insert_date, sc_lc, with_qty";
			$data_array="(".$id.",".$update_id.",".$hidd_job_id.",".$cbo_order_status.",'".$pono."',".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_etd_ldd.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",'".$dtls_remark."',".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_po_quantity.",".$txt_file_no.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_sc_lc.",".$with_qty.")";
		}
        //echo "5**insert into wo_po_break_down (".$field_array.") Values ".$data_array."";die;
		$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);		
		//====================================================================================

      
		if(str_replace("'","",$update_id_details)=="")
		{
			$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
			$field_array1="id, po_break_down_id, job_no_mst, job_id, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total ,excess_cut_perc, plan_cut_qnty, is_deleted, status_active, inserted_by, insert_date";
			$add_comma=0;
			$new_array_size=array(); $new_array_color=array();
			$set_breck_down=explode('__',str_replace("'",'',$set_breck_down));
			if ( count($set_breck_down)>0 && $defult_color==1 && str_replace("'","",$cbo_order_status)==2)
			{
				$txt_avg_price_color=str_replace("'",'',$txt_avg_price)/str_replace("'",'',$tot_set_qnty);
				for($c=0;$c < count($set_breck_down);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down[$c]);
					$cbogmtsitem=$set_breck_down_arr[0];
					$cbogmtsitem_ratio=$set_breck_down_arr[1];
					$txtorderquantity=str_replace("'",'',$txt_po_quantity)*$cbogmtsitem_ratio;
					$txtorderamount=str_replace("'",'',$txt_avg_price_color)*$txtorderquantity;
					$txtorderplancut=str_replace("'",'',$txt_plan_cut)*$cbogmtsitem_ratio;
					
					if (!in_array(TBA,$new_array_color))
					 {
						  $color_id = return_id( TBA, $color_library, "lib_color", "id,color_name","401");  
						  $new_array_color[$color_id]=TBA;
					 }
					 else $color_id =  array_search(TBA, $new_array_color);
					 
					 if (!in_array(TBA,$new_array_size))
					 {
						  $size_id = return_id(TBA, $size_library, "lib_size", "id,size_name","401");   
						  $new_array_size[$size_id]=TBA;
					 }
					 else $size_id =  array_search(TBA, $new_array_size); 
					 
					 $txtarticleno="no article";
					if ($add_comma!=0) $data_array1 .=",";
					 $data_array1 .="(".$id1.",".$id.",".$update_id.",".$hidd_job_id.",".$id1.",".$id1.",".$id1.",".$id1.",'".$txtarticleno."','".$cbogmtsitem."','245',".$txt_pub_shipment_date.",".$size_id.",".$color_id.",'".$txtorderquantity."',".$txt_avg_price_color.",'".$txtorderamount."',".$txt_excess_cut.",'".$txtorderplancut."',0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 $id1=$id1+1;
					 $add_comma++;
				}
			}
		}
		
		if(str_replace("'","",$update_id_details)!="")
		{
			$color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id" );
			$size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id" );
			$item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id" );
			
			$i=1;
			$data_array1="";
			$id_co=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
			$field_array1="id, po_break_down_id, job_no_mst, job_id, color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, country_remarks, color_order, size_order, is_deleted, status_active, inserted_by, insert_date";
			
			$sql_se_co=sql_select("select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, shiping_status, color_order, size_order, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, country_remarks from wo_po_color_size_breakdown where job_no_mst=$update_id and po_break_down_id=".$update_id_details." and is_deleted=0 and status_active=1");
			//echo "select id, po_break_down_id, job_no_mst,color_mst_id,size_mst_id,item_mst_id,country_mst_id,article_number,item_number_id,country_id,cutup_date,cutup,country_ship_date,size_number_id, 	color_number_id,order_quantity,order_rate,order_total,excess_cut_perc,plan_cut_qnty,shiping_status,color_order,size_order,is_deleted,is_used,inserted_by,insert_date,updated_by,update_date,status_active,is_locked,country_remarks from wo_po_color_size_breakdown  where job_no_mst=$update_id and po_break_down_id=".$update_id_details."";
			foreach($sql_se_co as $row_se_co)
			{
				if (array_key_exists($row_se_co[csf('item_number_id')],$item_mst))
				{
					$item_mst_id=$item_mst[$row_se_co[csf('item_number_id')]];
				}
				else
				{
					$item_mst[$row_se_co[csf('item_number_id')]]=$id_co;
					$item_mst_id=$id_co;
				}
				if(array_key_exists($row_se_co[csf('color_number_id')],$color_mst))
				{
					$color_mst_id=$color_mst[$row_se_co[csf('color_number_id')]];	
				}
				else
				{
					$color_mst[$row_se_co[csf('color_number_id')]]=$id_co;
					$color_mst_id=$id_co;
				}
				
				if(array_key_exists($row_se_co[csf('size_number_id')],$size_mst))
				{
					$size_mst_id=$size_mst[$row_se_co[csf('size_number_id')]];	 
				}
				else
				{
					$size_mst[$row_se_co[csf('size_number_id')]]=$id_co;
					$size_mst_id=$id_co;
				}
				
				if ($i!=1) $data_array1 .=",";
				$data_array1 .="(".$id_co.",".$id.",".$update_id.",".$hidd_job_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$row_se_co[csf('article_number')]."',".$row_se_co[csf('item_number_id')].",".$row_se_co[csf('country_id')].",'".$row_se_co[csf('cutup_date')]."','".$row_se_co[csf('cutup')]."','".$row_se_co[csf('country_ship_date')]."',".$row_se_co[csf('size_number_id')].",".$row_se_co[csf('color_number_id')].",".$row_se_co[csf('order_quantity')].",".$row_se_co[csf('order_rate')].",".$row_se_co[csf('order_total')].",'".$row_se_co[csf('excess_cut_perc')]."',".$row_se_co[csf('plan_cut_qnty')].",'".$row_se_co[csf('country_remarks')]."','".$row_se_co[csf('color_order')]."','".$row_se_co[csf('size_order')]."',0,".$row_se_co[csf('status_active')].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_co=$id_co+1;
				$i++;
			}
			$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
		}
		//echo "10**insert into wo_po_color_size_breakdown (".$field_array1.") values ".$data_array1."";die;
		
		//============================================================================================
		
		 $sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 //$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$cbo_buyer_name order by sequ");
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 		
		 $data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id=$id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
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
		 }
		//============================================================================================
		//============================================================================================
		 $lap=1;
		 $id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted"; 		
		 $data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id=$id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
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
		 }
		//============================================================================================
		execute_query("update wo_pre_cost_mst set isorder_change='$bom_sync_msg' where job_no =".$update_id." and status_active=1 and is_deleted=0 and isorder_change=0",1);
		
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		//=============================================================================================
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) //Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
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
		$cu_order_val=str_replace("'","",$txt_amount);
		if($cu_order_val<$tot_attach_value)
		{
			echo "16**Order Value Not Allowed Less Then Attach Value.";disconnect($con);die;
		}
		//$update_id_details
		
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
		
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
			$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1)
		{
			$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		}
		else
		{
			$txt_pub_shipment_date_cond="";	
		}
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
		
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		
		$color_size_value=sql_select("select sum(b.order_quantity/a.total_set_qnty) as order_qnty from wo_po_details_master a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  and b.is_deleted=0 and b.po_break_down_id=$update_id_details");
		$color_size_qnty=$color_size_value[0][csf("order_qnty")]*1;
		if($color_size_qnty) $color_size_qnty=$color_size_qnty;else $color_size_qnty=0;
		$po_qnty=str_replace("'","",$txt_po_quantity)*1;
		if($color_size_qnty>0)
		{
			if($po_qnty<$color_size_qnty && str_replace("'","",$with_qty)==1)
			{
				echo "50**Order Quantity Not Allowed Less Then Color Size Breakdown Quantity."; disconnect($con);die;
			}
		}
		
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="")
		{
			$org_shipment_date=$txt_pub_shipment_date;
		}
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="")
		{
			$txt_pub_shipment_date=$txt_org_shipment_date;
		}
		//==============================Lead Time Validation ==============================
		$min_lead_time_control=2;
		$sql_min_lead_time_control=sql_select("select min_lead_time_control from variable_order_tracking where company_name=$cbo_company_name and variable_list=51");
		foreach($sql_min_lead_time_control as $row_min_lead_time_control){
			$min_lead_time_control=$row_min_lead_time_control[csf('min_lead_time_control')];
		}
		 
		$received_date=date('Y-m-d',strtotime(str_replace("'","",$txt_po_received_date)));
        $pub_shipment_date=date('Y-m-d',strtotime(str_replace("'","",$txt_pub_shipment_date)));
        $dDiff=datediff( 'd', $received_date, $pub_shipment_date, $using_timestamps = false );
		$year=date("Y",strtotime(str_replace("'","",$org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$org_shipment_date)));

		$pub_year=date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)));
	    $pub_month= (int) date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)));
		
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		if($dDiff < $min_leadtime_allocation && $min_lead_time_control==1){
			echo "LeadTime**0**".$min_leadtime_allocation;
			disconnect($con);die;
		}
		
		//=============================================================
		//==============================capacity Validation ==============================
		if(str_replace("'","",$cbo_status)==1){				
			$buyer_allocation_maintain=2; $capacity_exceed_level=0;
			$sql_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_company_name and variable_list=52");
			foreach($sql_capa_vari as $row_capa_vari){
				$buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
				$capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
			}
			
			$capaBuyerCond=""; $poBuyerCond="";
			if($buyer_allocation_maintain==1){
				$capaBuyerCond="and a.buyer_id=$cbo_buyer_name";
				$poBuyerCond="and b.buyer_name=$cbo_buyer_name";
			}else{
				$capaBuyerCond=""; $poBuyerCond="";
			}
			//==============================capacity Validation For Working Company==============================
			$lc_company_id=str_replace("'","",$cbo_company_name);
			$w_company_id=str_replace("'","",$cbo_working_company_id);
			$buyer_id=str_replace("'","",$cbo_buyer_name);
			
			$w_buyer_allocation_maintain=2;
			$w_capacity_exceed_level=0;
			$w_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_working_company_id and variable_list=52");
			foreach($w_capa_vari as $row_capa_vari){
				$w_buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
				$w_capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
			}
			$w_capaBuyerCond="";
			$w_poBuyerCond="";
			if($w_buyer_allocation_maintain==1){
				if($lc_company_id==$w_company_id)
				{
					//$w_capaBuyerCond="and b.buyer_id=$cbo_buyer_name";
					//$w_poBuyerCond="and b.buyer_name=$cbo_buyer_name";
				}
				else
				{
					$w_capaBuyerCond="";
					//$w_poBuyerCond="";
				}
			
			}else{
				$w_capaBuyerCond=""; $w_poBuyerCond="";
			}
			//End
		
			$year_month_name=$month.",".$year;
			$sales_target_qty=0; $sales_target_value=0; $sales_target_mint=0;
			$sql_sales_target=sql_select("select sum(sales_target_qty) as sales_target_qty,  sum(sales_target_value) as sales_target_value,   sum(sales_target_mint) as sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.company_id=$cbo_company_name   $capaBuyerCond and  b.year_month_name='$year_month_name' and a.status_active=1 and a.is_deleted=0  order by a.id");//and a.team_leader='$cbo_team_leader'and  a.starting_year='$year'
			foreach($sql_sales_target as $row_sales_target){
				$sales_target_qty=$row_sales_target[csf('sales_target_qty')];	;
				$sales_target_value=$row_sales_target[csf('sales_target_value')];	
				$sales_target_mint=$row_sales_target[csf('sales_target_mint')];
			}
			
			if($w_buyer_allocation_maintain==1){
				$sql_allowcat="select  b.buyer_id, b.allocation_percentage FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b
				 where a.id=b.mst_id AND a.company_id=$cbo_working_company_id   AND a.month_id=$month AND a.year_id=$year  and b.allocation_percentage is not null and b.status_active=1 and b.is_deleted=0 $w_capaBuyerCond";
			 	$sql_allowcat_result=sql_select($sql_allowcat);
				 $tot_allocation_percentage=0;
				foreach($sql_allowcat_result as $row)
				{
					$buyer_allocate_percent_arr[$row[csf('buyer_id')]]=$row[csf('allocation_percentage')];
					$com_buyer_allocate_arr[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
					$tot_allocation_percentage+=$row[csf('allocation_percentage')];
					$allocat_buyer_id.=$row[csf('buyer_id')].',';
				}
				$buyer_remain_allocate_percent=100-$tot_allocation_percentage;
				if($lc_company_id!=$w_company_id)
				{
					$allocat_buyer_ids=rtrim($allocat_buyer_id,',');
					if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
				}
				$w_poBuyerCond='';
				if($lc_company_id==$w_company_id)
				{
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
					
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						//if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name not in($buyer_id) ";else $w_poBuyerCond='';
						if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
					}
					else
					{
						if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name in($buyer_id) ";else $w_poBuyerCond='';
					}
				}
			}
			$sql_con_capa="SELECT sum(capacity_pcs) as sales_target_qty, sum(d.capacity_min) as capacity_min FROM  lib_capacity_calc_mst c,  lib_capacity_calc_dtls d WHERE c.id=d.mst_id AND c.comapny_id=$cbo_working_company_id AND c.year=$pub_year and d.month_id=$pub_month and c.status_active=1 and c.is_deleted=0";
			//echo "10**".$sql_con_capa;die;
			$con_capa_result=sql_select($sql_con_capa);
			foreach($con_capa_result as $row)
			{
				$tot_company_capacity_min=$row[csf('capacity_min')];
				$working_sales_target_qty+=$row[csf('sales_target_qty')];	;
				$working_sales_target_value+=$row[csf('sales_target_value')];	
			}
			if($buyer_allocation_maintain==1){
				if($capacity_exceed_level==1){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
					$po_quantity=0;
					foreach($sql_po as $row_po){
						$po_quantity+=$row_po[csf('po_quantity')];
					}
					$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
					if($totPoqty>$sales_target_qty){
						echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==2){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
					$po_total_price=0;
					foreach($sql_po as $row_po){
						$po_total_price+=$row_po[csf('po_total_price')];
					}
					$totPrice=$po_total_price+str_replace("'","",$txt_amount);
					if($totPrice>$sales_target_value){
						echo "CapaCityValue**".$totPrice."**".$sales_target_value;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==3){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
					$smv=0;
					foreach($sql_po as $row_po){
						$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
					$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$totsmv=$smv+$curr_smv;
					if($totsmv>$sales_target_mint){
						echo "CapaCityMin**".$po_quantity."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==4){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
					$po_quantity=0;
					foreach($sql_po as $row_po){
						$po_quantity+=$row_po[csf('po_quantity')];
					}
					$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
					if($totPoqty>$sales_target_qty){
						echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==5){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
					$po_total_price=0;
					foreach($sql_po as $row_po){
						$po_total_price+=$row_po[csf('po_total_price')];
					}
					$totPrice=$po_total_price+str_replace("'","",$txt_amount);
					if($totPrice>$sales_target_value){
						echo "CapaCityValue**".$totPrice."**".$sales_target_value;
						disconnect($con);die;
					}
				}
				if($capacity_exceed_level==6){
					$previous_smv_arr=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv, a.t_month from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id =$update_id_details");
	
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
					$smv=0;
					foreach($sql_po as $row_po){
						$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
					$previous_smv=0;
					foreach ($previous_smv_arr as $row) {
						$previous_smv=$row[csf('po_quantity')]*$row[csf('set_smv')];
						$previous_month=$row[csf('t_month')];
					}
					
					$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$totsmv=$smv+$curr_smv;
					//echo "10**".$sales_target_mint.'--'.$totsmv; die;
					if($previous_smv != $curr_smv || $previous_month!=$month)
					{
						if($curr_smv>$previous_smv || $previous_month!=$month)
						{
							if($totsmv > $sales_target_mint){
								echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
								 disconnect($con);die;
							}
						}					
					}				
				}
				if($capacity_exceed_level==7){ //Working Company
					$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
					if($db_type==2)
					{
					$date_from=change_date_format($pub_shipment_date,'','',1);
					$date_to=change_date_format($pub_shipment_date,'','',1);
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$second_month_ldate=date("Y-M-t",strtotime($date_to));
					$ship_last_day=change_date_format($second_month_ldate,'','',1);
					$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
					}
					$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.order_uom, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond  $allocat_buyer_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_confirmed=1 and a.id !=$update_id_details $pub_date_upto";
					//echo "10**".$sql_po; die;	
					$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
					$w_sql_po=sql_select($sql_po);
					foreach($w_sql_po as $row_po){
						$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
						$pcs_qty=$row_po[csf('total_set_qnty')];
						if($w_buyer_allocation_maintain==1)//Yes
						{
							if($lc_company_id==$w_company_id)
							{
								$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
								
								if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
								{
									$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
								}
								else
								{
									$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
								}
							}
							else
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
							}
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
						}
					}
					//echo "10**".$w_tot_prev_po_qty.'---'.$tot_set_qnty; die;
					if($w_buyer_allocation_maintain==1)//Yes
					{
						//$tot_buyer_capacity_min=$tot_company_capacity_min;
						//$tot_buyer_capacity_min=$tot_company_capacity_min;
						$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
						$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];				
						if($lc_company_id==$w_company_id)
						{
							if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
							{					
								$buyer_allocate_percent=$buyer_remain_allocate_percent;
								$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
								$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
								$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
							}
							else
							{
								$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
								$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
								$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
								$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
							}					
						}
						else
						{
							$buyer_allocate_percent=100-$tot_allocation_percentage;
							$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
					}
					else
					{
						$total_company_capacity_min=$working_sales_target_qty;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
					//echo "10**".$total_company_capacity_min; die;
					if($w_tot_po_qty>$total_company_capacity_min){
						echo "CapaCityQty**".$w_tot_po_qty."**".$total_company_capacity_min;
						disconnect($con);die;
					}
				}
				if($capacity_exceed_level==12){ //Working Company
					$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
					if($db_type==2)
					{
						$date_from=change_date_format($pub_shipment_date,'','',1);
						$date_to=change_date_format($pub_shipment_date,'','',1);
						$dateFrom= explode("-",$date_from);
						$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
						$second_month_ldate=date("Y-M-t",strtotime($date_to));
						$ship_last_day=change_date_format($second_month_ldate,'','',1);
						$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
					}
				}
				else
				{
					$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
					$second_month_ldate=date("Y-m-t",strtotime($date_to));
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
					$pub_date_upto=" and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day' ";
				}
				$w_sql_po=sql_select("SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond $allocat_buyer_cond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details and b.working_location_id = $cbo_working_location_id $pub_date_upto");
				
				$w_tot_prev_po_qty=0;
				foreach($w_sql_po as $row_po){
					$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
						//$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					if($w_buyer_allocation_maintain==1)//Yes
					{
						if($lc_company_id==$w_company_id)
						{
							$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
							
							if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
							}
							else
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
							}
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
				}
				$buyer_id=str_replace("'","",$cbo_buyer_name);
				if($w_buyer_allocation_maintain==1)//Yes
				{
					$tot_buyer_capacity_min=$tot_company_capacity_min;
					$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];				
					if($lc_company_id==$w_company_id)
					{
						if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
						{
							$buyer_allocate_percent=$buyer_remain_allocate_percent;
							$total_company_capacity_min=$tot_buyer_capacity_min-$w_tot_prev_po_qty;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
						else
						{
							$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
							$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
							
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
					}
					else
					{
						$buyer_allocate_percent=100-$tot_allocation_percentage;
						$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
						
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
				}
				else
				{
					$total_company_capacity_min=$tot_company_capacity_min;
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				if($w_tot_po_qty>$total_company_capacity_min){
					echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
					disconnect($con);die;
				}			
			}
			if($buyer_allocation_maintain==3){
				if($capacity_exceed_level==1){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
					$po_quantity=0;
					foreach($sql_po as $row_po){
						$po_quantity+=$row_po[csf('po_quantity')];
					}
					$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
					if($totPoqty>$sales_target_qty){
						echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==2){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
					$po_total_price=0;
					foreach($sql_po as $row_po){
						$po_total_price+=$row_po[csf('po_total_price')];
					}
					$totPrice=$po_total_price+str_replace("'","",$txt_amount);
					if($totPrice>$sales_target_value){
						echo "CapaCityValue**".$totPrice."**".$sales_target_value;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==3){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
					$smv=0;
					foreach($sql_po as $row_po){
						$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
					$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$totsmv=$smv+$curr_smv;
					if($totsmv>$sales_target_mint){
						echo "CapaCityMin**".$po_quantity."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==4){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
					$po_quantity=0;
					foreach($sql_po as $row_po){
						$po_quantity+=$row_po[csf('po_quantity')];
					}
					$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
					if($totPoqty>$sales_target_qty){
						echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
						disconnect($con);die;
					}
				}			
				if($capacity_exceed_level==5){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
					$po_total_price=0;
					foreach($sql_po as $row_po){
						$po_total_price+=$row_po[csf('po_total_price')];
					}
					$totPrice=$po_total_price+str_replace("'","",$txt_amount);
					if($totPrice>$sales_target_value){
						echo "CapaCityValue**".$totPrice."**".$sales_target_value;
						disconnect($con);die;
					}
				}
				if($capacity_exceed_level==6){
					$previous_smv_arr=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv, a.t_month from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id =$update_id_details");
	
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
					$smv=0;
					foreach($sql_po as $row_po){
						$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
					$previous_smv=0;
					foreach ($previous_smv_arr as $row) {
						$previous_smv=$row[csf('po_quantity')]*$row[csf('set_smv')];
						$previous_month=$row[csf('t_month')];
					}
					
					$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$totsmv=$smv+$curr_smv;
					//echo "10**".$sales_target_mint.'--'.$totsmv; die;
					if($previous_smv != $curr_smv || $previous_month!=$month)
					{
						if($curr_smv>$previous_smv || $previous_month!=$month)
						{
							if($totsmv > $sales_target_mint){
								echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
								 disconnect($con);die;
							}
						}					
					}				
				}
				if($capacity_exceed_level==7){ //Working Company
					$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
					if($db_type==2)
					{
					$date_from=change_date_format($pub_shipment_date,'','',1);
					$date_to=change_date_format($pub_shipment_date,'','',1);
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$second_month_ldate=date("Y-M-t",strtotime($date_to));
					$ship_last_day=change_date_format($second_month_ldate,'','',1);
					$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
					}
					$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.order_uom, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond  $allocat_buyer_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_confirmed=1 and a.id !=$update_id_details $pub_date_upto";
					//echo "10**".$sql_po; die;	
					$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
					$w_sql_po=sql_select($sql_po);
					foreach($w_sql_po as $row_po){
						$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
						$pcs_qty=$row_po[csf('total_set_qnty')];
						if($w_buyer_allocation_maintain==1)//Yes
						{
							if($lc_company_id==$w_company_id)
							{
								$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
								
								if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
								{
									$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
								}
								else
								{
									$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
								}
							}
							else
							{
								$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
							}
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$pcs_qty;
						}
					}
					//echo "10**".$w_tot_prev_po_qty.'---'.$tot_set_qnty; die;
					if($w_buyer_allocation_maintain==1)//Yes
					{
						//$tot_buyer_capacity_min=$tot_company_capacity_min;
						//$tot_buyer_capacity_min=$tot_company_capacity_min;
						$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
						$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];				
						if($lc_company_id==$w_company_id)
						{
							if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
							{					
								$buyer_allocate_percent=$buyer_remain_allocate_percent;
								$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
								$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
								$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
							}
							else
							{
								$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
								$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
								$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
								$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
							}					
						}
						else
						{
							$buyer_allocate_percent=100-$tot_allocation_percentage;
							$total_company_capacity_min=($working_sales_target_qty*$buyer_allocate_percent)/100;
							$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
							$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						}
					}
					else
					{
						$total_company_capacity_min=$working_sales_target_qty;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
					//echo "10**".$total_company_capacity_min; die;
					if($w_tot_po_qty>$total_company_capacity_min){
						echo "CapaCityQty**".$w_tot_po_qty."**".$total_company_capacity_min;
						disconnect($con);die;
					}
				}
				if($capacity_exceed_level==10){
					$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
					$po_quantity=0;
					foreach($sql_po as $row_po){
						$po_quantity+=$row_po[csf('po_quantity')]*$row_po[csf('total_set_qnty')];
					}
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_set_qnty);
                	$totPoqty=$po_quantity+$w_curr_po_qty;
					//$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
					if($totPoqty>$working_sales_target_qty){
						echo "CapaCityQty**".$totPoqty."**".$working_sales_target_qty;
						disconnect($con);die;
					}
				}
				if($capacity_exceed_level==12){ //Working Company
					$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
					if($db_type==2)
					{
					$date_from=change_date_format($pub_shipment_date,'','',1);
					$date_to=change_date_format($pub_shipment_date,'','',1);
					$dateFrom= explode("-",$date_from);
					$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
					$second_month_ldate=date("Y-M-t",strtotime($date_to));
					$ship_last_day=change_date_format($second_month_ldate,'','',1);
					$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
					}
				}
			}
						
		
		}
		//==============================capacity Validation ==============================
		$is_produced=0;
		if(str_replace("'","",$cbo_status)!=1) //Inactive/Cancel
		{
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
				}
			}
			
			if($is_produced==1 && $_SESSION['logic_erp']["user_level"]!=2)//ISD-23-04662
			{
				echo "ProdFound**0";
				disconnect($con);die;
			}
		}
		//echo "10**".$is_produced.'-'.$update_id_details; disconnect($con); die;
		$prev_data=sql_select("SELECT is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, file_no, is_deleted, status_active, updated_by, update_date, po_number_prev, pub_shipment_date_prev FROM wo_po_break_down WHERE id=$update_id_details");
		foreach($prev_data as $rows)
		{
			$prev_po_no=$rows[csf('po_number')];
			$prev_order_status=$rows[csf('is_confirmed')];
			$prev_po_received_date=$rows[csf('po_received_date')];
			$prev_po_qty=$rows[csf('po_quantity')];
			$prev_pub_shipment_date=$rows[csf('pub_shipment_date')];
			$prev_status=$rows[csf('status_active')];
			$prev_org_shipment_date=$rows[csf('shipment_date')];
			$prev_factory_rec_date=$rows[csf('factory_received_date')];
			$prev_projected_po=$rows[csf('projected_po_id')];
			$prev_packing=$rows[csf('packing')];
			$prev_details_remark=$rows[csf('details_remarks')];
			$prev_file_no=$rows[csf('file_no')];
			$prev_avg_price=$rows[csf('unit_price')];
			$prev_excess_cut=$rows[csf('excess_cut')];
			$prev_plan_cut=$rows[csf('plan_cut')];
			$prev_status=$rows[csf('status_active')];
			$prev_updated_by=$rows[csf('updated_by')];
			$prev_update_date=$rows[csf('update_date')];
			
			$prev_pono=$rows[csf('po_number_prev')];
			$prev_pubship_date=$rows[csf('pub_shipment_date_prev')];
		}
		
		if($prev_po_no==str_replace("'","",$txt_po_no))
		{
			$pre_po_no=$prev_pono;
		}
		else $pre_po_no=$prev_po_no;
		
		if( change_date_format($prev_pub_shipment_date)==change_date_format(str_replace("'","",$txt_pub_shipment_date)))
		{
			$pre_pubship_date=$prev_pubship_date;
		}
		else $pre_pubship_date=$prev_pub_shipment_date;
		
		$project_fab_po_chk='';$project_trim_po_chk='';
	    $project_fab_po_chk=return_field_value("po_break_down_id", "wo_pre_cos_fab_co_avg_con_dtls", "po_break_down_id=$update_id_details and status_active=1");
		$project_trim_po_chk=return_field_value("po_break_down_id", "wo_pre_cost_trim_co_cons_dtls", "po_break_down_id=$update_id_details and status_active=1");
		
		$txt_po_no=str_replace("'",'',$txt_po_no);
		$pono='';
		$pono=trim(str_replace($str_replace_check,'',$txt_po_no));
		
		$txt_details_remark=str_replace("'",'',$txt_details_remark);
		$dtls_remark='';
		$dtls_remark=trim(str_replace($str_replace_check,'',$txt_details_remark));
		
		if($poEntryControlWithBomApproval==3 && $bomApproved==1)//issue id ISD-21-19965
		{
			if (str_replace("'","",$cbo_order_status)==2 &&  ($project_fab_po_chk=='' || $project_trim_po_chk=='')) //Projected Po
			{
				$field_array="po_quantity*po_total_price*plan_cut*original_po_qty*updated_by*update_date";
				$data_array="".$txt_po_quantity."*".$txt_amount."*".$txt_plan_cut."*".$txt_po_quantity."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				$field_array="po_quantity*po_total_price*plan_cut*updated_by*update_date";
				$data_array="".$txt_po_quantity."*".$txt_amount."*".$txt_plan_cut."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
		}
		else
		{
			if (str_replace("'","",$cbo_order_status)==2 &&  ($project_fab_po_chk=='' || $project_trim_po_chk=='')) //Projected Po
			{
				$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*txt_etd_ldd*po_quantity*unit_price*po_total_price*excess_cut*plan_cut*details_remarks*delay_for*packing*grouping*original_po_qty*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*sc_lc*po_number_prev*pub_shipment_date_prev*with_qty*is_deleted*status_active*updated_by*update_date";
				$data_array="".$cbo_order_status."*'".$pono."'*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$org_shipment_date."*".$txt_factory_rec_date."*".$txt_etd_ldd."*".$txt_po_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_excess_cut."*".$txt_plan_cut."*'".$dtls_remark."'*".$cbo_delay_for."*".$packing."*".$txt_grouping."*".$txt_po_quantity."*".$cbo_projected_po."*".$cbo_tna_task."*".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".$txt_file_no."*".$txt_sc_lc."*'".$pre_po_no."'*'".$pre_pubship_date."'*".$with_qty_pop."*0*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*txt_etd_ldd*po_quantity*unit_price*po_total_price*excess_cut*plan_cut*details_remarks*delay_for*packing*grouping*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*sc_lc*po_number_prev*pub_shipment_date_prev*with_qty*is_deleted*status_active*updated_by*update_date";
				$data_array="".$cbo_order_status."*'".$pono."'*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$org_shipment_date."*".$txt_factory_rec_date."*".$txt_etd_ldd."*".$txt_po_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_excess_cut."*".$txt_plan_cut."*'".$dtls_remark."'*".$cbo_delay_for."*".$packing."*".$txt_grouping."*".$cbo_projected_po."*".$cbo_tna_task."*".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".$txt_file_no."*".$txt_sc_lc."*'".$pre_po_no."'*'".$pre_pubship_date."'*".$with_qty_pop."*0*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
		}
				
		//Log History Start.------------------------...REZA
		$sql_con="is_confirmed=$cbo_order_status and po_number =$txt_po_no and job_no_mst=$update_id and po_received_date=$txt_po_received_date and pub_shipment_date=$txt_pub_shipment_date and shipment_date=$org_shipment_date and factory_received_date=$txt_factory_rec_date and po_quantity=$txt_po_quantity and unit_price=$txt_avg_price and po_total_price=$txt_amount and excess_cut=$txt_excess_cut and plan_cut=$txt_plan_cut and details_remarks=$txt_details_remark and delay_for=$cbo_delay_for and packing=$packing and grouping=$txt_grouping and projected_po_id=$cbo_projected_po and tna_task_from_upto=$cbo_tna_task and t_year=".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))." and t_month=".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))." and file_no=$txt_file_no and id=$update_id_details and is_deleted=0";
		$sql_con=str_replace("=''"," IS NULL ",$sql_con);
		
		$is_duplicate=is_duplicate_field( "po_number", "wo_po_break_down", $sql_con );
		//echo"10**=". $is_duplicate;disconnect($con);die;
		$log_id_mst = return_next_id( "id", "wo_po_update_log", 1 ) ;
		
		if($db_type==0)
		{
			$current_date = $pc_date_time;
		}
		else
		{
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		}
		
		$previous_po_qty=return_field_value("po_quantity","wo_po_break_down","job_no_mst=".$update_id." and id=".$update_id_details."");
		
		$log_update_date=return_field_value("update_date","wo_po_update_log","job_no=".$update_id." and po_id=".$update_id_details." order by id DESC");
		
		$log_update=date("Y-m-d", strtotime($log_update_date));
		$curr_date=date("Y-m-d", strtotime($current_date));
		
		if(($log_update=="" && $is_duplicate!=1) || ($log_update!=$curr_date && $is_duplicate!=1))
		{
			$field_array_history="id,entry_form,job_no,po_no,po_id,order_status,po_received_date,previous_po_qty,shipment_date,org_ship_date,po_status,t_year,t_month,fac_receive_date, projected_po, packing, remarks, file_no, avg_price, excess_cut_parcent, plan_cut,status,prev_update_date,prev_update_by,update_date,update_by";
			
			$data_array_history="(".$log_id_mst.",1,".$update_id.",'".$prev_po_no."',".$update_id_details.",'".$prev_order_status."','".$prev_po_received_date."','".$prev_po_qty."','".$prev_pub_shipment_date."','".$prev_org_shipment_date."','".$prev_status."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".$prev_factory_rec_date."','".$prev_projected_po."','".$prev_packing."','".$prev_details_remark."','".$prev_file_no."','".$prev_avg_price."','".$prev_excess_cut."','".$prev_plan_cut."','".$prev_status."','".$prev_update_date."',".$prev_updated_by.",'".$current_date."',".$_SESSION['logic_erp']['user_id'].")";
			
			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);	
		}
		else if( $log_update==$curr_date)
		{
			$field_array_history="po_no*po_id*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*avg_price*excess_cut_parcent*plan_cut*status*prev_update_date*prev_update_by*update_date*update_by";
			
			$data_array_history="'".$prev_po_no."'*".$update_id_details."*'".$prev_order_status."'*'".$prev_po_received_date."'*'".$prev_po_qty."'*'".$prev_pub_shipment_date."'*'".$prev_org_shipment_date."'*'".$prev_status."'*'".$prev_factory_rec_date."'*'".$prev_projected_po."'*'".$prev_packing."'*'".$prev_details_remark."'*'".$prev_file_no."'*'".$prev_avg_price."'*'".$prev_excess_cut."'*'".$prev_plan_cut."'*'".$prev_order_status."'*'".$prev_update_date."'*"."'*'".$prev_updated_by."'*"."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1); 
		}
		
		//Log History end.-------------------------...REZA
		$status_id=str_replace("'","",$cbo_status);//Inactive/Cancel
		if($status_id>0)
		{
			//echo "10**update  wo_po_color_size_breakdown set status_active=".$status_id.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details and is_deleted=0 and status_active=1";die;
		$rID=execute_query( "update wo_po_color_size_breakdown set status_active=".$status_id.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details and is_deleted=0",1);
		}
		//echo "10**".$status_id.'--'.$rID; die;
		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$rID2=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$update_id." and booking_type=1 and is_short=2 ",1);
		if($rID) 
		{
			//$tot_sc_attached_qnty_arr[$row[csf("lc_sc_id")]]['qty']
			//echo "10**".count($tot_sc_attached_qnty_arr); die;
			$update_tot_attached_amount=0;
			if(count($tot_sc_attached_qnty_arr)>0){
				foreach ($tot_sc_attached_qnty_arr as $lc_sc_id => $value) {
					$update_tot_attached_amount=$value['qty']*str_replace("'","",$txt_avg_price);
					if($update_tot_attached_amount>$value['val']){
						echo "16**Attach Value Not Allowed Over Then SC Value.";disconnect($con);die;
					} 
					//echo "10**".$update_tot_attached_amount.'**'.$value['qty'].'**'.str_replace("'","",$txt_avg_price).'**'.$lc_sc_id; die;
					$rID3=execute_query( "update  com_sales_contract_order_info set attached_rate=".$txt_avg_price.",attached_value=".$update_tot_attached_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details and com_sales_contract_id=$lc_sc_id",1);
				}
			}
			if(count($tot_lc_attached_qnty_arr)>0){
				foreach ($tot_lc_attached_qnty_arr as $lc_sc_id => $value) {
					$update_tot_attached_amount=$value['qty']*str_replace("'","",$txt_avg_price); 
					if($update_tot_attached_amount>$value['val']){
						echo "16**Attach Value Not Allowed Over Then LC Value.";disconnect($con);die;
					} 
					//echo "10**".$update_tot_attached_amount.'**'.$value['qty'].'**'.str_replace("'","",$txt_avg_price).'**'.$lc_sc_id; die;
					$rID3=execute_query( "update  com_export_lc_order_info set attached_rate=".$txt_avg_price.",attached_value=".$update_tot_attached_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details and com_export_lc_id=$lc_sc_id",1);
				}
			}
			/*$update_tot_attached_amount=$tot_attached_qnty*str_replace("'","",$txt_avg_price); // issue id :25912
			$rID3=execute_query( "update  com_export_lc_order_info set attached_rate=".$txt_avg_price.",attached_value=".$update_tot_attached_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details",1);*/
			//$rID4=execute_query( "update  com_sales_contract_order_info set attached_rate=".$txt_avg_price.",attached_value=".$update_tot_attached_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details",1);
		}
		//attached_rate,attached_value
		//txt_avg_price."*".$txt_amount
		//=================================================
		$return_data= update_job_mast($update_id);//define in common_functions.php
		if($bomApproved!=1) update_cost_sheet($update_id);
		
		$jobidArr[str_replace("'","",$hidd_job_id)]=str_replace("'","",$hidd_job_id);
		
		if(trim($prev_order_status)!=trim($cbo_order_status))
		{
			fnc_isdyeingplan("WO_PO_DETAILS_MASTER", $jobidArr);
		}
		//==================================================
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "1**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		if (is_duplicate_field( "po_break_down_id", "pro_garments_production_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			 disconnect($con);die;
		}
		else if (is_duplicate_field( "order_id", "ppl_cut_lay_size", "order_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			 disconnect($con);die;
		}
		else if (is_duplicate_field( "po_breakdown_id", "pro_roll_details", "po_breakdown_id=$update_id_details and is_deleted=0 and status_active=1 and is_sales<>1 and booking_without_order=0" ) == 1)
		{
			echo "14**0"; 
			 disconnect($con);die;
		}
		else if (is_duplicate_field( "po_break_down_id", "pro_bundle_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			 disconnect($con);die;
		}
		
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
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
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$rID=execute_query( "update  wo_po_color_size_breakdown set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details  ",1);
		
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		//echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3];
		disconnect($con);
		die;
	}
}

function get_tna_template( $remain_days, $tna_template, $buyer ) 
{
	global $tna_template_buyer;
	if(count($tna_template_buyer[$buyer])>0)
	{ 
		$n=count($tna_template_buyer[$buyer]); 
		for($i=0;$i<$n; $i++)
		{ 
			if($remain_days<$tna_template_buyer[$buyer][$i]['lead']) 
			{
				if( $i!=0 )
					return $tna_template_buyer[$buyer][$i-1]['id'];
				else
					return "0";
				 
			}
			else if( $remain_days==$tna_template_buyer[$buyer][$i]['lead'] ) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
			else if($remain_days>$tna_template_buyer[$buyer][$i]['lead'] &&  $i==$n-1) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
		}
	}
	else
	{
		 
		$n=count($tna_template); 
		for($i=0;$i<$n;$i++)
		{
			if( $remain_days<$tna_template[$i]['lead']) 
			{
				if( $i!=0 )
					return $tna_template[$i-1]['id'];
				else
					return "0";
				
			}
			else if($remain_days==$tna_template[$i]['lead']) 
			{
				return $tna_template[$i]['id'];
			}
			else if($remain_days>$tna_template[$i]['lead'] &&  $i==$n-1) 
			{
				return $tna_template[$i]['id'];
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

	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][163] );
	$po_quantity=0;
	

	$act_po_variable_sql=sql_select("select id, exeed_budge_qty from variable_order_tracking where company_name='$company_id' and variable_list=52 order by id");
	foreach($act_po_variable_sql as $row){
		$act_po_variable=$row[csf('exeed_budge_qty')];
	}
	$gmts_item_arr=sql_select("SELECT item_number_id, sum(order_quantity) as order_total from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id='$po_id' group by item_number_id");
	foreach($gmts_item_arr as $row){
		$gmts_item_data[$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_quantity+=$row[csf('order_total')];
	}
	$current_acc_po_dtls=sql_select("SELECT sum(b.po_qty) as po_qty from wo_po_acc_po_info a join wo_po_acc_po_info_dtls b on a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.acc_po_status=1 and a.po_break_down_id=$po_id and a.job_id=$job_id");
	foreach($current_acc_po_dtls as $row){
		$currrent_po_qty=$row[csf('po_qty')];
	}

	$balance_po_qty=$po_quantity-$currrent_po_qty;

	
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
					var dtls_data=return_global_ajax_value(rowid+'_'+updateid, 'delete_row', '', 'woven_order_entry_controller');
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
					var dtls_data=return_global_ajax_value(rowid+'_'+updateid, 'delete_row', '', 'woven_order_entry_controller');
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
					var dtls_data=return_global_ajax_value(rowid, 'pack_delete_row', '', 'woven_order_entry_controller');
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
			
			http.open("POST","woven_order_entry_controller.php",true);
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
				if(reponse[0]=='pack_finish')
				{
					alert("Packing And Finishing Data Found Update/Delete Not Possible");
					release_freezing();
					return;
				}
				if(reponse[0]=='pack_finish_qty')
				{
					alert("PO Qty Can Not less then Packing And Finishing Qty");
					release_freezing();
					return;
				}
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					var poqty=$("#txt_po_qty").val()*1;
					var poid=$("#hid_po_id").val()*1;
					get_php_form_data(poid+'_'+poqty, 'acc_po_balance_qty', 'woven_order_entry_controller');
					var datalist=document.getElementById('hid_po_id').value+'__'+document.getElementById('txt_job_no').value;
					show_list_view( datalist,'accpo_list_view','save_up_list_view','woven_order_entry_controller','');//setFilterGrid(\'tbl_upListView\',-1)
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
				if (form_validation('cartonQnty_'+i+'*cbm_'+i,'No. Of Carton*CBM')==false)
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
			
			http.open("POST","woven_order_entry_controller.php",true);
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
					show_list_view(act_po_id+'_'+po_id,'populate_act_pack_details','packing_finishing','woven_order_entry_controller','');
					
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
					var pack_qty = $("#pakqty_"+i).val()*1;
					var acc_qty=$("#poQnty_"+i).val()*1;
					if(acc_qty<pack_qty){
						alert("Actual PO Qty Can Not Less then Packing And Finishing Quantity: "+pack_qty);
						$("#poQnty_"+i).val(pack_qty);
						povalue=pack_qty*$("#pounitprice_"+i).val()*1;
						$("#povalue_"+i).val(povalue);
						return;
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
			get_php_form_data(rowid+'_'+poqty, 'populate_acc_details_data', 'woven_order_entry_controller');
			show_list_view(rowid+'_'+document.getElementById('hid_po_id').value+'_'+document.getElementById('hid_job_id').value,'show_acc_po_dtls','actual_po_details','woven_order_entry_controller','');
			show_list_view(rowid+'_'+document.getElementById('hid_po_id').value+'_'+document.getElementById('hid_job_id').value,'show_act_pack_dtls','packing_finishing','woven_order_entry_controller','');
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
			var unit_price = return_ajax_request_value(po_id+'*'+country+'*'+item+'*'+color+'*'+size, 'get_unit_price', 'woven_order_entry_controller');
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
			load_drop_down( 'woven_order_entry_controller', item+'_'+row+'_'+color+'_'+po_id, 'load_drop_down_gmtssize', 'gmtssize_'+row );
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
						<input type="hidden" id="pakqty_1" name="pakqty_1" class="text_boxes" value="0" />
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
						<?=create_drop_down( "cbopackGmtsItemId_1", 150, $garments_item, 0, 1, "All Gmts Items", $selected,"",0,$gmts_item); ?>
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
	show_list_view( '<?=$po_id.'__'.$txt_job_no; ?>','accpo_list_view','save_up_list_view','woven_order_entry_controller','');
	
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

if($action=="populate_act_pack_details"){
	$data=explode("_",$data);
	$i=1;
	$pack_finish_data=sql_select("SELECT id, po_break_down_id, gmts_item, gmts_color_id, gmts_size_id, carton_qty, cbm from wo_po_act_pack_finish_info where status_active=1 and is_deleted=0 and act_po_id=$data[0]");
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
			<?=create_drop_down( "cbopackGmtsItemId_".$i, 90, $garments_item, 0, 1, "All Gmts Items", $row[csf('gmts_item')],"",0,$gmts_item); ?>
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
			<?=create_drop_down( "cbopackGmtsItemId_1", 90, $garments_item, 0, 1, "All Gmts Items", $selected,"",0,$gmts_item); ?>
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
}

if($action=="show_acc_po_dtls"){
	$data=explode("_",$data);
	$prev_fin_qty = return_library_array("SELECT a.actual_po_dtls_id as id, sum(a.prod_qty) as qty from pro_garments_prod_actual_po_details a, pro_garments_production_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.production_type=8 and b.po_break_down_id=$data[1] and a.actual_po_id=$data[0] and a.prod_qty>0 group by a.actual_po_dtls_id", "id", "qty");

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
		$bgcolor='';
		$disabled='';
		if($prev_fin_qty[$row[csf('dtls_id')]]>0){
			$disabled="disabled";
			$bgcolor="yellow";
		}
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
				<input type="text" id="poQnty_<?= $i ?>" name="poQnty_<?= $i ?>" class="text_boxes_numeric" style="width:80px; background-color:<?= $bgcolor ?>" value="<?= $row[csf('po_qty')] ?>" onBlur="fnc_poqty_cal();" />
				<input type="hidden" id="rowid_<?= $i ?>" name="rowid_<?= $i ?>" class="text_boxes" value="<?= $row[csf('dtls_id')] ?>" />
				<input type="hidden" id="pakqty_<?= $i ?>" name="pakqty_<?= $i ?>" class="text_boxes" value="<?= $prev_fin_qty[$row[csf('dtls_id')]] ?>" />
			</td>
			<td align="center">
				<input type="text" id="pounitprice_<?= $i ?>" name="pounitprice_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('unit_price')] ?>" onBlur="fnc_poqty_cal();"/>
			</td>
			<td align="center">
				<input type="text" id="povalue_<?= $i ?>" name="povalue_<?= $i ?>" class="text_boxes_numeric" style="width:80px" value="<?= $row[csf('unit_value')] ?>" readonly/>
			</td>
			<td>
				<input type="button" id="increase_<?= $i ?>" name="increase_<?= $i ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>);" />				
				<input type="button" id="decrease_<?= $i ?>" name="decrease_<?= $i ?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<?= $i ?>);" <?= $disabled  ?>/>
			</td>
		</tr>
	<?
	$i++;
	}
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
			<?=create_drop_down( "cbopackGmtsItemId_".$i, 150, $garments_item, 0, 1, "All Gmts Items", $row[csf('gmts_item')],"",0,$gmts_item); ?>
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
			<?=create_drop_down( "cbopackGmtsItemId_1", 150, $garments_item, 0, 1, "All Gmts Items", $selected,"",0,$gmts_item); ?>
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
		$prev_fin_qty = sql_select("SELECT a.actual_po_dtls_id as dtls_id, sum(a.prod_qty) as qty from pro_garments_prod_actual_po_details a, pro_garments_production_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.production_type=8 and b.po_break_down_id=$hid_po_id and a.actual_po_id=$update_id and a.prod_qty>0 group by a.actual_po_dtls_id");

		$prev_fin_qty_arr=array();
		foreach($prev_fin_qty as $row){
			$prev_fin_qty_arr[$row[csf('dtls_id')]]=$row[csf('qty')];
		}
		/* if(count($prev_fin_qty)>0 && $_SESSION['logic_erp']['user_level']!=2){
			echo "pack_finish";
			disconnect($con);
			die;
		}
		if(count($prev_fin_qty)>0 && $_SESSION['logic_erp']['user_level']==2 && str_replace("'",'',$actpostatus)==2){
			echo "pack_finish";
			disconnect($con);
			die;
		} */
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
			$dtls_id=str_replace("'",'',$$rowid);
			$poqty=str_replace("'",'',$$poQnty);
			if(str_replace("'",'',$$rowid)!="")
			{

				if($prev_fin_qty_arr[$dtls_id]>$poqty){
					echo "pack_finish_qty";
					disconnect($con);
					die;
				}
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
		$prev_fin_qty = return_library_array("SELECT a.actual_po_dtls_id as id, sum(a.prod_qty) as qty from pro_garments_prod_actual_po_details a, pro_garments_production_mst b where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.production_type=8 and b.po_break_down_id=$hid_po_id and a.actual_po_id=$update_id and a.prod_qty>0 group by a.actual_po_dtls_id", "id", "qty");

		if(count($prev_fin_qty)>0){
			echo "pack_finish";
			disconnect($con);
			die;
		}
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
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_upListView" align="left">
            
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
                        <td align="center"><?= $row_status[$row[csf('acc_po_status')]]; ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qty+=$row[csf('acc_po_qty')];
					$tot_value+=$row[csf('acc_po_value')];
				}
			?>
           
            </table>
        </div>
        <table width="700" class="tbl_bottom"  border="1" class="rpt_table" rules="all">
			 
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
	$dateto=$pc_date_time;
	$data_array=sql_select("select id,job_id, po_break_down_id, acc_po_no, acc_rcv_date, acc_ship_date, acc_revise_ship_date, acc_ship_mode, acc_po_status, acc_po_qty, acc_po_value,remarks, insert_date from wo_po_acc_po_info where id='$data[0]' and is_deleted=0");
	foreach($data_array as $row)
	{
		$datefrom=$row[csf('insert_date')];
		$diff_hour=datediff('h', $datefrom, $dateto, false);
		if($_SESSION['logic_erp']['user_level']!=2 && $diff_hour>48){
			echo "$('#txt_po_rcv_date').attr('disabled',true);\n";
			echo "$('#txt_po_shipment_date').attr('disabled',true);\n";
		}
		else{
			echo "$('#txt_po_rcv_date').attr('disabled',false);\n";
			echo "$('#txt_po_shipment_date').attr('disabled',false);\n";
		}
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
				//data_all+=get_submitted_data_string('termscondition_'+i+'*termsconditionID_'+i,"../../../",i);
			// data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidthtype_'+i+'*txtdiawidth_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthtypeid_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*rollid_'+i,"../../",i);
			
		}  //alert(data_all);return;
		var data="action=save_update_delete_wo_order_entry_ref&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+data_all;
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller.php",true);
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
				document.getElementById('iref').value=reponse[2];
				parent.emailwindow.hide();
			}
			
			
			////if(reponse[0]==0)
			//{
				 set_button_status(1, permission, 'fnc_order_entry_terms_condition',1);
			//}
			
			
	}
}
//Row Sequence

function row_sequence(row_id)
	{
		var row_num=$('#tbl_termcondi_details tbody tr').length-1;
		
		var txt_seq=$('#termscondition_'+row_id).val();
		//alert(row_id);
		//var seq_no=1;
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
				
				if($db_type==0)
				{
					$insert_year_cond="  YEAR(job_insert_date)=$insert_date";
				}
				else if($db_type==2)
				{
					 $insert_year_cond=" to_char(job_insert_date,'YYYY')=$insert_date";
				}
			?>
           <input type="text" id="txt_job_no" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$txt_job_no) ?>"/>
            <input type="hidden" id="job_insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$job_insert_date) ?>"/>
             <input type="hidden" id="insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$insert_date) ?>"/>
             <input type="hidden" id="iref" name="iref"/>
            
            <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="30">Sl</th><th width="150">Internal Ref</th><th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					//echo $year_cond;
						// "select $year_cond from wo_po_details_master where job_no=$txt_job_no ";
				
					$current_year=date("Y",time());
					$data_array=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref where  $insert_year_cond");// quotation_id='$data'
					$max_ref=$data_array[0][csf('internal_ref')]+1;
					$data_array=sql_select("select id as update_id, internal_ref from   wo_order_entry_internal_ref where job_no=$txt_job_no order by id asc");// quotation_id='$data'
					
					if ( count($data_array)>0)
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
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:140px"   class="text_boxes" value="<? echo $row[csf('internal_ref')]; ?>" onBlur="row_sequence(<? echo $i; ?>); "   /> 
                                    <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('update_id')]?>"  />
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />                                   <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
								$k=1;		?>
                   		 		<tr id="settr_1" align="center">
                                    <td>
                                    <input type="text" id="sltd_<? echo $k;?>"   name="sltd_<? echo $k;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $k;?>"    /> 
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $k;?>"  onBlur="row_sequence(<? echo $k; ?>); "   name="termscondition_<? echo $k;?>" style="width:140px"   class="text_boxes" value="<? echo $max_ref;?>"    /> 
                                    <input type="hidden" id="termsconditionID_<? echo $k;?>"   name="termsconditionID_<? echo $k;?>" style="width:50px" value=""  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?> )" />                                    <input type="button" id="decrease_<? echo $k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> );" />                  </td>
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
		if($db_type==0)
			{
				$insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
			}
			else if($db_type==2)
			{
				 $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
			}
			//echo $insert_year_cond;die;
	//	if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}'".$pc_date_time."'		
		 $id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		 $field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			
			 
			 $internal_ref="termscondition_".$i;
			 $internal_cond="termscondition_".$i;
			// echo "11**0"; 	
			// echo "select  internal_ref from wo_order_entry_internal_ref where internal_ref=".$$internal_cond."  $insert_year_cond ";
			// disconnect($con);die;
			  if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$$internal_cond."  $insert_year_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					 disconnect($con);die;			
				}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
			$id=$id+1;
			 //echo $$internal_cond;
			//$sql="select internal_ref from wo_order_entry_internal_ref where internal_ref=".$$internal_cond." ";
			
			
		 }//echo  $sql;
		
		//$rID_de3=execute_query( "delete from wo_order_entry_internal_ref where  job_no =".$txt_job_no."",0);

		 $rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		
		$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no=$txt_job_no ", "internal_ref"  ), 0, -1);
	
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$job."**".$internal;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job."**".$internal;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job."**".$internal;
			}
			else{
				oci_rollback($con);
				echo "10**".$job."**".$internal;
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
		if($db_type==0)
			{
				$insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
			}
			else if($db_type==2)
			{
				 $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
			}
		$data_array2=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref");// quotation_id='$data'
		 $max_ref=$data_array2[0][csf('internal_ref')];
		 $id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		 $field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		 $field_array_up="job_no*internal_ref*job_insert_date*insert_date";
		 $add_comma=1;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $internal_ref="termscondition_".$i;
			 //$internal_cond="termscondition_".$i;
			 $internal_cond=str_replace("'","",$$internal_ref);
			 //echo $internal_cond.'<hr>';
			 $update_id="termsconditionID_".$i;
			 $mst_update_id=str_replace("'","",$$update_id);
			// echo $mst_update_id;die;
			if($mst_update_id!="") //and id!=$mst_update_id
			{ 
				//echo "10**select internal_ref from wo_order_entry_internal_ref where internal_ref=".$internal_cond."  $insert_year_cond ";
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref='".$internal_cond."' and id!=$mst_update_id  $insert_year_cond " )==1)
					{
						//check_table_status( $_SESSION['menu_id'],0);and $insert_year_cond
						echo "11**0"; 
						 disconnect($con);die;			
					}
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_array_up[str_replace("'",'',$$update_id)] =explode("*",("".$txt_job_no."*'".$internal_cond."'*".$job_insert_date."*'".$pc_date_time."' "));
			}
			if($mst_update_id=="")
			{ 
			 if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref='".$internal_cond."'  $insert_year_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					 disconnect($con);die;			
				}
			
			if ($add_comma!=1) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
				$id=$id+1;
				$add_comma++;
			}
			//echo "select id from wo_order_entry_internal_ref  where internal_ref=".$internal_cond." and job_no=$txt_job_no and id!=$mst_update_id ";die;
				//$rID_de3=execute_query( "delete from wo_order_entry_internal_ref where  job_no=".$txt_job_no." ",0);
		 }
		 //echo "10**".bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr );
		 $rID=execute_query(bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr ));
			//print_r($data_array);
		 if($data_array!="")
		 	{
		 	 $rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
			}
		// print_r($data_array);
		// check_table_status( $_SESSION['menu_id'],0);
				$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no=$txt_job_no", "internal_ref"  ), 0, -1);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$job."**".$internal;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job."**".$internal;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job."**".$internal;
			}
			else{
				oci_rollback($con);
				echo "10**".$job."**".$internal;
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

/*if($action=="check_ref_no")
{
	$data=explode("**",$data);
	$sql="select id, job_no_mst,grouping from wo_po_break_down where job_no_mst='".trim($data[0])."' and grouping='$data[2]' and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('grouping')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}*/

if($action=="check_internal_ref")
{
	$data=explode("**",$data);
	$sql="select id, internal_ref from wo_order_entry_internal_ref where internal_ref='".trim($data[0])."' and job_no='".trim($data[1])."'  order by id desc";
	//echo "10**".$sql; die;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('internal_ref')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="set_smv_work_study")
{
	$data=explode("**",$data);
	$item_id=$data[1];
	$style_id=$data[0];
	//print_r($data);
	//and style_ref='$style_id'
	  $sql_smv="select  upper(style_ref) as style_ref,gmts_item_id,total_smv from ppl_gsd_entry_mst where gmts_item_id=$item_id  and status_active=1 and is_deleted=0";
		  $sql_result=sql_select($sql_smv);$set_smv_arr=array();
		 foreach($sql_result as $row)
		 {
			$set_smv_arr[$row[csf('style_ref')]][$row[csf('gmts_item_id')]]['smv']+=$row[csf('total_smv')];
		 }
		// print_r($set_smv_arr);
	if(count($sql_result)>0)
	{
		echo "1_".$set_smv_arr[$style_id][$item_id]['smv'];
		//echo $set_smv_arr[$style_id][$item_id]['smv'];
	}
	else
	{
	 echo "0_";	
	}
	
	exit();	
}

if($action=="open_smv_list")
{
	 echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
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
                        <input type="hidden" id="item_id" value="<?=$item_id;?>">
                        <input type="hidden" id="row_id" value="<?=$row_id;?>">
                        <input type="hidden" id="company_id" value="<?=$cbo_company_name;?>">
						<input type="hidden" id="bulletin_type" value="<?=$bulletin_type;?>">
                        <input type="hidden" id="hiddprocess_type" value="<?=$processtype;?>">
                    &nbsp;</th>
                </thead>
                <tr>
                    <td><?=create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value+'_'+document.getElementById('bulletin_type').value+'_'+document.getElementById('hiddprocess_type').value, 'create_item_smv_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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

if($action=="create_item_smv_search_list_view") //IF any chnge need pls ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];
	$bulletin_type=$data[5];
	$process_type=$data[6];//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?
	/*$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where 0=0 $gmts_item_con2  order by a.id Desc";
	$result = sql_select($sewing_sql);
	foreach($result as $row)
	{
		$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
		$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
	}*/
	// print_r($code_smv_arr);b.lib_sewing_id
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

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and bulletin_type=$bulletin_type and c.department_code='$process_type' $gmts_item_con $style_con $buyer_id_con $appCond order by id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]]['system_no'].=$row[csf('system_no')].',';
		//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$smv_dtls_arr[$row[csf('id')]][$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
		$code_id=$code_smv_arr[$row[csf('id')]][$row[csf('lib_sewing_id')]]['dcode'];
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
		foreach($smv_dtls_arr as $bullitineid=>$bulldata)
		{
			foreach($bulldata as $ext_no=>$arrdata)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
				$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
	
				$finish_smv=$cut_smv=$sewing_smv=0;
	
				$sys_id=$bullitineid;//rtrim($arrdata['id'],',');
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
				$sewing_smv=$sewing_smv/$k;
				
				if($process_type==4) $data=$finish_smv."_".$row_id."_".$id_str; //ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
				else if($process_type==7) $data=$cut_smv."_".$row_id."_".$id_str; //ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
				else $data=$sewing_smv."_".$row_id."_".$id_str;
	
				//$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
				?>
				<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="cursor:pointer" onClick="js_set_value('<?=$data; ?>')">
					<td width="30"><?=$i;//.'='.$k ?></td>
					<td width="120" style="word-break:break-all"><?=$system_no; ?></td>
					<td width="50" style="word-break:break-all"><?=$ext_no; ?></td>
					<td width="160" style="word-break:break-all"><?=$arrdata['style_ref']; ?></td>
					<td width="60" align="right"><p><?=number_format($sewing_smv,2); ?></p></td>
					<td width="60" align="right"><p><?=number_format($cut_smv,2); ?></p></td>
					<td width="60" align="right"><p><?=number_format($finish_smv,2); ?></p></td>
					<td><p><?=$arrdata['operation_count']; ?></p></td>
				</tr>
				<?
				$i++;
			}
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
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="create_item_smv_search_list_view_bk")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];
	$bulletin_type=$data[5];
	$process_type=$data[6];//ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada

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
			
			if($process_type==4) $data=$finish_smv."_".$row_id."_".$id_str; //ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			else if($process_type==7) $data=$cut_smv."_".$row_id."_".$id_str; //ISD-22-25886 process type add if need db ref. its not possible ---issue discuss by beeresh dada
			else $data=$sewing_smv."_".$row_id."_".$id_str;

			//$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="cursor:pointer" onClick="js_set_value('<?=$data; ?>');">
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




function fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$data_int,$sewSmv,$cutSmv,$page)
{
	if($page==1)
	{
		$ex_data=explode("****",$data_int);
		$currercy=str_replace("'","",$ex_data[0]);
		$set_breck_down_arr=explode('__',str_replace("'",'',$ex_data[1]));
		$item_wise_arr=array(); $itm_arr=array();
		for($c=0; $c<count($set_breck_down_arr); $c++)
		{
			$set_breck_downdata_arr=explode('_',$set_breck_down_arr[$c]);
			$itm_arr[]=$set_breck_downdata_arr[0];
			$item_wise_arr[$set_breck_downdata_arr[0]]['ratio']=$set_breck_downdata_arr[1];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_pcs']=$set_breck_downdata_arr[2];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_set']=$set_breck_downdata_arr[3];
			$sewSmvn+=$set_breck_downdata_arr[3];
			$cutSmvn+=$set_breck_downdata_arr[7];
		}
		$is_pre_cost="";
		//return $db_type.'_##_'.$cbo_company_name.'_##_'.$txt_job_no.'_##_'.$data_int.'_##_'.$sewSmv.'_##_'.$cutSmv.'_##_'.$page; die;
		$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$cm_cost=0;
		//echo "select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1"; die;
		$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
		$txt_sew_smv=$sewSmvn*1;//$pre_cost_data[0][csf("sew_smv")];
		$txt_cut_smv=$cutSmvn*1;//$pre_cost_data[0][csf("cut_smv")];
		$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
		$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
		
		$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
		$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
		$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
		$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
		$costing_date= $pre_cost_data[0][csf("costing_date")];
		
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
		
		$sql_pre_cost_dtls="select sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(cm_cost) as cm_cost, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
		$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
		$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0;
		
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
			//return $data_arr_pre_cost; die;
			//'7.8279'*'18.12'*'21.1777'*'49.02'*'22.0223'*'50.98'*'1.7648'*'49.02'*'1.8352'*'50.98'
			$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
		}
		else
		{
			return;
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost; 
	}
}


if($action=="check_po_shipping_status_po_id")
{
  $data=explode("***", $data);
  $sql="SELECT shiping_status,status_active from wo_po_break_down where status_active in (1,2,3) and is_deleted=0 and job_no_mst='$data[0]' and id='$data[1]'";
  $res=sql_select($sql);
  if(count($res))
  {
    echo $res[0][csf('shiping_status')]."***".$res[0][csf('status_active')];
    exit();
  }
  else{
    echo "not";
    exit();
  }
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
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'woven_order_entry_controller');
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
	
		http.open("POST","woven_order_entry_controller.php",true);
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
				show_list_view( datalist,'accpo_list_view_v1','save_up_list_view','woven_order_entry_controller','');//setFilterGrid(\'tbl_upListView\',-1)
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
		get_php_form_data(rowid, 'populate_acc_details_data_v1', 'woven_order_entry_controller');
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
                    <td align="center">
						<input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:70px" value="" onBlur="fnc_poqty_cal();" />
						<input type="hidden" id="pakqty_1" name="pakqty_1" class="text_boxes" value="0" />
					</td>
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
	show_list_view( '<?=$po_id.'__'.$txt_job_no; ?>','accpo_list_view_v1','save_up_list_view','woven_order_entry_controller','');
	
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
        <table width="300" class="tbl_bottom"  border="1" class="rpt_table" rules="all">
			 
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

?>
