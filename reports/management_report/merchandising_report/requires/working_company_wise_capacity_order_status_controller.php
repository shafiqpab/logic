<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");


require_once('../../../../includes/common.php');
/*require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');*/

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}

if ($action=="lc_load_drop_down_location")
{
	echo create_drop_down( "cbo_lc_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$lc_company_id=str_replace("'","",$cbo_lc_company_id);
	$lc_location_id=str_replace("'","",$cbo_lc_location_id);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$country_name_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	
	if($location_id){$wc_locatin_cond=" and a.location_id in($location_id)";}else{$wc_locatin_cond="";}
	if($company_id){$company_con=" and a.comapny_id in($company_id)";}
	else{$company_con="";}

	$sql="select 
	a.comapny_id,a.location_id,b.capacity_min,b.capacity_pcs,b.date_calc 
	from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $company_con $wc_locatin_cond and b.date_calc between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 group by a.comapny_id,a.location_id,b.capacity_min,b.capacity_pcs,b.date_calc";

	$sql_data_smv=sql_select($sql);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		$key=$row[csf("comapny_id")].'_'.$row[csf("location_id")];
		$capacity_arr[$key]['efficency']+=$row[csf("effi_percent")];
		$capacity_arr[$key]['basic_smv']+=$row[csf("basic_smv")];
		$capacity_arr[$key]['avg_machine_line']+=$row[csf("avg_machine_line")];
		$capacity_arr[$key]['working_day']+=$row[csf("working_day")];
		$capacity_arr[$key]['no_of_line']+=$row[csf("no_of_line")];
		$capacity_arr[$key]['tot_mc_val']+=$row[csf("no_of_line")]*$row[csf("avg_machine_line")];
		$capacity_arr[$key]['capacity_min']+=$row[csf("capacity_min")];
		$capacity_arr[$key]['capacity_pcs']+=$row[csf("capacity_pcs")];
		//$location_id_arr[$date_key].=$row[csf("location_id")].',';
	}
	unset($sql_data_smv);
	 //echo "<pre>";
	 //print_r($capacity_arr);die;
	 //var_dump($capacity_arr);die;
	
	
	if($location_id){$wc_locatin_cond=" and a.working_location_id in($location_id)";}else{$wc_locatin_cond="";}
	if($lc_location_id){$lc_locatin_cond=" and a.location_name in($lc_location_id)";}else{$lc_locatin_cond="";}
	
	if($lc_company_id){$lc_company_con=" and a.company_name in($lc_company_id)";}else{$lc_company_con="";}
	if($company_id){$wc_company_con=" and a.style_owner in($company_id)";}else{$wc_company_con="";}
	if($company_id){$wc_company_con2=" and a.company_id in($company_id)";}else{$wc_company_con2="";}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	
	//$sql_set="select d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,d.SMV_PCS,d.SMV_SET from wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS C where b.JOB_NO_MST=c.JOB_NO";
	
	
	
	if($cbo_date_cat_id==1)//Pub Ship Date
	{
	$sql="SELECT d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,d.SMV_PCS,d.SMV_SET, $year_field,a.company_name,a.style_owner,a.working_location_id,a.dealing_marchant,a.location_name,a.job_no,a.style_ref_no,a.gmts_item_id,a.season_buyer_wise as season_id,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty, b.pub_shipment_date,b.pub_shipment_date as pub_ship,b.shipment_date as actual_shipment_date,b.po_received_date,b.id as po_id,b.po_number,b.shiping_status,b.is_confirmed,b.shipment_date,b.unit_price,b.insert_date,b.grouping,a.team_leader,a.product_dept,a.season_year,a.brand_id,c.country_ship_date as country_shipment_date,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS d,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=d.job_no and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.pub_shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	}
	else if($cbo_date_cat_id==3)//Actual Ship Date
	{
	$sql="SELECT d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,d.SMV_PCS,d.SMV_SET, $year_field,a.company_name,a.style_owner,a.working_location_id,a.location_name,a.job_no,a.style_ref_no,a.gmts_item_id,a.season_buyer_wise as season_id,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty,b.pub_shipment_date as po_pub_date,b.po_received_date, b.shipment_date as pub_shipment_date,pub_shipment_date as pub_ship, b.shipment_date as actual_shipment_date,b.id as po_id,b.po_number,b.shiping_status,b.is_confirmed,b.shipment_date,b.unit_price,b.insert_date,b.grouping,a.team_leader,a.product_dept,a.season_year,a.brand_id,c.country_ship_date as country_shipment_date,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS d,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=d.job_no and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else //Country Ship Date
	{
	 $sql="SELECT $year_field,a.company_name,a.style_owner,a.working_location_id,a.location_name,a.job_no,a.style_ref_no,a.gmts_item_id,a.season_buyer_wise as season_id,a.set_smv,a.set_break_down,a.buyer_name,a.total_set_qnty,b.po_received_date,b.pub_shipment_date as po_pub_date, c.country_id,c.country_ship_date as pub_shipment_date,c.country_ship_date as country_shipment_date,b.id as po_id,b.po_number,b.shiping_status,b.is_confirmed,b.shipment_date,b.unit_price,b.insert_date,b.grouping,a.team_leader,
	(c.order_quantity/a.total_set_qnty) as po_quantity,a.product_dept,a.season_year,a.brand_id,
	(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $lc_company_con $wc_company_con  $lc_locatin_cond $wc_locatin_cond AND c.country_ship_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
	//echo $sql;
	
	$sql_data=sql_select($sql);
	foreach( $sql_data as $row)
	{
		
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
		{
			//foreach($set_break_down_arr as $set_break_down){
			
				//list($item_id,$set,$smv)=explode('_',$set_break_down);
				$item_id=$row[GMTS_ITEM_ID];
				$set=$row[SET_ITEM_RATIO];
				$smv=$row[SMV_PCS];
				
				
				$confirm_qty=$row[csf('confirm_qty')]*$set;
				$project_qty=$row[csf('projected_qty')]*$set;
				
				$key=$row[csf('style_owner')].'_'.$row[csf('working_location_id')].'_'.$row[csf('company_name')].'_'.$row[csf('buyer_name')];
				
				$order_data_array[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')]][$row[csf('buyer_name')]]=$key;
				
				$order_wise_array[$row[csf('po_id')]]['style_owner']=$row[csf('style_owner')];
				
				$order_wise_array[$row[csf('po_id')]]['working_location_id']=$row[csf('working_location_id')];
				$order_wise_array[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
				$order_wise_array[$row[csf('po_id')]]['location_name']=$row[csf('location_name')];
				$order_wise_array[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$order_wise_array[$row[csf('po_id')]]['year']=$row[csf('year')];
				$order_wise_array[$row[csf('po_id')]]['po_qty_pcs']+=$confirm_qty+$project_qty;
				$order_wise_array[$row[csf('po_id')]]['po_value']+=($confirm_qty+$project_qty)*$rate_in_pcs;
				$order_wise_array[$row[csf('po_id')]]['po_minute']+=($confirm_qty+$project_qty)*$smv;
				$order_wise_array[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$order_wise_array[$row[csf('po_id')]]['season_id']=$row[csf('season_id')];
				$order_wise_array[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$order_wise_array[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$order_wise_array[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$order_wise_array[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$order_wise_array[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$order_wise_array[$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
				$order_wise_array[$row[csf('po_id')]]['shipment_date']=$row[csf('shipment_date')];
				$order_wise_array[$row[csf('po_id')]]['insert_date']=$row[csf('insert_date')];
				$order_wise_array[$row[csf('po_id')]]['grouping']=$row[csf('grouping')];
				$order_wise_array[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
				$order_wise_array[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
				$order_wise_array[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
				$order_wise_array[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
				$order_wise_array[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')]; 
				$order_wise_array[$row[csf('po_id')]]['actual_shipment_date']=$row[csf('actual_shipment_date')]; 
				$order_wise_array[$row[csf('po_id')]]['pub_ship']=$row[csf('pub_ship')]; 
				$order_wise_array[$row[csf('po_id')]]['country_shipment_date']=$row[csf('country_shipment_date')]; 
				$order_wise_array[$row[csf('po_id')]]['product_dept']=$row[csf('product_dept')];
				$order_wise_array[$row[csf('po_id')]]['season_year']=$row[csf('season_year')];
				$order_wise_array[$row[csf('po_id')]]['brand_id']=$row[csf('brand_id')];
				
				
				$for_row_sapn[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')].$row[csf('buyer_name')]]=1;
				
				
				$projected_qty_array[$key]+=$project_qty;
				$confirm_qty_array[$key]+=$confirm_qty;
				$projected_value_array[$key]+=$rate_in_pcs*$project_qty;
				$confirm_value_array[$key]+=$rate_in_pcs*$confirm_qty;
				
				$confirm_mint_array[$key]+=$smv*$confirm_qty;
				$projected_mint_array[$key]+=$smv*$project_qty;
				
				
				$wc_location_total_confirm_min_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($smv*$confirm_qty)+($smv*$project_qty);
				
				$wc_location_total_confirm_qty_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($confirm_qty+$project_qty);
				
				
			//}
		}
		else
		{
			
			//$item_id=$set_break_down_arr[0];
			//$set=$set_break_down_arr[1];
			//$smv=$set_break_down_arr[2];
			
			$confirm_qty=$row[csf('confirm_qty')];
			$project_qty=$row[csf('projected_qty')];
			
			$confirm_value=$row[csf('confirm_value')];
			$project_value=$row[csf('projected_value')];
			
			
			$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv=$po_qty_set_smv/$row[csf('po_quantity')];			
			
			/*$po_qty_set_smv=$row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv=$po_qty_set_smv/$row[csf('po_quantity')];
			$order_data_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_order_data_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			$confirm_qty_array[$date_key][$row[csf('buyer_name')]]+=$confirm_qty;
			$projected_qty_array[$date_key][$row[csf('buyer_name')]]+=$project_qty;
			
			
			$confirm_value_array[$date_key][$row[csf('buyer_name')]]+=$confirm_value;//$rate_in_pcs*$confirm_qty;
			$projected_value_array[$date_key][$row[csf('buyer_name')]]+=$project_value;//$rate_in_pcs*$project_qty;
			
			$booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$confirm_qty);
			$projected_booked_smv_mints_arr[$date_key][$row[csf("buyer_name")]]+=($smv*$project_qty);
			
			$booked_parcent_arr[$date_key]+=(($smv*$confirm_qty)/$capacity_arr[$date_key]['capacity_month_min'])*100;*/
			
			
			$key=$row[csf('style_owner')].'_'.$row[csf('working_location_id')].'_'.$row[csf('company_name')].'_'.$row[csf('buyer_name')];

			$order_data_array[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')]][$row[csf('buyer_name')]]=$key;
			$for_row_sapn[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')].$row[csf('buyer_name')]]=1;
			
			$projected_qty_array[$key]+=$project_qty;
			$confirm_qty_array[$key]+=$confirm_qty;
			$projected_value_array[$key]+=$rate_in_pcs*$project_qty;
			$confirm_value_array[$key]+=$rate_in_pcs*$confirm_qty;
			
			$confirm_mint_array[$key]+=$smv*$confirm_qty;
			$projected_mint_array[$key]+=$smv*$project_qty;
			
			
			$wc_location_total_confirm_min_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($smv*$confirm_qty)+($smv*$project_qty);
			
			$wc_location_total_confirm_qty_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($confirm_qty+$project_qty);
			
				//Details Part
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['style_owner']=$row[csf('style_owner')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['working_location_id']=$row[csf('working_location_id')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['company_name']=$row[csf('company_name')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['location_name']=$row[csf('location_name')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name']=$row[csf('buyer_name')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['year']=$row[csf('year')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty_pcs']+=$confirm_qty+$project_qty;
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_value']+=($confirm_qty+$project_qty)*$rate_in_pcs;
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_minute']+=($confirm_qty+$project_qty)*$smv;
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['unit_price']=$row[csf('unit_price')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['season_id']=$row[csf('season_id')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['job_no']=$row[csf('job_no')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_number']=$row[csf('po_number')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['country_date']=$row[csf('pub_shipment_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_received_date']=$row[csf('po_received_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['shipment_date']=$row[csf('shipment_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['insert_date']=$row[csf('insert_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['grouping']=$row[csf('grouping')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['set_smv']=$row[csf('set_smv')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['is_confirmed']=$row[csf('is_confirmed')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['shiping_status']=$row[csf('shiping_status')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['team_leader']=$row[csf('team_leader')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['pub_ship']=$row[csf('pub_ship')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['actual_shipment_date']=$row[csf('actual_shipment_date')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['country_shipment_date']=$row[csf('country_shipment_date')];	
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['product_dept']=$row[csf('product_dept')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['season_year']=$row[csf('season_year')];
				$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['brand_id']=$row[csf('brand_id')];
		}
		/* echo '<pre>';
		print_r($country_wise_array);//die; */
		
	}
	ob_start(); 
	$date_category_arr=array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date');
	
	?>
    <div style="margin:0 auto; width:2330px;">
        <table width="2300" border="0" cellpadding="2" cellspacing="0"> 
            <thead>
                <tr class="form_caption">
                    <td colspan="25" align="center" style="font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                
                <tr class="form_caption">
                    <td colspan="25" align="center"><? echo $date_category_arr[$cbo_date_cat_id].' ('. change_date_format($date_from).' To '.change_date_format($date_to); ?>)</td> 
                </tr>
            </thead>
        </table>
        <div style="width:2300px;">
        <table cellspacing="0" width="2300" border="1" rules="all" class="rpt_table" id="scroll_body">
            <thead>
                <tr>
                    <th rowspan="2" width="35">SL</th>
                    <th rowspan="2" width="70">Working Company</th>
                    <th rowspan="2" width="100">Location Name</th>
                    <th rowspan="2" width="70">100% Capacity (Mint)</th>
                    <th rowspan="2" width="70">100%  Capacity (Pcs)</th>
                    <th rowspan="2" width="60">LC Company</th>
                    <th rowspan="2" width="100">Buyer</th>
                    <th rowspan="2" width="70">Avg. SMV</th>
                    <th colspan="8">Quantity Details (Pcs)</th>	
                    <th colspan="3">Minute Details (SMV)</th>
                    <th colspan="4">Percentage (%)</th>
                    <th rowspan="2" width="100">Balance Qty.</th>
                    <th rowspan="2">Balance Mint.</th>
                </tr>
                <tr>
                    <th width="70">Proj. Avg.Unit Price</th>
                    <th width="100">Projected Qty.</th>
                    <th width="100">Projected Value</th>
                    <th width="70">Conf. Avg.Unit Price</th>
                    <th width="100">Confirm Qty.</th>
                    <th width="100">Confirm  Value</th>
                    <th width="100">Total Proj. and Conf. Qty. (Pcs)</th>
                    <th width="100">Total Proj. and Conf. Value</th>
                    
                    <th width="100">Projected Mint.</th>
                    <th width="100">Confirm Mint.</th>
                    <th width="100">Total Proj. and Conf. Mint.</th>
                    
                    <th width="100">Total Proj. and Conf Qty. %</th>	
                    <th width="100">Total Proj. and Conf Mint %</th>	
                    <th width="100">Proj. and Conf. Buyer Share % Mint.</th>	
                    <th width="100">Proj. and Conf. Buyer Share % Qty</th>
                </tr>
            </thead>
            </table>
            </div>
            <div style="width:2346px; max-height:350px; overflow-y:scroll" id="scroll_body">
            <table cellspacing="0" width="2300" border="1" rules="all" class="rpt_table" id="scroll_body">
            <tbody>
				 <? $i=1;
                 foreach($order_data_array as $style_company=>$style_location_arr){
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			
					foreach($style_location_arr as $style_location=>$lc_company_arr){
						$rspan=count($for_row_sapn[$style_company][$style_location]);
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
                    <td width="35" align="center" rowspan="<? echo $rspan;?>"><? echo $i;?></td>
				    <td width="70" align="center" rowspan="<? echo $rspan;?>"><? echo $companyArr[$style_company]; ?></td>
                    <td width="100" rowspan="<? echo $rspan;?>"><? echo $location_arr[$style_location]; ?></td>
                	<td width="70" rowspan="<? echo $rspan;?>" align="right"><? echo $capacity_arr[$style_company.'_'.$style_location]['capacity_min'];?></td>
                	<td width="70" rowspan="<? echo $rspan;?>" align="right"><? echo $capacity_arr[$style_company.'_'.$style_location]['capacity_pcs'];?></td>
                    <? 
					$tot_location_qty=0;$tot_location_val=0;$tot_location_min=0;
					$tot_location_projected_qty=0;$tot_location_confirm_qty=0;$tot_location_projected_val=0;
					$tot_location_confirm_val=0;$tot_location_projected_min=0;$tot_location_confirm_min=0;
					$total_location_proj_conf_qty_percent=0;$total_location_proj_conf_mint_percent=0;
					$flag=0;
					foreach($lc_company_arr as $lc_company=>$buyer_data_arr){
						if($flag!=0){echo "<tr>";}
					?>
                        <td width="60" align="center" rowspan="<? echo count($buyer_data_arr);?>"><? echo $companyArr[$lc_company]; ?></td>
                        <? 
						$flag2=0;
						foreach($buyer_data_arr as $buyer_id=>$key){ 
							if($flag2!=0){echo "<tr>";}
							
							$projected_avg_rate=$projected_value_array[$key]/$projected_qty_array[$key];
							$confirm_avg_rate=$confirm_value_array[$key]/$confirm_qty_array[$key];
							if(is_infinite($projected_avg_rate) || is_nan($projected_avg_rate)){$projected_avg_rate=0;}
							if(is_infinite($confirm_avg_rate) || is_nan($confirm_avg_rate)){$confirm_avg_rate=0;}
							
							$total_proj_conf_qty_percent=(($confirm_qty_array[$key]+$projected_qty_array[$key])*100)/$capacity_arr[$style_company.'_'.$style_location]['capacity_pcs'];
							if(is_infinite($total_proj_conf_qty_percent) || is_nan($total_proj_conf_qty_percent)){$total_proj_conf_qty_percent=0;}
							
							$total_proj_conf_mint_percent=(($confirm_mint_array[$key]+$projected_mint_array[$key])*100)/$capacity_arr[$style_company.'_'.$style_location]['capacity_min'];
							if(is_infinite($total_proj_conf_mint_percent) || is_nan($total_proj_conf_mint_percent)){$total_proj_conf_mint_percent=0;}
							
							$total_location_proj_conf_qty_percent+=$total_proj_conf_qty_percent;
							$total_location_proj_conf_mint_percent+=$total_proj_conf_mint_percent;
							
							
							
							$tot_location_projected_qty+=$projected_qty_array[$key];
							$tot_location_confirm_qty+=$confirm_qty_array[$key];
							
							$tot_location_projected_val+=$projected_value_array[$key];
							$tot_location_confirm_val+=$confirm_value_array[$key];
							
							$tot_location_projected_min+=$projected_mint_array[$key];
							$tot_location_confirm_min+=$confirm_mint_array[$key];
							
							
							$tot_location_qty+=$projected_qty_array[$key]+$confirm_qty_array[$key];
							$tot_location_val+=$projected_value_array[$key]+$confirm_value_array[$key];
							$tot_location_min+=$confirm_mint_array[$key]+$projected_mint_array[$key];
							
							$buyer_avg_smv=($confirm_mint_array[$key]+$projected_mint_array[$key])/($confirm_qty_array[$key]+$projected_qty_array[$key]);
							if(is_infinite($buyer_avg_smv) || is_nan($buyer_avg_smv)){$buyer_avg_smv=0;}
							
							
							$proj_conf_buyer_share_percent_mint=(($confirm_mint_array[$key]+$projected_mint_array[$key])*100)/$wc_location_total_confirm_min_arr[$style_company.'_'.$style_location];
							if(is_infinite($proj_conf_buyer_share_percent_mint) || is_nan($proj_conf_buyer_share_percent_mint)){$proj_conf_buyer_share_percent_mint=0;}
							
							$proj_conf_buyer_share_percent_qty =(($confirm_qty_array[$key]+$projected_qty_array[$key])*100)/$wc_location_total_confirm_qty_arr[$style_company.'_'.$style_location];
							if(is_infinite($proj_conf_buyer_share_percent_qty) || is_nan($proj_conf_buyer_share_percent_qty)){$proj_conf_buyer_share_percent_qty=0;}
							
						
						?>
                        	<td width="100"><? echo $buyer_arr[$buyer_id]; ?></td>
                            <td width="70" align="center"><? echo number_format($buyer_avg_smv,2);?></td>
                            <td width="70" align="right"><? echo number_format($projected_avg_rate,2);?></td>
                            <td width="100" align="right"><? echo $projected_qty_array[$key];?></td>
                            <td width="100" align="right"><? echo number_format($projected_value_array[$key],2);?></td>
                            <td width="70" align="right"><? echo number_format($confirm_avg_rate,2);?></td>
                            <td width="100" align="right"><? echo $confirm_qty_array[$key];?></td>
                            <td width="100" align="right"><? echo number_format($confirm_value_array[$key],2);?></td>
                            <td width="100" align="right"><? echo number_format($projected_qty_array[$key]+$confirm_qty_array[$key]);?></td>
                            <td width="100" align="right"><? echo number_format($projected_value_array[$key]+$confirm_value_array[$key],2);?></td>
                            <td width="100" align="right"><? echo $projected_mint_array[$key];?></td>
                            <td width="100" align="right"><? echo $confirm_mint_array[$key];?></td>
                            <td width="100" align="right"><? echo $projected_mint_array[$key]+$confirm_mint_array[$key];?></td>
                            <td width="100" align="right"><? echo number_format($total_proj_conf_qty_percent,2);?></td>
                            <td width="100" align="right"><? echo number_format($total_proj_conf_mint_percent,2);?></td>
                            <td width="100" align="right"><? echo number_format($proj_conf_buyer_share_percent_mint,2);?></td>
                            <td width="100" align="right"><? echo number_format($proj_conf_buyer_share_percent_qty,2);?></td>
                        <? 
							
							if($flag==0 && $flag2==0){
							echo"
							<td width='100' rowspan='".$rspan."' align='right'>".number_format(($capacity_arr[$style_company.'_'.$style_location]['capacity_pcs']-$wc_location_total_confirm_qty_arr[$style_company.'_'.$style_location]))."</td>
							<td width='' rowspan='".$rspan."' align='right'>".number_format(($capacity_arr[$style_company.'_'.$style_location]['capacity_min']-$wc_location_total_confirm_min_arr[$style_company.'_'.$style_location]),2)."</td>
							</tr>
                            ";
							}
							
							if($flag2!=0){echo "</tr>";}
							
						$flag2++;
						} 
						?>
					<? 
						 //if($flag!=0){echo "</tr>";}
					$flag++;
					} 
					?>
                <? $i++;
				echo "
				<tr bgcolor='#DDD'>
				<td colspan='7' align='right'><b>WC. Location Total: (".$location_arr[$style_location].")</b> </td>
				<td></td>
				<td></td>
				<td align='right'><b>$tot_location_projected_qty</b></td>
				<td align='right'><b>".number_format($tot_location_projected_val,2)."</b></td>
				<td></td>
				<td align='right'><b>$tot_location_confirm_qty</b></td>
				<td align='right'><b>".number_format($tot_location_confirm_val,2)."</b></td>
				<td align='right'><b>$tot_location_qty</b></td>
				<td align='right'><b>".number_format($tot_location_val,2)."</b></td>
				
				<td align='right'><b>$tot_location_projected_min</b></td>
				<td align='right'><b>$tot_location_confirm_min</b></td>
				<td align='right'><b>$tot_location_min</b></td>
				
				<td align='right'><b>".(number_format($total_location_proj_conf_qty_percent,2))."</b></td>
				<td align='right'><b>".(number_format($total_location_proj_conf_mint_percent,2))."</b></td>
				<td align='right'><b>100.00</b></td>
				<td align='right'><b>100.00</b></td>
				<td align='right'><b>".number_format((($capacity_arr[$style_company.'_'.$style_location]['capacity_pcs'])-$tot_location_qty))."</b></td>
				<td align='right'><b>".number_format((($capacity_arr[$style_company.'_'.$style_location]['capacity_min'])-$tot_location_min),2)."</b></td>
				</tr>
				";
				
				$grand_tot_location_projected_qty+=$tot_location_projected_qty;
				$grand_tot_location_projected_val+=$tot_location_projected_val;
				$grand_tot_location_confirm_qty+=$tot_location_confirm_qty;
				$grand_tot_location_confirm_val+=$tot_location_confirm_val;
				$grand_tot_location_qty+=$tot_location_qty;
				$grand_tot_location_val+=$tot_location_val;
				$grand_tot_location_projected_min+=$tot_location_projected_min;
				$grand_tot_location_confirm_min+=$tot_location_confirm_min;
				$grand_tot_location_min+=$tot_location_min;
				
				$grand_total_location_proj_conf_qty_percent+=$total_location_proj_conf_qty_percent;
				$grand_total_location_proj_conf_mint_percent+=$total_location_proj_conf_mint_percent;
				
				
				
				$grand_tot_bal_qty+=(($capacity_arr[$style_company.'_'.$style_location]['capacity_pcs'])-$tot_location_qty);
				$grand_tot_bal_min+=(($capacity_arr[$style_company.'_'.$style_location]['capacity_min'])-$tot_location_min);
				
				
				}
			 } 
			?>
            </tbody>
            <tfoot>
				<th colspan='7' align='right'><b>Grand Total: </b> </th>
				<th></th>
				<th></th>
				<th align='right'><b><? echo $grand_tot_location_projected_qty;?></b></th>
				<th align='right'><b><? echo number_format($grand_tot_location_projected_val,2);?></b></th>
				<th></th>
				<th align='right'><b><? echo $grand_tot_location_confirm_qty;?></b></th>
				<th align='right'><b><? echo number_format($grand_tot_location_confirm_val,2);?></b></th>
				<th align='right'><b><? echo $grand_tot_location_qty;?></b></th>
				<th align='right'><b><? echo number_format($grand_tot_location_val,2);?></b></th>
				
				<th align='right'><b><? echo $grand_tot_location_projected_min;?></b></th>
				<th align='right'><b><? echo $grand_tot_location_confirm_min;?></b></th>
				<th align='right'><b><? echo number_format($grand_tot_location_min,2);?></b></th>
				
				<th><? //echo number_format($grand_total_location_proj_conf_qty_percent,2);?></th>
				<th><? //echo number_format($grand_total_location_proj_conf_mint_percent,2);?></th>
				<th align='right'><b></b></th>
				<th align='right'><b></b></th>
				<th align='right'><b><? echo number_format($grand_tot_bal_qty);?></b></th>
				<th align='right'><b><? echo number_format($grand_tot_bal_min,2);?></b></th>
            
            </tfoot>
        </table>
		
         </div>
       	 <br><br>
		 <?
		 if($cbo_date_cat_id==1)//Pub Ship Date
		 {
		 	$shipment_year="  extract(year from b.pub_shipment_date) as year";
		    $shipment_month="  extract(month from b.pub_shipment_date) as month";
			$shipment_year2="  extract(year from b.sales_target_date) as year";
		    $shipment_month2="  extract(month from b.sales_target_date) as month";	
		 }
		 else if($cbo_date_cat_id==3)
		 {
			$shipment_year="  extract(year from b.shipment_date) as year";
			$shipment_month="  extract(month from b.shipment_date) as month";
			$shipment_year2="  extract(year from b.sales_target_date) as year";
		    $shipment_month2="  extract(month from b.sales_target_date) as month";
			
		 }
		 else{
			$shipment_year="  extract(year from c.country_ship_date) as year";
			$shipment_month="  extract(month from c.country_ship_date) as month";
			$shipment_year2="  extract(year from b.sales_target_date) as year";
		    $shipment_month2="  extract(month from b.sales_target_date) as month";
		 }
		 //echo $shipment_year;
		  if($cbo_date_cat_id==1)
		  { //b.po_quantity*a.total_set_qnty
			  $data_array=sql_select("select  a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.pub_shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.style_owner,  b.is_confirmed,b.pub_shipment_date ");
			  $sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
		  }
		  else if($cbo_date_cat_id==3)
		  { 
			$data_array=sql_select("select  a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.style_owner,  b.is_confirmed,b.shipment_date ");
			  $sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
		  }
		  else
		  {
			$data_array=sql_select("select  a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND c.country_ship_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.style_owner,  b.is_confirmed,c.country_ship_date ");
			$sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
		  } 
	  
			  $project_data_arr=array();
			  $confrim_data_arr=array();
			  $month_count_arr=array();
			  $total_buyer_arr=array();
			  $total_qty_arr=array();
			  $total_confrim=array();
			  $total_project=array();
			  $grand_confirm_buyer=array();
			  $grand_project_buyer=array();

			foreach($data_array as $row)
			{
				$total_buyer_arr[$row[csf('company_name')]]=$row[csf('company_name')];
				$month_count_arr[$row[csf('year')]][$row[csf('month')]]['year']=$row[csf('year')];
				$month_count_arr[$row[csf('year')]][$row[csf('month')]]['month']=$row[csf('month')];

			  	if($row[csf('is_confirmed')]==1)
				{
				   $confrim_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['confirmQty']+=$row[csf('po_quantity_pcs')];
				   $confrim_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['confirmMin']+=$row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
				   $tot_confrim_data_arr[$row[csf('year')]][$row[csf('month')]]['totconfirmQty']+=$row[csf('po_quantity_pcs')];
				   $tot_confrim_data_arr[$row[csf('year')]][$row[csf('month')]]['totconfirmMin']+=$row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
				}
				if($row[csf('is_confirmed')]==2)
				{
				   $project_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['projectQty']+=$row[csf('po_quantity_pcs')];
				   $project_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['projectMin']+=$row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
				   $tot_project_data_arr[$row[csf('year')]][$row[csf('month')]]['totprojectQty']+=$row[csf('po_quantity_pcs')];
				   $tot_project_data_arr[$row[csf('year')]][$row[csf('month')]]['totprojectMin']+=$row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
	  
				}

			}
			foreach($sql_sales as $row)
			{
				$capacity_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['capacityQty']+=$row[csf('sales_target_qty')];
				$capacity_data_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['capacityMin']+=$row[csf('sales_target_mint')];
				$tot_capacity_data_arr[$row[csf('year')]][$row[csf('month')]]['totcapacityQty']+=$row[csf('sales_target_qty')];
				$tot_capacity_data_arr[$row[csf('year')]][$row[csf('month')]]['totcapacityMin']+=$row[csf('sales_target_mint')];
			}
			$total_month=0;
			foreach($month_count_arr as $year_id=>$year_val)
			{
				foreach($year_val as $month_id=>$month_val)
				{
					$total_month=$total_month+1;
				}
			}
	  
		  $table_width=120+($total_month*530)+880;
		  $col_span=2+($total_month*7)+17;
		 ?>
			<table id="table_header" class="rpt_table" width="<? echo $table_width;  ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
      <thead>
           <tr >
                <th width="40" rowspan="3">SL</th>
                <th width="80" rowspan="3">Working Factory Name</th>
                  <?
                    foreach($month_count_arr as $year_id=>$year_val)
                      {

                              foreach($year_val as $month_id=>$month_val)
                               {
                                 ?>
                                   <th width="880" colspan="11"><?   echo $months[$month_val['month']]."   ".$month_val['year']; ?></th>

                                  <?
                               }

                       }
                  ?>
            </tr>
            <tr>
            <?
                  foreach($month_count_arr as $year_id=>$year_val)
                     {
                        
                            foreach($year_val as $month_id=>$month_val)
                            {
                               ?>
							   
                                    <th width="80" >Capacity Qty</th>
                                    <th width="80" >Capacity Minutes</th>
                                    <th width="80" >Confirm Qty/Pcs</th>
                                    <th width="80" >Confirm Minutes</th>
                                    <th width="80" >Projected Qty/Pcs</th>
                                    <th width="80" >Projected Minutes</th>
									<th width="80" >Total Qty/Pcs</th>
									<th width="80" >Total Minutes</th>
									<th width="80" >Qty</th>
                                    <th width="80" >Minutes</th>
									<th width="80" >Percentage</th>
                                <?
                            }
                      

                     }
            ?>
            </tr>
        </thead>
        <tbody>
              <?
			  $i=1;$total_sah=0;
			    foreach($total_buyer_arr as $com_id=>$com_val)
				{
				  /*  foreach($com_val as $buy_id=>$buy_val)
						{ */
					     if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						   ?>

							 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td> <? echo $i; ?>  </td>
                                    <td align="center"> <? echo $companyArr[$com_val]; ?>  </td>
										<?

                                                foreach($month_count_arr as $year_id=>$year_val)
                                                {
                                                    foreach($year_val as $month_id=>$month_val)
                                                    {
                                                       ?>
														<td width="80"  align="right"><? echo  number_format($capacity_data_arr[$com_id][$year_id][$month_id]['capacityQty'],0);  ?></td>
														<td width="80"  align="right"><? echo  number_format($capacity_data_arr[$com_id][$year_id][$month_id]['capacityMin'],0);  ?></td>
														<td width="80"  align="right"><? echo  number_format($confrim_data_arr[$com_id][$year_id][$month_id]['confirmQty'],0);  ?></td>
														<td width="80"  align="right"><? echo  number_format($confrim_data_arr[$com_id][$year_id][$month_id]['confirmMin'],0);  ?></td>
														<td width="80"  align="right"><? echo  number_format($project_data_arr[$com_id][$year_id][$month_id]['projectQty'],0);  ?></td>
														<td width="80"  align="right"><? echo  number_format($project_data_arr[$com_id][$year_id][$month_id]['confirmMin'],0);  ?></td>
														<td width="80"  align="right"><? 
														$tot_qty=$confrim_data_arr[$com_id][$year_id][$month_id]['confirmQty']+$project_data_arr[$com_id][$year_id][$month_id]['projectQty'];
														echo  number_format($tot_qty,0);  ?></td>
														<td width="80"  align="right"><? 
														$tot_min=$confrim_data_arr[$com_id][$year_id][$month_id]['confirmMin']+$project_data_arr[$com_id][$year_id][$month_id]['projectMin'];
														echo  number_format($tot_min,0);  ?></td>
														<td width="80"  align="right"><? echo  number_format(($tot_qty-$capacity_data_arr[$com_id][$year_id][$month_id]['capacityQty']),0);  ?></td>
														<td width="80"  align="right"><? echo  number_format(($tot_min-$capacity_data_arr[$com_id][$year_id][$month_id]['capacityMin']),0);  ?></td>
														<td width="80"  align="right"><? echo  number_format(($tot_qty/$capacity_data_arr[$com_id][$year_id][$month_id]['capacityQty'])*100,0);  ?>%</td>
                                                        <?
                                                    }
                                                }

                                             

                                        ?>   
							 </tr>

							<?
							$i++;
						//}
				}
				?>
        </tbody>
        <tfoot>
            <tr>
                <th width="40" ></th>
                <th width="120" >Total</th>
                <?

							foreach($month_count_arr as $year_id=>$year_val)
							{
								foreach($year_val as $month_id=>$month_val)
								{
								   ?>
										<th width="80" align="right" ><? echo  number_format($tot_capacity_data_arr[$year_id][$month_id]['totcapacityQty'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format($tot_capacity_data_arr[$year_id][$month_id]['totcapacityMin'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format($tot_confrim_data_arr[$year_id][$month_id]['totconfirmQty'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format($tot_confrim_data_arr[$year_id][$month_id]['totconfirmMin'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format($tot_project_data_arr[$year_id][$month_id]['totprojectQty'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format($tot_project_data_arr[$year_id][$month_id]['totprojectMin'],0);  ?></th>
										<th width="80" align="right" ><? echo  number_format(($tot_confrim_data_arr[$year_id][$month_id]['totconfirmQty']+$tot_project_data_arr[$year_id][$month_id]['totprojectQty']),0);  ?></th>
										<th width="80" align="right" ><? echo  number_format(($tot_confrim_data_arr[$year_id][$month_id]['totconfirmMin']+$tot_project_data_arr[$year_id][$month_id]['totprojectMin']),0);  ?></th>
										<th width="80" align="right" ><? echo  number_format((($tot_confrim_data_arr[$year_id][$month_id]['totconfirmQty']+$tot_project_data_arr[$year_id][$month_id]['totprojectQty'])-$tot_capacity_data_arr[$company_id][$year_id][$month_id]['totcapacityQty']),0);  ?></th>
										<th width="80" align="right" ><? echo  number_format((($tot_confrim_data_arr[$year_id][$month_id]['totconfirmMin']+$tot_project_data_arr[$year_id][$month_id]['totprojectMin'])-$tot_capacity_data_arr[$company_id][$year_id][$month_id]['totcapacityQty']),0);  ?></th>
										<th width="80" align="right" ><? //echo  number_format($total_project[$company_id][$year_id][$month_id]['poqty'],0);  ?></th>
									<?
								}
							}

						 
				 ?>
            </tr>
        </tfoot>
    </table>
	<br><br>
		 <?
			if($cbo_date_cat_id==1 || $cbo_date_cat_id==3)
			{
		 ?>
			 <div style="width:2720px;">
				<table width="2720" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100" >LC Company</th>
					<th width="100">LC Location</th>
					<th width="100">Working Company</th>
					<th width="100">Working Location</th>
					<th width="100">Job No</th>
					<th width="50">Job Year</th>
					<th width="120">Buyer</th>
					<th width="100">Brand</th>
					<th width="100">Style Ref</th>
					<th width="100">Order No</th>
					<th width="80">Internal Ref</th>
					
					<th width="80">Season</th>
					<th width="80">Season Year</th>
					<th width="80">Prod Dept.</th>
					<th width="120">Item</th>
					<th width="80">Po Insert Date</th>
					<th width="80">PO Rec. Date</th>
					<th width="80">PHD/PCD Date</th>
					<th width="80">Pub. Shipment Date</th>
					<th width="80">Country Ship Date</th>
					<th width="80">Shipment Date</th>
					
					<th width="50">SMV</th>
					<th width="100">Total Minute</th>
					<th width="100">Order Qty (Pcs)</th>
					<th width="70">Unit Price</th>
					<th width="100">Order Value</th>
					
					<th width="90">Shipping Status</th>
					<th width="90">Order Status</th>
					<th width="100">Team Name</th>
					<th width="100">Dealing Merchant</th>
				
				</tr>
				</thead>
				</table>	
                <div style=" max-height:400px; overflow-y:scroll; width:2740px"  align="left" id="scroll_body">
                    <table width="2720" border="1" class="rpt_table" rules="all" id="table-body">
					<?
					$k=1;$total_po_qty=$total_po_value=$total_po_minute=0;
					foreach($order_wise_array as $po_id=>$row)
					{
					  $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					
						 <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle"  onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>" >                          <td width="30" align="center" bgcolor="<? echo $color; ?>"> <? echo $k; ?> </td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $companyArr[$row[('company_name')]];?></td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $location_arr[$row[('location_name')]];?></td>
                            <td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $companyArr[$row[('style_owner')]];//echo $buyer_short_name_arr[$row[('buyer_name')]];?></td>
                            <td  width="100" align="center" style="color:<? echo $color_font; ?>; word-break: break-all; word-wrap: break-word;"><?  echo $location_arr[$row[('working_location_id')]];//echo $row[('po_number')];?></td>
                             <td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><font style="color:<? //echo $color_font; ?>"><? echo $row[('job_no')];?></font></td>
                            <td width="50"><? echo $row[('year')];?> </td>
                            <td width="120" align="center" style="word-break: break-all; word-wrap: break-word;">
                           
                            <p> <? echo $buyer_arr[$row[('buyer_name')]];// echo rtrim($gmts_item_name,","); ?> </p>
                            </td>
							<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;">
                           
                            <p> <? echo $brand_arr[$row[('brand_id')]];// echo rtrim($gmts_item_name,","); ?> </p>
                            </td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[('style_ref_no')];?></td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[('po_number')];?></td>
                            <td width="80" align="center">
                            <p>
							<?
								echo $row[("grouping")];
							?>
                            </p></td>
                          
                            <td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $season_name_arr[$row[('season_id')]];//change_date_format($row[csf('ship_date')],'dd-mm-yyyy','-');?></td>
							<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[('season_year')];//change_date_format($row[csf('ship_date')],'dd-mm-yyyy','-');?></td>
							<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $product_dept[$row[("product_dept")]];;?></td>
                            <td  width="120" align="center">
                            <?
                           
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row[('gmts_item_id')]);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                           echo rtrim($gmts_item_name,",");
                            ?>
                            </td>

                            <td  width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?  $insert_date=explode(" ",$row[('insert_date')]); echo change_date_format($insert_date[0]); ?></td>
                            <td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
                            <?
							echo change_date_format($row[('po_received_date')]);
                            ?>
                            </td>
							<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?  echo change_date_format($row[('pub_ship')]); ?></td>
							<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?  echo change_date_format($row[('actual_shipment_date')]); ?></td>
							<td width="80" align="center style="word-break: break-all; word-wrap: break-word;""><?  echo change_date_format($row[('country_shipment_date')]); ?></td>
                            <td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?  echo change_date_format($row[('pub_shipment_date')]); ?></td>
                            
                            <td style="word-break: break-all; word-wrap: break-word;" width="50" align="center" title="<? echo $row[("set_smv")];?>">
                            <?
							echo $row[("set_smv")];
                            ?>
                            </td>
                            <td  style="word-break: break-all; word-wrap: break-word;" width="100" align="right"><? echo number_format($row[('po_minute')],2);?></td>
                            <td style="word-break: break-all; word-wrap: break-word;" width="100" align="right">
                            <?
                            echo number_format($row[('po_qty_pcs')],2);
                           
                            ?>
                            </td>
                            <td style="word-break: break-all; word-wrap: break-word;" width="70"  align="right"><? 
							echo number_format($row[('unit_price')],2); 
							?></td>
                            <td style="word-break: break-all; word-wrap: break-word;" width="100" align="right"><? $net_order_value=$row[('po_value')]-$commission;$net_order_value_tot+=$net_order_value; echo number_format ($net_order_value,2); ?></td>
                            <td style="word-break: break-all; word-wrap: break-word;" width="90" align="center"  style="word-break: break-all; word-wrap: break-word;">
                           
                            <?
                            echo $shipment_status[$row[('shiping_status')]];
                            ?>
                            </td>
                            <td  width="90" align="center"  style="word-break: break-all; word-wrap: break-word;">
                            <?
                            echo $order_status[$row[('is_confirmed')]];
                            ?>
                            </td>
							<td  width="100" align="center"  style="word-break: break-all; word-wrap: break-word;">
                            <?
                            echo $company_team_name_arr[$row[('team_leader')]];
                            ?>
                            </td>
                            <td  width="100" align="center"  style="word-break: break-all; word-wrap: break-word;">
                            <?
                            echo $team_leader_arr[$row[('dealing_marchant')]];
                            ?>
                            </td>
                        </tr>
						<?
							$k++;
							$total_po_qty+=$row[('po_qty_pcs')];
							$total_po_value+=$row[('po_value')];
							$total_po_minute+=$row[('po_minute')];
						}
						?>
					</table>
					</div>
					<table width="2720" rules="all" cellpadding="0" cellspacing="0"  border="1" class="tbl_bottom" >
					<thead>
						<tr>
						<td width="30">&nbsp;</td>
					<td width="100" >&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="80">&nbsp;</td>
					
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					
					<td width="50">Total</td>
					<td width="100" ><? echo $total_po_minute; ?></td>
					<td width="100"  ><? echo $total_po_qty; ?></td>
					<td width="70">&nbsp;</td>
					<td width="100" ><? echo $total_po_value; ?></td>
					
					<td width="90">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
						</tr>
					</thead>
				</table>
					
			</div>
			<?
				//End Po Wise
			}
			else
			{
			?>
			<!--Start Country Wise-->
			<div style="width:2300px;">
			 
				<table width="2300" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100" >LC Company</th>
					<th width="100">LC Location</th>
					<th  width="100">Working Company</th>
					<th  width="100">Working Location</th>
					<th  width="100">Job No</th>
					<th width="50">Job Year</th>
					<th width="120">Buyer</th>
					<th width="100">Style Ref</th>
					<th width="100">Order No</th>
					<th width="80">Internal Ref</th>
					<th width="100">Country</th>
					<th width="80">Season</th>
					<th  width="120">Item</th>
					<th  width="80">Po Insert Date</th>
					<th width="80">PO Rec. Date</th>
					<th width="80">Shipment Date</th>
					<th width="80">Country Ship Date</th>
					<th width="50">SMV</th>
					<th  width="100">Total Minute</th>
					<th width="100">Order Qty (Pcs)</th>
					<th width="70">Unit Price</th>
					<th width="100">Order Value</th>
					<th width="90">Order Status</th>
					<th  width="90">Shipping Status</th>
					<th  width="">Dealing Merchant</th>
				
				</tr>
				</thead>
				</table>	
                <div style=" max-height:400px; overflow-y:scroll; width:2320px"  align="left" id="scroll_body">
                    <table width="2300" border="1" class="rpt_table" rules="all" id="table-body">
					<?
					$k=1;$total_country_qty=$total_country_value=$total_country_minute=0;
					foreach($country_wise_array as $po_id=>$po_data)
					{
					foreach($po_data as $country_id=>$row)
					{
					  $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					
						 <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle"  onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>" >                          <td width="30" align="center" bgcolor="<? echo $color; ?>"> <? echo $k; ?> </td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $companyArr[$row[('company_name')]];?></td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $location_arr[$row[('location_name')]];?></td>
                            <td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $companyArr[$row[('style_owner')]];//echo $buyer_short_name_arr[$row[('buyer_name')]];?></td>
                            <td  width="100" align="center" style="color:<? echo $color_font; ?>; word-break: break-all; word-wrap: break-word;"><?  echo $location_arr[$row[('working_location_id')]];//echo $row[('po_number')];?></td>
                             <td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><font style="color:<? //echo $color_font; ?>"><? echo $row[('job_no')];?></font></td>
                            <td width="50"><? echo $row[('year')];?> </td>
                            <td width="120" align="center" style="word-break: break-all; word-wrap: break-word;">
                           
                            <p> <? echo $buyer_arr[$row[('buyer_name')]];// echo rtrim($gmts_item_name,","); ?> </p>
                            </td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[('style_ref_no')];?></td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[('po_number')];?></td>
                            <td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
                            <p>
							<?
								echo $row[("grouping")];
							?>
                            </p></td>
                            <td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><p><? echo $country_name_arr[$country_id];?></p></td>
                            <td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $season_name_arr[$row[('season_id')]];//change_date_format($row[csf('ship_date')],'dd-mm-yyyy','-');?></td>
                            <td  width="120" align="center">
                            <?
                           
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row[('gmts_item_id')]);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                           echo rtrim($gmts_item_name,",");
                            ?>
                            </td>

                            <td  width="80" align="center"><?  $insert_date=explode(" ",$row[('insert_date')]); echo change_date_format($insert_date[0]); ?></td>
                            <td width="80" align="center">
                            <?
							echo change_date_format($row[('po_received_date')]);
                            ?>
                            </td>
                            <td width="80" align="center"><?  echo change_date_format($row[('pub_shipment_date')]); ?></td>
                            <td width="80" align="center">
                            <?
                         	change_date_format($row[('country_date')]); 
                            ?>
                            </td>
                            <td width="50" align="center" title="<? echo $row[("set_smv")];?>">
                            <?
							echo $row[("set_smv")];
                            ?>
                            </td>
                            <td  width="100" align="right"><? echo number_format($row[('po_minute')],2);?></td>
                            <td width="100" align="right">
                            <?
                            echo number_format($row[('po_qty_pcs')],2);
                           
                            ?>
                            </td>
                            <td width="70"  align="right"><? 
							echo number_format($row[('unit_price')],2); 
							?></td>
                            <td width="100" align="right"><? $net_order_value=$row[('po_value')]-$commission;$net_order_value_tot+=$net_order_value; echo number_format ($net_order_value,2); ?></td>
                            <td width="90" align="center"  style="word-break: break-all; word-wrap: break-word;">
                           
                            <?
                            echo $shipment_status[$row[('shiping_status')]];
                            ?>
                            </td>
                            <td  width="90" align="center"  style="word-break: break-all; word-wrap: break-word;">
                            <?
                            echo $order_status[$row[('is_confirmed')]];
                            ?>
                            </td>
                            <td  width="" align="center"  style="word-break: break-all; word-wrap: break-word;">
                            <?
                            echo $team_leader_arr[$row[('dealing_marchant')]];
                            ?>
                            </td>
                        </tr>
						<?
							$k++;
							$total_country_qty+=$row[('po_qty_pcs')];
							$total_country_value+=$row[('po_value')];
							$total_country_minute+=$row[('po_minute')];
							}
						}
						?>
					</table>
					</div>
					<table width="2300" rules="all" cellpadding="0" cellspacing="0"  border="1" class="tbl_bottom" >
					<tr>
						<td width="30">&nbsp;</td>
						<td width="100" >&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td  width="100">&nbsp;</td>
						<td  width="100">&nbsp;</td>
						<td  width="100">&nbsp;</td>
						<td width="50">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td  width="120">&nbsp;</td>
						<td  width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="50">&nbsp;</td>
						<td  width="100"> <? echo $total_country_minute; ?></td>
						<td width="100" > <? echo $total_country_qty; ?></td>
						<td width="70">&nbsp;</td>
						<td width="100"> <? echo $total_country_value; ?></td>
						<td width="90">&nbsp;</td>
						<td  width="90">&nbsp;</td>
						<td  width="">&nbsp;</td>
					</tr>
				</table>
					
			</div>
			<?
			}
			?>
			</div>
	<?
		$user_id=$_SESSION['logic_erp']['user_id'];
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename****$report_type"; 
	exit();	
}


?>