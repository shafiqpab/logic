<?
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.commisions.php');


$date=date('Y-m-d');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data']; 
$action=$_REQUEST['action'];

$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];

$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
}

if($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	exit();
}

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	//$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=203 and is_deleted=0 and status_active=1");
	$print_report_format=fnc_report_button($data,11,203,0);//Company,Menu Id,page Id,First Button

	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 70, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}

if($action=="load_drop_down_team_leader")
{
	echo create_drop_down( "cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team  where id='$data' and status_active=1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Team Leader-", $selected, "" );
	exit();
}

if($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 100, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Team Member-", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$team_name=str_replace("'","",$cbo_team_name);
	$team_member=str_replace("'","",$cbo_team_member);
	$search_by=str_replace("'","",$cbo_search_by);
	$search_string=str_replace("'","",$txt_search_string);
	$txt_file=str_replace("'","",$txt_file);
	$txt_ref=str_replace("'","",$txt_ref);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$category_by=str_replace("'","",$cbo_category_by);
	$year_id=str_replace("'","",$cbo_year);
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_season=str_replace("'","",$cbo_season);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_sustainability_standard=str_replace("'","",$cbo_sustainability_standard);
	$cbo_fab_material=str_replace("'","",$cbo_fab_material);

	
	//
	//if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	if($company_name==0 && $buyer_name==0)
    {
        echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
        die;
    }
	if($cbo_season!=0) $season_cond="and a.season_buyer_wise=$cbo_season "; else $season_cond="";
	//if($cbo_brand_name!=0) $brand_cond="and a.brand_id=$cbo_brand_name "; else $brand_cond="";
	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else { $buyer_id_cond=""; $buyer_id_cond2=""; }
		}
		else { $buyer_id_cond="";$buyer_id_cond2=""; }
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$buyer_name";
	}
	$brand_cond="";
	if($cbo_brand_name==0)
	{
		if($filterBrandId!="") $brand_cond="and a.brand_id in ($filterBrandId)"; else $brand_cond="";
	}
	else $brand_cond="and a.brand_id=$cbo_brand_name ";

	if(trim($date_from)!="") $start_date=$date_from;
	if(trim($date_to)!="") $end_date=$date_to;

	// if($$cbo_order_status2==2) $cbo_order_status="%%"; else $cbo_order_status= "$cbo_order_status2";
	
	if(trim($cbo_order_status)!=0){$order_confirm_status_con=" and b.is_confirmed = $cbo_order_status";}

	if(trim($team_name)=="0") $team_leader="%%"; else $team_leader="$team_name";
	if(trim($team_member)=="0") $dealing_marchant="%%"; else $dealing_marchant="$team_member";
	if(trim($company_name)=="0") $company_name="%%"; else $company_name="$company_name";
	if($cbo_shipment_status) $shipment_status_cond="and b.shiping_status in($cbo_shipment_status)";else $shipment_status_cond="";

	if($cbo_sustainability_standard!=0) $sustaina_cond="and a.sustainability_standard=$cbo_sustainability_standard";else $sustaina_cond="";
	if($cbo_fab_material!=0) $fab_mat_cond="and a.fab_material=$cbo_fab_material";else $fab_mat_cond="";
	//echo $shipment_status_cond;die;
	//if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	if(trim($txt_file)!="") $file_cond="and b.file_no='$txt_file'"; else $file_cond="";
	if(trim($txt_ref)!="") $ref_cond="and b.grouping='$txt_ref'";else $ref_cond="";
	if($db_type==0)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-');
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-',1);
	}

	//$cbo_category_by=$data[7]; $caption_date='';
	if($category_by==1)
	{
		if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	else if($category_by==2)
	{
		if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	else if($category_by==3)
	{
		if ($start_date!="" && $end_date!="")
		{
			if($db_type==0) $date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			else if($db_type==2) $date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
		}
		else $date_cond="";
	}else if($category_by==4)
	{
			if ($start_date!="" && $end_date!="") $date_cond=" and b.pack_handover_date between '$start_date' and '$end_date'"; else $date_cond="";
	}else if($category_by==5)
	{
			if ($start_date!="" && $end_date!="") $date_cond=" and b.factory_received_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	else if($category_by==6)
	{
		if ($start_date!="" && $end_date!="") $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	

	if($search_by==1)
	{
		if($search_string=="") $search_string_cond=""; else $search_string_cond= " and b.po_number like '$search_string'";
	}
	else if($search_by==2)
	{
		if($search_string=="") $search_string_cond=""; else $search_string_cond= " and a.style_ref_no like '$search_string'";
	}
	else if($search_by==3)
	{
		if($search_string=="") $search_string_cond=""; else $search_string_cond= " and a.job_no like ('%$search_string')";
	}

	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	
	$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	
	
	
	if($rpt_type==7)//Details 3
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		$factory_mar_arr=return_library_array( "select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name",'id','team_member_name');
		$sub_dep_arr=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment where  status_active =1 and is_deleted=0 order by sub_department_name",'id','sub_department_name');
		
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
		
		
		
		if($db_type==2) 
		{ 
			$date=date('d-m-Y');
			$year_select="to_char(a.insert_date,'YYYY') as year";
			$days_on=" (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4";
		}
		else
		{ 
			$date=date('d-m-Y');
			$year_select="YEAR(a.insert_date) as year";
			$days_on="DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4";
		}
		
	  $sql_data="SELECT a.PRODUCT_CODE,a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.season_buyer_wise,a.brand_id, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $sustaina_cond $fab_mat_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.PRODUCT_CODE,a.season_buyer_wise,a.brand_id, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  //echo  $sql_data; die;
		$data_array=sql_select( $sql_data);
		$all_po_id="";
		foreach($data_array as $row) //
		{
			$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
			$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
			$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
			$po_wise_arr[$row[csf('po_id')]]['brand_name']=$row[csf('brand_id')];
			$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
			$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
			$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
			$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
			$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
			$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			
			$po_wise_arr[$row[csf('po_id')]]['qlty_label']=$row[csf('qlty_label')];
			$po_wise_arr[$row[csf('po_id')]]['sustainability_standard']=$row[csf('sustainability_standard')];
			$po_wise_arr[$row[csf('po_id')]]['fab_material']=$row[csf('fab_material')];
			$po_wise_arr[$row[csf('po_id')]]['order_nature']=$row[csf('quality_level')];

			$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
			$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
			$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
			$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
			$po_wise_arr[$row[csf('po_id')]]['inserted_by']=$row[csf('inserted_by')];
			$po_wise_arr[$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
			$po_wise_arr[$row[csf('po_id')]]['shipment_date']=$row[csf('shipment_date')];
			$po_wise_arr[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$po_wise_arr[$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
			$po_wise_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
			$po_wise_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
			$po_wise_arr[$row[csf('po_id')]]['details_remarks']=$row[csf('details_remarks')];
			
			$po_wise_arr[$row[csf('po_id')]]['file_no']=$row[csf('file_no')];
			$po_wise_arr[$row[csf('po_id')]]['grouping']=$row[csf('grouping')];
			$po_wise_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
			$po_wise_arr[$row[csf('po_id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_1']=$row[csf('date_diff_1')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_2']=$row[csf('date_diff_2')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_3']=$row[csf('date_diff_3')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_4']=$row[csf('date_diff_4')];
			$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
			$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$po_wise_arr[$row[csf('po_id')]]['TXT_ETD_LDD']=$row[csf('TXT_ETD_LDD')];
			$po_wise_arr[$row[csf('po_id')]]['SHIP_MODE']=$shipment_mode[$row[csf('SHIP_MODE')]];
			
			$po_wise_arr[$row[csf('po_id')]]['PRODUCT_DEPT']=$product_dept[$row[csf('PRODUCT_DEPT')]];
			$po_wise_arr[$row[csf('po_id')]]['PRO_SUB_DEP']=$sub_dep_arr[$row[csf('PRO_SUB_DEP')]];
			$po_wise_arr[$row[csf('po_id')]]['PRODUCT_CODE']=$row[csf('PRODUCT_CODE')];
			$po_wise_arr[$row[csf('po_id')]]['factory_marchant']=$factory_mar_arr[$row[csf('factory_marchant')]];
			
			$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
			$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];

			//Company Buyer Wise
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
			
			$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$company_buyer_by_po_arr[$row[csf('po_id')]]=$row[csf('company_name')].'**'.$row[csf('buyer_name')];
			
			
		}
		
		
		$color_arr=return_library_array("select id, color_name from lib_color", "id", "color_name");
		$size_arr=return_library_array("select id, size_name from lib_size", "id", "size_name");
		$poSizeSql="select PO_BREAK_DOWN_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID from wo_po_color_size_breakdown where IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."";
		$poSizeSqlRes=sql_select($poSizeSql);
		foreach($poSizeSqlRes as $row)
		{
			$colorArr[$row[PO_BREAK_DOWN_ID]][$row[COLOR_NUMBER_ID]]=$color_arr[$row[COLOR_NUMBER_ID]];
			$sizeArr[$row[PO_BREAK_DOWN_ID]][$row[SIZE_NUMBER_ID]]=$size_arr[$row[SIZE_NUMBER_ID]];
		}
		
		
		$po_cond_for_in=where_con_using_array($all_po_id_arr,0,'b.po_break_down_id');
		$po_cond_for_in2=where_con_using_array($all_po_id_arr,0,'b.id');
		$po_cond_for_in3=where_con_using_array($all_po_id_arr,0,'a.wo_po_break_down_id');
		
 
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id like '$company_name' $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");
		
	
		
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			$po_qty_pcs=$po_wise_arr[$row[csf('po_id')]]['po_quantity']*$po_wise_arr[$row[csf('po_id')]]['total_set_qnty'];
			//Buyer Wise
			if($shiping_status_id==3)//Full shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
				
				if(($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')])>$po_qty_pcs){
					$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['over_del_qty']+=($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')])-$po_qty_pcs;
				}
				
				unset($company_buyer_by_po_arr[$row[csf('po_id')]]);
			
			}
			else if($shiping_status_id==2)//Partial shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			
			if($shiping_status_id!=3){
				$ex_factory_qty_arr[$row[csf('po_id')]]['pending_del_qty']+=($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')]);
				
			}
		}
		
		
		foreach($company_buyer_by_po_arr as  $po_id=>$rowsStr){
			list($company,$buyer)=explode('**',$rowsStr);
			$buyer_ex_factory_qty_arr[$company][$buyer]['runnign_qty']+=($po_wise_arr[$po_id]['po_quantity']-$ex_factory_qty_arr[$po_id]['pending_del_qty']);
			
		}
 	
		
		
 		
		
		if($db_type==0) $fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
		else if($db_type==2) $fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			
		$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');

		//$sql_budget="select a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		$sql_budget="select a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,c.fabric_description from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond";
		
		$data_budget_array=sql_select($sql_budget);
		
		$fabric_arr=array();
		foreach ($data_budget_array as $row)
		{
			$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('fabric_description')]]=$row[csf('fabric_description')];
			if($row[csf('yarn_cons_qnty')]>0)
			{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			}
			$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
		}
		//var_dump($fabric_arr);die;
		$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "Select b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "Select b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}

		foreach($actual_po_sql as $row)
		{
			$actual_po_no_arr[$row[csf('po_break_down_id')]]=$row[csf('acc_po_no')];
		}
		unset($actual_po_sql);
		//die;
		$sql_lc_result=sql_select("select a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor ");
		$lc_po_id="";
		foreach ($sql_lc_result as $row)
		{
			$lc_id_arr[$row[csf('wo_po_break_down_id')]] = $row[csf('com_export_lc_id')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
			
			if($lc_po_id=="") $lc_po_id=$row[csf('com_export_lc_id')];else $lc_po_id.=",".$row[csf('com_export_lc_id')];
		}
		unset($sql_lc_result);
		$sql_sc_result=sql_select("select a.wo_po_break_down_id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
		foreach ($sql_sc_result as $row)
		{
			$sc_number_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('contract_no')].',';
			$sc_bank_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('lien_bank')].',';
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
		}
		unset($sql_sc_result);
						
		if($db_type==0)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		if($db_type==2)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		$lcIds=chop($lc_po_id,','); $lc_cond_for_in=""; 
		$lc_ids=count(array_unique(explode(",",$lc_po_id)));
		if($db_type==2 && $lc_ids>1000)
		{
			$lc_cond_for_in=" and (";
			$lcIdsArr=array_chunk(explode(",",$lcIds),999);
			foreach($lcIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$lc_cond_for_in.=" export_lc_id in($ids) or"; 
			}
			$lc_cond_for_in=chop($lc_cond_for_in,'or ');
			$lc_cond_for_in.=")";
		}
		else $lc_cond_for_in=" and export_lc_id in($lcIds)";
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		
		
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		 if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
		 if(count($all_po_id_arr)>0)
		 {
			$all_po_id_str=implode(',',$all_po_id_arr);
			$condition->po_id("in($all_po_id_str)");
		 }
		 
		 
		$condition->init();
		
		$commission= new commision($condition);
		$commission_costing_sum_arr=$commission->getAmountArray_by_order();
		 //print_r($commission_costing_sum_arr);die;
	
				
		ob_start();
		
		$width=5430;
		?>
		<div align="center">
			<div align="center">
			<table>
				<tr valign="top">
					<td valign="top">
					<h3 align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu( this.id,'content_summary1_panel', '')"> -Summary Panel</h3>
					<div id="content_summary1_panel">
					<fieldset>
					<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
						<thead>
							<th width="50">SL</th>
							<th width="60">Company Name</th>
                            <th width="120">Buyer Name</th>
							<th width="100">Quantity</th>
                            <th width="100">Value</th>
                            <th width="50">Value %</th>
							<th width="80"><strong>Full Shipped</strong></th>
                            <th width="80"><strong>Partial Shipped</strong></th>
							<th width="80"><strong>Running</strong></th>
							<th width="80"><strong>Over Shipped Qty</strong></th>
 							<th width="80"><strong>Over Shipped Value</strong></th>
                            <th><strong>Ex-factory Percentage</strong></th>
                        </thead>
						<tbody>
						<?
						$i=1; $total_po=0; $total_price=0;
						$po_qnty_array= array(); $po_value_array= array(); $po_full_shiped_array= array(); $po_full_shiped_value_array= array(); $po_partial_shiped_array= array(); $po_partial_shiped_value_array= array();  $po_running_array= array(); $po_running_value_array= array();
						foreach ($buyer_wise_arr as $com_id=>$buyer_data)
						{
							foreach ($buyer_data as $buyer_id=>$row)
							{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$full_shiped=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['full_del_qty'];
							$partial_shiped=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['partial_del_qty'];
							?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td align="center"><?=$i;?></td>
								<td><?=$company_short_name_arr[$com_id]; ?></td>
								<td><?=$buyer_short_name_arr[$buyer_id];?></td>
								<td align="right">
								<?
								echo number_format($row[('po_quantity')],0); $total_po +=$row[('po_quantity')];
								if (array_key_exists($com_id, $po_qnty_array))
								{
									$po_qnty_array[$com_id]+=$row[('po_quantity')];
								}
								else $po_qnty_array[$com_id]=$row[('po_quantity')];
								?>
								</td>
								<td align="right">
								<?
								echo number_format($row[('po_total_price')],2); $total_price+= $row[('po_total_price')];
								if (array_key_exists($com_id, $po_value_array))
								{
									$po_value_array[$com_id]+=$row[('po_total_price')];
								}
								else $po_value_array[$com_id]=$row[('po_total_price')];
								?><input type="hidden" id="value_<?=$i; ?>" value="<?=$row[('po_total_price')]; ?>"/>
								</td>
								<td id="value_percent_<?=$i; ?>" align="right"></td>
								<td align="right">
								<?
								echo number_format($full_shiped,0); $full_shipped_total+=$full_shiped;
								if (array_key_exists($com_id, $po_full_shiped_array))
								{
									$po_full_shiped_array[$com_id]+=$full_shiped;
								}
								else $po_full_shiped_array[$com_id]=$full_shiped;
								?>
								</td>
								<td align="right">
								<?
								echo number_format($partial_shiped,0); $partial_shipped_total+=$partial_shiped;
								if (array_key_exists($com_id, $po_partial_shiped_array))
								{
									$po_partial_shiped_array[$com_id]+=$partial_shiped;
								}
								else $po_partial_shiped_array[$com_id]=$partial_shiped;
								?>
								</td>
								<td align="right">
								<?
								//$runing=$row[('po_quantity')]-($full_shiped+$partial_shiped); 
								$runing=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['runnign_qty'];
								echo number_format($runing,0);
								$running_shipped_total+=$runing;
								
								if (array_key_exists($com_id, $po_running_array))
								{
									$po_running_array[$com_id]+=$runing;
								}
								else $po_running_array[$com_id]=$runing;
								?>
								</td>
                                <td width="80" align="right">
								<?
								echo $over_del_qty=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['over_del_qty'];
								$total_over_del_qty+=$over_del_qty;
								?>
                                </td>
                                <td width="80" align="center">
								<? 
								echo number_format($over_del_val=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['over_del_qty']*($row[('po_total_price')]/$row[('po_quantity')]),2);
								$total_over_del_val+=$over_del_val;
								
								?>
                                </td>
								<td align="right"><? $status=(($full_shiped+$partial_shiped)/$row[('po_quantity')])*100; $full_shipped_total_percent+=$status;  echo number_format($status,2); ?></td>
							</tr>
							<?
							$i++;
							}
						}
						?>
						</tbody>
						<tfoot>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
                            <th>&nbsp;</th>
							<th><?=number_format($total_po,0); ?></th>
                            <th><?=number_format($total_price,2); ?><input type="hidden" id="total_value" value="<?=$total_price;?>"/></th>
                            <th>&nbsp;</th>
							<th><?=number_format($full_shipped_total,0); ?></th>
                            <th><?=number_format($partial_shipped_total,0); ?></th>
							<th><?=number_format($running_shipped_total,0); ?></th>
                            <th><?=number_format($total_over_del_qty,0);?></th>
                            <th><?=number_format($total_over_del_val,2);?></th>
                            <th><input type="hidden" id="tot_row" value="<?=$i;?>"/>&nbsp;</th>
						</tfoot>
					</table>
				</fieldset>
			</div>
					</td>
					<td valign="top">
					<h3 align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu( this.id,'content_summary2_panel', '')"> -Summary Panel</h3>
					<div id="content_summary2_panel">
					<fieldset>
						<table width="800" border="1" class="rpt_table" rules="all">
							<thead>
								<th>Company Name</th>
								<th>Particular Name</th>
								<th>Total Amount</th>
								<th>Full Shipped </th>
								<th>Partial Shipped </th>
								<th>Running</th>
                                <th>Over Shipped Qty</th>
								<th>Ex-factory Percentage</th>
							</thead>
						<?
						$comp_po_total=0; $comp_po_total_value=0; $total_full_shiped_qnty=0; $total_par_qnty=0; $total_run_qnty=0; $total_full_shiped_val=0; $total_par_val=0; $total_run_val=0;
						foreach($po_qnty_array as $key=> $value)
						{
							?>
							<tr>
								<td rowspan="2" align="center"><? echo $company_short_name_arr[$key];//echo $company_name; ?></td>
								<td align="center">PO Quantity</td>
								<td align="right"><? echo number_format($value+$po_qnty_array_projec[$key],0);$comp_po_total=$comp_po_total+$value+$po_qnty_array_projec[$key]; ?></td>
								<td align="right"><? echo number_format($po_full_shiped_array[$key],0); $total_full_shiped_qnty+=$po_full_shiped_array[$key];?></td>
								<td align="right"><? echo number_format($po_partial_shiped_array[$key],0); $total_par_qnty+=$po_partial_shiped_array[$key];?></td>
								<td align="right"><? echo number_format($po_running_array[$key],0); $total_run_qnty+=$po_running_array[$key]; ?> </td>
                                <td align="right">
                                <?
                                	foreach($buyer_ex_factory_qty_arr[$key] as $drow){
										$overDelQty+=$drow['over_del_qty'];
									}
									echo $overDelQty;
									$grandTotalOverQty+=$overDelQty;
								?>
                                </td>
								<td align="right"><? $ex_factory_per=(($po_full_shiped_array[$key]+$po_partial_shiped_array[$key])/($value))*100; echo number_format($ex_factory_per,2).' %'; ?></td>
							</tr>
							<tr bgcolor="white">
								<td align="center">LC Value</td>
								<td align="right"><? echo number_format($po_value_array[$key],2);  $comp_po_total_value=$comp_po_total_value+$po_value_array[$key];?></td>
								<td align="right"><? $full_shiped_value=($po_value_array[$key]/$value)*$po_full_shiped_array[$key]; echo number_format($full_shiped_value,2); $total_full_shiped_val+=$full_shiped_value; ?></td>
								<td align="right"><? $full_partial_shipeddd_value=($po_value_array[$key]/$value)*$po_partial_shiped_array[$key]; echo number_format($full_partial_shipeddd_value,2); $total_par_val+=$full_partial_shipeddd_value; ?></td>
								<td align="right"><? $full_running_value=($po_value_array[$key]/$value)*$po_running_array[$key]; echo number_format($full_running_value,2); $total_run_val+=$full_running_value; ?></td>
								<td align="right">
                                <?
                                	foreach($buyer_ex_factory_qty_arr[$key] as $drow){
										$overDelQty+=$drow['over_del_qty'];
									}
									echo number_format($overLCValie=($po_value_array[$key]/$value)*$overDelQty,2);
									$grandTotalOverValue+=$overLCValie;
								?>
                                </td>
                                <td align="right"><? $ex_factory_per_value=(($full_shiped_value+$full_partial_shipeddd_value)/($po_value_array[$key]))*100; echo number_format($ex_factory_per_value,2).' %'; ?></td>
							</tr>
							<?
						}
						?>
						<tfoot>
							<tr>
								<th align="center" rowspan="2"> Total:</th>
								<th align="center">Qnty Total:</th>
								<th align="right"><? echo number_format($comp_po_total,0); ?></th>
								<th align="right"><? echo number_format($total_full_shiped_qnty,2); ?></th>
								<th align="right"><? echo number_format($total_par_qnty,2); ?></th>
								<th align="right"><? echo number_format($total_run_qnty,2); ?></th>
                                <th align="right"><?=$grandTotalOverQty;?></th>
								<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; ?></th>
							</tr>
							<tr bgcolor="#999999">
								<th align="center">Value Total:</th>
								<th align="right"><? echo number_format($comp_po_total_value,2); ?></th>
								<th align="right"><? echo number_format($total_full_shiped_val,2); ?></th>
								<th align="right"><? echo number_format($total_par_val,2); ?></th>
								<th align="right"><? echo number_format($total_run_val,2); ?></th>
                                <th align="right"><?=number_format($grandTotalOverValue,2);?></th>
								<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; ?></th>
							</tr>
						</tfoot>
					</table>
					</fieldset>
				</div>
				</td>
				<td valign="top">
				<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_summary3_panel', '')"> -Shipment Performance Summary</h3>
				<div id="content_summary3_panel">
				</div>
				</td>
			</tr>
		</table>
		<h3 style="width:<?= $width;?>px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
		<div id="content_report_panel">
			<table width="<?= $width;?>" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="50">Buyer</th>
						<th width="50">Brand</th>
						<th width="100">Season</th>
						<th width="60">Year</th>
                        <th width="100">Prod Dept</th>
                        <th width="100">Sub Dept</th>
                        <th width="100">Class</th>

                        <th width="100">Sustaina. Standard</th>
                        <th width="100">Fab. Material</th>
                        <th width="100">Order Nature</th>
                        <th width="100">Quality Label</th>

						<th width="150">Item</th>
                        <th width="40">Img</th>
						<th width="70">Prod. Catg</th>
						<th width="90">Style Ref</th>
						<th width="70">Job No</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">GMT Color</th>
                        <th width="100">GMT Size</th>
                        <th width="100">Ref No</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
                        <th width="70">Ship/RFI Date</th>
                        <th width="70">ETD/LDD Date</th>
                        <th width="70">Shipment Mode</th>
						<th width="200">Fab. Description</th>
						<th width="70" style="font-size: 14px;"><strong>Ship Date</strong></th>
						<th width="70">PO Rec. Date</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qnty(Pcs)</th>
						<th width="90">Order Qnty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Gross Order Value</th>
						<th width="100">Net Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>
						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>
						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short Qnty</th>
						<th width="100">Short Value</th>
						<th width="90">Excess Qnty</th>
						<th width="100">Excess Value</th>
                        
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
                        <th width="150">Factory Merchandiser</th>
						<th width="100">File No</th>
						<th width="40">Id</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:<?= $width+20;?>px"  align="left" id="scroll_body">
			<table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;$net_goreder_value_tot=0;

					if($db_type==2)
					{
						$date=date('d-m-Y');
						if($row_group[csf('grouping')]!="")
						{
							$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
						}
						if($row_group[csf('grouping')]=="")
						{
							$grouping="and b.grouping IS NULL";
						}
					}
					
					foreach ($po_wise_arr as $po_id=>$row)
					{
						//echo $lc_id_arr[$row[csf('id')]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$cons=0;
						$costing_per_pcs=0;
						$yarn_cons_qnty=$job_yarn_cons_arr[$row[('job_no')]]['yarn_cons_qnty'];
						$costing_per=$job_yarn_cons_arr[$row[('job_no')]]['costing_per'];
						//echo $costing_per.'='.$yarn_cons_qnty.',';
						if($costing_per==1) $costing_per_pcs=1*12;
						else if($costing_per==2) $costing_per_pcs=1*1;
						else if($costing_per==3) $costing_per_pcs=2*12;
						else if($costing_per==4) $costing_per_pcs=3*12;
						else if($costing_per==5) $costing_per_pcs=4*12;

							$cons=$yarn_cons_qnty;
							$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[('shiping_status')]==1 && $row[('date_diff_1')]>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($row[('shiping_status')]==1 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($row[('shiping_status')]==1 &&  $row[('date_diff_1')]<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($row[('shiping_status')]==2 && $row[('date_diff_1')]>10 )
						{
							$color="";
						}
						if($row[('shiping_status')]==2 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
						{
							$color="orange";
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_1')]<0)
						{
							$color="red";
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($row[('shiping_status')]==3 && $row[('date_diff_3')]>=0 )
						{
							$color="green";
						}
						if($row[('shiping_status')]==3 &&  $row[('date_diff_3')]<0)
						{
							$color="#2A9FFF";
						}
						if($row[('shiping_status')]==3 && $row[('date_diff_4')]>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($row[('shiping_status')]==3 &&  $row[('date_diff_4')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
                            <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
							<td width="60"><p><? echo $row[('year')]; ?></p></td>
							
                            <td width="100"><?=$row['PRODUCT_DEPT'];?></td>
                            <td width="100"><?=$row['PRO_SUB_DEP'];?></td>
                            <td width="100"><p><?=$row['PRODUCT_CODE'];?></p></td>
                            	
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $sustainability_standard[$row[('sustainability_standard')]];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><?
                            $fab_material=array(1=>"Organic",2=>"BCI");
                             echo $fab_material[$row[('fab_material')]];?></div></p></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $fbooking_order_nature[$row[('order_nature')]];?></div></p></td>
                            <td width="100"><p><div style="word-wrap:break-word; width:100px"><? echo $quality_label[$row[('qlty_label')]];?></div></p></td>

                            <td width="150">
                            <div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
								$fabric_description_arr=array();
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									$fabric_description_arr[]=implode(',',$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]);
									echo $garments_item[$gmts_item_id[$j]];
								}
								$fabric_description=implode(',',$fabric_description_arr);
								?></div>
                             </td>

                            <td width="40" onclick="openmypage_image('requires/shipment_schedule_controller_v3.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
							<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
							
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
                            <td width="100"><p><?= implode(',',$colorArr[$po_id]);?></p></td>
                            <td width="100"><p><?= implode(',',$sizeArr[$po_id]);?></p></td>
                            
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
                            
                            <td width="70" align="center"><?=change_date_format($row['shipment_date']);?></td>
                            <td width="70" align="center"><?=change_date_format($row['TXT_ETD_LDD']);?></td>
                            <td width="70" align="center"><?=$row['SHIP_MODE'];?></td>
                            
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><strong><? echo '&nbsp;'.change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></strong></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
							<td width="50" bgcolor="<? echo $color; ?>"><div style="word-wrap:break-word; width:50px">
								<?
								if($row[('shiping_status')]==1 || $row[('shiping_status')]==2)
								{
									echo $row[('date_diff_1')];
								}
								if($row[('shiping_status')]==3)
								{
									echo $row[('date_diff_3')];
								}
								?></div></td>
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								?></p></td>
							<td width="90" align="right"><p>
								<?
								echo number_format( $row[('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
								?></p></td>
                            <td width="100" align="right" title="Commission:<?=$commission_costing_sum_arr[$po_id];?>">
							<?
							echo number_format($net_total=($row[('po_total_price')]-$commission_costing_sum_arr[$po_id]),2);
							$net_goreder_value_tot+=$net_total;
							?>
                            </td>
                            <td width="100" align="center"><div style="word-wrap:break-word; width:100px">
                            	<?
								unset($bank_id_arr);
								unset($bank_string_arr);
								if($lc_bank_arr[$po_id] !="")
								{
									$bank_id_arr=array_unique(explode(",",$lc_bank_arr[$po_id]));
									foreach($bank_id_arr as $bank_id)
									{
										$bank_string_arr[]=$bank_name_arr[$bank_id];
									}
									echo implode(",",$bank_string_arr);
								}
								$sc_bank=rtrim($sc_bank_arr[$po_id],',');
								if($sc_bank !="")
								{
									$bank_id_arr=array_unique(explode(",",$sc_bank));
									foreach($bank_id_arr as $bank_id)
									{
										$bank_string_arr[]=$bank_name_arr[$bank_id];
									}
									echo implode(",",$bank_string_arr);
								}
								?>

                            </div>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
								<?
								if($lc_number_arr[$po_id] !="")
								{
									echo "LC: ". $lc_number_arr[$po_id];
									$lc_no = $lc_number_arr[$po_id];
								}
								$sc_number=rtrim($sc_number_arr[$po_id],',');
								$sc_numbers=implode(",",array_unique(explode(",",$sc_number)));
								if($sc_numbers !="")
								{
									echo " SC: ".$sc_numbers;
								}
								?>
								</div></td>
							<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
								<? if($lc_number_arr[$po_id] !="")
									{
										 echo $lc_amendment_arr[$lc_id_arr[$po_id]];

									}
								?>
							</div></td>
							<td width="80" align="center"><p>
							<?
							if($export_lc_arr[$po_id]['file_no']!='') echo $export_lc_arr[$po_id]['file_no'];
							if($export_sc_arr[$po_id]['file_no']!='') echo $export_sc_arr[$po_id]['file_no'];

							?>

							</p></td>
							<td width="80" align="center"><p><?

							if($export_lc_arr[$po_id]['pay_term']!="") echo $export_lc_arr[$po_id]['pay_term'];
							if($export_sc_arr[$po_id]['pay_term']!="") echo $export_sc_arr[$po_id]['pay_term'];

							 ?></p></td>
							<td width="80" align="center"><p><?

							if($export_lc_arr[$po_id]['tenor']!="" ) echo $export_lc_arr[$po_id]['tenor'];
							if($export_sc_arr[$po_id]['tenor']!="" ) echo $export_sc_arr[$po_id]['tenor'];

							 ?></p></td>

							<td width="90" align="right"><p>
							<?
								$ex_factory_del_qty=$ex_factory_qty_arr[$po_id]['del_qty'];
								$ex_factory_return_qty=$ex_factory_qty_arr[$po_id]['return_qty'];
								$ex_factory_qnty=$ex_factory_del_qty-$ex_factory_return_qty;

								//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
								?>
								<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
                                <?

								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($row[('po_quantity')]*$row[('total_set_qnty')]);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qnty;
									$po_value['ontime']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qnty;
									$po_value['after']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
									$po_qnty['yet']+=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
								}
								?></p></td>
							<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									$access_qnty=($ex_factory_qnty-($row[('po_quantity')]*$row[('total_set_qnty')]));

									$short_qnty=($short_qnty>0)?$short_qnty:0;
									$access_qnty=($access_qnty>0)?$access_qnty:0;
									
									
									echo number_format($short_qnty,0);
									$total_short_qnty=$total_short_qnty+$short_qnty;
									$gtotal_short_qnty=$gtotal_short_qnty+$short_qnty;
									
									$total_access_qnty=$total_access_qnty+$access_qnty;
									$gtotal_access_qnty=$gtotal_access_qnty+$access_qnty;
								?></p>
							</td>
							<td width="100" align="right"><p>
								<?
									$short_value=$short_qnty*$row[('unit_price')];
									$access_value=$access_qnty*$row[('unit_price')];
									
									echo number_format($short_value,2);
									$total_short_value=$total_short_value+$short_value;
									$gtotal_short_value=$gtotal_short_value+$short_value;
									
									$total_access_value=$total_access_value+$access_value;
									$gtotal_access_value=$gtotal_access_value+$access_value;
									
								?></p>
							</td>
                            
                            <td width="90" align="right"><?=$access_qnty;?></td>
                            <td width="100" align="right"><?=$access_value;?></td>
                            
                            
							<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
                            
							<td width="150"><?= $row['factory_marchant'];?></td>
                            
                            
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
							<td width="40"><p><? echo $po_id; ?></p></td>
							<td><p><? echo $row[('details_remarks')]; ?></p></td>
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td align="center" >  Total: </td>
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
                        <td></td>

                        <td></td>
						<td></td>
                        <td></td>
                        <td></td>
                        
						<td></td>
						<td></td>
                        
                        <td></td>
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
						<td></td>
                        
                        
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td></td>
						<td></td>

						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
                        <td align="right"><? echo number_format($net_goreder_value_tot,2); ?></td>
                        <td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_qnty,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_value,0); ?></td>
                        
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_saccess_qnty,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_access_value,0); ?></td>
                        
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></th>
					 </tr>
				<?
				
				?>
				</table>
				</div>
				<table style="display: none;" width="<?= $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="60"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>

                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>

							<th width="150"></th>
							<th width="40"></th>
							<th width="70"></th>
							<th width="90"></th>
							<th width="70" ></th>
							<th width="110"></th>
                            <th width="100"></th>
                            
                            <th width="100"></th>
                            <th width="100"></th>
                            
                            <th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
                            
                            <th width="70"></th>
                            <th width="70"></th>
                            <th width="70"></th>
                            
                            
                            
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>

							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
                             <th width="100">ccc</th>
                             <th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_qnty,0); ?></th>
							<th width="100" id="value_total_short_access_value" align="right"><? echo number_format($total_short_value,0); ?></th>
                            
							<th width="90" id="total_access_qnty" align="right"><? echo number_format($total_access_qnty,0); ?></th>
							<th width="100" id="value_total_access_value" align="right"><? echo number_format($total_access_value,0); ?></th>
                            
                            
                            
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100" ></th>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="40"></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
				<?
		

		?>
			<div id="shipment_performance" style="visibility:hidden">
				<fieldset>
					<table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<th colspan="4"> <font size="4">Shipment Performance</font></th>
							</tr>
							<tr>
								<th>Particulars</th><th>No of PO</th><th>PO Qnty</th><th> %</th>
							</tr>
						</thead>
						<tr bgcolor="#E9F3FF">
							<td>On Time Shipment</td><td><? echo $number_of_order['ontime']; ?></td><td align="right"><? echo number_format($po_qnty['ontime'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
							<td> Delivery After Shipment Date</td><td><? echo $number_of_order['after']; ?></td><td align="right"><? echo number_format($po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['after'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
							<td>Yet To Shipment </td>
                            <td><? echo $number_of_order['yet']; ?></td>
                            <td align="right">
							<? $po_qnty['yet']=$total_short_qnty;
							 echo number_format($po_qnty['yet'],0); ?>
                            </td>
                            <td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>

							<tr bgcolor="#E9F3FF">
							<td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			</div>
			</div>
		</div>
		<?
	}if($rpt_type==8)//Details 4
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		$factory_mar_arr=return_library_array( "select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name",'id','team_member_name');
		$sub_dep_arr=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment where  status_active =1 and is_deleted=0 order by sub_department_name",'id','sub_department_name');
		
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
		

		
		
		
			if($db_type==2) 
			{ 
				$date=date('d-m-Y');
				$year_select="to_char(a.insert_date,'YYYY') as year";
				$days_on=" (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4";
			}
			else
			{ 
				$date=date('d-m-Y');
				$year_select="YEAR(a.insert_date) as year";
				$days_on="DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4";
			}
		
	  		$sql_data="SELECT a.PRODUCT_CODE,a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.season_buyer_wise,a.brand_id, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $sustaina_cond $fab_mat_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.PRODUCT_CODE,a.season_buyer_wise,a.brand_id, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  //echo  $sql_data; die;
		$data_array=sql_select( $sql_data);
		$all_po_id="";
		foreach($data_array as $row) //
		{
			$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
			$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
			$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
			$po_wise_arr[$row[csf('po_id')]]['brand_name']=$row[csf('brand_id')];
			$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
			$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
			$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
			$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
			$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
			$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
			
			$po_wise_arr[$row[csf('po_id')]]['qlty_label']=$row[csf('qlty_label')];
			$po_wise_arr[$row[csf('po_id')]]['sustainability_standard']=$row[csf('sustainability_standard')];
			$po_wise_arr[$row[csf('po_id')]]['fab_material']=$row[csf('fab_material')];
			$po_wise_arr[$row[csf('po_id')]]['order_nature']=$row[csf('quality_level')];

			$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
			$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
			$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
			$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
			$po_wise_arr[$row[csf('po_id')]]['inserted_by']=$row[csf('inserted_by')];
			$po_wise_arr[$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
			$po_wise_arr[$row[csf('po_id')]]['shipment_date']=$row[csf('shipment_date')];
			$po_wise_arr[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$po_wise_arr[$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
			$po_wise_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
			$po_wise_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
			$po_wise_arr[$row[csf('po_id')]]['details_remarks']=$row[csf('details_remarks')];
			
			$po_wise_arr[$row[csf('po_id')]]['file_no']=$row[csf('file_no')];
			$po_wise_arr[$row[csf('po_id')]]['grouping']=$row[csf('grouping')];
			$po_wise_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
			$po_wise_arr[$row[csf('po_id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_1']=$row[csf('date_diff_1')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_2']=$row[csf('date_diff_2')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_3']=$row[csf('date_diff_3')];
			$po_wise_arr[$row[csf('po_id')]]['date_diff_4']=$row[csf('date_diff_4')];
			$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
			$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$po_wise_arr[$row[csf('po_id')]]['TXT_ETD_LDD']=$row[csf('TXT_ETD_LDD')];
			$po_wise_arr[$row[csf('po_id')]]['SHIP_MODE']=$shipment_mode[$row[csf('SHIP_MODE')]];
			
			$po_wise_arr[$row[csf('po_id')]]['PRODUCT_DEPT']=$product_dept[$row[csf('PRODUCT_DEPT')]];
			$po_wise_arr[$row[csf('po_id')]]['PRO_SUB_DEP']=$sub_dep_arr[$row[csf('PRO_SUB_DEP')]];
			$po_wise_arr[$row[csf('po_id')]]['PRODUCT_CODE']=$row[csf('PRODUCT_CODE')];
			$po_wise_arr[$row[csf('po_id')]]['factory_marchant']=$factory_mar_arr[$row[csf('factory_marchant')]];
			
			$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
			$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];

			//Company Buyer Wise
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
			
			$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$company_buyer_by_po_arr[$row[csf('po_id')]]=$row[csf('company_name')].'**'.$row[csf('buyer_name')];
			
			
		}
		
		
		$color_arr=return_library_array("select id, color_name from lib_color", "id", "color_name");
		$size_arr=return_library_array("select id, size_name from lib_size", "id", "size_name");
		$poSizeSql="select PO_BREAK_DOWN_ID,COLOR_NUMBER_ID,ORDER_QUANTITY,SIZE_NUMBER_ID from wo_po_color_size_breakdown where IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."";
		$poSizeSqlRes=sql_select($poSizeSql);
		foreach($poSizeSqlRes as $row)
		{
			$colorArr[$row[PO_BREAK_DOWN_ID]][$row[COLOR_NUMBER_ID]]=$color_arr[$row[COLOR_NUMBER_ID]];
			$sizeArr[$row[PO_BREAK_DOWN_ID]][$row[SIZE_NUMBER_ID]]=$size_arr[$row[SIZE_NUMBER_ID]];
			$brk_downPcs_arr[$row['PO_BREAK_DOWN_ID']]['brk_dn_pcs']+=$row['ORDER_QUANTITY'];
		}
		
		
		$po_cond_for_in=where_con_using_array($all_po_id_arr,0,'b.po_break_down_id');
		$po_cond_for_in2=where_con_using_array($all_po_id_arr,0,'b.id');
		$po_cond_for_in3=where_con_using_array($all_po_id_arr,0,'a.wo_po_break_down_id');
		
 
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id like '$company_name' $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");
		
	
		
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			$po_qty_pcs=$po_wise_arr[$row[csf('po_id')]]['po_quantity']*$po_wise_arr[$row[csf('po_id')]]['total_set_qnty'];
			//Buyer Wise
			if($shiping_status_id==3)//Full shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
				
				if(($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')])>$po_qty_pcs){
					$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['over_del_qty']+=($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')])-$po_qty_pcs;
				}
				
				unset($company_buyer_by_po_arr[$row[csf('po_id')]]);
			
			}
			else if($shiping_status_id==2)//Partial shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			
			if($shiping_status_id!=3){
				$ex_factory_qty_arr[$row[csf('po_id')]]['pending_del_qty']+=($row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')]);
				
			}
		}
		
		
		foreach($company_buyer_by_po_arr as  $po_id=>$rowsStr){
			list($company,$buyer)=explode('**',$rowsStr);
			$buyer_ex_factory_qty_arr[$company][$buyer]['runnign_qty']+=($po_wise_arr[$po_id]['po_quantity']-$ex_factory_qty_arr[$po_id]['pending_del_qty']);
			
		}
 	
		
		
 		
		
		if($db_type==0) $fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
		else if($db_type==2) $fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			
		$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');

		//$sql_budget="select a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		$sql_budget="select a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,c.fabric_description from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond";
		
		$data_budget_array=sql_select($sql_budget);
		
		$fabric_arr=array();
		foreach ($data_budget_array as $row)
		{
			$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('fabric_description')]]=$row[csf('fabric_description')];
			if($row[csf('yarn_cons_qnty')]>0)
			{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			}
			$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
		}
		//var_dump($fabric_arr);die;
		$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "Select b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "Select b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}

		foreach($actual_po_sql as $row)
		{
			$actual_po_no_arr[$row[csf('po_break_down_id')]]=$row[csf('acc_po_no')];
		}
		unset($actual_po_sql);
		//die;
		$sql_lc_result=sql_select("select a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor ");
		$lc_po_id="";
		foreach ($sql_lc_result as $row)
		{
			$lc_id_arr[$row[csf('wo_po_break_down_id')]] = $row[csf('com_export_lc_id')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
			
			if($lc_po_id=="") $lc_po_id=$row[csf('com_export_lc_id')];else $lc_po_id.=",".$row[csf('com_export_lc_id')];
		}
		unset($sql_lc_result);
		$sql_sc_result=sql_select("select a.wo_po_break_down_id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
		foreach ($sql_sc_result as $row)
		{
			$sc_number_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('contract_no')].',';
			$sc_bank_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('lien_bank')].',';
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
		}
		unset($sql_sc_result);
						
		if($db_type==0)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		if($db_type==2)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		$lcIds=chop($lc_po_id,','); $lc_cond_for_in=""; 
		$lc_ids=count(array_unique(explode(",",$lc_po_id)));
		if($db_type==2 && $lc_ids>1000)
		{
			$lc_cond_for_in=" and (";
			$lcIdsArr=array_chunk(explode(",",$lcIds),999);
			foreach($lcIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$lc_cond_for_in.=" export_lc_id in($ids) or"; 
			}
			$lc_cond_for_in=chop($lc_cond_for_in,'or ');
			$lc_cond_for_in.=")";
		}
		else $lc_cond_for_in=" and export_lc_id in($lcIds)";
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		
		
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		 if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
		 if(count($all_po_id_arr)>0)
		 {
			$all_po_id_str=implode(',',$all_po_id_arr);
			$condition->po_id("in($all_po_id_str)");
		 }
		 
		 
		$condition->init();
		
		$commission= new commision($condition);
		$commission_costing_sum_arr=$commission->getAmountArray_by_order();
		 //print_r($commission_costing_sum_arr);die;
	
				
		ob_start();
		
		$width=5520;
		?>
		<div align="center">
			<div align="center">
			<table>
				<tr valign="top">
					<td valign="top">
					<h3 align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu( this.id,'content_summary1_panel', '')"> -Summary Panel</h3>
					<div id="content_summary1_panel">
					<fieldset>
					<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
						<thead>
							<th width="50">SL</th>
							<th width="60">Company Name</th>
                            <th width="120">Buyer Name</th>
							<th width="100">Quantity</th>
                            <th width="100">Value</th>
                            <th width="50">Value %</th>
							<th width="80"><strong>Full Shipped</strong></th>
                            <th width="80"><strong>Partial Shipped</strong></th>
							<th width="80"><strong>Running</strong></th>
							<th width="80"><strong>Over Shipped Qty</strong></th>
 							<th width="80"><strong>Over Shipped Value</strong></th>
                            <th><strong>Ex-factory Percentage</strong></th>
                        </thead>
						<tbody>
						<?
						$i=1; $total_po=0; $total_price=0;
						$po_qnty_array= array(); $po_value_array= array(); $po_full_shiped_array= array(); $po_full_shiped_value_array= array(); $po_partial_shiped_array= array(); $po_partial_shiped_value_array= array();  $po_running_array= array(); $po_running_value_array= array();
						foreach ($buyer_wise_arr as $com_id=>$buyer_data)
						{
							foreach ($buyer_data as $buyer_id=>$row)
							{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$full_shiped=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['full_del_qty'];
							$partial_shiped=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['partial_del_qty'];
							?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td align="center"><?=$i;?></td>
								<td><?=$company_short_name_arr[$com_id]; ?></td>
								<td><?=$buyer_short_name_arr[$buyer_id];?></td>
								<td align="right">
								<?
								echo number_format($row[('po_quantity')],0); $total_po +=$row[('po_quantity')];
								if (array_key_exists($com_id, $po_qnty_array))
								{
									$po_qnty_array[$com_id]+=$row[('po_quantity')];
								}
								else $po_qnty_array[$com_id]=$row[('po_quantity')];
								?>
								</td>
								<td align="right">
								<?
								echo number_format($row[('po_total_price')],2); $total_price+= $row[('po_total_price')];
								if (array_key_exists($com_id, $po_value_array))
								{
									$po_value_array[$com_id]+=$row[('po_total_price')];
								}
								else $po_value_array[$com_id]=$row[('po_total_price')];
								?><input type="hidden" id="value_<?=$i; ?>" value="<?=$row[('po_total_price')]; ?>"/>
								</td>
								<td id="value_percent_<?=$i; ?>" align="right"></td>
								<td align="right">
								<?
								echo number_format($full_shiped,0); $full_shipped_total+=$full_shiped;
								if (array_key_exists($com_id, $po_full_shiped_array))
								{
									$po_full_shiped_array[$com_id]+=$full_shiped;
								}
								else $po_full_shiped_array[$com_id]=$full_shiped;
								?>
								</td>
								<td align="right">
								<?
								echo number_format($partial_shiped,0); $partial_shipped_total+=$partial_shiped;
								if (array_key_exists($com_id, $po_partial_shiped_array))
								{
									$po_partial_shiped_array[$com_id]+=$partial_shiped;
								}
								else $po_partial_shiped_array[$com_id]=$partial_shiped;
								?>
								</td>
								<td align="right">
								<?
								//$runing=$row[('po_quantity')]-($full_shiped+$partial_shiped); 
								$runing=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['runnign_qty'];
								echo number_format($runing,0);
								$running_shipped_total+=$runing;
								
								if (array_key_exists($com_id, $po_running_array))
								{
									$po_running_array[$com_id]+=$runing;
								}
								else $po_running_array[$com_id]=$runing;
								?>
								</td>
                                <td width="80" align="right">
								<?
								echo $over_del_qty=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['over_del_qty'];
								$total_over_del_qty+=$over_del_qty;
								?>
                                </td>
                                <td width="80" align="center">
								<? 
								echo number_format($over_del_val=$buyer_ex_factory_qty_arr[$com_id][$buyer_id]['over_del_qty']*($row[('po_total_price')]/$row[('po_quantity')]),2);
								$total_over_del_val+=$over_del_val;
								
								?>
                                </td>
								<td align="right"><? $status=(($full_shiped+$partial_shiped)/$row[('po_quantity')])*100; $full_shipped_total_percent+=$status;  echo number_format($status,2); ?></td>
							</tr>
							<?
							$i++;
							}
						}
						?>
						</tbody>
						<tfoot>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
                            <th>&nbsp;</th>
							<th><?=number_format($total_po,0); ?></th>
                            <th><?=number_format($total_price,2); ?><input type="hidden" id="total_value" value="<?=$total_price;?>"/></th>
                            <th>&nbsp;</th>
							<th><?=number_format($full_shipped_total,0); ?></th>
                            <th><?=number_format($partial_shipped_total,0); ?></th>
							<th><?=number_format($running_shipped_total,0); ?></th>
                            <th><?=number_format($total_over_del_qty,0);?></th>
                            <th><?=number_format($total_over_del_val,2);?></th>
                            <th><input type="hidden" id="tot_row" value="<?=$i;?>"/>&nbsp;</th>
						</tfoot>
					</table>
				</fieldset>
			</div>
					</td>
					<td valign="top">
					<h3 align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu( this.id,'content_summary2_panel', '')"> -Summary Panel</h3>
					<div id="content_summary2_panel">
					<fieldset>
						<table width="800" border="1" class="rpt_table" rules="all">
							<thead>
								<th>Company Name</th>
								<th>Particular Name</th>
								<th>Total Amount</th>
								<th>Full Shipped </th>
								<th>Partial Shipped </th>
								<th>Running</th>
                                <th>Over Shipped Qty</th>
								<th>Ex-factory Percentage</th>
							</thead>
						<?
						$comp_po_total=0; $comp_po_total_value=0; $total_full_shiped_qnty=0; $total_par_qnty=0; $total_run_qnty=0; $total_full_shiped_val=0; $total_par_val=0; $total_run_val=0;
						foreach($po_qnty_array as $key=> $value)
						{
							?>
							<tr>
								<td rowspan="2" align="center"><? echo $company_short_name_arr[$key];//echo $company_name; ?></td>
								<td align="center">PO Quantity</td>
								<td align="right"><? echo number_format($value+$po_qnty_array_projec[$key],0);$comp_po_total=$comp_po_total+$value+$po_qnty_array_projec[$key]; ?></td>
								<td align="right"><? echo number_format($po_full_shiped_array[$key],0); $total_full_shiped_qnty+=$po_full_shiped_array[$key];?></td>
								<td align="right"><? echo number_format($po_partial_shiped_array[$key],0); $total_par_qnty+=$po_partial_shiped_array[$key];?></td>
								<td align="right"><? echo number_format($po_running_array[$key],0); $total_run_qnty+=$po_running_array[$key]; ?> </td>
                                <td align="right">
                                <?
                                	foreach($buyer_ex_factory_qty_arr[$key] as $drow){
										$overDelQty+=$drow['over_del_qty'];
									}
									echo $overDelQty;
									$grandTotalOverQty+=$overDelQty;
								?>
                                </td>
								<td align="right"><? $ex_factory_per=(($po_full_shiped_array[$key]+$po_partial_shiped_array[$key])/($value))*100; echo number_format($ex_factory_per,2).' %'; ?></td>
							</tr>
							<tr bgcolor="white">
								<td align="center">LC Value</td>
								<td align="right"><? echo number_format($po_value_array[$key],2);  $comp_po_total_value=$comp_po_total_value+$po_value_array[$key];?></td>
								<td align="right"><? $full_shiped_value=($po_value_array[$key]/$value)*$po_full_shiped_array[$key]; echo number_format($full_shiped_value,2); $total_full_shiped_val+=$full_shiped_value; ?></td>
								<td align="right"><? $full_partial_shipeddd_value=($po_value_array[$key]/$value)*$po_partial_shiped_array[$key]; echo number_format($full_partial_shipeddd_value,2); $total_par_val+=$full_partial_shipeddd_value; ?></td>
								<td align="right"><? $full_running_value=($po_value_array[$key]/$value)*$po_running_array[$key]; echo number_format($full_running_value,2); $total_run_val+=$full_running_value; ?></td>
								<td align="right">
                                <?
                                	foreach($buyer_ex_factory_qty_arr[$key] as $drow){
										$overDelQty+=$drow['over_del_qty'];
									}
									echo number_format($overLCValie=($po_value_array[$key]/$value)*$overDelQty,2);
									$grandTotalOverValue+=$overLCValie;
								?>
                                </td>
                                <td align="right"><? $ex_factory_per_value=(($full_shiped_value+$full_partial_shipeddd_value)/($po_value_array[$key]))*100; echo number_format($ex_factory_per_value,2).' %'; ?></td>
							</tr>
							<?
						}
						?>
						<tfoot>
							<tr>
								<th align="center" rowspan="2"> Total:</th>
								<th align="center">Qnty Total:</th>
								<th align="right"><? echo number_format($comp_po_total,0); ?></th>
								<th align="right"><? echo number_format($total_full_shiped_qnty,2); ?></th>
								<th align="right"><? echo number_format($total_par_qnty,2); ?></th>
								<th align="right"><? echo number_format($total_run_qnty,2); ?></th>
                                <th align="right"><?=$grandTotalOverQty;?></th>
								<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; ?></th>
							</tr>
							<tr bgcolor="#999999">
								<th align="center">Value Total:</th>
								<th align="right"><? echo number_format($comp_po_total_value,2); ?></th>
								<th align="right"><? echo number_format($total_full_shiped_val,2); ?></th>
								<th align="right"><? echo number_format($total_par_val,2); ?></th>
								<th align="right"><? echo number_format($total_run_val,2); ?></th>
                                <th align="right"><?=number_format($grandTotalOverValue,2);?></th>
								<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; ?></th>
							</tr>
						</tfoot>
					</table>
					</fieldset>
				</div>
				</td>
				<td valign="top">
				<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_summary3_panel', '')"> -Shipment Performance Summary</h3>
				<div id="content_summary3_panel">
				</div>
				</td>
			</tr>
		</table>
		
		<?
		if($search_by==1)
		{
		?>


		<h3 style="width:<?= $width;?>px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel[Order Wise]</h3>
		<div id="content_report_panel">
			<table width="<?= $width;?>" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="50">Buyer</th>
						<th width="50">Brand</th>
						<th width="100">Season</th>
						<th width="60">Year</th>
                        <th width="100">Prod Dept</th>
                        <th width="100">Sub Dept</th>
                        <th width="100">Class</th>

                        <th width="100">Sustaina. Standard</th>
                        <th width="100">Fab. Material</th>
                        <th width="100">Order Nature</th>
                        <th width="100">Quality Label</th>

						<th width="150">Item</th>
                        <th width="40">Img</th>
						<th width="70">Prod. Catg</th>
						<th width="90">Style Ref</th>
						<th width="70">Job No</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">GMT Color</th>
                        <th width="100">GMT Size</th>
                        <th width="100">Ref No</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
                        <th width="70">Ship/RFI Date</th>
                        <th width="70">ETD/LDD Date</th>
                        <th width="70">Shipment Mode</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qnty(Pcs)</th>
						<th width="90">Order Qnty</th>
						<th width="90">Order Qty<br>[Breakdown qty pcs]</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Gross Order Value</th>
						<th width="100">Net Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>
						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>
						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short Qnty</th>
						<th width="100">Short Value</th>
						<th width="90">Excess Qnty</th>
						<th width="100">Excess Value</th>
                        
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
                        <th width="150">Factory Merchandiser</th>
						<th width="100">File No</th>
						<th width="40">Id</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:<?= $width+20;?>px"  align="left" id="scroll_body">
			<table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_body">
				<?

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;$net_goreder_value_tot=0;

					if($db_type==2)
					{
						$date=date('d-m-Y');
						if($row_group[csf('grouping')]!="")
						{
							$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
						}
						if($row_group[csf('grouping')]=="")
						{
							$grouping="and b.grouping IS NULL";
						}
					}
					
					foreach ($po_wise_arr as $po_id=>$row)
					{
						//echo $lc_id_arr[$row[csf('id')]];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$cons=0;
						$costing_per_pcs=0;
						$yarn_cons_qnty=$job_yarn_cons_arr[$row[('job_no')]]['yarn_cons_qnty'];
						$costing_per=$job_yarn_cons_arr[$row[('job_no')]]['costing_per'];
						//echo $costing_per.'='.$yarn_cons_qnty.',';
						if($costing_per==1) $costing_per_pcs=1*12;
						else if($costing_per==2) $costing_per_pcs=1*1;
						else if($costing_per==3) $costing_per_pcs=2*12;
						else if($costing_per==4) $costing_per_pcs=3*12;
						else if($costing_per==5) $costing_per_pcs=4*12;

							$cons=$yarn_cons_qnty;
							$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[('shiping_status')]==1 && $row[('date_diff_1')]>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($row[('shiping_status')]==1 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($row[('shiping_status')]==1 &&  $row[('date_diff_1')]<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($row[('shiping_status')]==2 && $row[('date_diff_1')]>10 )
						{
							$color="";
						}
						if($row[('shiping_status')]==2 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
						{
							$color="orange";
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_1')]<0)
						{
							$color="red";
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($row[('shiping_status')]==3 && $row[('date_diff_3')]>=0 )
						{
							$color="green";
						}
						if($row[('shiping_status')]==3 &&  $row[('date_diff_3')]<0)
						{
							$color="#2A9FFF";
						}
						if($row[('shiping_status')]==3 && $row[('date_diff_4')]>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($row[('shiping_status')]==3 &&  $row[('date_diff_4')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
                            <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
							<td width="60"><p><? echo $row[('year')]; ?></p></td>
							
                            <td width="100"><?=$row['PRODUCT_DEPT'];?></td>
                            <td width="100"><?=$row['PRO_SUB_DEP'];?></td>
                            <td width="100"><p><?=$row['PRODUCT_CODE'];?></p></td>
                            	
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $sustainability_standard[$row[('sustainability_standard')]];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><?
                            $fab_material=array(1=>"Organic",2=>"BCI");
                             echo $fab_material[$row[('fab_material')]];?></div></p></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $fbooking_order_nature[$row[('order_nature')]];?></div></p></td>
                            <td width="100"><p><div style="word-wrap:break-word; width:100px"><? echo $quality_label[$row[('qlty_label')]];?></div></p></td>

                            <td width="150">
                            <div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
								$fabric_description_arr=array();
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									$fabric_description_arr[]=implode(',',$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]);
									echo $garments_item[$gmts_item_id[$j]];
								}
								$fabric_description=implode(',',$fabric_description_arr);
								?></div>
                             </td>

                            <td width="40" onclick="openmypage_image('requires/shipment_schedule_controller_v3.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
							<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
							
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
                            <td width="100"><p><?= implode(',',$colorArr[$po_id]);?></p></td>
                            <td width="100"><p><?= implode(',',$sizeArr[$po_id]);?></p></td>
                            
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
                            
                            <td width="70" align="center"><?=change_date_format($row['shipment_date']);?></td>
                            <td width="70" align="center"><?=change_date_format($row['TXT_ETD_LDD']);?></td>
                            <td width="70" align="center"><?=$row['SHIP_MODE'];?></td>
                            
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
							<td width="50" bgcolor="<? echo $color; ?>"><div style="word-wrap:break-word; width:50px">
								<?
								if($row[('shiping_status')]==1 || $row[('shiping_status')]==2)
								{
									echo $row[('date_diff_1')];
								}
								if($row[('shiping_status')]==3)
								{
									echo $row[('date_diff_3')];
								}
								?></div></td>
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								?></p></td>
							<td width="90" align="right"><p>
								<?
								echo number_format( $row[('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
								?></p></td>
								<td width="90" align="right"><p>
								<?
								 echo number_format($brk_downPcs_arr[$po_id]['brk_dn_pcs'],0);
								 $brk_order_qntytot=$brk_downPcs_arr[$po_id]['brk_dn_pcs'];
								$gBrkorder_qntytot+=$brk_order_qntytot;
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
								?></p></td>
                            <td width="100" align="right" title="Commission:<?=$commission_costing_sum_arr[$po_id];?>">
							<?
							echo number_format($net_total=($row[('po_total_price')]-$commission_costing_sum_arr[$po_id]),2);
							$net_goreder_value_tot+=$net_total;
							?>
                            </td>
                            <td width="100" align="center"><div style="word-wrap:break-word; width:100px">
                            	<?
								unset($bank_id_arr);
								unset($bank_string_arr);
								if($lc_bank_arr[$po_id] !="")
								{
									$bank_id_arr=array_unique(explode(",",$lc_bank_arr[$po_id]));
									foreach($bank_id_arr as $bank_id)
									{
										$bank_string_arr[]=$bank_name_arr[$bank_id];
									}
									echo implode(",",$bank_string_arr);
								}
								$sc_bank=rtrim($sc_bank_arr[$po_id],',');
								if($sc_bank !="")
								{
									$bank_id_arr=array_unique(explode(",",$sc_bank));
									foreach($bank_id_arr as $bank_id)
									{
										$bank_string_arr[]=$bank_name_arr[$bank_id];
									}
									echo implode(",",$bank_string_arr);
								}
								?>

                            </div>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
								<?
								if($lc_number_arr[$po_id] !="")
								{
									echo "LC: ". $lc_number_arr[$po_id];
									$lc_no = $lc_number_arr[$po_id];
								}
								$sc_number=rtrim($sc_number_arr[$po_id],',');
								$sc_numbers=implode(",",array_unique(explode(",",$sc_number)));
								if($sc_numbers !="")
								{
									echo " SC: ".$sc_numbers;
								}
								?>
								</div></td>
							<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
								<? if($lc_number_arr[$po_id] !="")
									{
										 echo $lc_amendment_arr[$lc_id_arr[$po_id]];

									}
								?>
							</div></td>
							<td width="80" align="center"><p>
							<?
							if($export_lc_arr[$po_id]['file_no']!='') echo $export_lc_arr[$po_id]['file_no'];
							if($export_sc_arr[$po_id]['file_no']!='') echo $export_sc_arr[$po_id]['file_no'];

							?>

							</p></td>
							<td width="80" align="center"><p><?

							if($export_lc_arr[$po_id]['pay_term']!="") echo $export_lc_arr[$po_id]['pay_term'];
							if($export_sc_arr[$po_id]['pay_term']!="") echo $export_sc_arr[$po_id]['pay_term'];

							 ?></p></td>
							<td width="80" align="center"><p><?

							if($export_lc_arr[$po_id]['tenor']!="" ) echo $export_lc_arr[$po_id]['tenor'];
							if($export_sc_arr[$po_id]['tenor']!="" ) echo $export_sc_arr[$po_id]['tenor'];

							 ?></p></td>

							<td width="90" align="right"><p>
							<?
								$ex_factory_del_qty=$ex_factory_qty_arr[$po_id]['del_qty'];
								$ex_factory_return_qty=$ex_factory_qty_arr[$po_id]['return_qty'];
								$ex_factory_qnty=$ex_factory_del_qty-$ex_factory_return_qty;

								//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
								?>
								<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
                                <?

								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($row[('po_quantity')]*$row[('total_set_qnty')]);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qnty;
									$po_value['ontime']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qnty;
									$po_value['after']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
									$po_qnty['yet']+=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
								}
								?></p></td>
							<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									$access_qnty=($ex_factory_qnty-($row[('po_quantity')]*$row[('total_set_qnty')]));

									$short_qnty=($short_qnty>0)?$short_qnty:0;
									$access_qnty=($access_qnty>0)?$access_qnty:0;
									
									
									echo number_format($short_qnty,0);
									$total_short_qnty=$total_short_qnty+$short_qnty;
									$gtotal_short_qnty=$gtotal_short_qnty+$short_qnty;
									
									$total_access_qnty=$total_access_qnty+$access_qnty;
									$gtotal_access_qnty=$gtotal_access_qnty+$access_qnty;
								?></p>
							</td>
							<td width="100" align="right"><p>
								<?
									$short_value=$short_qnty*$row[('unit_price')];
									$access_value=$access_qnty*$row[('unit_price')];
									
									echo number_format($short_value,2);
									$total_short_value=$total_short_value+$short_value;
									$gtotal_short_value=$gtotal_short_value+$short_value;
									
									$total_access_value=$total_access_value+$access_value;
									$gtotal_access_value=$gtotal_access_value+$access_value;
									
								?></p>
							</td>
                            
                            <td width="90" align="right"><?=$access_qnty;?></td>
                            <td width="100" align="right"><?=$access_value;?></td>
                            
                            
							<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
                            
							<td width="150"><?= $row['factory_marchant'];?></td>
                            
                            
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
							<td width="40"><p><? echo $po_id; ?></p></td>
							<td><p><? echo $row[('details_remarks')]; ?></p></td>
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td align="center" >  Total: </td>
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
                        <td></td>

                        <td></td>
						<td></td>
                        <td></td>
                        <td></td>
                        
						<td></td>
						<td></td>
                        
                        <td></td>
						<td></td>
						<td></td>
						<td></td>
                        
						<td></td>
						<td></td>
						<td></td>
                        
                        
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gBrkorder_qntytot,0); ?></td>
						<td></td>
						<td></td>

						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
                        <td align="right"><? echo number_format($net_goreder_value_tot,2); ?></td>
                        <td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_qnty,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_value,0); ?></td>
                        
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_saccess_qnty,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_access_value,0); ?></td>
                        
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></th>
					 </tr>
				<?
				
				?>
				</table>
				</div>
				<table style="display: none;" width="<?= $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="60"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>

                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>

							<th width="150"></th>
							<th width="40"></th>
							<th width="70"></th>
							<th width="90"></th>
							<th width="70" ></th>
							<th width="110"></th>
                            <th width="100"></th>
                            
                            <th width="100"></th>
                            <th width="100"></th>
                            
                            <th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
                            
                            <th width="70"></th>
                            <th width="70"></th>
                            <th width="70"></th>
                            
                            
                            
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>

							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
                             <th width="100">ccc</th>
                             <th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_qnty,0); ?></th>
							<th width="100" id="value_total_short_access_value" align="right"><? echo number_format($total_short_value,0); ?></th>
                            
							<th width="90" id="total_access_qnty" align="right"><? echo number_format($total_access_qnty,0); ?></th>
							<th width="100" id="value_total_access_value" align="right"><? echo number_format($total_access_value,0); ?></th>
                            
                            
                            
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100" ></th>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="40"></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
				<?
		
	}
	elseif($search_by==2)
	{
		$sql_data="SELECT a.PRODUCT_CODE,a.job_no_prefix_num, a.job_no,b.job_id, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.season_buyer_wise,a.brand_id, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.id=b.job_id and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $sustaina_cond $fab_mat_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.PRODUCT_CODE,a.season_buyer_wise,a.brand_id, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by,b.job_id order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  	// echo  $sql_data; die;
		$data_array=sql_select( $sql_data);
		$all_po_id="";
		foreach($data_array as $row) //
		{
			$style_wise_arr[$row[csf('job_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$style_wise_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$style_wise_arr[$row[csf('job_id')]]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_id')]]['company_name']=$row[csf('company_name')];
			$style_wise_arr[$row[csf('job_id')]]['buyer_name']=$row[csf('buyer_name')];
			$style_wise_arr[$row[csf('job_id')]]['brand_name']=$row[csf('brand_id')];
			$style_wise_arr[$row[csf('job_id')]]['agent_name']=$row[csf('agent_name')];
			$style_wise_arr[$row[csf('job_id')]]['job_quantity']=$row[csf('job_quantity')];
			$style_wise_arr[$row[csf('job_id')]]['product_category']=$row[csf('product_category')];
			$style_wise_arr[$row[csf('job_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$style_wise_arr[$row[csf('job_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
			$style_wise_arr[$row[csf('job_id')]]['order_uom']=$row[csf('order_uom')];
			$style_wise_arr[$row[csf('job_id')]]['team_leader']=$row[csf('team_leader')];
			
			$style_wise_arr[$row[csf('job_id')]]['qlty_label']=$row[csf('qlty_label')];
			$style_wise_arr[$row[csf('job_id')]]['sustainability_standard']=$row[csf('sustainability_standard')];
			$style_wise_arr[$row[csf('job_id')]]['fab_material']=$row[csf('fab_material')];
			$style_wise_arr[$row[csf('job_id')]]['order_nature']=$row[csf('quality_level')];

			$style_wise_arr[$row[csf('job_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$style_wise_arr[$row[csf('job_id')]]['season']=$row[csf('season')];
			$style_wise_arr[$row[csf('job_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
			$style_wise_arr[$row[csf('job_id')]]['id']=$row[csf('id')];
			$style_wise_arr[$row[csf('job_id')]]['shiping_status']=$row[csf('shiping_status')];
			$style_wise_arr[$row[csf('job_id')]]['po_number']=$row[csf('po_number')];
			//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
			$style_wise_arr[$row[csf('job_id')]]['inserted_by']=$row[csf('inserted_by')];
			$style_wise_arr[$row[csf('job_id')]]['po_quantity']+=$row[csf('po_quantity')];
			$style_wise_arr[$row[csf('job_id')]]['shipment_date']=$row[csf('shipment_date')];
			$style_wise_arr[$row[csf('job_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$style_wise_arr[$row[csf('job_id')]]['po_received_date']=$row[csf('po_received_date')];
			$style_wise_arr[$row[csf('job_id')]]['unit_price']=$row[csf('unit_price')];
			$style_wise_arr[$row[csf('job_id')]]['po_total_price']+=$row[csf('po_total_price')];
			$style_wise_arr[$row[csf('job_id')]]['details_remarks']=$row[csf('details_remarks')];
			
			$style_wise_arr[$row[csf('job_id')]]['file_no']=$row[csf('file_no')];
			$style_wise_arr[$row[csf('job_id')]]['grouping']=$row[csf('grouping')];
			$style_wise_arr[$row[csf('job_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
			$style_wise_arr[$row[csf('job_id')]]['ex_factory_date']+=$row[csf('ex_factory_date')];
			$style_wise_arr[$row[csf('job_id')]]['date_diff_1']=$row[csf('date_diff_1')];
			$style_wise_arr[$row[csf('job_id')]]['date_diff_2']=$row[csf('date_diff_2')];
			$style_wise_arr[$row[csf('job_id')]]['date_diff_3']=$row[csf('date_diff_3')];
			$style_wise_arr[$row[csf('job_id')]]['date_diff_4']=$row[csf('date_diff_4')];
			$style_wise_arr[$row[csf('job_id')]]['year']=$row[csf('year')];
			$style_wise_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$style_wise_arr[$row[csf('job_id')]]['TXT_ETD_LDD']=$row[csf('TXT_ETD_LDD')];
			$style_wise_arr[$row[csf('job_id')]]['SHIP_MODE']=$shipment_mode[$row[csf('SHIP_MODE')]];
			
			$style_wise_arr[$row[csf('job_id')]]['PRODUCT_DEPT']=$product_dept[$row[csf('PRODUCT_DEPT')]];
			$style_wise_arr[$row[csf('job_id')]]['PRO_SUB_DEP']=$sub_dep_arr[$row[csf('PRO_SUB_DEP')]];
			$style_wise_arr[$row[csf('job_id')]]['PRODUCT_CODE']=$row[csf('PRODUCT_CODE')];
			$style_wise_arr[$row[csf('job_id')]]['factory_marchant']=$factory_mar_arr[$row[csf('factory_marchant')]];
			
			$style_wise_arr[$row[csf('job_id')]]['is_confirmed']=$row[csf('is_confirmed')];
			// $style_wise_arr[$row[csf('job_id')]]['total_set_qnty']+=$row[csf('total_set_qnty')];

			//Company Buyer Wise
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
			
			$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$company_buyer_by_po_arr[$row[csf('job_id')]]=$row[csf('company_name')].'**'.$row[csf('buyer_name')];
			$style_wise_po_arr[$row[csf('job_id')]][$row[csf('po_number')]]=$row[csf('po_number')];
			
			
		}
		$poSizeSql="select a.STYLE_REF_NO,c.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,c.SIZE_NUMBER_ID,c.job_id from wo_po_details_master a ,wo_po_color_size_breakdown c where a.id=c.job_id and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 ".where_con_using_array($all_po_id_arr,0,'c.PO_BREAK_DOWN_ID')."";
		// echo $poSizeSql; die;
		$poSizeSqlRes=sql_select($poSizeSql);
		foreach($poSizeSqlRes as $row)
		{
			$brk_downPcs_arr[$row['job_id']]['brk_dn_pcs']+=$row['ORDER_QUANTITY'];
		}
		
		
				?>


				<h3 style="width:<?= $width;?>px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel[Style Wise]</h3>
				<div id="content_report_panel">

					<table width="<?= $width;?>" id="table_header_1" border="1" class="rpt_table" rules="all">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="70" >Company</th>
								<th width="50">Buyer</th>
								<th width="50">Brand</th>
								<th width="100">Season</th>
								<th width="60">Year</th>
								<th width="100">Prod Dept</th>
								<th width="100">Sub Dept</th>
								<th width="100">Class</th>

								<th width="100">Sustaina. Standard</th>
								<th width="100">Fab. Material</th>
								<th width="100">Order Nature</th>
								<th width="100">Quality Label</th>

								<th width="150">Item</th>
								<th width="40">Img</th>
								<th width="70">Prod. Catg</th>
								<th width="90">Style Ref</th>
								<th width="70">Job No</th>
								<th width="110">PO No</th>
								<th width="100">Actual PO No</th>
								<th width="100">GMT Color</th>
								<th width="100">GMT Size</th>
								<th width="100">Ref No</th>
								<th width="50">Agent</th>
								<th width="70">Order Status</th>
								<th width="70">Ship/RFI Date</th>
								<th width="70">ETD/LDD Date</th>
								<th width="70">Shipment Mode</th>
								<th width="200">Fab. Description</th>
								<th width="70">Ship Date</th>
								<th width="70">PO Rec. Date</th>
								<th width="50">Days in Hand</th>
								<th width="90">Order Qnty(Pcs)</th>
								<th width="90">Order Qnty</th>
								<th width="90">Order Qty<br>[Breakdown qty pcs]</th>
								<th width="40">Uom</th>
								<th width="50">Per Unit Price</th>
								<th width="100">Gross Order Value</th>
								<th width="100">Net Order Value</th>
								<th width="100">Lien Bank</th>
								<th width="100">LC/SC No</th>
								<th width="90">Ex. LC Amendment No(Last)</th>
								<th width="80"> Int.File No </th>
								<th width="80">Pay Term </th>
								<th width="80">Tenor </th>
								<th width="90">Ex-Fac Qnty </th>
								<th width="70">Last Ex-Fac Date</th>
								<th width="90">Short Qnty</th>
								<th width="100">Short Value</th>
								<th width="90">Excess Qnty</th>
								<th width="100">Excess Value</th>
								
								<th width="100">Yarn Req</th>
								<th width="100">CM </th>
								<th width="100" >Shipping Status</th>
								<th width="150"> Team Member</th>
								<th width="150">Team Name</th>
								<th width="150">Factory Merchandiser</th>
								<th width="100">File No</th>
								<th width="40">Id</th>
								<th>Remarks</th>
							</tr>
						</thead>
					</table>
					<div style=" max-height:400px; overflow-y:scroll; width:<?= $width+20;?>px"  align="left" id="scroll_body">
					<table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_body">
						<?
						

						$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
						$brk_gorder_qntytot=0;
							$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;$net_goreder_value_tot=0;

							if($db_type==2)
							{
								$date=date('d-m-Y');
								if($row_group[csf('grouping')]!="")
								{
									$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
								}
								if($row_group[csf('grouping')]=="")
								{
									$grouping="and b.grouping IS NULL";
								}
							}
							
							foreach ($style_wise_arr as $job_id=>$row)
							{
								//echo $lc_id_arr[$row[csf('id')]];
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$cons=0;
								$costing_per_pcs=0;
								$yarn_cons_qnty=$job_yarn_cons_arr[$row[('job_no')]]['yarn_cons_qnty'];
								$costing_per=$job_yarn_cons_arr[$row[('job_no')]]['costing_per'];
								//echo $costing_per.'='.$yarn_cons_qnty.',';
								if($costing_per==1) $costing_per_pcs=1*12;
								else if($costing_per==2) $costing_per_pcs=1*1;
								else if($costing_per==3) $costing_per_pcs=2*12;
								else if($costing_per==4) $costing_per_pcs=3*12;
								else if($costing_per==5) $costing_per_pcs=4*12;

									$cons=$yarn_cons_qnty;
									$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
								//--Calculation Yarn Required-------
								//--Color Determination-------------
								//==================================
								$shipment_performance=0;
								if($row[('shiping_status')]==1 && $row[('date_diff_1')]>10 )
								{
									$color="";
									$number_of_order['yet']+=1;
									$shipment_performance=0;
								}

								if($row[('shiping_status')]==1 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
								{
									$color="orange";
									$number_of_order['yet']+=1;
									$shipment_performance=0;
								}
								if($row[('shiping_status')]==1 &&  $row[('date_diff_1')]<0)
								{
									$color="red";
									$number_of_order['yet']+=1;
									$shipment_performance=0;
								}
										//=====================================
								if($row[('shiping_status')]==2 && $row[('date_diff_1')]>10 )
								{
									$color="";
								}
								if($row[('shiping_status')]==2 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
								{
									$color="orange";
								}
								if($row[('shiping_status')]==2 &&  $row[('date_diff_1')]<0)
								{
									$color="red";
								}
								if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]>=0)
								{
									$number_of_order['ontime']+=1;
									$shipment_performance=1;
								}
								if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]<0)
								{
									$number_of_order['after']+=1;
									$shipment_performance=2;
								}
								//========================================
								if($row[('shiping_status')]==3 && $row[('date_diff_3')]>=0 )
								{
									$color="green";
								}
								if($row[('shiping_status')]==3 &&  $row[('date_diff_3')]<0)
								{
									$color="#2A9FFF";
								}
								if($row[('shiping_status')]==3 && $row[('date_diff_4')]>=0 )
								{
									$number_of_order['ontime']+=1;
									$shipment_performance=1;
								}
								if($row[('shiping_status')]==3 &&  $row[('date_diff_4')]<0)
								{
									$number_of_order['after']+=1;
									$shipment_performance=2;
								}
								//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
								?>
								<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
									<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
									<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
									<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
									<td width="60"><p><? echo $row[('year')]; ?></p></td>
									
									<td width="100"><?=$row['PRODUCT_DEPT'];?></td>
									<td width="100"><?=$row['PRO_SUB_DEP'];?></td>
									<td width="100"><p><?=$row['PRODUCT_CODE'];?></p></td>
										
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $sustainability_standard[$row[('sustainability_standard')]];?></div></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><?
									$fab_material=array(1=>"Organic",2=>"BCI");
									echo $fab_material[$row[('fab_material')]];?></div></p></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $fbooking_order_nature[$row[('order_nature')]];?></div></p></td>
									<td width="100"><p><div style="word-wrap:break-word; width:100px"><? echo $quality_label[$row[('qlty_label')]];?></div></p></td>

									<td width="150">
									<div style="word-wrap:break-word; width:150px">
									<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
										$fabric_description_arr=array();
										for($j=0; $j<=count($gmts_item_id); $j++)
										{
											$fabric_description_arr[]=implode(',',$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]);
											echo $garments_item[$gmts_item_id[$j]];
										}
										$fabric_description=implode(',',$fabric_description_arr);
										?></div>
									</td>

									<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller_v3.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
									<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
									<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
									<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
									
									<td width="110"><div style="word-wrap:break-word; width:110px"><? echo implode(",",$style_wise_po_arr[$job_id]);?></div></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
									<td width="100"><p><?= implode(',',$colorArr[$po_id]);?></p></td>
									<td width="100"><p><?= implode(',',$sizeArr[$po_id]);?></p></td>
									
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
									<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
									<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
									
									<td width="70" align="center"><?=change_date_format($row['shipment_date']);?></td>
									<td width="70" align="center"><?=change_date_format($row['TXT_ETD_LDD']);?></td>
									<td width="70" align="center"><?=$row['SHIP_MODE'];?></td>
									
									<td width="200"><div style="word-wrap:break-word; width:200px">
										<?
										$fabric_des="";
										$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
										echo $fabric_des;//$fabric_des;?></div></td>
									<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></div></td>
									<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
									<td width="50" bgcolor="<? echo $color; ?>"><div style="word-wrap:break-word; width:50px">
										<?
										if($row[('shiping_status')]==1 || $row[('shiping_status')]==2)
										{
											echo $row[('date_diff_1')];
										}
										if($row[('shiping_status')]==3)
										{
											echo $row[('date_diff_3')];
										}
										?></div></td>
									<td width="90" align="right"><p>
										<?
										echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
										$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
										$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
										?></p></td>
									<td width="90" align="right"><p>
										<?
										echo number_format( $row[('po_quantity')],0);
										$order_qntytot=$order_qntytot+$row[('po_quantity')];
										$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
										?></p></td>

										<td width="90" align="right"><p>
										<?
										 echo number_format( $brk_downPcs_arr[$job_id]['brk_dn_pcs'],0);
										//$order_qntytot=$order_qntytot+$row[('po_quantity')];
										$brk_gorder_qntytot+=$brk_downPcs_arr[$job_id]['brk_dn_pcs'];
										?></p></td>
									<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
									<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
									<td width="100" align="right"><p>
										<?
											echo number_format($row[('po_total_price')],2);
											$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
											$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
										?></p></td>
									<td width="100" align="right" title="Commission:<?=$commission_costing_sum_arr[$po_id];?>">
									<?
									echo number_format($net_total=($row[('po_total_price')]-$commission_costing_sum_arr[$po_id]),2);
									$net_goreder_value_tot+=$net_total;
									?>
									</td>
									<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
										<?
										unset($bank_id_arr);
										unset($bank_string_arr);
										if($lc_bank_arr[$po_id] !="")
										{
											$bank_id_arr=array_unique(explode(",",$lc_bank_arr[$po_id]));
											foreach($bank_id_arr as $bank_id)
											{
												$bank_string_arr[]=$bank_name_arr[$bank_id];
											}
											echo implode(",",$bank_string_arr);
										}
										$sc_bank=rtrim($sc_bank_arr[$po_id],',');
										if($sc_bank !="")
										{
											$bank_id_arr=array_unique(explode(",",$sc_bank));
											foreach($bank_id_arr as $bank_id)
											{
												$bank_string_arr[]=$bank_name_arr[$bank_id];
											}
											echo implode(",",$bank_string_arr);
										}
										?>

									</div>
									<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
										<?
										if($lc_number_arr[$po_id] !="")
										{
											echo "LC: ". $lc_number_arr[$po_id];
											$lc_no = $lc_number_arr[$po_id];
										}
										$sc_number=rtrim($sc_number_arr[$po_id],',');
										$sc_numbers=implode(",",array_unique(explode(",",$sc_number)));
										if($sc_numbers !="")
										{
											echo " SC: ".$sc_numbers;
										}
										?>
										</div></td>
									<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
										<? if($lc_number_arr[$po_id] !="")
											{
												echo $lc_amendment_arr[$lc_id_arr[$po_id]];

											}
										?>
									</div></td>
									<td width="80" align="center"><p>
									<?
									if($export_lc_arr[$po_id]['file_no']!='') echo $export_lc_arr[$po_id]['file_no'];
									if($export_sc_arr[$po_id]['file_no']!='') echo $export_sc_arr[$po_id]['file_no'];

									?>

									</p></td>
									<td width="80" align="center"><p><?

									if($export_lc_arr[$po_id]['pay_term']!="") echo $export_lc_arr[$po_id]['pay_term'];
									if($export_sc_arr[$po_id]['pay_term']!="") echo $export_sc_arr[$po_id]['pay_term'];

									?></p></td>
									<td width="80" align="center"><p><?

									if($export_lc_arr[$po_id]['tenor']!="" ) echo $export_lc_arr[$po_id]['tenor'];
									if($export_sc_arr[$po_id]['tenor']!="" ) echo $export_sc_arr[$po_id]['tenor'];

									?></p></td>

									<td width="90" align="right"><p>
									<?
										$ex_factory_del_qty=$ex_factory_qty_arr[$po_id]['del_qty'];
										$ex_factory_return_qty=$ex_factory_qty_arr[$po_id]['return_qty'];
										$ex_factory_qnty=$ex_factory_del_qty-$ex_factory_return_qty;

										//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
										?>
										<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
										<?

										$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
										$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
										if ($shipment_performance==0)
										{
											$po_qnty['yet']+=($row[('po_quantity')]*$row[('total_set_qnty')]);
											$po_value['yet']+=100;
										}
										else if ($shipment_performance==1)
										{
											$po_qnty['ontime']+=$ex_factory_qnty;
											$po_value['ontime']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
											$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
										}
										else if ($shipment_performance==2)
										{
											$po_qnty['after']+=$ex_factory_qnty;
											$po_value['after']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
											$po_qnty['yet']+=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
										}
										?></p></td>
									<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
									<td  width="90" align="right"><p>
										<?
											$short_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
											$access_qnty=($ex_factory_qnty-($row[('po_quantity')]*$row[('total_set_qnty')]));

											$short_qnty=($short_qnty>0)?$short_qnty:0;
											$access_qnty=($access_qnty>0)?$access_qnty:0;
											
											
											echo number_format($short_qnty,0);
											$total_short_qnty=$total_short_qnty+$short_qnty;
											$gtotal_short_qnty=$gtotal_short_qnty+$short_qnty;
											
											$total_access_qnty=$total_access_qnty+$access_qnty;
											$gtotal_access_qnty=$gtotal_access_qnty+$access_qnty;
										?></p>
									</td>
									<td width="100" align="right"><p>
										<?
											$short_value=$short_qnty*$row[('unit_price')];
											$access_value=$access_qnty*$row[('unit_price')];
											
											echo number_format($short_value,2);
											$total_short_value=$total_short_value+$short_value;
											$gtotal_short_value=$gtotal_short_value+$short_value;
											
											$total_access_value=$total_access_value+$access_value;
											$gtotal_access_value=$gtotal_access_value+$access_value;
											
										?></p>
									</td>
									
									<td width="90" align="right"><?=$access_qnty;?></td>
									<td width="100" align="right"><?=$access_value;?></td>
									
									
									<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
										<?
											echo number_format($yarn_req_for_po,2);
											$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
											$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
										?></p>
									</td>
									<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
									<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
									<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
									
									<td width="150"><?= $row['factory_marchant'];?></td>
									
									
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
									<td width="40"><p><? echo $po_id; ?></p></td>
									<td><p><? echo $row[('details_remarks')]; ?></p></td>
								</tr>
							<?
							$i++;
							}
							?>
							<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
								<td align="center" >  Total: </td>
								<td></td>
								<td></td>
								<td></td>
								
								<td></td>
								<td></td>
								<td></td>
								
								<td></td>
								<td></td>
								<td></td>

								<td></td>
								<td></td>
								<td></td>
								<td></td>
								
								<td></td>
								<td></td>
								
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								
								<td></td>
								<td></td>
								<td></td>
								
								
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($brk_gorder_qntytot,0); ?></td>
								<td></td>
								<td></td>

								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
								<td align="right"><? echo number_format($net_goreder_value_tot,2); ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
								<td></td>
								<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_qnty,0); ?></td>
								<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_value,0); ?></td>
								
								<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_saccess_qnty,0); ?></td>
								<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_access_value,0); ?></td>
								
								<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></th>
							</tr>
						<?
						
						?>
						</table>
						</div>
						<table style="display: none;" width="<?= $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
							<tfoot>
								<tr>
									<th width="50"></th>
									<th width="70"></th>
									<th width="50"></th>
									<th width="50"></th>
									<th width="100"></th>
									<th width="60"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>

									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>

									<th width="150"></th>
									<th width="40"></th>
									<th width="70"></th>
									<th width="90"></th>
									<th width="70" ></th>
									<th width="110"></th>
									<th width="100"></th>
									
									<th width="100"></th>
									<th width="100"></th>
									
									<th width="100"></th>
									<th width="50"></th>
									<th width="70"></th>
									
									<th width="70"></th>
									<th width="70"></th>
									<th width="70"></th>
									
									
									
									<th width="200"></th>
									<th width="70"></th>
									<th width="70"></th>
									<th width="50"></th>
									<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
									<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
									<th width="40"></th>
									<th width="50"></th>

									<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
									<th width="100">ccc</th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="90"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
									<th width="70"></th>
									<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_qnty,0); ?></th>
									<th width="100" id="value_total_short_access_value" align="right"><? echo number_format($total_short_value,0); ?></th>
									
									<th width="90" id="total_access_qnty" align="right"><? echo number_format($total_access_qnty,0); ?></th>
									<th width="100" id="value_total_access_value" align="right"><? echo number_format($total_access_value,0); ?></th>
									
									
									
									<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
									<th width="100"></th>
									<th width="100" ></th>
									<th width="150"> </th>
									<th width="150"></th>
									<th width="150"></th>
									<th width="100"></th>
									<th width="40"></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
						<?
	}
	else
	{
			$sql_data="SELECT a.PRODUCT_CODE,a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.season_buyer_wise,a.brand_id, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $sustaina_cond $fab_mat_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.qlty_label, a.sustainability_standard, a.fab_material, a.quality_level, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.PRODUCT_CODE,a.season_buyer_wise,a.brand_id, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  //echo  $sql_data; die;
		$data_array=sql_select( $sql_data);
		$all_po_id="";
		foreach($data_array as $row) //
		{
			$job_wise_arr[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$job_wise_arr[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
			$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_arr[$row[csf('job_no')]]['company_name']=$row[csf('company_name')];
			$job_wise_arr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$job_wise_arr[$row[csf('job_no')]]['brand_name']=$row[csf('brand_id')];
			$job_wise_arr[$row[csf('job_no')]]['agent_name']=$row[csf('agent_name')];
			$job_wise_arr[$row[csf('job_no')]]['job_quantity']=$row[csf('job_quantity')];
			$job_wise_arr[$row[csf('job_no')]]['product_category']=$row[csf('product_category')];
			$job_wise_arr[$row[csf('job_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$job_wise_arr[$row[csf('job_no')]]['total_set_qnty']=$row[csf('total_set_qnty')];
			$job_wise_arr[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
			$job_wise_arr[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
			
			$job_wise_arr[$row[csf('job_no')]]['qlty_label']=$row[csf('qlty_label')];
			$job_wise_arr[$row[csf('job_no')]]['sustainability_standard']=$row[csf('sustainability_standard')];
			$job_wise_arr[$row[csf('job_no')]]['fab_material']=$row[csf('fab_material')];
			$job_wise_arr[$row[csf('job_no')]]['order_nature']=$row[csf('quality_level')];

			$job_wise_arr[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$job_wise_arr[$row[csf('job_no')]]['season']=$row[csf('season')];
			$job_wise_arr[$row[csf('job_no')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
			$job_wise_arr[$row[csf('job_no')]]['id']=$row[csf('id')];
			$job_wise_arr[$row[csf('job_no')]]['shiping_status']=$row[csf('shiping_status')];
			$job_wise_arr[$row[csf('job_no')]]['po_number']=$row[csf('po_number')];
			//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
			$job_wise_arr[$row[csf('job_no')]]['inserted_by']=$row[csf('inserted_by')];
			$job_wise_arr[$row[csf('job_no')]]['po_quantity']=$row[csf('po_quantity')];
			$job_wise_arr[$row[csf('job_no')]]['shipment_date']=$row[csf('shipment_date')];
			$job_wise_arr[$row[csf('job_no')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$job_wise_arr[$row[csf('job_no')]]['po_received_date']=$row[csf('po_received_date')];
			$job_wise_arr[$row[csf('job_no')]]['unit_price']=$row[csf('unit_price')];
			$job_wise_arr[$row[csf('job_no')]]['po_total_price']+=$row[csf('po_total_price')];
			$job_wise_arr[$row[csf('job_no')]]['details_remarks']=$row[csf('details_remarks')];
			
			$job_wise_arr[$row[csf('job_no')]]['file_no']=$row[csf('file_no')];
			$job_wise_arr[$row[csf('job_no')]]['grouping']=$row[csf('grouping')];
			$job_wise_arr[$row[csf('job_no')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
			$job_wise_arr[$row[csf('job_no')]]['ex_factory_date']+=$row[csf('ex_factory_date')];
			$job_wise_arr[$row[csf('job_no')]]['date_diff_1']=$row[csf('date_diff_1')];
			$job_wise_arr[$row[csf('job_no')]]['date_diff_2']=$row[csf('date_diff_2')];
			$job_wise_arr[$row[csf('job_no')]]['date_diff_3']=$row[csf('date_diff_3')];
			$job_wise_arr[$row[csf('job_no')]]['date_diff_4']=$row[csf('date_diff_4')];
			$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_wise_arr[$row[csf('job_no')]]['TXT_ETD_LDD']=$row[csf('TXT_ETD_LDD')];
			$job_wise_arr[$row[csf('job_no')]]['SHIP_MODE']=$shipment_mode[$row[csf('SHIP_MODE')]];
			
			$job_wise_arr[$row[csf('job_no')]]['PRODUCT_DEPT']=$product_dept[$row[csf('PRODUCT_DEPT')]];
			$job_wise_arr[$row[csf('job_no')]]['PRO_SUB_DEP']=$sub_dep_arr[$row[csf('PRO_SUB_DEP')]];
			$job_wise_arr[$row[csf('job_no')]]['PRODUCT_CODE']=$row[csf('PRODUCT_CODE')];
			$job_wise_arr[$row[csf('job_no')]]['factory_marchant']=$factory_mar_arr[$row[csf('factory_marchant')]];
			
			$job_wise_arr[$row[csf('job_no')]]['is_confirmed']=$row[csf('is_confirmed')];
			$job_wise_arr[$row[csf('job_no')]]['total_set_qnty']+=$row[csf('total_set_qnty')];

			//Company Buyer Wise
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
			
			$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$company_buyer_by_po_arr[$row[csf('job_no')]]=$row[csf('company_name')].'**'.$row[csf('buyer_name')];
			$job_wise_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];
			
			
		}

		$poSizeSql="select a.JOB_NO,c.PO_BREAK_DOWN_ID,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,c.SIZE_NUMBER_ID from wo_po_details_master a,wo_po_color_size_breakdown c where a.id=c.job_id and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 ".where_con_using_array($all_po_id_arr,0,'c.PO_BREAK_DOWN_ID')."";
		$poSizeSqlRes=sql_select($poSizeSql);
		foreach($poSizeSqlRes as $row)
		{
			 
			$brk_downPcs_arr[$row['JOB_NO']]['brk_dn_pcs']+=$row['ORDER_QUANTITY'];
		}
		


		?>
	
	
			<h3 style="width:<?= $width;?>px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel[Job Wise]</h3>
			<div id="content_report_panel">
				<table width="<?= $width;?>" id="table_header_1" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="70" >Company</th>
							<th width="50">Buyer</th>
							<th width="50">Brand</th>
							<th width="100">Season</th>
							<th width="60">Year</th>
							<th width="100">Prod Dept</th>
							<th width="100">Sub Dept</th>
							<th width="100">Class</th>
	
							<th width="100">Sustaina. Standard</th>
							<th width="100">Fab. Material</th>
							<th width="100">Order Nature</th>
							<th width="100">Quality Label</th>
	
							<th width="150">Item</th>
							<th width="40">Img</th>
							<th width="70">Prod. Catg</th>
							<th width="90">Style Ref</th>
							<th width="70">Job No</th>
							<th width="110">PO No</th>
							<th width="100">Actual PO No</th>
							<th width="100">GMT Color</th>
							<th width="100">GMT Size</th>
							<th width="100">Ref No</th>
							<th width="50">Agent</th>
							<th width="70">Order Status</th>
							<th width="70">Ship/RFI Date</th>
							<th width="70">ETD/LDD Date</th>
							<th width="70">Shipment Mode</th>
							<th width="200">Fab. Description</th>
							<th width="70">Ship Date</th>
							<th width="70">PO Rec. Date</th>
							<th width="50">Days in Hand</th>
							<th width="90">Order Qnty(Pcs)</th>
							<th width="90">Order Qnty</th>
							<th width="90">Order Qty<br>[Breakdown qty pcs]</th>
							<th width="40">Uom</th>
							<th width="50">Per Unit Price</th>
							<th width="100">Gross Order Value</th>
							<th width="100">Net Order Value</th>
							<th width="100">Lien Bank</th>
							<th width="100">LC/SC No</th>
							<th width="90">Ex. LC Amendment No(Last)</th>
							<th width="80"> Int.File No </th>
							<th width="80">Pay Term </th>
							<th width="80">Tenor </th>
							<th width="90">Ex-Fac Qnty </th>
							<th width="70">Last Ex-Fac Date</th>
							<th width="90">Short Qnty</th>
							<th width="100">Short Value</th>
							<th width="90">Excess Qnty</th>
							<th width="100">Excess Value</th>
							
							<th width="100">Yarn Req</th>
							<th width="100">CM </th>
							<th width="100" >Shipping Status</th>
							<th width="150"> Team Member</th>
							<th width="150">Team Name</th>
							<th width="150">Factory Merchandiser</th>
							<th width="100">File No</th>
							<th width="40">Id</th>
							<th>Remarks</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:400px; overflow-y:scroll; width:<?= $width+20;?>px"  align="left" id="scroll_body">
				<table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_body">
					<?
					
	
					$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;$brk_gorder_qntytot=0;
					
						$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;$net_goreder_value_tot=0;
	
						if($db_type==2)
						{
							$date=date('d-m-Y');
							if($row_group[csf('grouping')]!="")
							{
								$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
							}
							if($row_group[csf('grouping')]=="")
							{
								$grouping="and b.grouping IS NULL";
							}
						}
						
						foreach ($job_wise_arr as $job_id=>$row)
						{
							//echo $lc_id_arr[$row[csf('id')]];
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	
							$cons=0;
							$costing_per_pcs=0;
							$yarn_cons_qnty=$job_yarn_cons_arr[$row[('job_no')]]['yarn_cons_qnty'];
							$costing_per=$job_yarn_cons_arr[$row[('job_no')]]['costing_per'];
							//echo $costing_per.'='.$yarn_cons_qnty.',';
							if($costing_per==1) $costing_per_pcs=1*12;
							else if($costing_per==2) $costing_per_pcs=1*1;
							else if($costing_per==3) $costing_per_pcs=2*12;
							else if($costing_per==4) $costing_per_pcs=3*12;
							else if($costing_per==5) $costing_per_pcs=4*12;
	
								$cons=$yarn_cons_qnty;
								$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
							//--Calculation Yarn Required-------
							//--Color Determination-------------
							//==================================
							$shipment_performance=0;
							if($row[('shiping_status')]==1 && $row[('date_diff_1')]>10 )
							{
								$color="";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
	
							if($row[('shiping_status')]==1 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
							{
								$color="orange";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
							if($row[('shiping_status')]==1 &&  $row[('date_diff_1')]<0)
							{
								$color="red";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
									//=====================================
							if($row[('shiping_status')]==2 && $row[('date_diff_1')]>10 )
							{
								$color="";
							}
							if($row[('shiping_status')]==2 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
							{
								$color="orange";
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_1')]<0)
							{
								$color="red";
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]>=0)
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							//========================================
							if($row[('shiping_status')]==3 && $row[('date_diff_3')]>=0 )
							{
								$color="green";
							}
							if($row[('shiping_status')]==3 &&  $row[('date_diff_3')]<0)
							{
								$color="#2A9FFF";
							}
							if($row[('shiping_status')]==3 && $row[('date_diff_4')]>=0 )
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($row[('shiping_status')]==3 &&  $row[('date_diff_4')]<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
								<td width="60"><p><? echo $row[('year')]; ?></p></td>
								
								<td width="100"><?=$row['PRODUCT_DEPT'];?></td>
								<td width="100"><?=$row['PRO_SUB_DEP'];?></td>
								<td width="100"><p><?=$row['PRODUCT_CODE'];?></p></td>
									
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $sustainability_standard[$row[('sustainability_standard')]];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><?
								$fab_material=array(1=>"Organic",2=>"BCI");
								 echo $fab_material[$row[('fab_material')]];?></div></p></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $fbooking_order_nature[$row[('order_nature')]];?></div></p></td>
								<td width="100"><p><div style="word-wrap:break-word; width:100px"><? echo $quality_label[$row[('qlty_label')]];?></div></p></td>
	
								<td width="150">
								<div style="word-wrap:break-word; width:150px">
								<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
									$fabric_description_arr=array();
									for($j=0; $j<=count($gmts_item_id); $j++)
									{
										$fabric_description_arr[]=implode(',',$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]);
										echo $garments_item[$gmts_item_id[$j]];
									}
									$fabric_description=implode(',',$fabric_description_arr);
									?></div>
								 </td>
	
								<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller_v3.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
								<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
								<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
								
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo implode(",",$job_wise_po_arr[$row[('job_no')]]);?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
								<td width="100"><p><?= implode(',',$colorArr[$po_id]);?></p></td>
								<td width="100"><p><?= implode(',',$sizeArr[$po_id]);?></p></td>
								
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
								
								<td width="70" align="center"><?=change_date_format($row['shipment_date']);?></td>
								<td width="70" align="center"><?=change_date_format($row['TXT_ETD_LDD']);?></td>
								<td width="70" align="center"><?=$row['SHIP_MODE'];?></td>
								
								<td width="200"><div style="word-wrap:break-word; width:200px">
									<?
									$fabric_des="";
									$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
									echo $fabric_des;//$fabric_des;?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
								<td width="50" bgcolor="<? echo $color; ?>"><div style="word-wrap:break-word; width:50px">
									<?
									if($row[('shiping_status')]==1 || $row[('shiping_status')]==2)
									{
										echo $row[('date_diff_1')];
									}
									if($row[('shiping_status')]==3)
									{
										echo $row[('date_diff_3')];
									}
									?></div></td>
								<td width="90" align="right"><p>
									<?
									echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
									$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									?></p></td>
								<td width="90" align="right"><p>
									<?
									echo number_format( $row[('po_quantity')],0);
									$order_qntytot=$order_qntytot+$row[('po_quantity')];
									$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
									?></p></td>
									<td width="90" align="right"><p>
									<?
									echo number_format($brk_downPcs_arr[$row[('job_no')]]['brk_dn_pcs'],0);
									 
									$brk_gorder_qntytot+=$brk_downPcs_arr[$row[('job_no')]]['brk_dn_pcs'];
									?></p></td>
								
								<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
								<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
								<td width="100" align="right"><p>
									<?
										echo number_format($row[('po_total_price')],2);
										$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
										$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
									?></p></td>
								<td width="100" align="right" title="Commission:<?=$commission_costing_sum_arr[$po_id];?>">
								<?
								echo number_format($net_total=($row[('po_total_price')]-$commission_costing_sum_arr[$po_id]),2);
								$net_goreder_value_tot+=$net_total;
								?>
								</td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
									<?
									unset($bank_id_arr);
									unset($bank_string_arr);
									if($lc_bank_arr[$po_id] !="")
									{
										$bank_id_arr=array_unique(explode(",",$lc_bank_arr[$po_id]));
										foreach($bank_id_arr as $bank_id)
										{
											$bank_string_arr[]=$bank_name_arr[$bank_id];
										}
										echo implode(",",$bank_string_arr);
									}
									$sc_bank=rtrim($sc_bank_arr[$po_id],',');
									if($sc_bank !="")
									{
										$bank_id_arr=array_unique(explode(",",$sc_bank));
										foreach($bank_id_arr as $bank_id)
										{
											$bank_string_arr[]=$bank_name_arr[$bank_id];
										}
										echo implode(",",$bank_string_arr);
									}
									?>
	
								</div>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
									<?
									if($lc_number_arr[$po_id] !="")
									{
										echo "LC: ". $lc_number_arr[$po_id];
										$lc_no = $lc_number_arr[$po_id];
									}
									$sc_number=rtrim($sc_number_arr[$po_id],',');
									$sc_numbers=implode(",",array_unique(explode(",",$sc_number)));
									if($sc_numbers !="")
									{
										echo " SC: ".$sc_numbers;
									}
									?>
									</div></td>
								<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
									<? if($lc_number_arr[$po_id] !="")
										{
											 echo $lc_amendment_arr[$lc_id_arr[$po_id]];
	
										}
									?>
								</div></td>
								<td width="80" align="center"><p>
										<?
										if($export_lc_arr[$po_id]['file_no']!='') echo $export_lc_arr[$po_id]['file_no'];
										if($export_sc_arr[$po_id]['file_no']!='') echo $export_sc_arr[$po_id]['file_no'];
			
										?>
	
								 </p>
							   </td>
								<td width="80" align="center"><p><?
	
								if($export_lc_arr[$po_id]['pay_term']!="") echo $export_lc_arr[$po_id]['pay_term'];
								if($export_sc_arr[$po_id]['pay_term']!="") echo $export_sc_arr[$po_id]['pay_term'];
	
								 ?></p></td>
								<td width="80" align="center"><p><?
	
								if($export_lc_arr[$po_id]['tenor']!="" ) echo $export_lc_arr[$po_id]['tenor'];
								if($export_sc_arr[$po_id]['tenor']!="" ) echo $export_sc_arr[$po_id]['tenor'];
	
								 ?></p></td>
	
								<td width="90" align="right"><p>
								<?
									$ex_factory_del_qty=$ex_factory_qty_arr[$po_id]['del_qty'];
									$ex_factory_return_qty=$ex_factory_qty_arr[$po_id]['return_qty'];
									$ex_factory_qnty=$ex_factory_del_qty-$ex_factory_return_qty;
	
									//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
									?>
									<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
									<?
	
									$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
									$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
									if ($shipment_performance==0)
									{
										$po_qnty['yet']+=($row[('po_quantity')]*$row[('total_set_qnty')]);
										$po_value['yet']+=100;
									}
									else if ($shipment_performance==1)
									{
										$po_qnty['ontime']+=$ex_factory_qnty;
										$po_value['ontime']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
										$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									}
									else if ($shipment_performance==2)
									{
										$po_qnty['after']+=$ex_factory_qnty;
										$po_value['after']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
										$po_qnty['yet']+=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									}
									?></p></td>
								<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
								<td  width="90" align="right"><p>
									<?
										$short_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
										$access_qnty=($ex_factory_qnty-($row[('po_quantity')]*$row[('total_set_qnty')]));
	
										$short_qnty=($short_qnty>0)?$short_qnty:0;
										$access_qnty=($access_qnty>0)?$access_qnty:0;
										
										
										echo number_format($short_qnty,0);
										$total_short_qnty=$total_short_qnty+$short_qnty;
										$gtotal_short_qnty=$gtotal_short_qnty+$short_qnty;
										
										$total_access_qnty=$total_access_qnty+$access_qnty;
										$gtotal_access_qnty=$gtotal_access_qnty+$access_qnty;
									?></p>
								</td>
								<td width="100" align="right"><p>
									<?
										$short_value=$short_qnty*$row[('unit_price')];
										$access_value=$access_qnty*$row[('unit_price')];
										
										echo number_format($short_value,2);
										$total_short_value=$total_short_value+$short_value;
										$gtotal_short_value=$gtotal_short_value+$short_value;
										
										$total_access_value=$total_access_value+$access_value;
										$gtotal_access_value=$gtotal_access_value+$access_value;
										
									?></p>
								</td>
								
								<td width="90" align="right"><?=$access_qnty;?></td>
								<td width="100" align="right"><?=$access_value;?></td>
								
								
								<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
									<?
										echo number_format($yarn_req_for_po,2);
										$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
										$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
									?></p>
								</td>
								<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
								<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
								<td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
								
								<td width="150"><?= $row['factory_marchant'];?></td>
								
								
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
								<td width="40"><p><? echo $po_id; ?></p></td>
								<td><p><? echo $row[('details_remarks')]; ?></p></td>
							</tr>
						<?
						$i++;
						}
						?>
						<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
							<td align="center" >  Total: </td>
							<td></td>
							<td></td>
							<td></td>
							
							<td></td>
							<td></td>
							<td></td>
							
							<td></td>
							<td></td>
							<td></td>
	
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							
							<td></td>
							<td></td>
							
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							
							<td></td>
							<td></td>
							<td></td>
							
							
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($brk_gorder_qntytot,0); ?></td>
							<td></td>
							<td></td>
	
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
							<td align="right"><? echo number_format($net_goreder_value_tot,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
							<td></td>
							<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_qnty,0); ?></td>
							<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_value,0); ?></td>
							
							<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_saccess_qnty,0); ?></td>
							<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_access_value,0); ?></td>
							
							<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></th>
						 </tr>
					<?
					
					?>
					</table>
					</div>
					<table style="display: none;" width="<?= $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
						<tfoot>
							<tr>
								<th width="50"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="50"></th>
								<th width="100"></th>
								<th width="60"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
	
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
	
								<th width="150"></th>
								<th width="40"></th>
								<th width="70"></th>
								<th width="90"></th>
								<th width="70" ></th>
								<th width="110"></th>
								<th width="100"></th>
								
								<th width="100"></th>
								<th width="100"></th>
								
								<th width="100"></th>
								<th width="50"></th>
								<th width="70"></th>
								
								<th width="70"></th>
								<th width="70"></th>
								<th width="70"></th>
								
								
								
								<th width="200"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
								<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
								<th width="40"></th>
								<th width="50"></th>
	
								<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
								 <th width="100">ccc</th>
								 <th width="100"></th>
								<th width="100"></th>
								<th width="90"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
								<th width="70"></th>
								<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_qnty,0); ?></th>
								<th width="100" id="value_total_short_access_value" align="right"><? echo number_format($total_short_value,0); ?></th>
								
								<th width="90" id="total_access_qnty" align="right"><? echo number_format($total_access_qnty,0); ?></th>
								<th width="100" id="value_total_access_value" align="right"><? echo number_format($total_access_value,0); ?></th>
								
								
								
								<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
								<th width="100"></th>
								<th width="100" ></th>
								<th width="150"> </th>
								<th width="150"></th>
								<th width="150"></th>
								<th width="100"></th>
								<th width="40"></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
					<?
					}
		?>
			<div id="shipment_performance" style="visibility:hidden">
				<fieldset>
					<table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<th colspan="4"> <font size="4">Shipment Performance</font></th>
							</tr>
							<tr>
								<th>Particulars</th><th>No of PO</th><th>PO Qnty</th><th> %</th>
							</tr>
						</thead>
						<tr bgcolor="#E9F3FF">
							<td>On Time Shipment</td><td><? echo $number_of_order['ontime']; ?></td><td align="right"><? echo number_format($po_qnty['ontime'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
							<td> Delivery After Shipment Date</td><td><? echo $number_of_order['after']; ?></td><td align="right"><? echo number_format($po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['after'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>
							<tr bgcolor="#E9F3FF">
							<td>Yet To Shipment </td>
                            <td><? echo $number_of_order['yet']; ?></td>
                            <td align="right">
							<? $po_qnty['yet']=$total_short_qnty;
							 echo number_format($po_qnty['yet'],0); ?>
                            </td>
                            <td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>

							<tr bgcolor="#E9F3FF">
							<td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			</div>
			</div>
		</div>
		<?
	}

	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type####$search_by";
	disconnect($con);
	exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
			<td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
if($action=="show_file")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=2");
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=2";
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
            <td><a href="../../../../<? echo $row[csf('image_location')] ?>" target="_new"> 
            	<img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
            </td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}

if($action=="last_ex_factory_Date")
{
 	echo load_html_head_contents("Last Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:550px">
        <div class="form_caption" align="center"><strong>Last Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">Challan No.</th>
                        <th width="100">Ex-Fact. Qnty.</th>
                        <th width="100">Ex-Fact. Return Qnty.</th>
                        <th width="">Trans. Com.</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1; $job_po_qnty=0; $job_plan_qnty=0; $job_total_price=0;
                $ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com,
				CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
                //echo $ex_fac_sql;
                $sql_dtls=sql_select($ex_fac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        <td width=""><? echo $row_real[csf("transport_com")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($return_qnty,2); ?></th>
                     <th>&nbsp;</th>
                </tr>

                 <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="3" align="right"><? echo number_format($rec_qnty-$return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}
if($action=="smv_set_details")
{
 	echo load_html_head_contents("SMV Set Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	$data_array=sql_select("select a.id, a.job_no, a.job_no_prefix, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no='$job_no' and a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1");
	
	
	$sql_d=sql_select("Select gmts_item_id, set_item_ratio, smv_pcs, smv_set, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, ws_id from wo_po_details_mas_set_details where job_no='$job_no' order by id");
	
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:450px">
        <div class="form_caption" align="center"><strong>SMV Set Details</strong></div><br />
            <div style="width:100%">
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                       
						<td width="100">Job Number</td>
                        <td width="100" align="center"><? echo $data_array[0][csf("job_no")];?></td>
                     </tr>
					 <tr>
						 
                        <td width="100">Style Ref. No.</td>
						<td width="100" align="center"><? echo $data_array[0][csf("style_ref_no")];?></td>
                       
                     </tr>
					 <tr>					
                        <td width="100">Buyer name</td>
						<td width="100" align="center"><? echo $data_array[0][csf("buyer_name")];?></td>
                     </tr>
					 <tr>
                        <td width="100">Order Numbers</td>
						<td width="100" align="center"><? echo $data_array[0][csf("po_number")];?></td>
                     </tr>
                </thead>
            </table>
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                       
						<th width="150">Item</th>
                        <th width="100">Set Ratio</th>
						<th width="100">Sew SMV/Psc</th>
                     </tr>	
                </thead>
				<? 
				foreach($sql_d as $row){?>
				    	<tr>
							<td align="center"><?echo $garments_item[$row[csf("gmts_item_id")]];?></td>
							<td align="center"><?echo $row[csf("set_item_ratio")];?></td>
							<td align="center"><?echo $row[csf("smv_pcs")];?></td>                       
                       </tr>
					 <?$tot_smv +=$row[csf("smv_pcs")];}?>
					 <tr>
							
							<td colspan="2" align="right">Total</td>
							<td align="center"><?echo $tot_smv;?></td>                       
                       </tr>
            </table>        
        </div>     
		</fieldset>
	</div>
	<?
    exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
                //$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
                //echo $ex_fac_sql;
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

if($action=="order_status_popup")
{
 	echo load_html_head_contents("Order Status", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Order Status</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th width="150">Order No</th>
                        <th >Status</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1; $job_po_qnty=0; $job_plan_qnty=0; $job_total_price=0;
                $order_sql="SELECT id, po_number, is_confirmed from wo_po_break_down where id in($id) and status_active=1 and is_deleted=0";
                $sql_dtls=sql_select($order_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $row_real[csf("po_number")]; ?></td>
                        <td><? echo $order_status[$row_real[csf("is_confirmed")]]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

?>
