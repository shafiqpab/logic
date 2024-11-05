<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

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
   // cbo_company_id     ==> working company  (a.working_company_id)
   // cbo_location_id     ==> working location  a.(working_location_id)
   // cbo_lc_company_id     ==> LC company  (a.company_name)
   // cbo_lc_location_id   ==>  LC location (a.location_name)
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_id = str_replace("'","",$cbo_company_id);
	$w_location_id = str_replace("'","",$cbo_location_id);

	$lc_company_id = str_replace("'","",$cbo_lc_company_id);
	$lc_location_id = str_replace("'","",$cbo_lc_location_id);

	$cbo_date_cat_id = str_replace("'","",$cbo_date_cat_id);
	$date_from = str_replace("'","",$txt_date_from);
	$date_to = str_replace("'","",$txt_date_to);
	
	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$brand_arr = return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_team_name_arr = return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr = return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$season_name_arr = return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr = return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$country_name_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$date_category_arr = array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date',4=>'PHD/PCD Ship Date',5=>'Plan Date');

	 
	
	if($w_location_id){$wc_locatin_cond=" and a.location_id in($w_location_id)";}else{$wc_locatin_cond="";}
	if($company_id){$company_con=" and a.comapny_id in($company_id)";}
	else{$company_con="";}
 
	//echo $wc_locatin_cond;die;

	$sql="SELECT a.comapny_id, a.location_id, a.year, b.capacity_min, b.capacity_pcs, b.date_calc, b.month_id
	FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b 
	WHERE a.id=b.mst_id  $company_con $wc_locatin_cond AND a.capacity_source=1 AND b.date_calc BETWEEN '$date_from' AND '$date_to' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 
	GROUP BY a.comapny_id, a.location_id, a.year, b.capacity_min, b.capacity_pcs, b.date_calc, b.month_id";


    //  echo $sql;die;
  
	$sql_data_smv=sql_select($sql);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{
		//$key=$row[csf("comapny_id")].'_'.$row[csf("location_id")];
		$key=$row[csf("comapny_id")];
		$capacity_arr[$key][$row[csf("year")]][$row[csf("month_id")]]['capacity_pcs']+=$row[csf("capacity_pcs")];
		$capacity_arr[$key][$row[csf("year")]][$row[csf("month_id")]]['capacity_min']+=$row[csf("capacity_min")];
	}
	unset($sql_data_smv);

	//echo "<pre>";
	//print_r($capacity_arr);
	//die;
	
	
	if($company_id){$wc_company_con=" and a.style_owner in($company_id)";}else{$wc_company_con="";}
	if($company_id){$wc_company_con2=" and a.company_id in($company_id)";}else{$wc_company_con2="";}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

 
	if($company_id){$working_company_cod=" and a.working_company_id in($company_id)";}else{$working_company_cod="";}
	if($w_location_id){$working_location_cod=" and a.working_location_id in($w_location_id)";}else{$working_location_cod="";}
	if($lc_company_id){$lc_company_con=" and a.company_name in($lc_company_id)";}else{$lc_company_con="";}
	if($lc_location_id){$lc_locatin_cond=" and a.location_name in($lc_location_id)";}else{$lc_locatin_cond="";}

	if($cbo_date_cat_id==1) // Pub Ship Date
	{
		$status_date = " AND b.pub_shipment_date between '$date_from' and '$date_to'";
	}
	else if($cbo_date_cat_id==2) // Country Ship Date
	{
		$status_date = "AND c.country_ship_date between '$date_from' and '$date_to'";
	}
	else if($cbo_date_cat_id==3) // Actual Ship Date
	{
		$status_date = "AND b.shipment_date between '$date_from' and '$date_to'";
	}
	else if($cbo_date_cat_id==4) // PHD/PCD Ship Date
	{
		$status_date = "AND b.pack_handover_date between '$date_from' and '$date_to'";
	}

	 

	//echo $status_date ;die;

	$sql="SELECT  a.order_uom, d.GMTS_ITEM_ID, d.SET_ITEM_RATIO, d.SMV_PCS, d.SMV_SET, $year_field, a.company_name, a.style_owner, a.working_company_id, a.working_location_id, a.dealing_marchant, a.location_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.season_buyer_wise as season_id, a.set_smv, a.set_break_down, a.buyer_name, a.total_set_qnty, b.pub_shipment_date, b.pub_shipment_date as pub_ship, b.pack_handover_date, b.shipment_date as actual_shipment_date, b.po_received_date, b.id as po_id, b.po_number, b.shiping_status, b.is_confirmed, b.shipment_date, b.unit_price, b.insert_date, b.grouping, a.team_leader, a.product_dept, a.season_year, a.brand_id, c.country_ship_date as country_shipment_date,
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
		FROM wo_po_details_master a, wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS d, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst and a.job_no=d.job_no and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $working_company_cod $working_location_cod  $lc_company_con $lc_locatin_cond $status_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

    // $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond
 
	// echo $sql;die;

	$sql_data=sql_select($sql);
	//echo "<pre>";
	//print_r($sql_data);die;
	foreach( $sql_data as $row)
	{
		$rate_in_pcs=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		
		if($cbo_date_cat_id==1 || $cbo_date_cat_id==2 || $cbo_date_cat_id==3 || $cbo_date_cat_id==4 )
		{
			$item_id=$row['GMTS_ITEM_ID'];
			$set=$row['SET_ITEM_RATIO'];
			$smv=$row['SMV_PCS'];
			$confirm_qty=$row[csf('confirm_qty')]*$set;
			$project_qty=$row[csf('projected_qty')]*$set;

			// total_set_qnty

			// 

			
			$key=$row[csf('style_owner')].'_'.$row[csf('working_location_id')].'_'.$row[csf('company_name')].'_'.$row[csf('buyer_name')];
			$order_data_array[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')]][$row[csf('buyer_name')]]=$key;
			$order_wise_array[$row[csf('po_id')]]['style_owner']=$row[csf('style_owner')];
			$order_wise_array[$row[csf('po_id')]]['working_location_id']=$row[csf('working_location_id')];
			$order_wise_array[$row[csf('po_id')]]['working_company_id']=$row[csf('working_company_id')];
			$order_wise_array[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
			$order_wise_array[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
			
			$order_wise_array[$row[csf('po_id')]]['location_name']=$row[csf('location_name')];
			$order_wise_array[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_wise_array[$row[csf('po_id')]]['year']=$row[csf('year')];
			// $order_wise_array[$row[csf('po_id')]]['po_qty_pcs']+=$confirm_qty+$project_qty;
			$order_wise_array[$row[csf('po_id')]]['po_value']+=($confirm_qty+$project_qty)*$rate_in_pcs;
			$order_wise_array[$row[csf('po_id')]]['po_minute']+=($confirm_qty+$project_qty)*$smv;


			$order_wise_array[$row[csf('po_id')]]['set_item_ratio']+= $row['SET_ITEM_RATIO'];

			if($row[csf('is_confirmed')] == 1){
				$order_wise_array[$row[csf('po_id')]]['po_qty_pcs'] = $row[csf('confirm_qty')]*$row[csf('total_set_qnty')] ;
				$order_wise_array[$row[csf('po_id')]]['po_qty_pcs_mims'] = $row[csf('confirm_qty')] ;
			}
			else{
				$order_wise_array[$row[csf('po_id')]]['po_qty_pcs'] = $row[csf('projected_qty')]*$row[csf('total_set_qnty')];
				$order_wise_array[$row[csf('po_id')]]['po_qty_pcs_mims'] = $row[csf('confirm_qty')] ;
			}
			
			$order_wise_array[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
			$order_wise_array[$row[csf('po_id')]]['season_id']=$row[csf('season_id')];
			$order_wise_array[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$order_wise_array[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$order_wise_array[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
			$order_wise_array[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			$order_wise_array[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$order_wise_array[$row[csf('po_id')]]['pack_handover_date']=$row[csf('pack_handover_date')];
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
			
			$projected_qty_array[$key] += $project_qty;
			$confirm_qty_array[$key] += $confirm_qty;
			$projected_value_array[$key] += $rate_in_pcs*$project_qty;
			$confirm_value_array[$key] += $rate_in_pcs*$confirm_qty;
			$confirm_mint_array[$key] += $smv*$confirm_qty;
			$projected_mint_array[$key] += $smv*$project_qty;

			$wc_location_total_confirm_min_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($smv*$confirm_qty)+($smv*$project_qty);
			$wc_location_total_confirm_qty_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]]+=($confirm_qty+$project_qty);
		}
		else
		{
			$confirm_qty = $row[csf('confirm_qty')];
			$project_qty = $row[csf('projected_qty')];
			$confirm_value = $row[csf('confirm_value')];
			$project_value = $row[csf('projected_value')];
			$po_qty_set_smv = $row[csf('set_smv')]*$row[csf('po_quantity')];
			$smv = $po_qty_set_smv/$row[csf('po_quantity')];
			
			$key=$row[csf('style_owner')].'_'.$row[csf('working_location_id')].'_'.$row[csf('company_name')].'_'.$row[csf('buyer_name')];

			$order_data_array[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')]][$row[csf('buyer_name')]] = $key;
			$for_row_sapn[$row[csf('style_owner')]][$row[csf('working_location_id')]][$row[csf('company_name')].$row[csf('buyer_name')]] = 1;
			
			$projected_qty_array[$key] += $project_qty;
			$confirm_qty_array[$key] += $confirm_qty;
			$projected_value_array[$key] += $rate_in_pcs*$project_qty;
			$confirm_value_array[$key] += $rate_in_pcs*$confirm_qty;
			
			$confirm_mint_array[$key] += $smv*$confirm_qty;
			$projected_mint_array[$key] += $smv*$project_qty;
			
			$wc_location_total_confirm_min_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]] += ($smv*$confirm_qty)+($smv*$project_qty);
			$wc_location_total_confirm_qty_arr[$row[csf('style_owner')].'_'.$row[csf('working_location_id')]] += ($confirm_qty+$project_qty);
			//Details Part
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['style_owner'] = $row[csf('style_owner')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['working_location_id'] = $row[csf('working_location_id')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['company_name'] = $row[csf('company_name')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['location_name'] = $row[csf('location_name')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['buyer_name'] = $row[csf('buyer_name')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['year'] = $row[csf('year')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_qty_pcs'] += $confirm_qty+$project_qty;
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_value'] += ($confirm_qty+$project_qty)*$rate_in_pcs;
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_minute'] += ($confirm_qty+$project_qty)*$smv;
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['unit_price'] = $row[csf('unit_price')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['season_id'] = $row[csf('season_id')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['gmts_item_id'] = $row[csf('gmts_item_id')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['job_no'] = $row[csf('job_no')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_number'] = $row[csf('po_number')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['country_date'] = $row[csf('pub_shipment_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['po_received_date'] = $row[csf('po_received_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['shipment_date'] = $row[csf('shipment_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['insert_date'] = $row[csf('insert_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['grouping'] = $row[csf('grouping')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['set_smv'] = $row[csf('set_smv')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['is_confirmed'] = $row[csf('is_confirmed')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['shiping_status'] = $row[csf('shiping_status')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['team_leader'] = $row[csf('team_leader')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['dealing_marchant'] = $row[csf('dealing_marchant')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['pub_ship'] = $row[csf('pub_ship')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['actual_shipment_date'] = $row[csf('actual_shipment_date')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['country_shipment_date'] = $row[csf('country_shipment_date')];	
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['product_dept'] = $row[csf('product_dept')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['season_year'] = $row[csf('season_year')];
			$country_wise_array[$row[csf('po_id')]][$row[csf('country_id')]]['brand_id'] = $row[csf('brand_id')];
		}
		/* echo '<pre>';
		print_r($country_wise_array);//die; */
	}
	ob_start(); 

	//echo "<pre>";
	//print_r($capacity_arr);die;
 
	if($cbo_date_cat_id==1)//Pub Ship Date
	{
		$shipment_year="  extract(year from b.pub_shipment_date) as year";
		$shipment_month="  extract(month from b.pub_shipment_date) as month";
		$shipment_year2="  extract(year from b.sales_target_date) as year";
		$shipment_month2="  extract(month from b.sales_target_date) as month";	
	}
 
	else if($cbo_date_cat_id==2)
	{
		$shipment_year="  extract(year from c.country_ship_date) as year";
		$shipment_month="  extract(month from c.country_ship_date) as month";
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
	else if($cbo_date_cat_id==4)
	{
		$shipment_year="  extract(year from b.pack_handover_date) as year";
		$shipment_month="  extract(month from b.pack_handover_date) as month";
		$shipment_year2="  extract(year from b.sales_target_date) as year";
		$shipment_month2="  extract(month from b.sales_target_date) as month";
	}
	
	//echo $cbo_date_cat_id;die;

	if($cbo_date_cat_id==1)
	{ 
		//b.po_quantity*a.total_set_qnty
		$sql="SELECT a.working_company_id, a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $working_company_cod $working_location_cod $lc_company_con AND b.pub_shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.working_company_id, a.style_owner, b.is_confirmed, b.pub_shipment_date ORDER BY month";
		
 
		//echo $sql;die;

		$sql_sales=sql_select("SELECT a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
	}
	else if($cbo_date_cat_id==3)
	{
	    $sql="SELECT  a.working_company_id, a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id $working_company_cod  AND b.shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.working_company_id, a.style_owner,  b.is_confirmed,b.shipment_date ORDER BY month";
		// $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond
		$sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
	}
	// Country Ship Date
	else if($cbo_date_cat_id==2)
	{ 
		 
		$sql="SELECT  a.working_company_id, a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id $working_company_cod  AND c.country_ship_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.working_company_id, a.style_owner, b.is_confirmed, c.country_ship_date ORDER BY month";

		$sql_sales=sql_select("SELECT a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
	}

	// PHD/PCD Ship Date
	else if($cbo_date_cat_id==4)
	{
		$sql="SELECT  a.working_company_id, a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year, $shipment_month,  sum(c.order_total) as po_total_price FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $working_company_cod $lc_company_con AND b.pack_handover_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.working_company_id, a.style_owner, b.is_confirmed, b.pack_handover_date ORDER BY month";
		
		$sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2, $shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
	}


	// else
	// {
	// 	$data_array=sql_select("select  a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND c.country_ship_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.style_owner,  b.is_confirmed,c.country_ship_date ");
		
	// 	$sql_sales=sql_select("select a.company_id as company_name, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value,$shipment_year2,$shipment_month2 from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 $wc_company_con2 and b.sales_target_date between '$date_from' and '$date_to'");
	// }

	//$dd ="SELECT  a.working_company_id, a.style_owner as company_name,avg(a.set_smv) as set_smv,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs,sum(distinct b.po_quantity) as po_quantity, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id $working_company_cod  AND b.shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by  a.working_company_id, a.style_owner,  b.is_confirmed,b.shipment_date ";

    //echo $dd;die;

	// echo "<pre>";
	//print_r($data_array);die;

	//echo $cbo_date_cat_id;die;

	$project_data_arr=array();
	$confrim_data_arr=array();
	$month_count_arr=array();
	$total_buyer_arr=array();
	$total_qty_arr=array();
	$total_confrim=array();
	$total_project=array();
	$grand_confirm_buyer=array();
	$grand_project_buyer=array();

	// echo $data_array;die;
	$data_array = sql_select($sql);

	foreach($data_array as $row)
	{
		$total_buyer_arr[$row[csf('working_company_id')]] = $row[csf('working_company_id')];
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['year'] = $row[csf('year')];
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['month'] = $row[csf('month')];

		if($row[csf('is_confirmed')]==1)
		{
			$confrim_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['confirmQty'] += $row[csf('po_quantity_pcs')];
			$confrim_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['confirmMin'] += $row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
			
			$tot_confrim_data_arr[$row[csf('year')]][$row[csf('month')]]['totconfirmQty'] += $row[csf('po_quantity_pcs')];
			$tot_confrim_data_arr[$row[csf('year')]][$row[csf('month')]]['totconfirmMin'] += $row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
			
		}
		if($row[csf('is_confirmed')]==2)
		{
			$project_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['projectQty'] += $row[csf('po_quantity_pcs')];
			$project_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['projectMin'] += $row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
			$tot_project_data_arr[$row[csf('year')]][$row[csf('month')]]['totprojectQty'] += $row[csf('po_quantity_pcs')];
			$tot_project_data_arr[$row[csf('year')]][$row[csf('month')]]['totprojectMin'] += $row[csf('po_quantity_pcs')]*$row[csf('set_smv')];
		}
	}
	foreach($sql_sales as $row)
	{
		$capacity_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['capacityQty']+=$row[csf('sales_target_qty')];
		$capacity_data_arr[$row[csf('working_company_id')]][$row[csf('year')]][$row[csf('month')]]['capacityMin']+=$row[csf('sales_target_mint')];
		$tot_capacity_data_arr[$row[csf('year')]][$row[csf('month')]]['totcapacityQty']+=$row[csf('sales_target_qty')];
		$tot_capacity_data_arr[$row[csf('year')]][$row[csf('month')]]['totcapacityMin']+=$row[csf('sales_target_mint')];
	}
	$total_month=0;
	// print_r($month_count_arr);die;
	foreach($month_count_arr as $year_id=>$year_val)
	{
		foreach($year_val as $month_id=>$month_val)
		{
			$total_month = $total_month+1;
		}
	}

	//ksort($month_count_arr);

	//echo "<pre>";
	//print_r($order_wise_array);die;
 
	ob_start(); 
	?>
    <div>
        <table width="300" border="0" cellpadding="2" cellspacing="0"> 
            <thead>
			    <tr class="form_caption">
                    <td colspan="25" align="center" style="font-size:16px; font-weight:bold">Working Company Wise Capacity VS Allocation Report</td> 
                </tr>
                <tr class="form_caption">
                    <td colspan="25" align="center"><?= $date_category_arr[$cbo_date_cat_id].' ('. change_date_format($date_from).' To '.change_date_format($date_to); ?>)</td> 
                </tr>
            </thead>
        </table>
		<table id="table_header" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="40" rowspan="3">SL</th>
					<th width="80" rowspan="3">Working Factory Name</th>
					<?
                    foreach($month_count_arr as $year_id=>$year_val)
                    {
						foreach($year_val as $month_id=>$month_val)
						{
						?>
							<th width="880" colspan="11"><?= $months[$month_val['month']]."   ".$month_val['year']; ?></th>
						<?
						}
                    }
                    ?>
					<!-- <th width="880" colspan="11">October 2023</th> -->
				</tr>
				
				<tr> 
				<?
                    foreach($month_count_arr as $year_id=>$year_val)
                    {
						foreach($year_val as $month_id=>$month_val)
						{
						?>
						<th colspan="2" width="80">Capacity</th>
						<th colspan="6" width="80">Order Booking</th> 
						<th colspan="2" width="80">Variance</th>
						<th rowspan="2" width="80">Percentage (%)</th>
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
					<th width="80">Qnty</th> 
					<th width="80">Minutes</th>

					<th width="80">Confirm Qty/Pcs</th>
					<th width="80">Confirm Minutes</th>
					<th width="80">Projected Qty/Pcs</th>
					<th width="80">Projected Minutes</th>
					<th width="80">Total Qty/Pcs</th>
					<th width="80">Total Minutes</th>

					<th width="80">Qnty</th> 
					<th width="80">Minutes</th>
					<?
						}
                    }
                    ?>
				</tr>
				
			</thead>
			<tbody> 
			    <?
			    $i=1;
				$total_qnty = array();$total_mins = array();
				$confirm_qty_pcs = array();$confirm_qty_mins = array();
				$projected_qty_pcs = array(); $projected_qty_mins = array();
				$total_qty_pcs = array(); $total_qty_mins = array();
				$variance_qnty = array(); $variance_mins = array();
				$total_percents = array();
			    foreach($total_buyer_arr as $com_id=>$com_val)
				{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd1','<? echo $bgcolor; ?>')" id="tr_2nd1">
				 
					<td><?= $i; ?></td>
					<td align="center"><?= $companyArr[$com_id]; ?></td>
					<?
					
                    foreach($month_count_arr as $year_id=>$year_val)
                    {
						foreach($year_val as $month_id=>$month_val)
						{
							$year = $month_val['year'];
							$month = $month_val['month']; 
						?>
							<td width="80" align="right"><?= number_format($capacity_arr[$com_id][$year][$month]['capacity_pcs'],0);?></td>
							<td width="80" align="right"><?= number_format($capacity_arr[$com_id][$year][$month]['capacity_min'],0);?></td>

							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmQty'],0);?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmMin'],0);?></td>
							<td width="80" align="right"><?= number_format($project_data_arr[$com_id][$year][$month]['projectQty'],0);?></td>
							<td width="80" align="right"><?= number_format($project_data_arr[$com_id][$year][$month]['projectMin'],0);?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'], 0) ?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'], 0) ?></td>
					

							<td width="80" align="right"><?= number_format(($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'])-$capacity_arr[$com_id][$year][$month]['capacity_pcs']) ?></td>
							<td width="80" align="right"><?= number_format(($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'])-$capacity_arr[$com_id][$year][$month]['capacity_min'],0) ?></td>

							<?php
							 
							$plus = $confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'];
							$division = $plus/$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
							$percent = $division*100;

						    if(is_infinite($percent)){
								$total_percent = 0;
							}
							else{
								$total_percent = $percent;
							}

							?>
							<td width="80" align="right"><?= number_format($total_percent, 0); ?>%</td>

							<?
						    $total_qnty[$year][$month]['total_qnty'] +=$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
							$total_mins[$year][$month]['total_mins'] +=$capacity_arr[$com_id][$year][$month]['capacity_min'];

							$confirm_qty_pcs[$year][$month]['confirm_qty_pcs'] +=$confrim_data_arr[$com_id][$year][$month]['confirmQty'];
							$confirm_qty_mins[$year][$month]['confirm_qty_mins'] +=$confrim_data_arr[$com_id][$year][$month]['confirmMin']; 
							$projected_qty_pcs[$year][$month]['projected_qty_pcs']  +=$project_data_arr[$com_id][$year][$month]['projectQty'];
							$projected_qty_mins[$year][$month]['projected_qty_mins'] +=$project_data_arr[$com_id][$year][$month]['projectMin'];

							$total_qty_pcs[$year][$month]['total_qty_pcs'] += $confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'];
							$total_qty_mins[$year][$month]['total_qty_mins'] += $confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'];

							$variance_qnty[$year][$month]['variance_qnty'] += ($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'])-$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
							$variance_mins[$year][$month]['variance_mins'] += ($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'])-$capacity_arr[$com_id][$year][$month]['capacity_min'];

							$total_percents[$year][$month]['total_percents'] += $total_percent;
						}
                    }
                    ?>
				</tr>
				<?php
				++$i;
				}
				?>
				 
			</tbody>
			<tfoot>  
				<tr>
					<th width="40"></th>
					<th width="120">Grand Total</th>
					<?
					//echo "<pre>";
					//print_r($total_qnty);die;
					foreach($month_count_arr as $year_id=>$year_val)
					{
						foreach($year_val as $month_id=>$month_val)
						{
							$year = $month_val['year'];
							$month = $month_val['month']; 
						?>
						<th width="80" align="right"><?=number_format($total_qnty[$year][$month]['total_qnty'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_mins[$year][$month]['total_mins'], 0);?></th>
						
						<th width="80" align="right"><?=number_format($confirm_qty_pcs[$year][$month]['confirm_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($confirm_qty_mins[$year][$month]['confirm_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($projected_qty_pcs[$year][$month]['projected_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($projected_qty_mins[$year][$month]['projected_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_qty_pcs[$year][$month]['total_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_qty_mins[$year][$month]['total_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($variance_qnty[$year][$month]['variance_qnty'], 0);?></th>
						<th width="80" align="right"><?=number_format($variance_mins[$year][$month]['variance_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_percents[$year][$month]['total_percents'], 0);?>%</th>
					 
						<?
						}
					}
					?>
				</tr>
			</tfoot>
		</table>
	</div>

	<br>
	<br>
	<br>
	<div style="width:2720px;">
		<table width="2720" id="table_header_1" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">LC Company</th>
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
					<th width="100">Order Qty (Pcs)</th>
					<th width="100" title="Order Qty (Pcs)*SMV">Total Minute</th>
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
					$k=1;$total_po_qty=$total_po_value=$total_po_minute=0;$total_order_values=0;
					foreach($order_wise_array as $po_id=>$row)
					{
					  $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<tr bgcolor="<?= $bgcolor; ?>" style="vertical-align:middle"  onclick="change_color('tr_<?= $k; ?>','<?= $bgcolor;?>')" id="tr_<?= $k; ?>">
						<td width="30" align="center" bgcolor="<?= $color; ?>"><?= $k; ?></td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $companyArr[$row[('company_name')]];?></td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $location_arr[$row[('location_name')]];?></td>
						<td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $companyArr[$row[('working_company_id')]];?></td>
						<td  width="100" align="center" style="color:<?= $color_font; ?>; word-break: break-all; word-wrap: break-word;"><?= $location_arr[$row[('working_location_id')]];?></td>
						<td  width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><font><?=$row[('job_no')];?></font></td>
						<td width="50"><?= $row[('year')];?> </td>
						<td width="120" align="center" style="word-break: break-all; word-wrap: break-word;">
						<p><?= $buyer_arr[$row[('buyer_name')]];?> </p>
						</td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;">
						<p><?= $brand_arr[$row[('brand_id')]]; ?> </p>
						</td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $row[('style_ref_no')];?></td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $row[('po_number')];?></td>
						<td width="80" align="center">
							<p><?=$row[("grouping")];?></p>
						</td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $season_name_arr[$row[('season_id')]];?></td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $row[('season_year')];?></td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?= $product_dept[$row[("product_dept")]];;?></td>
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
						<td  width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?  $insert_date=explode(" ",$row[('insert_date')]); echo change_date_format($insert_date[0]); ?>
						</td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?= change_date_format($row[('po_received_date')]);?>
						</td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;"><?= change_date_format($row[('pack_handover_date')]); ?></td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?= change_date_format($row[('pub_shipment_date')]); ?>
						</td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?= change_date_format($row[('country_shipment_date')]); ?>
						</td>
						<td width="80" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?= change_date_format($row[('actual_shipment_date')]); ?>
						</td>
						<td style="word-break: break-all; word-wrap: break-word;" width="50" align="center" title="<?= $row[("set_smv")];?>"><?= $row[("set_smv")];?></td>
						<td style="word-break: break-all; word-wrap: break-word;" width="100" align="right"><?= number_format($row[('po_qty_pcs')],2);?></td>
						<td style="word-break: break-all; word-wrap: break-word;" width="100" align="right"><?= number_format($row[('po_qty_pcs_mims')]*$row[("set_smv")],2);?></td>
						<td style="word-break: break-all; word-wrap: break-word;" width="70" align="right">
							<?= number_format($row[('unit_price')],2);?>
						</td>
						<td style="word-break: break-all; word-wrap: break-word;" width="100" align="right">

						<?php
						    $type = $row[('order_uom')];
						    if($type==58){
							    $total_order_value = ($row[('unit_price')]/2)*$row[('po_qty_pcs')];
						    }
							else{
								$total_order_value = $row[('po_qty_pcs')]*$row[('unit_price')];
							}
							  
							echo number_format($total_order_value,2);
						?>

							<!-- < ?= number_format(($row[('unit_price')]/2)*$row[('po_qty_pcs')],2);?> -->
						</td>
						<td style="word-break: break-all; word-wrap: break-word;" width="90" align="center"  style="word-break: break-all; word-wrap: break-word;">
							<?= $shipment_status[$row[('shiping_status')]];?>
						</td>
						<td width="90" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?=$order_status[$row[('is_confirmed')]];?>
						</td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?=$company_team_name_arr[$row[('team_leader')]];?>
						</td>
						<td width="100" align="center" style="word-break: break-all; word-wrap: break-word;">
							<?=$team_leader_arr[$row[('dealing_marchant')]];?>
						</td>
					</tr>
					<?
					$k++;
					$total_po_qty+=$row[('po_qty_pcs')];
					$total_po_qty_mis+=$row[('po_qty_pcs_mims')]*$row[("set_smv")];

					$total_order_values+=$total_order_value;
					

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
					<td width="100"><?= number_format($total_po_qty,2);?></td>
					<td width="100"><?= number_format($total_po_qty_mis,2);?></td>
					<td width="70">&nbsp;</td>
					<td width="100"><?= number_format($total_order_values, 2);?></td>
					<td width="90">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
				</tr>
			</thead>
		</table>		
	</div>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****$report_type"; 
	exit();
}

if($action = 'report_generate_plan')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_id = str_replace("'","",$cbo_company_id);
	$w_location_id = str_replace("'","",$cbo_location_id);

	$lc_company_id = str_replace("'","",$cbo_lc_company_id);
	$lc_location_id = str_replace("'","",$cbo_lc_location_id);

	$cbo_date_cat_id = str_replace("'","",$cbo_date_cat_id);
	$date_from = str_replace("'","",$txt_date_from);
	$date_to = str_replace("'","",$txt_date_to);
	
	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$brand_arr = return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$company_team_name_arr = return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr = return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$season_name_arr = return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$team_leader_arr = return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	$country_name_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$date_category_arr = array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date',4=>'PHD/PCD Ship Date',5=>'Plan Date');

	if($date_from!="" && $date_to!=""){
		$start_date=change_date_format($date_from,"","",1);
		$end_date=change_date_format($date_to,"","",1);
		$where_con=" and d.plan_date between '$start_date' and '$end_date'"; 
	} 


	if($location_id){$wc_locatin_cond=" and c.location_id in($location_id)";}else{$wc_locatin_cond="";}
	
	if($company_id){$company_con=" and a.comapny_id in($company_id)";}
	else{$company_con="";}
	// if($company_id){$working_company_cod=" and a.working_company_id in($company_id)";}else{$working_company_cod="";}
 
	$sql="SELECT a.comapny_id, a.location_id, a.year, b.capacity_min, b.capacity_pcs, b.date_calc, b.month_id
	FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b 
	WHERE a.id=b.mst_id  $company_con $wc_locatin_cond AND a.capacity_source=1 AND b.date_calc BETWEEN '$date_from' AND '$date_to' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 
	GROUP BY a.comapny_id, a.location_id, a.year, b.capacity_min, b.capacity_pcs, b.date_calc, b.month_id";
 
   // echo $sql;die;

	$sql_data_smv=sql_select($sql);
	$capacity_arr=array();
	foreach( $sql_data_smv as $row)
	{ 
		$key=$row[csf("comapny_id")];
		$capacity_arr[$key][$row[csf("year")]][$row[csf("month_id")]]['capacity_pcs']+=$row[csf("capacity_pcs")];
		$capacity_arr[$key][$row[csf("year")]][$row[csf("month_id")]]['capacity_min']+=$row[csf("capacity_min")];
	}
	unset($sql_data_smv);


	if($company_id){$company_con=" and a.working_company_id in($company_id)";}else{$company_con="";}

	if($cbo_date_cat_id==1)
	{
		$shipment_year="  extract(year from b.pub_shipment_date) as year";
		$shipment_month="  extract(month from b.pub_shipment_date) as month";
	}
	else if($cbo_date_cat_id==4)
	{
		$shipment_year="  extract(year from b.pack_handover_date) as year";
		$shipment_month="  extract(month from b.pack_handover_date) as month";
	}
	else if($cbo_date_cat_id==5)
	{
		$shipment_year="  extract(year from d.plan_date) as year";
	    $shipment_month="  extract(month from d.plan_date) as month";
	}
 

	if($cbo_date_cat_id==1) // Pub Ship Date
	{
		$where_con = "AND b.pub_shipment_date between '$date_from' and '$date_to'";
	}
	else if($cbo_date_cat_id==4) // PHD/PCD Ship Date
	{
		$where_con = "AND b.pack_handover_date between '$date_from' and '$date_to'";
	}
	else if($cbo_date_cat_id==5) // PHD/PCD Ship Date
	{
		$start_date=change_date_format($date_from,"","",1);
		$end_date=change_date_format($date_to,"","",1);
		$where_con=" and d.plan_date between '$start_date' and '$end_date'"; 
	}


	$sql ="SELECT a.working_company_id, c.company_id, c.plan_id, c.merged_plan_id, c.location_id, f.floor_name, b.is_confirmed, c.line_id, c.plan_id, d.plan_qnty, e.item_number_id, a.set_smv,(b.po_quantity*a.total_set_qnty) as po_quantity, b.plan_cut, b.pub_shipment_date, b.po_total_price, c.start_date, c.end_date, a.job_no, a.ID AS job_id, b.unit_price,b.id AS po_id,e.color_number_id, b.po_number, b.grouping, a.buyer_name, a.style_ref_no, d.plan_date,a.brand_id,c.allocated_mp, A.season_buyer_wise, a.season_year,c.first_day_output, (d.working_hour) as working_hour, $shipment_year,$shipment_month from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.job_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=c.line_id  and c.company_id in($company_id) $where_con $company_con $wc_locatin_cond and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ORDER BY month ";
	//echo $sql;die;

	$data_array = sql_select($sql);
	foreach($data_array as $row)
	{
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['year']=$row[csf('year')];
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['month']=$row[csf('month')];
	}
  
	$project_data_arr=array();
	$confrim_data_arr=array();
	$month_count_arr=array();
	$total_buyer_arr=array();


	//$sql = "SELECT a.working_company_id, c.COMPANY_ID, c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,b.is_confirmed,C.LINE_ID,C.PLAN_ID,d.PLAN_QNTY, A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.UNIT_PRICE,B.ID AS PO_ID, B.PO_NUMBER,b.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO, d.PLAN_DATE,A.BRAND_ID,c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, $shipment_year,$shipment_month from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d where a.id=b.job_id and c.plan_id=d.plan_id and c.company_id in($company_id) $where_con $company_con $wc_locatin_cond and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ORDER BY month";

	//echo $sql;die;

	// $sql = "SELECT a.working_company_id, c.COMPANY_ID, c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,
	// b.is_confirmed,C.LINE_ID,C.PLAN_ID,
	// d.PLAN_QNTY,
	// E.ITEM_NUMBER_ID , 
	// A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,
	// B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER,b.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO, d.PLAN_DATE,A.BRAND_ID,c.ALLOCATED_MP,
	//  A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, extract(year from b.pub_shipment_date) as year, 
	//  extract(month from b.pub_shipment_date) as month 
	//  from wo_po_details_master a, wo_po_break_down b, 
	//  ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, 
	//  LIB_SEWING_LINE f 
	//  where a.id=b.job_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID 
	//  and c.company_id in(4) 
	//  AND b.pub_shipment_date between '01-Dec-2023' and '31-Dec-2023' 
	//  --and a.working_company_id in(4) 
	//  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ORDER BY month";

  
	$sql = "SELECT a.working_company_id, c.COMPANY_ID, c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,b.is_confirmed,C.LINE_ID,C.PLAN_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER,b.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO, d.PLAN_DATE,A.BRAND_ID,c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, $shipment_year,$shipment_month from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.job_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ORDER BY month";
	//  order by f.sewing_line_serial 
	//	echo $sql;die;

	$dataArr=array();	
	$sql_result=sql_select($sql);
	foreach($sql_result as $row){
		//$dateKey=date("d M-Y",strtotime($row['PLAN_DATE']));
		//$total_buyer_arr[$row[csf('working_company_id')]]=$row[csf('working_company_id')];
		$total_buyer_arr[$row[csf('company_id')]]=$row[csf('company_id')];
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['year']=$row[csf('year')];
		$month_count_arr[$row[csf('year')]][$row[csf('month')]]['month']=$row[csf('month')];
		
		if($row[csf('is_confirmed')]==1)
		{
			$confrim_data_arr[$row[csf('company_id')]][$row[csf('year')]][$row['MONTH']]['confirmQty'] += $row['PLAN_QNTY'];
			$confrim_data_arr[$row[csf('company_id')]][$row[csf('year')]][$row['MONTH']]['confirmMin'] += $row['PLAN_QNTY']*$row['SET_SMV'];
		}
		if($row[csf('is_confirmed')]==2)
		{
			$project_data_arr[$row[csf('company_id')]][$row[csf('year')]][$row[csf('month')]]['projectQty']+=$row['PLAN_QNTY'];
			$project_data_arr[$row[csf('company_id')]][$row[csf('year')]][$row[csf('month')]]['projectMin']+=$row['PLAN_QNTY']*$row['SET_SMV'];
		}
	}
	ob_start(); 
    ?>
	<div id="scroll_body">
        <table width="300" border="0" cellpadding="2" cellspacing="0"  id="table-body"> 
            <thead>
			    <tr class="form_caption">
                    <td colspan="25" align="center" style="font-size:16px; font-weight:bold">Working Company Wise Capacity Vs Plan Report</td> 
                </tr> 
                <tr class="form_caption">
                    <td colspan="25" align="center"><?= $date_category_arr[$cbo_date_cat_id].' ('. change_date_format($date_from).' To '.change_date_format($date_to); ?>)</td> 
                </tr>
            </thead>
        </table>
		<table id="table_header" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="40" rowspan="3">SL</th>
					<th width="80" rowspan="3">Working Factory Name</th>
					<?
                    foreach($month_count_arr as $year_id=>$year_val)
                    {
						foreach($year_val as $month_id=>$month_val)
						{
						?>
						<th width="880" colspan="11"><?= $months[$month_val['month']]."   ".$month_val['year']; ?></th>
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
						<th colspan="2" width="80">Capacity</th>
						<th colspan="6" width="80">Order Plan</th> 
						<th colspan="2" width="80">Variance</th>
						<th rowspan="2" width="80">Percentage (%)</th>
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
                  
						<th width="80">Qnty</th> 
						<th width="80">Minutes</th>

						<th width="80">Confirm Qty/Pcs</th>
						<th width="80">Confirm Minutes</th>
						<th width="80">Projected Qty/Pcs</th>
						<th width="80">Projected Minutes</th>
						<th width="80">Total Qty/Pcs</th>
						<th width="80">Total Minutes</th>

						<th width="80">Qnty</th> 
						<th width="80">Minutes</th>
						<?
						}
                    }
                    ?>
				</tr>
				
			</thead>
			<tbody> 
			    <?
				foreach($total_buyer_arr as $com_id=>$com_val)

				// foreach($dataArr as $year_id=>$com_val)
				{
                ?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd1','<? echo $bgcolor; ?>')" id="tr_2nd1">
				 
					<td>1</td>
					<td align="center"><?= $companyArr[$com_id]; ?></td>
						<?
						$total_qnty = array();$total_mins = array();
						$confirm_qty_pcs = array();$confirm_qty_mins = array();
						$projected_qty_pcs = array(); $projected_qty_mins = array();
						$total_qty_pcs = array(); $total_qty_mins = array();
						$variance_qnty = array(); $variance_mins = array();
						$total_percents = array();
						foreach($month_count_arr as $year_id=>$year_val)
						{
							foreach($year_val as $month_id=>$month_val)
							{
								$year = $month_val['year'];
							    $month = $month_val['month'];
							?>
							<td width="80" align="right"><?= number_format($capacity_arr[$com_id][$year][$month]['capacity_pcs'],0);?></td>
							<td width="80" align="right"><?= number_format($capacity_arr[$com_id][$year][$month]['capacity_min'],0);?></td>

							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmQty'],0);?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmMin'],0);?></td>
							<td width="80" align="right"><?= number_format($project_data_arr[$com_id][$year][$month]['projectQty'],0);?></td>
							<td width="80" align="right"><?= number_format($project_data_arr[$com_id][$year][$month]['projectMin'],0);?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'], 0) ?></td>
							<td width="80" align="right"><?= number_format($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'], 0) ?></td>

							<td width="80" align="right"><?= number_format(($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'])-$capacity_arr[$com_id][$year][$month]['capacity_pcs']) ?></td>
							<td width="80" align="right"><?= number_format(($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'])-$capacity_arr[$com_id][$year][$month]['capacity_min'],0) ?></td>

							<?php
							 
							$plus = $confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'];
							$division = $plus/$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
							$percent = $division*100;

						    if(is_infinite($percent)){
								$total_percent = 0;
							}
							else{
								$total_percent = $percent;
							}

							?>
							<td width="80" align="right"><?= number_format($total_percent, 0); ?>%</td>
 
							 
							<?
								$total_qnty[$year][$month]['total_qnty'] +=$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
								$total_mins[$year][$month]['total_mins'] +=$capacity_arr[$com_id][$year][$month]['capacity_min'];

								$confirm_qty_pcs[$year][$month]['confirm_qty_pcs'] +=$confrim_data_arr[$com_id][$year][$month]['confirmQty'];
								$confirm_qty_mins[$year][$month]['confirm_qty_mins'] +=$confrim_data_arr[$com_id][$year][$month]['confirmMin']; 
								$projected_qty_pcs[$year][$month]['projected_qty_pcs']  +=$project_data_arr[$com_id][$year][$month]['projectQty'];
								$projected_qty_mins[$year][$month]['projected_qty_mins'] +=$project_data_arr[$com_id][$year][$month]['projectMin'];


								$total_qty_pcs[$year][$month]['total_qty_pcs'] += $confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'];
								$total_qty_mins[$year][$month]['total_qty_mins'] += $confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'];

								$variance_qnty[$year][$month]['variance_qnty'] += ($confrim_data_arr[$com_id][$year][$month]['confirmQty']+$project_data_arr[$com_id][$year][$month]['projectQty'])-$capacity_arr[$com_id][$year][$month]['capacity_pcs'];
								$variance_mins[$year][$month]['variance_mins'] += ($confrim_data_arr[$com_id][$year][$month]['confirmMin']+$project_data_arr[$com_id][$year][$month]['projectMin'])-$capacity_arr[$com_id][$year][$month]['capacity_min'];

								$total_percents[$year][$month]['total_percents'] += $total_percent;
						    }
                        }
                       ?> 
				</tr>
				<?
				}   
                ?> 
			</tbody>
			<tfoot>  
				<tr>
					<th width="40"></th>
					<th width="120">Grand Total</th>
					<?
					foreach($month_count_arr as $year_id=>$year_val)
					{
						foreach($year_val as $month_id=>$month_val)
						{
							$year = $month_val['year'];
							$month = $month_val['month']; 
						?>
						<th width="80" align="right"><?=number_format($total_qnty[$year][$month]['total_qnty'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_mins[$year][$month]['total_mins'], 0);?></th>
					
						<th width="80" align="right"><?=number_format($confirm_qty_pcs[$year][$month]['confirm_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($confirm_qty_mins[$year][$month]['confirm_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($projected_qty_pcs[$year][$month]['projected_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($projected_qty_mins[$year][$month]['projected_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_qty_pcs[$year][$month]['total_qty_pcs'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_qty_mins[$year][$month]['total_qty_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($variance_qnty[$year][$month]['variance_qnty'], 0);?></th>
						<th width="80" align="right"><?=number_format($variance_mins[$year][$month]['variance_mins'], 0);?></th>
						<th width="80" align="right"><?=number_format($total_percents[$year][$month]['total_percents'], 0);?>%</th>
						<?
						}
					}
                    ?> 
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****$report_type"; 
	exit();
	}
?>