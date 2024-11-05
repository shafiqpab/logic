<?
include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_details=return_library_array( "select id,company_name from lib_company",'id','company_name');
$country_name_arr=return_library_array( "select id,country_name from  lib_country",'id','country_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   
	exit();  	 
} 

$team_mem=sql_select("select id,team_member_name,member_contact_no from  lib_mkt_team_member_info");
foreach($team_mem as $tm)
{
	$company_team_member_name_arr[$tm[csf('id')]]=$tm[csf('team_member_name')];
	$company_team_member_contact_arr[$tm[csf('id')]]=$tm[csf('member_contact_no')];
}

$team_leader=sql_select("select id,team_leader_name,team_contact_no from  lib_marketing_team");
foreach($team_leader as $tl)
{
	$team_leader_arr[$tl[csf('id')]]=$tl[csf('team_leader_name')];
	$company_team_leader_contact_arr[$tl[csf('id')]]=$tl[csf('team_contact_no')];
}

if($action=="report_generate")
{
	$company_id=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$year_id=str_replace("'","",$cbo_year);
	$job_no=str_replace("'","",$txt_job_no);
	$style_ref=str_replace("'","",$txt_style_ref);
	$po_no=str_replace("'","",$txt_po_no);
	$po_id=str_replace("'","",$txt_po_id);

	$txt_internal_ref=str_replace("'","",$txt_internal_ref);
	$txt_file_no=str_replace("'","",$txt_file_no);

	if($company_id==0) $company_cond="%%"; else $company_cond=$company_id;
	//if($buyer_id!=0) $buyer_id_cond=" and c.buyer_name='$buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_location_id=="") $location_id=""; else $location_id=$cbo_location_id;
	if($location_id!=0)
	{
		$home_page_location_con=" and c.location_name='$location_id'";
	}
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$buyer_id";
	}
	//echo $buyer_id_cond;
	
	if($year_id!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(c.insert_date)=$year_id";
		else if($db_type==2) $year_cond=" and to_char(c.insert_date,'YYYY')=$year_id";
	}
	else $year_cond="";
	
	if($job_no!="") $job_cond=" and c.job_no_prefix_num='$job_no'"; else  $job_cond="";
	if(trim($style_ref)!="") $style_ref_cond=" and c.style_ref_no like '%".trim($style_ref)."%'"; else $style_ref_cond="";
	if(trim($po_no)!="") $po_number_cond=" and b.po_number like '%".trim($po_no)."%'"; else $po_number_cond="";
	//wo_po_break_down
	if(trim($txt_internal_ref)!="") $internal_ref_cond=" and b.grouping like '%".trim($txt_internal_ref)."%'"; else $internal_ref_cond="";
	if(trim($txt_file_no)!="") $file_number_cond=" and b.file_no like '%".trim($txt_file_no)."%'"; else $file_number_cond="";

	$start_date = return_field_value("min(a.country_ship_date)" ,"wo_po_color_size_breakdown a,wo_po_break_down b, wo_po_details_master c"," a.po_break_down_id=b.id and a.job_no_mst=b.job_no_mst and c.job_no=a.job_no_mst and c.company_name like '$company_id' $buyer_id_cond $job_cond $style_ref_cond and a.shiping_status!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $po_number_cond $internal_ref_cond $file_number_cond");
	$end_date=date("Y-m-01"); 
	$start_month=date("Y-m",strtotime($start_date));
	$end_month=date("Y-m",strtotime("-1 days"));
	$end_date2=date("Y-m-d",strtotime("-1 days"));
	
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
	}
	
	$total_months=datediff("m",$start_month,$end_month);
	$last_month=date("Y-m", strtotime("+1 Months", strtotime($end_month)));
	$previous_month_year=date("Y-m",strtotime("-1 Months", strtotime($end_month)));
	$array_previous_month_year=explode("-",$previous_month_year);
	$number_of_dayes_prev_moth=cal_days_in_month(CAL_GREGORIAN, $array_previous_month_year[1], $array_previous_month_year[0]);
	$previous_month_end_date=$previous_month_year."-".$number_of_dayes_prev_moth;
	if($db_type==2)
	{
		$previous_month_end_date=change_date_format($previous_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	$month_identify=explode("-",$end_date2);
	$month=$month_identify[1];
	$year=$month_identify[0];
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($db_type==2)
	{
		$current_month_end_date=change_date_format($current_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	//$order_wise_smv=return_library_array("select b.id, a.set_smv from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id","id","set_smv");
	
	
	/*$order_wise_smv_result=sql_select("select b.id, a.set_smv, a.set_break_down from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id");
	$order_wise_smv=array();
	foreach($order_wise_smv_result as $row)
	{
		$item_break_arr=explode("__",$row[csf("set_break_down")]);
		foreach($item_break_arr as $val)
		{
			$val_ref=explode("_",$val);
			$order_wise_smv[$row[csf("id")]][$val_ref[0]]=$val_ref[3];
		}
		
	}*/
	
	
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($company_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$order_wise_smv[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
	}
	
	$str_cond3="  and a.country_ship_date between '$start_date' and '$current_month_end_date'";
	//$str_cond3="  and a.country_ship_date between '01-Jul-2014' and '$current_month_end_date'";
	$exFactory_arr=array(); $cut_qty_arr=array(); $sew_qty_arr=array(); $iron_qty_arr=array();$finish_qty_arr=array();
	$data_arr=sql_select( "SELECT po_break_down_id, country_id, item_number_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id, item_number_id");
	foreach($data_arr as $row)
	{
		$exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]]=$row[csf('ex_factory_qnty')];
	}
	
	$data_arr_cut=sql_select( "SELECT po_break_down_id, country_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where  production_type ='1' and status_active=1 and is_deleted=0 group by po_break_down_id, country_id, item_number_id");
	foreach($data_arr_cut as $row_cut)
	{
		$cut_qty_arr[$row_cut[csf('po_break_down_id')]][$row_cut[csf('country_id')]][$row_cut[csf('item_number_id')]]=$row_cut[csf('production_quantity')];
	}
	
	$sewing_qnty=sql_select("SELECT po_break_down_id, country_id, item_number_id, sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 group by po_break_down_id,country_id,item_number_id ");
	foreach($sewing_qnty as $row_sew)
	{
		$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_id')]][$row_sew[csf('item_number_id')]]=$row_sew[csf('production_quantity')];
	}
	
	$iron_qnty=sql_select("SELECT po_break_down_id, country_id, item_number_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='7' and is_deleted=0 and status_active=1 group by po_break_down_id,country_id, item_number_id ");
	foreach($iron_qnty as $row)
	{
		$iron_qty_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]]=$row[csf('production_quantity')];
	}
				
	$finish_qnty=sql_select("SELECT po_break_down_id, country_id, item_number_id, sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='8' and is_deleted=0 and status_active=1 group by po_break_down_id,country_id,item_number_id");
	foreach($finish_qnty as $row_finish)
	{
		$finish_qty_arr[$row_finish[csf('po_break_down_id')]][$row_finish[csf('country_id')]][$row_finish[csf('item_number_id')]]=$row_finish[csf('production_quantity')];
	}
	
	
	/*echo "select a.country_id, a.country_remarks, a.country_ship_date, a.item_number_id, a.shiping_status, a.order_quantity AS po_quantity, a.plan_cut_qnty, a.order_total AS amnt, a.order_rate, b.id as po_id, b.po_number,b.details_remarks, c.job_no_prefix_num, c.job_no, c.buyer_name, c.style_ref_no 
	from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
	where a.po_break_down_id=b.id and a.job_no_mst=b.job_no_mst and a.job_no_mst=c.job_no and c.company_name='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.shiping_status!=3 $buyer_id_cond $job_cond $style_ref_cond $txt_po_number $year_cond $str_cond3 $home_page_location_con order by a.country_ship_date DESC";*/
	
	
	$sql_order_level=sql_select("SELECT c.team_leader,c.dealing_marchant,a.country_id, max(a.country_remarks) as country_remarks, a.country_ship_date, a.item_number_id, max(a.shiping_status) as shiping_status, sum(a.order_quantity) AS po_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty, sum(a.order_total) AS amnt, b.id as po_id, b.po_number,b.details_remarks, c.job_no_prefix_num, c.job_no, c.buyer_name, c.style_ref_no 
	from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
	where a.po_break_down_id=b.id and a.job_no_mst=b.job_no_mst and a.job_no_mst=c.job_no and c.company_name='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.shiping_status!=3 $buyer_id_cond $job_cond $style_ref_cond $txt_po_number $year_cond $str_cond3 $home_page_location_con $internal_ref_cond $file_number_cond $po_number_cond
	group by c.team_leader,c.dealing_marchant,a.country_id, a.country_ship_date, a.item_number_id, b.id, b.po_number, b.details_remarks, c.job_no_prefix_num, c.job_no, c.buyer_name, c.style_ref_no 
	order by a.country_ship_date DESC");

	/*echo "select a.country_id, max(a.country_remarks) as country_remarks, a.country_ship_date, a.item_number_id, max(a.shiping_status) as shiping_status, sum(a.order_quantity) AS po_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty, sum(a.order_total) AS amnt, b.id as po_id, b.po_number,b.details_remarks, c.job_no_prefix_num, c.job_no, c.buyer_name, c.style_ref_no 
	from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
	where a.po_break_down_id=b.id and a.job_no_mst=b.job_no_mst and a.job_no_mst=c.job_no and c.company_name='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.shiping_status!=3 $buyer_id_cond $job_cond $style_ref_cond $txt_po_number $year_cond $str_cond3 $home_page_location_con $internal_ref_cond $file_number_cond $po_number_cond
	group by a.country_id, a.country_ship_date, a.item_number_id, b.id, b.po_number, b.details_remarks, c.job_no_prefix_num, c.job_no, c.buyer_name, c.style_ref_no 
	order by a.country_ship_date DESC";*/
	//
	$month_arr=array();
	$month_year_arr=array();
	$sql_order_level_arr=array();
	$item_number_arr=array();
	$month_wise_sammary_buyer=array();
	$month_wise_sammary=array();
	$month_wise_buyer_total=array();
	$grand_total_arr=array();
	foreach($sql_order_level as $sql_order_level_row)
	{
		$order_rate=$sql_order_level_row[csf('amnt')]/$sql_order_level_row[csf('po_quantity')];
		$month_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]=date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]));
		$month_year_arr[ date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['year']=date("Y",strtotime($sql_order_level_row[csf('country_ship_date')]));
		$month_year_arr[ date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['month']=date("F",strtotime($sql_order_level_row[csf('country_ship_date')]));
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['job_no'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('job_no_prefix_num')];

		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['buyer_name'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('buyer_name')];

		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['team_leader'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('team_leader')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['dealing_marchant'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('dealing_marchant')];

		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['po_number'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('po_number')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['style_ref_no'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('style_ref_no')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['po_quantity'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$sql_order_level_row[csf('po_quantity')];
		
		
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['country_ship_date'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('country_ship_date')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['shiping_status'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('shiping_status')];
		//$sql_order_level_arr['shiping_status'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('shiping_status')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['order_rate'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$order_rate;
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['plan_cut_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$sql_order_level_row[csf('plan_cut_qnty')];
		$item_number_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]['item_number_id'][]=$sql_order_level_row[csf('item_number_id')];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['country_remarks'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]=$sql_order_level_row[csf('country_remarks')];
		
		$b_id=$sql_order_level_row[csf('buyer_name')];
		
		
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['po_sah_quantity'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=((($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
		
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['cut_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$cut_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['sewing_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]];

		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['sewing_sah_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=((($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
		
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['finish_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$finish_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]];
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['ex_fact_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]];
		
		
		$sql_order_level_arr[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['iron_qnty'][$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]]+=($sql_order_level_row[csf('po_quantity')]-$iron_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
		
		
		$month_wise_buyer_total[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['po_qty']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
		
		$month_wise_buyer_total[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['sewing_sah_qnty']+=((($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
		$month_wise_sammary_buyer[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))][$b_id]['po_qty']+=$sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]];
		
		$month_wise_sammary_buyer[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))][$b_id]['sewing_sah_qnty']+=((($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
		
		$month_wise_sammary_buyer[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))][$b_id]['order_value']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_rate;
		$month_wise_buyer_total[date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))]['order_value']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_rate;
		
		
		if(date("Y-m",strtotime($sql_order_level_row[csf('country_ship_date')]))==date("Y-m",strtotime("-1 days")))
		{
			//$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_id')]]
			$grand_total_arr['current_po_qty']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['current_cutting_qty']+=($sql_order_level_row[csf('po_quantity')]-$cut_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['current_sewing_qty']+=($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['current_sewing_sah_qty']+=((($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
			$grand_total_arr['current_iron_qty']+=($sql_order_level_row[csf('po_quantity')]-$iron_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['current_finish_qty']+=($sql_order_level_row[csf('po_quantity')]-$finish_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['current_po_value']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_rate;
			// $carrent_month_name=$month_year_arr[date("Y-m",strtotime("-1 days"))][month].",".$month_year_arr[date("Y-m",strtotime("-1 days"))][year];
		}
		else
		{
			$grand_total_arr['previpus_po_qty']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['previpus_cutting_qty']+=($sql_order_level_row[csf('po_quantity')]-$cut_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['previpus_sewing_qty']+=($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['previpus_sewing_sah_qty']+=((($sql_order_level_row[csf('po_quantity')]-$sew_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_wise_smv[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('item_number_id')]])/60);
			$grand_total_arr['previpus_iron_qty']+=($sql_order_level_row[csf('po_quantity')]-$iron_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['previpus_finish_qty']+=($sql_order_level_row[csf('po_quantity')]-$finish_qty_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]]);
			$grand_total_arr['previpus_po_value']+=($sql_order_level_row[csf('po_quantity')]-$exFactory_arr[$sql_order_level_row[csf('po_id')]][$sql_order_level_row[csf('country_id')]][$sql_order_level_row[csf('item_number_id')]])*$order_rate;
		}
		
	}
	//var_dump($test_data);
	/*$b_id="";
	$m_count=1;
	foreach($month_arr as $row)
	{
		foreach($sql_order_level_arr[$row]['job_no'] as $p_id=>$p_value)
		{
		   foreach($p_value as $c_id=>$c_value)
		   {
				$b_id=$sql_order_level_arr[$row]['buyer_name'][$p_id][$c_id];
				$month_wise_buyer_total[$row]['po_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id];
				$month_wise_buyer_total[$row]['sewing_sah_qnty']+=((($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id])*$order_wise_smv[$p_id])/60);
				$month_wise_sammary_buyer[$row][$b_id]['po_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id];
				$month_wise_sammary_buyer[$row][$b_id]['sewing_sah_qnty']+=((($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id])*$order_wise_smv[$p_id])/60);
				$month_wise_sammary_buyer[$row][$b_id]['order_value']+=($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id])*$sql_order_level_arr[$row]['order_rate'][$p_id][$c_id];
				$month_wise_buyer_total[$row]['order_value']+=($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id])*$sql_order_level_arr[$row]['order_rate'][$p_id][$c_id];
				if($row==date("Y-m",strtotime("-1 days")))
				{
					//$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_id')]]
					 $grand_total_arr['current_po_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id];
					 $grand_total_arr['current_cutting_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$cut_qty_arr[$p_id][$c_id];
					 $grand_total_arr['current_sewing_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id];
					 $grand_total_arr['current_sewing_sah_qty']+=((($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id])*$order_wise_smv[$p_id])/60);
					 $grand_total_arr['current_iron_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$iron_qty_arr[$p_id][$c_id];
					 $grand_total_arr['current_finish_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$finish_qty_arr[$p_id][$c_id];
					 $grand_total_arr['current_po_value']+=($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id])*$sql_order_level_arr[$row]['order_rate'][$p_id][$c_id];	
					// $carrent_month_name=$month_year_arr[date("Y-m",strtotime("-1 days"))][month].",".$month_year_arr[date("Y-m",strtotime("-1 days"))][year];
				}
				else
				{
				 $grand_total_arr['previpus_po_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id];
				 $grand_total_arr['previpus_cutting_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$cut_qty_arr[$p_id][$c_id];
				 $grand_total_arr['previpus_sewing_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id];
				 $grand_total_arr['previpus_sewing_sah_qty']+=((($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$sew_qty_arr[$p_id][$c_id])*$order_wise_smv[$p_id])/60);
				 $grand_total_arr['previpus_iron_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$iron_qty_arr[$p_id][$c_id];
				 $grand_total_arr['previpus_finish_qty']+=$sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$finish_qty_arr[$p_id][$c_id];
				 $grand_total_arr['previpus_po_value']+=($sql_order_level_arr[$row]['po_quantity'][$p_id][$c_id]-$exFactory_arr[$p_id][$c_id])*$sql_order_level_arr[$row]['order_rate'][$p_id][$c_id];
				}
			}
		}
		$m_count++;
	}*/
	ob_start();
	
	$bgcolor1='#E9F3FF';
	$bgcolor2='#FFFFFF';
    ?>  
    <table width="1200" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="9" align="center" ><font size="3"><strong><u><? echo $company_details[$company_id]; ?></u></strong></font></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="9" align="center"><font size="2"><strong>Total Pending Order Summary </strong></font></td>
        </tr>
    </table>
    <table border="1" rules="all" class="rpt_table" width="1200">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="170"> Month </th>
                <th width="140">Pending PO Qty. </th>
                <th width="140">Pending PO Value</th>
                <th width="140">Cutting Pending </th>
                <th width="140">Sewing Pending</th>
                <th width="140">Sewing Pending [SAH]</th>
                <th width="140">Iron Pending</th>
                <th>Finishing Pending</th>
            </tr>
        </thead>
        <tbody>
            <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
                <td align="center">1</td>
                <td>Previous To Current Month</td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_po_qty'],0); $summary_grand_total_po_qny+=$grand_total_arr['previpus_po_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_po_value'],2); $summary_grand_total_lc_value+=$grand_total_arr['previpus_po_value']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_cutting_qty'],0); $summary_grand_total_cut_qny+=$grand_total_arr['previpus_cutting_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_sewing_qty'],0); $summary_grand_total_sewing_qny+=$grand_total_arr['previpus_sewing_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_sewing_sah_qty'],0); $summary_grand_total_sewing_sah_qny+=$grand_total_arr['previpus_sewing_sah_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_iron_qty'],0); $summary_grand_total_iron_qny+=$grand_total_arr['previpus_iron_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['previpus_finish_qty'],0); $summary_grand_total_finish_qny+=$grand_total_arr['previpus_finish_qty']; ?></td>
            </tr>
            <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
                <td align="center">2</td>
                <td><? $current_month_id=date("Y-m",strtotime("-1 days")); echo $month_year_arr[$current_month_id]['month'].",".$month_year_arr[$current_month_id]['year']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_po_qty'],0); $summary_grand_total_po_qny+=$grand_total_arr['current_po_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_po_value'],2); $summary_grand_total_lc_value+=$grand_total_arr['current_po_value']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_cutting_qty'],0); $summary_grand_total_cut_qny+=$grand_total_arr['current_cutting_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_sewing_qty'],0); $summary_grand_total_sewing_qny+=$grand_total_arr['current_sewing_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_sewing_sah_qty'],0); $summary_grand_total_sewing_sah_qny+=$grand_total_arr['current_sewing_sah_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_iron_qty'],0); $summary_grand_total_iron_qny+=$grand_total_arr['current_iron_qty']; ?></td>
                <td align="right"><? echo number_format($grand_total_arr['current_finish_qty'],0); $summary_grand_total_finish_qny+=$grand_total_arr['current_finish_qty']; ?></td>
            </tr>
        </tbody> 
        <tfoot>
            <tr>
                <th colspan="2" align="right">Total</th>
                <th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_cut_qny,0); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_sewing_sah_qny,0); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_iron_qny,0); ?></th>
                <th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
            </tr>
        </tfoot>
  </table> 
    <br/>      
    <table width="1200">
        <tr class="form_caption"><td colspan="3" style="border:none;font-size:16px; font-weight:bold"><u>Month Wise Total Summary</u></td></tr>
        <tr>
            <?
            $s=0;
			//var_dump($month_wise_sammary_buyer);
            foreach($month_wise_sammary_buyer as $mid=>$mval)
            {
                if($s%3==0) $tr="</tr><tr>"; else $tr="";
                echo $tr; 
                ?>
                <td valign="top">
                    <div style="width:400px">
                    <table width="400px"  cellspacing="0"  class="display">
                        <tr>
                            <td colspan="4" align="center">
                            <font size="2"><strong>Total Summary <? echo $month_year_arr[$mid][month].",".$month_year_arr[$mid][year]; ?></strong></font>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" class="rpt_table" border="1" rules="all">
                        <thead>
                            <th width="30">SL</th>
                            <th width="70">Buyer Name</th>
                            <th width="100">Po Qnty</th>
                            <th width="100">Sewing Pending [SAH]</th>
                            <th>PO Value</th>
                        </thead>
                      
                        <?
                        $l=1;
                        foreach($mval as $b_id=>$bvalue)
                        {
                            if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trcolor_<? echo $s; ?><? echo $l; ?>','<? echo $bgcolor; ?>')" id="trcolor_<? echo $s; ?><? echo $l; ?>">
                                <td align="center"><? echo $l; ?></td>
                                <td><div style="word-wrap:break-word; width:70px"><? echo $buyer_short_name_arr[$b_id]; ?></div></td>
                                <td align="right"><? echo number_format($bvalue["po_qty"],0); $tot_po_qnty+=$bvalue['po_qty']; ?></td>
                                <td align="right"><? echo number_format($bvalue["sewing_sah_qnty"],0); $tot_sewing_sah_qnty+=$bvalue['sewing_sah_qnty']; ?></td>
                                <td align="right"><? echo number_format($bvalue['order_value'],2); $tot_po_val+=$buyer_order_val; ?></td>
                            </tr>
                            <?
                            $l++;
                         }
                         ?>
                        <tfoot>
                            <th colspan="2" align="right">Total</th>
                            <th align="right"><? echo number_format($month_wise_buyer_total[$mid]['po_qty'],0); ?></th>
                            <th align="right"><? echo number_format($month_wise_buyer_total[$mid]['sewing_sah_qnty'],0); ?></th>
                            <th align="right"><? echo number_format($month_wise_buyer_total[$mid]['order_value'],2); ?></th>
                        </tfoot>
                    </table>
                </div>
            </td>
            <?
            $s++;
         }
        ?>
        </tr>
     </table>
    <br/>
    <div>
    <div align="left" style="background-color:#E1E1E1; color:#000; width:2000px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i> Details Report</i></u></strong></div>
        <table cellspacing="0" cellpadding="0" width="2000"  border="1" rules="all" class="rpt_table" >
        	<thead>
            	<tr>
                    <th width="30">SL</th>
                    <th width="100">Team Leader</th>
                    <th width="100">Dealing Marchant</th>
                    <th width="50">Job No</th>
                    <th width="50">Buyer Name</th>
                    <th width="110">Po Number</th>
                    <th width="110">Style Name</th>
                    <th width="140">Item Name</th>
                    <th width="100">Country Name</th>
                    <th width="90"> Po Qnty.</th>
                    <th width="70">Ship Date</th>
                    <th width="50">Delay</th>
                    <th width="80">Plan Cut Qnty</th>
                    <th width="80">Cut Qnty</th>
                    <th width="80">Actual Cut %</th>
                    <th width="80">Sewing Qnty </th>
                    <th width="80">Sewing Pending [SAH] </th>
                    <th width="80">Iron Pending</th>
                    <th width="80">Finish Qnty</th>
                    <th width="80">Finish Pending</th>
                    <th width="80">Ship Qnty</th>
                    <th width="90">Pending PO Qnty.</th>
                    <th width="90">Pending PO [SAH]</th>
                    <th>Remarks</th>
                </tr>
        	</thead>
		</table>
    	<div style="width:2020px; max-height:410px; overflow-y:scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" width="2000"  border="1" rules="all" class="rpt_table" id="table_body" >
			<?
            $ii=1; $k=1; $total_po_qnty=0; $total_cut_quantity=0; $total_sew_qnty=0; $total_finish_qnty=0; $total_ship_qnty=0; $total_balance_qnty=0;
			$total_plan_cut_qaty=0;
			foreach($month_arr as $row)
			{
				?>
				  <tr bgcolor="#EFEFEF">
						<td colspan="22"><b><?php echo $month_year_arr[$row][month].",".$month_year_arr[$row][year];?></b></td>
				  </tr>
				<?
				$monthly_total_po_qnty=0;
				$monthly_plan_cut_qaty=0;
				$monthly_total_cut_qnty=0;
				$monthly_total_sew_qnty=0;
				$monthly_total_finish_qnty=0;
				$monthly_total_ship_qnty=0;$monthly_total_sew_sah_quantity=0;$monthly_total_iron_qnty=0;$monthly_tot_panding_sah_qty=0;
				foreach( $sql_order_level_arr[$row]["job_no"] as $po_id=> $value)
				{
					foreach($value as $country_id=> $value1)
					{
						if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$panding_qty=$sql_order_level_arr[$row]['po_quantity'][$po_id][$country_id]-$sql_order_level_arr[$row]['ex_fact_qnty'][$po_id][$country_id]; 
						//echo $ii."<br>";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii;?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                            <td width="30"><? echo $ii; ?></td>
                            <td width="100" align="left"><? echo $team_leader_arr[$sql_order_level_arr[$row]['team_leader'][$po_id][$country_id]]; ?></td>
                            <td width="100" align="left"><? echo $company_team_member_name_arr[$sql_order_level_arr[$row]['dealing_marchant'][$po_id][$country_id]]; ?></td>
                            <td width="50" align="center"><? echo $sql_order_level_arr[$row]['job_no'][$po_id][$country_id]; ?></td>
                            <td width="50"><p><? echo $buyer_short_name_arr[$sql_order_level_arr[$row]['buyer_name'][$po_id][$country_id]]; ?></p></td>
                            <td width="110"><p><? echo $sql_order_level_arr[$row]['po_number'][$po_id][$country_id];?></p></td>
                            <td width="110"><p><? echo $sql_order_level_arr[$row]['style_ref_no'][$po_id][$country_id];  ?></p></td>
                            <td width="140"><p>
							<?
                            $item_naume="";
							foreach(array_unique($item_number_arr[$row][$po_id][$country_id]['item_number_id']) as $item_id)
							{
								if($item_naume!="")  $item_naume.=",".$garments_item[$item_id]; else $item_naume=$garments_item[$item_id];
							} 
							echo $item_naume; 
							?>
                            </p></td>
                            <td align="center" width="100"><p><? echo $country_name_arr[$country_id]; ?></p></td>
                            <td align="right" width="90">
							<? 
                            $po_qnty=$sql_order_level_arr[$row]['po_quantity'][$po_id][$country_id];
                            echo number_format($po_qnty,0);
                            $tot_po_quantity+=$po_qnty;
                            $monthly_total_po_qnty+=$po_qnty;
                            ?>
                            </td>
							<td width="70" align="center"><? echo change_date_format($sql_order_level_arr[$row]['country_ship_date'][$po_id][$country_id],'dd-mm-yyyy','-'); ?></td>
                            <td width="50" align="center" bgcolor="<? echo $color; ?>"><p><? $days_remian=datediff("d",$sql_order_level_arr[$row]['country_ship_date'][$po_id][$country_id],date("Y-m-d")); echo $days_remian; ?></p></td>
                            <td align="right" width="80">
							<? 
							$plan_cut_qaty=$sql_order_level_arr[$row]['plan_cut_qnty'][$po_id][$country_id]; 
							$total_plan_cut_qaty+=$plan_cut_qaty; $monthly_plan_cut_qaty+=$plan_cut_qaty; 
							echo number_format($plan_cut_qaty,0); 
							?></td>
                            <td align="right" width="80" title="Cutting Qnty Not Exceed Order Qnty">
							<?
							$cut_qnty=$sql_order_level_arr[$row]['cut_qnty'][$po_id][$country_id];
							echo number_format($cut_qnty,0); $total_cut_quantity+=$cut_qnty; $monthly_total_cut_qnty+=$cut_qnty; 
							$cutting_percentage=($cut_qnty/$plan_cut_qaty*100); if($cutting_percentage>100)  $plan_color="#FF0000"; else $plan_color=$bgcolor; 
							?>
                            </td>
                            <td align="right" width="80" bgcolor="<? echo $plan_color ?>"><? echo number_format($cutting_percentage,2)."%"; ?></td>
							<td align="right" width="80">
							<? 
							$sew_quantity=$sql_order_level_arr[$row]['sewing_qnty'][$po_id][$country_id]; 
							$total_sew_qnty+=$sew_quantity; 
							$monthly_total_sew_qnty+=$sew_quantity; 
							echo number_format($sew_quantity,0); 
							?>
                            </td>
                            <td align="right" width="80">
							<?
							
							$sew_sah_quantity=$sql_order_level_arr[$row]['sewing_sah_qnty'][$po_id][$country_id]; 
							$total_sew_sah_quantity+=$sew_sah_quantity; 
							$monthly_total_sew_sah_quantity+=$sew_sah_quantity; 
							echo number_format($sew_sah_quantity,0); 
							?></td>
                            <td align="right" width="80">
							<? 
							$iron_qnty=$sql_order_level_arr[$row]['iron_qnty'][$po_id][$country_id];
							$total_iron_qnty+=$iron_qnty; $monthly_total_iron_qnty+=$iron_qnty; 
							echo number_format($iron_qnty,0);
							?></td>
                            <td align="right" width="80">
							<? 
							$finish_quantity=$sql_order_level_arr[$row]['finish_qnty'][$po_id][$country_id];
							$total_finish_qnty+=$finish_quantity; $monthly_total_finish_qnty+=$finish_quantity;
							echo number_format($finish_quantity,0); 
							?></td>
                            <td align="right" width="80"><?  echo number_format($sql_order_level_arr[$row]['po_quantity'][$po_id][$country_id]-$finish_quantity,0); ?></td>
							<td align="right" width="80">
							<? 
							$ex_qty=$sql_order_level_arr[$row]['ex_fact_qnty'][$po_id][$country_id]; 
							$total_ex_fac+= $ex_qty; $monthly_total_ship_qnty+=$ex_qty; 
							echo number_format($ex_qty,0); 
							?></td>
                            <td align="right" width="90"><? $tot_panding_qty+=$panding_qty; echo number_format($panding_qty,0); ?></td>
                            <td align="right" width="90">
							<? 
							$panding_sah_qty=$sql_order_level_arr[$row]['po_sah_quantity'][$po_id][$country_id];
							$tot_panding_sah_qty+=$panding_sah_qty; $monthly_tot_panding_sah_qty+=$panding_sah_qty; 
							echo number_format($panding_sah_qty,0); 
							?></td>
                            <td><p><? echo $sql_order_level_arr[$row]['country_remarks'][$po_id][$country_id];//$row_order_level[csf('details_remarks')]; ?></p></td>
                       </tr>
						<?
                        $ii++;
					}
				}
				?>
				<tr bgcolor="#CCCCCC">
					<td colspan="9" align="right"><b>Monthly Total</b></td>
					<td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($monthly_plan_cut_qaty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                    <td align="right"><? echo number_format($monthly_total_sew_sah_quantity,0); ?></td>
                    <td align="right"><? echo number_format($monthly_total_iron_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty-$monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                    <td align="right"><? echo number_format($monthly_tot_panding_sah_qty,0); ?></td>
					<td>&nbsp;</td>
				</tr>
				<?
			}
			?>
            </table>
            </div>
            <table cellspacing="0" cellpadding="0" width="2000"  border="1" rules="all" class="tbl_bottom">
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="140">&nbsp;</td>
                    <td width="100" align="right">Grand Total</td>
                    <td width="90" align="right"><? echo  number_format($tot_po_quantity,0);?></td>
                    <td width="70">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="80" align="right"><? echo number_format($total_plan_cut_qaty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($total_cut_quantity,0); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="80" align="right"><? echo number_format($total_sew_qnty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($total_sew_sah_quantity,0); ?></td>
                    <td width="80" align="right"><? echo number_format($total_iron_qnty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($total_finish_qnty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($tot_po_quantity-$total_finish_qnty,0); ?></td>
                    <td width="80" align="right"><? echo number_format($total_ex_fac,0); ?></td>
                    <td width="90" align="right"><? echo number_format($tot_panding_qty,0); ?></td>
                    <td width="90" align="right"><? echo number_format($tot_panding_sah_qty,0); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table> 
        </div> 
	<?
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
    echo "$html####$filename"; 
    exit();
}
?>