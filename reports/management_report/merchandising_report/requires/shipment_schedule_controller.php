<?
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');
 

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
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	exit();
}

if($action=="print_button_variable_setting")
{
	
	//$print_report_format=0;
	$print_report_format=fnc_report_button($data,11,121,0);//Company,Menu Id,page Id,First Button
	
	/*$print_report_format=0;
	//echo $data.'DDDDDDDDDDDDD';
	$sql_button= "select format_id from lib_report_template where template_name in(".$data.") and module_id=11 and report_id=121 and is_deleted=0 and status_active=1";
	$sql_button_result=sql_select($sql_button);
	foreach($sql_button_result as $row)
	{
		$format_idArr[$row[csf('format_id')]]=$row[csf('format_id')];
	}
	if(count($sql_button_result)>0)
	{
		  $format_idAll=implode(",",$format_idArr);
		  $print_idArrAll=array_unique(explode(",",$format_idAll));
		  $print_report_format=implode(",",$print_idArrAll);
	}
	else
	{
	 $print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$data.") and module_id=11 and report_id=121 and is_deleted=0 and status_active=1");  }*/
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
	echo create_drop_down( "cbo_brand_name", 70, "select id, brand_name from lib_buyer_brand where buyer_id in($data) and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}

if($action=="load_drop_down_team_leader")
{

	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where user_tag_id=$user_id and id='$data' and status_active=1", "id", "team_leader_name");		
	if(count($team_leader_arr)>0){
		echo create_drop_down( "cbo_team_leader", 100, $team_leader_arr,"", 1, "-Team Leader-", $data, "" );
	}else{
		echo create_drop_down( "cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team  where id='$data' and status_active=1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 0, "-Team Leader-", $selected, "" );
	}

	exit();
}

if($action=="load_drop_down_team_member")
{

	$team_member_data=sql_select( "select id,team_member_name,user_tag_id from lib_mkt_team_member_info where  team_id='$data' and status_active=1 ");
	$user_member=array();
	foreach($team_member_data as $row){
		$user_member[$row[csf('user_tag_id')]]=$row[csf('id')];
		$team_member_arr[$row[csf('id')]]=$row[csf('team_member_name')];
	}
			
	if(count($team_member_arr)>0){
		echo create_drop_down( "cbo_team_member", 100, $team_member_arr,"", 1, "-Team Member-",$user_member[$user_id], "" );
	}else{
		echo create_drop_down( "cbo_team_member", 100, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Team Member-", $selected, "" );
	}


	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$style_owner_name=str_replace("'","",$cbo_style_owner_name);
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
	if($company_name!=0) $company_name_cond="and a.company_name in($company_name) "; else $company_name_cond="";
	if($company_name!=0) $company_name_cond2="and a.company_id in($company_name) "; else $company_name_cond2="";
	if($style_owner_name!=0) $style_owner_con="and a.style_owner=$style_owner_name"; else $style_owner_con="";
	if($year_id!=0) $season_year_cond="and a.season_year=$year_id "; else $season_year_cond="";
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
		$buyer_id_cond=" and a.buyer_name in($buyer_name)";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id in($buyer_name)";
	}
	$brand_cond="";
	if($cbo_brand_name==0)
	{
		if($filterBrandId!="") $brand_cond="and a.brand_id in ($filterBrandId)"; else $brand_cond="";
	}
	else $brand_cond="and a.brand_id in($cbo_brand_name) ";

	if(trim($date_from)!="") $start_date=$date_from;
	if(trim($date_to)!="") $end_date=$date_to;

	// if($$cbo_order_status2==2) $cbo_order_status="%%"; else $cbo_order_status= "$cbo_order_status2";
	
	if(trim($cbo_order_status)!=0){$order_confirm_status_con=" and b.is_confirmed = $cbo_order_status";}

	if(trim($team_name)=="0") $team_leader="%%"; else $team_leader="$team_name";
	if(trim($team_member)=="0") $dealing_marchant="%%"; else $dealing_marchant="$team_member";
	//if(trim($company_name)=="0") $company_name="%%"; else $company_name="$company_name";
	if($cbo_shipment_status) $shipment_status_cond="and b.shiping_status in($cbo_shipment_status)";else $shipment_status_cond="";
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
	}
	else if($category_by==4)
	{ 
			if ($start_date!="" && $end_date!="") $date_cond=" and b.pack_handover_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	else if($category_by==5)
	{
			if ($start_date!="" && $end_date!="") $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	
	//echo $date_cond;die;

	//echo $date_cond;die;

    //echo $rpt_type;die;

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


	if($rpt_type==1)//Details
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
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
		
	  $sql_data="SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity,a.brand_id, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst   $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $company_name_cond  $style_owner_con $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity,a.brand_id, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  // echo  $sql_data;  
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
			$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
			$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
			$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
			$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
			$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
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
			if($all_po_id=="") $all_po_id=$row[csf('po_id')];else $all_po_id.=",".$row[csf('po_id')];
			$po_idArr[$row[csf('po_id')]]=$row[csf('po_id')];
			//Company Buyer Wise
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
		}
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
				$po_cond_for_in2.=" b.id in($ids) or";
				$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
		}
		else
		{
			$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and b.id in($poIds)";
			$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
		}
 
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $company_name_cond2 $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");
		/*echo "select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id";*/
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			//Buyer Wise
			if($shiping_status_id==3)//Full shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			else if($shiping_status_id==2)//Partial shipped
			{
				$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
		}
		
		if($db_type==0) $fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
		else if($db_type==2) $fab_dec_cond="rtrim(xmlagg(XMLELEMENT(e,c.fabric_description,',').EXTRACT('//text()')
		).GetClobVal(),',') fabric_description";
			
		$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b 
		where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');

		$sql_budget="select a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		$data_budget_array=sql_select($sql_budget);
		
		$fabric_arr=array();
		foreach ($data_budget_array as $row)
		{
			$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')]->load();
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
	 
		
					 
		ob_start();
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
							<th width="130">Company Name</th><th width="200">Buyer Name</th>
							<th width="130">Quantity</th><th width="100">Value</th><th width="50">Value %</th>
							<th width="130"><strong>Full Shipped</strong></th><th width="130"><strong>Partial Shipped</strong></th>
							<th width="130"><strong>Running</strong></th><th><strong>Ex-factory Percentage</strong></th>
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
								<td width="50" align="center"><?=$i;?></td>
								<td width="130"><?=$company_short_name_arr[$com_id]; ?></td>
								<td width="200"><?=$buyer_short_name_arr[$buyer_id];?></td>
								<td width="130" align="right">
								<?
								echo number_format($row[('po_quantity')],0); $total_po +=$row[('po_quantity')];
								if (array_key_exists($com_id, $po_qnty_array))
								{
									$po_qnty_array[$com_id]+=$row[('po_quantity')];
								}
								else $po_qnty_array[$com_id]=$row[('po_quantity')];
								?>
								</td>
								<td width="100" align="right">
								<?
								echo number_format($row[('po_total_price')],2); $total_price+= $row[('po_total_price')];
								if (array_key_exists($com_id, $po_value_array))
								{
									$po_value_array[$com_id]+=$row[('po_total_price')];
								}
								else $po_value_array[$com_id]=$row[('po_total_price')];
								?><input type="hidden" id="value_<?=$i; ?>" value="<?=$row[('po_total_price')]; ?>"/>
								</td>
								<td width="50" id="value_percent_<?=$i; ?>" align="right"></td>
								<td width="130" align="right">
								<?
								echo number_format($full_shiped,0); $full_shipped_total+=$full_shiped;
								if (array_key_exists($com_id, $po_full_shiped_array))
								{
									$po_full_shiped_array[$com_id]+=$full_shiped;
								}
								else $po_full_shiped_array[$com_id]=$full_shiped;
								?>
								</td>
								<td width="130" align="right">
								<?
								echo number_format($partial_shiped,0); $partial_shipped_total+=$partial_shiped;
								if (array_key_exists($com_id, $po_partial_shiped_array))
								{
									$po_partial_shiped_array[$com_id]+=$partial_shiped;
								}
								else $po_partial_shiped_array[$com_id]=$partial_shiped;
								?>
								</td>
								<td width="130" align="right">
								<?
								$runing=$row[('po_quantity')]-($full_shiped+$partial_shiped); echo number_format($runing,0);$running_shipped_total+=$runing;
								if (array_key_exists($com_id, $po_running_array))
								{
									$po_running_array[$com_id]+=$runing;
								}
								else $po_running_array[$com_id]=$runing;
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
							<th width="50">&nbsp;</th>
							<th width="130">&nbsp;</th><th width="200">&nbsp;</th>
							<th width="130"><?=number_format($total_po,0); ?></th>
                            <th width="100"><?=number_format($total_price,2); ?><input type="hidden" id="total_value" value="<?=$total_price;?>"/></th>
                            <th width="50">&nbsp;</th>
							<th width="130"><?=number_format($full_shipped_total,0); ?></th>
                            <th width="130"><?=number_format($partial_shipped_total,0); ?></th>
							<th width="130"><?=number_format($running_shipped_total,0); ?></th>
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
								<th>Running </th>
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
								<td align="right"><? $ex_factory_per=(($po_full_shiped_array[$key]+$po_partial_shiped_array[$key])/($value))*100; echo number_format($ex_factory_per,2).' %'; ?></td>
							</tr>
							<tr bgcolor="white">
								<td align="center">LC Value</td>
								<td align="right"><? echo number_format($po_value_array[$key],2);  $comp_po_total_value=$comp_po_total_value+$po_value_array[$key];?></td>
								<td align="right"><? $full_shiped_value=($po_value_array[$key]/$value)*$po_full_shiped_array[$key]; echo number_format($full_shiped_value,2); $total_full_shiped_val+=$full_shiped_value; ?></td>
								<td align="right"><? $full_partial_shipeddd_value=($po_value_array[$key]/$value)*$po_partial_shiped_array[$key]; echo number_format($full_partial_shipeddd_value,2); $total_par_val+=$full_partial_shipeddd_value; ?></td>
								<td align="right"><? $full_running_value=($po_value_array[$key]/$value)*$po_running_array[$key]; echo number_format($full_running_value,2); $total_run_val+=$full_running_value; ?></td>
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
								<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; ?></th>
							</tr>
							<tr bgcolor="#999999">
								<th align="center">Value Total:</th>
								<th align="right"><? echo number_format($comp_po_total_value,2); ?></th>
								<th align="right"><? echo number_format($total_full_shiped_val,2); ?></th>
								<th align="right"><? echo number_format($total_par_val,2); ?></th>
								<th align="right"><? echo number_format($total_run_val,2); ?></th>
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
		<h3 style="width:3300px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
		<div id="content_report_panel">
        <?
		// echo '11111111';die;
		if($search_by==1)
		{
		$condition= new condition();
		if(count($po_idArr)>0)
		{
			$condition->po_id_in("".implode(",",$po_idArr)."");
		}
		//team_leader
				
		$condition->company_name("=$company_name");
		if(str_replace("'","",$buyer_name)>0){
			//$condition->buyer_name("=$buyer_name");
		}
		if(str_replace("'","",$search_string) !=''){
			if($search_by==1)
			{
			$condition->po_number("='$search_string'");
			}
			if($search_by==2)
			{
			$condition->style_ref_no("='$search_string'");
			}
			if($search_by==3)
			{
			$condition->job_no_prefix_num("=$search_string");
			}
			//style_ref_no
		}
		if(str_replace("'","",$team_name)>0){
			$condition->team_leader("=$team_name");
		}
		if(str_replace("'","",$cbo_order_status)>0){
			$condition->is_confirmed("=$cbo_order_status");
		}
		if(str_replace("'","",$cbo_order_status)==0){
			$condition->is_confirmed("in(1,2)");
		}
		if($category_by==1)
		{
			if ($start_date!="" && $end_date!="")
			{
				$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			}
			
		}
		else if($category_by==2)
		{
			if ($start_date!="" && $end_date!="")
			{
			$condition->po_received_date(" between '$start_date' and '$end_date'");
			}
		}
		else if($category_by==3)
		{
			if ($start_date!="" && $end_date!="")
			{
				 
					if ($start_date!="" && $end_date!="")
					{
					$condition->insert_date(" between '$start_date' and '$end_date' 11:59:59 PM");
					 
					}

			}
			 
		}
		else if($category_by==4)
		{ 
				//if ($start_date!="" && $end_date!="") $date_cond=" and b.pack_handover_date between '$start_date' and '$end_date'"; else $date_cond="";
				//pack_handover_date
			if ($start_date!="" && $end_date!="")
				{
				$condition->pack_handover_date(" between '$start_date' and '$end_date'");
				}
		}
		else if($category_by==5)
		{
				if ($start_date!="" && $end_date!="")// $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
				{
					$condition->shipment_date(" between '$start_date' and '$end_date'");
				}
		}

		$condition->init();
		$fabric= new fabric($condition);
		// echo $fabric->getQuery(); die;
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
		// print_r($fabric_costing_arr); 
	 	$yarn= new yarn($condition);
	 	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		//print_r($yarn_costing_arr);die;
		$conversion= new conversion($condition);
		$conversion_costing_arr_process=$conversion->getAmountArray_by_order();
		$trims= new trims($condition);
		// echo $trims->getQuery(); die;
		$trims_costing_arr=$trims->getAmountArray_by_order();
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
		$wash= new wash($condition);
		//echo $wash->getQuery(); die;
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_order();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_order();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();

			?>
			<table width="3930" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="70">Job No</th>
						<th width="60">Year</th>
						<th width="50">Buyer</th>
						<th width="50">Brand</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">Ref No</th>
						<th width="100">Season</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
						<th width="90">Style Ref</th>
						<th width="150">Item Name</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qnty(Pcs)</th>
						<th width="90">Order Qnty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100">Contribution Margin </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="40">Id</th>
						<th>Remarks</th>
						<th width="100">User Name</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:3950px"  align="left" id="scroll_body">
			<table width="3930" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
				
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
					//DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4
					//	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, b.id,b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					}
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

						// (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4
						/* $data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/

					}
					$total_contribution_margin=0;
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
							if($yarn_cons_qnty)
							{
							$yarn_req_for_po=($yarn_cons_qnty/$costing_per_pcs)*$row[('po_quantity')];
							}
							else $yarn_req_for_po=0;
							//$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$ttl_fab_cost=array_sum($fabric_costing_arr['knit']['grey'][$po_id])+array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
						$yarn_costing=$yarn_costing_arr[$po_id];
						$conversion_cost=array_sum($conversion_costing_arr_process[$po_id]);
						$trims_cost=$trims_costing_arr[$po_id];
						$embel_cost=$emblishment_costing_arr[$po_id];
						$wash_cost=$emblishment_costing_arr_wash[$po_id];
						$commercial_cost=$commercial_costing_arr[$po_id];
						$tot_commission=$commission_costing_arr[$po_id];
						$inspection_cost=$other_costing_arr[$po_id]['inspection'];
						$certificate_cost=$other_costing_arr[$po_id]['certificate_pre_cost'];
						$currier_cost=$other_costing_arr[$po_id]['currier_pre_cost'];
						$cm_cost=$other_costing_arr[$po_id]['cm_cost'];
						$lab_test_cost=$other_costing_arr[$po_id]['lab_test'];
						$depr_amor_pre_cost=$other_costing_arr[$po_id]['depr_amor_pre_cost'];
						$deffdlc_cost=$other_costing_arr[$po_id]['deffdlc_cost'];
						//if($po_id==77041) echo $ttl_fab_cost.'='.$yarn_costing.'='.$conversion_cost.'='.$trims_cost.'='.$embel_cost.'='.$wash_cost.'='.$commercial_cost.',';
						$material_service_cost=$ttl_fab_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash_cost+$commercial_cost+$inspection_cost+$certificate_cost+$currier_cost+$lab_test_cost+$deffdlc_cost;
						$msg_ttl='Fab='.$ttl_fab_cost.',Y='.$yarn_costing.',Conv='.$conversion_cost.',Trim='.$trims_cost.',Emb='.$embel_cost.',Wash='.$wash_cost.',Commercl='.$commercial_cost.',inspct='.$inspection_cost.',Certificte='.$certificate_cost.',Currier='.$currier_cost.',Lab='.$lab_test_cost.',deffdlc='.$deffdlc_cost;
						$net_fob_val=$row[('po_total_price')]-$tot_commission;

						$contribution_margin=$net_fob_val-$material_service_cost;

						 //echo $row[('po_total_price')].'='.$net_fob_val.'='.$material_service_cost.'D';
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
							<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
							<td width="60"><p><? echo $row[('year')]; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
							<!-- <td width="110"><div style="word-wrap:break-word; width:110px"><? //echo $row[('po_number')];?></div></td> -->
							<td width="110"><? 
							//$po_number=implode(",",array_unique(explode(",",$row[csf('po_number')])));?>
							<a href="##" onClick="fn_report_generated_cutoff('<? echo $row[('company_name')];?>', '<? echo $row[('buyer_name')]?>', '<? echo $row[('job_no_prefix_num')]?>', '<? echo $row[('po_number')]; ?>', '<? echo $row[('style_ref_no')]?>')"><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')]; ?></div></a></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='<? echo base_url($imge_arr[$row[('job_no')]]); ?>' height='25' width='30' /></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
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
								// $gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								$gorder_qnty_pcs_tot+=$row[('po_quantity')]*$row[('total_set_qnty')];
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
									$short_access_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								 <?
									$short_access_value=$short_access_qnty*$row[('unit_price')];
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>
							<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100" align="right"><p><? if($cm_cost) echo number_format($cm_cost,2);else echo 0; //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="right" title="<?=$net_fob_val.'='.$material_service_cost.'='.$msg_ttl;?>"><div style="word-wrap:break-word; width:100px"><? echo number_format($contribution_margin,2); ?></div></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
							<td width="40"><p><? echo $po_id; ?></p></td>
							<td><p><? echo $row[('details_remarks')]; ?></p></td>
							<td width="100"><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
						</tr>
					<?
					$i++;
					$total_contribution_margin+=$contribution_margin;
					}
					?>
					<!-- <tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="50" align="center" >  Total: </td>
						<td width="70" ></td>
						<td width="70"></td>
						<td width="60"></td>
						<td width="50"></td>
						<td width="50"></td>
						<td width="110"></td>
                        <td width="100"></td>
                        <td width="100"></td>
						<td width="100"></td>
						<td width="50"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="40"></td>
						<td width="90"></td>
						<td width="150"></td>
						<td width="200"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>

						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
                        <td width="100"></td>
						<td width="100"></td>
						<td width="90"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100" ></td>
						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="40"></td>
						<td></th>
						<td width="100"></td>
					 </tr> -->
				<?
				
				?>
				</table>
				</div>
				<table width="3930" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70" ></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="50"></th>
							<th width="50"></th>
							<th width="110"></th>
                            <th width="100"></th>
                            <th width="100"></th>
							<th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="40"></th>
							<th width="90"></th>
							<th width="150"></th>
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>

							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
                             <th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100" ><? echo number_format($total_contribution_margin,2); ?></th>
							<th width="100" ></th>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="40"></th>
							<th></th>
							<th width="100"></th>
						</tr>
					</tfoot>
				</table>
				<?
		}
		else
		{
			?>
			<table width="4410" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="70">Job No</th>
						<th width="60">Year</th>
						<th width="50">Buyer</th>
						<th width="50">Buyer</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">Ref No</th>
						<th width="100">Season</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
						<th width="90">Style Ref</th>
						<th width="150">Item</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qnty(Pcs)</th>
						<th width="90">Order Qnty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80">Int. File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor</th>

						<th width="100">Last Accessories In-House Date</th>
						<th width="100">Last Fabric In-House Date</th>
						<th width="100">Cutting Qty</th>
						<th width="100">Sewing Qty</th>
						<th width="100">Finishing</th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100">Contribution Margin </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="120">Id</th>
						<th>Remarks</th>
						<th width="100">User Name</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:4430px"  align="left" id="scroll_body">
			<table width="4410" border="1" class="rpt_table" rules="all" id="table_body">
				<?
			

				$ex_fact_sql=sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.id=b.job_id and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $buyer_id_cond  $style_owner_con $pocond $year_cond $search_string_cond $file_cond $ref_cond $brand_cond and a.status_active=1 and b.status_active=1 group by a.job_no");
				$ex_fact_data=array();
				foreach($ex_fact_sql as $row)
				{
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
				}

				
				//var_dump($fabric_arr);die;

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				if($db_type==0)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				}
				if($db_type==2)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				}
				//
				$data_array_group=sql_select("select a.id as job_id,b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond  $style_owner_con $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond  group by a.id ,b.grouping");
				foreach ($data_array_group as $row)
				{
					$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
				}
				$job_cond_in=where_con_using_array($job_idArr,0,'job_id');
				
				$condition= new condition();
				if(count($job_idArr)>0)
				{
					$condition->jobid_in("".implode(",",$job_idArr)."");
				}
				$condition->company_name("=$company_name");
				 
				if(str_replace("'","",$search_string) !=''){
					if($search_by==1)
					{
					$condition->po_number("='$search_string'");
					}
					if($search_by==2)
					{
					$condition->style_ref_no("='$search_string'");
					}
					if($search_by==3)
					{
					$condition->job_no_prefix_num("=$search_string");
					}
					//style_ref_no
				}
				if(str_replace("'","",$team_name)>0){
					$condition->team_leader("=$team_name");
				}
				if(str_replace("'","",$cbo_order_status)>0){
					$condition->is_confirmed("=$cbo_order_status");
				}
				if(str_replace("'","",$cbo_order_status)==0){
					$condition->is_confirmed("in(1,2)");
				}
				if($category_by==1)
				{
					if ($start_date!="" && $end_date!="")
					{
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					
				}
				else if($category_by==2)
				{
					if ($start_date!="" && $end_date!="")
					{
					$condition->po_received_date(" between '$start_date' and '$end_date'");
					}
				}
				else if($category_by==3)
				{
					if ($start_date!="" && $end_date!="")
					{
						if ($start_date!="" && $end_date!="")
						{
						$condition->insert_date(" between '$start_date' and '$end_date' 11:59:59 PM");
						 
						}
					}
				}
				else if($category_by==4)
				{ 
						if ($start_date!="" && $end_date!="")
						{
						$condition->pack_handover_date(" between '$start_date' and '$end_date'");
						}
				}
				else if($category_by==5)
				{
						if ($start_date!="" && $end_date!="")// $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
						{
							$condition->shipment_date(" between '$start_date' and '$end_date'");
						}
				}
		
				$condition->init();
				$fabric= new fabric($condition);
				// echo $fabric->getQuery(); die;
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				// print_r($fabric_costing_arr); 
				$yarn= new yarn($condition);
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				//print_r($yarn_costing_arr);die;
				$conversion= new conversion($condition);
				$conversion_costing_arr_process=$conversion->getAmountArray_by_order();
				$trims= new trims($condition);
				// echo $trims->getQuery(); die;
				$trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$wash= new wash($condition);
				//echo $wash->getQuery(); die;
				$emblishment_costing_arr_wash=$wash->getAmountArray_by_order();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
		
				$yarn_cons_arr=return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0 $job_cond_in","job_no","yarn_cons_qnty");
				$costing_per_arr=return_library_array("select job_no, costing_per from  wo_pre_cost_mst where status_active=1 and is_deleted=0 $job_cond_in","job_no","costing_per");
				if($db_type==0)
				{
					//$fab_dec_cond="group_concat(fabric_description)";
				}
				else if($db_type==2)
				{
				//	$fab_dec_cond="listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
				}
				$fabric_arr=array();
				$fab_sql=sql_select("select job_no,fabric_description, item_number_id from wo_pre_cost_fabric_cost_dtls where status_active=1
				 and is_deleted=0 $job_cond_in ");
				foreach ($fab_sql as $row)
				{
					$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]].=$row[csf('fabric_description')].',';
				}

				foreach ($data_array_group as $row_group)
				{
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
						$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, group_concat(b.id) as id, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(DATEDIFF(b.pub_shipment_date,'$date')) date_diff_1, max(DATEDIFF(b.shipment_date,'$date')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, group_concat(b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond  $style_owner_con $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond  $brand_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise
						order by a.style_ref_no");
					}
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
						$data_array=sql_select("select a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) as po_number, listagg(cast(b.is_confirmed as varchar2(4000)),',') within group (order by b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1,  max(b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.id=b.job_id and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' $grouping  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $style_owner_con $brand_cond $ref_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise
						order by a.style_ref_no");
					}

					foreach ($data_array as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


						$ex_factory_qnty=$ex_fact_data[$row[csf('job_no')]]["ex_factory_qnty"];
						$ex_factory_date=$ex_fact_data[$row[csf('job_no')]]["ex_factory_date"];
						$date_diff_3=datediff("d",$ex_factory_date, $row[csf('pub_shipment_date')]);
						$date_diff_4=datediff("d",$ex_factory_date, $row[csf('shipment_date')]);

						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=$yarn_cons_arr[$row[csf('job_no')]];
						$data_array_costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($data_array_costing_per==1) $costing_per_pcs=1*12;
						else if($data_array_costing_per==2) $costing_per_pcs=1*1;
						else if($data_array_costing_per==3) $costing_per_pcs=2*12;
						else if($data_array_costing_per==4) $costing_per_pcs=3*12;
						else if($data_array_costing_per==5) $costing_per_pcs=4*12;

						if($data_array_yarn_cons)
						{
						$yarn_req_for_po=($data_array_yarn_cons/ $costing_per_pcs)*$row[csf('po_quantity')];
						}
						else $yarn_req_for_po=0;
						$poIdArr=array_unique(explode(",",$row[csf('id')]));
						$ttl_fab_cost=$yarn_costing=$conversion_cost=$trims_cost=$embel_cost=$wash_cost=$commercial_cost=$tot_commission=$inspection_cost=$certificate_cost+$currier_cost+$lab_test_cost+$depr_amor_pre_cost+$deffdlc_cost=0;
						foreach($poIdArr as $po_id)
						{
							$ttl_fab_cost+=array_sum($fabric_costing_arr['knit']['grey'][$po_id])+array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
							$yarn_costing+=$yarn_costing_arr[$po_id];
							$conversion_cost+=array_sum($conversion_costing_arr_process[$po_id]);
							$trims_cost+=$trims_costing_arr[$po_id];
							$embel_cost+=$emblishment_costing_arr[$po_id];
							$wash_cost+=$emblishment_costing_arr_wash[$po_id];
							$commercial_cost+=$commercial_costing_arr[$po_id];
							$tot_commission+=$commission_costing_arr[$po_id];
							$inspection_cost+=$other_costing_arr[$po_id]['inspection'];
							$certificate_cost+=$other_costing_arr[$po_id]['certificate_pre_cost'];
							$currier_cost+=$other_costing_arr[$po_id]['currier_pre_cost'];
							//$cm_cost+=$other_costing_arr[$po_id]['cm_cost'];
							$lab_test_cost+=$other_costing_arr[$po_id]['lab_test'];
							$depr_amor_pre_cost+=$other_costing_arr[$po_id]['depr_amor_pre_cost'];
							$deffdlc_cost+=$other_costing_arr[$po_id]['deffdlc_cost'];
						}
						
						$material_service_cost=$ttl_fab_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash_cost+$commercial_cost+$inspection_cost+$certificate_cost+$currier_cost+$lab_test_cost+$depr_amor_pre_cost+$deffdlc_cost;
						$net_fob_val=$row[csf('po_total_price')]-$tot_commission;
						$contribution_margin=$net_fob_val-$material_service_cost;



						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shiping_status_arr=explode(",",$row[csf('shiping_status')]);
						$shiping_status_arr=array_unique($shiping_status_arr);
						if(count($shiping_status_arr)>1) $shiping_status=2; else $shiping_status=$shiping_status_arr[0];
						$shipment_performance=0;
						if($shiping_status==1 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($shiping_status && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($shiping_status==1 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($shiping_status==2 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
						}
						if($shiping_status==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($shiping_status==3 && $date_diff_3>=0 )
						{
							$color="green";
						}
						if($shiping_status==3 &&  $date_diff_3<0)
						{
							$color="#2A9FFF";
						}
						if($shiping_status==3 && $date_diff_4>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==3 &&  $date_diff_4<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						$actual_po="";
						$ex_po_id=explode(",",$row[csf('id')]);
						foreach($ex_po_id as $poId)
						{
							if($actual_po=="") $actual_po=$actual_po_no_arr[$row[csf('id')]]; else $actual_po.=','.$actual_po_no_arr[$row[csf('id')]];
						}
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('company_name')]];?></div></td>
							<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[csf('brand_id')]];?></div></td>
							<td width="110"><? 
							$po_number=explode(",",$row[csf('po_number')]);
							foreach($po_number as $po){
							?>
							<a href="##" onClick="fn_report_generated_cutoff('<? echo $row[csf('company_name')];?>', '<? echo $row[csf('buyer_name')]?>', '<? echo $row[csf('job_no_prefix_num')]?>', '<? echo $po; ?>', '<? echo $row[csf('style_ref_no')]?>')"><div style="word-wrap:break-word; width:110px"><? echo $po.","; ?>
							<?}?>
						</div></a></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo implode(",",array_unique(explode(",",$actual_po)));?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('season')];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><a href="##" onClick="order_status('order_status_popup', '<? echo $row[csf('id')]; ?>','750px')">View</a></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[csf('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_ref_no')];?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=rtrim($fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]],','); else $fabric_description.=','.rtrim($fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]],',');
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(", ",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]);?>&nbsp;</div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('po_received_date')]!="" && $row[csf('po_received_date')]!="0000-00-00") echo change_date_format($row[csf('po_received_date')]);?>&nbsp;</div></td>
							<td width="50" bgcolor="<? echo $color; ?>"  align="center"><div style="word-wrap:break-word; width:50px">
								<?
								if($shiping_status==1 || $shiping_status==2)
								{
									echo $row[csf('date_diff_1')];
								}
								if($shiping_status==3)
								{
									echo $date_diff_3;
								}
								?></div></td>
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								?></p></td>
							<td width="90" align="right"><p>
								<?
								echo number_format( $row[csf('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? $unit_price=$row[csf('po_total_price')]/$row[csf('po_quantity')]; echo number_format($unit_price,2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[csf('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
								?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
								<?
								if($lc_number_arr[$row[csf('id')]] !="")
								{
									echo "LC: ". $lc_number_arr[$row[csf('id')]];
								}
								if($sc_number_arr[$row[csf('id')]] !="")
								{
									echo " SC: ".$sc_number_arr[$row[csf('id')]];
								}
								?>
								</div></td>
							<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
							<? if($lc_number_arr[$row[csf('id')]] !="")
								{
									echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]];

								}
							?>
							</div></td>
							<td width="80" align="center"><p><? echo $export_lc_arr[$row[csf('id')]]['file_no'];?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['pay_term']; ?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>

							<td width="100" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>
							<td width="100" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>
							<td width="100" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>
							<td width="100" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>
							<td width="100" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>

							<td width="90" align="right"><p>
							<?

								?>
								<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
                                <?
								//echo  number_format( $ex_factory_qnty,0);
								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qnty;
									$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qnty;
									$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								?></p></td>
							<td width="70" align="center"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? if($ex_factory_date!="" && $ex_factory_date!="0000-00-00") echo change_date_format($ex_factory_date); ?>&nbsp;</div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								<?
									$short_access_value=$short_access_qnty*$unit_price;
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>
							<td width="100" align="right" title="<? echo "Cons:".$data_array_yarn_cons."Costing per:".$data_array_costing_per;?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100" align="right"><p><? if($cm_for_shipment_schedule_arr[$row[csf('job_no')]]) echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')],2);else echo 0; ?></p></td>
							<td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($contribution_margin,2); ?></div></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$shiping_status]; ?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')]; ?></div></td>
							<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('id')]))); ?></p></td>
							<td><p><? echo $row[csf('details_remarks')]; ?></p></td>
							<td width="100"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
						</tr>
					<?
					$i++;
					$total_contribution_margin+=$contribution_margin;
					}
					?>
					<!-- <tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="50" align="center" >  Total (<?=$row_group[csf('grouping')]; ?>): </td>
						<td width="70" ></td>
						<td width="70"></td>
						<td width="60"></td>
						<td width="50"></td>
						<td width="50"></td>
						<td width="110"></td>
                        <td width="100"></td>
						<td width="100"></td>
                        <td width="100"></td>
						<td width="50"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="40"></td>
						<td width="90"></td>
						<td width="150"></td>
						<td width="200"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90" align="right" ><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
						<td width="100"></td>
						<td width="90"></td>

						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>

						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100" ></td>
						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td></th>
						<td width="100"></td>
					 </tr> -->
				<?
				}
				?>
				</table>
				</div>
				<table width="4410" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70" ></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="50"></th>
							<th width="50"></th>
							<th width="110"></th>
                            <th width="100"></th>
							<th width="100"></th>
                            <th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="40"></th>
							<th width="90"></th>
							<th width="150"></th>
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>
							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
			

							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100" ><? echo number_format($total_contribution_margin,2); ?></th>
							<th width="100" ></th>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="120"></th>
							<th></th>
							<th width="100"></th>
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
							<td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
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
	else if($rpt_type==2)//Short
	{
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$Dealing_marcent_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
		$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		
		?>
        <div style="width:1600px">
            <table width="1500" cellpadding="0" cellspacing="0" id="caption"  align="left">
            <tr>
                <td align="center" width="100%" class="form_caption"  colspan="13"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" class="form_caption"  colspan="13"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" class="form_caption"  colspan="13"><strong style="font-size:14px">From <? echo change_date_format($start_date); ?> To <? echo change_date_format($end_date); ?> </strong></td>
            </tr>
            </table>
            <br />
            <table width="1680" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header"  align="left">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">Company</th>
                        <th width="100">Buyer</th>
						<th width="100">Brand</th>
                        <th width="110">Style No</th>
                        <th width="110">PO No</th>
                        <th width="100">Internal Ref.No</th>
                        <th width="100">Dealing Merchant</th>
                        <th width="130">Item Description</th>
                        <th width="70">GSM</th>
                        <th width="250">Fabrication</th>
                        <th width="100">Order Qnty(Pcs)</th>
                        <th width="70"><? if($category_by==1) echo "Ship Date"; elseif($category_by==2) echo "PO Receive Date"; ?></th>
                        <th width="50">Unit Price</th>
                        <th width="100">FOB Price</th>
                        <th width="100">Remarks</th>
						 <th >User Name</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1700px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="1680" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <tbody>
                <?
                if($category_by==1)
                {
                    if ($start_date!="" && $end_date!="")
                    {
                        $date_cond=" and b.shipment_date between '$start_date' and '$end_date'";
                    }
                    else $date_cond="";
                }
                else
                {
                    if ($start_date!="" && $end_date!="")
                    {
                        $date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
                    }
                    else $date_cond="";
                }

				//$fabrication_sql=sql_select("select listagg(cast(a.fabric_description as varchar2(4000)),',') within group (order by a.fabric_description) as fabric_description, b.po_break_down_id from wo_pre_cost_fabric_cost_dtls a,  wo_pre_cos_fab_co_avg_con_dtls b,  where a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.cons>0 and c.company_name=$company_name group by b.po_break_down_id");

				
				//$file_cond  $ref_cond $rpt_type
                $main_sql="SELECT a.company_name, a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.remarks,a.brand_id,b.id as po_id, b.po_number, b.grouping, b.inserted_by, b.po_received_date, b.shipment_date, (a.total_set_qnty*b.po_quantity) as po_quantity_pcs,
				(b.unit_price/a.total_set_qnty) as unit_price_pcs,a.dealing_marchant
				from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and
				b.status_active=1 and b.is_deleted=0 and a.company_name in ($company_name) $date_cond $search_string_cond $shipment_status_cond $brand_cond  $year_cond $buyer_id_cond $season_cond $file_cond $ref_cond $order_confirm_status_con and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by a.buyer_name, a.id, b.id";
				//echo $main_sql; die;
                $main_result=sql_select($main_sql);

				$po_id_arr=array();
				foreach($main_result as $row)
                {
				$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
				}



				$fabrication_sql=sql_select("select a.fabric_description, b.po_break_down_id,a.gsm_weight from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.cons>0 ".where_con_using_array($po_id_arr,1,'b.po_break_down_id')."");

				$fabrication_data_arr=array();
				foreach($fabrication_sql as $row)
				{
					$fabrication_data_arr[$row[csf("po_break_down_id")]].=$row[csf("fabric_description")].",";
					$gsm_data_arr[$row[csf("po_break_down_id")]].=$row[csf("gsm_weight")].",";
				}
                $k=1;$m=1;
                $temp_arr_buyer=array();
                foreach($main_result as $row)
                {
					$po_total_price=0;
                    if(!in_array($row[csf("buyer_name")],$temp_arr_buyer))
                    {
                        $temp_arr_buyer[]=$row[csf("buyer_name")];
                        if($k!=1)
                        {
                            ?>
                            <tr bgcolor="#CCCCCC">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">Sub Total:&nbsp;</td>
                                <td align="right"><? echo number_format($buyer_tot_qnty,2); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><? echo number_format($buyer_tot_price,2); ?></td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
                            </tr>
                            <?
							unset($buyer_tot_qnty);
							unset($buyer_tot_price);
                        }
                        $k++;
                    }
                    if($m%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                        <td width="40" align="center"><p><? echo $m;?>&nbsp;</p></td>
                        <td width="100"><p><? echo $company_short_name_arr[$row[csf("company_name")]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $brand_arr[$row[csf("brand_id")]]; ?>&nbsp;</p></td>
                        <td width="110"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                        <td width="110"><p><? echo $row[csf("po_number")]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf("grouping")];?>&nbsp;</p></td>
                        <td width="100"><p><? echo $Dealing_marcent_arr[$row[csf("dealing_marchant")]]; ?>&nbsp;</p></td>
                        <td width="130"><p>
						<?
							$garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
							$all_garments_item="";
							foreach($garments_item_arr as $garments_item_id)
							{
								$all_garments_item.=$garments_item[$garments_item_id]." , ";
							}
							$all_garments_item=chop($all_garments_item," , ");
							echo $all_garments_item;
						?>&nbsp;</p></td>
                        <td width="70"><p>
						<?
						$gsm_data=implode(", ",array_unique(explode(",",chop($gsm_data_arr[$row[csf("po_id")]]," , ")))); echo $gsm_data;

						 //$gsm_data_arr[$row[csf("po_break_down_id")]];echo $row[csf("po_number")]; ?>&nbsp;</p></td>
                        <td width="250"><p><? $fabrication_data=implode(", ",array_unique(explode(",",chop($fabrication_data_arr[$row[csf("po_id")]]," , ")))); echo $fabrication_data; ?>&nbsp;</p></td>
                        <td width="100" align="right"><? echo number_format($row[csf("po_quantity_pcs")],2); $buyer_tot_qnty+=$row[csf("po_quantity_pcs")]; $total_po_qnty+=$row[csf("po_quantity_pcs")]; ?></td>
                        <td width="70" align="center"><p>
                        <?
                        if($category_by==1)
                        {
                            if($row[csf("shipment_date")]!="" && $row[csf("shipment_date")]!="0000-00-00") echo change_date_format($row[csf("shipment_date")]);
                        }
                        else if($category_by==2)
                        {
                            if($row[csf("po_received_date")]!="" && $row[csf("po_received_date")]!="0000-00-00") echo change_date_format($row[csf("po_received_date")]);
                        }
                        ?>
                        &nbsp;</p></td>
                        <td width="50" align="right"><? echo number_format($row[csf("unit_price_pcs")],2) ?></td>
                        <td width="100" align="right"><? $po_total_price=$row[csf("po_quantity_pcs")]*$row[csf("unit_price_pcs")]; echo number_format($po_total_price,2);  $buyer_tot_price+=$po_total_price; $total_po_price+=$po_total_price;?></td>
                        <td width="100"><p><? echo $row[csf("remarks")]; ?></p></td>
						<td width=""><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
                    </tr>
                    <?
                    $m++;
                }
                ?>
                    <tr bgcolor="#DDDDDD">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Sub Total:&nbsp;</td>
                        <td align="right"><? echo number_format($buyer_tot_qnty,2); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($buyer_tot_price,2); ?></td>
                        <td  width="100">&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                    <tr  bgcolor="#CCCCCC">
                        <td width="40">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="130">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="250" align="right">Grand Total:&nbsp;</td>
                        <td width="100"  align="right"><? echo number_format($total_po_qnty,2); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="100"  align="right"><? echo number_format($total_po_price,2); ?></td>
                        <td width="100">&nbsp;</td>
						<td  width="">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <?
	}
	else if($rpt_type==3)//Size Wise
	{
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
		$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		$color_arr=return_library_array("select id, color_name from lib_color", "id", "color_name");
		$size_arr=return_library_array("select id, size_name from lib_size", "id", "size_name");
		$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
		?>
        <div style="width:1500px">
            <table width="1500" cellpadding="0" cellspacing="0" id="caption"  align="left">
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:16px"><? echo $report_title.' [Size Wise]'; ?></strong></td>
                </tr>
                <? if($buyer_name != 0){ ?>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:14px"><? echo 'BUYER NAME: '.$buyer_arr[$buyer_name]; ?></strong></td>
                </tr>
                <? } ?>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:12px">From <? echo change_date_format($start_date); ?> To <? echo change_date_format($end_date); ?> </strong></td>
                </tr>
            </table>
            <br />
                <?
                if($category_by==1)
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and c.country_ship_date between '$start_date' and '$end_date'"; else $date_cond="";
                }
                else
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
                }

                $main_sql="select a.company_name,a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.total_set_qnty, a.style_ref_no, a.season_buyer_wise,a.brand_id, a.style_description, a.ship_mode, b.id as po_id, b.po_number, c.country_ship_date, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.order_rate, c.order_total,c.size_order,b.grouping,c.item_number_id
				from  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $date_cond $shipment_status_cond $search_string_cond $year_cond $buyer_id_cond $season_cond $brand_cond $file_cond $ref_cond $order_confirm_status_con and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by c.size_order";
				//echo $main_sql; die;
                $main_result=sql_select($main_sql);
				$all_size_arr=array(); $po_country_color_arr=array(); $po_country_color_size_arr=array(); $tot_rows=0; $poIds='';
				foreach($main_result as $row)
				{
					$tot_rows++;
					$poIds.=$row[csf("po_id")].",";
					$all_size_arr[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
					$po_country_color_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf('item_number_id')]][$row[csf("color_number_id")]]=$row[csf("style_ref_no")].'***'.$row[csf("season_buyer_wise")].'***'.$row[csf("style_description")].'***'.$row[csf("ship_mode")].'***'.$row[csf("po_number")]."***".$row[csf('grouping')]."***".$row[csf('item_number_id')];//

					$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf('item_number_id')]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['qty']+=$row[csf("order_quantity")];
					//$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['rate']+=$row[csf("order_rate")];
					$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf('item_number_id')]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['amount']+=$row[csf("order_total")];


				}
				unset($main_result);// die;

				$poIds=chop($poIds,','); $poIds_yarn_cond=""; $poIds_insp_cond="";
				if($db_type==2 && $tot_rows>1000)
				{
					$poIds_yarn_cond=" and (";
					$poIds_insp_cond=" and (";

					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$poIds_yarn_cond.=" b.po_break_down_id in($ids) or ";
						$poIds_insp_cond.=" po_break_down_id in($ids) or ";
					}
					$poIds_yarn_cond=chop($poIds_yarn_cond,'or ');
					$poIds_yarn_cond.=")";

					$poIds_insp_cond=chop($poIds_insp_cond,'or ');
					$poIds_insp_cond.=")";
				}
				else
				{
					$poIds_yarn_cond=" and b.po_break_down_id in ($poIds)";
					$poIds_insp_cond=" and po_break_down_id in ($poIds)";
				}

				$fabrication_data_arr=array();
				$fabrication_sql=sql_select("select a.fabric_description, b.po_break_down_id from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.cons>0 $poIds_yarn_cond");
				$fabrication_data_arr=array();
				foreach($fabrication_sql as $row)
				{
					$fabrication_data_arr[$row[csf("po_break_down_id")]].=$row[csf("fabric_description")].",";
				}
				unset($fabrication_sql);

				$insp_date_arr=array();
				$insp_sql=sql_select("select po_break_down_id, country_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_insp_cond group by po_break_down_id, country_id");
				foreach($insp_sql as $row)
				{
					$insp_date_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]]=$row[csf("inspection_date")];
				}
				unset($insp_sql);

				$size_count=count($all_size_arr);
				$width=(80*$size_count)+1650;
				$job_count_arr=array(); $po_count_arr=array(); $date_count_arr=array(); $country_count_arr=array();$item_count_arr=array();

				foreach($po_country_color_arr as $job=>$job_data)
				{
					$jobc=0;
					foreach($job_data as $po_id=>$po_data)
					{
						$poc=0;
						foreach($po_data as $ship_date=>$date_data)
						{
							$datec=0;
							foreach($date_data as $country_id=>$country_data)
							{
								$countryc=0;
								foreach($country_data as $item_number_id=>$item_data)
								{
									foreach ($item_data as $color_id=>$other_val) 
									{
										$jobc++; $poc++; $datec++; $countryc++;
										$job_count_arr[$job]=$jobc;
										$po_count_arr[$job][$po_id]=$poc;
										$date_count_arr[$job][$po_id][$ship_date]=$datec;
										$country_count_arr[$job][$po_id][$ship_date][$country_id]=$countryc;
										$ext=explode("***", $other_val);
										$item_count_arr[$job][$po_id][$ship_date][$country_id][$ext[6]]++;
									}
								}
							}
						}
					}
				}
				// echo "<pre>";
				// print_r($item_count_arr);
				// echo "</pre>";

			?>
			<table width="<? echo $width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">IMAGE</th>
                        <th width="100" rowspan="2">JOB NO</th>
                        <th width="100" rowspan="2">Int. ref.</th>
                        <th width="100" rowspan="2">STYLE</th>
                        <th width="80" rowspan="2">SEASON</th>
                        <th width="100" rowspan="2">STYLE DESCRIPTION</th>
                        <th width="70" rowspan="2">SHIP MODE</th>

                        <th width="100" rowspan="2">PO NO.</th>
                        <th width="130" rowspan="2">Yarn Composition</th>
                        <th width="70" rowspan="2">SHIP DATE</th>
                        <th width="100" rowspan="2">COUNTRY</th>
                        <th width="100" rowspan="2">Gmt Item</th>
                        <th width="90" rowspan="2">COLOR</th>
                        <th colspan="<? echo $size_count; ?>">SIZE QTY</th>
                        <th width="90" rowspan="2">COLOR QTY</th>
                        <th width="80" rowspan="2">AVG. FOB ($)</th>
                        <th width="100" rowspan="2">AMOUNT ($)</th>
						<th rowspan="2">INSPECTION DATE</th>
                    </tr>
                    <tr>
                    	<?
							foreach($all_size_arr as $size_id)
							{
								?>
                                	<th width="80"><? echo $size_arr[$size_id]; ?></th>
                                <?
							}
						?>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="<? echo $width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
				<?
				 $style_size_sum_arr=array(); $size_sum_arr=array(); $style_size_sumamt_arr=array(); $size_sumamt_arr=array(); $temp_arr_job=array();
				 $k=1; $m=1;
				foreach($po_country_color_arr as $job=>$job_data)
				{
					$jobcount=$job_count_arr[$job]; $jobr=1;
					foreach($job_data as $po_id=>$po_data)
					{
						$pocount=$po_count_arr[$job][$po_id]; $por=1;
						foreach($po_data as $ship_date=>$date_data)
						{
							$datecount=$date_count_arr[$job][$po_id][$ship_date]; $sdater=1;
							foreach($date_data as $country_id=>$country_data)
							{
								$countrycount=$country_count_arr[$job][$po_id][$ship_date][$country_id]; $countryr=1;
								$itm_pre="";

								foreach($country_data as $item_number_id=>$item_data)
								{
									foreach ($item_data as $color_id=>$other_val) 
									{
										
									
										if($m%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$ex_val=explode('***',$other_val); $style_ref_no=''; $season_buyer_wise=0; $style_description=''; $ship_mode=0; $po_number='';

										$style_ref_no=$ex_val[0];
										$season_buyer_wise=$ex_val[1];
										$style_description=$ex_val[2];
										$ship_mode=$ex_val[3];
										$po_number=$ex_val[4];
										$internal_ref=$ex_val[5];
										$item_id=$ex_val[6];
										$item_count=$item_count_arr[$job][$po_id][$ship_date][$country_id][$item_id];
										if(!in_array($job,$temp_arr_job))
										{
											$temp_arr_job[]=$job;
											if($k!=1)
											{
												?>
												<tr bgcolor="#CCCCCC">
													<td width="30">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="80">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="70">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="130" align="right">Style Total :</td>
	                                                <td width="70">&nbsp;</td>
	                                                <td width="100">&nbsp;</td>
	                                                <td width="90">&nbsp;</td>
	                                                <td width="90">&nbsp;</td>
	                                                <?
	                                                    $subcolor_qty=0; $subcolor_amt=0;
	                                                    foreach($all_size_arr as $size_id)
	                                                    {
	                                                        $subsize_qty=0; $subsize_amount=0;
	                                                        $subsize_qty=$style_size_sum_arr[$size_id];
	                                                        $subsize_amount=$style_size_sumamt_arr[$size_id];
	                                                        ?>
	                                                            <td width="80" align="right" style='word-break:break-all'><? if ($subsize_qty!="") echo number_format($subsize_qty,2); else echo ''; ?></td>
	                                                        <?
	                                                        $subcolor_qty+=$subsize_qty;
	                                                        $subcolor_amt+=$subsize_amount;
	                                                    }
	                                                ?>
	                                                <td width="90" align="right"><? echo number_format($subcolor_qty,2); ?></td>
	                                                <td width="80">&nbsp;</td>
	                                                <td width="100" align="right"><? echo number_format($subcolor_amt,2); ?></td>
	                                                <td>&nbsp;</td>
												</tr>
												<?
												unset($style_size_sum_arr);
												unset($style_size_sumamt_arr);
											}
											$k++;
										}
										//echo $jobcount.'='.$pocount.'='.$datecount.'='.$countrycount.'='.$colorcount.'<br>';
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
	                                    	<? if($jobr==1) 
	                                    	{

		                                    		?>
		                                        <td width="30" align="center" rowspan="<? echo $jobcount; ?>"><? echo $m;?></td>
		                                        <td width="100" rowspan="<? echo $jobcount; ?>" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $job; ?>','Image View')"><img  src='../../../<? echo $imge_arr[$job]; ?>' height='25' width='80' /></td>
		                                        <td width="100" rowspan="<? echo $jobcount; ?>"><p><? echo $job; ?></p></td>
		                                       			 <? 
	                                        }
	                                       if($por==1) 
	                                       {
	                                       			 ?>
	                                        	<td width="100" rowspan="<? echo $pocount; ?>"><p><? echo $internal_ref; ?></p></td>

	                                       			 <?php 
	                                    	}
	                                        if($jobr==1)
	                                        { 


		                                         ?>
		                                        <td width="100" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $style_ref_no; ?></td>
		                                        <td width="80" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $season_arr[$season_buyer_wise]; ?></td>
		                                        <td width="100" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $style_description; ?></td>
		                                        <td width="70" rowspan="<? echo $jobcount; ?>"><? echo $shipment_mode[$ship_mode]; ?></td>
		                                        	<? $m++; 
		                                    } 
		                                    if($por==1) 
		                                    {
		                                    	?>
		                                        <td width="100" rowspan="<? echo $pocount; ?>" style='word-break:break-all'><? echo $po_number; ?></td>
		                                        <td width="130" rowspan="<? echo $pocount; ?>" style='word-break:break-all'><p><? $fabrication_data=implode(", ",array_unique(explode(",",chop($fabrication_data_arr[$po_id]," , ")))); echo $fabrication_data; ?>&nbsp;</p></td>
		                                       		 <? 
	                                    	}
	                                         if($sdater==1) {?>
	                                        <td width="70" rowspan="<? echo $datecount; ?>"><? echo change_date_format($ship_date); ?></td>
	                                        <? } if($countryr==1) {?>
	                                        <td width="100" rowspan="<? echo $countrycount; ?>" style='word-break:break-all'><? echo $country_arr[$country_id]; ?></td>
	                                        <? } ?>
	                                        <? if($itm_pre!=$item_id || $itm_pre==""){?>
	                                        <td width="100" rowspan="<? echo $item_count; ?>" style='word-break:break-all'><? echo $garments_item[$item_id]; ?></td>
	                                        <?}?>
	                                        <td width="90" style='word-break:break-all'><? echo $color_arr[$color_id]; ?></td>
	                                        <?
												$color_qty=0; $color_amt=0;
												foreach($all_size_arr as $size_id)
												{
													$size_qty=0; $size_amount=0;
													$size_qty=$po_country_color_size_arr[$job][$po_id][$ship_date][$country_id][$item_number_id][$color_id][$size_id]['qty'];
													$size_amount=$po_country_color_size_arr[$job][$po_id][$ship_date][$country_id][$item_number_id][$color_id][$size_id]['amount'];
													?>
														<td width="80" align="right" style='word-break:break-all'><? if($size_qty!='') echo number_format($size_qty,2); else echo ""; ?></td>
													<?
													$color_qty+=$size_qty;
													$color_amt+=$size_amount;
													$size_sum_arr[$size_id]+=$size_qty;
													$style_size_sum_arr[$size_id]+=$size_qty;
													$style_size_sumamt_arr[$size_id]+=$size_amount;
												}
											?>
	                                        <td width="90" align="right"><? echo number_format($color_qty,2); ?></td>
	                                        <td width="80" align="right"><? $color_fob=0; $color_fob=$color_amt/$color_qty; echo number_format($color_fob,2); ?></td>
	                                        <td width="100" align="right"><? $gcolor_amt+=$color_amt; echo number_format($color_amt,2); ?></td>
	                                        <td>
	                                        	<?
												$inspection_date='';
												$inspection_date=$insp_date_arr[$po_id][$country_id];
	                                         echo change_date_format($inspection_date); ?>&nbsp;
	                                        </td>
	                                    </tr>
	                                    <?
	                                     $jobr++; $por++; $sdater++; $countryr++; $k++;
	                                     $itm_pre=$item_id;
	                                 }
								}
							}
						}
					}
				}
				?>
				<tr bgcolor="#CCCCCC">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="130" align="right">Style Total :</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <?
                        $subcolor_qty=0; $subcolor_amt=0;
                        foreach($all_size_arr as $size_id)
                        {
                            $subsize_qty=0; $subsize_amount=0;
                            $subsize_qty=$style_size_sum_arr[$size_id];
                            $subsize_amount=$style_size_sumamt_arr[$size_id];
                            ?>
                                <td width="80" align="right" style='word-break:break-all'><? if($subsize_qty!="") echo number_format($subsize_qty,2); else echo ""; ?></td>
                            <?
                            $subcolor_qty+=$subsize_qty;
                            $subcolor_amt+=$subsize_amount;
                        }
                    ?>
                    <td width="90" align="right"><? echo number_format($subcolor_qty,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="100" align="right"><? echo number_format($subcolor_amt,2); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        </div>
        <table width="<? echo $width; ?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
        <thead>
            <tr bgcolor="#CCCCCC">
                <td width="30">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="130">Buyer Total :</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <?
                    $gcolor_qty=0;
                    foreach($all_size_arr as $size_id)
                    {
                        $gsize_qty=0;
                        $gsize_qty=$size_sum_arr[$size_id];
                        ?>
                            <td width="80" align="right"><? echo number_format($gsize_qty,2); ?></td>
                        <?
                        $gcolor_qty+=$gsize_qty;
                    }
                ?>
                <td width="90" align="right"><? echo number_format($gcolor_qty,2); ?></td>
                <td width="80">&nbsp;</td>
                <td width="100" align="right"><? echo number_format($gcolor_amt,2); ?></td>
                <td>&nbsp;</td>
            </tr>
        </thead>
	    </table>
	    </div>
	    <?
	}
	else if($rpt_type==4)//Short2
	{
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$Dealing_marcent_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
		$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_short_name_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		?>
        <div style="width:1940px">
            <table width="1940" cellpadding="0" cellspacing="0" id="caption"  align="left">
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:18px"><? echo $report_title.'[Short2]'; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:14px">From <? echo change_date_format($start_date); ?> To <? echo change_date_format($end_date); ?> </strong></td>
                </tr>
            </table>
            <br />
            <table width="2040" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" align="left">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">Company</th>
                        <th width="100">Buyer</th>
						<th width="100">Brand</th>
                        <th width="110">Style No</th>
                        <th width="110">PO No</th>
                        <th width="80">Internal Ref.No</th>
                        <th width="100">Dealing Merchant</th>
                        <th width="130">Item Description</th>
                        <th width="70">GSM</th>
                        <th width="250">Fabrication</th>
                        <th width="100">Order Qnty(Pcs)</th>
                        <th width="70"><? if($category_by==1) echo "Ship Date"; elseif($category_by==2) echo "PO Receive Date"; ?></th>
                        <th width="50">Unit Price</th>
                        <th width="100">FOB Price</th>
                        
                        <th width="80">CM/PCS</th>
                        <th width="80">Total CM Value</th>
                        <th width="80">SMV/PCS</th>
                        <th width="100">Total SMV Min</th>
                        
                        <th width="100">Remarks</th>
						<th>User Name</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2040px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="2020" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <tbody>
                <?
                if($category_by==1)
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
                }
                else
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
                }

				$fabrication_sql=sql_select("select a.fabric_description, b.po_break_down_id,a.gsm_weight from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.cons>0");
				$fabrication_data_arr=array();
				foreach($fabrication_sql as $row)
				{
					$fabrication_data_arr[$row[csf("po_break_down_id")]].=$row[csf("fabric_description")].",";
					$gsm_data_arr[$row[csf("po_break_down_id")]].=$row[csf("gsm_weight")].",";
				}
				unset($fabrication_sql);
				
				$sql_bom="select job_no, costing_per_id, cm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0";
				$sql_bomRes=sql_select($sql_bom);
				$bomData_arr=array();
				foreach($sql_bomRes as $row)
				{
					$costingPer=$row[csf('costing_per_id')];
					$costingPerQty=0; $cmCostPcs=0;
					if($costingPer==1) $costingPerQty=12;
					if($costingPer==2) $costingPerQty=1;
					if($costingPer==3) $costingPerQty=24;
					if($costingPer==4) $costingPerQty=36;
					if($costingPer==5) $costingPerQty=48;
					
					$cmCostPcs=$row[csf("cm_cost")]/$costingPerQty;
					
					$bomData_arr[$row[csf("job_no")]]['costing_per']=$costingPerQty;
					$bomData_arr[$row[csf("job_no")]]['cmpcs']=$cmCostPcs;
				}
				unset($sql_bomRes);
				//$file_cond  $ref_cond $rpt_type
                $main_sql="SELECT a.company_name, a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, (a.set_smv/a.total_set_qnty) as smvpcs, a.remarks,a.brand_id, b.id as po_id, b.po_number, b.grouping, b.inserted_by, b.po_received_date, b.shipment_date, (a.total_set_qnty*b.po_quantity) as po_quantity_pcs, (b.unit_price/a.total_set_qnty) as unit_price_pcs, a.dealing_marchant from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name in ($company_name) $date_cond $search_string_cond $year_cond $shipment_status_cond $buyer_id_cond $order_confirm_status_con $season_cond $file_cond $ref_cond $brand_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by a.buyer_name, a.id, b.id";
				//echo $main_sql; die;
                $main_result=sql_select($main_sql);
                $k=1;$m=1;
                $temp_arr_buyer=array();
                foreach($main_result as $row)
                {
					$po_total_price=0;
                    if(!in_array($row[csf("buyer_name")],$temp_arr_buyer))
                    {
                        $temp_arr_buyer[]=$row[csf("buyer_name")];
                        if($k!=1)
                        {
                            ?>
                            <tr bgcolor="#CCCCCC">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">Sub Total:&nbsp;</td>
                                <td align="right"><? echo number_format($buyer_tot_qnty); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><? echo number_format($buyer_tot_price,2); ?></td>
                                
                                <td>&nbsp;</td>
                                <td align="right"><?=number_format($buyer_cmvalue,2); ?></td>
                                <td>&nbsp;</td>
                                <td align="right"><?=number_format($buyer_smvvalue,4); ?></td>
                                <td>&nbsp;</td>
								<td>&nbsp;</td>
                            </tr>
                            <?
							unset($buyer_tot_qnty);
							unset($buyer_tot_price);
							
							unset($buyer_cmvalue);
							unset($buyer_smvvalue);
                        }
                        $k++;
                    }
                    if($m%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_total_price=$row[csf("po_quantity_pcs")]*$row[csf("unit_price_pcs")];
					$gsm_data=implode(", ",array_unique(explode(",",chop($gsm_data_arr[$row[csf("po_id")]]," , "))));
					$fabrication_data=implode(", ",array_unique(explode(",",chop($fabrication_data_arr[$row[csf("po_id")]]," , "))));
					
					$cmPcs=$rowCmValue=$rowSmvMin=0;
					$cmPcs=$bomData_arr[$row[csf("job_no")]]['cmpcs']/$row[csf("total_set_qnty")];
					$rowCmValue=$cmPcs*$row[csf("po_quantity_pcs")];
					$rowSmvMin=$row[csf("smvpcs")]*$row[csf("po_quantity_pcs")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                        <td width="40" align="center"><?=$m;?></td>
                        <td width="100" style="word-break:break-all;"><?=$company_short_name_arr[$row[csf("company_name")]]; ?></td>
                        <td width="100" style="word-break:break-all;"><?=$buyer_arr[$row[csf("buyer_name")]]; ?></td>
						<td width="100" style="word-break:break-all;"><?=$brand_arr[$row[csf("brand_id")]]; ?></td>
                        <td width="110" style="word-break:break-all;"><?=$row[csf("style_ref_no")]; ?></td>
                        <td width="110" style="word-break:break-all;"><?=$row[csf("po_number")]; ?></td>
                        <td width="80" style="word-break:break-all;"><?=$row[csf("grouping")];?></td>
                        <td width="100" style="word-break:break-all;"><?=$Dealing_marcent_arr[$row[csf("dealing_marchant")]]; ?></td>
                        <td width="130" style="word-break:break-all;">
						<?
							$garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
							$all_garments_item="";
							foreach($garments_item_arr as $garments_item_id)
							{
								$all_garments_item.=$garments_item[$garments_item_id].", ";
							}
							$all_garments_item=chop($all_garments_item," , ");
							echo $all_garments_item;
						?></td>
                        <td width="70" style="word-break:break-all;"><?=$gsm_data; ?></td>
                        <td width="250" style="word-break:break-all;"><?=$fabrication_data; ?></td>
                        <td width="100" align="right"><?=number_format($row[csf("po_quantity_pcs")]); ?></td>
                        <td width="70" style="word-break:break-all;" align="center">
							<?
                            if($category_by==1)
                            {
                                if($row[csf("shipment_date")]!="" && $row[csf("shipment_date")]!="0000-00-00") echo change_date_format($row[csf("shipment_date")]);
                            }
                            else if($category_by==2)
                            {
                                if($row[csf("po_received_date")]!="" && $row[csf("po_received_date")]!="0000-00-00") echo change_date_format($row[csf("po_received_date")]);
                            }
                            ?>
                        </td>
                        <td width="50" align="right"><?=number_format($row[csf("unit_price_pcs")],2) ?></td>
                        <td width="100" align="right"><?=number_format($po_total_price,2); ?></td>
                        
                        <td width="80" align="right"><?=number_format($cmPcs,4); ?></td>
                        <td width="80" align="right"><?=number_format($rowCmValue,2); ?></td>
                        <td width="80" align="right"><?=number_format($row[csf("smvpcs")],2); ?></td>
                        <td width="100" align="right"><?=number_format($rowSmvMin,4); ?></td>
                        
                        <td width="100" style="word-break:break-all;"><?=$row[csf("remarks")]; ?></td>
						<td style="word-break:break-all;"><?=$user_name_arr[$row[csf('inserted_by')]]; ?></td>
                    </tr>
                    <?
					$m++;
					$buyer_tot_qnty+=$row[csf("po_quantity_pcs")];
					$total_po_qnty+=$row[csf("po_quantity_pcs")];
					$buyer_tot_price+=$po_total_price;
					$total_po_price+=$po_total_price;
					
					$buyer_cmvalue+=$rowCmValue;
					$total_cmvalue+=$rowCmValue;
					
					$buyer_smvvalue+=$rowSmvMin;
					$total_smvvalue+=$rowSmvMin;
                }
                ?>
                    <tr bgcolor="#DDDDDD">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Sub Total:&nbsp;</td>
                        <td align="right"><? echo number_format($buyer_tot_qnty); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($buyer_tot_price,2); ?></td>
                        
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=number_format($buyer_cmvalue,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><?=number_format($buyer_smvvalue,4); ?></td>
                        
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Grand Total:&nbsp;</td>
                        <td align="right"><? echo number_format($total_po_qnty); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_po_price,2); ?></td>
                        
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($total_cmvalue,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($total_smvvalue,4); ?></td>
                        
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <?
	}
	else if($rpt_type==5)//PCD
	{
		ob_start();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$Dealing_marcent_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
		$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
		$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_short_name_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$color_arr=return_library_array("select id, color_name from lib_color", "id", "color_name");
		
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1 and FORM_NAME in( 'quotation_inquery_front_image','quotation_inquery_back_image','quotation_inquery_back_image','quotation_inquery_front_image') order by id asc",'master_tble_id','image_location');

		
		
		
		if($year_id!=0){$season_year_cond=" and a.season_year=$year_id";}
		$width=2050;
		?>
        <div style="width:<? echo $width;?>px">
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption"  align="left">
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:18px"><? echo $report_title.'[Short2]'; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="form_caption" colspan="20"><strong style="font-size:14px">From <? echo "&nbsp;".change_date_format($start_date); ?> To <? echo "&nbsp;".change_date_format($end_date); ?> </strong></td>
                </tr>
            </table>
            <br />
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" align="left">
                <caption><b style="float:left"> Po Summary</b> </caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="50">LC Company</th>
                        <th width="50">Manu. Company</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Deling Merchant</th>
                        <th width="100">Buyer</th>
                        <th width="70">Brand</th>
                        <th width="70">Season</th>
                        <th width="50">Season Year</th>
                        <th width="130">Master Style NO</th>
                        <th width="100">Body/Wash Color</th>
                        <th width="100">Merch Style</th>
                        <th width="100">PO No</th>
                        <th width="70">Prod Department</th>
                        <th width="100">Product Name</th>
                        <th width="35">Image</th>
                        <th width="100">Style Description</th>
                        <th width="70">Category<? //if($category_by==1) echo "Ship Date"; elseif($category_by==2) echo "PO Receive Date"; ?></th>
                        <th width="70">Order Qty. </th>
                        <th width="50">UOM</th>
                        
                        <th width="70">Avg Unit Price</th>
                        <th width="80">Oder Value</th>
                        <th width="70">PCD</th>
                        <th width="80">Ship Cancel</th>
                        <th width="50">SMV</th>
                        <th width="60">PO Lead Time</th>
						<th>Wash Type</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $width+20;?>px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <tbody>
                <? 
                if($category_by==1)
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and b.shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
                }
                else
                {
                    if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
                }
				//$year_cond
				
				$embl_sql=sql_select("select b.id as po_id,c.emb_name, c.emb_type,c.job_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $date_cond $search_string_cond $order_confirm_status_con $buyer_id_cond $season_cond $file_cond $ref_cond  $brand_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' and c.emb_name=3 order by a.buyer_name, a.id, b.id");
				//echo "select b.id as po_id,c.emb_name, c.emb_type,c.job_no from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $date_cond $search_string_cond $year_cond $buyer_id_cond $season_cond $file_cond $ref_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' and c.emb_name=3 order by a.buyer_name, a.id, b.id";
				$wash_type_data_arr=array();
				foreach($embl_sql as $row)
				{
					//$fabrication_data_arr[$row[csf("po_break_down_id")]].=$row[csf("fabric_description")].",";
					$wash_type_data_arr[$row[csf("po_id")]].=$emblishment_wash_type[$row[csf("emb_type")]].",";
				}
				unset($embl_sql);
				
				//$file_cond  $ref_cond $rpt_type
                $main_sql="SELECT a.INQUIRY_ID,a.season_year,a.company_name, a.id as job_id, a.job_no_prefix_num, a.job_no,a.style_owner,a.brand_id, a.product_dept,a.product_category,a.order_uom,a.buyer_name,a.style_description,a.team_leader, a.style_ref_no, a.gmts_item_id, a.total_set_qnty,a.season_buyer_wise, (a.set_smv/a.total_set_qnty) as smvpcs, a.remarks, b.id as po_id, b.po_number, b.grouping, b.inserted_by, b.po_received_date,b.pub_shipment_date, b.shipment_date, (a.total_set_qnty*b.po_quantity) as po_quantity_pcs, (b.unit_price/a.total_set_qnty) as unit_price_pcs, a.dealing_marchant,b.grouping,b.is_confirmed,a.BODY_WASH_COLOR from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name in ($company_name) $order_confirm_status_con $date_cond $search_string_cond $shipment_status_cond $brand_cond $season_year_cond $buyer_id_cond $season_cond $file_cond $ref_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by a.buyer_name, a.id, b.id";
				//echo $main_sql; die;
                $main_result=sql_select($main_sql);
                $k=1;$m=1;
                $temp_arr_buyer=array();
                foreach($main_result as $row)
                {
					if($row[csf("is_confirmed")]==2 && $row[csf("po_quantity_pcs")]<1){continue;}
					
					
					$po_total_price=0;
                   
                    if($m%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_total_price=$row[csf("po_quantity_pcs")]*$row[csf("unit_price_pcs")];
					$gsm_data=implode(", ",array_unique(explode(",",chop($gsm_data_arr[$row[csf("po_id")]]," , "))));
					$fabrication_data=implode(", ",array_unique(explode(",",chop($fabrication_data_arr[$row[csf("po_id")]]," , "))));
					
					$cmPcs=$rowCmValue=$rowSmvMin=0;
					$cmPcs=$bomData_arr[$row[csf("job_no")]]['cmpcs']/$row[csf("total_set_qnty")];
					$rowCmValue=$cmPcs*$row[csf("po_quantity_pcs")];
					$rowSmvMin=$row[csf("smvpcs")]*$row[csf("po_quantity_pcs")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                        <td width="20" align="center"><?=$m;?></td>
                        <td width="50" style="word-break:break-all;"><?=$company_short_name_arr[$row[csf("company_name")]]; ?></td>
                        <td width="50" style="word-break:break-all;"><?=$company_short_name_arr[$row[csf("style_owner")]];//$buyer_arr[$row[csf("buyer_name")]]; ?></td>
                        <td width="100" style="word-break:break-all;"><?=$team_leader_arr[$row[csf("team_leader")]];//$row[csf("style_ref_no")]; style_owner?></td>
                        <td width="100" style="word-break:break-all;"><?=$Dealing_marcent_arr[$row[csf("dealing_marchant")]];//$row[csf("po_number")]; ?></td>
                        <td width="100" style="word-break:break-all;"><?=$buyer_arr[$row[csf("buyer_name")]];?></td>
                        <td width="70" style="word-break:break-all;"><?=$brand_name_arr[$row[csf("brand_id")]];?></td>
                        
                        <td width="70" style="word-break:break-all;"><?=$buyer_wise_season_arr[$row[csf("season_buyer_wise")]];?></td>
                        <td width="50" style="word-break:break-all;"><?=$row[csf("season_year")];?></td>
                        <td width="130" style="word-break:break-all;"><?=$row[csf("grouping")];?></td>
                        <td width="100" align="center"><?=$color_arr[$row[BODY_WASH_COLOR]]?></td>
                        <td width="100" style="word-break:break-all;"><?=$row[csf("style_ref_no")]; ?></td>
                        <td width="100" style="word-break:break-all;">
						<?
							
							echo $row[csf("po_number")];
							
							$garments_item_arr=explode(",",$row[csf("gmts_item_id")]);
							$all_garments_item="";
							foreach($garments_item_arr as $garments_item_id)
							{
								$all_garments_item.=$garments_item[$garments_item_id].", ";
							}
							$all_garments_item=chop($all_garments_item," , ");
							//echo $all_garments_item;
						?></td>
                        <td width="70" style="word-break:break-all;"><?=$product_dept[$row[csf("product_dept")]]; ?></td>
                        <td width="100" style="word-break:break-all;"><?=$all_garments_item; ?></td>
                        
                        <td width="35" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image2&job_no=<? echo $row[("INQUIRY_ID")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('INQUIRY_ID')]]; ?>' height='25' width='30' /></td>
                        
                        
                        
                        <td width="100" align="center" style="word-break:break-all;"><?=$row[csf("style_description")];//$product_category[$row[csf("product_category")]];//number_format($row[csf("style_description")]); ?></td>
                        <td width="70" style="word-break:break-all;" align="center">
							<?
							
							echo $product_category[$row[csf("product_category")]];//$row[csf("style_ref_no")];
							
							$wash_type=rtrim($wash_type_data_arr[$row[csf("po_id")]],',');
							$wash_types=implode(",",array_unique(explode(",",$wash_type)));
                            ?>
                        </td>
                        <td width="70" align="right"><?=number_format($row[csf("po_quantity_pcs")]);//number_format($row[csf("unit_price_pcs")],2) ?></td>
                        
                        <td width="50" align="center"><?=$unit_of_measurement[$row[csf("order_uom")]];//number_format($po_total_price,2); ?></td>
                        
                        <td width="70" align="right"><?=number_format($row[csf("unit_price_pcs")],2); ?></td>
                       
                        <td width="80" align="right"><?=number_format($po_total_price,2); ?></td>
                        <td width="70" align="center" title="<? echo $row[csf("shipment_date")].',Calculate=35 days';?>"><? $pcd_date=add_date($row[csf("shipment_date")],-35); echo "&nbsp;".change_date_format($pcd_date);?></td>
                        <td width="80" align="center"><?="&nbsp;".change_date_format($row[csf("pub_shipment_date")]); ?></td>
                        
                        <td width="50" align="right"><?=number_format($row[csf("smvpcs")],2); ?></td>
                        <td width="60" align="right" title="PO Recv Date=<? echo $row[csf('po_received_date')];?>"><? 	$po_lead_time=datediff("d",$row[csf('po_received_date')], $row[csf('pub_shipment_date')]);echo $po_lead_time; // ?></td>
						<td style="word-break:break-all;"><?=$wash_types; ?></td>
                    </tr>
                    <?
					$m++;
				//	$buyer_tot_qnty+=$row[csf("po_quantity_pcs")];
					$total_po_qnty+=$row[csf("po_quantity_pcs")];
				//	$buyer_tot_price+=$po_total_price;
					$total_po_price+=$po_total_price;
					
					
                }
                ?>
                    
                    <tr bgcolor="#CCCCCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                       
                        <td align="right"></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Grand Total:&nbsp;</td>
                        <td align="right"><? echo number_format($total_po_qnty); ?></td>
                        <td align="right"><? //echo number_format($total_po_price,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($total_po_price,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? //=number_format($total_smvvalue,4); ?></td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <?
	}
	else if($rpt_type==6)//Details 2
	{
		
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
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
		$condition= new condition();
			$condition->company_name("=$company_name");
		  if(str_replace("'","",$buyer_name)>0){
			  $condition->buyer_name("=$buyer_name");
		 }
		 //$txt_file=str_replace("'","",$txt_file);
		//$txt_ref
		 if($search_string!='' || $search_string!=0)
		 {
			$condition->po_number("in('$search_string')");
		 } 
		 if(str_replace("'","",$txt_ref)!='')
		 {
				$condition->grouping("='$txt_ref'"); 
		 }
		 if(str_replace("'","",$txt_file)!='')
		 {
			$condition->file_no("in('$txt_file')");
		 }
		 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
				  
				   if($category_by==1)
					{
						//if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==2)
					{
						//if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->po_received_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==3)
					{
					   if($db_type==0)
						{
						 $condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
						}
						else
						{
							$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
						}
					}
			 }
			 
		$condition->init();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order(); 
		
		//$commission= new commision($condition);
		//$commission_costing_sum_arr=$commission->getAmountArray_by_order();
		$margin_pcs_set_arr=return_library_array( "select job_no, margin_pcs_set from wo_pre_cost_dtls",'job_no','margin_pcs_set');
		
	  $sql_data="SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name,a.working_company_id, a.buyer_name,a.set_smv, a.agent_name, a.style_ref_no,a.style_description,a.brand_id, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where a.id=b.job_id and a.company_name in ($company_name) $order_confirm_status_con $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $shipment_status_cond $ref_cond $season_cond $brand_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no,a.style_description,a.brand_id, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom,a.set_smv,a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  $data_array=sql_select( $sql_data);
	  $all_po_id_arr=array();
	  foreach($data_array as $row) //
	  {
	  	$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
		$po_wise_arr[$row[csf('po_id')]]['working_company_id']=$row[csf('working_company_id')];
		$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_wise_arr[$row[csf('po_id')]]['brand_name']=$row[csf('brand_id')];
		$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
		$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
		
		$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
		$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
		$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
		$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
		$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
		$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
		$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
		$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
		$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
		$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
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
		$po_wise_arr[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_wise_arr[$row[csf('po_id')]]['style_description']=$row[csf('style_description')];
		$jobArr[$row[csf('job_no')]]="'".$row[csf('job_no')]."'";
		
		$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		//Company Buyer Wise
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
		$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		
		//Sumary
		$month_wise_arr[$pub_date_key]=$pub_date_key;
		$summ_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['smv_min']+=$row[csf('set_smv')]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['total_margin']+=$margin_pcs_set_arr[$row[csf('job_no')]]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		//echo $summ_cm_cost.'='.$row[csf('po_quantity')]*$row[csf('total_set_qnty')].',';
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['cm_value']+=$summ_cm_cost;
		$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
		
	  }
	 // asort($month_wise_arr);
		//  print_r($month_wise_arr);
		// $poIds=chop($all_po_id,','); 
		$poIds=implode(",", $all_po_id_arr); 
		$po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=$all_po_id_arr;
		// print_r($all_po_id_arr);die();
			if($db_type==2 && count($all_po_id_arr)>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$po_cond_for_in3=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or";
					$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
				$po_cond_for_in3=chop($po_cond_for_in3,'or ');
				$po_cond_for_in3.=")";
			}
			else
			{
				$po_cond_for_in=" and b.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
				$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
			}
 
		$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");

		/*echo "SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id";*/
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			//echo $shiping_status_id.', ';
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			//Buyer Wise
			//	$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			if($shiping_status_id==3)//Full shipped
			{
				//echo $row[csf('ex_factory_qnty')].'dd';
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			else if($shiping_status_id==2)//Partial shipped
			{
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			//$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['return_qty']=$row[csf('ex_factory_return_qnty')];
		}
		
		if($db_type==0)
			{
				$fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
			}
			else if($db_type==2)
			{
				$fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			}
			//echo "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no $po_cond_for_in2 ";
		//	echo  "select c.job_no,c.cm_for_sipment_sche as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ";die;
		//	$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');
		//	print_r($cm_for_shipment_schedule_arr);
		
		$sql_pre="SELECT a.costing_per,a.approved, c.job_no,c.cm_cost as cm_for_sipment_sche,c.margin_pcs_set, d.company_name, d.buyer_name,b.pub_shipment_date,b.id as po_id from  wo_pre_cost_mst a,wo_pre_cost_dtls c,wo_po_break_down b,wo_po_details_master d where a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and  c.job_no=b.job_no_mst  $po_cond_for_in2 ";
		 $data_budget_pre=sql_select($sql_pre);
			foreach ($data_budget_pre as $row)
			{
				
				$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
				$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']=$row[csf('margin_pcs_set')];
				$po_wise_margin_arr[$row[csf('po_id')]]['margin_pcs_set']=$row[csf('margin_pcs_set')];
				
				$job_approved_arr[$row[csf('job_no')]]=$row[csf('approved')];
			
			}
		  $sql_budget="SELECT a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		   $data_budget_array=sql_select($sql_budget);
		
			$fabric_arr=array();
			foreach ($data_budget_array as $row)
			{
				$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				if($row[csf('yarn_cons_qnty')]>0)
				{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			
				}
					//$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				//$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
			}
				//var_dump($fabric_arr);die;
				$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}

		foreach($actual_po_sql as $row)
		{
			$actual_po_no_arr[$row[csf('po_break_down_id')]]=$row[csf('acc_po_no')];
		}
		unset($actual_po_sql);
		//die;
		$sql_lc_result=sql_select("SELECT a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor,b.id,b.export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor,b.id,b.export_lc_no ");
		$lc_po_id="";
		foreach ($sql_lc_result as $row)
		{
			$lc_id_arr[$row['WO_PO_BREAK_DOWN_ID']] = $row['COM_EXPORT_LC_ID'];
			$lc_bank_arr[$row['WO_PO_BREAK_DOWN_ID']].= $row['LIEN_BANK'].',';
			$lc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['lc_number'].= $row['EXPORT_LC_NO'].',';
			$lc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['lc_id'].= $row['ID'].',';
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['file_no']= $row['INTERNAL_FILE_NO'];
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['pay_term']= $pay_term[$row['PAY_TERM']];
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['tenor']= $row['TENOR'];
			
				if($lc_po_id=="") $lc_po_id=$row['COM_EXPORT_LC_ID'];else $lc_po_id.=",".$row['COM_EXPORT_LC_ID'];
		}
		unset($sql_lc_result);
		$sql_sc_result=sql_select("SELECT a.wo_po_break_down_id,b.id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,b.id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
		foreach ($sql_sc_result as $row)
		{
			$sc_bank_arr[$row['WO_PO_BREAK_DOWN_ID']].= $row['LIEN_BANK'].',';
			$sc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['sc_number'].= $row['CONTRACT_NO'].',';
			$sc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['sc_id'].= $row['ID'].',';
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['file_no']= $row['INTERNAL_FILE_NO'];
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['pay_term']= $pay_term[$row['PAY_TERM']];
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['tenor']= $row['TENOR'];
		}
		unset($sql_sc_result);
						
		/* if($db_type==0)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		if($db_type==2)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		} */
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
			else
			{
				$lc_cond_for_in=" and export_lc_id in($lcIds)";
			}
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		
		
		
		$cut_qty_sql_res = sql_select("SELECT JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COUNTRY_SHIP_DATE,PLAN_CUT_QNTY  FROM WO_PO_COLOR_SIZE_BREAKDOWN where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."");
		$cut_qty_arr=array(); 
		foreach($cut_qty_sql_res as $rows)
		{
			
			$key=date("M-Y",strtotime($rows[COUNTRY_SHIP_DATE]));
			$comapny_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['company_name'];
			$buyer_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['buyer_name'];
			$cut_qty_by_month_arr[$comapny_id][$buyer_id][$key] += $rows[PLAN_CUT_QNTY];
			$cut_qty_dtls_arr[$rows[JOB_NO_MST]][$rows[PO_BREAK_DOWN_ID]] += $rows[PLAN_CUT_QNTY];
		}
		
		//var_dump($cut_qty_by_month_arr);die;
		
		
		$net_export_val_result=sql_select("select B.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/b.current_invoice_value)*b.current_invoice_qnty) AS PO_NET_INVO_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAKDOWN_ID')."");	

		foreach($net_export_val_result as $row){
			$net_export_val_arr[$row[PO_BREAKDOWN_ID]]=$row[PO_NET_INVO_VALUE];
		}
		
		//print_r($net_export_val_arr);die;		
		
		//=====================Sewing==cutting==packing==trims rcv==fabric rcv=============================

		$cut_and_lay_data=sql_select("SELECT c.po_break_down_id po_id,a.job_no_mst ,sum(b.production_qnty) as  production_quantity ,sum(b.reject_qty) as reject_qnty 
		from pro_garments_production_mst c,pro_garments_production_dtls b,wo_po_break_down a  
		where c.id=b.mst_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1
		 and c.production_type=b.production_type and b.is_deleted=0 and c.production_type=1
		  and c.po_break_down_id=a.id ".where_con_using_array($jobArr,0,'a.job_no_mst')."			  
		  group by c.po_break_down_id,a.job_no_mst");

		//  echo "SELECT a.order_qty,b.job_no,c.order_id as po_id, c.country_id,b.job_year,b.company_id, sum(c.marker_qty) marker_qty
		//  from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_size c 
		//   where b.id=a.mst_id ".where_con_using_array($jobArr,0,'b.job_no')."  and b.id=c.mst_id and c.dtls_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 			 
		//   group by a.order_qty,b.job_no,b.job_year,b.company_id, c.country_id  ,c.order_id";
		

		foreach($cut_and_lay_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['cutting_qty']+=$row[csf('production_quantity')];
			$po_wise_data_arr[$row[csf('po_id')]]['cutting_qty']+=$row[csf('production_quantity')];
		}


		$sweing_data=sql_select("SELECT sum (b.production_qnty) as production_quantity,c.job_no_mst,c.id as po_id
			  FROM pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_break_down c
			 WHERE  a.id = b.mst_id ".where_con_using_array($jobArr,0,'c.job_no_mst')." and c.id = a.po_break_down_id and a.production_type = '5' and a.status_active = 1 and a.is_deleted = 0 and b.production_type = '5' and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and (   b.color_size_break_down_id != 0	or b.color_size_break_down_id is not null)
			  group by c.job_no_mst,c.id");

			 
			foreach($sweing_data as $row){
				$job_wise_data_arr[$row[csf('job_no_mst')]]['sweing_out_qty']+=$row[csf('production_quantity')];
				$po_wise_data_arr[$row[csf('po_id')]]['sweing_out_qty']+=$row[csf('production_quantity')];
			}


		$packing_data=sql_select("SELECT  b.job_no_mst , sum(a.production_quantity) as production_quantity,b.id AS po_id
			from pro_garments_production_mst a,wo_po_break_down b
			 where  b.id = a.po_break_down_id and a.production_type='8' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($jobArr,0,'b.job_no_mst')." 
			  group by b.job_no_mst,b.id");
			 

		foreach($packing_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['packing_qty']+=$row[csf('production_quantity')];
			$po_wise_data_arr[$row[csf('po_id')]]['packing_qty']+=$row[csf('production_quantity')];
		}

		$trims_rcv_data=sql_select("SELECT max(a.id) id, a.receive_date,d.job_no_mst,d.id as po_id
		FROM inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c,wo_po_break_down d  
		WHERE c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active='1' 
		and b.is_deleted='0' ".where_con_using_array($jobArr,0,'d.job_no_mst')."  and c.po_breakdown_id=d.id
			GROUP BY  a.receive_date  ,d.job_no_mst,d.id");

		foreach($trims_rcv_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['last_accessories_date']=$row[csf('receive_date')];
			$po_wise_data_arr[$row[csf('po_id')]]['last_accessories_date']=$row[csf('receive_date')];
		}

	 $fabric_rcv_data=sql_select("SELECT max(a.id) id ,a.receive_date,d.job_no,d.po_break_down_id as po_id
	 from inv_receive_master a, inv_transaction c , wo_booking_dtls d where a.id=c.mst_id  ".where_con_using_array($jobArr,0,'d.job_no')."  and a.item_category=3 and a.entry_form in (37,17) and a.BOOKING_NO=d.BOOKING_NO and a.status_active=1  and c.status_active=1  and d.status_active=1 
     GROUP BY a.receive_date, d.job_no ,d.po_break_down_id");

			
		
				foreach($fabric_rcv_data as $row){
					$job_wise_data_arr[$row[csf('job_no')]]['last_fabric_date']=$row[csf('receive_date')];
					$po_wise_data_arr[$row[csf('po_id')]]['last_fabric_date']=$row[csf('receive_date')];
				}
		$tot_width=300+count($month_wise_arr)*400;		
		ob_start();
		?>
		<div align="center">
			<div align="center">
			<table width="<? echo $tot_width;?>" border="1" class="rpt_table" rules="all">
							<thead>
                            <tr>
                            <th colspan="3">&nbsp; </th>
                              <?
                                foreach($month_wise_arr as $date_key=>$val_data)
								{
								?>
								<th title="<? echo count($val_data);?>"  colspan="5"><? echo $date_key;?></th>
                                <?
								}
								?>
                            </tr>
                             <tr>
								<th width="20">SL</th>
                                <th width="100">Company Name</th>
								<th width="100">Buyer Name</th>
                                <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th width="80">Quantity(pcs)</th>
								<th width="80">Value </th>
								<th width="80">Total CM </th>
								<th width="80">Total Margin</th>
								<th width="80">Total Munit/SMV </th>
								<th width="80">PO Breakdown Qty(pcs)</th>
                                <?
								}
								?>
							</tr>
							</thead>
                           <tbody>
						<?
					
						 $k=1;
						foreach($comp_buyer_wise_arr as $company_key=> $comp_data)
						{
							foreach($comp_data as $buyer_key=> $row)
							{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('trsum_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $k; ?>">
								<td width="20" align="center"><? echo $k;//echo $company_name; ?></td>
                                <td width="100" align="center"><? echo $company_short_name_arr[$company_key];//echo $company_name; ?></td>
								<td width="100" align="center"><? echo $buyer_short_name_arr[$buyer_key];//echo $company_name; ?></td>
                                <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
									$po_quantity=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_quantity'];
									$po_total_price=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_total_price'];
									$cm_value=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['cm_value'];
									$total_margin=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['total_margin'];
									$smv_min=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['smv_min'];
									$cut_qty_by_month=$cut_qty_by_month_arr[$company_key][$buyer_key][$date_key];
								?>
								<td width="80" align="right"><? echo number_format($po_quantity,0); $total_po_qnty_arr[$date_key]+=$po_quantity;?></td>
								<td width="80" align="right"><? echo number_format($po_total_price,2); $total_po_value_arr[$date_key]+=$po_total_price;?></td>
								<td width="80" align="right" title="Order Qty*CM value"><? echo number_format($cm_value,2); $total_cm_value_arr[$date_key]+=$cm_value; ?> </td>
								<td width="80" align="right"  ><? echo number_format($total_margin,2); $total_margin_arr[$date_key]+=$total_margin; ?> </td>
								<td width="80" align="right" title="Order Qty*SMV"><? $total_smv_min_arr[$date_key]+=$smv_min; echo number_format($smv_min,2); ?></td>
                                <td width="90" align="right"><? $total_cut_qty_by_month_arr[$date_key]+=$cut_qty_by_month; echo $cut_qty_by_month; ?></td>
                                <?
								}
								?>
							</tr>
							
							<?
							$k++;
							}
						}
						?>
                        </tbody>
						<tfoot>
							<tr>
								
								<th align="center" colspan="3">Total:</th>
                                 <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th align="right"><? echo number_format($total_po_qnty_arr[$date_key],0); ?></th>
								<th align="right"><? echo number_format($total_po_value_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_cm_value_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_margin_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_smv_min_arr[$date_key],2); ?></th>
								<th align="right"><?= number_format($total_cut_qty_by_month_arr[$date_key],0);?></th>
                                <?
								}
								?>
								
							</tr>
							
						</tfoot>
			</table>
		<h3 style="width:4200px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
		<div id="content_report_panel">
        <? 
		
		if($search_by==1)
		{
			?>
			<table width="5600" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="100" >Working Company</th>
						<th width="70">Job No</th>
						<th width="90">Style Ref</th>
						<th width="100">Style Des</th>
						<th width="60">Year</th>
                        <th width="70">Approve Status</th>
						<th width="50">Buyer</th>
						<th width="50">Brand</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">Ref No</th>
						<th width="100">Season</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
                        <th width="40">File</th>
						<!-- <th width="90">Style Ref</th> -->
						<th width="150">Item</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="70">Lead Time</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qty(Pcs)</th>
						<th width="90">PO Breakdown Qty(pcs)</th>
						<th width="90">Order Qty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="100">LC/SC File</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>

						<th width="100">Last Accessories In-House Date</th>
						<th width="100">Last Fabric In-House Date</th>
						<th width="100">Cutting Qty </th>
						<th width="100">Sewing Qty</th>					
						<th width="100">Finishing </th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="90">Net Ex-Fac Val </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100">Margin(Pcs) </th>
						<th width="100">CM(Pcs)</th>
						<th width="100">Total Margin </th>
						<th width="100">SMV(Pcs) </th>
						<th width="100">SMV </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="40">Id</th>
						<th>Remarks</th>
						<th width="100">User Name</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:5620px"  align="left" id="scroll_body">
			<table width="5600" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
				
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
					//DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4
					//	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, b.id,b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					}
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

						// (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4
						/* $data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/

					}
					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
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
						$approved_id=$job_approved_arr[$row['job_no']];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}
						
						//echo $file_type_name.'DDDDD,';
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50"   bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('working_company_id')]];?></div></td>
							<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_description')];?></div></td>
							<td width="60"><p><? echo $row[('year')]; ?></p></td>
                            <td width="70" bgcolor="<? echo $color_app_td;?>"><p><? echo $msg_app; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')];?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
                            <td width="40">
                            
                             <? 
							 $file_type_name=$system_file_arr[$row[('job_no')]]['file'];
							 if($file_type_name!="")
							    {
							 ?>
                             <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[("job_no")] ?>','File View'),2"/>
                             <?
							   }
							  else echo " ";
							 ?>
                            </td>
							
							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
							<?
							$po_lead_time_diff = abs(strtotime($row[('pub_shipment_date')]) - strtotime($row[('po_received_date')]));								
							$po_lead_time = floor($po_lead_time_diff / (60*60*24));
							?>
							<td width="70"><div style="word-wrap:break-word; width:70px; align:center;"><? echo $po_lead_time;?></div></td>
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
                            <td width="90" align="right"><?= $cut_qty_dtls_arr[$row[('job_no')]][$po_id];?></td>
							<td width="90" align="right"><p>
								<?
								echo number_format( $row[('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
								
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								
								
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
								?></p></td>
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
								$lc_sc_no=$lc_sc_id=$lc_sc_type="";
								if($lc_number_arr[$po_id]["lc_number"] !="")
								{
									$lc_sc_no.="LC: ". $lc_number_arr[$po_id]["lc_number"];
									$lc_sc_id.=$lc_number_arr[$po_id]["lc_id"];
									$lc_sc_type.='1,';
								}
								if($sc_number_arr[$po_id]["sc_number"] !="")
								{
									$lc_sc_no.=" SC: ".$sc_number_arr[$po_id]["sc_number"];
									$lc_sc_id.=$sc_number_arr[$po_id]["sc_id"];
									$lc_sc_type.='2,';
								}
								echo implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
								$lc_sc_id=implode(",",array_unique(explode(",",chop($lc_sc_id,','))));
								$lc_sc_type=implode(",",array_unique(explode(",",chop($lc_sc_type,','))));
								?>
								</div>
							</td>
							<td width="100" align="center">								
								<?
									if($lc_sc_id!="")
									{
										?>
											<input type="button" class="image_uploader" id="system_id" style="width:40px" value="File" onClick="openmypage_file('requires/shipment_schedule_controller.php?action=file_show&mst_id=<?=$lc_sc_id;?>&lc_sc_type=<?=$lc_sc_type;?>','File View'),2"/>
										<?
									}
								?></td>
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

							<td width="100" align="center"><p><? 	echo $po_wise_data_arr[$po_id]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['last_fabric_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['cutting_qty'];$total_cutting_qty+=$po_wise_data_arr[$po_id]['cutting_qty'];  ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['sweing_out_qty'];$total_sweing_out_qty+=$po_wise_data_arr[$po_id]['sweing_out_qty'];  ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['packing_qty']; $total_packing_qty+=$po_wise_data_arr[$po_id]['packing_qty']; ?></p></td>

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
                            <td width="90" align="right"><? //ssssssss
							echo number_format($net_exfactory_val = $net_export_val_arr[$po_id],2);
							$net_tot_exfactory_val+=$net_exfactory_val;
							?></td>
                                
                                
							<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_access_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								<?
									$short_access_value=$short_access_qnty*$row[('unit_price')];
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>
							<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$po_wise_margin_arr[$po_id]['margin_pcs_set'];?></div></td>


							<td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs),2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>

							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$po_wise_margin_arr[$po_id]['margin_pcs_set']*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]); ?></div></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)/$row[csf('set_smv')],2); ?></div></td>
							<?
								
								if($row[('order_uom')]==58){									
									?>
							<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[('job_no')];?>', '<? echo $row[('id')]; ?>','500px')"><?echo number_format($row[('set_smv')],2);?></a></td>
							<?}else{?>
								<td width="100" align="right"><p><?echo number_format($row[('set_smv')],2);?></p></td>	<?	}?>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
							<td width="40"><p><? echo $po_id; ?></p></td>
							<td><p><? echo $row[('details_remarks')]; ?></p></td>
							<td width="100"><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="50" align="center" >  Total: </td>
						<td width="70" ></td>
						<td width="100"></td>
						<td width="70"></td>
						<td width="90"></td>
						<td width="100"></td>
						<td width="60"></td>
                        <td width="70"></td>
						<td width="50"></td>
						<td width="50"></td>
						<td width="110"></td>
                        <td width="100"></td>
                        <td width="100"></td>
						<td width="100"></td>
						<td width="50"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="40"></td>
                        <td width="40"></td>
						<td width="150"></td>
						<td width="200"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						
						<td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
                        <td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>

						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
                        <td width="100"></td>
                        <td width="100"></td>
				
						<td width="100"></td>
						<td width="90"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_cutting_qty,0); ?></td>
						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_sweing_out_qty,0); ?></td>
						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_packing_qty,0); ?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="90" align="right"><?=number_format($net_tot_exfactory_val,2);?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" ></td>
						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="40"></td>
						<td></th>
						<td width="100"></td>
					 </tr>
				<?
				
				?>
				</table>
				</div>
				<table width="5600" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70" ></th>
							<td width="100"></td>
							<th width="70"></th>
							<td width="90"></td>
							<td width="100"></td>
							<th width="60"></th>
                            <td width="70"></td>
							<th width="50"></th>
							<td width="50"></td>
							<th width="110"></th>
                            <th width="100"></th>
                            <th width="100"></th>
							<th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="40"></th>
                            <td width="40"></td>
							<th width="150"></th>
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<td width="70"></td>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            
							<th width="90"><?=$grand_total_cut_qty_dtls;?></th>
                            
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>

							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
                             <th width="100"></th>
                             <th width="100"></th>
						
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<td width="100"></td>
							<td width="100"></td>
							<th width="100" align="right"><? echo number_format($total_cutting_qty,0); ?></th>
							<th width="100" align="right"><? echo number_format($total_sweing_out_qty,0); ?></th>
							<th width="100" align="right"><? echo number_format($total_packing_qty,0); ?></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="90" align="right" id="net_total_ex_factory_value"></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" ></th>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="40"></th>
							<th></th>
							<th width="100"></th>
							<th width="100"></th>
						</tr>
					</tfoot>
				</table>
				<?
		}
		else
		{
			?>
			<table width="5580" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="80">SL</th>
						<th width="70" >Company</th>
						<th width="100" >Working Company</th>
						<th width="70">Job No</th>
						<th width="90">Style Ref</th>
						<th width="100">Style Des</th>
						<th width="60">Year</th>
                        <th width="70">Approve Status</th>
						<th width="50">Buyer</th>
						<th width="50">Brand</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
                        <th width="100">Ref No</th>
						<th width="100">Season</th>
						<th width="50">Agent</th>
						<th width="70">Order Status</th>
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
                        <th width="40">File</th>
						
						<th width="150">Item</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="70">Lead Time</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qty(Pcs)</th>
						<th width="90">PO Breakdown Qty(pcs)</th>
						<th width="90">Order Qty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
						<th width="100">LC/SC No</th>
						<th width="100">LC/SC File</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80">Int. File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor</th>

						<th width="100">Last Accessories In-House Date</th>
						<th width="100">Last Fabric In-House Date</th>
						<th width="100">Cutting Qty </th>
						<th width="100">Sewing Qty</th>					
						<th width="100">Finishing </th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100">Margin(Pcs) </th>
						<th width="100">CM(Pcs) </th>
						<th width="100">Total Margin </th>
						<th width="100">SMV(Pcs) </th>
						<th width="100">SMV </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="120">Id</th>
						<th>Remarks</th>
						<th width="100">User Name</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:5500px"  align="left" id="scroll_body">
			<table width="5480" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				$yarn_cons_arr=return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0","job_no","yarn_cons_qnty");
				//$costing_per_arr=return_library_array("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0","job_no","costing_per");
				$sql_pre=sql_select("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0");
				foreach($sql_pre as $row)
				{
					$costing_per_arr[$row[csf("job_no")]]=$row[csf("costing_per")];
					$job_approved_arr[$row[csf("job_no")]]=$row[csf("approved")];
				}
				
				
				//$approved_id=$job_approved_arr[$row['job_no']];

				$ex_fact_sql=sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.job_no=b.job_no_mst and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $pocond $year_cond  $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond group by a.job_no");
				$ex_fact_data=array();
				foreach($ex_fact_sql as $row)
				{
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
				}

				if($db_type==0)
				{
					$fab_dec_cond="group_concat(fabric_description)";
				}
				else if($db_type==2)
				{
					$fab_dec_cond="listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
				}
				$fabric_arr=array();
				$fab_sql=sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
				foreach ($fab_sql as $row)
				{
					$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				}
				//var_dump($fabric_arr);die;

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;$grand_total_cut_qty_dtls=0;
				/* if($db_type==0)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				}
				if($db_type==2)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				} */
				$lc_number_arr=$sc_number_arr=array();
				$lc_data=sql_select( "SELECT a.wo_po_break_down_id, b.id, b.export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.is_deleted=0");
				foreach($lc_data as $row)
				{
					$lc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["lc_number"].=$row["EXPORT_LC_NO"].",";
					$lc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["lc_id"].=$row["ID"].",";
				}
				unset($lc_data);

				$sc_data=sql_select( "SELECT a.wo_po_break_down_id, b.id, b.contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 ");
				foreach($sc_data as $row)
				{
					$sc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["sc_number"].=$row["CONTRACT_NO"].",";
					$sc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["sc_id"].=$row["ID"].",";
				}
				unset($sc_data);

				$data_array_group=sql_select("SELECT b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where a.id=b.job_id and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond  group by b.grouping");
				foreach ($data_array_group as $row_group)
				{
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
						$data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description,a.set_smv, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, group_concat(b.id) as id, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(DATEDIFF(b.pub_shipment_date,'$date')) date_diff_1, max(DATEDIFF(b.shipment_date,'$date')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, group_concat(b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond  $brand_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
						order by a.style_ref_no");
					}
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
						$data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category,a.set_smv, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) as po_number, listagg(cast(b.is_confirmed as varchar2(4000)),',') within group (order by b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1,  max(b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' $grouping  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
						order by a.style_ref_no");
					}
				
					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
					$total_cut_qty_dtls=0;
				
					foreach ($data_array as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					
						$ex_factory_qnty=$ex_fact_data[$row[csf('job_no')]]["ex_factory_qnty"];
						$ex_factory_date=$ex_fact_data[$row[csf('job_no')]]["ex_factory_date"];
						$date_diff_3=datediff("d",$ex_factory_date, $row[csf('pub_shipment_date')]);
						$date_diff_4=datediff("d",$ex_factory_date, $row[csf('shipment_date')]);

						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=$yarn_cons_arr[$row[csf('job_no')]];
						$data_array_costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($data_array_costing_per==1) $costing_per_pcs=1*12;
						else if($data_array_costing_per==2) $costing_per_pcs=1*1;
						else if($data_array_costing_per==3) $costing_per_pcs=2*12;
						else if($data_array_costing_per==4) $costing_per_pcs=3*12;
						else if($data_array_costing_per==5) $costing_per_pcs=4*12;

						$yarn_req_for_po=($data_array_yarn_cons/ $costing_per_pcs)*$row[csf('po_quantity')];



						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shiping_status_arr=explode(",",$row[csf('shiping_status')]);
						$shiping_status_arr=array_unique($shiping_status_arr);
						if(count($shiping_status_arr)>1) $shiping_status=2; else $shiping_status=$shiping_status_arr[0];


						$shipment_performance=0;
						if($shiping_status==1 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($shiping_status && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($shiping_status==1 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($shiping_status==2 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
						}
						if($shiping_status==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($shiping_status==3 && $date_diff_3>=0 )
						{
							$color="green";
						}
						if($shiping_status==3 &&  $date_diff_3<0)
						{
							$color="#2A9FFF";
						}
						if($shiping_status==3 && $date_diff_4>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==3 &&  $date_diff_4<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						$actual_po="";
						$ex_po_id=explode(",",$row[csf('id')]);
						foreach($ex_po_id as $poId)
						{
							if($actual_po=="") $actual_po=$actual_po_no_arr[$row[csf('id')]]; else $actual_po.=','.$actual_po_no_arr[$row[csf('id')]];
						}
						$approved_id=$job_approved_arr[$row[csf('job_no')]];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="80" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('company_name')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('working_company_id')]];?></div></td>
							<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_ref_no')];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_description')];?></div></td>
							<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
                            <td width="70" bgcolor="<? echo $color_app_td; ?>" ><p><? echo $msg_app; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[csf('brand_id')]];?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px"><? echo implode(",",array_unique(explode(",",$row[csf('po_number')])));?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo implode(",",array_unique(explode(",",$actual_po)));?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('season')];?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><a href="##" onClick="order_status('order_status_popup', '<? echo $row[csf('id')]; ?>','750px')">View</a></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[csf('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                            	<td width="40"> 
                             <? 
							 $file_type_name=$system_file_arr[$row[csf("job_no")]]['file'];
							 if($file_type_name!="")
							    {
							 ?>
                             <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[csf("job_no")] ?>','File View'),2"/>
                             <?
							   }
							  else echo " ";
							 ?></td>
							
							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]);?>&nbsp;</div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('po_received_date')]!="" && $row[csf('po_received_date')]!="0000-00-00") echo change_date_format($row[csf('po_received_date')]);?>&nbsp;</div></td>
							<?
							$po_lead_time_diff = abs(strtotime($row[csf('pub_shipment_date')]) - strtotime($row[csf('po_received_date')]));								
							$po_lead_time = floor($po_lead_time_diff / (60*60*24));
							?>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><?  echo $po_lead_time;?>&nbsp;</div></td>
							<td width="50" bgcolor="<? echo $color; ?>"  align="center"><div style="word-wrap:break-word; width:50px">
								<?
								if($shiping_status==1 || $shiping_status==2)
								{
									echo $row[csf('date_diff_1')];
								}
								if($shiping_status==3)
								{
									echo $date_diff_3;
								}
								?></div></td>
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								
								?></p></td>
							
                            <td width="90" align="right"><?=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];?></td>
                            
                            <td width="90" align="right"><p>
								<?
								echo number_format( $row[csf('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? $unit_price=$row[csf('po_total_price')]/$row[csf('po_quantity')]; echo number_format($unit_price,2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[csf('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
								?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
								<?
								$po_id_arr=explode(",",$row[csf('id')]);
								$lc_sc_no=$lc_sc_id=$lc_sc_type="";
								foreach($po_id_arr as $val)
								{
									if($lc_number_arr[$val]["lc_number"] !="")
									{
										$lc_sc_no.="LC: ". $lc_number_arr[$val]["lc_number"];
										$lc_sc_id.=$lc_number_arr[$val]["lc_id"];
										$lc_sc_type.='1,';
									}
									if($sc_number_arr[$val]["sc_number"] !="")
									{
										$lc_sc_no.=" SC: ".$sc_number_arr[$val]["sc_number"];
										$lc_sc_id.=$sc_number_arr[$val]["sc_id"];
										$lc_sc_type.='2,';
									}
								}
								echo implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
								$lc_sc_id=implode(",",array_unique(explode(",",chop($lc_sc_id,','))));
								$lc_sc_type=implode(",",array_unique(explode(",",chop($lc_sc_type,','))));
								?>
								</div>
							</td>
                            <td width="100" align="center">
								<?
									if($lc_sc_id!="")
									{
										?>
											<input type="button" class="image_uploader" id="system_id" style="width:40px" value="File" onClick="openmypage_file('requires/shipment_schedule_controller.php?action=file_show&mst_id=<?=$lc_sc_id;?>&lc_sc_type=<?=$lc_sc_type;?>','File View'),2"/>
										<?
									}
								?>
							</td>
							<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
							<? if($lc_number_arr[$row[csf('id')]] !="")
								{
									echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]];

								}
							?>
							</div></td>
							<td width="80" align="center"><p><? echo $export_lc_arr[$row[csf('id')]]['file_no'];?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['pay_term']; ?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>

							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_fabric_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['cutting_qty']; $total_cutting_qty+=$job_wise_data_arr[$row[csf('job_no')]]['cutting_qty']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['sweing_out_qty']; $total_sweing_out_qty+=$job_wise_data_arr[$row[csf('job_no')]]['sweing_out_qty']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['packing_qty'];$total_packing_qty+=$job_wise_data_arr[$row[csf('job_no')]]['packing_qty'];  ?></p></td>

							<td width="90" align="right"><p>
							<?

								?>
								<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
                                <?
								//echo  number_format( $ex_factory_qnty,0);
								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qnty;
									$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qnty;
									$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								?></p></td>
							<td width="70" align="center"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? if($ex_factory_date!="" && $ex_factory_date!="0000-00-00") echo change_date_format($ex_factory_date); ?>&nbsp;</div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								<?
									$short_access_value=$short_access_qnty*$unit_price;
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>
							<td width="100" align="right" title="<? echo "Cons:".$data_array_yarn_cons."Costing per:".$data_array_costing_per;?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							
							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')],2); ?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set'];?></div></td>

							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs),2); ?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]); ?></div></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)/$row[csf('set_smv')],2); ?></div></td>
					
							<?
								if($row[csf('order_uom')]==58){								
									?>
							<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','500px')"><?echo number_format($row[csf('set_smv')],2);?></a></td>
							<?}else{?>
								<td width="100" align="right"><p><?echo number_format($row[csf('set_smv')],2);?></p></td>	<?	}?>
							
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$shiping_status]; ?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')]; ?></div></td>
							<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('id')]))); ?></p></td>
							<td><p><? echo $row[csf('details_remarks')]; ?></p></td>
							<td width="100"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="80" align="center" style="word-wrap:break-word;" >  Total (<?=$row_group[csf('grouping')]; ?>): </td>
						<td width="70" ></td>
						<td width="100"></td>
						<td width="70"></td>
						<td width="90"></td>
						<td width="100"></td>
						<td width="60"></td>
                        <td width="70"></td>
						<td width="50"></td>
						<td width="50"></td>
						<td width="110"></td>
                        <td width="100"></td>
						<td width="100"></td>
                        <td width="100"></td>
						<td width="50"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="40"></td>
                        <td width="40"></td>
						
						<td width="150"></td>
						<td width="200"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90" align="right" ><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
                        <td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="90"></td>

						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>

						<td width="100"></td>
						<td width="100"></td>
						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_cutting_qty,0); ?></td>
						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_sweing_out_qty,0); ?></td>
						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_packing_qty,0); ?></td>

						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" ></td>
						<td width="100" ></td>
						<td width="100" ></td>
						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td></th>
						<td width="100"></td>
					 </tr>
				<?
				}
				?>
				</table>
				</div>
				<table width="5280" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="80"></th>
							<th width="70" ></th>
							<td width="100"></td>
							<th width="70"></th>
							<td width="90"></td>
							<td width="100"></td>
							<th width="60"></th>
                            <th width="70"></th>
							<td width="50"></td>
							<th width="50"></th>
							<th width="110"></th>
                            <th width="100"></th>
							<th width="100"></th>
                            <th width="100"></th>
							<th width="50"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="40"></th>
                            <th width="40"></th>
							<th width="150"></th>
							<th width="200"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            <th width="90"><?= $grand_total_cut_qty_dtls;?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>
							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>

							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo number_format($total_cutting_qty,0); ?></th>
							<th width="100"><? echo number_format($total_sweing_out_qty,0); ?></th>
							<th width="100"><? echo number_format($total_packing_qty,0); ?></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" ></th>
							<th width="150"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="120"></th>
							<th></th>
							<th width="100"></th>
						</tr>
					</tfoot>
				</table>
				<?
		}
		?>
			<?php /* ?>
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
							<td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>

							<tr bgcolor="#E9F3FF">
							<td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<? */  ?>
			</div>
			</div>
		</div>
		<?
	}	
	else if($rpt_type==7)//Details 3
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
		
	  $sql_data="SELECT a.PRODUCT_CODE,a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.season_buyer_wise,a.brand_id, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $order_confirm_status_con group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.SHIP_MODE,a.PRODUCT_DEPT,a.PRO_SUB_DEP, a.PRODUCT_CODE,a.season_buyer_wise,a.brand_id, b.id, b.is_confirmed,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.TXT_ETD_LDD, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
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
			$colorArr[$row['PO_BREAK_DOWN_ID']][$row['COLOR_NUMBER_ID']]=$color_arr[$row['COLOR_NUMBER_ID']];
			$sizeArr[$row['PO_BREAK_DOWN_ID']][$row['SIZE_NUMBER_ID']]=$size_arr[$row['SIZE_NUMBER_ID']];
		}
		
		
		$po_cond_for_in=where_con_using_array($all_po_id_arr,0,'b.po_break_down_id');
		$po_cond_for_in2=where_con_using_array($all_po_id_arr,0,'b.id');
		$po_cond_for_in3=where_con_using_array($all_po_id_arr,0,'a.wo_po_break_down_id');
		
 
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");
		
		
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
		
		
		$net_export_val_result=sql_select("select B.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/b.current_invoice_value)*b.current_invoice_qnty) AS PO_NET_INVO_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAKDOWN_ID')."");	

		foreach($net_export_val_result as $row){
			$net_export_val_arr[$row[PO_BREAKDOWN_ID]]=$row[PO_NET_INVO_VALUE];
		}
		
				
		ob_start();
		
		$width=5030;
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
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>
						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>
						<th width="90">Ex-Fac Qnty </th>
						<th width="90">Net Ex-Fac Val </th>
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
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

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

                            <td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
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
							<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
								?></p></td>
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
                                <td width="90" align="right"><?
									echo number_format($net_export_val_arr[$po_id],2);//dddd
									$net_export_val += $net_export_val_arr[$po_id];
								
								?></td>
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
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td></td>
						<td></td>
                        <td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
                        <td align="right"><?=number_format($net_export_val,2);?></td>
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
				<table width="<?= $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
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
                             <th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="90" id="net_total_ex_factory_val" align="right"><? echo number_format($net_export_val,0); ?></th>
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
		// echo "excel download";die;
		foreach (glob("$user_id*.xls") as $filename)
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$content = ob_get_contents();
		
		$is_created = fwrite($create_new_doc,$content);
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####$rpt_type####$search_by";
		disconnect($con);
		exit();
	}
	else if($rpt_type==8)//PHD
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and YEAR(b.pack_handover_date)=$year_id"; else $year_cond="";
		}
		else if ($db_type==2)
		{
			if($year_id!=0) $year_cond=" and to_char(b.pack_handover_date,'YYYY')=$year_id"; else $year_cond="";
		}
	
		// cbo_category_by
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

	
		$condition= new condition();
			$condition->company_name("in ($company_name)");
		  if(str_replace("'","",$buyer_name)>0){
			  $condition->buyer_name("=$buyer_name");
		 }
		 //$txt_file=str_replace("'","",$txt_file);
		//$txt_ref
		 if($search_string!='' || $search_string!=0)
		 {
			$condition->po_number("in('$search_string')");
		 } 
		 if(str_replace("'","",$txt_ref)!='')
		 {
				$condition->grouping("='$txt_ref'"); 
		 }
		 if(str_replace("'","",$txt_file)!='')
		 {
			$condition->file_no("in('$txt_file')");
		 }
		// echo $category_by;die;
		if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){				  
			if($category_by==1)
			{
				$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			}
			else if($category_by==2)
			{
				$condition->po_received_date(" between '$start_date' and '$end_date'");
			}
			else if($category_by==3)
			{
				if($db_type==0)
				{
					$condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
				}
				else
				{
					$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
				}
			}
			else if($category_by==4)
			{
				if($db_type==0)
				{
					$condition->pack_handover_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
				}
				else
				{
					$condition->pack_handover_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
				}
			}
		}
		
			
		$condition->init();
		
		$other= new other($condition);

		$other_costing_arr=$other->getAmountArray_by_order(); 
		
	
	    if($search_by==1){
				$sql_data="SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year,b.insert_date as po_insert_date, a.company_name,a.working_company_id, a.buyer_name,a.set_smv, a.agent_name, a.style_ref_no,a.brand_id, a.job_quantity, a.product_category,a.product_dept, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, a.season_year, a.style_description, b.id as po_id, b.is_confirmed, b.inserted_by,b.pack_handover_date, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $order_confirm_status_con $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $shipment_status_cond $ref_cond $season_cond $brand_cond $season_year_cond group by a.job_no_prefix_num, a.job_no, a.insert_date,b.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no,a.brand_id, a.job_quantity, a.product_category,a.product_dept, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom,a.set_smv,a.team_leader, a.dealing_marchant, a.season_buyer_wise, a.season_year, a.style_description, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping,b.pack_handover_date, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";


			
			//echo $sql_data; die;
			$data_array=sql_select( $sql_data);
			$all_po_id_arr=array();
			foreach($data_array as $row) //
			{
				$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
				$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
				$po_wise_arr[$row[csf('po_id')]]['work_company']=$row[csf('working_company_id')];
				$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$po_wise_arr[$row[csf('po_id')]]['brand_name']=$row[csf('brand_id')];
				$po_wise_arr[$row[csf('po_id')]]['season_year']=$row[csf('season_year')];
				$po_wise_arr[$row[csf('po_id')]]['style_desc']=$row[csf('style_description')];
				$po_wise_arr[$row[csf('po_id')]]['phd_date']=$row[csf('pack_handover_date')];
				$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
				$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
				
				$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
				$po_wise_arr[$row[csf('po_id')]]['product_dept']=$row[csf('product_dept')];
				$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
				$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
				$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
				$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
				$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
				$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
				$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
				$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
				//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
				$po_wise_arr[$row[csf('po_id')]]['inserted_by']=$row[csf('inserted_by')];
				$po_wise_arr[$row[csf('po_id')]]['po_insert_date']=$row[csf('po_insert_date')];
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
				$po_wise_arr[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
				$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
				$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				
				$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
				$all_job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
				//Company Buyer Wise
				$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
				$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
				$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
				
				//Sumary
				$month_wise_arr[$pub_date_key]=$pub_date_key;
				$summ_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];
				$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
				$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['smv_min']+=$row[csf('set_smv')]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
				//echo $summ_cm_cost.'='.$row[csf('po_quantity')]*$row[csf('total_set_qnty')].',';
				$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['cm_value']+=$summ_cm_cost;
				$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
				
			}


		//======================================start color list==============================

			$color_sql= "select id, item_number_id, color_number_id, po_break_down_id, color_order from wo_po_color_size_breakdown where   is_deleted=0 and status_active in (1,2,3) ".where_con_using_array($all_po_id_arr,1,'po_break_down_id')." order by color_order";
			//echo $sql; die;
			$color_data = sql_select($color_sql);

				foreach($color_data as $row){
					$po_color_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]=$color_library[$row[csf('color_number_id')]];
				}
				//======================================end color list==============================

				//===============================================Wash Type=================================

				$wash_data_array=sql_select("select id, quotdtlsid as pri_id, job_no, emb_name, emb_type, country, nominated_supp_multi, cons_dzn_gmts, rate, amount, status_active, budget_on,job_id from wo_pre_cost_embe_cost_dtls where emb_name=3 ".where_con_using_array($all_job_arr,1,'job_no')." and status_active=1 and is_deleted=0 order by id");

				foreach($wash_data_array as $row){
			
					$job_wise_wash_arr[$row[csf('job_no')]][$row[csf('emb_type')]]=$emblishment_wash_type[$row[csf('emb_type')]];
					$job_wise_wash_rate_arr[$row[csf('job_no')]][$row[csf('rate')]]=$row[csf('rate')];
					$job_id_wash_arr[$row[csf('job_no')]]=$row[csf('job_id')];
				} 
				//===============================================end Wash Type ==========================================
				$poIds=implode(",", $all_po_id_arr); 
				$po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
				$po_ids=$all_po_id_arr;
				// print_r($all_po_id_arr);die();
				if($db_type==2 && count($all_po_id_arr)>1000)
				{
					$po_cond_for_in=" and (";
					$po_cond_for_in2=" and (";
					$po_cond_for_in3=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
						$po_cond_for_in2.=" b.id in($ids) or";
						$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
					}
					$po_cond_for_in=chop($po_cond_for_in,'or ');
					$po_cond_for_in.=")";
					$po_cond_for_in2=chop($po_cond_for_in2,'or ');
					$po_cond_for_in2.=")";
					$po_cond_for_in3=chop($po_cond_for_in3,'or ');
					$po_cond_for_in3.=")";
				}
				else
				{
					$po_cond_for_in=" and b.po_break_down_id in($poIds)";
					$po_cond_for_in2=" and b.id in($poIds)";
					$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
				}
		
				$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
				sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
				sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");
				$ex_factory_qty_arr=array();
				foreach($sql_res as $row)
				{
					$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
					$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
					$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
					$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
					$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
					
					//Buyer Wise
					if($shiping_status_id==3)//Full shipped
					{
						$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
					}
					else if($shiping_status_id==2)//Partial shipped
					{
						$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
					}
				}
				
				if($db_type==0)
				{
					$fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
				}
				else if($db_type==2)
				{
					$fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
				}
				
				$sql_pre="SELECT a.costing_per,a.approved, c.job_no,c.cm_cost as cm_for_sipment_sche,c.total_cost from  wo_pre_cost_mst a,wo_pre_cost_dtls c,wo_po_break_down b where a.job_no=b.job_no_mst and  c.job_no=b.job_no_mst  $po_cond_for_in2 ";
				
				$data_budget_pre=sql_select($sql_pre);
					foreach ($data_budget_pre as $row)
					{
						$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
						$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
						$job_approved_arr[$row[csf('job_no')]]=$row[csf('approved')];
						$job_fob_cost[$row[csf('job_no')]]=$row[csf('total_cost')];
						$job_cm_cost[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_for_sipment_sche')];
					}
				$sql_budget="SELECT a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
				$data_budget_array=sql_select($sql_budget);
				
					$fabric_arr=array();
					foreach ($data_budget_array as $row)
					{
						$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
						if($row[csf('yarn_cons_qnty')]>0)
						{
						$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
					
						}
							//$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
						//$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
					}
						//var_dump($fabric_arr);die;
						$actual_po_no_arr=array();
				if($db_type==0)
				{
					$actual_po_sql=sql_select( "SELECT b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
				}
				else
				{
					$actual_po_sql=sql_select( "SELECT b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
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
					else
					{
						$lc_cond_for_in=" and export_lc_id in($lcIds)";
					}
				
				$lc_amendment_arr= array();
				$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
			
				foreach($last_amendment_arr as $data)
				{
					$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
				}
				
				
				
				$cut_qty_sql_res = sql_select("SELECT JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COUNTRY_SHIP_DATE,PLAN_CUT_QNTY  FROM WO_PO_COLOR_SIZE_BREAKDOWN where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."");
				$cut_qty_arr=array();
				foreach($cut_qty_sql_res as $rows)
				{
					
					$key=date("M-Y",strtotime($rows[COUNTRY_SHIP_DATE]));
					$comapny_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['company_name'];
					$buyer_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['buyer_name'];
					$cut_qty_by_month_arr[$comapny_id][$buyer_id][$key] += $rows[PLAN_CUT_QNTY];
					$cut_qty_dtls_arr[$rows[JOB_NO_MST]][$rows[PO_BREAK_DOWN_ID]] += $rows[PLAN_CUT_QNTY];
				}
			}
		
		//var_dump($cut_qty_by_month_arr);die;
		
		
		$tot_width=220+count($month_wise_arr)*320;		
		ob_start();
		?>
		<div align="center">
			<div align="center">
		
		<h3 style="width:3490px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
		<div id="content_report_panel">
        <? 
		
		if($search_by==1)
		{
			?>
			<table width="4230" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50"><span class="break_word">SL</span></th>
						<th width="70" ><span class="break_word">LC Company</span></th>
						<th width="70" ><span class="break_word">Manu. Company</span></th>
						<th width="70"><span class="break_word">Job No</span></th>
						<th width="60"><span class="break_word">Year</span></th>                      

						<th width="50"><span class="break_word">Buyer</span></th>
						<th width="50"><span class="break_word">Brand</span></th>
						<th width="250"><span class="break_word">PO No</span></th>
                        <th width="100"><span class="break_word">Actual PO No</span></th>
						<th width="100"><span class="break_word">Season Year</span></th>                     
						<th width="100"><span class="break_word">Season</span></th>
						

						<th width="70"><span class="break_word">Order Status</span></th>
						<th width="70"><span class="break_word">Product Dept.</span></th>
						<th width="40"><span class="break_word">Img</span></th>                   
						<th width="90"><span class="break_word">Style Ref</span></th>
						<th width="100"><span class="break_word">Style Description</span></th>
						<th width="150"><span class="break_word">Item</span></th>
						<th width="100"><span class="break_word">Color</span></th>
						<th width="350"><span class="break_word">Fab. Description</span></th>
										
						<th width="90"><span class="break_word">Order Qty</span></th>					
						<th width="40"><span class="break_word">Uom</span></th>
						<th width="50"><span class="break_word">FOB</span></th>
						<th width="80"><span class="break_word">CM Cost (Dzn)</span></th>
						<th width="70"><span class="break_word">EPM</span></th>
						<th width="100"><span class="break_word">SMV</span></th>
						<th width="100"><span class="break_word">Total Minute</span></th>
						<th width="100"><span class="break_word">Order Value</span></th>   
						
						<th width="70"><span class="break_word">PO Rec. Date</span></th>
						<th width="70"><span class="break_word">PO Insert Date</span></th>
						<th width="70"><span class="break_word">PHD. Date</span></th>
						<th width="70"><span class="break_word">Ship Date</span></th>
						<th width="70"><span class="break_word">PO Lead Time</span></th>
						<th width="70"><span class="break_word">PO Entry Delay</span></th>
						<th width="70"><span class="break_word">Prod Lead Time</span></th>		
						<th width="100"><span class="break_word">Wash Rate(Dzn)</span></th>					
						<th width="160"><span class="break_word">Wash Type</span></th>						
						         
						
				
						<th width="100" ><span class="break_word">Shipping Status</span></th>
						<th width="70" ><span class="break_word">file</span></th>
						<th width="150"><span class="break_word">Team Member</span></th>
						<th width="150"><span class="break_word">Team Name</span></th>
						<th width="100"><span class="break_word">File No</span></th>					
						<th ><span class="break_word">User Name</span></th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:4260px"  align="left" id="scroll_body">
			<table width="4230" border="1" class="rpt_table" rules="all" id="table_body_job_8">
				<?
				

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
				
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;
					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
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
						$approved_id=$job_approved_arr[$row['job_no']];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}
						
						//echo $file_type_name.'DDDDD,';
						$company_name=$row[('company_name')];
						$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id=$company_name","image_location");
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50"   bgcolor="<? echo $color; ?>"> <span class="break_word"><? echo $i; ?></span> </td>
							<td width="70"><span class="break_word"><? echo $company_short_name_arr[$row[('company_name')]];?></span></td>
							<td width="70"><span class="break_word"><? echo $company_short_name_arr[$row[('work_company')]];?></span></td>
							<td width="70"><span class="break_word"><? echo $row[('job_no')]; ?></span></td>
							<td width="60"><span class="break_word"><? echo $row[('year')]; ?></span></td>     
							
					
							
							<td width="50"><span class="break_word"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></span></td>
							<td width="50"><span class="break_word"><? echo $brand_arr[$row[('brand_name')]];?></span></td>
							<td width="250"><span class="break_word"><? echo $row[('po_number')];?></span></td>
                            <td width="100"><span class="break_word"><? echo $actual_po_no_arr[$po_id]; ?></span></td>
							<td width="100"><span class="break_word"><?  echo $row[('season_year')]; ?></span></td>
							<td width="100"><span class="break_word"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></span></td>

							<td width="70"><span class="break_word"><? echo $order_status[$row[('is_confirmed')]];?></span></td>
							<td width="70"><span class="break_word"><? echo $product_dept[$row[('product_dept')]];?></span></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[('job_no')] ?>','Image View')"><span class="break_word"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></span></td>
							<td width="90"><span class="break_word"><? echo $row[('style_ref_no')];?></span></td>
							<td width="100"><span class="break_word"><? echo $row[('style_desc')];?></span></td>
							<td width="150"><span class="break_word">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);

								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></span></td>
								
							<td width="100"><span class="break_word"><? echo implode(",",$po_color_arr[$po_id][$row[('gmts_item_id')]]);?></span></td>
							<td width="350"><span class="break_word"><? $fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></span></td>

								









							<td width="90" align="right"><span class="break_word">
								<?
								echo number_format( $row[('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
								
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								 
								?></span>
							</td>











							<td width="40" align="center"><span class="break_word"><? echo $unit_of_measurement[$row[('order_uom')]];?></span></td>
							<td width="50" align="right"><span class="break_word"><? $unit_price=$row[('po_total_price')]/$row[('po_quantity')]; echo number_format($unit_price,2);?></span></td>
							<td width="80" align="right"><span class="break_word"><?  echo number_format($job_cm_cost[$row['job_no']]['cm_cost'],2);?></span></td>
							<td width="70" align="right" title="cm/12/smv"><span class="break_word"><?  echo number_format(($job_cm_cost[$row['job_no']]['cm_cost']/12)/$row[('set_smv')],4);?></span></td>

							<?	if($row[('order_uom')]==58){?>
							<td width="100" align="right"><span class="break_word"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[('job_no')];?>', '<? echo $row[('id')]; ?>','500px')"><?echo number_format($row[('set_smv')],2);?></a></span></td>
							<?}else{?>
							<td width="100" align="right"><span class="break_word"><?echo number_format($row[('set_smv')],2);?></span></td>	<?	}?>

							<td width="100" align="right"><span class="break_word">
								<? echo number_format($row[('po_quantity')]*$row[('set_smv')],2);
									$order_minute_tot+=$row[('po_quantity')]*$row[('set_smv')];
									$gorder_minute_tot+=$row[('po_quantity')]*$row[('set_smv')];								
								?></span>
								</td>								
							<td width="100" align="right">
								<span class="break_word">
									<?
							
										echo number_format($row[('po_quantity')]*$unit_price,2);
										$oreder_value_tot=$row[('po_quantity')]*$unit_price;
										$goreder_value_tot=$row[('po_quantity')]*$unit_price;
									?>
								</span>
							</td>                    
                            

							<td width="70">
								<span class="break_word"><? echo change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></span>
							</td>
							<td width="70"><span class="break_word"><? echo change_date_format($row[('po_insert_date')],'dd-mm-yyyy','-');?></span></td>
							<td width="70"><span class="break_word"><? echo change_date_format($row[('phd_date')],'dd-mm-yyyy','-');?></span></td>
							<td width="70"><span class="break_word"><? echo change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></span></td>
									<?php

								

									$po_lead_time_diff = abs(strtotime($row[('pub_shipment_date')]) - strtotime($row[('po_received_date')]));								
									$po_lead_time = floor($po_lead_time_diff / (60*60*24));

									$pro_lead_time_diff = abs(strtotime($row[('pub_shipment_date')]) - strtotime($row[('phd_date')]));
									$pro_lead_time = floor($pro_lead_time_diff / (60*60*24));
									
									$po_delay_time_diff=abs(strtotime($row[('po_received_date')]) - strtotime($row[('po_insert_date')]));	
									$po_delay_time = floor($po_delay_time_diff / (60*60*24));

									?>

							<td width="70"><span class="break_word"><? echo $po_lead_time;?></span></td>
							<td width="70"><span class="break_word"><? echo $po_delay_time;?></span></td>
							<td width="70"><span class="break_word"><? echo $pro_lead_time;?></span></td>
							<?php
								$job_id 	= $job_id_wash_arr[$row[('job_no')]];
								$buyer_id 	= $row['buyer_name']; 
							?>
							<td width="100"><span class="break_word"> <a href="##" onclick="wash_rate_popup(<?= $job_id ?>,<?= $buyer_id ?>)"> <? echo implode("+",$job_wise_wash_rate_arr[$row[('job_no')]]);?> </a> </span></td>
							<td width="160"><span class="break_word"><? echo implode(",",$job_wise_wash_arr[$row[('job_no')]]);;?></span></td>
						
						
							
							<td width="100" align="center"><span class="break_word"><? echo $shipment_status[$row[('shiping_status')]]; ?></span></td>
							<td width="70" align="center"><span class="break_word">  <img  src='<?="../../../../".$image_location; ?>' height='25'  width="35" align="left" /></span></td>
							<td width="150" align="center"><span class="break_word"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></span></td>
							<td width="150" align="center"><span class="break_word"><? echo $company_team_name_arr[$row[('team_leader')]];?></span></td>
							<td width="100"><span class="break_word"><? echo $row[('file_no')]; ?></span></td>							
							<td ><span class="break_word"><? echo $user_name_arr[$row[('inserted_by')]]; ?></span></td>
						</tr>
						<?
						$i++;
					}
					
				
				?>
				</table>
				</div>
				<table width="4230" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70" ></th>
							<th width="70" ></th>
							<th width="70"></th>
							<th width="60"></th>
                            
							<th width="50"></th>
							<th width="50"></th>
							<th width="250"></th>
                            <th width="100"></th>
                            <th width="100"></th>
							<th width="100"></th>					

							<th width="70"></th>
							<th width="70"></th>
							<th width="40"></th>                           
							<th width="90"></th>
							<th width="100"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="350">Total :</th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>
							<th width="80"></th>
							<th width="70"></th>
							<th width="100"></th>							
							<th width="100" id="value_total_order_min" align="right"><? echo number_format($gorder_minute_tot,2); $gorder_minute_tot=0;?></th>
							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>						
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="160"></th>			
							
							<th width="100" ></th>
							<th width="70"></th>
							<th width="150"></th>
							<th width="150"></th>
							<th width="100"></th>							
							<th ></th>
						</tr>
					</tfoot>
				</table>
				<?
		}
		else
		{
			?>
			<table width="4330" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50"><span class="break_word">SL</span></th>
						<th width="70" ><span class="break_word">Company</span></th>
						<th width="70" ><span class="break_word">Menu Company</span></th>
						<th width="70"><span class="break_word">Job No</span></th>
						<th width="60"><span class="break_word">Year</span></th>
            
						<th width="50"><span class="break_word">Buyer</span></th>
						<th width="50"><span class="break_word">Brand</span></th>
						<th width="250"><span class="break_word">PO No</span></th>
                        <th width="100"><span class="break_word">Actual PO No</span></th>
						<th width="100"><span class="break_word">Season Year</span></th>
                     
						<th width="100"><span class="break_word">Season</span></th>
						
						<th width="70"><span class="break_word">Order Status</span></th>
						<th width="70"><span class="break_word">Prod. Dept.</span></th>
						<th width="40"><span class="break_word">Img</span></th>
                     
						<th width="90"><span class="break_word">Style Ref</span></th>
						<th width="150"><span class="break_word">Style Description</span></th>
						<th width="150"><span class="break_word">Item</span></th>
						<th width="100"><span class="break_word">Color</span></th>
						<th width="350"><span class="break_word">Fab. Description</span></th>
								
						<th width="90"><span class="break_word">Order Qty</span></th>
						<th width="40"><span class="break_word">Uom</span></th>
						<th width="50"><span class="break_word">FOB</span></th>
						<th width="80"><span class="break_word">CM Cost (Dzn)</span></th>
						<th width="70"><span class="break_word">EPM</span></th>
						<th width="100"><span class="break_word">SMV</span></th>
						<th width="100"><span class="break_word">Total Minute</span></th>
						<th width="100"><span class="break_word">Order Value</span></th>
					
						<th width="70"><span class="break_word">PO Rec. Date</span></th>
						<th width="70"><span class="break_word">PO Insert Date</span></th>
						<th width="70"><span class="break_word">PHD Date</span></th>
						<th width="70"><span class="break_word">Ship Date</span></th>
						<th width="70"><span class="break_word">PO Lead Time</span></th>
						<th width="70"><span class="break_word">PO Delay Time</span></th>
						<th width="70"><span class="break_word">Prod Lead Time</span></th>
						<th width="100"><span class="break_word">Wash Rate(Dzn)</span></th>
						<th width="140"><span class="break_word">Wash Type</span></th>	
						<th width="100" ><span class="break_word">Shipping Status</span></th>
						<th width="70" ><span class="break_word">File</span></th>
						<th width="150"><span class="break_word">Team Member</span></th>
						<th width="150"><span class="break_word">Team Name</span></th>
						<th width="100"><span class="break_word">File No</span></th>					
						<th ><span class="break_word">User Name</span></th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:4350px"  align="left" id="scroll_body">
				<table width="4330" border="1" class="rpt_table" rules="all" id="table_body_job_8">
					<?
					$yarn_cons_arr=return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0","job_no","yarn_cons_qnty");
					$sql_pre=sql_select("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0");
					foreach($sql_pre as $row)
					{
						$costing_per_arr[$row[csf("job_no")]]=$row[csf("costing_per")];
						$job_approved_arr[$row[csf("job_no")]]=$row[csf("approved")];
					}
					
					
					//$approved_id=$job_approved_arr[$row['job_no']];

					$ex_fact_sql=sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.job_no=b.job_no_mst and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $pocond $year_cond  $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond group by a.job_no");
					$ex_fact_data=array();
					foreach($ex_fact_sql as $row)
					{
						$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
						$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
					}

					if($db_type==0)
					{
						$fab_dec_cond="group_concat(fabric_description)";
					}
					else if($db_type==2)
					{
						$fab_dec_cond="listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
					}
					
					$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;$grand_total_cut_qty_dtls=0;
					if($db_type==0)
					{
						$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

						$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
					}
					if($db_type==2)
					{
						$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

						$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
					}			

					$data_array=sql_select("select a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year,a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no,a.job_quantity, a.product_category,a.set_smv, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, b.id as id,b.po_number  as po_number, b.is_confirmed as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date ,	max(b.pub_shipment_date - to_date('26-10-2021','dd-mm-yyyy')) date_diff_1, max(b.shipment_date - to_date('26-10-2021','dd-mm-yyyy')) date_diff_2,sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status,max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by, a.season_year, a.style_description,b.pack_handover_date,a.product_dept from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond $brand_cond $season_year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.set_smv, a.season_buyer_wise, a.season_year, a.style_description,b.pack_handover_date,a.product_dept, b.is_confirmed,b.id,b.po_number order by a.style_ref_no");
					

					foreach ($data_array as $row)
					{
						$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];

						$job_wise_data[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
						$job_wise_data[$row[csf('job_no')]]['company_name']=$row[csf('company_name')];
						$job_wise_data[$row[csf('job_no')]]['working_company_id']=$row[csf('working_company_id')];
						$job_wise_data[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
						$job_wise_data[$row[csf('job_no')]]['year']=$row[csf('year')];
						$job_wise_data[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
						$job_wise_data[$row[csf('job_no')]]['season_year']=$row[csf('season_year')];
						$job_wise_data[$row[csf('job_no')]]['season']=$row[csf('season')];
						$job_wise_data[$row[csf('job_no')]]['id']=$row[csf('id')];
						$job_wise_data[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];

						$job_wise_data[$row[csf('job_no')]]['product_dept']=$row[csf('product_dept')];
						$job_wise_data[$row[csf('job_no')]]['style_description']=$row[csf('style_description')];
						$job_wise_data[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
						$job_wise_data[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
						$job_wise_data[$row[csf('job_no')]]['po_total_price']+=$row[csf('po_total_price')];
						$job_wise_data[$row[csf('job_no')]]['set_smv']=$row[csf('set_smv')];
						$job_wise_data[$row[csf('job_no')]]['po_received_date']=$row[csf('po_received_date')];
						$job_wise_data[$row[csf('job_no')]]['insert_date']=$row[csf('insert_date')];
						$job_wise_data[$row[csf('job_no')]]['po_insert_date']=$row[csf('po_insert_date')];
						$job_wise_data[$row[csf('job_no')]]['pack_handover_date']=$row[csf('pack_handover_date')];
						$job_wise_data[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
						$job_wise_data[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
						$job_wise_data[$row[csf('job_no')]]['file_no']=$row[csf('file_no')];
						$job_wise_data[$row[csf('job_no')]]['inserted_by']=$row[csf('inserted_by')];
						$job_wise_data[$row[csf('job_no')]]['shiping_status']=$row[csf('shiping_status')];						
						$job_wise_data[$row[csf('job_no')]]['date_diff_1']=$row[csf('date_diff_1')];
						$job_wise_data[$row[csf('job_no')]]['date_diff_2']=$row[csf('date_diff_2')];
						$job_wise_data[$row[csf('job_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
						$job_wise_data[$row[csf('job_no')]]['brand_id']=$row[csf('brand_id')];

						$job_wise_data[$row[csf('job_no')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
						$job_wise_data[$row[csf('job_no')]]['shipment_date']=$row[csf('shipment_date')];					

						$job_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];
						$job_id_arr[$row[csf('job_no')]][$row[csf('id')]]=$row[csf('id')];
						$job_gmts_item_id_arr[$row[csf('job_no')]][$row[csf('gmts_item_id')]]=$row[csf('gmts_item_id')];
					}



					//======================================start color list==============================

					$color_sql= "select a.item_number_id, a.color_number_id, a.po_break_down_id, a.color_order ,b.job_no_mst from wo_po_color_size_breakdown a,wo_po_break_down b where  b.id=a.po_break_down_id and  a.is_deleted=0 and a.status_active in (1,2,3) ".where_con_using_array($job_arr,1,'b.job_no_mst')." group by a.item_number_id, a.color_number_id, a.po_break_down_id, a.color_order ,b.job_no_mst order by a.color_order";

					$color_data = sql_select($color_sql);

					foreach($color_data as $row){
						$job_color_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]=$color_library[$row[csf('color_number_id')]];
					}

					$wash_data_array=sql_select("select id, job_no, emb_name, emb_type, rate, status_active, budget_on,job_id from wo_pre_cost_embe_cost_dtls where emb_name=3 ".where_con_using_array($job_arr,1,'job_no')." and status_active=1 and is_deleted=0 order by id");

					foreach($wash_data_array as $row){	
						$job_wash_arr[$row[csf('job_no')]][$row[csf('emb_type')]]=$emblishment_wash_type[$row[csf('emb_type')]];
						$job_wash_rate_arr[$row[csf('job_no')]][$row[csf('rate')]]=$row[csf('rate')];
						$job_id_wash_arr[$row[csf('job_no')]]=$row[csf('job_id')];
					}
					
					//===============================================end Wash Type ==========================================
						
					//===============================================fab Description ==========================================
					$fabric_arr=array();
					$fab_sql=sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0  ".where_con_using_array($job_arr,1,'job_no')." group by job_no, item_number_id");
					foreach ($fab_sql as $row)
					{
						$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
					}

					//===============================================end Fab Description ==========================================


					//===========================================================================================================
					$cm_data_array=sql_select("select id, job_no,  cm_cost, cm_cost_percent from wo_pre_cost_dtls where  status_active=1 and is_deleted=0 ".where_con_using_array($job_arr,1,'job_no')."");

					foreach ($cm_data_array as $row)
					{
						$job_cm_cost[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
					}


					//==========================================================================================================




					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
					$total_cut_qty_dtls=0;
					foreach ($job_wise_data as $job_no=> $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					
						$ex_factory_qnty=$ex_fact_data[$row['job_no']]["ex_factory_qnty"];
						$ex_factory_date=$ex_fact_data[$row['job_no']]["ex_factory_date"];
						$date_diff_3=datediff("d",$ex_factory_date, $row['pub_shipment_date']);
						$date_diff_4=datediff("d",$ex_factory_date, $row['shipment_date']);

						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=$yarn_cons_arr[$row['job_no']];
						$data_array_costing_per=$costing_per_arr[$row['job_no']];
						if($data_array_costing_per==1) $costing_per_pcs=1*12;
						else if($data_array_costing_per==2) $costing_per_pcs=1*1;
						else if($data_array_costing_per==3) $costing_per_pcs=2*12;
						else if($data_array_costing_per==4) $costing_per_pcs=3*12;
						else if($data_array_costing_per==5) $costing_per_pcs=4*12;

						$yarn_req_for_po=($data_array_yarn_cons/ $costing_per_pcs)*$row['po_quantity'];



						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shiping_status_arr=explode(",",$row['shiping_status']);
						$shiping_status_arr=array_unique($shiping_status_arr);
						if(count($shiping_status_arr)>1) $shiping_status=2; else $shiping_status=$shiping_status_arr[0];


						$shipment_performance=0;
						if($shiping_status==1 && $row['date_diff_1']>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($shiping_status && ($row['date_diff_1']<=10 && $row['date_diff_1']>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($shiping_status==1 &&  $row['date_diff_1']<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($shiping_status==2 && $row['date_diff_1']>10 )
						{
							$color="";
						}
						if($shiping_status==2 && ($row['date_diff_1']<=10 && $row['date_diff_1']>=0))
						{
							$color="orange";
						}
						if($shiping_status==2 &&  $row['date_diff_1']<0)
						{
							$color="red";
						}
						if($shiping_status==2 &&  $row['date_diff_2']>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==2 &&  $row['date_diff_2']<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($shiping_status==3 && $date_diff_3>=0 )
						{
							$color="green";
						}
						if($shiping_status==3 &&  $date_diff_3<0)
						{
							$color="#2A9FFF";
						}
						if($shiping_status==3 && $date_diff_4>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==3 &&  $date_diff_4<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
					
						$actual_po="";
						$ex_po_id=explode(",",$row['id']);
						foreach($ex_po_id as $poId)
						{
							if($actual_po=="") $actual_po=$actual_po_no_arr[$row['id']]; else $actual_po.=','.$actual_po_no_arr[$row['id']];
						}
						$approved_id=$job_approved_arr[$row['job_no']];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}

					
						// 	echo "<pre>";
						// print_r($job_po_arr[$job_no]);

						$company_name=$row[('company_name')];
						$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='knit_order_entry' and master_tble_id=$company_name","image_location");


						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50" bgcolor="<? echo $color; ?>"><span class="break_word"> <? echo $i; ?></span> </td>
							<td width="70"><span class="break_word"><? echo $company_short_name_arr[$row['company_name']];?></span></td>
							<td width="70"><span class="break_word"><? echo $company_short_name_arr[$row['working_company_id']];?></span></td>
							<td width="70"><span class="break_word"><? echo $row['job_no_prefix_num']; ?></span></td>
							<td width="60"><span class="break_word"><? echo $row['year']; ?></span></td>
                     
							<td width="50"><span class="break_word"><? echo $buyer_short_name_arr[$row['buyer_name']];?></span></td>
							<td width="50"><span class="break_word"><? echo $brand_arr[$row['brand_id']];?></span></td>
							<td width="250"><span class="break_word"><? echo implode(",",$job_po_arr[$job_no]);?></span></td>
                            <td width="100"><span class="break_word"><? echo $actual_po;?></span></td> 
							<td width="100"><span class="break_word"><? echo $row['season_year']; ?></span></td>
                            
							<td width="100"><span class="break_word"><? echo $buyer_wise_season_arr[$row['season']];?></span></td>
							
							<td width="70"><span class="break_word"><a href="##" onClick="order_status('order_status_popup', '<? echo $row['id']; ?>','750px')">View</a></span></td>
							<td width="70"><span class="break_word"><? echo $product_dept[$row['product_dept']];?></span></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $job_no; ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25' width='30' /></td>
                            	
							<td width="90"><span class="break_word"><? echo $row['style_ref_no'];?></span></td>
							<td width="150"><span class="break_word"><? echo $row['style_description'];?></span></td>
							<td width="150">
								<span class="break_word">
							<?
							
							$gmts_itemid=implode(",",$job_gmts_item_id_arr[$job_no]);
							$gmts_item_id=explode(",",$gmts_itemid);

					
						
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$job_no][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$job_no][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
							
								?></span></td>
							<td width="100">
								<span class="break_word"><? echo implode(",",$job_color_arr[$job_no][$row['gmts_item_id']]);?></span></td>
							<td width="350">
								<span class="break_word">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;?>
								</span>
							</td>
									
							
                           
                            
                            <td width="90" align="right">
                            	<span class="break_word">
								<?
								echo number_format( $row['po_quantity'],0);
								$order_qntytot=$order_qntytot+$row['po_quantity'];
								$gorder_qntytot+=$row['po_quantity'];
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row['job_no']][$poId];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row['job_no']][$poId];
								?></span></td>
							<td width="40" align="center"><span class="break_word"><? echo $unit_of_measurement[$row['order_uom']];?></span></td>
							<td width="50" align="right"><span class="break_word"><? $unit_price=$row['po_total_price']/$row['po_quantity']; echo number_format($unit_price,2);?></span></td>
							<td width="80" align="right"><span class="break_word"><?  echo number_format($job_cm_cost[$row['job_no']]['cm_cost'],4);?></span></td>
							<td width="70" align="right" title="cm/12/smv"><span class="break_word"><?  echo number_format(($job_cm_cost[$row['job_no']]['cm_cost']/12)/$row['set_smv'],4);?></span></td>
							<?
								if($row['order_uom']==58){								
									?>
							<td width="100" align="right"><span class="break_word"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row['job_no'];?>', '<? echo $row['id']; ?>','500px')"><?echo number_format($row['set_smv'],2);?></a></span></td>
							<?}else{?>
								<td width="100" align="right"><span class="break_word"><?echo number_format($row['set_smv'],2);?></span></td>	<?	}?>
							<td width="100" align="right"><span class="break_word"><? echo number_format($row['set_smv']*$row['po_quantity'],2);
							
									$order_minute_tot+=$row['set_smv']*$row['po_quantity'];
									$gorder_minute_tot+=$row['set_smv']*$row['po_quantity'];
							
							?></span></td>
								<td width="100" align="right"><span class="break_word">
								<?
									echo number_format($row['po_quantity']*$unit_price,2);
									$oreder_value_tot=$row['po_quantity']*$unit_price;
									$goreder_value_tot=$row['po_quantity']*$unit_price;
								?></span></td>
							
							<td width="70"  align="center"><span class="break_word"><? if($row['po_received_date']!="" && $row['po_received_date']!="0000-00-00") echo change_date_format($row['po_received_date']);?>&nbsp;</span></td>
							<td width="70"  align="center"><span class="break_word"><?  echo change_date_format($row['insert_date']);?>&nbsp;</span></td>
							<td width="70"  align="center"><span class="break_word"><?  echo change_date_format($row['pack_handover_date']);?>&nbsp;</span></td>
							<td width="70"  align="center"><span class="break_word"><? if($row['pub_shipment_date']!="" && $row['pub_shipment_date']!="0000-00-00") echo change_date_format($row['pub_shipment_date']);?>&nbsp;</span></td>
							<?php

								$po_lead_time_diff = abs(strtotime($row['pub_shipment_date']) - strtotime($row['po_received_date']));								
								$po_lead_time = floor($po_lead_time_diff / (60*60*24));

								if($row['pack_handover_date']!="" && $row['pack_handover_date']!="0000-00-00"){
								$pro_lead_time_diff = abs(strtotime($row['pub_shipment_date']) - strtotime($row['pack_handover_date']));
								$pro_lead_time = floor($pro_lead_time_diff / (60*60*24));

								}

								$po_delay_time_diff = abs(strtotime($row['po_received_date']) - strtotime($row['insert_date']));								
								$po_delay_time = floor($po_delay_time_diff / (60*60*24));
								
								?>
							<td width="70"  align="center"><span class="break_word"><?  echo $po_lead_time;?></span></td>
							<td width="70"  align="center"><span class="break_word"><?  echo $po_delay_time;?></span></td>
							<td width="70"  align="center"><span class="break_word"><? echo $pro_lead_time;?></span></td>
							<?php
								$job_id 	= $job_id_wash_arr[$job_no];
								$buyer_id 	= $row['buyer_name'];
							?>
							<td width="100"  align="left"><span class="break_word"><a href="##" onclick="wash_rate_popup(<?= $job_id ?>,<?= $buyer_id ?>)"> <?  echo implode("+",$job_wash_rate_arr[$job_no]);?> </a></span></td>
							<td width="140"  align="left"><span class="break_word"><?  echo implode(",",$job_wash_arr[$job_no]);?>&nbsp;</span></td>	 
							<td width="100" align="center"><span class="break_word"><? echo $shipment_status[$shiping_status]; ?></span></td>
							<td width="70" align="center"><img  src='<?="../../../../".$image_location; ?>' align="left" height="25" width="35" /></td>
							<td width="150" align="center"><span class="break_word"><? echo $company_team_member_name_arr[$row['dealing_marchant']];?></span></td>
							<td width="150" align="center"><span class="break_word"><? echo $company_team_name_arr[$row['team_leader']];?></span></td>
							<td width="100"><span class="break_word"><? echo $row['file_no']; ?></span></td>
					
							<td ><span class="break_word"><? echo $user_name_arr[$row['inserted_by']]; ?></span></td>
						</tr>
						<?
						$i++;
					}
					?>
				
					<?
					//}
					?>
				</table>
			</div>
			<table width="4330" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr class="rpt_table" rules="all">

						<th width="50"></th>
						<th width="70" ></th>
						<th width="70" ></th>
						<th width="70"></th>
						<th width="60"></th>
						<th width="50"></th>
						<th width="50"></th>
						<th width="250"></th>
						<th width="100"></th>
						<th width="100"></th>                           
						<th width="100"></th>


						<th width="70"></th>
						<th width="70"></th>
						<th width="40"></th>

						<th width="90"></th>
						<th width="150"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="350"> Total : </th>	

						<th width="90" id="total_order_qnty" align="right"><? echo number_format($gorder_qntytot,0); ?></th>
						<th width="40"></th>
						<th width="50"></th>
						<th width="80"></th>
						<th width="70"></th>
						<th width="100"></th>
						<th width="100" id="value_total_order_min" align="right"><? echo number_format($gorder_minute_tot,2); ?></th>
						<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>

						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="100"></th>
						<th width="140"></th>							


						<th width="100" ></th>
						<th width="70"></th>
						<th width="150"> </th>
						<th width="150"></th>
						<th width="100"></th>							
						<th ></th>
						</tr>
					</tfoot>
			</table>
				<?
		}
		?>
			</div>
			</div>
		</div>
		<?
	}	
	else if($rpt_type==9)//Order Book Button (Shafiq)
	{
		$buyer_arr=return_library_array( "SELECT id, short_name from lib_buyer",'id','short_name');
		$brand_library = return_library_array("SELECT id, brand_name from lib_buyer_brand brand where status_active =1 and is_deleted=0", "id", "brand_name");
		$color_arr=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
		$company_name_arr=return_library_array( "SELECT id,company_name from lib_company",'id','company_name');
		$floor_arr=return_library_array( "SELECT id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "SELECT id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "SELECT id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		$factory_mar_arr=return_library_array( "SELECT a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name",'id','team_member_name');
		$merchant_library   = return_library_array("SELECT id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name");
		$sub_dep_arr=return_library_array( "SELECT id,sub_department_name from lib_pro_sub_deparatment where  status_active =1 and is_deleted=0 order by sub_department_name",'id','sub_department_name');
		$lineArr = return_library_array("SELECT id,line_name from lib_sewing_line where status_active=1","id","line_name"); 
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst where is_deleted=0",'id','line_number');
		
		
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

		/*===============================================================================/
	    /                                  MAIN QUERY                                    /
	    /============================================================================== */
	    /*$sql = "SELECT a.id,a.buyer_name,a.style_ref_no,a.brand_id,(a.job_quantity*a.total_set_qnty) as job_qty_pcs,a.set_smv,a.team_leader,a.dealing_marchant,a.factory_marchant,a.product_category,a.set_smv,

	        b.is_confirmed,b.id as po_id,b.po_number,b.unit_price,b.po_total_price,to_char(b.insert_date,'DD-MM-YYYY') as po_insert_date,to_char(b.po_received_date,'DD-MON') as po_received_date,to_char(b.factory_received_date,'DD-MM-YYYY') as factory_received_date,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.pub_shipment_date,'MON-YYYY') as ship_month,to_char(b.shipment_date,'DD-MM-YYYY') as shipment_date,to_char(b.txt_etd_ldd,'DD-MM-YYYY') as txt_etd_ldd,b.shiping_status,(b.pub_shipment_date-b.po_received_date) as lead_time,(b.pub_shipment_date - trunc(sysdate)) AS days_in_hand,
	        c.item_number_id as item_id,c.color_number_id as color_id,c.order_quantity,c.plan_cut_qnty,to_char(c.country_ship_date,'DD-MM-YYYY') as country_ship_date,c.excess_cut_perc,c.order_rate,
	        d.fabric_color_id,d.gmts_color_id

	        from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_booking_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.booking_type=1 and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $order_confirm_status_con ";*/

	        $sql = "SELECT a.id,a.buyer_name,a.style_ref_no,a.brand_id,(a.job_quantity*a.total_set_qnty) as job_qty_pcs,a.set_smv,a.team_leader,a.dealing_marchant,a.factory_marchant,a.product_category,a.set_smv,

	        b.is_confirmed,b.id as po_id,b.po_number,b.unit_price,b.po_total_price,to_char(b.insert_date,'DD-MM-YYYY') as po_insert_date,to_char(b.po_received_date,'DD-MON') as po_received_date,to_char(b.factory_received_date,'DD-MM-YYYY') as factory_received_date,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.pub_shipment_date,'MON-YYYY') as ship_month,to_char(b.shipment_date,'DD-MM-YYYY') as shipment_date,to_char(b.txt_etd_ldd,'DD-MM-YYYY') as txt_etd_ldd,b.shiping_status,(b.pub_shipment_date-b.po_received_date) as lead_time,(b.pub_shipment_date - trunc(sysdate)) AS days_in_hand,
	        c.item_number_id as item_id,c.color_number_id as color_id,c.order_quantity,c.plan_cut_qnty,to_char(c.country_ship_date,'DD-MM-YYYY') as country_ship_date,c.excess_cut_perc,c.order_rate,
	        d.CONTRAST_COLOR_ID as fabric_color_id,d.gmts_color_id

	        from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join wo_pre_cos_fab_co_color_dtls d on c.job_id=d.job_id  AND d.status_active = 1   AND d.is_deleted = 0  and c.color_number_id=d.gmts_color_id
	        where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and a.company_name in ($company_name) $style_owner_con $buyer_id_cond and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond $brand_cond $shipment_status_cond $order_confirm_status_con ";
	    // echo $sql;die();
	    $result = sql_select($sql);		
	    $data_array = array();
	    $po_id_arr = array();
	    $job_id_array = array();
	    $tot_color_arr = array();
	    $tot_fab_color_arr = array();
	    foreach ($result as $val) 
	    {
	    	// $fab_color = ($val['FABRIC_COLOR_ID']!="") ? $val['FABRIC_COLOR_ID'] : $val['COLOR_ID'];

	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['order_status'] = $val['IS_CONFIRMED'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['po_received_date'] = $val['PO_RECEIVED_DATE'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['ship_date'] = $val['PUB_SHIPMENT_DATE'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['lead_time'] = $val['LEAD_TIME'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['ship_month'] = $val['SHIP_MONTH'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['buyer_name'] = $val['BUYER_NAME'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['brand_id'] = $val['BRAND_ID'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['style'] = $val['STYLE_REF_NO'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['dealing_marchant'] = $val['DEALING_MARCHANT'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['product_category'] = $val['PRODUCT_CATEGORY'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['shiping_status'] = $val['SHIPING_STATUS'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['excess_cut_perc'] = $val['EXCESS_CUT_PERC'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['smv'] = $val['SET_SMV'];

	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['item_id'] = $val['ITEM_ID'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['item_id'] = $val['ITEM_ID'];

	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['order_quantity'] += $val['ORDER_QUANTITY'];
	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['plan_cut_qnty'] += $val['PLAN_CUT_QNTY'];

	    	$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['COLOR_ID']]['order_rate'] += $val['ORDER_RATE'];

	    	// $tot_fab_color_arr[$val['PO_ID']][$val['COLOR_ID']][$val['FABRIC_COLOR_ID']] = $val['FABRIC_COLOR_ID'];
	    	$tot_color_arr[$val['PO_ID']][$val['COLOR_ID']]++;

	    	$po_id_arr[$val['PO_ID']]=$val['PO_ID'];
	    	$job_id_array[$val['ID']] = $val['ID'];
	    }

	    // ================================== for contrust color ========================
	    foreach ($result as $val) 
	    {
	    	if($val['FABRIC_COLOR_ID']!="")
	    	{
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['order_status'] = $val['IS_CONFIRMED'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['po_received_date'] = $val['PO_RECEIVED_DATE'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['ship_date'] = $val['PUB_SHIPMENT_DATE'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['lead_time'] = $val['LEAD_TIME'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['ship_month'] = $val['SHIP_MONTH'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['buyer_name'] = $val['BUYER_NAME'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['brand_id'] = $val['BRAND_ID'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['style'] = $val['STYLE_REF_NO'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['dealing_marchant'] = $val['DEALING_MARCHANT'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['product_category'] = $val['PRODUCT_CATEGORY'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['shiping_status'] = $val['SHIPING_STATUS'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['excess_cut_perc'] = $val['EXCESS_CUT_PERC'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['smv'] = $val['SET_SMV'];

		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['item_id'] = $val['ITEM_ID'];
		    	$data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['item_id'] = $val['ITEM_ID'];

		    	// $data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['order_quantity'] += $val['ORDER_QUANTITY'];
		    	// $data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['plan_cut_qnty'] += $val['PLAN_CUT_QNTY'];

		    	// $data_array[$val['PO_ID']][$val['GMTS_COLOR_ID']][$val['FABRIC_COLOR_ID']]['order_rate'] += $val['ORDER_RATE'];
		    	$tot_color_arr[$val['PO_ID']][$val['FABRIC_COLOR_ID']]++;
		    }
	    }

	    // echo "<pre>";print_r($data_array);die();

	    // ==================================== emblishment order qty ============================
	    $job_cond = where_con_using_array($job_id_array,0,"a.job_id");
	    $po_cond = where_con_using_array($po_id_arr,0,"a.po_break_down_id");
	    $sql = "SELECT a.po_break_down_id as po_id,a.color_number_id as color_id, a.order_quantity,b.emb_name from wo_po_color_size_breakdown a, wo_pre_cost_embe_cost_dtls b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $po_cond";
	    // echo $sql;
	    $result = sql_select($sql);
	    $emb_order_qty_arr = array();
	    foreach ($result as $val) 
	    {
	    	$emb_order_qty_arr[$val['PO_ID']][$val['COLOR_ID']][$val['EMB_NAME']] += $val['ORDER_QUANTITY'];
	    }

	    /*===============================================================================/
	    /                                  Embel Name                                    /
	    /============================================================================== */
	    $job_cond = where_con_using_array($job_id_array,0,"a.job_id");
	    $po_cond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
	    $sql = "SELECT a.emb_name, b.po_break_down_id as po_id,b.color_number_id as color_id from wo_pre_cost_embe_cost_dtls a,wo_pre_cos_emb_co_avg_con_dtls b where a.id=b.pre_cost_emb_cost_dtls_id and  a.status_active=1 and a.is_deleted=0 and a.emb_type!=0 $job_cond $po_cond";
	    // echo $sql;
	    $res = sql_select($sql);
	    $emb_name_arr = array();
	    $emb_id_chk_arr = array();
	    foreach ($res as $val) 
	    {
	        if(!in_array($val['EMB_NAME'], $emb_id_chk_arr[$val['PO_ID']][$val['COLOR_ID']]))
	        {
	            $emb_name_arr[$val['PO_ID']][$val['COLOR_ID']] .= ($emb_name_arr[$val['PO_ID']][$val['COLOR_ID']]=="") ? $emblishment_name_array[$val['EMB_NAME']] : ",".$emblishment_name_array[$val['EMB_NAME']];
	            $emb_id_chk_arr[$val['PO_ID']][$val['COLOR_ID']][$val['EMB_NAME']] = $val['EMB_NAME'];
	        }
	    }

	    /*===============================================================================/
	    /                             Fabric source and Cons/dzn                         /
	    /============================================================================== */
	    $job_cond = where_con_using_array($job_id_array,0,"a.job_id");
	    $po_cond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
	    $sql = "SELECT a.id,a.fabric_source,a.avg_cons, b.po_break_down_id as po_id,b.color_number_id as color_id,b.cons from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and  a.status_active=1 and a.is_deleted=0 and b.cons!=0 $job_cond $po_cond";
	    // echo $sql;
	    $res = sql_select($sql);
	    $fab_data_arr = array();
	    $fab_cost_chk_id_chk_arr = array();

	    foreach ($res as $val) 
	    {
	        $fab_data_arr[$val['PO_ID']][$val['COLOR_ID']]['fabric_source'] .= $val['FABRIC_SOURCE'].",";	
	        if($fab_cost_chk_id_chk_arr[$val['PO_ID']][$val['COLOR_ID']][$val['ID']]=="") 
	        {       
	        	$fab_data_arr[$val['PO_ID']][$val['COLOR_ID']]['cons'] += $val['CONS'];	
	        	$fab_cost_chk_id_chk_arr[$val['PO_ID']][$val['COLOR_ID']][$val['ID']] = $val['ID'];        
	        }
	    }

	    // =========================== cm cost ===========================
	 //   $cm_cost_arr = return_library_array( "SELECT b.id, a.cm_cost from wo_pre_cost_dtls a,wo_po_break_down b where a.job_id=b.job_id and b.status_active=1 and a.status_active=1 $job_cond",'id','cm_cost');
		$sql_po=sql_select("SELECT b.id,b.po_quantity,b.plan_cut,c.color_number_id,c.order_quantity,c.plan_cut_qnty, a.cm_cost from wo_pre_cost_dtls a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_id=b.job_id and   a.job_id=c.job_id and  b.id=c.po_break_down_id and b.status_active=1 and a.status_active=1 and c.status_active=1 $job_cond");
		foreach ($sql_po as $val) 
        {
		 $po_qty_arr[$val[csf('id')]][$val[csf('color_number_id')]]+=$val[csf('order_quantity')];
		  $po_plancut_arr[$val[csf('id')]][$val[csf('color_number_id')]]+=$val[csf('plan_cut_qnty')];
		  if($val[csf('cm_cost')]>0)
		  {
		  $cm_cost_arr[$val[csf('id')]]=$val[csf('cm_cost')];	
		  }
		}
	    // echo "<pre>";print_r($cm_cost_arr);die();

	    // ======================================= PP SAMPLE  ==================================
	    $po_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");
        $sqlLabSM = "SELECT po_break_down_id,color_number_id from wo_po_sample_approval_info where approval_status=1 $po_cond and sample_type_id=7 and status_active=1 and is_deleted=0 group by po_break_down_id,color_number_id";
        // echo $sqlLabSM;
        $sqlLabSMRes = sql_select($sqlLabSM);
        $pp_status = array();
        foreach ($sqlLabSMRes as $val) 
        {
            $pp_status[$val['PO_BREAK_DOWN_ID']][$val['COLOR_NUMBER_ID']]++;
        }
        unset($sqlLabSMRes);
        // print_r($pp_status);

        //======================================== YARN ISSUE ====================================
        $po_cond = where_con_using_array($po_id_arr,0,"c.po_breakdown_id");
        $sqlYrnIss = "SELECT c.po_breakdown_id as po_id,sum(case when b.transaction_type=2 then c.quantity else 0 end) as issue_qnty,sum(case when b.transaction_type=4 then c.quantity else 0 end) as rtn_qty from inv_issue_master a,inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type in(2,4) $po_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=3 and a.item_category=1 group by c.po_breakdown_id";
        // echo $sqlYrnIss;die();
        $sqlYrnIssRes = sql_select($sqlYrnIss);
        $yarnIssueQtyArr = array();
        foreach ($sqlYrnIssRes as $val) 
        {
            $yarnIssueQtyArr[$val['PO_ID']]['issue'] = $val['ISSUE_QNTY'];
            $yarnIssueQtyArr[$val['PO_ID']]['rtn'] = $val['RTN_QTY'];
        }

        // ======================================= grey fab prod ==============================
        $sqlGrey = "SELECT b.color_id, c.po_breakdown_id,sum(c.quantity) as qnty from inv_receive_master a,pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 $po_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=2 and c.entry_form=2 and a.item_category=13 group by b.color_id,c.po_breakdown_id";
        // echo $sqlGrey;die();
        $greyRes = sql_select($sqlGrey);
        $greyProdArr = array();
        foreach ($greyRes as  $val) 
        {
           $greyProdArr[$val['PO_BREAKDOWN_ID']][$val['COLOR_ID']]['qty']     += $val['QNTY'];
        }

        // ======================================= fab dyeing production ==============================
        $po_cond = where_con_using_array($po_id_arr,0,"c.po_breakdown_id");
        $sqlDye = "SELECT a.color_id,b.po_id,sum(b.batch_qnty) as qty from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id $po_cond and c.load_unload_id=2 and c.process_id='31' and c.result=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.extention_no is null group by b.po_id";
        // echo $sqlDye;die();
        $dyeRes = sql_select($sqlDye);
        $dyeingProdArr = array();
        foreach ($dyeRes as $val) 
        {
           $dyeingProdArr[$val['PO_ID']][$val['COLOR_ID']]['qty'] += $val['QTY'];
        }

        // =================================== fin fab rcv ==========================================
        $po_cond = where_con_using_array($po_id_arr,0,"c.po_breakdown_id");
	    $sqlFinFab = "SELECT c.po_breakdown_id, b.color_id,sum(c.quantity) AS QNTY from inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 $po_cond group by c.po_breakdown_id, b.color_id";
	    // echo $sqlFinFab;
	    $finRes = sql_select($sqlFinFab);
	    $fin_fab_rcv_arr = array();
	    foreach ($finRes as $val) 
	    {
	    	$fin_fab_rcv_arr[$val['PO_BREAKDOWN_ID']][$val['COLOR_ID']] += $val['QNTY'];
	    }
	    // echo "<pre>";print_r($fin_fab_rcv_arr);die();

	    // =================================== fin fab issue/delivery ==========================================
	    $sqlIssue = "SELECT d.COLOR,c.PO_BREAKDOWN_ID,sum(c.quantity) AS QNTY from inv_issue_master a,inv_finish_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=18 and a.item_category=2 $po_cond group by d.color,c.po_breakdown_id";
	    // echo $sqlIssue;
	    $res = sql_select($sqlIssue);
	    $fin_fab_issue_arr = array();
	    foreach ($res as $val) 
	    {
	    	$fin_fab_issue_arr[$val['PO_BREAKDOWN_ID']][$val['COLOR']] += $val['QNTY'];
	    }
	    // echo "<pre>";print_r($fin_fab_issue_arr);die();

	    /*===============================================================================/
	    /                                  gmts prod data                                /
	    /============================================================================== */
	    $po_cond = where_con_using_array($po_id_arr,0,"a.po_break_down_id");
	    $sql = "SELECT c.po_break_down_id as po_id,c.color_number_id as color_id,a.floor_id,a.sewing_line,a.production_source,a.prod_reso_allo,a.production_type,a.embel_name,b.production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_type in(1,2,3,4,5,7,8) $po_cond";
	    // echo $sql;
	    $result = sql_select($sql);
	    $gmts_prod_data = array();
	    foreach ($result as $val) 
	    {
	    	$gmts_prod_data[$val['PO_ID']][$val['COLOR_ID']][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];
	    	
	    	$gmts_prod_data[$val['PO_ID']][$val['COLOR_ID']][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['floor'] = $floor_arr[$val['FLOOR_ID']];
	    	$gmts_prod_data[$val['PO_ID']][$val['COLOR_ID']][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['source'] = $val['PRODUCTION_SOURCE'];

	    	if($val['PRODUCTION_TYPE']==4)
	    	{
		    	$sewing_line = "";
		    	if($val['PROD_RESO_ALLO']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
					foreach($line_number as $vals)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$vals]; else $sewing_line.=",".$lineArr[$vals];
					}
				}
				else 
				{
					$sewing_line=$lineArr[$val['SEWING_LINE']];
				}

		    	$gmts_prod_data[$val['PO_ID']][$val['COLOR_ID']][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['sewing_line'] .= $sewing_line."**";
		    }
	    }
	    // echo "<pre>";print_r($gmts_prod_data);die();


	    /*===============================================================================/
	    /                                  shipment  data                                /
	    /============================================================================== */
	    $sql = "SELECT c.po_break_down_id as po_id,c.color_number_id as color_id,a.ex_factory_date, b.production_qnty from pro_ex_factory_mst a, pro_ex_factory_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_cond";
	    // echo $sql;
	    $result = sql_select($sql);

	    $shipment_data = array();
	    foreach ($result as $val) 
	    {
	    	$shipment_data[$val['PO_ID']][$val['COLOR_ID']]['date'] = $val['EX_FACTORY_DATE'];
	    	$shipment_data[$val['PO_ID']][$val['COLOR_ID']]['qty'] += $val['PRODUCTION_QNTY'];
	    }

	    $poIDS = implode(",", $po_id_arr);
		$condition= new condition();     
	    $condition->po_id_in($poIDS);     
	    $condition->init();
	    $fabric= new fabric($condition);	
	    // $fabricReqQtyArr= $fabric->getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish();
	    $fabricReqQtyArr= $fabric->getQtyArray_by_OrderGmtsColorFabricColorAndFabricSouce_greyAndfinish();
	    $fabricReqQtyArrbyOrder= $fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
	    // echo "<pre>";print_r($fabricReqQtyArr);die();

	    $req_qty_array = array();
	    foreach ($fabricReqQtyArr as $g_type => $g_type_data) 
	    {
	    	foreach ($g_type_data as $f_type => $f_type_data) 
	    	{
	    		foreach ($f_type_data as $poid => $po_data) 
			    {
		    		foreach ($po_data as $g_color => $g_color_data) 
			    	{
			    		foreach ($g_color_data as $f_color => $row) 
				    	{
				    		// print_r($row);die();
				    		$f_color = ($f_color!="") ? $f_color :$g_color;
				    		$req_qty_array[$g_type][$f_type][$poid][$g_color][$f_color] += $row[1][12]+$row[2][12];
				    	}
			    	}
			    }
	    	}
	    }

	    // echo "<pre>";print_r($req_qty_array);die();

	    $rowspan = array();
	    foreach ($data_array as $po => $po_value) 
	    {
	    	foreach ($po_value as $color => $gmts_col_ata) 
	    	{
	    		foreach ($gmts_col_ata as $fab_color => $row) 
	    		{
	    			$rowspan[$po]++;
	    		}
	    	}
	    }

		ob_start();
		
		$width=4800;
		// 75 column
		?>
		<fieldset>
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
	            <p>Shipment Schedule Report[Order Book]</p>
	            <p>Company : <?=$company_name_arr[$company_name]; ?></p>
	        </div>

			<!-- =================== header part ======================= -->
			<div>
				<table border="1" cellspacing="0" cellpadding="0" class="rpt_table" width="<?=$width;?>" align="left">
					<thead>
						<tr>
							<th width="20"><p>Sl</p></th>
							<th width="80"><p>Order status</p></th>
							<th width="60"><p>PO receive date</p></th>
							<th width="60"><p>Ship Date</p></th>
							<th width="50"><p>Lead time</p></th>
							<th width="60"><p>Ship month</p></th>
							<th width="100"><p>Buyer</p></th>
							<th width="80"><p>Brand</p></th>
							<th width="100"><p>Style</p></th>
							<th width="100"><p>Po No</p></th>
							<th width="100"><p>Item Description</p></th>
							<th width="100"><p>Gmts Color</p></th>

							<th width="100"><p>Fabric Color</p></th>
							<th width="60"><p>Grey Req</p></th>
							<th width="60"><p>Yarn Issue</p></th>
							<th width="60"><p>Yarn balance to issue</p></th>
							<th width="60"><p>Grey prod</p></th>
							<th width="60"><p>Grey balance</p></th>
							<th width="60"><p>Dyeing done</p></th>
							<th width="60"><p>Dyeing balacne</p></th>
							<th width="60"><p>Fabric Rcv Start date</p></th>
							<th width="60"><p>Fabric Rcv End date</p></th>
							<th width="80"><p>Dealing Merchant</p></th>
							<th width="80"><p>Fabric Source</p></th>
							<th width="50"><p>Con/Dzn</p></th>
							<th width="60"><p>Fin Fab Req</p></th>
							<th width="60"><p>Fabric In-house</p></th>
							<th width="60"><p>Bal to in-house</p></th>
							<th width="60"><p>Total issued</p></th>
							<th width="60"><p>Stock fabric</p></th>

							<th width="60"><p>Embellishment</p></th>
							<th width="70"><p>Order Qty(Pcs)</p></th>
							<th width="50"><p>PPS status</p></th>

							<th width="60"><p>Cutting (%)</p></th>
							<th width="60"><p>Cutting Qty</p></th>
							<th width="60"><p>Cutting balance</p></th>

							<th width="60"><p>Print order qty (Pcs)</p></th>
							<th width="80"><p>Printing unit</p></th>
							<th width="60"><p>Total send</p></th>
							<th width="60"><p>Total receive</p></th>
							<th width="60"><p>Balance to Send for Print</p></th>
							<th width="60"><p>Print balance</p></th>

							<th width="60"><p>Emb order qty(Pcs)</p></th>
							<th width="60"><p>Emb unit</p></th>
							<th width="60"><p>Send to Emb</p></th>
							<th width="60"><p>Emb done</p></th>
							<th width="60"><p>Balance to Send for Emb</p></th>
							<th width="60"><p>Emb balance</p></th>

							<th width="60"><p>Sewing Input Qty</p></th>
							<th width="60"><p>Sewing Input Balance</p></th>
							<th width="60"><p>Sewing Production source</p></th>
							<th width="100"><p>Line no</p></th>
							<th width="60"><p>Total sewing</p></th>
							<th width="60"><p>Sewing balance</p></th>
							<th width="60"><p>Total Finish pcs</p></th>
							<th width="60"><p>Finish balance</p></th>

							<th width="60"><p>Wash order qty(Pcs)</p></th>
							<th width="80"><p>Wash Unit</p></th>
							<th width="60"><p>Send to wash</p></th>
							<th width="60"><p>Recceive from wah</p></th>
							<th width="60"><p>Balance to Send for Wash</p></th>
							<th width="60"><p>Wash balance</p></th>

							<th width="60"><p>Actual Handover date</p></th>
							<th width="60"><p>Shipped (Pcs)</p></th>
							<th width="60"><p>Balance to ship (Pcs)</p></th>
							<th width="60"><p>Shipped status</p></th>
							<th width="60"><p>FOB Price</p></th>
							<th width="60"><p>PO value</p></th>
							<th width="60"><p>Value balace to ship</p></th>
							<th width="60"><p>CM/Dzn</p></th>
							<th width="60"><p>Total CM</p></th>
							<th width="60"><p>SMV</p></th>
							<th width="60"><p>Order SAH</p></th>
							<th width="60"><p>Balance SAH</p></th>
							<th width="80"><p>Product category</p></th>

						</tr>
					</thead>
					
				</table>
			</div>
			<!-- =================== body part ======================= -->
			<div style=" max-height:300px; width:<?=$width+20;?>px; overflow-y:scroll;" id="scroll_body">
				<table border="1" cellspacing="0" cellpadding="0" class="rpt_table" width="<?=$width;?>" id="table_body" align="left">
					<tbody>
						<?
						$i=1;
						$j=1;
						$gr_order_qty = 0;
						$gr_fin_fab_req = 0;
						$gr_fin_fab_rcv = 0;
						$gr_fin_fab_bal = 0;
						$gr_fin_fab_iss = 0;
						$gr_fin_fab_stk = 0;
						$gr_cutting_qty = 0;
						$gr_cutting_bal = 0;
						$gr_prnt_odr_qty = 0;
						$gr_prnt_iss_qty = 0;
						$gr_prnt_rcv_qty = 0;
						$gr_prnt_bal_to_snd = 0;
						$gr_prnt_bal_qty = 0;
						$gr_emb_odr_qty = 0;
						$gr_emb_iss_qty = 0;
						$gr_emb_rcv_qty = 0;
						$gr_emb_bal_to_snd = 0;
						$gr_emb_bal_qty = 0;

						$gr_sew_in_qty = 0;
						$gr_sew_in_bal = 0;
						$gr_sew_out_qty = 0;
						$gr_sew_out_bal = 0;
						$gr_finish_qty = 0;
						$gr_finish_bal = 0;

						$gr_wash_odr_qty = 0;
						$gr_wash_iss_qty = 0;
						$gr_wash_rcv_qty = 0;
						$gr_wash_bal_to_snd = 0;
						$gr_wash_bal_qty = 0;

						$gr_ex_qty = 0;
						$gr_ex_bal = 0;
						$gr_fob_price = 0;
						$gr_fob_value = 0;
						$gr_value_bal_to_ex = 0;

						$gr_tot_cm_dzn = 0;
						$gr_tot_cm = 0;
						$gr_order_sha = 0;
						$gr_bal_sha = 0;

						$gr_grey_req = 0;
						$gr_yarn_issue = 0;
						$gr_yarn_issue_bal = 0;
						$gr_grey_prod = 0;
						$gr_grey_prod_bal = 0;
						$gr_dyeing_prod = 0;
						$gr_dyeing_prod_bal = 0;

						foreach ($data_array as $po_id => $po_data) 
						{
							$p=0;
							foreach ($po_data as $color_id => $gmts_color_data) 
							{
								$g = 0;$po_qty=$po_qty_arr[$po_id][$color_id];$po_plan_cut=$po_plancut_arr[$po_id][$color_id];
								foreach ($gmts_color_data as $fab_color_id => $row) 
								{
									$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF"; 
									$embel_name = $emb_name_arr[$po_id][$color_id];
										$row['order_quantity']='';
										$row['order_quantity']=$po_qty;
									$unit_price = $row['order_rate']/$tot_color_arr[$po_id][$color_id];
									$po_value = $row['order_quantity']*$unit_price;

									$total_cm = ($cm_cost_arr[$po_id]*$row['order_quantity'])/12;



									$cons = $fab_data_arr[$po_id][$color_id]['cons'];
									$source_arr = array_unique(array_filter(explode(",", $fab_data_arr[$po_id][$color_id]['fabric_source'])));
                                    $fabricSource = "";
									foreach ($source_arr as $val) 
									{
										$fabricSource .= ($fabricSource=="") ? $fabric_source[$val] : ", ".$fabric_source[$val];
									}

									$grey_fabreq = array_sum($fabricReqQtyArrbyOrder['knit']['grey'][$po_id]);
									$yarnIssue = $yarnIssueQtyArr[$po_id]['issue'];
									$yarnIssueBal = $grey_fabreq - $yarnIssue;

									// $fin_fab_req = array_sum($fabricReqQtyArr['knit']['finish'][$po_id][$fab_color_id]);
									// $grey_fab_req = array_sum($fabricReqQtyArr['knit']['grey'][$po_id][$fab_color_id]);
									$fin_fab_req = $req_qty_array['knit']['finish'][$po_id][$color_id][$fab_color_id];
									$grey_fab_req = $req_qty_array['knit']['grey'][$po_id][$color_id][$fab_color_id];

									$greyProd = $greyProdArr[$po_id][$fab_color_id]['qty'];
									$greyBalance = $grey_fab_req - $greyProdArr[$po_id][$fab_color_id]['qty'];

									$dyeing_qty = $dyeingProdArr[$po_id][$fab_color_id]['qty'];
									$dyeing_bal = $grey_fab_req - $dyeing_qty;

									$fin_fab_rcv = $fin_fab_rcv_arr[$po_id][$fab_color_id];
									// echo $po_id."=".$fab_color_id."=".$fin_fab_rcv_arr[$po_id][$fab_color_id]."<br>";
									$bal_in_house = $fin_fab_req - $fin_fab_rcv;
									$fin_fab_issue = $fin_fab_issue_arr[$po_id][$fab_color_id];
									$stock_fab = $fin_fab_rcv - $fin_fab_issue;

									$print_ord_qty = $emb_order_qty_arr[$po_id][$color_id][1];
									$emb_ord_qty = $emb_order_qty_arr[$po_id][$color_id][2];
									$wash_ord_qty = $emb_order_qty_arr[$po_id][$color_id][3];
									$spw_ord_qty = $emb_order_qty_arr[$po_id][$color_id][4];
									$row['plan_cut_qnty']='';
									$row['plan_cut_qnty']=$po_plan_cut;
									
									$cut_qty = $gmts_prod_data[$po_id][$color_id][1][0]['qty'];
									$cut_bal_qty = $row['plan_cut_qnty'] - $gmts_prod_data[$po_id][$color_id][1][0]['qty'];

									$print_floor = $gmts_prod_data[$po_id][$color_id][2][1]['floor'];
									$print_issue = $gmts_prod_data[$po_id][$color_id][2][1]['qty'];
									$print_rcv = $gmts_prod_data[$po_id][$color_id][3][1]['qty'];
									$bal_to_snd_print = $row['plan_cut_qnty'] - $print_issue;
									$print_bal = $print_issue - $print_rcv;

									$emb_floor = $gmts_prod_data[$po_id][$color_id][2][2]['floor'];
									$emb_issue = $gmts_prod_data[$po_id][$color_id][2][2]['qty'];
									$emb_rcv = $gmts_prod_data[$po_id][$color_id][3][2]['qty'];
									$bal_to_snd_emb = $row['plan_cut_qnty'] - $emb_issue;
									$emb_bal = $emb_issue - $emb_rcv;

									$wash_floor = $gmts_prod_data[$po_id][$color_id][2][3]['floor'];
									$wash_issue = $gmts_prod_data[$po_id][$color_id][2][3]['qty'];
									$wash_rcv = $gmts_prod_data[$po_id][$color_id][3][3]['qty'];
									$bal_to_snd_wash = $row['plan_cut_qnty'] - $wash_issue;
									$wash_bal = $wash_issue - $wash_rcv;

									$sewin_in_qty = $gmts_prod_data[$po_id][$color_id][4][0]['qty'];
									$input_bal = $row['plan_cut_qnty'] - $sewin_in_qty;
									$source = $knitting_source[$gmts_prod_data[$po_id][$color_id][5][0]['source']];

									$sewing_line_arr = array_unique(array_filter(explode("**", $gmts_prod_data[$po_id][$color_id][4][0]['sewing_line'])));
									$line_name = "";
									foreach ($sewing_line_arr as $val) 
									{
										$line_name .= ($line_name=="") ? $val : ", ".$val;
									}
									

									$sewin_out_qty = $gmts_prod_data[$po_id][$color_id][5][0]['qty'];
									$sewin_bal = $row['plan_cut_qnty'] - $sewin_out_qty;

									$finish_qty = $gmts_prod_data[$po_id][$color_id][8][0]['qty'];
									$finish_bal = $row['order_quantity'] - $finish_qty;

									$order_sha = ($row['order_quantity']*$row['smv'])/60;
									$order_sha_val = ($finish_qty*$row['smv'])/60;

									$ex_date = $shipment_data[$po_id][$color_id]['date'];
									$ex_qty = $shipment_data[$po_id][$color_id]['qty'];
									$ex_balance = $row['order_quantity'] - $ex_qty;
									$ex_val_bal = $ex_balance*$unit_price;

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
										<? 
										if($g==0)
										{ 
											?>
											<td rowspan="<?=count($gmts_color_data);?>" width="20"><p><?=$j;?></p></td>
											
											<td rowspan="<?=count($gmts_color_data);?>" width="80"><p><?=$order_status[$row['order_status']];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60"><p><?=$row['po_received_date'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60"><p><?=$row['ship_date'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="50"><p><?=$row['lead_time'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60"><p><?=$row['ship_month'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="100"><p><?=$buyer_arr[$row['buyer_name']];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="80"><p><?=$brand_library[$row['brand_id']];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="100"><p><?=$row['style'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="100"><p><?=$row['po_number'];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="100"><p><?=$garments_item[$row['item_id']];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" rowspan="<?=count($gmts_color_data);?>" width="100" title="<?=$color_id;?>"><p><?=$color_arr[$color_id];?></p></td>
											<? 
										}
                                        $source_arr_re = array_unique($source_arr);
                                        if($source_arr_re[0] == 2){
                                            $grey_fab_req = 0;
                                            $yarnIssue = 0;
                                            $yarnIssueBal = 0;
                                            $greyProd = 0;
                                            $greyBalance = 0;
                                            $dyeing_qty = 0;
                                            $dyeing_bal = 0;
                                            $a = 0;
                                        }
										?>
										<td width="100" title="<?=$fab_color_id;?>"><p><?=$color_arr[$fab_color_id];?></p></td>
										<td width="60" align="right"><p><?=number_format($grey_fab_req,2);?></p></td>
										<? if($p==0)
										{
											?>
											<td rowspan="<?=$rowspan[$po_id];?>" width="60" align="right"><p><?=number_format($yarnIssue,2);?></p></td>
											<td rowspan="<?=$rowspan[$po_id];?>" width="60" align="right"><p><?=number_format($yarnIssueBal,2);?></p></td>
											<? 
											$p++;
											$gr_yarn_issue += $yarnIssue;
											$gr_yarn_issue_bal += $yarnIssueBal;
										}
										?>
										<td width="60" align="right"><p><?=number_format($greyProd,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($greyBalance,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($dyeing_qty,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($dyeing_bal,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($a,0);?></p></td>
										<td width="60" align="right"><p><?=number_format($a,0);?></p></td>
										<td width="80" align="left"><p><?=$merchant_library[$row['dealing_marchant']];?></p></td>
										<td width="80" align="left"><p><?=$fabricSource;?></p></td>
										<td width="50" align="right"><p><?=$cons;?></p></td>
										<td width="60" align="right"><p><?=number_format($fin_fab_req,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($fin_fab_rcv,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($bal_in_house,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($fin_fab_issue,2);?></p></td>
										<td width="60" align="right"><p><?=number_format($stock_fab,2);?></p></td>

										<? 
										if($g==0)
										{
											?>
											
											<td rowspan="<?=count($gmts_color_data);?>" width="60"><p><?=$embel_name;?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="70" align="right"><p><?=$row['order_quantity'];?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="50" align="center"><p><?=($pp_status[$po_id][$color_id]) ? "Yes" : "";?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($row['excess_cut_perc'],2);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($cut_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($cut_bal_qty,0);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($print_ord_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="80" align="right"><p><?=$print_floor;?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($print_issue,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($print_rcv,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($bal_to_snd_print,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($print_bal,0);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($emb_order_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($emb_floor,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($emb_issue,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($emb_rcv,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($bal_to_snd_emb,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($emb_bal,0);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($sewin_in_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($input_bal,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="center"><p><?=$source;?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="100" align="left"><p><?=$line_name;?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($sewin_out_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($sewin_bal,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($finish_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($finish_bal,0);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($wash_ord_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="80" align="right"><p><?=number_format($wash_floor,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($wash_issue,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($wash_rcv,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($bal_to_snd_wash,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($wash_bal,0);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=change_date_format($ex_date);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($ex_qty,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($ex_balance,0);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=$shipment_status[$row['shiping_status']];?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($unit_price,2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($po_value,2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($ex_val_bal,2);?></p></td>

											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($cm_cost_arr[$po_id],2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($total_cm,2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($row['smv'],2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($order_sha,2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="60" align="right"><p><?=number_format($order_sha_val,2);?></p></td>
											<td rowspan="<?=count($gmts_color_data);?>" width="80" align="left"><p><?=$product_category[$row['product_category']];?></p></td>
											<? 
											$g++;
											$j++;

											$gr_order_qty += $row['order_quantity'];
											$gr_cutting_qty += $cut_qty;
											$gr_cutting_bal += $cut_bal_qty;
											$gr_prnt_odr_qty += $print_ord_qty;
											$gr_prnt_iss_qty += $print_issue;
											$gr_prnt_rcv_qty += $print_rcv;
											$gr_prnt_bal_to_snd += $bal_to_snd_print;
											$gr_prnt_bal_qty += $print_bal;
											$gr_emb_odr_qty += $emb_order_qty;
											$gr_emb_iss_qty += $emb_issue;
											$gr_emb_rcv_qty += $emb_rcv;
											$gr_emb_bal_to_snd += $bal_to_snd_emb;
											$gr_emb_bal_qty += $emb_bal;

											$gr_sew_in_qty += $sewin_in_qty;
											$gr_sew_in_bal += $input_bal;
											$gr_sew_out_qty += $sewin_out_qty;
											$gr_sew_out_bal += $sewin_bal;
											$gr_finish_qty += $finish_qty;
											$gr_finish_bal += $finish_bal;

											$gr_wash_odr_qty += $wash_ord_qty;
											$gr_wash_iss_qty += $wash_iss_qty;
											$gr_wash_rcv_qty += $wash_rcv_qty;
											$gr_wash_bal_to_snd += $bal_to_snd_wash;
											$gr_wash_bal_qty += $wash_bal;

											$gr_ex_qty += $ex_qty;
											$gr_ex_bal += $ex_balance;
											$gr_fob_price += $fob_price;
											$gr_fob_value += $po_value;
											$gr_value_bal_to_ex += $ex_val_bal;

											$gr_tot_cm_dzn += $cm_cost_arr[$po_id];
											$gr_tot_cm += $total_cm;
											$gr_order_sha += $order_sha;
											$gr_bal_sha += $order_sha_val;
										}
										?>
									</tr>
									<?
									$i++;
									$gr_fin_fab_req += $fin_fab_req;
									$gr_fin_fab_rcv += $fin_fab_rcv;
									$gr_fin_fab_bal += $bal_in_house;
									$gr_fin_fab_iss += $fin_fab_issue;
									$gr_fin_fab_stk += $fin_fab_stk;

									$gr_grey_req += $grey_fab_req;
									$gr_grey_prod += $greyProd;
									$gr_grey_prod_bal += $greyBalance;
									$gr_dyeing_prod += $dyeing_qty;
									$gr_dyeing_prod_bal += $dyeing_bal;
								}
							}
						}
						?>
					</tbody>
					
				</table>
			</div>
			<!-- =================== footer part ======================= -->
			<div>
				<table cellspacing="0" cellpadding="0" class="rpt_table" width="<?=$width;?>" align="left">
					<tfoot>
						<tr>
							<th width="20"><p></p></th>
							<th width="80"><p></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p></p></th>
							<th width="50"><p></p></th>
							<th width="60"><p></p></th>
							<th width="100"><p></p></th>
							<th width="80"><p></p></th>
							<th width="100"><p></p></th>
							<th width="100"><p></p></th>
							<th width="100"><p></p></th>
							<th width="100"><p></p></th>
							<th width="100">Total<p></p></th>
							<th width="60"><p><?=number_format($gr_grey_req,2);?></p></th>
							<th width="60"><p><?=number_format($gr_yarn_issue,2);?></p></th>
							<th width="60"><p><?=number_format($gr_yarn_issue_bal,2);?></p></th>
							<th width="60"><p><?=number_format($gr_grey_prod,2);?></p></th>
							<th width="60"><p><?=number_format($gr_grey_prod_bal,2);?></p></th>
							<th width="60"><p><?=number_format($gr_dyeing_prod,2);?></p></th>
							<th width="60"><p><?=number_format($gr_dyeing_prod_bal,2);?></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p></p></th>
							<th width="80"><p></p></th>
							<th width="80"><p></p></th>
							<th width="50"><p></p></th>
							<th width="60"><p><?=number_format($gr_fin_fab_req,2);?></p></th>
							<th width="60"><p><?=number_format($gr_fin_fab_rcv,2);?></p></th>
							<th width="60"><p><?=number_format($gr_fin_fab_bal,2);?></p></th>
							<th width="60"><p><?=number_format($gr_fin_fab_iss,2);?></p></th>
							<th width="60"><p><?=number_format($gr_fin_fab_stk,2);?></p></th>

							
							<th width="60"><p></p></th>
							<th width="70"><p><?=number_format($gr_order_qty,0);?></p></th>
							<th width="50"><p></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p><?=number_format($gr_cutting_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_cutting_bal,0);?></p></th>
							<th width="60"><p><?=number_format($gr_prnt_odr_qty,0);?></p></th>
							<th width="80"><p></p></th>
							<th width="60"><p><?=number_format($gr_prnt_iss_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_prnt_rcv_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_prnt_bal_to_snd,0);?></p></th>
							<th width="60"><p><?=number_format($gr_prnt_bal_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_emb_odr_qty,0);?></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p><?=number_format($gr_emb_iss_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_emb_rcv_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_emb_bal_to_snd,0);?></p></th>
							<th width="60"><p><?=number_format($gr_emb_bal_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_sew_in_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_sew_in_bal,0);?></p></th>
							<th width="60"><p></p></th>
							<th width="100"><p></p></th>
							<th width="60"><p><?=number_format($gr_sew_out_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_sew_out_bal,0);?></p></th>
							<th width="60"><p><?=number_format($gr_finish_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_finish_bal,0);?></p></th>
							<th width="60"><p><?=number_format($gr_wash_odr_qty,0);?></p></th>
							<th width="80"><p></p></th>
							<th width="60"><p><?=number_format($gr_wash_iss_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_wash_rcv_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_wash_bal_to_snd,0);?></p></th>
							<th width="60"><p><?=number_format($gr_wash_bal_qty,0);?></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p><?=number_format($gr_ex_qty,0);?></p></th>
							<th width="60"><p><?=number_format($gr_ex_bal,0);?></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p><?=number_format($gr_fob_price,2);?></p></th>
							<th width="60"><p><?=number_format($gr_fob_value,2);?></p></th>
							<th width="60"><p><?=number_format($gr_value_bal_to_ex,2);?></p></th>
							<th width="60"><p><?=number_format($gr_tot_cm_dzn,2);?></p></th>
							<th width="60"><p><?=number_format($gr_tot_cm,2);?></p></th>
							<th width="60"><p></p></th>
							<th width="60"><p><?=number_format($gr_order_sha,2);?></p></th>
							<th width="60"><p><?=number_format($gr_bal_sha,2);?></p></th>
							<th width="80"><p></p></th>
						</tr>
					</tfoot>
					
				</table>
			</div>
		</fieldset>
		<?	
	}
	else if($rpt_type==10)//Details 4
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
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
		$condition= new condition();
			$condition->company_name("=$company_name");
		  if(str_replace("'","",$buyer_name)>0){
			  $condition->buyer_name("=$buyer_name");
		 }
		 //$txt_file=str_replace("'","",$txt_file);
		//$txt_ref
		 if($search_string!='' || $search_string!=0)
		 {
			$condition->po_number("in('$search_string')");
		 } 
		 if(str_replace("'","",$txt_ref)!='')
		 {
				$condition->grouping("='$txt_ref'"); 
		 }
		 if(str_replace("'","",$txt_file)!='')
		 {
			$condition->file_no("in('$txt_file')");
		 }
		 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  //$condition->country_ship_date(" between '$start_date' and '$end_date'");		  
				   if($category_by==1)
					{
						//if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==2)
					{
						//if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->po_received_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==3)
					{
					   if($db_type==0)
						{
						 $condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
						}
						else
						{
							$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
						}
					}
		 }
			 
		$condition->init();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order(); 
		
		//$commission= new commision($condition);
		//$commission_costing_sum_arr=$commission->getAmountArray_by_order();
				
		
	   $sql_data="SELECT a.id as job_id,a.job_no_prefix_num,a.set_break_down, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name,a.set_smv, a.agent_name, a.style_ref_no,a.brand_id, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $order_confirm_status_con $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $shipment_status_cond $ref_cond $season_cond $brand_cond group by a.id,a.job_no_prefix_num, a.job_no, a.set_break_down,a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no,a.brand_id, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom,a.set_smv,a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  $data_array=sql_select( $sql_data);
	  $all_po_id_arr=array();
	  foreach($data_array as $row) //
	  {
	  	$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$job_id_arr[$row[csf('po_id')]]=$row[csf('job_id')];
		
		$po_wise_arr[$row[csf('po_id')]]['set_break_down']=$row[csf('set_break_down')];
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
		$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
		$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
		$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
		$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
		$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
		$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
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
		$po_wise_arr[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		
		$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		//Company Buyer Wise
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
		$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		
		//Sumary
		$month_wise_arr[$pub_date_key]=$pub_date_key;
		$summ_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['smv_min']+=$row[csf('set_smv')]*($row[csf('po_quantity')]);
		//echo $summ_cm_cost.'='.$row[csf('po_quantity')]*$row[csf('total_set_qnty')].',';
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['cm_value']+=$summ_cm_cost;
		$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
		
	  }
	 // asort($month_wise_arr);
		//  print_r($month_wise_arr);
		// $poIds=chop($all_po_id,','); 
		$poIds=implode(",", $all_po_id_arr); 
		$po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=$all_po_id_arr;
		// print_r($all_po_id_arr);die();
			if($db_type==2 && count($all_po_id_arr)>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$po_cond_for_in3=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or";
					$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
				$po_cond_for_in3=chop($po_cond_for_in3,'or ');
				$po_cond_for_in3.=")";
			}
			else
			{
				$po_cond_for_in=" and b.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
				$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
			}
 
		$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");

		/*echo "SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id";*/
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			//echo $shiping_status_id.', ';
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			//Buyer Wise
		//	$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			if($shiping_status_id==3)//Full shipped
			{
				//echo $row[csf('ex_factory_qnty')].'dd';
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			else if($shiping_status_id==2)//Partial shipped
			{
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			//$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['return_qty']=$row[csf('ex_factory_return_qnty')];
		}
		
		if($db_type==0)
			{
				$fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
			}
			else if($db_type==2)
			{
				$fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			}
			//echo "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no $po_cond_for_in2 ";
		//	echo  "select c.job_no,c.cm_for_sipment_sche as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ";die;
		//	$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');
		//	print_r($cm_for_shipment_schedule_arr);
		
		$sql_pre="SELECT a.costing_per,a.approved, c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_mst a,wo_pre_cost_dtls c,wo_po_break_down b where a.job_no=b.job_no_mst and  c.job_no=b.job_no_mst  $po_cond_for_in2 ";
		 $data_budget_pre=sql_select($sql_pre);
			foreach ($data_budget_pre as $row)
			{
				$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
				$job_approved_arr[$row[csf('job_no')]]=$row[csf('approved')];
			}
		  $sql_budget="SELECT a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		   $data_budget_array=sql_select($sql_budget);
		
			$fabric_arr=array();
			foreach ($data_budget_array as $row)
			{
				$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				if($row[csf('yarn_cons_qnty')]>0)
				{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			
				}
					//$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				//$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
			}
				//var_dump($fabric_arr);die;
				$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
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
			else
			{
				$lc_cond_for_in=" and export_lc_id in($lcIds)";
			}
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		 
		/*$set_item_arr = sql_select("SELECT gmts_item_id,job_id,smv_set  FROM wo_po_details_mas_set_details where  smv_set>0 ".where_con_using_array($job_id_arr,0,'job_id')."");
		//echo "SELECT gmts_item_id,job_id,smv_set  FROM wo_po_details_mas_set_details where smv_set>0  ".where_con_using_array($job_id_arr,0,'job_id')."";die;
		foreach($set_item_arr as $row)
		{
			$set_item_arr[$row[csf('job_id')]][$row[csf('gmts_item_id')]] = $row[csf('smv_set')];
		}
		unset($set_item_arr);*/
		
		$cut_qty_sql_res = sql_select("SELECT JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COUNTRY_SHIP_DATE,PLAN_CUT_QNTY  FROM WO_PO_COLOR_SIZE_BREAKDOWN where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."");
		$cut_qty_arr=array();
		foreach($cut_qty_sql_res as $rows)
		{
			
			$key=date("M-Y",strtotime($rows[COUNTRY_SHIP_DATE]));
			$comapny_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['company_name'];
			$buyer_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['buyer_name'];
			$cut_qty_by_month_arr[$comapny_id][$buyer_id][$key] += $rows[PLAN_CUT_QNTY];
			$cut_qty_dtls_arr[$rows[JOB_NO_MST]][$rows[PO_BREAK_DOWN_ID]] += $rows[PLAN_CUT_QNTY];
		}
		
		//var_dump($cut_qty_by_month_arr);die;
		
		$net_export_val_result=sql_select("select B.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/b.current_invoice_value)*b.current_invoice_qnty) AS PO_NET_INVO_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAKDOWN_ID')."");	

		foreach($net_export_val_result as $row){
			$net_export_val_arr[$row[PO_BREAKDOWN_ID]]=$row[PO_NET_INVO_VALUE];
		}
		
		//print_r($net_export_val_arr);die;		
		
		ob_start();
		$tot_width=730+count($month_wise_arr)*320;
		
		?>
		<div align="center">
			<div align="center">
			<table width="<? echo $tot_width;?>" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
					<th colspan="3">&nbsp; </th>
						<?
						foreach($month_wise_arr as $date_key=>$val_data)
						{
						?>
						<th title="<? echo count($val_data);?>"  colspan="5"><? echo $date_key;?></th>
						<?
						}
						?>
                        <th colspan="5">Total</th>
					</tr>
					<tr>
						<th width="20">SL</th>
						<th width="100">Company Name</th>
						<th width="100">Buyer Name</th>
						<?
						foreach($month_wise_arr as $date_key=>$val)
						{
						?>
                            <th width="80">Quantity(pcs)</th>
                            <th width="80">Value </th>
                            <th width="80">Total CM </th>
                            <th width="80">Total Munit/SMV </th>
                            <th width="90">PO Breakdown Qty(pcs)</th>
						<?
						}
						?>
                        <th width="100">Quantity(pcs)</th>
                        <th width="100">Value </th>
                        <th width="100">Total CM </th>
                        <th width="100">Total Munit/SMV </th>
                        <th width="100">PO Breakdown Qty(pcs)</th>
					</tr>
				</thead>
				<tbody>
				<?	
				$k=1; $row_total_po_qnty = 0; $row_total_po_value = 0; $row_total_cm_value = 0; $row_total_smv_min = 0; $row_total_cut_qty_by_month = 0;
				foreach($comp_buyer_wise_arr as $company_key=> $comp_data)
				{
					foreach($comp_data as $buyer_key=> $row)
					{
                        $row_po_quantity=0;
                        $row_po_total_price=0;
                        $row_cm_value=0;
                        $row_smv_min=0;
                        $row_cut_qty_by_month=0;
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('trsum_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $k; ?>">
						<td width="20" align="center"><? echo $k;//echo $company_name; ?></td>
						<td width="100" align="center"><? echo $company_short_name_arr[$company_key];//echo $company_name; ?></td>
						<td width="100" align="center"><? echo $buyer_short_name_arr[$buyer_key];//echo $company_name; ?></td>
						<?
						foreach($month_wise_arr as $date_key=>$val)
						{
							$po_quantity=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_quantity'];
							$po_total_price=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_total_price'];
							$cm_value=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['cm_value'];
							$smv_min=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['smv_min'];
							$cut_qty_by_month=$cut_qty_by_month_arr[$company_key][$buyer_key][$date_key];
                            $row_po_quantity+=$po_quantity;
                            $row_po_total_price+=$po_total_price;
                            $row_cm_value+=$cm_value;
                            $row_smv_min+=$smv_min;
                            $row_cut_qty_by_month+=$cut_qty_by_month;
						?>
						<td width="80" align="right"><? echo number_format($po_quantity,0); $total_po_qnty_arr[$date_key]+=$po_quantity;?></td>
						<td width="80" align="right"><? echo number_format($po_total_price,2); $total_po_value_arr[$date_key]+=$po_total_price;?></td>
						<td width="80" align="right" title="Order Qty*CM value"><? echo number_format($cm_value,2); $total_cm_value_arr[$date_key]+=$cm_value; ?> </td>
						<td width="80" align="right" title="Order Qty*SMV"><? $total_smv_min_arr[$date_key]+=$smv_min; echo number_format($smv_min,2); ?></td>
						<td width="90" align="right"><? $total_cut_qty_by_month_arr[$date_key]+=$cut_qty_by_month; echo $cut_qty_by_month; ?></td>
						<?
						}
						?>
                        <td width="100" align="right"><? echo number_format($row_po_quantity,0); $row_total_po_qnty+=$row_po_quantity;?></td>
                        <td width="100" align="right"><? echo number_format($row_po_total_price,2); $row_total_po_value+=$row_po_total_price;?></td>
                        <td width="100" align="right" title="Order Qty*CM value"><? echo number_format($row_cm_value,2); $row_total_cm_value+=$row_cm_value; ?> </td>
                        <td width="100" align="right" title="Order Qty*SMV"><? $row_total_smv_min+=$row_smv_min; echo number_format($row_smv_min,2); ?></td>
                        <td width="100" align="right"><? $row_total_cut_qty_by_month+=$row_cut_qty_by_month; echo $row_cut_qty_by_month; ?></td>
                    </tr>
					
					<?
					$k++;
					}
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						
						<th align="center" colspan="3">Total:</th>
							<?
						foreach($month_wise_arr as $date_key=>$val)
						{
						?>
                            <th align="right"><? echo number_format($total_po_qnty_arr[$date_key],0); ?></th>
                            <th align="right"><? echo number_format($total_po_value_arr[$date_key],2); ?></th>
                            <th align="right"><? echo number_format($total_cm_value_arr[$date_key],2); ?></th>
                            <th align="right"><? echo number_format($total_smv_min_arr[$date_key],2); ?></th>
                            <th align="right"><?= number_format($total_cut_qty_by_month_arr[$date_key],0);?></th>
						<?
						}
						?>
                        <th align="right"><? echo number_format($row_total_po_qnty,0); ?></th>
                        <th align="right"><? echo number_format($row_total_po_value,2); ?></th>
                        <th align="right"><? echo number_format($row_total_cm_value,2); ?></th>
                        <th align="right"><? echo number_format($row_total_smv_min,2); ?></th>
                        <th align="right"><?= number_format($row_total_cut_qty_by_month,0);?></th>
						
					</tr>
					
				</tfoot>
			</table>
			
			<h3 style="width:4200px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
			<div id="content_report_panel">
			<? 
		
			if($search_by==1)
			{
				?>
				<table width="4490" id="table_header_1" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="70" >Company</th>
							<th width="70">Job No</th>
							<th width="60">Year</th>
							<th width="70">Approve Status</th>
							<th width="50">Buyer</th>
							<th width="50">Brand</th>
							<th width="110">PO No</th>
							<th width="100">Actual PO No</th>
							<th width="100">Ref No</th>
							<th width="100">Season</th>
							<th width="50">Agent</th>
							<th width="70">Order Status</th>
							<th width="70">Prod. Catg</th>
							<th width="40">Img</th>
							<th width="40">File</th>
							<th width="90">Style Ref</th>
							<th width="150">Item/SMV</th>
							<th width="200">Fab. Description</th>
							<th width="70">Ship Date</th>
							<th width="70">PO Rec. Date</th>
							<th width="50">Days in Hand</th>
							<th width="90">Order Qty(Pcs)</th>
							<th width="90">PO Breakdown Qty(pcs)</th>
							<th width="90">Order Qty</th>
							<th width="40">Uom</th>
							<th width="50">Per Unit Price</th>
							<th width="100">Order Value</th>
							<th width="100">Lien Bank</th>
							<th width="100">LC/SC No</th>
							<th width="90">Ex. LC Amendment No(Last)</th>

							<th width="80"> Int.File No </th>
							<th width="80">Pay Term </th>
							<th width="80">Tenor </th>

							<th width="90">Ex-Fac Qnty </th>
							<th width="90">Net Ex-Fac Val </th>
							<th width="70">Last Ex-Fac Date</th>

							<th width="100">Yarn Req</th>
							<th width="100">CM </th>
							<th width="100">CM(Pack)</th>
							<th width="100">CM(pcs)</th>
							<th width="100">SMV </th>
							
							<th width="80">SAH </th>
							<th width="80">S.P.M </th>
							
							
							<th width="100" >Shipping Status</th>
							<th width="150"> Team Member</th>
							<th width="150">Team Name</th>
							<th width="100">File No</th>
							<th width="40">Id</th>
							<th>Remarks</th>
							<th width="100">User Name</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:400px; overflow-y:scroll; width:4510px"  align="left" id="scroll_body">
				<table width="4490" border="1" class="rpt_table" rules="all" id="table_body">
					<?
					

					$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
					
					
					
						$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

						if($db_type==0)
						{
						//DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4
						//	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, b.id,b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
						}
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

							// (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4
							/* $data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/

						}
						$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
						$system_file_arr=array();
						foreach($data_file as $row)
						{
						$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
						}
						unset($data_file);
						$total_spm=$total_sah=0;
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
							$approved_id=$job_approved_arr[$row['job_no']];
							//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
							if($approved_id==1)
							{
								$msg_app="Approved";
								$color_app_td="#00FF66";//Blue
							}
							else if($approved_id==3)
							{
								$msg_app="Approved";
								$color_app_td="#FF0000";//Red
							}
							else
							{
								$msg_app="UnApproved"; //Red
								$color_app_td="#FF0000";//Red
							}
							
							//echo $file_type_name.'DDDDD,';
							$set_break_down=$row[('set_break_down')];
							$setSmvArr=array();
							foreach(explode('__',$set_break_down) as $setBrAr){
										list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
										$setSmvArr[]=$garments_item[$itemId].': '.$setSmv;
									}
									
							?>
							<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="50"   bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
								<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
								<td width="60"><p><? echo $row[('year')]; ?></p></td>
								<td width="70" bgcolor="<? echo $color_app_td;?>"><p><? echo $msg_app; ?></p></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
								<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
								<td width="40">
								
								<? 
								$file_type_name=$system_file_arr[$row[('job_no')]]['file'];
								if($file_type_name!="")
									{
								?>
								<input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[("job_no")] ?>','File View'),2"/>
								<?
								}
								else echo " ";
								?>
								</td>
								<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
								<td width="150"><div style="word-wrap:break-word; width:150px">
								<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
									$fabric_description="";
									for($j=0; $j<=count($gmts_item_id); $j++)
									{
										if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
										//echo $garments_item[$gmts_item_id[$j]];
									}
									echo implode(',',$setSmvArr);
									?></div></td>
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
									$order_qty_pcs=$row[('po_quantity')]*$row[('total_set_qnty')];
									$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									?></p></td>
								<td width="90" align="right"><?= $cut_qty_dtls_arr[$row[('job_no')]][$po_id];?></td>
								<td width="90" align="right"><p>
									<?
									echo number_format( $row[('po_quantity')],0);
									$order_qntytot=$order_qntytot+$row[('po_quantity')];
									$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
									
									$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
									$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
									
									
									?></p></td>
								<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
								<td width="50" title="<? echo $row[('unit_price')];?>" align="right"><p><? echo number_format($row[('unit_price')],6);?></p></td>
								<td width="100" align="right"><p>
									<?
										echo number_format($row[('po_total_price')],2);
										$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
										$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
									?></p></td>
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
								<td width="90" align="right"><? //ssssssss
								echo number_format($net_exfactory_val = $net_export_val_arr[$po_id],2);
								$net_tot_exfactory_val+=$net_exfactory_val;
								?></td>
									
									
								<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
								
								<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
									<?
										echo number_format($yarn_req_for_po,2);
										$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
										$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
									?></p>
								</td>
								<td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
								$cm=($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')];
								?></p></td>
								<td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs),2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
								<td width="100" align="right" title="<? echo $order_qty_pcs.'='.$costing_per_pcs.'='.$cm;?>"><p><? echo number_format($cm/$order_qty_pcs,2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
								<?
								if($row[('order_uom')]==58){									
								?>
								<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[('job_no')];?>', '<? echo $row[('id')]; ?>','500px')"><?echo number_format($row[('set_smv')],2);?></a></td>
								<?}else{?>
							<td width="100" align="right"><p><?echo number_format($row[('set_smv')],2);?></p></td>	<?	}?>
							<td width="80" align="right" title="Order Qty*SMV/60"><p><?  
							$tot_cm_val=($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')];
							$tot_sah=$row[('po_quantity')]*$row[('set_smv')]/60; echo number_format($tot_sah,2);?></p></td>
							<td width="80" align="right" title="Tot CM Value/(SAH*60)"><p><? $tot_spm=$tot_cm_val/($tot_sah*60); echo number_format($tot_spm,4);?></p></td>
									
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
								<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
								<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
								<td width="40"><p><? echo $po_id; ?></p></td>
								<td><p><? echo $row[('details_remarks')]; ?></p></td>
								<td width="100"><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
							</tr>
						<?
						$i++;$total_sah+=$tot_sah;$total_spm+=$tot_spm;
						}
						?>
						<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
							<td width="50" align="center" >  Total: </td>
							<td width="70" ></td>
							<td width="70"></td>
							<td width="60"></td>
							<td width="70"></td>
							<td width="50"></td>
							<td width="50"></td>
							<td width="110"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="50"></td>
							<td width="70"></td>
							<td width="70"></td>
							<td width="40"></td>
							<td width="40"></td>
							<td width="90"></td>
							<td width="150"></td>
							<td width="200"></td>
							<td width="70"></td>
							<td width="70"></td>
							<td width="50"></td>
							<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
							
							<td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
							<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
							<td width="40"></td>
							<td width="50"></td>

							<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
							<td width="100"></td>
					
							<td width="100"></td>
							<td width="90"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
							<td width="90" align="right"><?=number_format($net_tot_exfactory_val,2);?></td>
							<td width="70"></td>
						
							<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" ></td>
							
							<td width="80"  align="right"><? echo number_format($total_sah,2); ?></td>
							<td width="80"  align="right"><? echo number_format($total_spm,4); ?></td>
							
							<td width="100" ></td>
							<td width="150"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="40"></td>
							<td></th>
							<td width="100"></td>
						</tr>
					<?
					
					?>
					</table>
				</div>
					<table width="4490" id="report_table_footer" border="1" class="rpt_table" rules="all">
						<tfoot>
							<tr>
								<th width="50"></th>
								<th width="70" ></th>
								<th width="70"></th>
								<th width="60"></th>
								<td width="70"></td>
								<th width="50"></th>
								<th width="50"></th>
								<th width="110"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="50"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="40"></th>
								<th width="40"></th>
								<th width="90"></th>
								<th width="150"></th>
								<th width="200"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
								
								<th width="90"><?=$grand_total_cut_qty_dtls;?></th>
								
								<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
								<th width="40"></th>
								<th width="50"></th>

								<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
								<th width="100"></th>
							
								<th width="100"></th>
								<th width="90"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
								<th width="90" align="right" id="net_total_ex_factory_value"></th>
								<th width="70"></th>
								
								<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<td width="80"  align="right"><? echo number_format($total_sah,2); ?></td>
								<td width="80"  align="right"><? echo number_format($total_spm,4); ?></td>
							
								<th width="100" ></th>
								<th width="150"> </th>
								<th width="150"></th>
								<th width="100"></th>
								<th width="40"></th>
								<th></th>
								<th width="100"></th>
							</tr>
						</tfoot>
					</table>
					<?
			}
			else
			{
				?>
				<table width="4430" id="table_header_1" border="1" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="70" >Company</th>
							<th width="70">Job No</th>
							<th width="60">Year</th>
							<th width="70">Approve Status</th>
							<th width="50">Buyer</th>
							<th width="50">Brand</th>
							<th width="110">PO No</th>
							<th width="100">Actual PO No</th>
							<th width="100">Ref No</th>
							<th width="100">Season</th>
							<th width="50">Agent</th>
							<th width="70">Order Status</th>
							<th width="70">Prod. Catg</th>
							<th width="40">Img</th>
							<th width="40">File</th>
							<th width="90">Style Ref</th>
							<th width="150">Item/SMV</th>
							<th width="200">Fab. Description</th>
							<th width="70">Ship Date</th>
							<th width="70">PO Rec. Date</th>
							<th width="50">Days in Hand</th>
							<th width="90">Order Qty(Pcs)</th>
							<th width="90">PO Breakdown Qty(pcs)</th>
							<th width="90">Order Qty</th>
							<th width="40">Uom</th>
							<th width="50">Per Unit Price</th>
							<th width="100">Order Value</th>
							<th width="100">LC/SC No</th>
							<th width="90">Ex. LC Amendment No(Last)</th>

							<th width="80">Int. File No </th>
							<th width="80">Pay Term </th>
							<th width="80">Tenor</th>

							<th width="90">Ex-Fac Qnty </th>
							<th width="70">Last Ex-Fac Date</th>
				
							<th width="100">Yarn Req</th>
							<th width="100">CM </th>
							<th width="100">CM(Pack) </th>
							<th width="100">CM(Pcs) </th>
							<th width="100">SMV </th>
							<th width="80">SAH </th>
							<th width="80">S.P.M </th>
							
							<th width="100" >Shipping Status</th>
							<th width="150"> Team Member</th>
							<th width="150">Team Name</th>
							<th width="100">File No</th>
							<th width="120">Id</th>
							<th>Remarks</th>
							<th width="100">User Name</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:400px; overflow-y:scroll; width:4450px"  align="left" id="scroll_body">
				<table width="4430" border="1" class="rpt_table" rules="all" id="table_body">
					<?
					$yarn_cons_arr=return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0","job_no","yarn_cons_qnty");
					//$costing_per_arr=return_library_array("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0","job_no","costing_per");
					$sql_pre=sql_select("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0");
					foreach($sql_pre as $row)
					{
						$costing_per_arr[$row[csf("job_no")]]=$row[csf("costing_per")];
						$job_approved_arr[$row[csf("job_no")]]=$row[csf("approved")];
					}
					
					
					//$approved_id=$job_approved_arr[$row['job_no']];

					$ex_fact_sql=sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.job_no=b.job_no_mst and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $pocond $year_cond  $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond group by a.job_no");
					$ex_fact_data=array();
					foreach($ex_fact_sql as $row)
					{
						$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
						$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
					}

				/*	if($db_type==0)
					{
						$fab_dec_cond="group_concat(fabric_description)";
					}
					else if($db_type==2)
					{
						$fab_dec_cond="listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
					}
					$fabric_arr=array();
					$fab_sql=sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
					foreach ($fab_sql as $row)
					{
						$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
					}*/
					//var_dump($fabric_arr);die;

					$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;$grand_total_cut_qty_dtls=0;
					if($db_type==0)
					{
						$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

						$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
					}
					if($db_type==2)
					{
						$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

						$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
					}
					$data_array_group=sql_select("select b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond  group by b.grouping");
					foreach ($data_array_group as $row_group)
					{
						$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

						if($db_type==0)
						{
							$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no,a.set_smv, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, group_concat(b.id) as id, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(DATEDIFF(b.pub_shipment_date,'$date')) date_diff_1, max(DATEDIFF(b.shipment_date,'$date')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, group_concat(b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.inserted_by) as inserted_by
							from wo_po_details_master a, wo_po_break_down b
							where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond  $brand_cond
							group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
							order by a.style_ref_no");
						}
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
							$data_array=sql_select("select a.id as job_id,a.job_no_prefix_num, a.set_break_down,a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category,a.set_smv, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) as po_number, listagg(cast(b.is_confirmed as varchar2(4000)),',') within group (order by b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1,  max(b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by
							from wo_po_details_master a, wo_po_break_down b
							where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' $grouping  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond
							group by a.id,a.job_no_prefix_num, a.set_break_down,a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
							order by a.style_ref_no");
						}
					
						$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
						$system_file_arr=array();
						foreach($data_file as $row)
						{
						$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
						}
						unset($data_file);
						
						$total_cut_qty_dtls=0;$total_spm=$total_sah=0;
					
						foreach ($data_array as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$set_break_down=$row[csf('set_break_down')];
							//echo $set_break_down.'<br>';
							$setSmvArr=array();
							foreach(explode('__',$set_break_down) as $setBrAr){
										list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
										$setSmvArr[]=$garments_item[$itemId].': '.$setSmv;
										
									}
						
							$ex_factory_qnty=$ex_fact_data[$row[csf('job_no')]]["ex_factory_qnty"];
							$ex_factory_date=$ex_fact_data[$row[csf('job_no')]]["ex_factory_date"];
							$date_diff_3=datediff("d",$ex_factory_date, $row[csf('pub_shipment_date')]);
							$date_diff_4=datediff("d",$ex_factory_date, $row[csf('shipment_date')]);

							$cons=0;
							$costing_per_pcs=0;
							$data_array_yarn_cons=$yarn_cons_arr[$row[csf('job_no')]];
							$data_array_costing_per=$costing_per_arr[$row[csf('job_no')]];
							if($data_array_costing_per==1) $costing_per_pcs=1*12;
							else if($data_array_costing_per==2) $costing_per_pcs=1*1;
							else if($data_array_costing_per==3) $costing_per_pcs=2*12;
							else if($data_array_costing_per==4) $costing_per_pcs=3*12;
							else if($data_array_costing_per==5) $costing_per_pcs=4*12;

							$yarn_req_for_po=($data_array_yarn_cons/ $costing_per_pcs)*$row[csf('po_quantity')];



							//--Calculation Yarn Required-------
							//--Color Determination-------------
							//==================================
							$shiping_status_arr=explode(",",$row[csf('shiping_status')]);
							$shiping_status_arr=array_unique($shiping_status_arr);
							if(count($shiping_status_arr)>1) $shiping_status=2; else $shiping_status=$shiping_status_arr[0];


							$shipment_performance=0;
							if($shiping_status==1 && $row[csf('date_diff_1')]>10 )
							{
								$color="";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}

							if($shiping_status && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
							{
								$color="orange";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
							if($shiping_status==1 &&  $row[csf('date_diff_1')]<0)
							{
								$color="red";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
									//=====================================
							if($shiping_status==2 && $row[csf('date_diff_1')]>10 )
							{
								$color="";
							}
							if($shiping_status==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
							{
								$color="orange";
							}
							if($shiping_status==2 &&  $row[csf('date_diff_1')]<0)
							{
								$color="red";
							}
							if($shiping_status==2 &&  $row[csf('date_diff_2')]>=0)
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($shiping_status==2 &&  $row[csf('date_diff_2')]<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							//========================================
							if($shiping_status==3 && $date_diff_3>=0 )
							{
								$color="green";
							}
							if($shiping_status==3 &&  $date_diff_3<0)
							{
								$color="#2A9FFF";
							}
							if($shiping_status==3 && $date_diff_4>=0 )
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($shiping_status==3 &&  $date_diff_4<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							$actual_po="";
							$ex_po_id=explode(",",$row[csf('id')]);
							foreach($ex_po_id as $poId)
							{
								if($actual_po=="") $actual_po=$actual_po_no_arr[$row[csf('id')]]; else $actual_po.=','.$actual_po_no_arr[$row[csf('id')]];
							}
							$approved_id=$job_approved_arr[$row[csf('job_no')]];
							//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
							if($approved_id==1)
							{
								$msg_app="Approved";
								$color_app_td="#00FF66";//Blue
							}
							else if($approved_id==3)
							{
								$msg_app="Approved";
								$color_app_td="#FF0000";//Red
							}
							else
							{
								$msg_app="UnApproved"; //Red
								$color_app_td="#FF0000";//Red
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('company_name')]];?></div></td>
								<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
								<td width="70" bgcolor="<? echo $color_app_td; ?>" ><p><? echo $msg_app; ?></p></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[csf('brand_id')]];?></div></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo implode(",",array_unique(explode(",",$row[csf('po_number')])));?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo implode(",",array_unique(explode(",",$actual_po)));?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('season')];?></div></td>
								<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><a href="##" onClick="order_status('order_status_popup', '<? echo $row[csf('id')]; ?>','750px')">View</a></div></td>
								<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[csf('product_category')]];?></div></td>
								<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
									<td width="40"> 
								<? 
								$file_type_name=$system_file_arr[$row[csf("job_no")]]['file'];
								if($file_type_name!="")
									{
								?>
								<input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[csf("job_no")] ?>','File View'),2"/>
								<?
								}
								else echo " ";
								?></td>
								<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_ref_no')];?></div></td>
								<td width="150"><div style="word-wrap:break-word; width:150px">
								<? $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
									$fabric_description="";
									for($j=0; $j<=count($gmts_item_id); $j++)
									{
										if($fabric_description=="") $fabric_description=$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
										// echo $garments_item[$gmts_item_id[$j]];
									}
									echo  implode(', ',$setSmvArr);
									?></div></td>
								<td width="200"><div style="word-wrap:break-word; width:200px">
									<?
									$fabric_des="";
									$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
									echo $fabric_des;//$fabric_des;?></div></td>
								<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]);?>&nbsp;</div></td>
								<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('po_received_date')]!="" && $row[csf('po_received_date')]!="0000-00-00") echo change_date_format($row[csf('po_received_date')]);?>&nbsp;</div></td>
								<td width="50" bgcolor="<? echo $color; ?>"  align="center"><div style="word-wrap:break-word; width:50px">
									<?
									if($shiping_status==1 || $shiping_status==2)
									{
										echo $row[csf('date_diff_1')];
									}
									if($shiping_status==3)
									{
										echo $date_diff_3;
									}
									?></div></td>
								<td width="90" align="right"><p>
									<?
									echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
									$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
									$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
									
									?></p></td>
								
								<td width="90" align="right"><?=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];?></td>
								
								<td width="90" align="right"><p>
									<?
									echo number_format( $row[csf('po_quantity')],0);
									$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
									$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
									$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
									$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
									?></p></td>
								<td width="40"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
								<td width="50" align="right" title="<?=$row[csf('po_total_price')]/$row[csf('po_quantity')]; ?>"><p><? $unit_price=$row[csf('po_total_price')]/$row[csf('po_quantity')]; echo number_format($unit_price,6);?></p></td>
								<td width="100" align="right"><p>
									<?
										echo number_format($row[csf('po_total_price')],2);
										$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
										$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
									?></p></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
									<?
									if($lc_number_arr[$row[csf('id')]] !="")
									{
										echo "LC: ". $lc_number_arr[$row[csf('id')]];
									}
									if($sc_number_arr[$row[csf('id')]] !="")
									{
										echo " SC: ".$sc_number_arr[$row[csf('id')]];
									}
									?>
									</div></td>
									
								<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
								<? if($lc_number_arr[$row[csf('id')]] !="")
									{
										echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]];

									}
								?>
								</div></td>
								<td width="80" align="center"><p><? echo $export_lc_arr[$row[csf('id')]]['file_no'];?></p></td>
								<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['pay_term']; ?></p></td>
								<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>

								<td width="90" align="right"><p>
								<?

									?>
									<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
									<?
									//echo  number_format( $ex_factory_qnty,0);
									$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
									$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
									if ($shipment_performance==0)
									{
										$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
										$po_value['yet']+=100;
									}
									else if ($shipment_performance==1)
									{
										$po_qnty['ontime']+=$ex_factory_qnty;
										$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
										$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
									}
									else if ($shipment_performance==2)
									{
										$po_qnty['after']+=$ex_factory_qnty;
										$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
										$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
									}
									?></p></td>
								<td width="70" align="center"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? if($ex_factory_date!="" && $ex_factory_date!="0000-00-00") echo change_date_format($ex_factory_date); ?>&nbsp;</div></a></td>
								
								<td width="100" align="right" title="<? echo "Cons:".$data_array_yarn_cons."Costing per:".$data_array_costing_per;?>"><p>
									<?
										echo number_format($yarn_req_for_po,2);
										$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
										$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
									
									$tot_cm_val=($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')];
									?></p>
								</td>
								<td width="100" align="right"><p><? echo number_format($tot_cm_val,2);
								$cm=($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')];
								
								?></p></td>
								<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs),2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($cm/$row[csf('po_quantity')],2); ?></p></td>
						
								<?
									if($row[csf('order_uom')]==58){								
										?>
								<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','500px')"><?echo number_format($row[csf('set_smv')],2);?></a></td>
								<?}else{?>
									<td width="100" align="right"><p><? echo number_format($row[csf('set_smv')],2);?></p></td>	<?	}?>
									
									<td width="80" align="right" title="Order Qty*SMV/60"><p><?  $tot_sah=$row[csf('po_quantity')]*$row[csf('set_smv')]/60; echo number_format($tot_sah,2);?></p></td>
									<td width="80" align="right" title="Tot CM Value/(SAH*60)"><p><? 
									$tot_spm=$tot_cm_val/($tot_sah*60); echo number_format($tot_spm,4);?></p></td>
									
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$shiping_status]; ?></div></td>
								<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></div></td>
								<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')]; ?></div></td>
								<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('id')]))); ?></p></td>
								<td><p><? echo $row[csf('details_remarks')]; ?></p></td>
								<td width="100"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
							</tr>
						<?
						$i++; $total_sah+=$tot_sah;$total_spm+=$tot_spm;
						}
						?>
						<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
							<td width="50" align="center" >  Total (<?=$row_group[csf('grouping')]; ?>): </td>
							<td width="70" ></td>
							<td width="70"></td>
							<td width="60"></td>
							<td width="70"></td>
							<td width="50"></td>
							<td width="50"></td>
							<td width="110"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="50"></td>
							<td width="70"></td>
							<td width="70"></td>
							<td width="40"></td>
							<td width="40"></td>
							<td width="90"></td>
							<td width="150"></td>
							<td width="200"></td>
							<td width="70"></td>
							<td width="70"></td>
							<td width="50"></td>
							<td width="90" align="right" ><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
							<td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
							<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
							<td width="40"></td>
							<td width="50"></td>
							<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
							<td width="100"></td>
							<td width="90"></td>

							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>

							<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
							<td width="70"></td>
							
							<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
							<td width="100"></td>
							<td width="100" ></td>
							<td width="100" ></td>
							<td width="100" ></td>
							<td width="80"  align="right"><? echo number_format($total_sah,2); ?></td>
							<td width="80"  align="right"><? echo number_format($total_spm,4); ?></td>
							
							<td width="100" ></td>
							<td width="150"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td></th>
							<td width="100"></td>
						</tr>
					<?
					}
					?>
					</table>
					</div>
					<table width="4430" id="report_table_footer" border="1" class="rpt_table" rules="all">
						<tfoot>
							<tr>
								<th width="50"></th>
								<th width="70" ></th>
								<th width="70"></th>
								<th width="60"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="50"></th>
								<th width="110"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="50"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="40"></th>
								<th width="40"></th>
								<th width="90"></th>
								<th width="150"></th>
								<th width="200"></th>
								<th width="70"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
								<th width="90"><?= $grand_total_cut_qty_dtls;?></th>
								<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
								<th width="40"></th>
								<th width="50"></th>
								<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
								<th width="100"></th>
								<th width="90"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>

								<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
								<th width="70"></th>
							
								<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="80"  align="right"><? echo number_format($total_sah,2); ?></th>
							<th width="80"  align="right"><? echo number_format($total_spm,4); ?></th>
								
								
								<th width="100" ></th>
								<th width="150"> </th>
								<th width="150"></th>
								<th width="100"></th>
								<th width="120"></th>
								<th></th>
								<th width="100"></th>
							</tr>
						</tfoot>
					</table>
					<?
			}
			?>
			<?php /* ?>
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
							<td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>

							<tr bgcolor="#E9F3FF">
							<td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<? */  ?>
			</div>
			</div>
		</div>
		<?
	}
	else if($rpt_type==11)//Buyer Summary
	{
		$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$company_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');				
		if($company_name!=""){$company_cond=" and a.company_name in ($company_name)";}else{$company_cond="";}
		$sql_data="SELECT a.id as JOB_ID, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.GMTS_ITEM_ID, a.ORDER_UOM, b.id as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, b.PUB_SHIPMENT_DATE, b.UNIT_PRICE, b.PO_TOTAL_PRICE from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst $company_cond $order_confirm_status_con $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $shipment_status_cond $ref_cond $season_cond $brand_cond ";
		$data_array=sql_select( $sql_data);
		$all_data_array=array();
		foreach($data_array as $row)
		{
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["COMPANY_NAME"]=$row["COMPANY_NAME"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["GMTS_ITEM_ID"]=$row["GMTS_ITEM_ID"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["ORDER_UOM"]=$row["ORDER_UOM"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["PO_QUANTITY"]=$row["PO_QUANTITY"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["PUB_SHIPMENT_DATE"]=$row["PUB_SHIPMENT_DATE"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["UNIT_PRICE"]=$row["UNIT_PRICE"];
			$all_data_array[$row["COMPANY_NAME"]][$row["BUYER_NAME"]][$row["PO_ID"]]["PO_TOTAL_PRICE"]=$row["PO_TOTAL_PRICE"];
		}
		$tbl_width=950;
		ob_start();
		?>
		<style>.wrd_brk{word-wrap:break-word;}</style>
		<div align="center">
		<div id="content_report_panel">
		<table width="<?=$tbl_width+18;?>" rules="all" >
			<thead>
				<tr>
					<th colspan="10" align="center"><?=$company_name_arr[$company_name]?></th>
				</tr>
				<tr>
					<th colspan="10" align="center">Buyer Summary</th>
				</tr>
				<tr>
					<th colspan="10" align="center">From <?=$date_from;?> To <?=$date_to;?></th>
				</tr>
			</thead>
		</table>
		<table width="<?=$tbl_width+18;?>" id="table_header_1" border="1" class="rpt_table" rules="all" >
			<thead>
				<tr>
					<th colspan="10">Po Summary</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="100" >LC Company</th>
					<th width="120">Buyer</th>
					<th width="120">Merch Style</th>
					<th width="120">PO No</th>
					<th width="120">Product Name</th>
					<th width="80">Order Qty.(PCS)</th>
					<th width="80">Avg Unit Price</th>
					<th width="80">Oder Value</th>
					<th >Shipment Date</th>
				</tr>
			</thead>
		</table>

		<div style=" max-height:400px; overflow-y:scroll; width:<?=$tbl_width+18;?>px"  align="left" id="scroll_body">
			<table width="<?=$tbl_width;?>" border="1" class="rpt_table" rules="all" id="table_body">
				<?					
					foreach($all_data_array as $company_id=>$company_data_arr)
					{
						$tot_company_qnty=$tot_company_value=0;
						foreach($company_data_arr as $buyer_id=>$po_data_arr)
						{
							$i=1;$tot_buyer_qnty=$tot_buyer_value=0;
							foreach($po_data_arr as $row)
							{
								if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}														
								?>
								<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="40" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
									<td width="100" class="wrd_brk"><? echo $company_name_arr[$row["COMPANY_NAME"]];?></td>
									<td width="120" class="wrd_brk"><? echo $buyer_name_arr[$row['BUYER_NAME']];?></td>
									<td width="120" class="wrd_brk"><? echo $row['STYLE_REF_NO'];?></td>								
									<td width="120" class="wrd_brk"><? echo $row['PO_NUMBER'];?></td>								
									<td width="120" class="wrd_brk"><? echo $garments_item[$row['GMTS_ITEM_ID']];?></td>								
									<td width="80" align="right"><? echo number_format($row['PO_QUANTITY'],2);?></td>								
									<td width="80" align="right"><? echo number_format($row['UNIT_PRICE'],2);?></td>								
									<td width="80" align="right"><? echo number_format($row['PO_TOTAL_PRICE'],2);?></td>								
									<td align="center"><? echo change_date_format($row['PUB_SHIPMENT_DATE']);?></td>								
								</tr>
								<?
								$i++;
								$tot_buyer_qnty+=$row['PO_QUANTITY'];
								$tot_buyer_value+=$row['PO_TOTAL_PRICE'];
								$tot_company_qnty+=$row['PO_QUANTITY'];
								$tot_company_value+=$row['PO_TOTAL_PRICE'];
							}
							?>
							<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="20">
								<td colspan="6" align="right" ><b>Buyer Wise Total Qty & Value :</b> </td>
								<td align="right"><b><?=number_format($tot_buyer_qnty,2);?></b></td>
								<td ></td>
								<td align="right"><b><?=number_format($tot_buyer_value,2);?></b></td>
								<td> </td>
							</tr>
							<?
						}
						?>
						<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="20">
							<td colspan="6" align="right" ><b>Company Wise Grand Total Qty & Value :</b> </td>
							<td align="right"><b><?=number_format($tot_company_qnty,2);?></b></td>
							<td ></td>
							<td align="right"><b><?=number_format($tot_company_value,2);?></b></td>
							<td> </td>
						</tr>
						<?
					}
				?>
			</table>
			</div>
		</div>
		</div>
		</div>
		<?
	}
	else if($rpt_type==12)//Details 5 /copy Details2/md mamun 16-02-2023/crm-4063/16-02-2023
	{
		$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
		$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');

		// if($db_type==0)
		// {
		// 	if($year!=0) $search_cond .=" and YEAR(insert_date)=$year"; else $search_cond .="";
		// }
		// else if($db_type==2)
		// {
		// 	$year_field_con=" and to_char(insert_date,'YYYY')";
		// 	if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
		// }
		
		
		
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
		$condition= new condition();
			$condition->company_name("=$company_name");
		  if(str_replace("'","",$buyer_name)>0){
			  $condition->buyer_name("=$buyer_name");
		 }
		 //$txt_file=str_replace("'","",$txt_file);
		//$txt_ref
		 if($search_string!='' || $search_string!=0)
		 {
			$condition->po_number("in('$search_string')");
		 } 
		 if(str_replace("'","",$txt_ref)!='')
		 {
				$condition->grouping("='$txt_ref'"); 
		 }
		 if(str_replace("'","",$txt_file)!='')
		 {
			$condition->file_no("in('$txt_file')");
		 }
		 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
				  
				   if($category_by==1)
					{
						//if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==2)
					{
						//if ($start_date!="" && $end_date!="") $date_cond=" and b.po_received_date between '$start_date' and '$end_date'"; else $date_cond="";
						 $condition->po_received_date(" between '$start_date' and '$end_date'");
					}
					else if($category_by==3)
					{
					   if($db_type==0)
						{
						 $condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
						}
						else
						{
							$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
						}
					}
			 }
			 
		$condition->init();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order(); 
		
		//$commission= new commision($condition);
		//$commission_costing_sum_arr=$commission->getAmountArray_by_order();
		$margin_pcs_set_arr=return_library_array( "select job_no, margin_pcs_set from wo_pre_cost_dtls",'job_no','margin_pcs_set');
		
	  $sql_data="SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name,a.working_company_id, a.buyer_name,a.set_smv, a.agent_name, a.style_ref_no,a.style_description,a.brand_id, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id as po_id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where a.id=b.job_id and a.company_name in ($company_name) $order_confirm_status_con $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $shipment_status_cond $ref_cond $season_cond $brand_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no,a.style_description,a.brand_id, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom,a.set_smv,a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  $data_array=sql_select( $sql_data);
	  $all_po_id_arr=array();
	  foreach($data_array as $row) //
	  {
	  	$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
		$po_wise_arr[$row[csf('po_id')]]['working_company_id']=$row[csf('working_company_id')];
		$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_wise_arr[$row[csf('po_id')]]['brand_name']=$row[csf('brand_id')];
		$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
		$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
		
		$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
		$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
		$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
		$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
		$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
		$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
		$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
		$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
		$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
		$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
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
		$po_wise_arr[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_wise_arr[$row[csf('po_id')]]['style_description']=$row[csf('style_description')];
		$jobArr[$row[csf('job_no')]]="'".$row[csf('job_no')]."'";
		
		$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		//Company Buyer Wise
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
		$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		
		//Sumary
		$month_wise_arr[$pub_date_key]=$pub_date_key;
		$summ_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['smv_min']+=$row[csf('set_smv')]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['total_margin']+=$margin_pcs_set_arr[$row[csf('job_no')]]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		//echo $summ_cm_cost.'='.$row[csf('po_quantity')]*$row[csf('total_set_qnty')].',';
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['cm_value']+=$summ_cm_cost;
		$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
		
	  }
	 // asort($month_wise_arr);
		//  print_r($month_wise_arr);
		// $poIds=chop($all_po_id,','); 
		$poIds=implode(",", $all_po_id_arr); 
		$po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=$all_po_id_arr;
		// print_r($all_po_id_arr);die();
			if($db_type==2 && count($all_po_id_arr)>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$po_cond_for_in3=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or";
					$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
				$po_cond_for_in3=chop($po_cond_for_in3,'or ');
				$po_cond_for_in3.=")";
			}
			else
			{
				$po_cond_for_in=" and b.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
				$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
			}
 
		$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");

		/*echo "SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in ($company_name) $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id";*/
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			//echo $shiping_status_id.', ';
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			//Buyer Wise
			//	$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			if($shiping_status_id==3)//Full shipped
			{
				//echo $row[csf('ex_factory_qnty')].'dd';
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			else if($shiping_status_id==2)//Partial shipped
			{
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			//$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['return_qty']=$row[csf('ex_factory_return_qnty')];
		}
		
		if($db_type==0)
			{
				$fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
			}
			else if($db_type==2)
			{
				$fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			}
			//echo "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no $po_cond_for_in2 ";
		//	echo  "select c.job_no,c.cm_for_sipment_sche as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ";die;
		//	$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');
		//	print_r($cm_for_shipment_schedule_arr);
		
		$sql_pre="SELECT a.costing_per,a.approved, c.job_no,c.cm_cost as cm_for_sipment_sche,c.margin_pcs_set, d.company_name, d.buyer_name,b.pub_shipment_date,b.id as po_id,a.sew_effi_percent from  wo_pre_cost_mst a,wo_pre_cost_dtls c,wo_po_break_down b,wo_po_details_master d where a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and  c.job_no=b.job_no_mst  $po_cond_for_in2 ";
		 $data_budget_pre=sql_select($sql_pre);
			foreach ($data_budget_pre as $row)
			{
				
				$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
				$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']=$row[csf('margin_pcs_set')];
				$job_wise_margin_arr[$row[csf('job_no')]]['sew_effi_percent']=$row[csf('sew_effi_percent')];
				$po_wise_margin_arr[$row[csf('po_id')]]['margin_pcs_set']=$row[csf('margin_pcs_set')];
				
				$job_approved_arr[$row[csf('job_no')]]=$row[csf('approved')];
				$poIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
			
			}
			$poIds=implode(",",$poIdArr);
		  $sql_budget="SELECT a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		   $data_budget_array=sql_select($sql_budget);
		
			$fabric_arr=array();
			foreach ($data_budget_array as $row)
			{
				$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				if($row[csf('yarn_cons_qnty')]>0)
				{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			
				}
					//$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				//$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
			}
				//var_dump($fabric_arr);die;
				$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}

		foreach($actual_po_sql as $row)
		{
			$actual_po_no_arr[$row[csf('po_break_down_id')]]=$row[csf('acc_po_no')];
		}
		unset($actual_po_sql);
		//die;
		$sql_lc_result=sql_select("SELECT a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor,b.id,b.export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor,b.id,b.export_lc_no ");
		$lc_po_id="";
		foreach ($sql_lc_result as $row)
		{
			$lc_id_arr[$row['WO_PO_BREAK_DOWN_ID']] = $row['COM_EXPORT_LC_ID'];
			$lc_bank_arr[$row['WO_PO_BREAK_DOWN_ID']].= $row['LIEN_BANK'].',';
			$lc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['lc_number'].= $row['EXPORT_LC_NO'].',';
			$lc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['lc_id'].= $row['ID'].',';
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['file_no']= $row['INTERNAL_FILE_NO'];
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['pay_term']= $pay_term[$row['PAY_TERM']];
			$export_lc_arr[$row['WO_PO_BREAK_DOWN_ID']]['tenor']= $row['TENOR'];
			
				if($lc_po_id=="") $lc_po_id=$row['COM_EXPORT_LC_ID'];else $lc_po_id.=",".$row['COM_EXPORT_LC_ID'];
		}
		unset($sql_lc_result);
		$sql_sc_result=sql_select("SELECT a.wo_po_break_down_id,b.id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,b.id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
		foreach ($sql_sc_result as $row)
		{
			$sc_bank_arr[$row['WO_PO_BREAK_DOWN_ID']].= $row['LIEN_BANK'].',';
			$sc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['sc_number'].= $row['CONTRACT_NO'].',';
			$sc_number_arr[$row['WO_PO_BREAK_DOWN_ID']]['sc_id'].= $row['ID'].',';
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['file_no']= $row['INTERNAL_FILE_NO'];
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['pay_term']= $pay_term[$row['PAY_TERM']];
			$export_sc_arr[$row['WO_PO_BREAK_DOWN_ID']]['tenor']= $row['TENOR'];
		}
		unset($sql_sc_result);
						
		/* if($db_type==0)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		if($db_type==2)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		} */
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
			else
			{
				$lc_cond_for_in=" and export_lc_id in($lcIds)";
			}
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		
		
		
		$cut_qty_sql_res = sql_select("SELECT JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COUNTRY_SHIP_DATE,PLAN_CUT_QNTY  FROM WO_PO_COLOR_SIZE_BREAKDOWN where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."");
		$cut_qty_arr=array(); 
		foreach($cut_qty_sql_res as $rows)
		{
			
			$key=date("M-Y",strtotime($rows[COUNTRY_SHIP_DATE]));
			$comapny_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['company_name'];
			$buyer_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['buyer_name'];
			$cut_qty_by_month_arr[$comapny_id][$buyer_id][$key] += $rows[PLAN_CUT_QNTY];
			$cut_qty_dtls_arr[$rows[JOB_NO_MST]][$rows[PO_BREAK_DOWN_ID]] += $rows[PLAN_CUT_QNTY];
		}
		
		//var_dump($cut_qty_by_month_arr);die;
		
		
		$net_export_val_result=sql_select("select B.PO_BREAKDOWN_ID,((a.NET_INVO_VALUE/b.current_invoice_value)*b.current_invoice_qnty) AS PO_NET_INVO_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a ,COM_EXPORT_INVOICE_SHIP_dtls b where a.id=b.MST_ID ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAKDOWN_ID')."");	

		foreach($net_export_val_result as $row){
			$net_export_val_arr[$row[PO_BREAKDOWN_ID]]=$row[PO_NET_INVO_VALUE];
		}
		
		//print_r($net_export_val_arr);die;		
		
		//=====================Sewing==cutting==packing==trims rcv==fabric rcv=============================

		$cut_and_lay_data=sql_select("SELECT c.po_break_down_id po_id,a.job_no_mst ,sum(b.production_qnty) as  production_quantity ,sum(b.reject_qty) as reject_qnty 
		from pro_garments_production_mst c,pro_garments_production_dtls b,wo_po_break_down a  
		where c.id=b.mst_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1
		 and c.production_type=b.production_type and b.is_deleted=0 and c.production_type=1
		  and c.po_break_down_id=a.id ".where_con_using_array($jobArr,0,'a.job_no_mst')."			  
		  group by c.po_break_down_id,a.job_no_mst");

		//  echo "SELECT a.order_qty,b.job_no,c.order_id as po_id, c.country_id,b.job_year,b.company_id, sum(c.marker_qty) marker_qty
		//  from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_size c 
		//   where b.id=a.mst_id ".where_con_using_array($jobArr,0,'b.job_no')."  and b.id=c.mst_id and c.dtls_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 			 
		//   group by a.order_qty,b.job_no,b.job_year,b.company_id, c.country_id  ,c.order_id";
		

		foreach($cut_and_lay_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['cutting_qty']+=$row[csf('production_quantity')];
			$po_wise_data_arr[$row[csf('po_id')]]['cutting_qty']+=$row[csf('production_quantity')];
		}


		$sweing_data=sql_select("SELECT sum (b.production_qnty) as production_quantity,c.job_no_mst,c.id as po_id
			  FROM pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_break_down c
			 WHERE  a.id = b.mst_id ".where_con_using_array($jobArr,0,'c.job_no_mst')." and c.id = a.po_break_down_id and a.production_type = '5' and a.status_active = 1 and a.is_deleted = 0 and b.production_type = '5' and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and (   b.color_size_break_down_id != 0	or b.color_size_break_down_id is not null)
			  group by c.job_no_mst,c.id");

			 
			foreach($sweing_data as $row){
				$job_wise_data_arr[$row[csf('job_no_mst')]]['sweing_out_qty']+=$row[csf('production_quantity')];
				$po_wise_data_arr[$row[csf('po_id')]]['sweing_out_qty']+=$row[csf('production_quantity')];
			}


		$packing_data=sql_select("SELECT  b.job_no_mst , sum(a.production_quantity) as production_quantity,b.id AS po_id
			from pro_garments_production_mst a,wo_po_break_down b
			 where  b.id = a.po_break_down_id and a.production_type='8' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($jobArr,0,'b.job_no_mst')." 
			  group by b.job_no_mst,b.id");
			 

		foreach($packing_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['packing_qty']+=$row[csf('production_quantity')];
			$po_wise_data_arr[$row[csf('po_id')]]['packing_qty']+=$row[csf('production_quantity')];
		}

		$trims_rcv_data=sql_select("SELECT    max(a.receive_date) receive_date,d.job_no_mst,d.id as po_id 
		FROM inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c,wo_po_break_down d  
		WHERE c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active='1' 
		and b.is_deleted='0' ".where_con_using_array($jobArr,0,'d.job_no_mst')."  and c.po_breakdown_id=d.id
			 GROUP BY  d.job_no_mst,d.id ");

		 

		foreach($trims_rcv_data as $row){
			$job_wise_data_arr[$row[csf('job_no_mst')]]['last_accessories_date']=$row[csf('receive_date')];
			$po_wise_data_arr[$row[csf('po_id')]]['last_accessories_date']=$row[csf('receive_date')];
		}

	 $fabric_rcv_data=sql_select("SELECT max(a.receive_date) receive_date,d.job_no,d.po_break_down_id as po_id
	 from inv_receive_master a, inv_transaction c , wo_booking_dtls d where a.id=c.mst_id  ".where_con_using_array($jobArr,0,'d.job_no')."  and a.item_category=3 and a.entry_form in (37,17) and a.BOOKING_NO=d.BOOKING_NO and a.status_active=1  and c.status_active=1  and d.status_active=1 
     GROUP BY   d.job_no ,d.po_break_down_id");

			
		
		foreach($fabric_rcv_data as $row){
			$job_wise_data_arr[$row[csf('job_no')]]['last_fabric_date']=$row[csf('receive_date')];
			$po_wise_data_arr[$row[csf('po_id')]]['last_fabric_date']=$row[csf('receive_date')];
		}



		//-----------------------------------sample approve data--------------------------
		$sample_app_data=sql_select("select   a.job_no_mst,c.po_break_down_id,max(approval_status_date) as last_pp_sample_date 	from wo_po_sample_approval_info a ,lib_buyer_tag_sample b,wo_po_color_size_breakdown c ,lib_sample d where b.tag_sample=a.sample_type_id and c.id=a.color_number_id and
		  a.job_no_mst = c.job_no_mst and d.id=b.tag_sample and d.id=a.sample_type_id  and a.approval_status=3 and a.sample_type_id=99  and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1  and c.is_deleted=0 and c.status_active=1   and b.sequ!=0 and (a.entry_form_id is null or a.entry_form_id=0) and c.po_break_down_id in ($poIds) group by a.job_no_mst,c.po_break_down_id  ");

	 
		  foreach($sample_app_data as $val){
			$job_wise_data_arr[$val[csf('job_no_mst')]]['last_pp_sample_date']=$val[csf('last_pp_sample_date')];
			$po_wise_data_arr[$val[csf('po_break_down_id')]]['last_pp_sample_date']=$val[csf('last_pp_sample_date')];
		  }

		  //----------------------------order Update Entry----------------------------------
		  $orderUpdateData=sql_select("select  po_id,job_no,shipment_date,org_ship_date from wo_po_update_log where  po_id in ($poIds)");
		  
		  foreach($orderUpdateData as $row){
			$job_wise_data_arr[$row[csf('job_no')]]['org_shipment_date']=$row[csf('org_ship_date')];
			$po_wise_data_arr[$row[csf('po_id')]]['org_ship_date']=$row[csf('org_ship_date')];

		  }

		  $cpm_data=sql_select("select company_id,applying_period_date,applying_period_to_date,cost_per_minute from  lib_standard_cm_entry where  company_id in ($company_name) ");

				 

				foreach($cpm_data as $val){
					$str=$val[csf('applying_period_date')]."**".$val[csf('applying_period_to_date')]."**".$val[csf('cost_per_minute')];
					$cpm_data_arr[$val[csf('company_id')]][$str]=$str;
				}
		$tot_width=300+count($month_wise_arr)*400;		
		ob_start();
		?>
		<div align="center">
			<div align="center">
			<table width="<? echo $tot_width;?>" border="1" class="rpt_table" rules="all">
							<thead>
                            <tr>
                            <th colspan="3">&nbsp; </th>
                              <?
                                foreach($month_wise_arr as $date_key=>$val_data)
								{
								?>
								<th title="<? echo count($val_data);?>"  colspan="5"><? echo $date_key;?></th>
                                <?
								}
								?>
                            </tr>
                             <tr>
								<th width="20">SL</th>
                                <th width="100">Company Name</th>
								<th width="100">Buyer Name</th>
                                <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th width="80">Quantity(pcs)</th>
								<th width="80">Value </th>
								<th width="80">Total CM </th>
								<th width="80">Total Margin</th>
								<th width="80">Total Munit/SMV </th>
								<th width="80">PO Breakdown Qty(pcs)</th>
                                <?
								}
								?>
							</tr>
							</thead>
                           <tbody>
						<?
					
						 $k=1;
						foreach($comp_buyer_wise_arr as $company_key=> $comp_data)
						{
							foreach($comp_data as $buyer_key=> $row)
							{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('trsum_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $k; ?>">
								<td width="20" align="center"><? echo $k;//echo $company_name; ?></td>
                                <td width="100" align="center"><? echo $company_short_name_arr[$company_key];//echo $company_name; ?></td>
								<td width="100" align="center"><? echo $buyer_short_name_arr[$buyer_key];//echo $company_name; ?></td>
                                <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
									$po_quantity=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_quantity'];
									$po_total_price=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_total_price'];
									$cm_value=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['cm_value'];
									$total_margin=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['total_margin'];
									$smv_min=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['smv_min'];
									$cut_qty_by_month=$cut_qty_by_month_arr[$company_key][$buyer_key][$date_key];
								?>
								<td width="80" align="right"><? echo number_format($po_quantity,0); $total_po_qnty_arr[$date_key]+=$po_quantity;?></td>
								<td width="80" align="right"><? echo number_format($po_total_price,2); $total_po_value_arr[$date_key]+=$po_total_price;?></td>
								<td width="80" align="right" title="Order Qty*CM value"><? echo number_format($cm_value,2); $total_cm_value_arr[$date_key]+=$cm_value; ?> </td>
								<td width="80" align="right"  ><? echo number_format($total_margin,2); $total_margin_arr[$date_key]+=$total_margin; ?> </td>
								<td width="80" align="right" title="Order Qty*SMV"><? $total_smv_min_arr[$date_key]+=$smv_min; echo number_format($smv_min,2); ?></td>
                                <td width="90" align="right"><? $total_cut_qty_by_month_arr[$date_key]+=$cut_qty_by_month; echo $cut_qty_by_month; ?></td>
                                <?
								}
								?>
							</tr>
							
							<?
							$k++;
							}
						}
						?>
                        </tbody>
						<tfoot>
							<tr>
								
								<th align="center" colspan="3">Total:</th>
                                 <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th align="right"><? echo number_format($total_po_qnty_arr[$date_key],0); ?></th>
								<th align="right"><? echo number_format($total_po_value_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_cm_value_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_margin_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_smv_min_arr[$date_key],2); ?></th>
								<th align="right"><?= number_format($total_cut_qty_by_month_arr[$date_key],0);?></th>
                                <?
								}
								?>
								
							</tr>
							
						</tfoot>
			</table>
		<h3 style="width:6650px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
		<div id="content_report_panel">
        <? 
		
		if($search_by==1)
		{
			?>
			<table width="6630" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="100" >Working Company</th>
						<th width="70">Order Status</th>
					
                        <th width="70">Approve Status</th>
						<th width="60">Year</th>
						<th width="70">Job No</th>
						<th width="50">Buyer</th>
						<th width="90">Style Ref</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
						<th width="70">PO Rec. Date</th>
						<th width="70">Original Ship Date</th>
						<th width="70">Revised Ship Date</th>
						<th width="70">Lead Time</th>
						<th width="50">Days in Hand</th>

						<th width="100">Last Sewing Trims In-House Date</th>
						<th width="100">Last Finished Trims In-House Date</th>
						<th width="100">Last Fabric In-House Date</th>		
						<th width="100">PP Sample Approval Date</th>				
						<th width="100">Yarn Req</th>

						<th width="100">Season</th>
						<th width="100">Style Des</th>
						<th width="150">Item</th>
					
						<th width="50">Brand</th>
					
                        <th width="100">Ref No</th>
						
						<th width="50">Agent</th>
			
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
                        <th width="40">File</th>
						<!-- <th width="90">Style Ref</th> -->
					
						<th width="200">Fab. Description</th>
						
						<th width="90">Order Qty(Pcs)</th>
						<th width="90">PO Breakdown Qty(pcs)</th>
						<th width="90">Order Qty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
                      

						<th width="100">CM(Pcs)</th>
						<th width="100">Total CM </th>
						<th width="100">Margin(Pcs) </th>
			
						<th width="100">Total Margin </th>
						<th width="100">CM Per Miniute </th>
						


						<th width="100">CM(Contribution Margin)Pcs</th>
						<th width="100">CM(Contribution Margin)</th>
						<th width="100">SMV </th>
						<th width="80">Total Miniute</th>
						<th width="80">Sewing Effi%-Pre</th>
						<th width="80">Use Minute</th>
						<th width="80">CPM</th>
						<th width="80">EPM</th>
						<th width="80">Profit Loss-Pcs</th>
						<th width="80">Total Profit Loss</th>


						<th width="100">Cutting Qty </th>
						<th width="100">Sewing Qty</th>					
						<th width="100">Finishing </th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="90">Net Ex-Fac Val </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>

						<th width="100" >Shipping Status</th>

						<th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="100">LC/SC File</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>

						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="40">Id</th>
						<th width="100">User Name</th>
						<th>Remarks</th>
						
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:6630px"  align="left" id="scroll_body">
			<table width="6610" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
				
				
				
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
					//DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4
					//	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, b.id,b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					}
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

						// (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4
						/* $data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/

					}
					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
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
						$approved_id=$job_approved_arr[$row['job_no']];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}
						
						foreach($cpm_data_arr[$row[('company_name')]] as $key =>$val){

							list($applying_date,$applying_date_to,$cpm)=explode("**",$val);

								if( strtotime($applying_date ) <= strtotime($row[('po_received_date')]) && strtotime($row[('po_received_date')]) <= strtotime($applying_date )){
									$cost_per_val=$cpm;
								}

						}
						//echo $file_type_name.'DDDDD,';
						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50"   bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('working_company_id')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
						
                            <td width="70" bgcolor="<? echo $color_app_td;?>"><p><? echo $msg_app; ?></p></td>
							<td width="60"><p><? echo $row[('year')]; ?></p></td>
							<td width="70"><p><? echo $row[('job_no')]; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px">
							<? 
							echo $row[('po_number')];
							?>
							<hr style="border:1px solid #DDD;">
							<?
							 
							 echo "<a href='#report_details' style='color:#990000' onclick= \"report_generate_popup('".$row['job_no']."','".$po_id."','206','1');\"><p>Matarial Report</p></a>";
							
							?>
								<hr style="border:1px solid #DDD ;">
							<?
								
								echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$po_id."','','1');\"><p>TNA</p></a>";
							?>
							
							
							</div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $actual_po_no_arr[$po_id]; ?></div></td>
						
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>	
							<td width="70" title="<?=$po_id;?>"><div style="word-wrap:break-word; width:70px"><?
							if(strtotime($po_wise_data_arr[$po_id]['org_ship_date'])>0){
								$orginal_ship_date=$po_wise_data_arr[$po_id]['org_ship_date'];
								$revise_ship_date=$row[('shipment_date')];
							}else{
								$orginal_ship_date=$row[('shipment_date')];
								$revise_ship_date="";
							}
							echo '&nbsp;'.change_date_format($orginal_ship_date,'dd-mm-yyyy','-');?></div></td>	<td width="70"><div style="word-wrap:break-word; width:70px"><? echo '&nbsp;'.change_date_format($revise_ship_date,'dd-mm-yyyy','-');?></div></td>
							<?
							$po_lead_time_diff = abs(strtotime($row[('pub_shipment_date')]) - strtotime($row[('po_received_date')]));								
							$po_lead_time = floor($po_lead_time_diff / (60*60*24));
							?>
							<td width="70"><div style="word-wrap:break-word; width:70px; align:center;"><? echo $po_lead_time;?></div></td>
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
							<td width="100" align="center"><p><? 	echo $po_wise_data_arr[$po_id]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><? 	echo $po_wise_data_arr[$po_id]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['last_fabric_date']; ?></p></td>
							<td width="100" align="center"><p><? 	echo $po_wise_data_arr[$po_id]['last_pp_sample_date']; ?></p></td>
							
							<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $row[('style_description')];?></div></td>

							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
							
						
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[('brand_name')]];?></div></td>
						
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('grouping')];?></div></td>
						
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('agent_name')]];?></div></td>
							
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
                            <td width="40">
                            
                             <? 
							 $file_type_name=$system_file_arr[$row[('job_no')]]['file'];
							 if($file_type_name!="")
							    {
							 ?>
                             <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[("job_no")] ?>','File View'),2"/>
                             <?
							   }
							  else echo " ";
							 ?>
                            </td>
							
							
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
								?></p></td>
                            <td width="90" align="right"><?= $cut_qty_dtls_arr[$row[('job_no')]][$po_id];?></td>
							<td width="90" align="right"><p>
								<?
								echo fn_number_format( $row[('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
								
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
								
								
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? echo fn_number_format($row[('unit_price')],2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo fn_number_format($row[('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
								?></p></td>
                          

						  <td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo fn_number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs),2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>"><p><? echo fn_number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$po_wise_margin_arr[$po_id]['margin_pcs_set'];?></div></td>


							

							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$po_wise_margin_arr[$po_id]['margin_pcs_set']*($row['po_quantity']*$row['total_set_qnty']); ?></div></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=fn_number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)/$row['set_smv'],2); ?></div></td>
							<?
								$contribution_margin_pcs=($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)+$po_wise_margin_arr[$po_id]['margin_pcs_set'];

								$contribution_margin=($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')]+$po_wise_margin_arr[$po_id]['margin_pcs_set']*($row['po_quantity']*$row['total_set_qnty']);
								$total_miniute=$row[('set_smv')]*$row[('po_quantity')];

								$sew_effi_percent=$job_wise_margin_arr[$row['job_no']]['sew_effi_percent'];
								$use_minute=(($row[('set_smv')]*$row[('po_quantity')])/$sew_effi_percent)*100;
								$epm=$contribution_margin/$use_minute;
								$cpm=($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)/$row[('set_smv')];;
								$profit_loss_pcs=$epm-$cost_per_val;
								$tot_profit_loss_pcs=$profit_loss_pcs*$row[('po_quantity')];
								?>
							
							<td width="100" align="right"><p><?echo fn_number_format($contribution_margin_pcs,2);?></p></td>
							<td width="100" align="right"><p><?echo fn_number_format($contribution_margin,2);?></p></td>
							<?
								
								if($row[('order_uom')]==58){									
									?>
							<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[('job_no')];?>', '<? echo $row[('id')]; ?>','500px')"><?echo number_format($row[('set_smv')],2);?></a></td>
							<?}else{?>
								<td width="100" align="right"><p><?echo fn_number_format($row[('set_smv')],2);?></p></td>	<?	}
								
							
								?>
							<td width="80" align="right"><p><?echo fn_number_format($total_miniute,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($sew_effi_percent,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($use_minute,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($cpm,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($epm,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($profit_loss_pcs,2);?></p></td>
							<td width="80" align="right"><p><?echo fn_number_format($tot_profit_loss_pcs,2);?></p></td>

								
						    <td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['cutting_qty'];$total_cutting_qty+=$po_wise_data_arr[$po_id]['cutting_qty'];  ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['sweing_out_qty'];$total_sweing_out_qty+=$po_wise_data_arr[$po_id]['sweing_out_qty'];  ?></p></td>
							<td width="100" align="center"><p><?	echo $po_wise_data_arr[$po_id]['packing_qty']; $total_packing_qty+=$po_wise_data_arr[$po_id]['packing_qty']; ?></p></td>

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
                            <td width="90" align="right"><? //ssssssss
							echo number_format($net_exfactory_val = $net_export_val_arr[$po_id],2);
							$net_tot_exfactory_val+=$net_exfactory_val;
							?></td>
                                
                                
							<td width="70"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_access_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								<?
									$short_access_value=$short_access_qnty*$row[('unit_price')];
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>
						
						
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
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
								$lc_sc_no=$lc_sc_id=$lc_sc_type="";
								if($lc_number_arr[$po_id]["lc_number"] !="")
								{
									$lc_sc_no.="LC: ". $lc_number_arr[$po_id]["lc_number"];
									$lc_sc_id.=$lc_number_arr[$po_id]["lc_id"];
									$lc_sc_type.='1,';
								}
								if($sc_number_arr[$po_id]["sc_number"] !="")
								{
									$lc_sc_no.=" SC: ".$sc_number_arr[$po_id]["sc_number"];
									$lc_sc_id.=$sc_number_arr[$po_id]["sc_id"];
									$lc_sc_type.='2,';
								}
								echo implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
								$lc_sc_id=implode(",",array_unique(explode(",",chop($lc_sc_id,','))));
								$lc_sc_type=implode(",",array_unique(explode(",",chop($lc_sc_type,','))));
								?>
								</div>
							</td>
							<td width="100" align="center">								
								<?
									if($lc_sc_id!="")
									{
										?>
											<input type="button" class="image_uploader" id="system_id" style="width:40px" value="File" onClick="openmypage_file('requires/shipment_schedule_controller.php?action=file_show&mst_id=<?=$lc_sc_id;?>&lc_sc_type=<?=$lc_sc_type;?>','File View'),2"/>
										<?
									}
								?></td>
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

							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[('file_no')]; ?></div></td>
							<td width="40"><p><? echo $po_id; ?></p></td>
							
							<td width="100"><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
							<td><p><? echo $row[('details_remarks')]; ?></p></td>
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="50" align="center" >  Total: </td>
						<td width="70" ></td>
						<td width="100"></td>
						<td width="70"></td>
				
                        <td width="70"></td>
						<td width="60"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90"></td>
						<td width="110"></td>
                        <td width="100"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="150"></td>
						<td width="50"></td>
					
                        <td width="100"></td>
				
						<td width="50"></td>
						
						<td width="70"></td>
						<td width="40"></td>
                        <td width="40"></td>
					
						<td width="200"></td>
					
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
						
						<td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
                        <td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>

						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
                     
					
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
					
						<td width="100"></td>
						<td width="100"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>

						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_cutting_qty,0); ?></td>
						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_sweing_out_qty,0); ?></td>
						<td width="100"  align="right"><span style="color:#CCCCCC;">'<? echo number_format($total_packing_qty,0); ?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="90" align="right"><?=number_format($net_tot_exfactory_val,2);?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" ></td>
						<td width="100"></td>
                        <td width="100"></td>
				
						<td width="100"></td>
						<td width="90"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="40"></td>						
						<td width="100"></td>
						<td></td>
					 </tr>
				<?
				
				?>
				</table>
				</div>
				<table width="6630" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="70" ></th>
							<th width="100"></th>
							<th width="70"></th>
						
		
                            <th width="70"></th>
							<th width="60"></th>
						
							<th width="70"></th>
							<th width="50"></th>
							<th width="90"></th>
							<th width="110"></th>
                            <th width="100"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="150"></th>
						
							<th width="50"></th>
						
                           
							<th width="100"></th>
							<th width="50"></th>
						
							<th width="70"></th>
							<th width="40"></th>
                            <th width="40"></th>
						
							<th width="200"></th>
						
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            
							<th width="90"><?=$grand_total_cut_qty_dtls;?></th>
                            
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>

							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
                          
			
							
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" ></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							
							<th width="100"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>

							<th width="100" align="right"><? echo number_format($total_cutting_qty,0); ?></th>
							<th width="100" align="right"><? echo number_format($total_sweing_out_qty,0); ?></th>
							<th width="100" align="right"><? echo number_format($total_packing_qty,0); ?></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="90" align="right" id="net_total_ex_factory_value"></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
							<th width="100"></th>
                            <th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"> </th>
							
							<th width="150"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="40"></th>							
						
							<th width="100"></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
				<?
		}
		else
		{
			?>
			<table width="6470" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="80">SL</th>
						<th width="70" >Company</th>
						<th width="100" >Working Company</th>
						<th width="70">Order Status</th>
						<th width="70">Approve Status</th>
						<th width="60">Year</th>
						<th width="70">Job No</th>
						<th width="50">Buyer</th>
						<th width="90">Style Ref</th>
						<th width="110">PO No</th>
                        <th width="100">Actual PO No</th>
						<th width="70">PO Rec. Date</th>
						<th width="70">Original Ship Date</th>
						<th width="70">Revised Ship Date</th>
						<th width="70">Lead Time</th>
						<th width="50">Days in Hand</th>
						<th width="100">Last Sewing Trims In-House Date</th>
						<th width="100">Last Finished Trims In-House Date</th>
						<th width="100">Last Fabric In-House Date</th>
						<th width="100">PP Sample Approved Date</th>
						<th width="100">Yarn Req</th>
						<th width="100">Season</th>
						<th width="100">Style Des</th>
						<th width="150">Item</th>
                      
				
						<th width="50">Brand</th>
						
                        <th width="100">Ref No</th>
					
						<th width="50">Agent</th>
			
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
                        <th width="40">File</th>
						
					
						<th width="200">Fab. Description</th>
						
						
						<th width="90">Order Qty(Pcs)</th>
						<th width="90">PO Breakdown Qty(pcs)</th>
						<th width="90">Order Qty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
						

		

						<th width="100">CM(Pcs) </th>
						<th width="100">Total CM </th>
						<th width="100">Margin(Pcs) </th>
					
						<th width="100">Total Margin </th>
						<th width="100">CM per Miniute </th>
					


						<th width="100">CM(</br>Contribution</br> Margin) Pcs </th>
						<th width="100">CM(</br>Contribution</br> Margin) </th>
						<th width="100">SMV </th>
						<th width="60">Total </br>Miniute </th>
						<th width="60">Sewing </br>Effi%-Pre </th>
						<th width="60">Use Minute </th>
						<th width="60">CPM </th>
						<th width="60">EPM </th>
						<th width="60">Profit Loss-PCs </th>
						<th width="60">Total Profit Loss </th>


						<th width="100">Cutting Qty </th>
						<th width="100">Sewing Qty</th>					
						<th width="100">Finishing </th>

						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>


						<th width="100" >Shipping Status</th>
						<th width="100">LC/SC No</th>
						<th width="100">LC/SC File</th>
						<th width="90">Ex. LC Amendment No(Last)</th>

						<th width="80">Int. File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="100">File No</th>
						<th width="120">Id</th>
						<th width="100">User Name</th>
						<th>Remarks</th>
						
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:6790px"  align="left" id="scroll_body">
			<table width="6470" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				$yarn_cons_arr=return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0","job_no","yarn_cons_qnty");
				//$costing_per_arr=return_library_array("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0","job_no","costing_per");
				$sql_pre=sql_select("select job_no, costing_per,approved from  wo_pre_cost_mst where status_active=1 and is_deleted=0");
				foreach($sql_pre as $row)
				{
					$costing_per_arr[$row[csf("job_no")]]=$row[csf("costing_per")];
					$job_approved_arr[$row[csf("job_no")]]=$row[csf("approved")];
				}
				
				
				//$approved_id=$job_approved_arr[$row['job_no']];

				$ex_fact_sql=sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.job_no=b.job_no_mst and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $pocond $year_cond  $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond group by a.job_no");
				$ex_fact_data=array();
				foreach($ex_fact_sql as $row)
				{
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
					$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
				}

				if($db_type==0)
				{
					$fab_dec_cond="group_concat(fabric_description)";
				}
				else if($db_type==2)
				{
					$fab_dec_cond="listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
				}
				$fabric_arr=array();
				$fab_sql=sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
				foreach ($fab_sql as $row)
				{
					$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				}
				//var_dump($fabric_arr);die;

				$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;$grand_total_cut_qty_dtls=0;
				/* if($db_type==0)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				}
				if($db_type==2)
				{
					$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');

					$sc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ",'wo_po_break_down_id','contract_no');
				} */
				$lc_number_arr=$sc_number_arr=array();
				$lc_data=sql_select( "SELECT a.wo_po_break_down_id, b.id, b.export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.is_deleted=0");
				foreach($lc_data as $row)
				{
					$lc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["lc_number"].=$row["EXPORT_LC_NO"].",";
					$lc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["lc_id"].=$row["ID"].",";
				}
				unset($lc_data);

				$sc_data=sql_select( "SELECT a.wo_po_break_down_id, b.id, b.contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 ");
				foreach($sc_data as $row)
				{
					$sc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["sc_number"].=$row["CONTRACT_NO"].",";
					$sc_number_arr[$row["WO_PO_BREAK_DOWN_ID"]]["sc_id"].=$row["ID"].",";
				}
				unset($sc_data);

				


				$data_array_group=sql_select("SELECT b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where a.id=b.job_id and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond  group by b.grouping");



				foreach ($data_array_group as $row_group)
				{
					$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

					if($db_type==0)
					{
						$data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description,a.set_smv, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, group_concat(b.id) as id, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(DATEDIFF(b.pub_shipment_date,'$date')) date_diff_1, max(DATEDIFF(b.shipment_date,'$date')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, group_concat(b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.job_no=b.job_no_mst and a.company_name in ($company_name)  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond  $brand_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
						order by a.style_ref_no");
					}
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
						$data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category,a.set_smv, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id, a.season_buyer_wise as season, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) as po_number, listagg(cast(b.is_confirmed as varchar2(4000)),',') within group (order by b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1,  max(b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by
						from wo_po_details_master a, wo_po_break_down b
						where  a.job_no=b.job_no_mst and a.company_name in ($company_name) $buyer_id_cond and a.team_leader like '$team_leader' $grouping  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond $brand_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond
						group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name,a.working_company_id, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.brand_id,a.set_smv, a.season_buyer_wise
						order by a.style_ref_no");
					}
				
					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
					$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);
					
				

							
					$total_cut_qty_dtls=0;
				
					foreach ($data_array as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					
						$ex_factory_qnty=$ex_fact_data[$row[csf('job_no')]]["ex_factory_qnty"];
						$ex_factory_date=$ex_fact_data[$row[csf('job_no')]]["ex_factory_date"];
						$date_diff_3=datediff("d",$ex_factory_date, $row[csf('pub_shipment_date')]);
						$date_diff_4=datediff("d",$ex_factory_date, $row[csf('shipment_date')]);

						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=$yarn_cons_arr[$row[csf('job_no')]];
						$data_array_costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($data_array_costing_per==1) $costing_per_pcs=1*12;
						else if($data_array_costing_per==2) $costing_per_pcs=1*1;
						else if($data_array_costing_per==3) $costing_per_pcs=2*12;
						else if($data_array_costing_per==4) $costing_per_pcs=3*12;
						else if($data_array_costing_per==5) $costing_per_pcs=4*12;

						$yarn_req_for_po=($data_array_yarn_cons/ $costing_per_pcs)*$row[csf('po_quantity')];



						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shiping_status_arr=explode(",",$row[csf('shiping_status')]);
						$shiping_status_arr=array_unique($shiping_status_arr);
						if(count($shiping_status_arr)>1) $shiping_status=2; else $shiping_status=$shiping_status_arr[0];


						$shipment_performance=0;
						if($shiping_status==1 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}

						if($shiping_status && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($shiping_status==1 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
								//=====================================
						if($shiping_status==2 && $row[csf('date_diff_1')]>10 )
						{
							$color="";
						}
						if($shiping_status==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==2 &&  $row[csf('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						//========================================
						if($shiping_status==3 && $date_diff_3>=0 )
						{
							$color="green";
						}
						if($shiping_status==3 &&  $date_diff_3<0)
						{
							$color="#2A9FFF";
						}
						if($shiping_status==3 && $date_diff_4>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($shiping_status==3 &&  $date_diff_4<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;
						}
						$actual_po="";
						$ex_po_id=explode(",",$row[csf('id')]);
						foreach($ex_po_id as $poId)
						{
							if($actual_po=="") $actual_po=$actual_po_no_arr[$row[csf('id')]]; else $actual_po.=','.$actual_po_no_arr[$row[csf('id')]];
						}
						$approved_id=$job_approved_arr[$row[csf('job_no')]];
						//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
						if($approved_id==1)
						{
							$msg_app="Approved";
							$color_app_td="#00FF66";//Blue
						}
						else if($approved_id==3)
						{
							$msg_app="Approved";
							$color_app_td="#FF0000";//Red
						}
						else
						{
							$msg_app="UnApproved"; //Red
							$color_app_td="#FF0000";//Red
						}
					
						foreach($cpm_data_arr[$row[csf('company_name')]] as $key =>$val){

								list($applying_date,$applying_date_to,$cpm)=explode("**",$val);

									if( strtotime($applying_date ) <= strtotime($row[csf('po_received_date')]) && strtotime($row[csf('po_received_date')]) <= strtotime($applying_date )){
										$cost_per_val=$cpm;
									}

						}

						?>
						<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="80" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('company_name')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[csf('working_company_id')]];?></div></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><a href="##" onClick="order_status('order_status_popup', '<? echo $row[csf('id')]; ?>','750px')">View</a></div></td>
							<td width="70" bgcolor="<? echo $color_app_td; ?>" ><p><? echo $msg_app; ?></p></td>
							<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
							<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></div></td>
							<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_ref_no')];?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px"><? echo implode(",",array_unique(explode(",",$row[csf('po_number')])));?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo implode(",",array_unique(explode(",",$actual_po)));?></div></td>
						
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('po_received_date')]!="" && $row[csf('po_received_date')]!="0000-00-00") echo change_date_format($row[csf('po_received_date')]);?>&nbsp;</div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><?echo change_date_format($job_wise_data_arr[$row[csf('job_no')]]['org_shipment_date']);?>&nbsp;</div></td>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]);?>&nbsp;</div></td>
							<?
							$po_lead_time_diff = abs(strtotime($row[csf('pub_shipment_date')]) - strtotime($row[csf('po_received_date')]));								
							$po_lead_time = floor($po_lead_time_diff / (60*60*24));
							?>
							<td width="70"  align="center"><div style="word-wrap:break-word; width:70px"><?  echo $po_lead_time;?>&nbsp;</div></td>
							<td width="50" bgcolor="<? echo $color; ?>"  align="center"><div style="word-wrap:break-word; width:50px">
								<?
								if($shiping_status==1 || $shiping_status==2)
								{
									echo $row[csf('date_diff_1')];
								}
								if($shiping_status==3)
								{
									echo $date_diff_3;
								}
								?></div></td>

							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_accessories_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_fabric_date']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['last_pp_sample_date']; ?></p></td>
							<td width="100" align="right" title="<? echo "Cons:".$data_array_yarn_cons."Costing per:".$data_array_costing_per;?>"><p>
								<?
									echo number_format($yarn_req_for_po,2);
									$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
									$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?></p>
							</td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('season')];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('style_description')];?></div></td>
							<td width="150"><div style="word-wrap:break-word; width:150px">
							<? $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
								$fabric_description="";
								for($j=0; $j<=count($gmts_item_id); $j++)
								{
									if($fabric_description=="") $fabric_description=$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
									echo $garments_item[$gmts_item_id[$j]];
								}
								?></div></td>
                      
						
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $brand_arr[$row[csf('brand_id')]];?></div></td>
						
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')]; ?></div></td>
							
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></div></td>
							
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[csf('product_category')]];?></div></td>
							<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                            	<td width="40"> 
                             <? 
							 $file_type_name=$system_file_arr[$row[csf("job_no")]]['file'];
							 if($file_type_name!="")
							    {
							 ?>
                             <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[csf("job_no")] ?>','File View'),2"/>
                             <?
							   }
							  else echo " ";
							 ?></td>
							
							
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								$fabric_des="";
								$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
								echo $fabric_des;//$fabric_des;?></div></td>
							
						
							<td width="90" align="right"><p>
								<?
								echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								
								?></p></td>
							
                            <td width="90" align="right"><?=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];?></td>
                            
                            <td width="90" align="right"><p>
								<?
								echo number_format( $row[csf('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
								$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
								$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[csf('job_no')]][$poId];
								?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
							<td width="50" align="right"><p><? $unit_price=$row[csf('po_total_price')]/$row[csf('po_quantity')]; echo number_format($unit_price,2);?></p></td>
							<td width="100" align="right"><p>
								<?
									echo number_format($row[csf('po_total_price')],2);
									$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
									$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
								?></p></td>
							

						
						
							
							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs),2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')],2); ?></p></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set'];?></div></td>

						
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]); ?></div></td>
								<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><?=number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)/$row[csf('set_smv')],2); ?></div></td>
					


									<?php

								$contribution_margin_pcs=$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']+($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs);
								$contribution_margin=($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/ $costing_per_pcs)*$row[csf('po_quantity')]+$job_wise_margin_arr[$row[csf('job_no')]]['margin_pcs_set']*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$total_miniute=$row[csf('set_smv')]*$row[csf('po_quantity')];
								$sew_effi_percent=$job_wise_margin_arr[$row[csf('job_no')]]['sew_effi_percent'];
								$use_minute=(($row[csf('set_smv')]*$row[csf('po_quantity')])/$sew_effi_percent)*100;
								$epm=$contribution_margin/$use_minute;
								$profit_loss_pcs=$epm-$cost_per_val;
									?>

							<td width="100" align="right"><div style="word-wrap:break-word; width:100px"><?=number_format($contribution_margin_pcs,2);;?></div></td>
							<td width="100" align="right"><div style="word-wrap:break-word; width:100px"><?=number_format($contribution_margin,2);;?></div></td>
							<?
								if($row[csf('order_uom')]==58){								
									?>
							<td width="100" align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','500px')"><?echo number_format($row[csf('set_smv')],2);?></a></td>
							<?}else{?>
								<td width="100" align="right"><p><?echo number_format($row[csf('set_smv')],2);?></p></td>	<?	}?>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($total_miniute,2);;?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($sew_effi_percent,2);?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($use_minute,2);?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($cost_per_val,2);;?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($epm,2);;?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($profit_loss_pcs,2);;?></div></td>
							<td width="60" align="right"><div style="word-wrap:break-word; width:60px"><?=number_format($profit_loss_pcs*$row[csf('po_quantity')],2);;?></div></td>

							
							
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['cutting_qty']; $total_cutting_qty+=$job_wise_data_arr[$row[csf('job_no')]]['cutting_qty']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['sweing_out_qty']; $total_sweing_out_qty+=$job_wise_data_arr[$row[csf('job_no')]]['sweing_out_qty']; ?></p></td>
							<td width="100" align="center"><p><?	echo $job_wise_data_arr[$row[csf('job_no')]]['packing_qty'];$total_packing_qty+=$job_wise_data_arr[$row[csf('job_no')]]['packing_qty'];  ?></p></td>

							<td width="90" align="right"><p>
							<?

								?>
								<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
                                <?
								//echo  number_format( $ex_factory_qnty,0);
								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qnty;
									$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qnty;
									$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
									$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								?></p></td>
							<td width="70" align="center"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? if($ex_factory_date!="" && $ex_factory_date!="0000-00-00") echo change_date_format($ex_factory_date); ?>&nbsp;</div></a></td>
							<td  width="90" align="right"><p>
								<?
									$short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
									echo number_format($short_access_qnty,0);
									$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
									$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?></p>
							</td>
							<td width="120" align="right"><p>
								<?
									$short_access_value=$short_access_qnty*$unit_price;
									echo number_format($short_access_value,2);
									$total_short_access_value=$total_short_access_value+$short_access_value;
									$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?></p>
							</td>


							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$shiping_status]; ?></div></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:100px">
								<?
								$po_id_arr=explode(",",$row[csf('id')]);
								$lc_sc_no=$lc_sc_id=$lc_sc_type="";
								foreach($po_id_arr as $val)
								{
									if($lc_number_arr[$val]["lc_number"] !="")
									{
										$lc_sc_no.="LC: ". $lc_number_arr[$val]["lc_number"];
										$lc_sc_id.=$lc_number_arr[$val]["lc_id"];
										$lc_sc_type.='1,';
									}
									if($sc_number_arr[$val]["sc_number"] !="")
									{
										$lc_sc_no.=" SC: ".$sc_number_arr[$val]["sc_number"];
										$lc_sc_id.=$sc_number_arr[$val]["sc_id"];
										$lc_sc_type.='2,';
									}
								}
								echo implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
								$lc_sc_id=implode(",",array_unique(explode(",",chop($lc_sc_id,','))));
								$lc_sc_type=implode(",",array_unique(explode(",",chop($lc_sc_type,','))));
								?>
								</div>
							</td>
                            <td width="100" align="center">
								<?
									if($lc_sc_id!="")
									{
										?>
											<input type="button" class="image_uploader" id="system_id" style="width:40px" value="File" onClick="openmypage_file('requires/shipment_schedule_controller.php?action=file_show&mst_id=<?=$lc_sc_id;?>&lc_sc_type=<?=$lc_sc_type;?>','File View'),2"/>
										<?
									}
								?>
							</td>
							<td width="90" align="center"><div style="word-wrap:break-word; width:90px">
							<? if($lc_number_arr[$row[csf('id')]] !="")
								{
									echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]];

								}
							?>
							</div></td>
							<td width="80" align="center"><p><? echo $export_lc_arr[$row[csf('id')]]['file_no'];?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['pay_term']; ?></p></td>
							<td width="80" align="center"><p><?	echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></p></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></div></td>
							<td width="150" align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')]; ?></div></td>
							<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('id')]))); ?></p></td>
							<td width="100"><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
							<td><p><? echo $row[csf('details_remarks')]; ?></p></td>
							
						</tr>
					<?
					$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
						<td width="80" align="center" style="word-wrap:break-word;" >  Total (<?=$row_group[csf('grouping')]; ?>): </td>
						<td width="70" ></td>
						<td width="100"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="60"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="90"></td>
						<td width="110"></td>
                        <td width="100"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="70"></td>
						<td width="50"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="150"></td>
					
						<td width="50"></td>
						
					
                        <td width="100"></td>
						<td width="50"></td>
					
						<td width="70"></td>
						<td width="40"></td>
                        <td width="40"></td>
						
					
						<td width="200"></td>
					
						
						<td width="90" align="right" ><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>
                        <td width="90" align="right"><?=$total_cut_qty_dtls;?></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
						<td width="40"></td>
						<td width="50"></td>
						<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
					

		
					
						
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" ></td>
						<td width="100" ></td>

						<td width="100"></td>
						<td width="100"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="60"></td>


						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_cutting_qty,0); ?></td>
						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_sweing_out_qty,0); ?></td>
						<td width="100"><span style="color:#CCCCCC;">'</span><? echo number_format($total_packing_qty,0); ?></td>

						<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
						<td width="70"></td>
						<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
						<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
						<td width="100" ></td>
						
						<td width="100"></td>
						<td width="100"></td>
						<td width="90"></td>
						<td width="80"></td>
						<td width="80"></td>
						<td width="80"></td>

						<td width="150"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td width="100"></td>
						<td></th>
						
					 </tr>
				<?
				}
				?>
				</table>
				</div>
				<table width="6470" id="report_table_footer" border="1" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							<th width="80"></th>
							<th width="70" ></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="60"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="90"></th>
							<th width="110"></th>
                            <th width="100"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="100"></th>
							<td width="100"></td>
							<th width="100"></th>
							<td width="100"></td>
							<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total,2); ?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="150"></th>
						
							<th width="50"></th>
							
						
                            <th width="100"></th>
							<th width="50"></th>
						
							<th width="70"></th>
							<th width="40"></th>
                            <th width="40"></th>
						
							<th width="200"></th>
				
							<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            <th width="90"><?= $grand_total_cut_qty_dtls;?></th>
							<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot,0); ?></th>
							<th width="40"></th>
							<th width="50"></th>
							<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot,2); ?></th>
						

						
						
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>

							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>

							<th width="100"><? echo number_format($total_cutting_qty,0); ?></th>
							<th width="100"><? echo number_format($total_sweing_out_qty,0); ?></th>
							<th width="100"><? echo number_format($total_packing_qty,0); ?></th>
							<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty,0); ?></th>
							<th width="70"></th>
							<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty,0); ?></th>
							<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value,0); ?></th>
						
							<th width="100" ></th>
							
							<th width="100"></th>
							<th width="100"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>

							<th width="150"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="120"></th>
							<th width="100"></th>
							<th></th>
							
						</tr>
					</tfoot>
				</table>
				<?
		}
		?>
			<?php /* ?>
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
							<td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
							</tr>

							<tr bgcolor="#E9F3FF">
							<td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<? */  ?>
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
	$content = ob_get_contents();
	
	$is_created = fwrite($create_new_doc,$content);
	$filename=$user_id."_".$name.".xls";
	if($rpt_type==8)
	{
		?>
		<style type="text/css">
			.break_word{
				word-break: break-all;
				word-wrap: break-word;
				/*	width: 98%;*/
			}
		</style>
		<?
	}
	  
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

if($action=="show_image2")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name in( 'quotation_inquery_front_image','quotation_inquery_back_image','quotation_inquery_back_image','quotation_inquery_front_image') and is_deleted=0 and file_type=1");
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

if($action=="file_show")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$mst_id_all=implode("','",explode(",",$mst_id));
	$lc_sc_type_arr=explode(",",$lc_sc_type);
	if(count($lc_sc_type_arr)>1)
	{
		$sql_cond=" and form_name in ('Export LC Entry','sales_contract_entry')";
	}
	else
	{
		if($row[0]==1){$sql_cond=" and form_name= 'Export LC Entry' ";}
		if($row[0]==2){$sql_cond=" and form_name= 'sales_contract_entry' ";}
	}

	$data_array=sql_select("SELECT image_location, real_file_name from common_photo_library where master_tble_id in ('$mst_id_all') $sql_cond and is_deleted=0 and file_type=2");

	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
            <td><a href="<? echo "../../../../".$row['IMAGE_LOCATION']; ?>" target="_new"> 
            	<img src="<? echo "../../../../".'file_upload/blank_file.png'; ?>" width="80" height="60"> <br>
				<?=$row["REAL_FILE_NAME"];?>
			</a>
            </td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}
if($action=="report_generate_popup"){
	extract($_REQUEST);
	echo load_html_head_contents("TNA Process","../../../../", 1, 1, $unicode,1,'');
	?>
	<div style="text-align:right;">
		<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="fn_report_generated('report_generate')" />
		<input type="button" id="show_button3" class="formbutton" style="width:80px;" value="Sweater" onClick="fn_report_generated('report_generate3')" />
	</div>

 
	<script>
		

	let fn_report_generated=(action)=>{
		
		//get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_group*cbo_date_type*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_style_ref*txt_order_no*cbo_search_by*cbo_year_selection*txt_internal_ref*txt_file_no*txt_order_no_id*txt_style_id*cbo_season_id*cbo_ship_status',"../../../")
			var search_by=2;
			var report_title='Material Followup Report';
			var data="action="+action+"&cbo_company_name='<?=$cbo_company_name;?>'&cbo_buyer_name='0'&cbo_item_group=''&cbo_date_type='2'&txt_date_from=''&txt_date_to=''&cbo_year='0'&txt_job_no=''&txt_style_ref=''&txt_order_no=''&cbo_search_by='2'&cbo_year_selection='2022'&txt_internal_ref=''&txt_file_no=''&txt_order_no_id='<?=$po_id;?>'&txt_style_id=''&cbo_season_id='0'&cbo_ship_status='0'&report_title=Material Followup Report";
			//alert(data);
			//freeze_window(3);


			http.open("POST","material_followup_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = ()=>{
				if(http.readyState == 4) 
				{
					
					var reponse=trim(http.responseText).split("****");
					var tot_rows=reponse[2];
					 
					$('#report_container2').html(reponse[0]);
					//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					if (reponse[3]==2)
					{
						if(tot_rows*1>1){
							if(search_by==1){
							
								var tableFilters = {
									col_operation: {
									id: ["value_pre_costing","value_wo_qty","value_in_amount","value_rec_qty","value_issue_amount","value_leftover_amount"],
									col: [20,23,28,29,31,33],
									operation: ["sum","sum","sum","sum","sum","sum"],
									write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
									}	
								}
								//alert(tableFilters);
								
							}
							if(search_by==2){
								var tableFilters = {
									col_operation: {
									id: ["value_pre_costing","value_wo_qty","value_in_amount","value_rec_qty","value_issue_amount","value_leftover_amount"],
									col: [19,22,27,28,30,32],
									operation: ["sum","sum","sum","sum","sum","sum"],
									write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
									}	
								}
							}
							setFilterGrid("table_body",-1,tableFilters);
						}
					}
					//setFilterGrid("table_body",-1,tableFilters);
					//setFilterGrid("table_body_style",-1);
					//show_msg('3');
					//release_freezing();
				}


			}//-----
		 
	}
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	

	<div id="report_container2"></div>

	<?
}

if ($action=='wash_rate_popup') 
{
	echo load_html_head_contents("SMV Set Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$width = 530;
  ?>
	<style>  		
		tbody tr th{
			border: 1px solid #8DAFDA;
		}
	</style>
	<script>
		function print_window()
		{
			//$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:100%;display:flex; justify-content:center;margin-top:10px;">
		<div>
			<input  type="button" value="Print" onClick="print_window()" style="width:70px"  class="formbutton"/> &nbsp;
		</div>
		<div id="report_container_popup"> </div>
	</div>	
	<?
	 	$sql_cond = "";
	 	$sql_cond .= $job_id ? " and a.job_id=$job_id " : "";
	 	$sql_cond .= $buyer_id ? " and b.buyer_name=$buyer_id " : "";
		$sql = "SELECT a.color_number_id as color,a.item_number_id as item,a.rate,b.job_no,b.style_ref_no as style,b.buyer_name as buyer,c.emb_type from wo_pre_cos_emb_co_avg_con_dtls a,wo_po_details_master b,wo_pre_cost_embe_cost_dtls c where a.job_id=b.id and c.id=a.pre_cost_emb_cost_dtls_id $sql_cond and c.emb_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		// echo $sql; die;
		$sql_res = sql_select($sql);
		$data_array = array();
		foreach ($sql_res as  $v) 
		{ 
			$info_data_array ['JOB_NO']	= $v['JOB_NO'];
			$info_data_array ['STYLE']	= $v['STYLE'];
			$info_data_array ['BUYER']	= $v['BUYER'];

			$data_array[$v['EMB_TYPE']][$v['ITEM']][$v['COLOR']]['TOTAL_RATE'] += $v['RATE'];
			$data_array[$v['EMB_TYPE']][$v['ITEM']][$v['COLOR']]['RATE_COUNT'] ++;
		}

		/* echo "<pre>";
		print_r($data_array); 
		die; */
		ob_start(); 
	?>
	<fieldset  style="height:auto; width:<? echo $width+20;?>px; margin:20px auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
			<thead class="form_caption" >
				<tr>
				<td colspan="6" align="center" style="font-size:18px; font-weight:bold" >Target Value Details</td>
				</tr>
			</thead>
		</table>
		<div id="report_div">  
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin: 10px auto; padding:0;">
				<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body"> 
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table details_table" id="table_body" width="<?= $width; ?>" rules="all" align="left"> 
						<thead> 
							<th colspan="2"> Wash Rate Details </th>  
						</thead>
						<tbody>   
							<tr>
								<td align="center" width="201"> <b>Job Number</b> </td> 
								<td align="center" width="350"> <?=  $info_data_array ['JOB_NO'] ?>  </td>  
							</tr>  
							<tr>
								<td align="center"> <b>Style Ref. No.</b> </td> 
								<td align="center"> <?=  $info_data_array ['STYLE'] ?>  </td>  
							</tr>  
							<tr>
								<td align="center"> <b>Buyer Name</b> </td> 
								<td align="center"> <?= $buyer_library[$info_data_array['BUYER']] ?>  </td>  
							</tr>  
						</tbody>
					</table>
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table details_table" id="table_body" width="<?= $width; ?>" rules="all" align="left"> 
						<thead> 
							<th width="30"> Sl </th> 
							<th width="120"> Wash Type </th>
							<th width="120"> Item </th>
							<th width="80"> Color </th>
							<th width="60"> Rate </th> 
						</thead>
						<tbody>  
							<?
								$i=0;
								foreach ($data_array as $emb_type => $item_data)
								{
									foreach ($item_data as $item_id => $color_data)
									{
										foreach ($color_data as $color_id => $v)
										{
											$avg_rate = $v['TOTAL_RATE'] / $v['RATE_COUNT'] ;
											?>
												<tr>
													<td> <?= ++$i ?> </td> 
													<td width="120"> <?= $emblishment_wash_type_arr[$emb_type] ?>  </td>
													<td width="120"> <?= $garments_item[$item_id] ?> </td>
													<td width="80"> <?= $color_library[$color_id] ?> </td>
													<td width="60" align="right"> <?= number_format($avg_rate,2) ?> </td> 
												</tr>
											<?
										}

									}
								}
							?>  
						</tbody>
					</table>
				</div>
			</div>
		</div>	
		<?php
			$html=ob_get_contents();
			ob_flush();
			
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen($name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container_popup').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel View" name="excel" id="excel" class="formbutton" style="width:90px;"/></a>&nbsp;&nbsp;';
			});	
		</script>
	</fieldset>  

  <?
}

?>
