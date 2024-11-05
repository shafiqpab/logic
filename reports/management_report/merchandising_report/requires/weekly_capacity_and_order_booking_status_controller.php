<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
Functionality	:
JS Functions	:
Created by		:	Monzu
Creation date 	: 	13-10-2012
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where file_type=1",'master_tble_id','image_location');
$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls",'job_no','commission');
$country_name_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
$lib_buyer_season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');

if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=76 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if ($action=="load_drop_down_buyer")
{
echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/weekly_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );
}

if ($action=="load_drop_down_team_member")
{
echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Dealing Merchant-", $selected, "" );
}
if ($action=="week_date")
{
	$data=explode("_",$data);
	$sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week=$data[0] and year= $data[1] group by week");
	$week_start_day=0;
	$week_end_day=0;
	foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
	{
		$week_start_day=$row_week_week_start_end_date[csf("week_start_day")];
		$week_end_day=$row_week_week_start_end_date[csf("week_end_day")];
	}
	echo change_date_format($week_start_day,"dd-mm-yyyy",'-')."_".change_date_format($week_end_day,"dd-mm-yyyy",'-');
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_id", 120, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}


if($type=="report_generate")
{
	$data=explode("_",$data);
	//print_r($data);die;
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	$cbo_order_status2=$data[4];
	if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";
	if(trim($data[5])=="0") $team_leader="%%"; else $team_leader="$data[5]";
	if(trim($data[6])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[6]";
	//if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	if(trim($data[10])=="0") $product_category_con=""; else $product_category_con=" and a.product_category=$data[10]";

	//if(trim($data[10])=="0") $product_category_con=""; else $product_category_con=" and a.product_category=$data[10]";
	$cbo_order_status=$data[8];

	$cbo_season_id=$data[12];
	if($cbo_season_id!=0){$season_con=" and a.season_buyer_wise=$cbo_season_id";}else{$season_con="";}


	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}

	$cbo_category_by=$data[7]; $caption_date='';
	$cbo_year_selection=$data[9];



	if ($start_date!="" && $end_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($start_date));
		$ey = date('Y',strtotime($end_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
			$tot_year.=",".$ey;
			}
			else
			{
			$tot_year.=$ey;
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}


function week_of_year($year,$week_start_day)
{
$week_array=array();
$week=0;
for($i=1;$i<=12; $i++)
{
	$month=str_pad($i, 2, '0', STR_PAD_LEFT);
	$year=$year;
	$first_date_of_year=$year."-01-01";
	$first_day_of_year=date('l', strtotime($first_date_of_year));
	if($i==1)
	{
		if(date('l', strtotime($first_day_of_year))==$week_start_day)
		{
			$week=0;
		}
		else
		{
			$week=1;
		}
	}
	$days_in_month = cal_days_in_month(0, $month, $year) ;

    foreach (range(1, $days_in_month) as $day)
	{
		$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
		global $db_type;
		if($db_type==2)
		{
			$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
		}

		if(date('l', strtotime($test_date))==$week_start_day)
		{
		  $week++;
		}
		$week_day=date('l', strtotime($test_date));
		$week_array[$test_date]=$week;


		/*$con = connect();//the connection have to be called out of function
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "week_of_year", 1 );
		$field_array="id, year, month, week, week_start_day, week_date,week_day";
		$data_array="(".$id.",".$year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
		$rID=sql_insert("week_of_year",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
			}
			else{
				mysql_query("ROLLBACK");
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
			}
			else{
				oci_rollback($con);
			}
		}*/

    }
}
return $week_array ;
}
$weekarr=week_of_year($cbo_year_selection,"Sunday");


//$con = connect();
function week_of_year_new($year,$week_start_day)
{

	$week_array=array();
	for($i=1;$i<=12; $i++)
	{
		$month=str_pad($i, 2, '0', STR_PAD_LEFT);
		$year=$year;

		$days_in_month = cal_days_in_month(0, $month, $year) ;
		foreach (range(1, $days_in_month) as $day)
		{
			$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
			global $db_type;

			if($db_type==2)
			{
				$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
			}
			$date = new DateTime($test_date);
            $week = $date->format("W");
			$week_year = $date->format("o");
			//$week= (int) date("W", strtotime($test_date));
			$week_day=date('l', strtotime($test_date));
			$week_array[$test_date]=$week;


			/*//$con = connect();//the connection have to be called out of function
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "week_of_year", 1 );
			$field_array="id, year, month, week, week_start_day, week_date,week_day";
			$data_array="(".$id.",".$week_year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
			$rID=sql_insert("week_of_year",$field_array,$data_array,0);
			return_next_id( "id", "week_of_year", 1 );
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");
				}
				else{
					mysql_query("ROLLBACK");
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID){
					if($test_date=="31-Dec-2012"){
						echo $rID."=".$id."=insert into week_of_year (".$field_array.") VALUES ".$data_array;
			          }
					oci_commit($con);
				}
				else{
					oci_rollback($con);
				}
			}*/

		}
	}
	return $week_array ;
}
//$weekarr=week_of_year($cbo_year_selection,"Monday");
//print_r($weekarr);
//echo "ok".$cbo_year_selection; die;
$week_for_header=array();$no_of_week_for_header=array();
$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date'");
foreach ($sql_week_header as $row_week_header)
{
	$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
}
//echo "select week_date,week, min(week_date) as week_start_day,Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week";
$week_start_day=array();
$week_end_day=array();
$sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week");
foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
{
	$week_start_day[$row_week_week_start_end_date[csf("week")]][week_start_day]=$row_week_week_start_end_date[csf("week_start_day")];
	$week_end_day[$row_week_week_start_end_date[csf("week")]][week_end_day]=$row_week_week_start_end_date[csf("week_end_day")];
}
$from_date=$week_start_day[min(array_keys($week_for_header))][week_start_day];
$to_date=$week_end_day[max(array_keys($week_for_header))][week_end_day];


/*if($cbo_category_by==1)
{
	if ($from_date!="" && $to_date!="")
	{
		$date_cond="and c.country_ship_date between '$from_date' and  '$to_date'";
		$date_cond_target_basic="and b.date between '$from_date' and  '$to_date'";
	}
	else
	{
		$date_cond="";
		$date_cond_target_basic="";
	}
}
else
{
	if ($start_date!="" && $end_date!="")
	{
		$date_cond=" and c.country_ship_date between '$from_date' and  '$to_date'";
		$date_cond_target_basic="and b.date between '$from_date' and  '$to_date'";
	}
	else
	{
		$date_cond="";
		$date_cond_target_basic="";
	}
}*/

	if ($from_date!="" && $to_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($from_date));
		$ey = date('Y',strtotime($to_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
			$tot_year.=",".$ey;
			}
			else
			{
			$tot_year.=$ey;
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}

	 

	if ($start_date!="" && $end_date!="")
	{
		$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		if($cbo_category_by==1)
		{
			$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
		}
		else if($cbo_category_by==2)
		{
			$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
		}
	}
	else
	{
		$date_cond_target_basic="";
		$date_cond="";
	}

$capacity_basic_qty_array=array();
//$sql_capacity_basic_qty=sql_select("Select b.capacity_pcs,b.date_calc from lib_capacity_calc_mst a,  lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_name and a.capacity_source=1 and a.location_id=32 and  b.date_calc between '$from_date' and  '$to_date' and b.day_status=1");
$sql_capacity_basic_qty=sql_select("Select b.capacity_pcs ,b.date_calc from lib_capacity_calc_mst a,  lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_name and a.location_id>0  and  b.date_calc between '$start_date' and  '$end_date' and b.day_status=1");

foreach ($sql_capacity_basic_qty as $row_capacity_basic_qty)
{
	$capacity_basic_week=$weekarr[$row_capacity_basic_qty[csf("date_calc")]];
	if($db_type==2)
	{
			$capacity_basic_week=$weekarr[change_date_format($row_capacity_basic_qty[csf("date_calc")],'dd-mm-yyyy','-',1)];

	}
	$capacity_basic_qty_array[$capacity_basic_week][capacity_basic_qty]+=$row_capacity_basic_qty[csf("capacity_pcs")];
}


//var_dump($capacity_basic_qty_array);die;

$asking_avg_rate_arr=return_library_array( "select company_id, asking_avg_rate from lib_standard_cm_entry",'company_id','asking_avg_rate');
$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst",'comapny_id','basic_smv');
$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');
$buy_sew_effi_percent_arr=return_library_array( "select id, sewing_effi_plaing_per from lib_buyer",'id','sewing_effi_plaing_per');

$week_order_qty=array();
$eqv_basic_qty_array=array();

$buyer_array=array();
$buyer_week_order_qty=array();
$buyer_eqv_basic_qty_array=array();

/*$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.shipment_date, '$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date,MAX(c.ex_factory_date)) date_diff_4, b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 group by b.id order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/


//echo $date_cond;die;


$exfactory_data_array=array();
$exfactory_data=sql_select("select po_break_down_id,country_id,
sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
foreach($exfactory_data as $exfatory_row)
{
	if($cbo_category_by==3)
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
	}
	else
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
	}
}

//echo $date_cond.jahid;die;

$order_status_cond="";
if($cbo_order_status>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";

if($db_type==0)
{
	if($cbo_category_by==3)
	{
		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date as ship_date,c.country_id,  b.t_year, b.t_month,a.style_owner
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_status_cond $product_category_con $season_con group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,c.country_id,c.country_ship_date,c.shiping_status,a.style_owner order by c.country_ship_date,a.job_no_prefix_num,b.id";
	}
	else
	{
		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.pub_shipment_date, '$date') date_diff_2, b.unit_price, b.po_total_price as po_total_price, b.details_remarks,b.pub_shipment_date as ship_date, b.shiping_status,  b.t_year, b.t_month,a.style_owner
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_status_cond  $product_category_con $season_con
		order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	}

}
if($db_type==2)
{
  $date=date('d-m-Y');

	if($cbo_category_by==3)
	{
		$sql=("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date as ship_date,c.country_id,  b.t_year, b.t_month,a.style_owner
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_status_cond $product_category_con $season_con  group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,c.country_id,c.country_ship_date,c.shiping_status,a.style_owner order by c.country_ship_date,a.job_no_prefix_num,b.id");
	}
	else
	{

		$sql=("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price as po_total_price, b.details_remarks, b.pub_shipment_date  as ship_date, b.shiping_status,  b.t_year, b.t_month,a.style_owner
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_status_cond $product_category_con $season_con
		order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
	}




}
ob_start();

//echo $sql;

$data_array=sql_select($sql);

foreach ($data_array as $row)
{
	$week=$weekarr[$row[csf("ship_date")]];
	if($db_type==2)
	{
		$week=$weekarr[change_date_format($row[csf("ship_date")],'dd-mm-yyyy','-',1)];
	}
	if( date('l', strtotime($row[csf("ship_date")]))=='Sunday' && $week_pad==1){
		$week=$week+1;
	}
	$week_order_qty[$week][po_quantity]+=$row[csf("po_quantity_pcs")];
	$week_order_qty[$week][po_total_price]+=$row[csf("po_total_price")];

	$buyer_array[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_quantity]+=$row[csf("po_quantity_pcs")];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_total_price]+=$row[csf("po_total_price")];

	if($job_smv_arr[$row[csf("job_no")]] !=0)
	{
		//$eqv_bsic_qty=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]*100/$buy_sew_effi_percent_arr[$row[csf("buyer_name")]]))/$basic_smv_arr[$row[csf("company_name")]];
		$eqv_bsic_qty=(($row[csf("po_quantity")]*$job_smv_arr[$row[csf("job_no")]])/$basic_smv_arr[$row[csf("company_name")]]);
		
		if(is_infinite($eqv_bsic_qty) || is_nan($eqv_bsic_qty)){$eqv_bsic_qty=0;}
		
		$eqv_basic_qty_array[$week][eqv_bsic_qty]+=$eqv_bsic_qty;
		//$eqv_basic_qty_array[$week][eqv_bsic_qty]+=$buy_sew_effi_percent_arr[$row[csf("buyer_name")]];
		$buyer_eqv_basic_qty_array[$row[csf("buyer_name")]][$week][eqv_bsic_qty]+=$eqv_bsic_qty;
		$test_buyer[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
	}
	$poIdArr[$row[csf("id")]]=$row[csf("id")];
}
//print_r($test_buyer);die;
//print_r($eqv_basic_qty_array); echo count($buyer_week_order_qty);die;
	$poIds=implode(",",$poIdArr);
	$booking_no_fin_qnty_array=array();
	$booking_sql=sql_select("SELECT a.po_break_down_id,a.booking_no  from wo_booking_dtls a,wo_po_break_down b,wo_booking_mst c where c.id=a.booking_mst_id and b.id=a.po_break_down_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.entry_form IN (118, 88) and a.po_break_down_id in ($poIds) group by a.po_break_down_id,a.booking_no");

		

	foreach($booking_sql as $vals) 
	{
		$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]]["booking_no"].=$vals[csf("booking_no")].",";
		 
	}

 $production_sql="SELECT a.po_break_down_id,sum(b.production_qnty) as qntys,a.country_id  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id in ($poIds) and a.production_type='5'  group by  a.po_break_down_id,a.country_id";
	$production_sql_data=sql_select($production_sql);
	foreach($production_sql_data as  $val)
	{

		// $po_wise_data[$val[csf("po_break_down_id")]]['sewing_output_qty']+=$val[csf("qntys")];
		$po_country_wise_data[$val[csf("po_break_down_id")]][$val[csf("country_id")]]['sewing_output_qty']+=$val[csf("qntys")];
	}
  ?>

    <div>
    <table cellspacing="0" width="1700px"  border="1" rules="all" class="rpt_table" >
        <thead align="center">
            <tr>
            <th width="145" align="center">Purticulars</th>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="225" colspan="3" align="center">
            Week-
            <?
            //echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
			echo $week_key."<br/>".$week_start_day[$week_key][week_start_day]." To ".$week_end_day[$week_key][week_end_day];
            ?>
            </th>
            <?
            }
            ?>
            <th width="75" rowspan="2" align="center">Total Qty</th>
            <th width="75" rowspan="2" align="center">Total Value</th>
            </tr>
            <tr>
            <th width="145" align="center">Purticulars</th>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="75" align="center">Qty</th>
            <th width="75" align="center">Value</th>
            <th width="75" align="center">Avg Price</th>
            <?
            }
            ?>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="145" align="center">Capacity In Basic Qty</td>
                <?
                $tot_capacity_basic_qty=0;
                $tot_capacity_basic_value=0;
                foreach($week_for_header as $week_key => $week_value)
                {
					?>
					<td width="75"  align="right"><?  echo number_format($capacity_basic_qty_array[$week_key][capacity_basic_qty],0);$tot_capacity_basic_qty+=$capacity_basic_qty_array[$week_key][capacity_basic_qty];?>&nbsp;</td>
					<td width="75"  align="right">
					<?
					$capacity_basic_value=$capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name];
					echo number_format($capacity_basic_value,2);
					$tot_capacity_basic_value+=$capacity_basic_value;
					?>&nbsp;
					</td>
					<td width="75"  align="right"><? echo $asking_avg_rate_arr[$company_name]; ?>&nbsp;</td>

					<?
                }
                ?>
                <td width="75" align="center"><? echo number_format($tot_capacity_basic_qty,0); ?></td>
                <td width="75" align="center"><? echo number_format($tot_capacity_basic_value,2); ?></td>

            </tr>
            <tr>
                <td width="145" align="center">Eqv. Basic Qty</td>
                <?
                $tot_eqv_bsic_qty=0;
                $tot_eqv_bsic_value=0;
                foreach($week_for_header as $week_key => $week_value)
                {
					?>
					<td width="75"  align="right"><? echo  number_format($eqv_basic_qty_array[$week_key][eqv_bsic_qty],0); $tot_eqv_bsic_qty+=$eqv_basic_qty_array[$week_key][eqv_bsic_qty];?>&nbsp;</td>
					<td width="75"  align="right"><? echo number_format($week_order_qty[$week_key][po_total_price],2); $tot_eqv_bsic_value+=$week_order_qty[$week_key][po_total_price];?>&nbsp;</td>
					<td width="75"  align="right"><? 
						$cv=$week_order_qty[$week_key][po_total_price]/$eqv_basic_qty_array[$week_key][eqv_bsic_qty];
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2); 
					?>&nbsp;</td>

					<?
                }
                ?>
                <td width="75" align="center"><? echo number_format($tot_eqv_bsic_qty,0); ?></td>
                <td width="75" align="center"><? echo number_format($tot_eqv_bsic_value,2); ?></td>
            </tr>
            <tr>
            <td width="145" align="center">Order Qty</td>
            <?
			$tot_or_qty=0;
			$tot_ord_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right"><? echo number_format($week_order_qty[$week_key][po_quantity],0); $tot_or_qty+=$week_order_qty[$week_key][po_quantity];?>&nbsp;</td>
            <td width="75"  align="right"><? echo number_format($week_order_qty[$week_key][po_total_price],2); $tot_ord_value+=$week_order_qty[$week_key][po_total_price];?>&nbsp;</td>
            <td width="75"  align="right"><? 
				$cv=$week_order_qty[$week_key][po_total_price]/$week_order_qty[$week_key][po_quantity];
				if(is_infinite($cv) || is_nan($cv)){$cv=0;}
				echo number_format($cv,2);
			?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="center"><? echo number_format($tot_or_qty,0); ?>&nbsp;</td>
            <td width="75" align="center"><? echo number_format($tot_ord_value,2); ?>&nbsp;</td>
            </tr>
            <tr>
            <td width="145" align="center">Balance</td>
            <?
			$tot_balance_qty=0;
			$tot_balance_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right"  bgcolor="<? if($capacity_basic_qty_array[$week_key][capacity_basic_qty]-$eqv_basic_qty_array[$week_key][eqv_bsic_qty] <0){ echo "#FF0000";} else{ echo "#FFFFFF";} ?>">
            <?
            $balance_qty=$capacity_basic_qty_array[$week_key][capacity_basic_qty]-$eqv_basic_qty_array[$week_key][eqv_bsic_qty];
            echo number_format($balance_qty,0);
			$tot_balance_qty+=$balance_qty;
            ?>&nbsp;
            </td>
            <td width="75"  align="right" bgcolor="<? if($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price] <0){ echo "#FF0000";} else{ echo "#FFFFFF";} ?>">
			<?
			echo number_format($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price],2);
			$tot_balance_value+=$capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price];
			?>&nbsp;
            </td>
            <td width="75"  align="right">
            <?
            //echo number_format(($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price])/$balance_qty,2)
            ?>&nbsp;
            </td>

            <?
            }
            ?>
            <td width="75" align="center" bgcolor="<? if($tot_balance_qty < 0){echo "#FF0000";} else{ echo "#FFFFFF";} ?>"><? echo number_format($tot_balance_qty,0); ?></td>
            <td width="75" align="center"><? echo number_format($tot_balance_value,2); ?></td>
            </tr>

            <tr>
            <td width="145" align="center">Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right" bgcolor="<? if((($capacity_basic_qty_array[$week_key][capacity_basic_qty]-$eqv_basic_qty_array[$week_key][eqv_bsic_qty])/$capacity_basic_qty_array[$week_key][capacity_basic_qty])*100 <0){ echo "#FF0000";} else{ echo "#FFFFFF";} ?>">
            <?
            $balance_qty_per=(($capacity_basic_qty_array[$week_key][capacity_basic_qty]-$eqv_basic_qty_array[$week_key][eqv_bsic_qty])/$capacity_basic_qty_array[$week_key][capacity_basic_qty])*100;
            echo number_format($balance_qty_per,2);
            ?>&nbsp;
            </td>
            <td width="75"  align="right" bgcolor="<? if((($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price])/($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]))*100 <0){ echo "#FF0000";} else{ echo "#FFFFFF";} ?>" >
			<? echo number_format((($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price])/($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]))*100,2); ?>&nbsp;
            </td>
            <td width="75"  align="right">
            <?
           // echo number_format(($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$week_order_qty[$week_key][po_total_price])/$balance_qty,2)
            ?>&nbsp;
            </td>

            <?
            }
            ?>
            <td width="75" align="center"><? //echo number_format($tot_balance_qty,2); ?>&nbsp;</td>
            <td width="75" align="center"><? //echo number_format($tot_balance_value,2); ?>&nbsp;</td>
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
   <font size="2px" color="#FF0000"> N.B: Red Means Over Booking.</font>
    <br/>
    <br/>

    <!--<table cellspacing="0" width="1700px"  border="1" rules="all" class="rpt_table" >
        <thead align="center">
            <tr>
            <th width="145" rowspan="2" align="center">Buyer Name</th>
            <th width="145" rowspan="2" align="center">Purticulars</th>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="225" colspan="3" align="center">
            Week-
            <?
            echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
            ?>
            </th>
            <?
            }
            ?>
            <th width="75" rowspan="2" align="center">Total Qty</th>
            <th width="75" rowspan="2" align="center">Total Value</th>
            </tr>
            <tr>

            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="75" align="center">Qty</th>
            <th width="75" align="center">Value</th>
            <th width="75" align="center">Avg Price</th>
            <?
            }
            ?>

            </tr>
        </thead>
        <tbody>
        <?
		foreach ($buyer_array as $buyer_key => $buyer_value)
		{
		 ?>

            <tr>
            <td width="145" rowspan="5" align="center"><? echo $buyer_short_name_arr[$buyer_value];?></td>
            <td width="145" align="center">Order Qty</td>
            <?
			$tot_or_qty=0;
			$tot_ord_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_quantity],0); $tot_or_qty+=$buyer_week_order_qty[$buyer_value][$week_key][po_quantity];?>&nbsp;</td>
            <td width="75"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_total_price],2); $tot_ord_value+=$buyer_week_order_qty[$buyer_value][$week_key][po_total_price];?>&nbsp;</td>
            <td width="75"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_total_price]/$buyer_week_order_qty[$buyer_value][$week_key][po_quantity],2);?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="center"><? echo number_format($tot_or_qty,0);?>&nbsp;</td>
            <td width="75" align="center"><? echo number_format($tot_ord_value,2);?>&nbsp;</td>
            </tr>
            <tr>
            <td width="145" align="center">Eqv. Basic Qty</td>
            <?
			$tot_eqv_bsic_qty=0;
			$tot_eqv_bsic_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right"><? echo  number_format($buyer_eqv_basic_qty_array[$buyer_value][$week_key][eqv_bsic_qty],0); $tot_eqv_bsic_qty+=$buyer_eqv_basic_qty_array[$buyer_value][$week_key][eqv_bsic_qty];?>&nbsp;</td>
            <td width="75"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_total_price],2); $tot_eqv_bsic_value+=$buyer_week_order_qty[$buyer_value][$week_key][po_total_price];?>&nbsp;</td>
            <td width="75"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_total_price]/$buyer_eqv_basic_qty_array[$buyer_value][$week_key][eqv_bsic_qty],2); ?>&nbsp;</td>

            <?
            }
            ?>
            <td width="75" align="center"><? echo number_format($tot_eqv_bsic_qty,0); ?></td>
            <td width="75" align="center"><? echo number_format($tot_eqv_bsic_value,2); ?></td>
            </tr>
            <tr>
            <td width="145" align="center">Capacity In Basic Qty</td>
            <?
			$tot_capacity_basic_qty=0;
			$tot_capacity_basic_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right"><?  echo number_format($capacity_basic_qty_array[$week_key][capacity_basic_qty],0);$tot_capacity_basic_qty+=$capacity_basic_qty_array[$week_key][capacity_basic_qty];?>&nbsp;</td>
            <td width="75"  align="right">
            <?
            $capacity_basic_value=$capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name];
            echo number_format($capacity_basic_value,2);
			$tot_capacity_basic_value+=$capacity_basic_value;
            ?>&nbsp;
            </td>
            <td width="75"  align="right"><? echo $asking_avg_rate_arr[$company_name]; ?>&nbsp;</td>

            <?
            }
            ?>
            <td width="75" align="center"><? echo number_format($tot_capacity_basic_qty,0); ?></td>
            <td width="75" align="center"><? echo number_format($tot_capacity_basic_value,2); ?></td>
            </tr>
            <tr>
            <td width="145" align="center">Balance</td>
            <?
			$tot_balance_qty=0;
			$tot_balance_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right">
            <?
            $balance_qty=$capacity_basic_qty_array[$week_key][capacity_basic_qty]-$buyer_eqv_basic_qty_array[$buyer_value][$week_key][eqv_bsic_qty];
            echo number_format($balance_qty,0);
			$tot_balance_qty+=$balance_qty;
            ?>&nbsp;
            </td>
            <td width="75"  align="right">
			<?
			echo number_format($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$buyer_week_order_qty[$buyer_value][$week_key][po_total_price],2);
			$tot_balance_value+=$capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$buyer_week_order_qty[$buyer_value][$week_key][po_total_price];
			?>&nbsp;
            </td>
            <td width="75"  align="right">
            <?
            //echo number_format(($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$buyer_week_order_qty[$buyer_value][$week_key][po_total_price])/$balance_qty,2)
            ?>&nbsp;
            </td>

            <?
            }
            ?>
            <td width="75" align="center"><? echo number_format($tot_balance_qty,0); ?></td>
            <td width="75" align="center"><? echo number_format($tot_balance_value,2); ?></td>
            </tr>

            <tr>
            <td width="145" align="center">Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="75"  align="right">
            <?
            $balance_qty_per=(($capacity_basic_qty_array[$week_key][capacity_basic_qty]-$buyer_eqv_basic_qty_array[$buyer_value][$week_key][eqv_bsic_qty])/$capacity_basic_qty_array[$week_key][capacity_basic_qty])*100;
           
		    if(is_infinite($balance_qty_per) || is_nan($balance_qty_per)){$balance_qty_per=0;}
		    echo number_format($balance_qty_per,2);
            ?>&nbsp;
            </td>
            <td width="75"  align="right"><? echo number_format((($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$buyer_week_order_qty[$buyer_value][$week_key][po_total_price])/($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]))*100,2); ?>&nbsp;</td>
            <td width="75"  align="right">
            <?
           // echo number_format(($capacity_basic_qty_array[$week_key][capacity_basic_qty]*$asking_avg_rate_arr[$company_name]-$buyer_week_order_qty[$buyer_value][$week_key][po_total_price])/$balance_qty,2)
            ?>&nbsp;
            </td>

            <?
            }
            ?>
            <td width="75" align="center"><? //echo number_format($tot_balance_qty,2); ?>&nbsp;</td>
            <td width="75" align="center"><? //echo number_format($tot_balance_value,2); ?>&nbsp;</td>
            </tr>
            <?
		}
			?>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
   <br/>
   <br/>-->
  <table>
<tr>
<td bgcolor="orange" height="15" width="30"></td>
<td>Maximum 10 Days Remaing To Ship</td>
<td bgcolor="green" height="15" width="30">&nbsp;</td>
<td>On Time Shipment</td>
<td bgcolor="#2A9FFF" height="15" width="30"></td>
<td>Delay shipment</td>
<td bgcolor="red" height="15" width="30"></td>
<td>Shipment Date Over & Pending</td>


</tr>
</table>

<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
            <div id="content_report_panel">
                <table width="3680" id="table_header_1" border="1" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <th width="50">SL</th>
                            <th width="65" >Company</th>
                            <th width="60">Job No</th>
                            <th  width="50">Buyer</th>
                            <th  width="150">Order No</th>
                            <th  width="100">Fab. Booking No</th>
                            <th  width="100">Production Unit</th>
                            <th  width="100">Pord. Dept Code</th>
                            <th width="30">Img</th>
                            <th width="150">Item</th>
                            <th width="90">Style Ref</th>
                            <th width="150">Style Des </th>
                            <th width="50">Week</th>
                            <th width="100">Country</th>
                            <th width="80">Ship Date</th>
                            <th  width="100">SMV</th>
                            <th  width="100">Total SMV</th>
                            <th width="90">Order Qnty</th>
                            <th width="30">Uom</th>
                            <th width="90">Order Qnty(Pcs)</th>
                            <th width="100">Eqv. Basic Qty. (Pcs)</th>
                            <th  width="50">Per Unit Price</th>
                            <th width="100">Order Value</th>
                            <th width="100">Commission</th>
                            <th width="100">Net Order Value</th>
							<th width="100">Sewing Output</th>
                            <th width="90">Ex-Fac Qnty (Pcs) </th>
                            <th  width="90">Ex-factory Bal. (Pcs)</th>
                            <th  width="90">Ex-factory Over (Pcs)</th>
                            <th width="120">Ex-factory Bal. Value</th>
                            <th width="120">Ex-factory Over. Value</th>
                            <th width="60">Order Status</th>
                            <th width="70">Prod. Catg</th>
                            <th width="80">PO Rec. Date</th>
                            <th  width="50">Days in Hand</th>
                            <th width="100" >Shipping Status</th>
                            <th width="150"> Dealing Merchant</th>
                            <th width="150">Team Name</th>
                            <th width="30">Id</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:400px; overflow-y:scroll; width:3580px"  align="left" id="scroll_body">
                    <table width="3560" border="1" class="rpt_table" rules="all" id="table-body">
                    <?
                    $i=1;
                    $order_qnty_pcs_tot=0;
                    $order_qntytot=0;
                    $oreder_value_tot=0;
                    $total_ex_factory_qnty=0;
                    $total_short_access_qnty=0;
                    $total_short_access_value=0;
                    $yarn_req_for_po_total=0;
                    foreach ($data_array as $row)
                    {
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						if($row[csf('is_confirmed')]==2)
						{
							$color_font="#F00";
						}
						else
						{
							$color_font="#000";
						}

						if($cbo_category_by==3)
						{
							$ex_factory_date=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_date];
						}
						else
						{
							$ex_factory_date=$exfactory_data_array[$row[csf('id')]][ex_factory_date];
						}

						$date_diff_3=datediff( "d", $ex_factory_date , $row[csf('ship_date')]);
						$date_diff_4=datediff( "d", $ex_factory_date , $row[csf('ship_date')]);


						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=sql_select("select yarn_cons_qnty from  wo_pre_cost_sum_dtls where  job_no='".$row[csf('job_no')]."'");
						$data_array_costing_per=sql_select("select costing_per from  wo_pre_cost_mst where  job_no='".$row[csf('job_no')]."'");
						list($costing_per)=$data_array_costing_per;
						if($costing_per[csf('costing_per')]==1)
						{
						  $costing_per_pcs=1*12;
						}
						else if($costing_per[csf('costing_per')]==2)
						{
						 $costing_per_pcs=1*1;
						}
						else if($costing_per[csf('costing_per')]==3)
						{
						 $costing_per_pcs=2*12;
						}
						else if($costing_per[csf('costing_per')]==4)
						{
						 $costing_per_pcs=3*12;
						}
						else if($costing_per[csf('costing_per')]==5)
						{
						 $costing_per_pcs=4*12;
						}

						$yarn_req_for_po=0;
						foreach($data_array_yarn_cons as $row_yarn_cons)
						{
							$cons=$row_yarn_cons[csf('yarn_cons_qnty')];
							$yarn_req_for_po=($row_yarn_cons[csf('yarn_cons_qnty')]/ $costing_per_pcs)*$row[csf('po_quantity')];
						}

						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[csf('shiping_status')]==1 && $row[csf('date_diff_1')]>10 )
						{
						$color="";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						//=====================================
						if($row[csf('shiping_status')]==2 && $row[csf('date_diff_1')]>10 )
						{
						$color="";
						}
						if($row[csf('shiping_status')]==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]>=0)
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;
						}
						//========================================
						if($row[csf('shiping_status')]==3 && $date_diff_3 >=0 )
						{
						$color="green";
						}
						if($row[csf('shiping_status')]==3 &&  $date_diff_3<0)
						{
						$color="#2A9FFF";
						}
						if($row[csf('shiping_status')]==3 && $date_diff_4>=0 )
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
						}
						if($row[csf('shiping_status')]==3 && $date_diff_4<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;
						}
						$po_id=$row[csf('id')];
						?>

                        <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
                            <td width="50" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                            <td width="65" align="center"><? echo $company_short_name_arr[$row[csf('company_name')]];?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
                            <td  width="50" align="center"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></td>
                            <td  width="150" align="center" style="color:<? echo $color_font; ?>; word-break: break-all; word-wrap: break-word;"><? echo $row[csf('po_number')];?></td>
							<td  width="100" align="center"><? echo rtrim($booking_no_fin_qnty_array[$po_id]['booking_no'],",");?></td>
							<td  width="100" align="center"><? echo $company_short_name_arr[$row[csf('style_owner')]];?></td>
							<td  width="100" align="center"><font style="color:<? echo $color_font; ?>"><? echo $row[csf('product_code')];?></font></td>
                            <td width="30" onclick="openmypage_image('requires/capacity_and_order_booking_status_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                            <td width="150" align="center" style="word-break: break-all; word-wrap: break-word;">
                            <?
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            ?>
                            <p> <? echo rtrim($gmts_item_name,","); ?> </p>
                            </td>
                            <td width="90" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[csf('style_ref_no')];?></td>
                            <td width="150" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[csf('style_description')];?></td>
                            <td width="50" align="center">
                            <p>
							<?
							$week_de= $no_of_week_for_header[$row[csf("ship_date")]];
							if( date('l', strtotime($row[csf("ship_date")]))=='Sunday' && $week_pad==1){
								$week_de=$week_de+1;
							}
							echo $week_de;
							?>
                            </p></td>
                            <td width="100" align="center"><p><? echo $country_name_arr[$row[csf('country_id')]];?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('ship_date')],'dd-mm-yyyy','-');?></td>
                            <td  width="100" align="right">
                            <?
                            //echo number_format($job_smv_arr[$row['job_no']],2);
                            echo number_format($job_smv_arr[$row[csf('job_no')]],2);
                            ?>
                            </td>

                            <td  width="100" align="right"><?  $smv= ($job_smv_arr[$row[csf('job_no')]])*$row[csf('po_quantity')]; $smv_tot+=$smv; echo number_format($smv,2); ?></td>
                            <td width="90" align="right">
                            <?
                            echo number_format( $row[csf('po_quantity')],0);
                            $order_qntytot=$order_qntytot+$row[csf('po_quantity')];
                            $gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
                            ?>
                            </td>
                            <td width="30" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
                            <td width="90" align="right">
                            <?
                            echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
                            $order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                            $gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                            ?>
                            </td>
                            <td width="100" align="right" title="<? echo "Basic SMV:".$basic_smv_arr[$row[csf("company_name")]];?>">
                            <?
                            //$basic_qnty_pcs= (($job_smv_arr[$row['job_no']]*100/$buy_sew_effi_percent_arr[$row[csf("buyer_name")]])*$row['po_quantity_pcs'])/$basic_smv_arr[$row[csf("company_name")]];
                            $basic_qnty_pcs= (($job_smv_arr[$row[csf('job_no')]])*$row[csf('po_quantity')])/$basic_smv_arr[$row[csf("company_name")]];
							if(is_infinite($basic_qnty_pcs) || is_nan($basic_qnty_pcs)){$basic_qnty_pcs=0;}

                            //$basic_qnty_pcs= ((($job_smv_arr[$row['job_no']]*$row['po_quantity_pcs'])/$basic_smv_arr[$row[csf("company_name")]])*$buy_sew_effi_percent_arr[$row[csf("buyer_name")]])/100;


                            $basic_qnty_pcs_tot+=$basic_qnty_pcs;
                            echo number_format($basic_qnty_pcs,2);

                            ?>
                            </td>
                            <td  width="50" align="right"><? echo number_format($row[csf('unit_price')],2);?></td>
                            <td width="100" align="right">
                            <?
                            echo number_format($row[csf('po_total_price')],2);
                            $oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
                            $goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
                            ?>
                            </td>
                            <td width="100"  align="right"><? 
							$commission=($row[csf('po_quantity')]/$costing_per_pcs)*$commission_for_shipment_schedule_arr[$row[csf('job_no')]]; 
							if(is_infinite($commission) || is_nan($commission)){$commission=0;}
							$commission_tot+=$commission; 
							echo number_format($commission,2); 
							?></td>
                            <td width="100" align="right"><? $net_order_value=$row[csf('po_total_price')]-$commission;$net_order_value_tot+=$net_order_value; echo number_format ($net_order_value,2); ?></td>
							<td width="100" align="right"><?=number_format($po_country_wise_data[$po_id][$row[csf('country_id')]]['sewing_output_qty'],2); $tot_sewing_output_qty+=$po_country_wise_data[$po_id][$row[csf('country_id')]]['sewing_output_qty'];?></td>
                            <td width="90" align="right">
                            <?
                            if($cbo_category_by==3)
                            {
                                $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
                            }
                            else
                            {
                                $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
                            }

                            ?>
                            <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo  number_format($ex_factory_qnty,0); ?></a>
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
                            ?>
                            </td>
                            <td  width="90" align="right">
                            <?
                            $short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
                            if($short_access_qnty>=0){
                            echo number_format($short_access_qnty,0);
                            $total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
                            $gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
                            }
                            ?>
                            </td>
                            <td  width="90" align="right">
                            <?
                            //$short_access_qnty=(($row['po_quantity']*$row['total_set_qnty'])-$ex_factory_qnty);
                            if($short_access_qnty<0){
                            echo number_format(ltrim($short_access_qnty,'-'),0);
                            $total_over_access_qnty=$total_over_access_qnty+$short_access_qnty;
                            //$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
                            }
                            ?>
                            </td>
                            <td width="120" align="right">
                            <?
                            if($short_access_qnty>=0){
                            $short_access_value=$short_access_qnty*$row[csf('unit_price')];
                            echo  number_format($short_access_value,2);
                            $total_short_access_value=$total_short_access_value+$short_access_value;
                            $gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
                            }
                            ?>
                            </td>
                            <td width="120" align="right">
                            <?
                            if($short_access_qnty<0){
                            $short_over_value=$short_access_qnty*$row[csf('unit_price')];
                            echo  number_format(ltrim($short_over_value,'-'),2);
                            $total_over_access_value=$total_over_access_value+$short_over_value;
                            //$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
                            }
                            ?>
                            </td>
                            <td width="60" align="center"><? echo  $order_status[$row[csf('is_confirmed')]];?></td>
                            <td width="70" align="center"><? echo $product_category[$row[csf('product_category')]];?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
                            <td  width="50" align="center" bgcolor="<? echo $color; ?>">
                            <?
                            if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2)
                            {
                            echo $row[csf('date_diff_1')];
                            }
                            if($row[csf('shiping_status')]==3)
                            {
                            echo $date_diff_3;
                            }
                            ?>
                            </td>
                            <td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                            <td width="150" align="center"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></td>
                            <td width="150" align="center"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></td>
                            <td width="30"><? echo $row[csf('id')]; ?></td>
                            <td><? echo $row[csf('details_remarks')]; ?></td>
                        </tr>
                    <?
                    $i++;
					}
                    ?>
                    </table>
                </div>
                <table width="3580" id="report_table_footer" border="1" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                            <th width="50"></th>
                            <th width="65" ></th>
                            <th width="60"></th>
                            <th  width="50"></th>
                            <th  width="150"></th>
							<th  width="100"></th>
							<th  width="100"></th>
                            <th  width="100"></th>
                            <th width="30"></th>
                            <th width="150"></th>
                            <th width="90"></th>
                            <th width="150"></th>
                            <th width="50"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th  width="100" id=""></th>
                            <th  width="100" id="value_smv_tot"><? echo number_format($smv_tot,2); ?></th>
                           <th width="90" id="total_order_qnty"><? echo number_format($order_qntytot,0); ?></th>
                            <th width="30"></th>

                            <th width="90" id="total_order_qnty_pcs"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            <th width="100" id="value_yarn_req_tot"><? echo number_format($basic_qnty_pcs_tot,2); ?></th>
                             <th  width="50"></th>
                             <th width="100" id="value_total_order_value"><? echo number_format($oreder_value_tot,2); ?></th>
                            <th width="100" id="value_total_commission"><? echo number_format($commission_tot,2); ?></th>
                            <th width="100" id="value_total_net_order_value"><? echo number_format($net_order_value_tot,2); ?></th>
							<th width="100" id="total_sewing_ouput"><? echo number_format($tot_sewing_output_qty,2); ?></th>
                            <th width="90" id="total_ex_factory_qnty"> <? echo number_format($total_ex_factory_qnty,0); ?></th>
                            <th  width="90" id="total_short_access_qnty"><? echo number_format($total_short_access_qnty,0); ?></th>
                             <th  width="90" id="total_over_access_qnty"><? echo number_format(ltrim($total_over_access_qnty,'-'),0); ?></th>
                            <th width="120" id="value_total_short_access_value"><? echo number_format($total_short_access_value,2); ?></th>
                            <th width="120" id="value_total_over_access_value"><? echo number_format(ltrim($total_over_access_value,'-'),2); ?></th>
                            <th width="60"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th  width="50"></th>
                            <th width="100" ></th>
                            <th width="150"> </th>
                            <th width="150"></th>
                            <th width="30"></th>
                            <th></th>
                        </tr>

                    </tfoot>
                </table>
                <div id="shipment_performance">
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

	$html = ob_get_contents();

	foreach (glob(""."*.xls") as $filename)
	{
	   @unlink($filename);
	}
	$name="weekcapabooking".".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	exit();

}
if($type=="report_generate_country_wise")
{
	$data=explode("_",$data);
	//print_r($data);die;
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	$cbo_order_status2=$data[4];
	if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";
	if(trim($data[5])=="0") $team_leader="%%"; else $team_leader="$data[5]";
	if(trim($data[6])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[6]";
	//if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	if(trim($data[10])=="0") $product_category_con=""; else $product_category_con=" and a.product_category=$data[10]";

	$cbo_order_status=$data[8];
	$cbo_season_id=$data[12];
	
	if($cbo_season_id!=0){$season_con=" and a.season_buyer_wise=$cbo_season_id";}else{$season_con="";}

	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}

	$cbo_category_by=$data[7]; $caption_date='';
	$cbo_year_selection=$data[9];

	if ($start_date!="" && $end_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($start_date));
		$ey = date('Y',strtotime($end_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
			$tot_year.=",".$ey;
			}
			else
			{
			$tot_year.=$ey;
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}


	function week_of_year($year,$week_start_day)
	{
		$week_array=array();
		$week=0;
		for($i=1;$i<=12; $i++)
		{
			$month=str_pad($i, 2, '0', STR_PAD_LEFT);
			$year=$year;
			$first_date_of_year=$year."-01-01";
			$first_day_of_year=date('l', strtotime($first_date_of_year));
			if($i==1)
			{
				if(date('l', strtotime($first_day_of_year))==$week_start_day)
				{
					$week=0;
				}
				else
				{
					$week=1;
				}
			}
			$days_in_month = cal_days_in_month(0, $month, $year) ;

		    foreach (range(1, $days_in_month) as $day)
			{
				$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
				global $db_type;
				if($db_type==2)
				{
					$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
				}

				if(date('l', strtotime($test_date))==$week_start_day)
				{
				  $week++;
				}
				$week_day=date('l', strtotime($test_date));
				$week_array[$test_date]=$week;


				/*$con = connect();//the connection have to be called out of function
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$id=return_next_id( "id", "week_of_year", 1 );
				$field_array="id, year, month, week, week_start_day, week_date,week_day";
				$data_array="(".$id.",".$year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
				$rID=sql_insert("week_of_year",$field_array,$data_array,0);
				if($db_type==0)
				{
					if($rID){
						mysql_query("COMMIT");
					}
					else{
						mysql_query("ROLLBACK");
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID){
						oci_commit($con);
					}
					else{
						oci_rollback($con);
					}
				}*/

		    }
		}
		return $week_array ;
	}
	$weekarr=week_of_year($cbo_year_selection,"Sunday");

	//$con = connect();
	function week_of_year_new($year,$week_start_day)
	{
		$week_array=array();
		for($i=1;$i<=12; $i++)
		{
			$month=str_pad($i, 2, '0', STR_PAD_LEFT);
			$year=$year;

			$days_in_month = cal_days_in_month(0, $month, $year) ;
			foreach (range(1, $days_in_month) as $day)
			{
				$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
				global $db_type;

				if($db_type==2)
				{
					$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
				}
				$date = new DateTime($test_date);
	            $week = $date->format("W");
				$week_year = $date->format("o");
				//$week= (int) date("W", strtotime($test_date));
				$week_day=date('l', strtotime($test_date));
				$week_array[$test_date]=$week;


				/*//$con = connect();//the connection have to be called out of function
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$id=return_next_id( "id", "week_of_year", 1 );
				$field_array="id, year, month, week, week_start_day, week_date,week_day";
				$data_array="(".$id.",".$week_year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
				$rID=sql_insert("week_of_year",$field_array,$data_array,0);
				return_next_id( "id", "week_of_year", 1 );
				if($db_type==0)
				{
					if($rID){
						mysql_query("COMMIT");
					}
					else{
						mysql_query("ROLLBACK");
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID){
						if($test_date=="31-Dec-2012"){
							echo $rID."=".$id."=insert into week_of_year (".$field_array.") VALUES ".$data_array;
				          }
						oci_commit($con);
					}
					else{
						oci_rollback($con);
					}
				}*/

			}
		}
		return $week_array ;
	}
	//$weekarr=week_of_year($cbo_year_selection,"Monday");
	//print_r($weekarr);
	//echo "ok".$cbo_year_selection; die;
	$week_for_header=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date'");
	foreach ($sql_week_header as $row_week_header)
	{
		$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
	}
	//echo "select week_date,week, min(week_date) as week_start_day,Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week";
	$week_start_day=array();
	$week_end_day=array();
	$sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week");
	foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
	{
		$week_start_day[$row_week_week_start_end_date[csf("week")]][week_start_day]=$row_week_week_start_end_date[csf("week_start_day")];
		$week_end_day[$row_week_week_start_end_date[csf("week")]][week_end_day]=$row_week_week_start_end_date[csf("week_end_day")];
	}
	$from_date=$week_start_day[min(array_keys($week_for_header))][week_start_day];
	$to_date=$week_end_day[max(array_keys($week_for_header))][week_end_day];

	if ($from_date!="" && $to_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($from_date));
		$ey = date('Y',strtotime($to_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
			$tot_year.=",".$ey;
			}
			else
			{
			$tot_year.=$ey;
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}

	if ($start_date!="" && $end_date!="")
	{
		$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		if($cbo_category_by==1)
		{
			$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
		}
		else if($cbo_category_by==2)
		{
			$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
		}
		else
		{
			$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
		}
	}
	else
	{
		$date_cond_target_basic="";
		$date_cond="";
	}

	$capacity_basic_qty_array=array();
	//$sql_capacity_basic_qty=sql_select("Select b.capacity_pcs,b.date_calc from lib_capacity_calc_mst a,  lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_name and a.capacity_source=1 and a.location_id=32 and  b.date_calc between '$from_date' and  '$to_date' and b.day_status=1");
	$sql_capacity_basic_qty=sql_select("Select b.capacity_pcs ,b.date_calc from lib_capacity_calc_mst a,  lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_name and a.location_id>0  and  b.date_calc between '$start_date' and  '$end_date' and b.day_status=1");

	foreach ($sql_capacity_basic_qty as $row_capacity_basic_qty)
	{
		$capacity_basic_week=$weekarr[$row_capacity_basic_qty[csf("date_calc")]];
		if($db_type==2)
		{
				$capacity_basic_week=$weekarr[change_date_format($row_capacity_basic_qty[csf("date_calc")],'dd-mm-yyyy','-',1)];

		}
		$capacity_basic_qty_array[$capacity_basic_week][capacity_basic_qty]+=$row_capacity_basic_qty[csf("capacity_pcs")];
	}
	//var_dump($capacity_basic_qty_array);die;
	$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$asking_avg_rate_arr=return_library_array( "select company_id, asking_avg_rate from lib_standard_cm_entry",'company_id','asking_avg_rate');
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst",'comapny_id','basic_smv');
	$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');
	$buy_sew_effi_percent_arr=return_library_array( "select id, sewing_effi_plaing_per from lib_buyer",'id','sewing_effi_plaing_per');

	$week_order_qty=array();
	$eqv_basic_qty_array=array();

	$buyer_array=array();
	$buyer_week_order_qty=array();
	$buyer_eqv_basic_qty_array=array();

	/*$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.shipment_date, '$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date,MAX(c.ex_factory_date)) date_diff_4, b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 group by b.id order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/


	//echo $date_cond;die;


	$exfactory_data_array=array();
	$exfactory_data=sql_select("select po_break_down_id,country_id,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
	MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
	foreach($exfactory_data as $exfatory_row)
	{
		if($cbo_category_by==3)
		{
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
		}
		else
		{
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
		}
	}

	//echo $date_cond.jahid;die;

	$order_status_cond="";
	if($cbo_order_status>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";

	if($db_type==0)
	{
		if($cbo_category_by==3)
		{
			$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date as ship_date,c.country_id,  b.t_year, b.t_month,c.cutup ,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks,sum(c.plan_cut_qnty) as plan_cut_qnty,a.style_owner
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_status_cond $product_category_con $season_con group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,,c.cutup ,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks ,c.country_id,c.country_ship_date,c.shiping_status,a.style_owner order by c.country_ship_date,a.job_no_prefix_num,b.id";
		}
		else
		{
			$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.pub_shipment_date, '$date') date_diff_2, b.unit_price, b.po_total_price as po_total_price, b.details_remarks,b.pub_shipment_date as ship_date, b.shiping_status,  b.t_year, b.t_month ,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks,sum(c.plan_cut_qnty) as plan_cut_qnty,a.style_owner
			from wo_po_details_master a, wo_po_break_down b
			where a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond $season_con  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_status_cond  $product_category_con
			order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
		}

	}
	if($db_type==2)
	{
	$date=date('d-m-Y');

		if($cbo_category_by==3)
		{
			$sql=("SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date as ship_date,c.country_id,  b.t_year, b.t_month ,c.cutup,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks,sum(c.plan_cut_qnty) as plan_cut_qnty,a.style_owner
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_status_cond $product_category_con $season_con  group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month ,c.cutup,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks,c.country_id,c.country_ship_date,c.shiping_status,a.style_owner order by c.country_ship_date,a.job_no_prefix_num,b.id");
		}
		else
		{

			$sql=("SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, b.po_quantity as po_quantity, (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price as po_total_price, b.details_remarks, b.pub_shipment_date  as ship_date, b.shiping_status,  b.t_year, b.t_month,a.ship_mode,  a.season_buyer_wise,  a.season_matrix,  a.season,c.country_remarks,sum(c.plan_cut_qnty) as plan_cut_qnty,a.style_owner
			from wo_po_details_master a, wo_po_break_down b
			where a.job_no=b.job_no_mst   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_status_cond $product_category_con $season_con
			order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
		}
	}	

	 //echo $sql;

	$data_array=sql_select($sql);
	$job_no_array = array();
	foreach ($data_array as $row)
	{
		$week=$weekarr[$row[csf("ship_date")]];
		if($db_type==2)
		{
			$week=$weekarr[change_date_format($row[csf("ship_date")],'dd-mm-yyyy','-',1)];
		}
		if( date('l', strtotime($row[csf("ship_date")]))=='Sunday' && $week_pad==1){
			$week=$week+1;
		}
		$week_order_qty[$week][po_quantity]+=$row[csf("po_quantity_pcs")];
		$week_order_qty[$week][po_total_price]+=$row[csf("po_total_price")];

		$buyer_array[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
		$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_quantity]+=$row[csf("po_quantity_pcs")];
		$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_total_price]+=$row[csf("po_total_price")];

		if($job_smv_arr[$row[csf("job_no")]] !=0)
		{
			//$eqv_bsic_qty=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]*100/$buy_sew_effi_percent_arr[$row[csf("buyer_name")]]))/$basic_smv_arr[$row[csf("company_name")]];
			$eqv_bsic_qty=(($row[csf("po_quantity")]*$job_smv_arr[$row[csf("job_no")]])/$basic_smv_arr[$row[csf("company_name")]]);
			$eqv_basic_qty_array[$week][eqv_bsic_qty]+=$eqv_bsic_qty;
			//$eqv_basic_qty_array[$week][eqv_bsic_qty]+=$buy_sew_effi_percent_arr[$row[csf("buyer_name")]];
			$buyer_eqv_basic_qty_array[$row[csf("buyer_name")]][$week][eqv_bsic_qty]+=$eqv_bsic_qty;
			$test_buyer[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
		}
		$job_no_array[$row[csf("job_no")]] = $row[csf("job_no")];
		$poIdArr[$row[csf("id")]]=$row[csf("id")];
	}
	//print_r($test_buyer);die;
	//print_r($eqv_basic_qty_array); echo count($buyer_week_order_qty);die;
	$job_nos = "'" . implode ( "', '", $job_no_array ) . "'";
	$poIds=implode(",",$poIdArr);

	// ====================== CHECK JOB NO IN BOOKING (without booking, fab req qty will not show) =========================
	$booking_sql = "SELECT b.job_no,sum(b.fin_fab_qnty) as qty,a.booking_no,b.po_break_down_id,a.entry_form from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in($job_nos) and a.status_active=1 and b.status_active=1 group by b.job_no,a.booking_no,b.po_break_down_id,a.entry_form ";
	$booking_sql_res = sql_select($booking_sql);
	$job_wise_booking_array = array();
	foreach ($booking_sql_res as $val) 
	{
		$job_wise_booking_array[$val[csf('job_no')]]['job'] = $val[csf('job_no')];
		$job_wise_booking_array[$val[csf('job_no')]]['qty'] = $val[csf('qty')];
		if($val[csf('entry_form')]==118 || $val[csf('entry_form')]==88){
			$booking_no_fin_qnty_array[$val[csf("po_break_down_id")]]["booking_no"].=$val[csf("booking_no")].",";
		}
	}

	$fin_con = sql_select("SELECT avg_finish_cons, job_no from wo_pre_cost_fabric_cost_dtls  where job_no in($job_nos) and status_active=1");
	$fin_con_array = array();
	foreach ($fin_con as $val) 
	{
		$fin_con_array[$val[csf('job_no')]] += $val[csf('avg_finish_cons')];
	}
	// print_r($job_wise_booking_array);


	$production_sql="SELECT a.po_break_down_id,sum(b.production_qnty) as qntys,a.country_id  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id in ($poIds) and a.production_type='5'  group by  a.po_break_down_id,a.country_id";
	$production_sql_data=sql_select($production_sql);
	foreach($production_sql_data as  $val)
	{

		// $po_wise_data[$val[csf("po_break_down_id")]]['sewing_output_qty']+=$val[csf("qntys")];
		$po_country_wise_data[$val[csf("po_break_down_id")]][$val[csf("country_id")]]['sewing_output_qty']+=$val[csf("qntys")];
	}
	ob_start();
	?>
	
    <div>
   		<font size="2px" color="#FF0000"> N.B: Red Means Over Booking.</font><br/><br/>
		<table>
			<tr>
				<td bgcolor="orange" height="15" width="30"></td>
				<td>Maximum 10 Days Remaing To Ship</td>
				<td bgcolor="green" height="15" width="30">&nbsp;</td>
				<td>On Time Shipment</td>
				<td bgcolor="#2A9FFF" height="15" width="30"></td>
				<td>Delay shipment</td>
				<td bgcolor="red" height="15" width="30"></td>
				<td>Shipment Date Over & Pending</td>
			</tr>
		</table>

		<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
	    <div id="content_report_panel">
	        <table width="2510" id="table_header_1" border="1" class="rpt_table" rules="all">
	            <thead>
	                <tr>
	                    <th width="50">Buyer</th>
	                    <th width="60">Job</th>
	                    <th width="60">Job Year</th>
	                    <th width="90">Style</th>
	                    <th width="150">Order No</th>
						<th  width="100">Fab. Booking No</th>
                        <th  width="100">Production Unit</th>
	                    <th width="100">Country</th>
	                    <th width="80">OPD Date</th>
	                    <th width="50">OPD Week</th>
	                    <th width="80">Country Ship Date</th>
	                    <th width="50">TOD Week</th>
	                    <th width="100">Cut Off</th>
	                    <th width="30">Uom</th>
	                    <th width="90">Order Qnty</th>
	                    <th width="50">Unit Price</th>
	                    <th width="50">Order Qnty(Pcs)</th>
	                    <th width="100">Order FOB value</th>
	                    <th width="80">Fab. Req. Qty.</th>
						<th width="100">Sewing Output</th>
	                    <th width="90">EX-Factory Qty </th>
	                    <th width="80">EX-Factory Bal. Qty </th>
	                    <th width="120">EX-Factory FOB Value</th>
	                    <th width="60">Ex. Fact. Week</th>
	                    <th width="70">Ex-Factory Date</th>
	                    <th width="80">Delay Week</th>
	                    <th width="50">Delay Days</th>
	                    <th width="60">Ship Mode</th>
	                    <th width="100" >Shipping Status</th>
	                    <th width="80">Season</th>
	                    <th>Reason</th>
	                </tr>
	            </thead>
	        </table>
	        <div style=" max-height:400px; overflow-y:scroll; width:2530px"  align="left" id="scroll_body">
	            <table width="2510" border="1" class="rpt_table" rules="all" id="table-body">
	            <?
	            $i=1;
	            $order_qnty_pcs_tot=0;
	            $order_qntytot=0;
	            $oreder_value_tot=0;
	            $total_ex_factory_qnty=0;
	            $total_short_access_qnty=0;
	            $total_short_access_value=0;
	            $total_fab_req_qty=0;
	            $yarn_req_for_po_total=0;
	            foreach ($data_array as $row)
	            {
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if($row[csf('is_confirmed')]==2)
					{
						$color_font="#F00";
					}
					else
					{
						$color_font="#000";
					}

					if($cbo_category_by==3)
					{
						$ex_factory_date=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_date];
					}
					else
					{
						$ex_factory_date=$exfactory_data_array[$row[csf('id')]][ex_factory_date];
					}

					$date_diff_3=datediff( "d", $ex_factory_date , $row[csf('ship_date')]);
					$date_diff_4=datediff( "d", $ex_factory_date , $row[csf('ship_date')]);

					$shipment_performance=0;
					if($row[csf('shiping_status')]==1 && $row[csf('date_diff_1')]>10 )
					{
						$color="";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
					}
					if($row[csf('shiping_status')]==1 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
					{
						$color="orange";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
					}
					if($row[csf('shiping_status')]==1 &&  $row[csf('date_diff_1')]<0)
					{
						$color="red";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
					}
					//=====================================
					if($row[csf('shiping_status')]==2 && $row[csf('date_diff_1')]>10 )
					{
						$color="";
					}
					if($row[csf('shiping_status')]==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
					{
						$color="orange";
					}
					if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_1')]<0)
					{
						$color="red";
					}
					if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]>=0)
					{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
					}
					if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]<0)
					{
						$number_of_order['after']+=1;
						$shipment_performance=2;
					}
					//========================================
					if($row[csf('shiping_status')]==3 && $date_diff_3 >=0 )
					{
						$color="green";
					}
					if($row[csf('shiping_status')]==3 &&  $date_diff_3<0)
					{
						$color="#2A9FFF";
					}
					if($row[csf('shiping_status')]==3 && $date_diff_4>=0 )
					{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
					}
					if($row[csf('shiping_status')]==3 && $date_diff_4<0)
					{
						$number_of_order['after']+=1;
						$shipment_performance=2;
					}
					if($job_wise_booking_array[$row[csf('job_no')]]['job'] !="")
					{
						$fab_req_qty = ($fin_con_array[$row[csf('job_no')]]/12)*$row[csf('plan_cut_qnty')];	
					}
					$po_id=$row[csf('id')];
					?>

	                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >

	                    <td  width="50" align="center"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></td>
	                    <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
	                    <td width="60" align="center"><? echo $row[csf('t_year')];?></td>


	                    <td width="90" align="center" style="word-break: break-all; word-wrap: break-word;"><? echo $row[csf('style_ref_no')];?></td>
	                    <td  width="150" align="center" style="color:<? echo $color_font; ?>;  word-break: break-all; word-wrap: break-word;"><? echo $row[csf('po_number')];?></td>
						<td  width="100" align="center"><? echo rtrim($booking_no_fin_qnty_array[$po_id]['booking_no'],",");?></td>
						<td  width="100" align="center"><? echo $company_short_name_arr[$row[csf('style_owner')]];?></td>
	                    <td width="100" align="center"><p><? echo $country_name_arr[$row[csf('country_id')]];?></p></td>
	                    <td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
	                    <td width="50" align="center">
	                    <p>
						<?
						$week_de1= $no_of_week_for_header[$row[csf("po_received_date")]];
						if( date('l', strtotime($row[csf("po_received_date")]))=='Sunday' && $week_pad==1){
							$week_de1=$week_de1+1;
						}
						// echo $week_de1;
						$po_date = $row[csf("po_received_date")];
						echo date("W", strtotime($po_date));
						?>
	                    </p></td>

	                    <td width="80" align="center"><? echo change_date_format($row[csf('ship_date')],'dd-mm-yyyy','-');?></td>
	                    <td width="50" align="center" title="<? echo $row[csf("ship_date")].'='.$no_of_week_for_header[$row[csf("ship_date")]];?>">
	                    <p>
						<?
						$week_de= $no_of_week_for_header[$row[csf("ship_date")]];
						if( date('l', strtotime($row[csf("ship_date")]))=='Sunday' && $week_pad==1){
							$week_de=$week_de+1;
						}
						echo $week_de;
						?>
	                    </p></td>


	                    <td  width="100"><?  echo $cut_up_array[$row[csf('cutup')]]; ?></td>
	                     <td width="30" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
	                    <td width="90" align="right">
	                    <?
	                    echo number_format( $row[csf('po_quantity')],0);
	                    $order_qntytot=$order_qntytot+$row[csf('po_quantity')];
	                    $gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
	                    ?>
	                    </td>


	                    <td  width="50" align="right"><? echo number_format($row[csf('unit_price')],2);?></td>
	                    <td  width="50" align="right">
							<?
							$poQtyPcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
							//echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);
							echo number_format($poQtyPcs,0);
							//$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
							//$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
							?>
						</td>
	                    <td width="100" align="right">
	                    <?
	                    echo number_format($row[csf('po_total_price')],2);
	                    $oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
	                    $goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
	                    ?>
	                    </td>
						<td width="80" align="right"><? echo number_format($fab_req_qty,2); ?></td>

						<td width="100" align="right"><?=number_format($po_country_wise_data[$po_id][$row[csf('country_id')]]['sewing_output_qty'],2); $tot_sewing_output_qty+=$po_country_wise_data[$po_id][$row[csf('country_id')]]['sewing_output_qty'];?></td>

	                    <td width="90" align="right">
	                    <?
	                    if($cbo_category_by==3)
	                    {
	                        $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
	                    }
	                    else
	                    {
	                        $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
	                    }

	                    ?>
	                    <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo  number_format($ex_factory_qnty,0); ?></a>
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
	                    ?>
	                    </td>
	                    <td width="80" align="right">
	                    <?
							$ex_factory_bal_qnty=$poQtyPcs-$ex_factory_qnty;
							$ex_factory_bal_qnty_tot+=$ex_factory_bal_qnty;
							echo number_format($ex_factory_bal_qnty,0);
						?>
	                    </td>
	                    <td width="120" align="right">
	                    <?
							$ex_factory_value=$ex_factory_qnty*($row[csf('unit_price')]/$row[csf('total_set_qnty')]);
							$ex_factory_value_tot+=	$ex_factory_value;
							echo  number_format($ex_factory_value,2);
	                    ?>
	                    </td>
	                    <td width="60" align="center">
						<?
							$week_ex= $no_of_week_for_header[$ex_factory_date];
							if( date('l', strtotime($ex_factory_date))=='Sunday' && $week_pad==1){
								$week_ex=$week_ex+1;
							}
							echo $week_ex;
						?>
	                    </td>
	                    <td width="70" align="center"><? echo change_date_format($ex_factory_date,'dd-mm-yyyy','-');?></td>
	                    <td width="80" align="center"><? echo  $week_ex - $week_de;?></td>
	                    <td  width="50" align="center" bgcolor="<? echo $color; ?>">
	                    <?
		                    if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2)
		                    {
		                    	echo $row[csf('date_diff_1')];
		                    }
		                    if($row[csf('shiping_status')]==3)
		                    {
		                    	echo $date_diff_3-1;
		                    }
	                    ?>
	                    </td>
	                    <td width="60"><? echo $shipment_mode[$row[csf('ship_mode')]]; ?></td>
	                    <td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>

	                    <td width="80">
						<?
							if( $row[csf('season')]){
								echo $row[csf('season')];
							}
							else if($row[csf('season_buyer_wise')])
							{
								echo $lib_buyer_season_arr[$row[csf('season_buyer_wise')]];
							}
							else if($row[csf('season_matrix')])
							{
								echo $lib_buyer_season_arr[$row[csf('season_matrix')]];
							}
						 ?>
	                    </td>
	                    <td style="word-break: break-all;word-wrap: break-word;"><? echo $row[csf('country_remarks')];?></td>
	                </tr>
	            <?
	            $i++;
	            $total_fab_req_qty += $fab_req_qty;
	            unset($fab_req_qty);
				}
	            ?>
	            </table>
	        </div>
	        <table width="2510" id="report_table_footer" border="1" class="rpt_table" rules="all">
	            <tfoot>
	                <tr>
	                    <th width="50"></th>
	                    <th width="60"></th>
	                    <th width="60"></th>
	                    <th width="90"></th>
	                    <th width="150"></th>
	                    <th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
	                    <th width="80"></th>
	                    <th width="50"></th>
	                    <th width="80"></th>
	                    <th width="50"></th>
	                    <th width="100" ><?  ?></th>
	                    <th width="30"></th>
	                    <th width="90" id="total_order_qnty"><? echo number_format($order_qntytot,0); ?></th>
	                    <th width="50"></th>
                        <th width="50" id="total_ord_qnty_pcs"></th>
	                    <th width="100" id="value_total_order_value"><? echo number_format($oreder_value_tot,2); ?></th>
                        <th width="80" id="total_fab_req_qty"><? echo number_format($total_fab_req_qty,2); ?></th>
						<th width="100" id="total_sewing_ouput"><? echo number_format($tot_sewing_output_qty,2); ?></th>
	                    <th width="90" id="total_ex_factory_qnty"> <? echo number_format($total_ex_factory_qnty,0); ?></th>
	                     <th width="80" id="total_ex_factory_qnty_bal"> <? echo number_format( $ex_factory_bal_qnty_tot,0); ?></th>


	                    <th width="120" id="value_total_ex_factory_value"><? echo number_format($ex_factory_value_tot,2); ?></th>
	                    <th width="60"></th>
	                    <th width="70"></th>
	                    <th width="80"></th>
	                    <th  width="50"></th>
	                    <th width="60"></th>
	                    <th width="100" ></th>
	                    <th width="80"></th>
	                    <th></th>
	                </tr>
	            </tfoot>
	        </table>
	    </div>
 	</div>

	<?

	$html = ob_get_contents();

	foreach (glob(""."*.xls") as $filename)
	{
	   @unlink($filename);
	}
	$name="weekcapabooking".".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
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
				$exfac_sql=("SELECT b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
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
?>
